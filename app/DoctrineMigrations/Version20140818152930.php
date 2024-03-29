<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140818152930 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE AccessToken (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_B39617F55F37A13B (token), INDEX IDX_B39617F519EB6921 (client_id), INDEX IDX_B39617F5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE AuthCode (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, redirect_uri LONGTEXT NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_F1D7D1775F37A13B (token), INDEX IDX_F1D7D17719EB6921 (client_id), INDEX IDX_F1D7D177A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Client (id INT AUTO_INCREMENT NOT NULL, random_id VARCHAR(255) NOT NULL, redirect_uris LONGTEXT NOT NULL COMMENT '(DC2Type:array)', secret VARCHAR(255) NOT NULL, allowed_grant_types LONGTEXT NOT NULL COMMENT '(DC2Type:array)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE RefreshToken (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_7142379E5F37A13B (token), INDEX IDX_7142379E19EB6921 (client_id), INDEX IDX_7142379EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE AccessToken ADD CONSTRAINT FK_B39617F519EB6921 FOREIGN KEY (client_id) REFERENCES Client (id)");
        $this->addSql("ALTER TABLE AccessToken ADD CONSTRAINT FK_B39617F5A76ED395 FOREIGN KEY (user_id) REFERENCES person (personid)");
        $this->addSql("ALTER TABLE AuthCode ADD CONSTRAINT FK_F1D7D17719EB6921 FOREIGN KEY (client_id) REFERENCES Client (id)");
        $this->addSql("ALTER TABLE AuthCode ADD CONSTRAINT FK_F1D7D177A76ED395 FOREIGN KEY (user_id) REFERENCES person (personid)");
        $this->addSql("ALTER TABLE RefreshToken ADD CONSTRAINT FK_7142379E19EB6921 FOREIGN KEY (client_id) REFERENCES Client (id)");
        $this->addSql("ALTER TABLE RefreshToken ADD CONSTRAINT FK_7142379EA76ED395 FOREIGN KEY (user_id) REFERENCES person (personid)");
        $this->addSql("ALTER TABLE upload_file_log CHANGE upload_type upload_type VARCHAR(1) DEFAULT NULL, CHANGE upload_date upload_date DATETIME DEFAULT NULL, CHANGE uploaded_columns uploaded_columns VARCHAR(6000) DEFAULT NULL, CHANGE uploaded_row_count uploaded_row_count INT DEFAULT NULL, CHANGE status status VARCHAR(1) DEFAULT NULL, CHANGE uploaded_file_path uploaded_file_path VARCHAR(500) DEFAULT NULL, CHANGE error_file_path error_file_path VARCHAR(500) DEFAULT NULL, CHANGE job_number job_number VARCHAR(255) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE AccessToken DROP FOREIGN KEY FK_B39617F519EB6921");
        $this->addSql("ALTER TABLE AuthCode DROP FOREIGN KEY FK_F1D7D17719EB6921");
        $this->addSql("ALTER TABLE RefreshToken DROP FOREIGN KEY FK_7142379E19EB6921");
        $this->addSql("DROP TABLE AccessToken");
        $this->addSql("DROP TABLE AuthCode");
        $this->addSql("DROP TABLE Client");
        $this->addSql("DROP TABLE RefreshToken");
        $this->addSql("ALTER TABLE upload_file_log CHANGE upload_type upload_type VARCHAR(1) NOT NULL, CHANGE upload_date upload_date DATETIME NOT NULL, CHANGE uploaded_columns uploaded_columns VARCHAR(6000) NOT NULL, CHANGE uploaded_row_count uploaded_row_count INT NOT NULL, CHANGE status status VARCHAR(1) NOT NULL, CHANGE uploaded_file_path uploaded_file_path VARCHAR(500) NOT NULL, CHANGE error_file_path error_file_path VARCHAR(500) NOT NULL, CHANGE job_number job_number VARCHAR(500) NOT NULL");
    }
}
