<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150814130808 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_top5_issues_calculated_values (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, person_id INT DEFAULT NULL, survey_id INT DEFAULT NULL, issue_id INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, survey_cohort_id INT NOT NULL, calculated_value_numerator NUMERIC(8, 4) DEFAULT NULL, calculated_value_denominator NUMERIC(8, 4) DEFAULT NULL, number_of_students INT NOT NULL, INDEX IDX_8326F944DE12AB56 (created_by), INDEX IDX_8326F94425F94802 (modified_by), INDEX IDX_8326F9441F6FA0AF (deleted_by), INDEX fk_org_top5_issues_calculated_values_organization1_idx (organization_id), INDEX fk_org_top5_issues_calculated_values_person1_idx (person_id), INDEX fk_org_top5_issues_calculated_values_survey1_idx (survey_id), INDEX fk_org_top5_issues_calculated_values_issue1_idx (issue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');       
        $this->addSql('ALTER TABLE org_top5_issues_calculated_values ADD CONSTRAINT FK_8326F944DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_top5_issues_calculated_values ADD CONSTRAINT FK_8326F94425F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_top5_issues_calculated_values ADD CONSTRAINT FK_8326F9441F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_top5_issues_calculated_values ADD CONSTRAINT FK_8326F94432C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_top5_issues_calculated_values ADD CONSTRAINT FK_8326F944217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_top5_issues_calculated_values ADD CONSTRAINT FK_8326F944B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE org_top5_issues_calculated_values ADD CONSTRAINT FK_8326F9445E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id)');
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE org_top5_issues_calculated_values');
        
    }
}
