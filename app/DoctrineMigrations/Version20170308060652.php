<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration Scriot for  ESPRJ-13566
 */
class Version20170308060652 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Added the audit columns and  organization _id column
        $this->addSql('ALTER TABLE AuthCode ADD organization_id INT DEFAULT NULL, ADD created_by INT DEFAULT NULL, ADD modified_by INT DEFAULT NULL, ADD deleted_by INT DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD modified_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE AuthCode ADD CONSTRAINT FK_F1D7D17732C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE AuthCode ADD CONSTRAINT FK_F1D7D177DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE AuthCode ADD CONSTRAINT FK_F1D7D17725F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE AuthCode ADD CONSTRAINT FK_F1D7D1771F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_F1D7D17732C8A3DE ON AuthCode (organization_id)');
        $this->addSql('CREATE INDEX IDX_F1D7D177DE12AB56 ON AuthCode (created_by)');
        $this->addSql('CREATE INDEX IDX_F1D7D17725F94802 ON AuthCode (modified_by)');
        $this->addSql('CREATE INDEX IDX_F1D7D1771F6FA0AF ON AuthCode (deleted_by)');

        // Added the audit columns and  person_id , organization _id column
        $this->addSql('ALTER TABLE Client ADD person_id INT DEFAULT NULL, ADD organization_id INT DEFAULT NULL, ADD created_by INT DEFAULT NULL, ADD modified_by INT DEFAULT NULL, ADD deleted_by INT DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD modified_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE Client ADD CONSTRAINT FK_C0E80163217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE Client ADD CONSTRAINT FK_C0E8016332C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE Client ADD CONSTRAINT FK_C0E80163DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE Client ADD CONSTRAINT FK_C0E8016325F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE Client ADD CONSTRAINT FK_C0E801631F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_C0E80163217BBB47 ON Client (person_id)');
        $this->addSql('CREATE INDEX IDX_C0E8016332C8A3DE ON Client (organization_id)');
        $this->addSql('CREATE INDEX IDX_C0E80163DE12AB56 ON Client (created_by)');
        $this->addSql('CREATE INDEX IDX_C0E8016325F94802 ON Client (modified_by)');
        $this->addSql('CREATE INDEX IDX_C0E801631F6FA0AF ON Client (deleted_by)');

        //ebi_config values and  APi coordinatxor role added
        $this->addSql("INSERT INTO `ebi_config` (`key`, `value`) VALUES ('max_API_error_count_on_interval', '500')");
        $this->addSql("INSERT INTO `ebi_config` (`key`, `value`) VALUES ('API_error_interval_in_minutes', '15')");
        $this->addSql("INSERT INTO `ebi_config` (`key`, `value`) VALUES ('API_integration_master_switch', '1')");
        $this->addSql("INSERT INTO `ebi_config` (`key`, `value`) VALUES ('post_put_body_max_record_count', '500')");
        $this->addSql("INSERT INTO `role` (`status`) VALUES ('A')");
        $this->addSql("SET @roleId := (select max(id) from role);
                       INSERT INTO `role_lang` (`role_id`,`lang_id`,`role_name`) VALUES (@roleId,1,'API Coordinator')"
                     );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
