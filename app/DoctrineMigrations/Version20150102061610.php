<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150102061610 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE org_talking_points (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, person_id INT DEFAULT NULL, talking_points_id INT DEFAULT NULL, survey_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, response VARCHAR(45) DEFAULT NULL, INDEX IDX_861DCA16DE12AB56 (created_by), INDEX IDX_861DCA1625F94802 (modified_by), INDEX IDX_861DCA161F6FA0AF (deleted_by), INDEX IDX_861DCA1632C8A3DE (organization_id), INDEX IDX_861DCA16217BBB47 (person_id), INDEX IDX_861DCA16CDC12E8B (talking_points_id), INDEX IDX_861DCA16B3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_talking_points ADD CONSTRAINT FK_861DCA16DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_talking_points ADD CONSTRAINT FK_861DCA1625F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_talking_points ADD CONSTRAINT FK_861DCA161F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_talking_points ADD CONSTRAINT FK_861DCA1632C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_talking_points ADD CONSTRAINT FK_861DCA16217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_talking_points ADD CONSTRAINT FK_861DCA16CDC12E8B FOREIGN KEY (talking_points_id) REFERENCES talking_points (id)');
        $this->addSql('ALTER TABLE org_talking_points ADD CONSTRAINT FK_861DCA16B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('DROP TABLE OrgTalkingPoints');
        $this->addSql('ALTER TABLE talking_points CHANGE min_range min_range INT DEFAULT NULL, CHANGE max_range max_range INT DEFAULT NULL');
        $this->addSql('ALTER TABLE talking_points_lang ADD title VARCHAR(400) DEFAULT NULL, ADD description VARCHAR(5000) DEFAULT NULL');
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE OrgTalkingPoints (id INT AUTO_INCREMENT NOT NULL, deleted_by INT DEFAULT NULL, person_id INT DEFAULT NULL, modified_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, survey_id INT DEFAULT NULL, talking_points_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, response VARCHAR(45) DEFAULT NULL, INDEX IDX_E88CCF18DE12AB56 (created_by), INDEX IDX_E88CCF1825F94802 (modified_by), INDEX IDX_E88CCF181F6FA0AF (deleted_by), INDEX IDX_E88CCF1832C8A3DE (organization_id), INDEX IDX_E88CCF18217BBB47 (person_id), INDEX IDX_E88CCF18CDC12E8B (talking_points_id), INDEX IDX_E88CCF18B3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE OrgTalkingPoints ADD CONSTRAINT FK_E88CCF181F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE OrgTalkingPoints ADD CONSTRAINT FK_E88CCF18217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE OrgTalkingPoints ADD CONSTRAINT FK_E88CCF1825F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE OrgTalkingPoints ADD CONSTRAINT FK_E88CCF1832C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE OrgTalkingPoints ADD CONSTRAINT FK_E88CCF18B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE OrgTalkingPoints ADD CONSTRAINT FK_E88CCF18CDC12E8B FOREIGN KEY (talking_points_id) REFERENCES talking_points (id)');
        $this->addSql('ALTER TABLE OrgTalkingPoints ADD CONSTRAINT FK_E88CCF18DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('DROP TABLE org_talking_points');
        $this->addSql('ALTER TABLE talking_points CHANGE min_range min_range NUMERIC(15, 4) DEFAULT NULL, CHANGE max_range max_range NUMERIC(15, 4) DEFAULT NULL');
        $this->addSql('ALTER TABLE talking_points_lang DROP title, DROP description');
       
    }
}
