<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script for Org_Person_Student_Survey entity creation
 */
class Version20160329065947 extends AbstractMigration {
	/**
	 *
	 * @param Schema $schema        	
	 */
	public function up(Schema $schema) {
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf ( $this->connection->getDatabasePlatform ()->getName () != 'mysql', 'Migration can only be executed safely on \'mysql\'.' );
		
		$this->addSql ( 'CREATE TABLE org_person_student_survey
		    					(
		    						id INT AUTO_INCREMENT NOT NULL,
		    						organization_id INT DEFAULT NULL,
		    						person_id INT DEFAULT NULL,
		    						survey_id INT DEFAULT NULL,
		    						receive_survey INT DEFAULT 0 NOT NULL,
		    						created_by INT DEFAULT NULL,
		    						modified_by INT DEFAULT NULL,
		    					    deleted_by INT DEFAULT NULL,
		    						created_at DATETIME DEFAULT NULL,
		    						modified_at DATETIME DEFAULT NULL,
		    						deleted_at DATETIME DEFAULT NULL,
		    						INDEX fk_org_person_student_survey_survey1_idx (survey_id),
		    						INDEX fk_org_person_student_survey_organization1_idx (organization_id),
		    						INDEX fk_org_person_student_survey_person1_idx (person_id),
		    						INDEX org_person_student_survey_covering_index (organization_id, survey_id, person_id, deleted_at),
		    						UNIQUE INDEX survey_unique_index (organization_id, person_id, survey_id),
		    						PRIMARY KEY(id)
		                        )
		                 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB' );
		
		$this->addSql ( 'ALTER TABLE org_person_student_survey ADD CONSTRAINT FK_37C84941DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)' );
		$this->addSql ( 'ALTER TABLE org_person_student_survey ADD CONSTRAINT FK_37C8494125F94802 FOREIGN KEY (modified_by) REFERENCES person (id)' );
		$this->addSql ( 'ALTER TABLE org_person_student_survey ADD CONSTRAINT FK_37C849411F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)' );
		$this->addSql ( 'ALTER TABLE org_person_student_survey ADD CONSTRAINT FK_37C8494132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)' );
		$this->addSql ( 'ALTER TABLE org_person_student_survey ADD CONSTRAINT FK_37C84941217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)' );
		$this->addSql ( 'ALTER TABLE org_person_student_survey ADD CONSTRAINT FK_37C84941B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)' );
	}
	
	/**
	 *
	 * @param Schema $schema        	
	 */
	public function down(Schema $schema) {
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf ( $this->connection->getDatabasePlatform ()->getName () != 'mysql', 'Migration can only be executed safely on \'mysql\'.' );
		$this->addSql ( 'DROP TABLE org_person_student_survey');
     }
}
