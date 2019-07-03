<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150520133919 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE person_risk_level_history (person_id INT NOT NULL, date_captured DATETIME NOT NULL, risk_level INT DEFAULT NULL, risk_model_id INT DEFAULT NULL, risk_score NUMERIC(6, 4) DEFAULT NULL, weighted_value NUMERIC(9, 4) DEFAULT NULL, maximum_weight_value NUMERIC(9, 4) DEFAULT NULL, INDEX fk_person_risk_level_history_person1_idx (person_id), INDEX fk_person_risk_level_history_risk_model_master1_idx (risk_model_id), INDEX fk_person_risk_level_history_risk_level1_idx (risk_level), PRIMARY KEY(person_id, date_captured)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE person_risk_level_history ADD CONSTRAINT FK_FB00ACB9217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_risk_level_history ADD CONSTRAINT FK_FB00ACB9EB88056C FOREIGN KEY (risk_level) REFERENCES risk_level (id)');
        $this->addSql('ALTER TABLE person_risk_level_history ADD CONSTRAINT FK_FB00ACB99F5CF488 FOREIGN KEY (risk_model_id) REFERENCES risk_model_master (id)');
        $this->addSql('ALTER TABLE risk_group_person_history CHANGE COLUMN assignment_date assignment_date DATETIME NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (person_id, risk_group_id, assignment_date)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE person_risk_level_history');
        $this->addSql('ALTER TABLE risk_group_person_history CHANGE COLUMN assignment_date assignment_date DATETIME NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (person_id, risk_group_id)');
    }
}
