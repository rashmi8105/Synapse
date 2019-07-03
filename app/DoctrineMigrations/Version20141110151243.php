<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141110151243 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE referrals ADD person_id_assigned_to INT DEFAULT NULL');
        $this->addSql('ALTER TABLE referrals ADD CONSTRAINT FK_1B7DC89674215258 FOREIGN KEY (person_id_assigned_to) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_1B7DC89674215258 ON referrals (person_id_assigned_to)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE referrals DROP FOREIGN KEY FK_1B7DC89674215258');
        $this->addSql('DROP INDEX IDX_1B7DC89674215258 ON referrals');
        $this->addSql('ALTER TABLE referrals DROP person_id_assigned_to');
    }
}
