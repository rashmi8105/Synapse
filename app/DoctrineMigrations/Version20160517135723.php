<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Inserts system data into the new success-marker-related tables.
 */
class Version20160517135723 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $this->addSql("INSERT INTO success_marker (`name`, sequence, needs_color_calculated, created_at, modified_at, created_by, modified_by)
                      VALUES ('Academic', 1, 1, NOW(), NOW(), -25, -25);");

        $this->addSql("INSERT INTO success_marker (`name`, sequence, needs_color_calculated, created_at, modified_at, created_by, modified_by)
                      VALUES ('Behaviors and Activities', 2, 1, NOW(), NOW(), -25, -25);");

        $this->addSql("INSERT INTO success_marker (`name`, sequence, needs_color_calculated, created_at, modified_at, created_by, modified_by)
                      VALUES ('Financial Means', 3, 1, NOW(), NOW(), -25, -25);");

        $this->addSql("INSERT INTO success_marker (`name`, sequence, needs_color_calculated, created_at, modified_at, created_by, modified_by)
                      VALUES ('Performance and Expectations', 4, 1, NOW(), NOW(), -25, -25);");

        $this->addSql("INSERT INTO success_marker (`name`, sequence, needs_color_calculated, created_at, modified_at, created_by, modified_by)
                      VALUES ('Socio-Emotional', 5, 1, NOW(), NOW(), -25, -25);");

        $this->addSql("INSERT INTO success_marker (`name`, sequence, needs_color_calculated, created_at, modified_at, created_by, modified_by)
                      VALUES ('Student Populations', 6, 0, NOW(), NOW(), -25, -25);");

        $this->addSql("INSERT INTO success_marker (`name`, sequence, needs_color_calculated, created_at, modified_at, created_by, modified_by)
                      VALUES ('Student Topics', 7, 0, NOW(), NOW(), -25, -25);");


        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Academic Integration', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Academic';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Academic Resiliency', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Academic';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Academic Self-Efficacy', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Academic';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Advanced Study Skills', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Academic';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Analytical Skills', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Academic';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Chosen a Major', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Academic';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Commitment to a Major', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Academic';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Commitment to Earning a Degree', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Academic';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Communication Skills', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Academic';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Course Difficulties', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Academic';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Selected a Career Path', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Academic';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Advanced Academic Behaviors', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Behaviors and Activities';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Basic Academic Behaviors', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Behaviors and Activities';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Class Attendance', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Behaviors and Activities';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Family Interference with Coursework', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Behaviors and Activities';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Number of Study Hours Per Week', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Behaviors and Activities';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Number of Work Hours Per Week', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Behaviors and Activities';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Self-Discipline', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Behaviors and Activities';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Student Organization Involvement', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Behaviors and Activities';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Time Management', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Behaviors and Activities';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Work Interference with Coursework', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Behaviors and Activities';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Financial Means', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Financial Means';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Expected Cumulative GPA Upon Completion/Graduation', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Performance and Expectations';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Expected Grades this Term', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Performance and Expectations';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'High School GPA (Self-Reported)', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Performance and Expectations';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT \"Parents'/Guardians' Educational Level\", id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Performance and Expectations';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Commitment to the Institution', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Socio-Emotional';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Homesickness: Distressed', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Socio-Emotional';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Homesickness: Separation', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Socio-Emotional';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Institutional Choice', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Socio-Emotional';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Living Environment (Off Campus)', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Socio-Emotional';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Living Environment (On Campus)', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Socio-Emotional';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'On-Campus Living: Roommates', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Socio-Emotional';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'On-Campus Living: Social Aspects', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Socio-Emotional';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Peer Connections', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Socio-Emotional';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Satisfaction with Institution', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Socio-Emotional';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Social Integration', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Socio-Emotional';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Active Military or Veteran', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Student Populations';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'External Commitments', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Student Populations';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Fraternity/Sorority Member', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Student Populations';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Off-Campus Student', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Student Populations';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Student Athlete', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Student Populations';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Transfer Student', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Student Populations';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Academic Major Evaluation', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Student Topics';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Academic/Career Planning', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Student Topics';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Post-Graduation/Completion Plans', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Student Topics';");

        $this->addSql("INSERT INTO success_marker_topic (`name`, success_marker_id, created_at, modified_at, created_by, modified_by)
                        SELECT 'Test Anxiety (Stressors)', id, NOW(), NOW(), -25, -25
                        FROM success_marker
                        WHERE `name` = 'Student Topics';");


        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Academic Integration'
                            AND fl.name = 'Academic Integration';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Academic Resiliency'
                            AND fl.name = 'Academic Resiliency';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Academic Self-Efficacy'
                            AND fl.name = 'Academic Self-Efficacy';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Advanced Academic Behaviors'
                            AND fl.name = 'Advanced Academic Behaviors';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Advanced Study Skills'
                            AND fl.name = 'Advanced Study Skills';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Analytical Skills'
                            AND fl.name = 'Self-Assessment: Analytical Skills';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Basic Academic Behaviors'
                            AND fl.name = 'Basic Academic Behaviors';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Commitment to the Institution'
                            AND fl.name = 'Commitment to the Institution';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Communication Skills'
                            AND fl.name = 'Self-Assessment: Communication Skills';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Financial Means'
                            AND fl.name = 'Financial Means';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Homesickness: Distressed'
                            AND fl.name = 'Homesickness: Distressed';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Homesickness: Separation'
                            AND fl.name = 'Homesickness: Separation';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Living Environment (On Campus)'
                            AND fl.name = 'On-Campus Living: Environment';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Living Environment (Off Campus)'
                            AND fl.name = 'Off-Campus Living: Environment';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'On-Campus Living: Roommates'
                            AND fl.name = 'On-Campus Living: Roommate Relationship';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'On-Campus Living: Social Aspects'
                            AND fl.name = 'On-Campus Living: Social Aspects';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Peer Connections'
                            AND fl.name = 'Peer Connections';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Satisfaction with Institution'
                            AND fl.name = 'Satisfaction with Institution';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Self-Discipline'
                            AND fl.name = 'Self-Assessment: Self-Discipline';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Social Integration'
                            AND fl.name = 'Social Integration';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Test Anxiety (Stressors)'
                            AND fl.name = 'Test Anxiety';");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, factor_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, fl.factor_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, factor_lang fl
                        WHERE smt.name = 'Time Management'
                            AND fl.name = 'Self-Assessment: Time Management';");


        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Academic Integration'
                            AND sq.qnbr in (209, 210, 211, 212)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Academic Major Evaluation'
                            AND sq.qnbr in (145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Academic Resiliency'
                            AND sq.qnbr in (85, 86, 87, 88)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Academic Self-Efficacy'
                            AND sq.qnbr in (82, 83, 84)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Academic/Career Planning'
                            AND sq.qnbr in (157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169, 170, 171, 172)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Active Military or Veteran'
                            AND sq.qnbr in (199, 200, 201, 202, 203, 204, 205)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Advanced Academic Behaviors'
                            AND sq.qnbr in (89, 90, 91, 92, 93, 94, 95)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Advanced Study Skills'
                            AND sq.qnbr in (92, 93, 94)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Analytical Skills'
                            AND sq.qnbr in (67, 68)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Basic Academic Behaviors'
                            AND sq.qnbr in (23, 24, 25, 95)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Chosen a Major'
                            AND sq.qnbr in (139, 140, 141, 142, 143, 144)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Class Attendance'
                            AND sq.qnbr in (20)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Commitment to a Major'
                            AND sq.qnbr in (145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Commitment to Earning a Degree'
                            AND sq.qnbr in (1, 2, 26)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Commitment to the Institution'
                            AND sq.qnbr in (2, 3, 4, 5, 6, 7, 8, 9)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Communication Skills'
                            AND sq.qnbr in (69, 70)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Course Difficulties'
                            AND sq.qnbr in (10, 11, 12, 13, 14, 15, 16, 17)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Expected Cumulative GPA Upon Completion/Graduation'
                            AND sq.qnbr in (22)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Expected Grades this Term'
                            AND sq.qnbr in (21, 32, 33, 39, 40)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'External Commitments'
                            AND sq.qnbr in (132, 133, 134, 135, 136, 137)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Family Interference with Coursework'
                            AND sq.qnbr in (128, 129, 130)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Financial Means'
                            AND sq.qnbr in (63, 64, 65, 66)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Fraternity/Sorority Member'
                            AND sq.qnbr in (193, 194, 195, 196, 197)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'High School GPA (Self-Reported)'
                            AND sq.qnbr in (29, 30, 31)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Homesickness: Distressed'
                            AND sq.qnbr in (119, 123, 124, 125, 126, 127)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Homesickness: Separation'
                            AND sq.qnbr in (119, 120, 121, 122)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Institutional Choice'
                            AND sq.qnbr in (28)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Living Environment (On Campus)'
                            AND sq.qnbr in (46, 47, 48)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Living Environment (Off Campus)'
                            AND sq.qnbr in (53, 54, 55)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Number of Study Hours Per Week'
                            AND sq.qnbr in (19, 32, 33, 39, 40, 79, 80, 81, 131)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Number of Work Hours Per Week'
                            AND sq.qnbr in (18, 19, 79, 80, 81, 131)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Off-Campus Student'
                            AND sq.qnbr in (54, 55, 56, 57, 58, 59, 60, 61, 62, 138)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'On-Campus Living: Roommates'
                            AND sq.qnbr in (49, 50, 51, 52)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'On-Campus Living: Social Aspects'
                            AND sq.qnbr in (43, 44, 45)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = \"Parents'/Guardians' Educational Level\"
                            AND sq.qnbr in (27)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Peer Connections'
                            AND sq.qnbr in (116, 117, 118)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Post-Graduation/Completion Plans'
                            AND sq.qnbr in (173, 174, 175, 176, 177, 178, 179)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Satisfaction with Institution'
                            AND sq.qnbr in (216, 217, 218)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Selected a Career Path'
                            AND sq.qnbr in (170, 171, 172)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Self-Discipline'
                            AND sq.qnbr in (71, 72, 73)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Social Integration'
                            AND sq.qnbr in (213, 214, 215)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Student Athlete'
                            AND sq.qnbr in (181, 182, 183, 184, 185, 186, 187, 188, 189, 190, 191)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Student Organization Involvement'
                            AND sq.qnbr in (104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Test Anxiety (Stressors)'
                            AND sq.qnbr in (96, 97, 98, 99, 100, 101, 102, 103)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Time Management'
                            AND sq.qnbr in (74, 75, 76, 77, 78)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Transfer Student'
                            AND sq.qnbr in (35, 36, 37, 38, 41)
                            AND sq.survey_id = 11;");

        $this->addSql("INSERT INTO success_marker_topic_detail (topic_id, ebi_question_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, sq.ebi_question_id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt, survey_questions sq
                        WHERE smt.name = 'Work Interference with Coursework'
                            AND sq.qnbr in (128, 129, 130)
                            AND sq.survey_id = 11;");


        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        WHERE smtd.factor_id IS NOT NULL
                        ORDER BY smt.id;");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 139
                            AND smt.name = 'Chosen a Major';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 148
	                        AND smt.name = 'Commitment to a Major';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 2
	                        AND smt.name = 'Commitment to Earning a Degree';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 11
	                        AND smt.name = 'Course Difficulties';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 170
	                        AND smt.name = 'Selected a Career Path';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 20
	                        AND smt.name = 'Class Attendance';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 128
	                        AND smt.name = 'Family Interference with Coursework';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 19
	                        AND smt.name = 'Number of Study Hours Per Week';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 131
	                        AND smt.name = 'Number of Work Hours Per Week';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 104
	                        AND smt.name = 'Student Organization Involvement';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 129
	                        AND smt.name = 'Work Interference with Coursework';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 22
	                        AND smt.name = 'Expected Cumulative GPA Upon Completion/Graduation';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 21
	                        AND smt.name = 'Expected Grades this Term';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 30
	                        AND smt.name = 'High School GPA (Self-Reported)';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 28
	                        AND smt.name = 'Institutional Choice';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 204
	                        AND smt.name = 'Active Military or Veteran';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 196
	                        AND smt.name = 'Fraternity/Sorority Member';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 55
	                        AND smt.name = 'Off-Campus Student';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 187
	                        AND smt.name = 'Student Athlete';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 35
	                        AND smt.name = 'Transfer Student';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 150
	                        AND smt.name = 'Academic Major Evaluation';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 157
	                        AND smt.name = 'Academic/Career Planning';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 175
	                        AND smt.name = 'Post-Graduation/Completion Plans';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 136
	                        AND smt.name = 'External Commitments';");

        $this->addSql("INSERT INTO success_marker_topic_representative (topic_id, representative_detail_id, created_at, modified_at, created_by, modified_by)
                        SELECT smt.id, smtd.id, NOW(), NOW(), -25, -25
                        FROM success_marker_topic smt
                        INNER JOIN success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = smtd.ebi_question_id
                        WHERE sq.qnbr = 27
	                        AND smt.name = \"Parents'/Guardians' Educational Level\";");


        $this->addSql("INSERT INTO success_marker_topic_detail_color (topic_detail_id, color, min_value, max_value, created_at, modified_at, created_by, modified_by)
                        SELECT *, NOW(), NOW(), -25, -25
                        FROM
                        (
                                SELECT id, 'red' AS color, 1, 2.999
                                FROM success_marker_topic_detail
                                WHERE factor_id IS NOT NULL
                            UNION
                                SELECT id, 'yellow' AS color, 3, 5.999
                                FROM success_marker_topic_detail
                                WHERE factor_id IS NOT NULL
                            UNION
                                SELECT id, 'green' AS color, 6, 7
                                FROM success_marker_topic_detail
                                WHERE factor_id IS NOT NULL
                        ) AS colors
                        ORDER BY id, FIELD(color, 'red', 'yellow', 'green');");

        $this->addSql("INSERT INTO success_marker_topic_detail_color (topic_detail_id, color, min_value, max_value, created_at, modified_at, created_by, modified_by)
                        SELECT *, NOW(), NOW(), -25, -25
                        FROM
                        (
                                SELECT smtd.id, 'red' AS color, 1, 2.999
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (1,2,12,13,14,23,24,25,43,44,45,46,47,48,50,51,52,53,54,55,60,61,62,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,116,117,118,120,121,122,123,124,125,126,128,129,130,134,135,136,137,140,148,149,150,151,152,153,154,155,157,158,159,160,161,162,163,165,166,167,168,169,171,186,187,195,203,204,209,210,211,212,213,214,215,216,217,218)
                            UNION
                                SELECT smtd.id, 'yellow' AS color, 3, 5.999
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (1,2,12,13,14,23,24,25,43,44,45,46,47,48,50,51,52,53,54,55,60,61,62,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,116,117,118,120,121,122,123,124,125,126,128,129,130,134,135,136,137,140,148,149,150,151,152,153,154,155,157,158,159,160,161,162,163,165,166,167,168,169,171,186,187,195,203,204,209,210,211,212,213,214,215,216,217,218)
                            UNION
                                SELECT smtd.id, 'green' AS color, 6, 7
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (1,2,12,13,14,23,24,25,43,44,45,46,47,48,50,51,52,53,54,55,60,61,62,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,116,117,118,120,121,122,123,124,125,126,128,129,130,134,135,136,137,140,148,149,150,151,152,153,154,155,157,158,159,160,161,162,163,165,166,167,168,169,171,186,187,195,203,204,209,210,211,212,213,214,215,216,217,218)
                        ) AS colors
                        ORDER BY id, FIELD(color, 'red', 'yellow', 'green');");

        $this->addSql("INSERT INTO success_marker_topic_detail_color (topic_detail_id, color, min_value, max_value, created_at, modified_at, created_by, modified_by)
                        SELECT *, NOW(), NOW(), -25, -25
                        FROM
                        (
                                SELECT smtd.id, 'red' AS color, 1, 3.999
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (3, 4)
                            UNION
                                SELECT smtd.id, 'yellow' AS color, 4, 5.999
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (3, 4)
                            UNION
                                SELECT smtd.id, 'green' AS color, 6, 7
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (3, 4)
                        ) AS colors
                        ORDER BY id, FIELD(color, 'red', 'yellow', 'green');");

        $this->addSql("INSERT INTO success_marker_topic_detail_color (topic_detail_id, color, min_value, max_value, created_at, modified_at, created_by, modified_by)
                        SELECT *, NOW(), NOW(), -25, -25
                        FROM
                        (
                                SELECT smtd.id, 'red' AS color, 1, 2
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (104, 196)
                            UNION
                                SELECT smtd.id, 'yellow' AS color, 3, 5
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (104, 196)
                            UNION
                                SELECT smtd.id, 'green' AS color, 6, 7
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (104, 196)
                        ) AS colors
                        ORDER BY id, FIELD(color, 'red', 'yellow', 'green');");

        $this->addSql("INSERT INTO success_marker_topic_detail_color (topic_detail_id, color, min_value, max_value, created_at, modified_at, created_by, modified_by)
                        SELECT *, NOW(), NOW(), -25, -25
                        FROM
                        (
                                SELECT smtd.id, 'red' AS color, 0, 2
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (19)
                            UNION
                                SELECT smtd.id, 'yellow' AS color, 3, 4
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (19)
                            UNION
                                SELECT smtd.id, 'green' AS color, 5, 9
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (19)
                        ) AS colors
                        ORDER BY id, FIELD(color, 'red', 'yellow', 'green');");

        $this->addSql("INSERT INTO success_marker_topic_detail_color (topic_detail_id, color, min_value, max_value, created_at, modified_at, created_by, modified_by)
                        SELECT *, NOW(), NOW(), -25, -25
                        FROM
                        (
                                SELECT smtd.id, 'red' AS color, 0 AS min, 0 AS max
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (27)
                            UNION
                                SELECT smtd.id, 'yellow' AS color, 1 AS min, 1 AS max
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (27)
                            UNION
                                SELECT smtd.id, 'green' AS color, 2, 4
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (27)
                        ) AS colors
                        ORDER BY id, FIELD(color, 'red', 'yellow', 'green');");

        $this->addSql("INSERT INTO success_marker_topic_detail_color (topic_detail_id, color, min_value, max_value, created_at, modified_at, created_by, modified_by)
                        SELECT *, NOW(), NOW(), -25, -25
                        FROM
                        (
                                SELECT smtd.id, 'red' AS color, 0 AS min, 0 AS max
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (139, 170)
                            UNION
                                SELECT smtd.id, 'green' AS color, 1 AS min, 1 AS max
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (139, 170)
                        ) AS colors
                        ORDER BY id, FIELD(color, 'red', 'yellow', 'green');");

        $this->addSql("INSERT INTO success_marker_topic_detail_color (topic_detail_id, color, min_value, max_value, created_at, modified_at, created_by, modified_by)
                        SELECT *, NOW(), NOW(), -25, -25
                        FROM
                        (
                                SELECT smtd.id, 'red' AS color, 2 AS min, 2 AS max
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (175)
                            UNION
                                SELECT smtd.id, 'green' AS color, 0, 1
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (175)
                        ) AS colors
                        ORDER BY id, FIELD(color, 'red', 'yellow', 'green');");

        $this->addSql("INSERT INTO success_marker_topic_detail_color (topic_detail_id, color, min_value, max_value, created_at, modified_at, created_by, modified_by)
                        SELECT *, NOW(), NOW(), -25, -25
                        FROM
                        (
                                SELECT smtd.id, 'red' AS color, 5, 9
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (18, 131)
                            UNION
                                SELECT smtd.id, 'yellow' AS color, 3, 4
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (18, 131)
                            UNION
                                SELECT smtd.id, 'green' AS color, 0, 2
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (18, 131)
                        ) AS colors
                        ORDER BY id, FIELD(color, 'red', 'yellow', 'green');");

        $this->addSql("INSERT INTO success_marker_topic_detail_color (topic_detail_id, color, min_value, max_value, created_at, modified_at, created_by, modified_by)
                        SELECT *, NOW(), NOW(), -25, -25
                        FROM
                        (
                                SELECT smtd.id, 'red' AS color, 5, 6
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (184, 194, 202)
                            UNION
                                SELECT smtd.id, 'yellow' AS color, 3, 4
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (184, 194, 202)
                            UNION
                                SELECT smtd.id, 'green' AS color, 0, 2
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (184, 194, 202)
                        ) AS colors
                        ORDER BY id, FIELD(color, 'red', 'yellow', 'green');");

        $this->addSql("INSERT INTO success_marker_topic_detail_color (topic_detail_id, color, min_value, max_value, created_at, modified_at, created_by, modified_by)
                        SELECT *, NOW(), NOW(), -25, -25
                        FROM
                        (
                                SELECT smtd.id, 'red' AS color, 2, 6
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (11)
                            UNION
                                SELECT smtd.id, 'yellow' AS color, 1 AS min, 1 AS max
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (11)
                            UNION
                                SELECT smtd.id, 'green' AS color, 0 AS min, 0 AS max
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (11)
                        ) AS colors
                        ORDER BY id, FIELD(color, 'red', 'yellow', 'green');");

        $this->addSql("INSERT INTO success_marker_topic_detail_color (topic_detail_id, color, min_value, max_value, created_at, modified_at, created_by, modified_by)
                        SELECT *, NOW(), NOW(), -25, -25
                        FROM
                        (
                                SELECT smtd.id, 'red' AS color, 2, 4
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (20)
                            UNION
                                SELECT smtd.id, 'yellow' AS color, 1 AS min, 1 AS max
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (20)
                            UNION
                                SELECT smtd.id, 'green' AS color, 0 AS min, 0 AS max
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (20)
                        ) AS colors
                        ORDER BY id, FIELD(color, 'red', 'yellow', 'green');");

        $this->addSql("INSERT INTO success_marker_topic_detail_color (topic_detail_id, color, min_value, max_value, created_at, modified_at, created_by, modified_by)
                        SELECT *, NOW(), NOW(), -25, -25
                        FROM
                        (
                                SELECT smtd.id, 'red' AS color, 2, 3
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (28, 35)
                            UNION
                                SELECT smtd.id, 'yellow' AS color, 1 AS min, 1 AS max
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (28, 35)
                            UNION
                                SELECT smtd.id, 'green' AS color, 0 AS min, 0 AS max
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (28, 35)
                        ) AS colors
                        ORDER BY id, FIELD(color, 'red', 'yellow', 'green');");

        $this->addSql("INSERT INTO success_marker_topic_detail_color (topic_detail_id, color, min_value, max_value, created_at, modified_at, created_by, modified_by)
                        SELECT *, NOW(), NOW(), -25, -25
                        FROM
                        (
                                SELECT smtd.id, 'red' AS color, 3, 4
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (21, 22, 30)
                            UNION
                                SELECT smtd.id, 'yellow' AS color, 2 AS min, 2 AS max
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (21, 22, 30)
                            UNION
                                SELECT smtd.id, 'green' AS color, 0, 1
                                FROM success_marker_topic_detail smtd
                                INNER JOIN survey_questions sq
                                    ON sq.ebi_question_id = smtd.ebi_question_id
                                WHERE sq.qnbr IN (21, 22, 30)
                        ) AS colors
                        ORDER BY id, FIELD(color, 'red', 'yellow', 'green');");


        $this->addSql("INSERT INTO success_marker_color (color, base_value, min_value, max_value, created_at, modified_at, created_by, modified_by)
                      VALUES ('red', 1, 1, 1.750, NOW(), NOW(), -25, -25);");

        $this->addSql("INSERT INTO success_marker_color (color, base_value, min_value, max_value, created_at, modified_at, created_by, modified_by)
                      VALUES ('yellow', 2, 1.751, 2.249, NOW(), NOW(), -25, -25);");

        $this->addSql("INSERT INTO success_marker_color (color, base_value, min_value, max_value, created_at, modified_at, created_by, modified_by)
                      VALUES ('green', 3, 2.250, 3, NOW(), NOW(), -25, -25);");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
