<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150818094826 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE email (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, person_id_student INT DEFAULT NULL, person_id_faculty INT DEFAULT NULL, activity_category_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, email_subject VARCHAR(50) DEFAULT NULL, email_body VARCHAR(500) DEFAULT NULL, email_bcc_list LONGTEXT DEFAULT NULL COMMENT \'BCC faculty list in comma separated format\', access_private TINYINT(1) DEFAULT NULL, access_public TINYINT(1) DEFAULT NULL, access_team TINYINT(1) DEFAULT NULL, INDEX IDX_E7927C74DE12AB56 (created_by), INDEX IDX_E7927C7425F94802 (modified_by), INDEX IDX_E7927C741F6FA0AF (deleted_by), INDEX fk_email_organization1 (organization_id), INDEX fk_email_person1 (person_id_student), INDEX fk_email_person2 (person_id_faculty), INDEX fk_email_activity_category1 (activity_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_teams (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, email_id INT DEFAULT NULL, teams_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_B8F63D7DE12AB56 (created_by), INDEX IDX_B8F63D725F94802 (modified_by), INDEX IDX_B8F63D71F6FA0AF (deleted_by), INDEX fk_email_teams_email1 (email_id), INDEX fk_email_teams_teams1 (teams_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C74DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C7425F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C741F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C7432C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C745F056556 FOREIGN KEY (person_id_student) REFERENCES person (id)');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C74FFB0AA26 FOREIGN KEY (person_id_faculty) REFERENCES person (id)');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C741CC8F7EE FOREIGN KEY (activity_category_id) REFERENCES activity_category (id)');
        $this->addSql('ALTER TABLE email_teams ADD CONSTRAINT FK_B8F63D7DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE email_teams ADD CONSTRAINT FK_B8F63D725F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE email_teams ADD CONSTRAINT FK_B8F63D71F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE email_teams ADD CONSTRAINT FK_B8F63D7A832C1C9 FOREIGN KEY (email_id) REFERENCES email (id)');
        $this->addSql('ALTER TABLE email_teams ADD CONSTRAINT FK_B8F63D7D6365F12 FOREIGN KEY (teams_id) REFERENCES Teams (id)');
             
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE email_teams DROP FOREIGN KEY FK_B8F63D7A832C1C9');       
        $this->addSql('DROP TABLE email');
        $this->addSql('DROP TABLE email_teams');
     }
}
