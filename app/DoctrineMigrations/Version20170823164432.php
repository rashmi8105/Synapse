<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15562 Performance Increases to Retention Completion Calculation
 * Adding Columns to retention completion variable name for column headers to pivot table and sequencing
 */
class Version20170823164432 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE `synapse`.`retention_completion_variable_name` 
            ADD COLUMN `variable` VARCHAR(100) NOT NULL AFTER `type`,
            ADD COLUMN `sequence` INT(11) NULL DEFAULT NULL AFTER `name_text`");

        $this->addSql("UPDATE 
                                retention_completion_variable_name
                            SET
                                variable = 'retained_to_midyear_year_1',
                                sequence = 1
                            WHERE
                                name_text = 'Retained to Midyear Year 1';");

        $this->addSql("UPDATE 
                            retention_completion_variable_name
                        SET
                            variable = 'retained_to_start_of_year_2',
                            sequence = 2
                        WHERE
                            name_text = 'Retained to Start of Year 2';");

        $this->addSql("UPDATE 
                                retention_completion_variable_name
                            SET
                                variable = 'retained_to_midyear_year_2',
                                sequence = 3
                            WHERE
                                name_text = 'Retained to Midyear Year 2';");
        $this->addSql("UPDATE 
                                retention_completion_variable_name
                            SET
                                variable = 'retained_to_start_of_year_3',
                                sequence = 4
                            WHERE
                                name_text = 'Retained to Start of Year 3';");
        $this->addSql("UPDATE 
                                retention_completion_variable_name
                            SET
                                variable = 'retained_to_midyear_year_3',
                                sequence = 5
                            WHERE
                                name_text = 'Retained to Midyear Year 3';");
        $this->addSql("UPDATE 
                                retention_completion_variable_name
                            SET
                                variable = 'retained_to_start_of_year_4',
                                sequence = 6
                            WHERE
                                name_text = 'Retained to Start of Year 4';");
        $this->addSql("UPDATE 
                                retention_completion_variable_name
                            SET
                                variable = 'retained_to_midyear_year_4',
                                sequence = 7
                            WHERE
                                name_text = 'Retained to Midyear Year 4';");
        $this->addSql("UPDATE 
                                retention_completion_variable_name
                            SET
                                variable = 'completed_degree_in_1_year_or_less',
                                sequence = 8
                            WHERE
                                name_text = 'Completed Degree in 1 Year or Less';");
        $this->addSql("UPDATE 
                                retention_completion_variable_name
                            SET
                                variable = 'completed_degree_in_2_years_or_less',
                                sequence = 9
                            WHERE
                                name_text = 'Completed Degree in 2 Years or Less';");
        $this->addSql("UPDATE 
                                retention_completion_variable_name
                            SET
                                variable = 'completed_degree_in_3_years_or_less',
                                sequence = 10
                            WHERE
                                name_text = 'Completed Degree in 3 Years or Less';");
        $this->addSql("UPDATE 
                                retention_completion_variable_name
                            SET
                                variable = 'completed_degree_in_4_years_or_less',
                                sequence = 11
                            WHERE
                                name_text = 'Completed Degree in 4 Years or Less';");
        $this->addSql("UPDATE 
                                retention_completion_variable_name
                            SET
                                variable = 'completed_degree_in_5_years_or_less',
                                sequence = 12
                            WHERE
                                name_text = 'Completed Degree in 5 Years or Less';");
        $this->addSql("UPDATE 
                                retention_completion_variable_name
                            SET
                                variable = 'completed_degree_in_6_years_or_less',
                                sequence = 13
                            WHERE
                                name_text = 'Completed Degree in 6 Years or Less';");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
