<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150821105411 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE related_activities ADD appointment_id INT DEFAULT NULL, ADD referral_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE related_activities ADD CONSTRAINT FK_3F3CA755E5B533F9 FOREIGN KEY (appointment_id) REFERENCES Appointments (id)');
        $this->addSql('ALTER TABLE related_activities ADD CONSTRAINT FK_3F3CA7553CCAA4B7 FOREIGN KEY (referral_id) REFERENCES referrals (id)');
        $this->addSql('CREATE INDEX IDX_3F3CA755E5B533F9 ON related_activities (appointment_id)');
        $this->addSql('CREATE INDEX IDX_3F3CA7553CCAA4B7 ON related_activities (referral_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE related_activities DROP FOREIGN KEY FK_3F3CA755E5B533F9');
        $this->addSql('ALTER TABLE related_activities DROP FOREIGN KEY FK_3F3CA7553CCAA4B7');
        $this->addSql('ALTER TABLE related_activities DROP appointment_id, DROP referral_id');
    }
}
