<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151202183637 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        //ADDING INDEX fk_datablock_questions_factor1_idx
        $this->addSQL(
        "SET @myquery = safe_index_builder('datablock_questions', 'fk_datablock_questions_factor1_idx', '(`factor_id` ASC, `deleted_at` ASC, `datablock_id` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //DROPPING INDEX org_academic
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_academic_terms', 'org_academic', '', false, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding INDEX org_academic_end
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_academic_terms', 'org_academic_end', '(`organization_id` ASC, `org_academic_year_id` ASC, `end_date` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index last_term
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_academic_terms', 'last_term', '(`id` ASC, `organization_id` ASC, `end_date` ASC, `start_date` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");


        //adding Index 'org_survey_cohort'
        $this->addSQL(
        "SET @myquery = safe_index_builder('wess_link', 'org_survey_cohort', '(`org_id` ASC, `survey_id` ASC, `cohort_code` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index 'opdb'
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_permissionset_datablock', 'opdb', '(`org_permissionset_id` ASC, `datablock_id` ASC, `organization_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index org_opdb
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_permissionset_datablock', 'org_opdb', '(`organization_id` ASC, `datablock_id` ASC, `org_permissionset_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index student-grade
        $this->addSQL(
        "SET @myquery = safe_index_builder('academic_update', 'student-grade', '(`person_id_student` ASC, `grade` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index issue-option
        $this->addSQL(
        "SET @myquery = safe_index_builder('issue_options', 'issue-option', '(`issue_id` ASC, `ebi_question_options_id` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index survey_question
        $this->addSQL(
        "SET @myquery = safe_index_builder('issue', 'survey_question', '(`survey_questions_id` ASC, `survey_id` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Updating Index fk_org_person_student_survey_link_survey1_idx
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_person_student_survey_link', 'fk_org_person_student_survey_link_survey1_idx', '(`survey_id` ASC, `org_id` ASC, `person_id` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index FK_3C3D059132C8A3DE_idx
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_person_faculty', 'FK_3C3D059132C8A3DE_idx', '(`organization_id` ASC, `person_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index FK_3C3D0591217BBB47_idx
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_person_faculty', 'FK_3C3D0591217BBB47_idx', '(`person_id` ASC, `organization_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index fk_org_person_student_organization1
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_person_student', 'fk_org_person_student_organization1', '(`organization_id` ASC, `person_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index fk_org_person_student_organization1
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_person_student', 'fk_org_person_student_person1', '(`person_id` ASC, `organization_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index course-person
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_course_faculty', 'course-person', '(`organization_id` ASC, `org_courses_id` ASC, `person_id` ASC, `org_permissionset_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index person-course
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_course_faculty', 'person-course', '(`person_id` ASC, `organization_id` ASC, `org_courses_id` ASC, `org_permissionset_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index fk_org_courses_organization1_idx
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_courses', 'fk_org_courses_organization1_idx', '(`organization_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index course_org
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_courses', 'course_org', '(`id` ASC, `organization_id` ASC, `org_academic_terms_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index course-person
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_course_student', 'course-person', '(`organization_id` ASC, `org_courses_id` ASC, `person_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

         //Adding Index person-course
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_course_student', 'person-course', '(`person_id` ASC, `organization_id` ASC, `org_courses_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index fk_course_student_org_courses1_idx
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_course_student', 'fk_course_student_org_courses1_idx', '(`org_courses_id` ASC, `organization_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index group-person
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_group_faculty', 'group-person', '(`organization_id` ASC, `org_group_id` ASC, `person_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index person-group
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_group_faculty', 'person-group', '(`person_id` ASC, `organization_id` ASC, `org_group_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

         //Adding Index org_person_group_delete
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_group_faculty', 'org_person_group_delete', '(`organization_id` ASC, `person_id` ASC, `org_group_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index org_person_delete_idx
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_group_faculty', 'org_person_delete_idx', '(`person_id` ASC, `organization_id` ASC, `org_permissionset_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index group-student
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_group_students', 'group-student', '(`organization_id` ASC, `org_group_id` ASC, `person_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");
        
        //Adding Index student-group
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_group_students', 'student-group', '(`person_id` ASC, `organization_id` ASC, `org_group_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index PG_perm
        $this->addSQL(
        "SET @myquery = safe_index_builder('org_group_faculty', 'PG_perm', '(`organization_id` ASC, `person_id` ASC, `org_group_id` ASC, `deleted_at` ASC, `org_permissionset_id` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index 
        $this->addSQL(
        "SET @myquery = safe_index_builder('issue', 'survey_delete', '(`survey_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        //Adding Index 
        $this->addSQL(
        "SET @myquery = safe_index_builder('datablock_questions', 'permfunc', '(`ebi_question_id` ASC, `deleted_at` ASC, `datablock_id` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");
    
   
    
}
    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your 
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
