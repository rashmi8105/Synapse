<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150514134921 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE org_person_student ADD person_id_primary_connect INT DEFAULT NULL');
        $this->addSql('ALTER TABLE org_person_student ADD CONSTRAINT FK_9C88CAE18661B904 FOREIGN KEY (person_id_primary_connect) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_9C88CAE18661B904 ON org_person_student (person_id_primary_connect)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_person_student DROP FOREIGN KEY FK_9C88CAE18661B904');
        $this->addSql('DROP INDEX IDX_9C88CAE18661B904 ON org_person_student');
        $this->addSql('ALTER TABLE org_person_student DROP person_id_primary_connect');
    }
}
