<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141118101230 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE calendar_sharing (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, person_id_sharedby INT DEFAULT NULL, person_id_sharedto INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, shared_on DATETIME DEFAULT NULL, INDEX IDX_CCF697AF32C8A3DE (organization_id), INDEX IDX_CCF697AFBF1A33A5 (person_id_sharedby), INDEX IDX_CCF697AF57563323 (person_id_sharedto), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calendar_sharing ADD CONSTRAINT FK_CCF697AF32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE calendar_sharing ADD CONSTRAINT FK_CCF697AFBF1A33A5 FOREIGN KEY (person_id_sharedby) REFERENCES person (id)');
        $this->addSql('ALTER TABLE calendar_sharing ADD CONSTRAINT FK_CCF697AF57563323 FOREIGN KEY (person_id_sharedto) REFERENCES person (id)');
     
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE calendar_sharing');
     
    }
}
