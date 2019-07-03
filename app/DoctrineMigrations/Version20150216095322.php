<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150216095322 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
		// Renaming creating issue, commenting out this and we may need to revisit this.
        //$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        //$this->addSql('RENAME TABLE AccessToken TO access_token');
        //$this->addSql('RENAME TABLE Appointments TO appointments');
        //$this->addSql('RENAME TABLE AuthCode TO auth_code');
        //$this->addSql('RENAME TABLE Client TO client');
        //$this->addSql('RENAME TABLE RefreshToken TO refresh_token');
        //$this->addSql('RENAME TABLE Teams TO team');


    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        //$this->addSql('CREATE TABLE org_search_shared (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_search_id_source INT DEFAULT NULL, person_id_sharedby INT DEFAULT NULL, person_id_sharedwith INT DEFAULT NULL, org_search_id_dest INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_49F4EBA8DE12AB56 (created_by), INDEX IDX_49F4EBA825F94802 (modified_by), INDEX IDX_49F4EBA81F6FA0AF (deleted_by), INDEX IDX_49F4EBA8F459207D (org_search_id_source), INDEX IDX_49F4EBA8BF1A33A5 (person_id_sharedby), INDEX IDX_49F4EBA85EA2D51A (person_id_sharedwith), INDEX IDX_49F4EBA887A20C50 (org_search_id_dest), UNIQUE INDEX fk_org_search_shared_unique (org_search_id_source, person_id_sharedby, person_id_sharedwith, org_search_id_dest), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        //$this->addSql('RENAME TABLE access_token TO AccessToken');
        //$this->addSql('RENAME TABLE appointments TO Appointments');
        //$this->addSql('RENAME TABLE auth_code TO AuthCode');
        //$this->addSql('RENAME TABLE client TO Client');
        //$this->addSql('RENAME TABLE refresh_token TO RefreshToken');
        //$this->addSql('RENAME TABLE team TO Teams');
    }
}
