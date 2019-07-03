<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161017062731 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $this->addSql('CREATE TABLE org_corporate_google_access (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, person_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, status VARCHAR(1) DEFAULT NULL, oauth_one_time_token VARCHAR(100) DEFAULT NULL, oauth_cal_access_token VARCHAR(100) DEFAULT NULL, oauth_cal_refresh_token VARCHAR(100) DEFAULT NULL, INDEX IDX_F16D73BFDE12AB56 (created_by), INDEX IDX_F16D73BF25F94802 (modified_by), INDEX IDX_F16D73BF1F6FA0AF (deleted_by), INDEX fk_org_corporate_google_access_organization1_idx (organization_id), INDEX fk_org_person_faculty_person1 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_corporate_google_access ADD CONSTRAINT FK_F16D73BFDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_corporate_google_access ADD CONSTRAINT FK_F16D73BF25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_corporate_google_access ADD CONSTRAINT FK_F16D73BF1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_corporate_google_access ADD CONSTRAINT FK_F16D73BF32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_corporate_google_access ADD CONSTRAINT FK_F16D73BF217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_corporate_google_access');
    }
}
