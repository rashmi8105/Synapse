<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151002000505 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `synapse`.`org_calc_flags_student_reports` ADD INDEX `calcat` (`calculated_at` ASC);');

        $this->addSql('ALTER EVENT event_report_calc DISABLE;');
        $this->addSql('DROP EVENT IF EXISTS event_report_calc;');

        $this->addSql("DROP PROCEDURE IF EXISTS `ReportCalculation`;
            CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `ReportCalculation`()

            BEGIN

                SELECT id into @rprtFlgId FROM org_calc_flags_student_reports where calculated_at is null limit 1;

                WHILE (@rprtFlgId IS NOT NULL) do

                    SET @calculatedTime := '1900-01-01 00:00:00';

                    SELECT person_id into @studentId FROM org_calc_flags_student_reports where id = @rprtFlgId;
                    SELECT org_id INTO @orgId FROM org_calc_flags_student_reports where id = @rprtFlgId;

                    SELECT COUNT(Distinct (survey_id)) INTO @count_survey
                    FROM survey_response sr
                    WHERE
                        (sr.org_id, sr.person_id) = (@orgId, @studentId)
                    ORDER BY modified_at DESC LIMIT 1;

                    if @count_survey > 0 then

                        SELECT survey_id INTO @surveyId
                        FROM survey_response sr
                        WHERE
                            (sr.org_id, sr.person_id) = (@orgId, @studentId)
                        ORDER BY modified_at DESC LIMIT 1;

                        if @surveyId IS NOT NULL then

                            Replace into report_calculated_values (org_id,person_id,report_id,section_id,element_id,element_bucket_id,survey_id,calculated_value)
                                (select organization_id, person_id, report_id, section_id, element_id, element_bucket_id, survey_id, mean_value
                                  from
                                      (select pfc.organization_id, pfc.person_id, rs.report_id, rse.section_id,reb.element_id,reb.id as element_bucket_id,pfc.survey_id,pfc.mean_value
                                          from reports r
                                            inner join report_sections rs on rs.report_id = r.id
                                            inner join report_section_elements rse on rse.section_id = rs.id and rse.source_type='F' and rse.factor_id is not null
                                            inner join report_element_buckets reb on reb.element_id = rse.id
                                            inner join person_factor_calculated pfc on pfc.factor_id = rse.factor_id
                                            inner join factor ft on ft.id = pfc.factor_id
                                          where pfc.person_id = @studentId and pfc.organization_id = @orgId and pfc.survey_id = @surveyId and r.name='student-report'
                                            and pfc.mean_value between reb.range_min and reb.range_max
                                          group by person_id,report_id,section_id,element_id,survey_id
                                        union all
                                        select sr.org_id, sr.person_id, rs.report_id, rse.section_id,reb.element_id,reb.id as element_bucket_id,sr.survey_id,sr.decimal_value
                                          from reports r
                                            inner join report_sections rs on rs.report_id = r.id
                                            inner join report_section_elements rse on rse.section_id = rs.id and rse.source_type='Q' and rse.survey_question_id is not null
                                            inner join report_element_buckets reb on reb.element_id = rse.id
                                            inner join survey_response sr on sr.survey_questions_id=rse.survey_question_id and sr.decimal_value is not null
                                          where sr.person_id = @studentId and sr.org_id = @orgId and sr.survey_id = @surveyId and r.name='student-report'
                                            and sr.decimal_value between reb.range_min and reb.range_max
                                          group by person_id,report_id,section_id,element_id,survey_id
                                      )
                                  merged
                                )
                            ;

                            set @reportId := (select id from reports where name='student-report');

                            UPDATE org_calc_flags_student_reports SET survey_id = @surveyId, report_id = @reportId
                              where id = @rprtFlgId;

                            insert into report_calc_history (report_id,org_id,person_id,survey_id,created_at,modified_at)
                              values(@reportId,@orgId,@studentId,@surveyId,now(),now());

                            SET @calculatedTime := now();

                        end if;

                    end if;

                    UPDATE org_calc_flags_student_reports SET calculated_at = @calculatedTime, modified_at = now()
                      where id = @rprtFlgId;

                    set @rprtFlgId := null;
                    set @orgId := null;
                    set @studentId := null;

                    SELECT id into @rprtFlgId FROM org_calc_flags_student_reports where calculated_at is null limit 1;

                end WHILE;

            END");

        $this->addSql("CREATE EVENT ReportCalculation_event
            ON SCHEDULE EVERY 5 minute
            STARTS '2015-08-16 03:14:37'
            DO BEGIN
                CALL ReportCalculation();
            END");

        $this->addSql('ALTER EVENT ReportCalculation_event ENABLE;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
