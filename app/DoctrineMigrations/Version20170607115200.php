<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-14791 - Migration script for updating retention_completion_variable_name table's name_text
 */
class Version20170607115200 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("UPDATE
                                retention_completion_variable_name 
                            SET
                                name_text = 'Completed Degree in 1 Year or Less'
                            WHERE 
                                name_text = 'Completed Degree in 1 Year';");

        $this->addSql("UPDATE
                                retention_completion_variable_name 
                            SET
                                name_text = 'Completed Degree in 2 Years or Less'
                            WHERE 
                                name_text = 'Completed Degree in 2 Years';");

        $this->addSql("UPDATE
                                retention_completion_variable_name 
                            SET
                                name_text = 'Completed Degree in 3 Years or Less'
                            WHERE 
                                name_text = 'Completed Degree in 3 Years';");

        $this->addSql("UPDATE
                                retention_completion_variable_name 
                            SET
                                name_text = 'Completed Degree in 4 Years or Less'
                            WHERE 
                                name_text = 'Completed Degree in 4 Years';");

        $this->addSql("UPDATE
                                retention_completion_variable_name 
                            SET
                                name_text = 'Completed Degree in 5 Years or Less'
                            WHERE 
                                name_text = 'Completed Degree in 5 Years';");

        $this->addSql("UPDATE
                                retention_completion_variable_name 
                            SET
                                name_text = 'Completed Degree in 6 Years or Less'
                            WHERE 
                            name_text = 'Completed Degree in 6 Years';");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
