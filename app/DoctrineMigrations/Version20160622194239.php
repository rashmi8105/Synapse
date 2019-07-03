<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This migration script will update teh ebi_template_lang to
 * not have "MAP-Works" in the title but "Mapworks" instead.
 * This is a fix for: https://jira-mnv.atlassian.net/browse/ESPRJ-10839
 */
class Version20160622194239 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("
                      UPDATE ebi_template_lang
                        SET
                            body = REPLACE(body, 'MAP-Works', 'Mapworks')
                        WHERE
                            body LIKE '%MAP-Works%'
");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("
                      UPDATE ebi_template_lang
                        SET
                            body = REPLACE(body, 'Mapworks', 'MAP-Works')
                        WHERE
                            body LIKE '%Mapworks%'
");


    }
}
