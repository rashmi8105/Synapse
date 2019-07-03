<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150506111729 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE risk_group_lang (lang_id INT NOT NULL, risk_group_id INT NOT NULL, name VARCHAR(200) DEFAULT NULL, description VARCHAR(2000) DEFAULT NULL, INDEX fk_risk_group_lang_risk_group1_idx (risk_group_id), INDEX fk_risk_group_lang_language_master1_idx (lang_id), PRIMARY KEY(lang_id, risk_group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE risk_group_lang ADD CONSTRAINT FK_15E35E79B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE risk_group_lang ADD CONSTRAINT FK_15E35E79187D9A28 FOREIGN KEY (risk_group_id) REFERENCES risk_group (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE risk_group_lang');
    }
}
