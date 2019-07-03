<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Reorganizes the columns in ebi_search, adds a few columns (including some from ebi_search_lang), populates them,
 * and changes the names and keys of the predefined searches.
 */
class Version20160902050150 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $sql = "SELECT 1
                FROM
                    INFORMATION_SCHEMA.COLUMNS
                WHERE
                    TABLE_NAME = 'ebi_search'
                    AND TABLE_SCHEMA = 'synapse'
                    AND COLUMN_NAME = 'category';";

        $results = $this->connection->executeQuery($sql)->fetchAll();

        if (empty($results)) {
            $this->addSql("ALTER TABLE ebi_search ADD COLUMN category VARCHAR(31) AFTER id;");
        }


        $this->addSql("ALTER TABLE ebi_search CHANGE COLUMN query_key query_key VARCHAR(255) AFTER category;");


        $sql = "SELECT 1
                FROM
                    INFORMATION_SCHEMA.COLUMNS
                WHERE
                    TABLE_NAME = 'ebi_search'
                    AND TABLE_SCHEMA = 'synapse'
                    AND COLUMN_NAME = 'name';";

        $results = $this->connection->executeQuery($sql)->fetchAll();

        if (empty($results)) {
            $this->addSql("ALTER TABLE ebi_search ADD COLUMN name VARCHAR(255) AFTER query_key;");
        }


        $sql = "SELECT 1
                FROM
                    INFORMATION_SCHEMA.COLUMNS
                WHERE
                    TABLE_NAME = 'ebi_search'
                    AND TABLE_SCHEMA = 'synapse'
                    AND COLUMN_NAME = 'description';";

        $results = $this->connection->executeQuery($sql)->fetchAll();

        if (empty($results)) {
            $this->addSql("ALTER TABLE ebi_search ADD COLUMN description VARCHAR(255) AFTER name;");
        }


        $sql = "SELECT 1
                FROM
                    INFORMATION_SCHEMA.COLUMNS
                WHERE
                    TABLE_NAME = 'ebi_search'
                    AND TABLE_SCHEMA = 'synapse'
                    AND COLUMN_NAME = 'sequence';";

        $results = $this->connection->executeQuery($sql)->fetchAll();

        if (empty($results)) {
            $this->addSql("ALTER TABLE ebi_search ADD COLUMN sequence INT AFTER description;");
        }


        $this->addSql("UPDATE ebi_search SET query_key = 'all_my_students' WHERE query_key = 'All_My_Student';");

        $this->addSql("UPDATE ebi_search SET query_key = 'my_primary_campus_connections' WHERE query_key = 'My_Primary_Campus_Connection';");

        $this->addSql("UPDATE ebi_search SET query_key = 'at_risk_students' WHERE query_key = 'At_Risk';");

        $this->addSql("UPDATE ebi_search SET query_key = 'high_intent_to_leave' WHERE query_key = 'High_Intent_To_Leave';");

        $this->addSql("UPDATE ebi_search SET query_key = 'high_priority_students' WHERE query_key = 'High_Priority_Students';");


        $this->addSql("UPDATE ebi_search SET query_key = 'high_risk_of_failure' WHERE query_key = 'At_Risk_Of_Failure';");

        $this->addSql("UPDATE ebi_search SET query_key = 'four_or_more_absences' WHERE query_key = 'Missed_3_Classes';");

        $this->addSql("UPDATE ebi_search SET query_key = 'in-progress_grade_of_c_or_below' WHERE query_key = 'In-progress_Grade_Of_C_Or_Below';");

        $this->addSql("UPDATE ebi_search SET query_key = 'in-progress_grade_of_d_or_below' WHERE query_key = 'In-progress_Grade_Of_D_Or_Below';");

        $this->addSql("UPDATE ebi_search SET query_key = 'two_or_more_in-progress_grades_of_d_or_below' WHERE query_key = 'Students_With_More_Than_One_In-progress_Grade_Of_D_Or_Below';");

        $this->addSql("UPDATE ebi_search SET query_key = 'final_grade_of_c_or_below' WHERE query_key = 'Final_Grade_Of_C_Or_Below';");

        $this->addSql("UPDATE ebi_search SET query_key = 'final_grade_of_d_or_below' WHERE query_key = 'Final_Grade_Of_D_Or_Below';");

        $this->addSql("UPDATE ebi_search SET query_key = 'two_or_more_final_grades_of_d_or_below' WHERE query_key = 'Students_With_More_Than_One_Final_Grade_Of_D_Or_Below';");


        $this->addSql("UPDATE ebi_search SET query_key = 'interaction_contacts' WHERE query_key = 'Interaction_Activity';");

        $this->addSql("UPDATE ebi_search SET query_key = 'no_interaction_contacts' WHERE query_key = 'Non-interaction_Activity';");

        $this->addSql("UPDATE ebi_search SET query_key = 'have_not_been_reviewed' WHERE query_key = 'Have_Not_Been_Reviewed';");


        $this->addSql("UPDATE ebi_search SET category = 'student_search' WHERE query_key IN ('all_my_students', 'my_primary_campus_connections', 'at_risk_students', 'high_intent_to_leave', 'high_priority_students');");

        $this->addSql("UPDATE ebi_search SET category = 'academic_update_search' WHERE query_key IN ('high_risk_of_failure', 'four_or_more_absences', 'in-progress_grade_of_c_or_below', 'in-progress_grade_of_d_or_below', 'two_or_more_in-progress_grades_of_d_or_below', 'final_grade_of_c_or_below', 'final_grade_of_d_or_below', 'two_or_more_final_grades_of_d_or_below');");

        $this->addSql("UPDATE ebi_search SET category = 'activity_search' WHERE query_key IN ('interaction_contacts', 'no_interaction_contacts', 'have_not_been_reviewed');");


        $this->addSql("UPDATE ebi_search SET name = 'All my students', description = 'Students that I am connected to through either a group or course', sequence = 1 WHERE query_key = 'all_my_students';");

        $this->addSql("UPDATE ebi_search SET name = 'My primary campus connections', description = 'Students for whom I am the primary campus connection', sequence = 2 WHERE query_key = 'my_primary_campus_connections';");

        $this->addSql("UPDATE ebi_search SET name = 'At-risk students', description = 'Students with a Red or Red 2 risk indicator', sequence = 3 WHERE query_key = 'at_risk_students';");

        $this->addSql("UPDATE ebi_search SET name = 'Students with a high intent to leave', description = 'Students who have indicated that they intend to leave the institution', sequence = 4 WHERE query_key = 'high_intent_to_leave';");

        $this->addSql("UPDATE ebi_search SET name = 'High priority students', description = 'Students who have not had any interaction contacts since their risk indicator changed to Red or Red 2', sequence = 5 WHERE query_key = 'high_priority_students';");


        $this->addSql("UPDATE ebi_search SET name = 'High risk of failure', description = 'Students with high risk of failure in any course in the current academic term(s)', sequence = 1 WHERE query_key = 'high_risk_of_failure';");

        $this->addSql("UPDATE ebi_search SET name = 'Four or more absences', description = 'Students with four or more absences in any course in the current academic term(s)', sequence = 2 WHERE query_key = 'four_or_more_absences';");

        $this->addSql("UPDATE ebi_search SET name = 'In-progress grade of C or below', description = 'Students with an in-progress grade of C or below in any course in the current academic term(s)', sequence = 3 WHERE query_key = 'in-progress_grade_of_c_or_below';");

        $this->addSql("UPDATE ebi_search SET name = 'In-progress grade of D or below', description = 'Students with an in-progress grade of D or below in any course in the current academic term(s)', sequence = 4 WHERE query_key = 'in-progress_grade_of_d_or_below';");

        $this->addSql("UPDATE ebi_search SET name = 'Two or more in-progress grades of D or below', description = 'Students with an in-progress grades of D or below in two or more courses in the current academic term(s)', sequence = 5 WHERE query_key = 'two_or_more_in-progress_grades_of_d_or_below';");

        $this->addSql("UPDATE ebi_search SET name = 'Final grade of C or below', description = 'Students with a final grade of C or below in any course in the current academic year', sequence = 6 WHERE query_key = 'final_grade_of_c_or_below';");

        $this->addSql("UPDATE ebi_search SET name = 'Final grade of D or below', description = 'Students with a final grade of D or below in any course in the current academic year', sequence = 7 WHERE query_key = 'final_grade_of_d_or_below';");

        $this->addSql("UPDATE ebi_search SET name = 'Two or more final grades of D or below', description = 'Students with a final grade of D or below in two or more courses in the current academic year', sequence = 8 WHERE query_key = 'two_or_more_final_grades_of_d_or_below';");


        $this->addSql("UPDATE ebi_search SET name = 'Students with interaction contacts', description = 'Students who have had interaction contacts logged with them', sequence = 1 WHERE query_key = 'interaction_contacts';");

        $this->addSql("UPDATE ebi_search SET name = 'Students without any interaction contacts', description = 'Students who have had no interaction contacts logged with them', sequence = 2 WHERE query_key = 'no_interaction_contacts';");

        $this->addSql("UPDATE ebi_search SET name = 'Students who have not been reviewed by me since their risk changed', description = 'Students whose profile pages have not been reviewed by me since their risk changed', sequence = 3 WHERE query_key = 'have_not_been_reviewed';");


        $this->addSql("UPDATE ebi_search_lang
                        SET deleted_at = NOW(), deleted_by = -25
                        WHERE description IN ('student_search', 'survey_search', 'academic_update_search', 'activity_search');");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');
    }
}
