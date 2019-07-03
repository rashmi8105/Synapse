<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141231130951 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE student_db_view_log (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, person_id_faculty INT DEFAULT NULL, person_id_student INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, last_viewed_on DATETIME DEFAULT NULL, INDEX IDX_A0599E8B32C8A3DE (organization_id), INDEX IDX_A0599E8BFFB0AA26 (person_id_faculty), INDEX IDX_A0599E8B5F056556 (person_id_student), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE student_db_view_log ADD CONSTRAINT FK_A0599E8B32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE student_db_view_log ADD CONSTRAINT FK_A0599E8BFFB0AA26 FOREIGN KEY (person_id_faculty) REFERENCES person (id)');
        $this->addSql('ALTER TABLE student_db_view_log ADD CONSTRAINT FK_A0599E8B5F056556 FOREIGN KEY (person_id_student) REFERENCES person (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE student_db_view_log');
    }
}
