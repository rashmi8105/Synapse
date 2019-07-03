<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150828120706 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE related_activities ADD email_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE related_activities ADD CONSTRAINT FK_3F3CA755A832C1C9 FOREIGN KEY (email_id) REFERENCES email (id)');
        $this->addSql('CREATE INDEX IDX_3F3CA755A832C1C9 ON related_activities (email_id)');
       
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE related_activities DROP email_id');
    }
}
