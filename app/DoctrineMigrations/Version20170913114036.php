<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15639  -  migration script to move the push PDF constants to ebi_config table
 */
class Version20170913114036 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PDF_INVERSE', '4A')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PDF_DPI', 72)");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PHANTOM_JS_PATH', '/usr/local/bin/phantomjs --web-security=false --ssl-protocol=tlsv12')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PDFIFY_JS', '/../pdfify.js')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('TOP_ISSUES_URL_PATH', '/top-issues/webpage')");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
