<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150820070312 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE issue_calculated_students (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, person_student_id INT DEFAULT NULL, person_staff_id INT DEFAULT NULL, issue_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_C74DDDFADE12AB56 (created_by), INDEX IDX_C74DDDFA25F94802 (modified_by), INDEX IDX_C74DDDFA1F6FA0AF (deleted_by), INDEX fk_issue_calculated_students_issue1_idx (issue_id), INDEX fk_issue_calculated_students_organization1_idx (organization_id), INDEX fk_issue_calculated_students_person_student1_idx (person_student_id), INDEX fk_issue_calculated_students_person_staff1_idx (person_staff_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
//      This is now a view
//      $this->addSql('CREATE TABLE org_calculated_risk_variables (org_id INT NOT NULL, person_id INT NOT NULL, risk_variable_id INT NOT NULL, risk_model_id INT NOT NULL, calc_bucket_value INT DEFAULT NULL, calc_weight NUMERIC(8, 4) DEFAULT NULL, risk_source_value NUMERIC(12, 4) DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_93D7B9DDF4837C1B (org_id), INDEX fk_org_computed_risk_variables_person1_idx (person_id), INDEX fk_org_computed_risk_variables_risk_variable1_idx (risk_variable_id), INDEX fk_org_calculated_risk_variables_risk_model_master1_idx (risk_model_id), PRIMARY KEY(org_id, person_id, risk_variable_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE issue_calculated_students ADD CONSTRAINT FK_C74DDDFADE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE issue_calculated_students ADD CONSTRAINT FK_C74DDDFA25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE issue_calculated_students ADD CONSTRAINT FK_C74DDDFA1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE issue_calculated_students ADD CONSTRAINT FK_C74DDDFA32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE issue_calculated_students ADD CONSTRAINT FK_C74DDDFAFD2F2C0F FOREIGN KEY (person_student_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE issue_calculated_students ADD CONSTRAINT FK_C74DDDFA990BF2FA FOREIGN KEY (person_staff_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE issue_calculated_students ADD CONSTRAINT FK_C74DDDFA5E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE issue_calculated_students');
    }
}
