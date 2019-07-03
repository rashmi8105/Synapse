<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150601125952 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE wess_link CHANGE wess_prod_year wess_prod_year VARCHAR(4) DEFAULT NULL, CHANGE wess_cust_id wess_cust_id VARCHAR(4) DEFAULT NULL');
        $this->addSql('INSERT INTO `ebi_config` ( `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `key`, `value`) VALUES  (NULL, NULL, NULL, NULL, NULL, NULL, "System_Admin_URL", "http://synapse-qa-admin.mnv-tech.com/")');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE wess_link CHANGE wess_prod_year wess_prod_year INT DEFAULT NULL, CHANGE wess_cust_id wess_cust_id INT DEFAULT NULL');
    }
}
