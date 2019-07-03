<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150511160405 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_conflict (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, src_org_id INT DEFAULT NULL, dst_org_id INT DEFAULT NULL, faculty_id INT DEFAULT NULL, student_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, record_type enum(\'master\', \'home\',\'other\'), status enum(\'conflict\', \'merged\'), INDEX IDX_31D50D4CDE12AB56 (created_by), INDEX IDX_31D50D4C25F94802 (modified_by), INDEX IDX_31D50D4C1F6FA0AF (deleted_by), INDEX fk_table1_org_person_faculty1_idx (faculty_id), INDEX fk_table1_org_person_student1_idx (student_id), INDEX fk_table1_organization1_idx (src_org_id), INDEX fk_table1_organization2_idx (dst_org_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_conflict ADD CONSTRAINT FK_31D50D4CDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_conflict ADD CONSTRAINT FK_31D50D4C25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_conflict ADD CONSTRAINT FK_31D50D4C1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_conflict ADD CONSTRAINT FK_31D50D4CEE195E00 FOREIGN KEY (src_org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_conflict ADD CONSTRAINT FK_31D50D4C4A6EE8E0 FOREIGN KEY (dst_org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_conflict ADD CONSTRAINT FK_31D50D4C680CAB68 FOREIGN KEY (faculty_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_conflict ADD CONSTRAINT FK_31D50D4CCB944F1A FOREIGN KEY (student_id) REFERENCES person (id)');        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_conflict');        
    }
}
