<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150609103524 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE ebi_template (`key` VARCHAR(45) NOT NULL, is_active ENUM(\'y\',\'n\'), PRIMARY KEY(`key`)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ebi_template_lang (ebi_template_key VARCHAR(45) NOT NULL, lang_id INT NOT NULL, description VARCHAR(100) DEFAULT NULL, body LONGTEXT DEFAULT NULL, INDEX fk_ebi_template_lang_language_master1_idx (lang_id), INDEX fk_ebi_template_key_idx (ebi_template_key), PRIMARY KEY(ebi_template_key, lang_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ebi_template_lang ADD CONSTRAINT FK_5E2527B11644997F FOREIGN KEY (ebi_template_key) REFERENCES ebi_template (`key`)');
        $this->addSql('ALTER TABLE ebi_template_lang ADD CONSTRAINT FK_5E2527B1B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE ebi_template_lang DROP FOREIGN KEY FK_5E2527B11644997F');
        $this->addSql('DROP TABLE ebi_template');
        $this->addSql('DROP TABLE ebi_template_lang');
    }
}
