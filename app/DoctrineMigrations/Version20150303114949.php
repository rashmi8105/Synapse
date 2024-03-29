<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150303114949 extends AbstractMigration {
	public function up(Schema $schema) {
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf ( $this->connection->getDatabasePlatform ()->getName () != 'mysql', 'Migration can only be executed safely on \'mysql\'.' );
		
		$this->addSql ( 'ALTER TABLE org_academic_terms CHANGE term_code term_code VARCHAR(10) DEFAULT NULL' );
	}
	public function down(Schema $schema) {
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf ( $this->connection->getDatabasePlatform ()->getName () != 'mysql', 'Migration can only be executed safely on \'mysql\'.' );
		
		$this->addSql ( 'ALTER TABLE org_academic_terms CHANGE term_code term_code INT DEFAULT NULL');
    }
}
