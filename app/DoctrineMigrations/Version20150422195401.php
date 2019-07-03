<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150422195401 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_change_request (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id_source INT DEFAULT NULL, org_id_destination INT DEFAULT NULL, person_id_requested_by INT DEFAULT NULL, person_id_student INT DEFAULT NULL, person_id_approved_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, date_submitted DATETIME DEFAULT NULL, date_approved DATETIME DEFAULT NULL, approval_status enum(\'yes\', \'no\'), INDEX IDX_C2A5FB6DDE12AB56 (created_by), INDEX IDX_C2A5FB6D25F94802 (modified_by), INDEX IDX_C2A5FB6D1F6FA0AF (deleted_by), INDEX fk_org_change_request_person1_idx (person_id_requested_by), INDEX fk_org_change_request_person2_idx (person_id_student), INDEX fk_org_change_request_organization1_idx (org_id_source), INDEX fk_org_change_request_organization2_idx (org_id_destination), INDEX fk_org_change_request_person3_idx (person_id_approved_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_users (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, person_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_EACE568BDE12AB56 (created_by), INDEX IDX_EACE568B25F94802 (modified_by), INDEX IDX_EACE568B1F6FA0AF (deleted_by), INDEX fk_organization_tier_users_organization1_idx (organization_id), INDEX fk_organization_tier_users_person1_idx (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_change_request ADD CONSTRAINT FK_C2A5FB6DDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_change_request ADD CONSTRAINT FK_C2A5FB6D25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_change_request ADD CONSTRAINT FK_C2A5FB6D1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_change_request ADD CONSTRAINT FK_C2A5FB6D40C2D6CC FOREIGN KEY (org_id_source) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_change_request ADD CONSTRAINT FK_C2A5FB6D2FE12506 FOREIGN KEY (org_id_destination) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_change_request ADD CONSTRAINT FK_C2A5FB6DFA81244C FOREIGN KEY (person_id_requested_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_change_request ADD CONSTRAINT FK_C2A5FB6D5F056556 FOREIGN KEY (person_id_student) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_change_request ADD CONSTRAINT FK_C2A5FB6DB36C36F4 FOREIGN KEY (person_id_approved_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_users ADD CONSTRAINT FK_EACE568BDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_users ADD CONSTRAINT FK_EACE568B25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_users ADD CONSTRAINT FK_EACE568B1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_users ADD CONSTRAINT FK_EACE568B32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_users ADD CONSTRAINT FK_EACE568B217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_change_request');
        $this->addSql('DROP TABLE org_users');        
    }
}
