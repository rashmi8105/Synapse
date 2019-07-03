<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140925115027 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE organization_role (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, person_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_6E60B4F7D60322AC (role_id), INDEX IDX_6E60B4F7217BBB47 (person_id), INDEX IDX_6E60B4F732C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE organization_role ADD CONSTRAINT FK_6E60B4F7D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE organization_role ADD CONSTRAINT FK_6E60B4F7217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE organization_role ADD CONSTRAINT FK_6E60B4F732C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE organization_role');
        
    }
}
