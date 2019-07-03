<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151102150958 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_permissionset_question ADD survey_id INT DEFAULT NULL, ADD cohort_code INT DEFAULT NULL');
        $this->addSql('ALTER TABLE org_permissionset_question ADD CONSTRAINT FK_5AC5069EB3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('CREATE INDEX IDX_5AC5069EB3FE509D ON org_permissionset_question (survey_id)');
        $this->addSql('ALTER TABLE survey_questions ADD cohort_code INT DEFAULT NULL');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
      
    }
}
