<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141222071555 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE AlertNotifications (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, referrals_id INT DEFAULT NULL, appointments_id INT DEFAULT NULL, person_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, event VARCHAR(45) DEFAULT NULL, is_viewed TINYINT(1) DEFAULT NULL, INDEX IDX_16328CC532C8A3DE (organization_id), INDEX IDX_16328CC5B24851AE (referrals_id), INDEX IDX_16328CC523F542AE (appointments_id), INDEX IDX_16328CC5217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');        
        $this->addSql('ALTER TABLE AlertNotifications ADD CONSTRAINT FK_16328CC532C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE AlertNotifications ADD CONSTRAINT FK_16328CC5B24851AE FOREIGN KEY (referrals_id) REFERENCES referrals (id)');
        $this->addSql('ALTER TABLE AlertNotifications ADD CONSTRAINT FK_16328CC523F542AE FOREIGN KEY (appointments_id) REFERENCES Appointments (id)');
        $this->addSql('ALTER TABLE AlertNotifications ADD CONSTRAINT FK_16328CC5217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE AlertNotifications');
        
    }
}
