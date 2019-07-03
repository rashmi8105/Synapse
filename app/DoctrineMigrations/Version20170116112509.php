<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script for creating tables org_person_student_retention,org_person_student_retention_tracking_group, retention_completion_variable_name
 */
class Version20170116112509 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_person_student_retention (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, person_id INT NOT NULL, org_academic_year_id INT NOT NULL, is_enrolled_beginning_year TINYINT(1) DEFAULT NULL, is_enrolled_midyear TINYINT(1) DEFAULT NULL, is_degree_completed TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL,created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, INDEX IDX_3D9CC22FDE12AB56 (created_by), INDEX IDX_3D9CC22F25F94802 (modified_by), INDEX IDX_3D9CC22F1F6FA0AF (deleted_by), INDEX IDX_3D9CC22F32C8A3DE (organization_id), INDEX IDX_3D9CC22F217BBB47 (person_id), INDEX IDX_3D9CC22FF3B0CE4A (org_academic_year_id), UNIQUE INDEX unique_opsr_idx (organization_id, person_id, org_academic_year_id, deleted_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_person_student_retention_tracking_group (id INT AUTO_INCREMENT NOT NULL,  organization_id INT NOT NULL, person_id INT NOT NULL, org_academic_year_id INT NOT NULL,  created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL,created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, INDEX IDX_A15D2C86DE12AB56 (created_by), INDEX IDX_A15D2C8625F94802 (modified_by), INDEX IDX_A15D2C861F6FA0AF (deleted_by), INDEX IDX_A15D2C8632C8A3DE (organization_id), INDEX IDX_A15D2C86217BBB47 (person_id), INDEX IDX_A15D2C86F3B0CE4A (org_academic_year_id), UNIQUE INDEX unique_osprtg_idx (organization_id, person_id, org_academic_year_id, deleted_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE retention_completion_variable_name (id INT AUTO_INCREMENT NOT NULL,  years_from_retention_track INT DEFAULT NULL, type VARCHAR(25) DEFAULT NULL, name_text VARCHAR(100) NOT NULL,  created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL,created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, INDEX IDX_A9BE9FFDDE12AB56 (created_by), INDEX IDX_A9BE9FFD25F94802 (modified_by), INDEX IDX_A9BE9FFD1F6FA0AF (deleted_by), INDEX years_from_retention_track_idx (years_from_retention_track), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_person_student_retention ADD CONSTRAINT FK_3D9CC22FDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student_retention ADD CONSTRAINT FK_3D9CC22F25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student_retention ADD CONSTRAINT FK_3D9CC22F1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student_retention ADD CONSTRAINT FK_3D9CC22F32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_person_student_retention ADD CONSTRAINT FK_3D9CC22F217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student_retention ADD CONSTRAINT FK_3D9CC22FF3B0CE4A FOREIGN KEY (org_academic_year_id) REFERENCES org_academic_year (id)');
        $this->addSql('ALTER TABLE org_person_student_retention_tracking_group ADD CONSTRAINT FK_A15D2C86DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student_retention_tracking_group ADD CONSTRAINT FK_A15D2C8625F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student_retention_tracking_group ADD CONSTRAINT FK_A15D2C861F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student_retention_tracking_group ADD CONSTRAINT FK_A15D2C8632C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_person_student_retention_tracking_group ADD CONSTRAINT FK_A15D2C86217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student_retention_tracking_group ADD CONSTRAINT FK_A15D2C86F3B0CE4A FOREIGN KEY (org_academic_year_id) REFERENCES org_academic_year (id)');
        $this->addSql('ALTER TABLE retention_completion_variable_name ADD CONSTRAINT FK_A9BE9FFDDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE retention_completion_variable_name ADD CONSTRAINT FK_A9BE9FFD25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE retention_completion_variable_name ADD CONSTRAINT FK_A9BE9FFD1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_person_student_retention');
        $this->addSql('DROP TABLE org_person_student_retention_tracking_group');
        $this->addSql('DROP TABLE retention_completion_variable_name');
    }
}
