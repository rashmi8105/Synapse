<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170206113613 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("DROP PROCEDURE IF EXISTS `Student_Data_Dump`");

        $procedure = <<<EOD
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Student_Data_Dump`(
        IN orgId int(11),
        IN slimit int(11),
        IN soffset int(11)
        )
BEGIN
    SET group_concat_max_len = 1000000;
    DROP TEMPORARY TABLE IF EXISTS temppivot;

    DROP TEMPORARY TABLE IF EXISTS listofmetacols;
    CREATE TEMPORARY TABLE listofmetacols (`meta_key` VARCHAR(50) NOT NULL, `em_id` int(11) NULL, `om_id` int(11) NULL, PRIMARY KEY (`meta_key`));
    DROP TEMPORARY TABLE IF EXISTS listofpeeps;
    CREATE TEMPORARY TABLE listofpeeps (`person_id` int(11) NOT NULL,PRIMARY KEY (`person_id`));

    #--This can populate first without actually having panaramic photo errors
    INSERT INTO listofmetacols
        SELECT    meta_key, em.id AS em_id, NULL AS om_id
        FROM      ebi_metadata em
        WHERE     deleted_at IS NULL
        UNION ALL
        SELECT    meta_key, NULL AS em_id, om.id AS om_id
        FROM      org_metadata om
        WHERE     organization_id = orgId and deleted_at IS NULL
    ;

    #--This can also populate first without actually having panaramic photo errors
    INSERT INTO listofpeeps
    (
    select op.person_id AS person_id
    from org_person_student op
    where op.organization_id = orgId AND op.deleted_at IS NULL
    limit slimit offset soffset
    );

    SET @tempsql = NULL;
    SELECT CONCAT("CREATE TEMPORARY TABLE `temppivot` (`person_id` int(11) NOT NULL,`year_id` CHAR(6) NOT NULL,`term_id` varchar(12) NOT NULL,", Group_concat("`", meta_key, "` text COLLATE utf8_unicode_ci DEFAULT NULL"), ",PRIMARY KEY (`person_id`, `year_id`, `term_id`))")
    INTO   @tempsql
    FROM   listofmetacols AS md;
    PREPARE stmt FROM @tempsql;
    EXECUTE stmt;

    START TRANSACTION READ ONLY;

    SELECT Group_concat(DISTINCT Concat( '
        MAX(IF(md.meta_key = ''', meta_key, ''', md.metadata_value, "")) AS `', meta_key , '`' ) )
    INTO   @sql
    FROM listofmetacols AS md;
    SET @sql = concat('INSERT INTO `temppivot` SELECT person_id, year_id, term_id, ',
    @sql, '
    FROM (
        (SELECT STRAIGHT_JOIN
        LP.person_id,
        IFNULL(oay.year_id,"") AS year_id,
        IFNULL(oat.term_code,"") AS term_id,
        LM.meta_key,
        IFNULL(COALESCE(pem.metadata_value, pom.metadata_value),"") AS metadata_value
    #--Construct the base product
    FROM listofpeeps AS LP
    CROSS JOIN listofmetacols AS LM
    #--Attach EBI metadata
    LEFT JOIN person_ebi_metadata AS pem
        ON pem.person_id = LP.person_id
    AND pem.ebi_metadata_id = LM.em_id
    #--Attach ORG metadata
    LEFT JOIN person_org_metadata AS pom
        ON pom.person_id = LP.person_id
    AND pom.org_metadata_id = LM.om_id
    #--Add bonus data
    LEFT JOIN org_academic_year AS oay
        ON oay.id = COALESCE(pem.org_academic_year_id, pom.org_academic_year_id)
    LEFT JOIN org_academic_terms AS oat
        ON oat.id = COALESCE(pem.org_academic_terms_id, pom.org_academic_periods_id)
    #--Order it...on second thought...order last so we can use names
    #--ORDER BY person_id, oay.year_id, oat.term_code ASC
    )
    union
    ( select person_id ,oay.year_id,"" as term_id,"" as meta_key,"" as meta_val from org_person_student_cohort as opc
left join org_academic_year as oay ON opc.org_academic_year_id = oay.id where opc.organization_id = ',orgId,')
    )
    AS
      md GROUP BY 1,2,3 #--the numbers designate columns in the result'
    );


    PREPARE stmt FROM @sql;
    EXECUTE stmt;



    SET @metalist = NULL;
    SELECT CONCAT("ExternalId,AuthUsername,Firstname,Lastname,Title,RecordType,StudentPhoto,IsActive,Participating,RetentionTrack,EnrolledAtMidpointOfAcademicYear,EnrolledAtBeginningOfAcademicYear,CompletedADegree,SurveyCohort,TransitiononeReceiveSurvey,CheckuponeReceiveSurvey,TransitiontwoReceiveSurvey,CheckuptwoReceiveSurvey,YearId,TermId,PrimaryConnect,RiskGroupId,StudentAuthKey,IsPrivacyPolicyAccepted,PrivacyPolicyAcceptedDate,Address1,Address2,City,State,Country,Zip,PrimaryMobile,AlternateMobile,HomePhone,PrimaryEmail,AlternateEmail,PrimaryMobileProvider,AlternateMobileProvider,", Group_concat("`", meta_key, "`"))
    INTO   @metalist
    FROM   listofmetacols AS md;

    SET @resultsql = CONCAT('
    SELECT distinct ',@metalist,' FROM (SELECT

    distinct
        IFNULL(p.external_id,"") AS ExternalId,
        IFNULL(p.auth_username,"") AS AuthUsername,
        IFNULL(p.firstname,"") AS Firstname,
        IFNULL(p.lastname,"") AS Lastname,
        IFNULL(pinfo.title,"") AS Title,
        IFNULL(pinfo.record_type,"") AS RecordType,
        IFNULL(op.photo_url, "") AS StudentPhoto,
		    IFNULL(opsy.is_active, if(surveyResponse.year_id AND surveyResponse.term_id = "", "0", "")) AS IsActive,
        IF(opsy.id, 1, if(surveyResponse.year_id AND surveyResponse.term_id = "", "0", "")) AS Participating,
        IF(opsrtg.id,1,"") AS RetentionTrack,
        IFNULL(opsr.is_enrolled_beginning_year,"") AS EnrolledAtBeginningOfAcademicYear,
        IFNULL(opsr.is_enrolled_midyear,"") AS EnrolledAtMidpointOfAcademicYear,
        IFNULL(opsr.is_degree_completed,"") AS CompletedADegree,
        IFNULL(surveyResponse.cohort, "") AS SurveyCohort,

        TransitiononeReceiveSurvey,
        CheckuponeReceiveSurvey,
        TransitiontwoReceiveSurvey,
        CheckuptwoReceiveSurvey,

        surveyResponse.year_id AS YearId,
        surveyResponse.term_id AS TermId,
        IFNULL(pc.external_id, "") AS PrimaryConnect,
        IFNULL(rh.risk_group_id, "") AS RiskGroupId,
        IFNULL(opinfo.auth_key,"") AS StudentAuthKey,
        IFNULL(op.is_privacy_policy_accepted,"") AS IsPrivacyPolicyAccepted,
        IFNULL(op.privacy_policy_accepted_date, "") AS PrivacyPolicyAcceptedDate,
        IFNULL(ci.address_1,"") AS Address1,
        IFNULL(ci.address_2,"") AS Address2,
        IFNULL(ci.city,"") AS City,
        IFNULL(ci.state,"") AS State,
        IFNULL(ci.country,"") AS Country,
        IFNULL(ci.zip,"") AS Zip,
        IFNULL(ci.primary_mobile,"") AS PrimaryMobile,
        IFNULL(ci.alternate_mobile,"") AS AlternateMobile,
        IFNULL(ci.home_phone,"") AS HomePhone,
        IFNULL(cinfo.primary_email,"") AS PrimaryEmail,
        IFNULL(ci.alternate_email,"") AS AlternateEmail,
        IFNULL(ci.primary_mobile_provider,"") AS PrimaryMobileProvider,
        IFNULL(ci.alternate_mobile_provider,"") AS AlternateMobileProvider,
        innie.*
    from

     (SELECT
        TransitiononeReceiveSurvey,
            CheckuponeReceiveSurvey,
            TransitiontwoReceiveSurvey,
            CheckuptwoReceiveSurvey,
            person_id,
            year_id,
            organization_id,
            "" AS term_id,
            ifnull(cohort, "") as cohort
    FROM
        (SELECT
        SUM(TransitiononeReceiveSurvey) AS TransitiononeReceiveSurvey,
            SUM(CheckuponeReceiveSurvey) AS CheckuponeReceiveSurvey,
            SUM(TransitiontwoReceiveSurvey) AS TransitiontwoReceiveSurvey,
            SUM(CheckuptwoReceiveSurvey) AS CheckuptwoReceiveSurvey,
            person_id,
            year_id,
            survey_Response.organization_id,
            cohort
    FROM
        (SELECT
        IF(SL.survey_id IN (SELECT
                    MIN(id)
                FROM
                    survey
                WHERE
                    year_id IS NOT NULL
                GROUP BY year_id), opss1.receive_survey, NULL) AS TransitiononeReceiveSurvey,
            IF(SL.survey_id IN (SELECT
                    MIN(id) + 1
                FROM
                    survey
                WHERE
                    year_id IS NOT NULL
                GROUP BY year_id), opss1.receive_survey, NULL) AS CheckuponeReceiveSurvey,
            IF(SL.survey_id IN (SELECT
                    MAX(id) - 1
                FROM
                    survey
                WHERE
                    year_id IS NOT NULL
                GROUP BY year_id), opss1.receive_survey, NULL) AS TransitiontwoReceiveSurvey,
            IF(SL.survey_id IN (SELECT
                    MAX(id)
                FROM
                    survey
                WHERE
                    year_id IS NOT NULL
                GROUP BY year_id), opss1.receive_survey, NULL) AS CheckuptwoReceiveSurvey,
            ops.person_id,
            org_academic_year.year_id,
            org_academic_year.organization_id AS organization_id,
            opsc.cohort
    FROM
        org_person_student AS ops
    INNER JOIN org_academic_year ON org_academic_year.organization_id = ops.organization_id  AND org_academic_year.deleted_at IS NULL
    LEFT JOIN org_person_student_survey AS opss1 ON opss1.person_id = ops.person_id
        AND opss1.deleted_at IS NULL
    LEFT JOIN survey AS s ON (opss1.survey_id = s.id)
        AND org_academic_year.year_id = s.year_id
    LEFT JOIN survey_lang AS SL ON s.id = SL.survey_id
    LEFT JOIN org_person_student_cohort opsc ON opsc.person_id = ops.person_id
        AND opsc.org_academic_year_id = org_academic_year.id AND opsc.deleted_at IS NULL
    WHERE
        ops.organization_id = ',orgId,'
        ) AS survey_Response
    GROUP BY person_id , year_id) AS SR UNION SELECT
        NULL AS a,
            NULL AS b,
            NULL AS c,
            NULL AS d,
            person_id,
            "" AS e,
            organization_id,
            "" AS f,
            "" AS g
    FROM
        org_person_student
    WHERE
        organization_id = ',orgId,'
        AND org_person_student.deleted_at IS NULL
        UNION SELECT
        NULL,
            NULL,
            NULL,
            NULL,
            person_id,
            org_academic_year.year_id,
            org_person_student.organization_id,
            org_academic_terms.term_code,
            ""
    FROM
        org_person_student
    INNER JOIN org_academic_terms ON org_academic_terms.organization_id = org_person_student.organization_id
    INNER JOIN org_academic_year ON org_academic_terms.org_academic_year_id = org_academic_year.id
    WHERE
        org_person_student.organization_id = ',orgId,'
        AND org_academic_terms.deleted_at IS NULL
        AND org_academic_year.deleted_at IS NULL
-- ORDER BY person_id , year_id

) as surveyResponse

     left join listofpeeps l on l.person_id = surveyResponse.person_id
     left join person p on p.deleted_at IS NULL and p.id = l.person_id and p.id = surveyResponse.person_id
     left join temppivot AS innie on innie.person_id = p.id and surveyResponse.term_id = innie.term_id and surveyResponse.year_id = innie.year_id
     left join org_person_student op on op.deleted_at IS NULL and
 op.person_id = l.person_id

     left join org_academic_year as oay on    oay.deleted_at IS NULL and ((surveyResponse.year_id = oay.year_id) and (oay.organization_id = op.organization_id ))
	   LEFT JOIN org_person_student_year as opsy on opsy.person_id = p.id AND oay.id = opsy.org_academic_year_id AND opsy.deleted_at IS NULL AND surveyResponse.term_id = ""
     LEFT JOIN org_person_student_retention_tracking_group opsrtg ON opsrtg.person_id = p.id AND   opsrtg.org_academic_year_id = oay.id  AND opsrtg.deleted_at IS NULL AND surveyResponse.term_id = ""
     LEFT JOIN org_person_student_retention opsr ON opsr.person_id = p.id  AND   opsr.org_academic_year_id = oay.id AND opsr.deleted_at IS NULL AND surveyResponse.term_id = ""
     left join org_person_student_cohort as opsc ON       opsc.deleted_at IS NULL and ((p.id  = opsc.person_id) and (p.organization_id =  opsc.organization_id) and (oay.id = opsc.org_academic_year_id ) and (surveyResponse.term_id = "")) and surveyResponse.cohort = opsc.cohort

     left join person pinfo on pinfo.deleted_at IS NULL and pinfo.id = innie.person_id AND innie.year_id="" AND innie.term_id="" AND (innie.year_id="" or innie.year_id is null) #--We do this so that some facts only appear on the non-year/non-term row
     left join person_contact_info pci on     pci.deleted_at IS NULL and pci.person_id = pinfo.id
     left join contact_info ci on ci.id = pci.contact_id
     left join person_contact_info pcinfo on pcinfo.deleted_at IS NULL and pcinfo.person_id = p.id
     left join contact_info cinfo on cinfo.id = pcinfo.contact_id
     left join org_person_student opinfo on opinfo.person_id = pinfo.id
     left join person as pc on    pc.deleted_at IS NULL AND pc.id = op.person_id_primary_connect
     left join risk_group_person_history as rh on rh.person_id = (SELECT rgh.person_id FROM risk_group_person_history as rgh WHERE rgh.person_id = p.id ORDER BY rgh.assignment_date DESC LIMIT 1)
      where
p.external_id != "" #-- remove rows that do not have external_ids

AND
(
!(TransitiononeReceiveSurvey IS NULL AND CheckuponeReceiveSurvey IS NULL AND TransitiontwoReceiveSurvey IS NULL AND CheckuptwoReceiveSurvey IS NULL
AND innie.person_id IS NULL AND opsy.id IS NULL AND opsrtg.id IS NULL AND opsr.id IS NULL)
)
     ORDER BY p.firstname, p.lastname, ExternalId, YearId, TermId ASC
    ) AS results
    ');

    PREPARE stmt FROM @resultsql;
    EXECUTE stmt;

    COMMIT;

    SET group_concat_max_len = 1024;

    END
EOD;

        $this->addSql($procedure);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
