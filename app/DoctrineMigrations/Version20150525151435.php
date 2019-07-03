<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150525151435 extends AbstractMigration
{

    /**
     *
     * @param Schema $schema            
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE ebi_users (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, first_name VARCHAR(100) DEFAULT NULL, last_name VARCHAR(100) DEFAULT NULL, email_address VARCHAR(200) DEFAULT NULL, password VARCHAR(100) DEFAULT NULL, mobile_number VARCHAR(15) DEFAULT NULL, is_active VARBINARY(10) DEFAULT NULL, INDEX IDX_53649663DE12AB56 (created_by), INDEX IDX_5364966325F94802 (modified_by), INDEX IDX_536496631F6FA0AF (deleted_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proxy_audit (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, proxy_log_id INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, action enum(\'insert\', \'update\', \'delete\'), resource VARCHAR(45) DEFAULT NULL, json_text_old VARCHAR(4000) DEFAULT NULL, json_text_new VARCHAR(4000) DEFAULT NULL, updated_on DATETIME DEFAULT NULL, INDEX IDX_4B645554DE12AB56 (created_by), INDEX IDX_4B64555425F94802 (modified_by), INDEX IDX_4B6455541F6FA0AF (deleted_by), INDEX fk_proxy_audit_proxy_log1_idx (proxy_log_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proxy_log (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT NOT NULL, person_id INT NOT NULL, ebi_users_id INT NOT NULL, person_id_proxied_for INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, login_date_time DATETIME DEFAULT NULL, logoff_date_time DATETIME DEFAULT NULL, INDEX IDX_7582DEABDE12AB56 (created_by), INDEX IDX_7582DEAB25F94802 (modified_by), INDEX IDX_7582DEAB1F6FA0AF (deleted_by), INDEX fk_proxy_log_organization1_idx (org_id), INDEX fk_proxy_log_person1_idx (person_id), INDEX fk_proxy_log_person2_idx (person_id_proxied_for), INDEX fk_proxy_log_ebi_users1_idx (ebi_users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE ebi_users ADD CONSTRAINT FK_53649663DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_users ADD CONSTRAINT FK_5364966325F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_users ADD CONSTRAINT FK_536496631F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE proxy_audit ADD CONSTRAINT FK_4B645554DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE proxy_audit ADD CONSTRAINT FK_4B64555425F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE proxy_audit ADD CONSTRAINT FK_4B6455541F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE proxy_audit ADD CONSTRAINT FK_4B645554AF617D59 FOREIGN KEY (proxy_log_id) REFERENCES proxy_log (id)');
        $this->addSql('ALTER TABLE proxy_log ADD CONSTRAINT FK_7582DEABDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE proxy_log ADD CONSTRAINT FK_7582DEAB25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE proxy_log ADD CONSTRAINT FK_7582DEAB1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE proxy_log ADD CONSTRAINT FK_7582DEABF4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE proxy_log ADD CONSTRAINT FK_7582DEAB217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE proxy_log ADD CONSTRAINT FK_7582DEAB411438C7 FOREIGN KEY (ebi_users_id) REFERENCES ebi_users (id)');
        $this->addSql('ALTER TABLE proxy_log ADD CONSTRAINT FK_7582DEABCF46066F FOREIGN KEY (person_id_proxied_for) REFERENCES person (id)');
    }

    /**
     *
     * @param Schema $schema            
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE proxy_log DROP FOREIGN KEY FK_7582DEAB411438C7');
        $this->addSql('ALTER TABLE proxy_audit DROP FOREIGN KEY FK_4B645554AF617D59');
        $this->addSql('DROP TABLE ebi_users');
        $this->addSql('DROP TABLE proxy_audit');
        $this->addSql('DROP TABLE proxy_log');
    }
}
