<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150806175812 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE person_factor_calculated (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, person_id INT DEFAULT NULL, factor_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, mean_value NUMERIC(13, 4) DEFAULT NULL, INDEX IDX_E03AD201DE12AB56 (created_by), INDEX IDX_E03AD20125F94802 (modified_by), INDEX IDX_E03AD2011F6FA0AF (deleted_by), INDEX fk_person_factor_calculated_person1_idx (person_id), INDEX fk_person_factor_calculated_organization_idx (organization_id), INDEX fk_person_factor_calculated_factor1_idx (factor_id), UNIQUE INDEX org_person_factor_uniq_idx (organization_id, person_id, factor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE success_marker_calculated (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, person_id INT DEFAULT NULL, surveymarker_questions_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, color VARCHAR(15) DEFAULT NULL, INDEX IDX_9FD161E5DE12AB56 (created_by), INDEX IDX_9FD161E525F94802 (modified_by), INDEX IDX_9FD161E51F6FA0AF (deleted_by), INDEX fk_success_marker_calculated_person1_idx (person_id), INDEX fk_success_marker_calculated_organization1_idx (organization_id), INDEX fk_success_marker_calculated_surveymarker_questions1_idx (surveymarker_questions_id), UNIQUE INDEX org_person_marker_uniq_idx (organization_id, person_id, surveymarker_questions_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE person_factor_calculated ADD CONSTRAINT FK_E03AD201DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_factor_calculated ADD CONSTRAINT FK_E03AD20125F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_factor_calculated ADD CONSTRAINT FK_E03AD2011F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_factor_calculated ADD CONSTRAINT FK_E03AD20132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE person_factor_calculated ADD CONSTRAINT FK_E03AD201217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_factor_calculated ADD CONSTRAINT FK_E03AD201BC88C1A3 FOREIGN KEY (factor_id) REFERENCES factor (id)');
        $this->addSql('ALTER TABLE success_marker_calculated ADD CONSTRAINT FK_9FD161E5DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE success_marker_calculated ADD CONSTRAINT FK_9FD161E525F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE success_marker_calculated ADD CONSTRAINT FK_9FD161E51F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE success_marker_calculated ADD CONSTRAINT FK_9FD161E532C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE success_marker_calculated ADD CONSTRAINT FK_9FD161E5217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE success_marker_calculated ADD CONSTRAINT FK_9FD161E57F16624C FOREIGN KEY (surveymarker_questions_id) REFERENCES surveymarker_questions (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE person_factor_calculated');
        $this->addSql('DROP TABLE success_marker_calculated');
        
    }
}
