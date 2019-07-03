<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150820213851 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        

        $this->addSQL('DROP TABLE IF EXISTS synapse.org_calc_flags_risk;');

        $this->addSQL('CREATE TABLE org_calc_flags_risk(
        	id INT(11) PRIMARY KEY AUTO_INCREMENT, 
        	created_by INT(11),
        	modified_by INT(11),
        	deleted_by INT(11),
        	org_id INT(11),
        	person_id INT(11),
        	created_at DATETIME ,
        	modified_at DATETIME,
        	deleted_at DATETIME, 
        	calculated_at DATETIME);');

        $this->addSQL('DROP TABLE IF EXISTS synapse.org_calc_flags_factor;');

        $this->addSQL('CREATE TABLE org_calc_flags_factor(
        	id INT(11) PRIMARY KEY AUTO_INCREMENT, 
        	created_by INT(11),
        	modified_by INT(11),
        	deleted_by INT(11),
        	org_id INT(11),
        	person_id INT(11),
        	created_at DATETIME,
        	modified_at DATETIME,
        	deleted_at DATETIME, 
        	calculated_at DATETIME);');

        $this->addSQL('DROP TABLE IF EXISTS synapse.org_calc_flags_success_marker;');

        $this->addSQL('CREATE TABLE org_calc_flags_success_marker(
        	id INT(11) PRIMARY KEY AUTO_INCREMENT, 
        	created_by INT(11),
        	modified_by INT(11),
        	deleted_by INT(11),
        	org_id INT(11),
        	person_id INT(11),
        	created_at DATETIME,
        	modified_at DATETIME,
        	deleted_at DATETIME, 
        	calculated_at DATETIME);');

        $this->addSQL('DROP TABLE IF EXISTS synapse.org_calc_flags_talking_point;');


        $this->addSQL('CREATE TABLE org_calc_flags_talking_point(
        	id INT(11) PRIMARY KEY AUTO_INCREMENT, 
        	created_by INT(11),
        	modified_by INT(11),
        	deleted_by INT(11),
        	org_id INT(11),
        	person_id INT(11),
        	created_at DATETIME,
        	modified_at DATETIME,
        	deleted_at DATETIME, 
        	calculated_at DATETIME);');

        $this->addSQL('ALTER TABLE org_calc_flags_talking_point
			ADD INDEX `id_idx` (id ASC),
			ADD INDEX `org_person_idx` (org_id ASC, person_id ASC),
			ADD INDEX `person_idx` (person_id ASC),
			ADD INDEX `created_at_idx` (created_at ASC),
			ADD INDEX `modified_at_idx` (modified_at ASC),
			ADD INDEX `calculated_at_idx` (calculated_at ASC);');

         $this->addSQL('ALTER TABLE org_calc_flags_risk
			ADD INDEX `id_idx` (id ASC),
			ADD INDEX `org_person_idx` (org_id ASC, person_id ASC),
			ADD INDEX `person_idx` (person_id ASC),
			ADD INDEX `created_at_idx` (created_at ASC),
			ADD INDEX `modified_at_idx` (modified_at ASC),
			ADD INDEX `calculated_at_idx` (calculated_at ASC);');


          $this->addSQL('ALTER TABLE org_calc_flags_factor
			ADD INDEX `id_idx` (id ASC),
			ADD INDEX `org_person_idx` (org_id ASC, person_id ASC),
			ADD INDEX `person_idx` (person_id ASC),
			ADD INDEX `created_at_idx` (created_at ASC),
			ADD INDEX `modified_at_idx` (modified_at ASC),
			ADD INDEX `calculated_at_idx` (calculated_at ASC);');


           $this->addSQL('ALTER TABLE org_calc_flags_success_marker
			ADD INDEX `id_idx` (id ASC),
			ADD INDEX `org_person_idx` (org_id ASC, person_id ASC),
			ADD INDEX `person_idx` (person_id ASC),
			ADD INDEX `created_at_idx` (created_at ASC),
			ADD INDEX `modified_at_idx` (modified_at ASC),
			ADD INDEX `calculated_at_idx` (calculated_at ASC);');




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
