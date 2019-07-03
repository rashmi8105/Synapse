<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script for creating org_person_student_cohort
 */
class Version20160316130041 extends AbstractMigration {
	/**
	 *
	 * @param Schema $schema        	
	 */
	public function up(Schema $schema) {
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf ( $this->connection->getDatabasePlatform ()->getName () != 'mysql', 'Migration can only be executed safely on \'mysql\'.' );
		
		$this->addSql ( 'CREATE TABLE org_person_student_cohort 
					      (
							id INT AUTO_INCREMENT NOT NULL,
							organization_id INT DEFAULT NULL,
							person_id INT DEFAULT NULL,
							org_academic_year_id INT DEFAULT NULL,
							cohort INT DEFAULT NULL,
							created_by INT DEFAULT NULL,
							created_at DATETIME DEFAULT NULL,
							modified_by INT DEFAULT NULL,
							modified_at DATETIME DEFAULT NULL,
							deleted_by INT DEFAULT NULL,
							deleted_at DATETIME DEFAULT NULL,
							INDEX fk_org_person_student_cohort_organization1 (organization_id),
							INDEX fk_org_person_student_cohort_person1 (person_id),
							INDEX fk_org_person_student_cohort_org_academic_year_id1 (org_academic_year_id),						
							INDEX org_person_student_cohort_covering_index (organization_id, org_academic_year_id, person_id, deleted_at),
							UNIQUE INDEX cohort_unique_index (organization_id, person_id, org_academic_year_id),
							PRIMARY KEY(id)
					      ) 
					    DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB' );
		
		$this->addSql ( 'ALTER TABLE org_person_student_cohort ADD CONSTRAINT FK_492F13D6DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)' );
		$this->addSql ( 'ALTER TABLE org_person_student_cohort ADD CONSTRAINT FK_492F13D625F94802 FOREIGN KEY (modified_by) REFERENCES person (id)' );
		$this->addSql ( 'ALTER TABLE org_person_student_cohort ADD CONSTRAINT FK_492F13D61F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)' );
		$this->addSql ( 'ALTER TABLE org_person_student_cohort ADD CONSTRAINT FK_492F13D632C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)' );
		$this->addSql ( 'ALTER TABLE org_person_student_cohort ADD CONSTRAINT FK_492F13D6217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)' );
		$this->addSql ( 'ALTER TABLE org_person_student_cohort ADD CONSTRAINT FK_492F13D6F3B0CE4A FOREIGN KEY (org_academic_year_id) REFERENCES org_academic_year (id)' );
	}
	
	/**
	 *
	 * @param Schema $schema        	
	 */
	public function down(Schema $schema) {
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf ( $this->connection->getDatabasePlatform ()->getName () != 'mysql', 'Migration can only be executed safely on \'mysql\'.' );
		
		$this->addSql ( 'DROP TABLE org_person_student_cohort' );
	}
}
