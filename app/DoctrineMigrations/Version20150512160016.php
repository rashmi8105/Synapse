<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150512160016 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_trail ADD person_id INT DEFAULT NULL, ADD audited_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE audit_trail ADD CONSTRAINT FK_B523E178217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_B523E178217BBB47 ON audit_trail (person_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_trail DROP FOREIGN KEY FK_B523E178217BBB47');
        $this->addSql('DROP INDEX IDX_B523E178217BBB47 ON audit_trail');
        $this->addSql('ALTER TABLE audit_trail DROP person_id, DROP audited_at');
    }
}
