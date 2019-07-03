<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Corrected text in reports table (which is displayed in the Report Center).
 */
class Version20160503035650 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE reports SET name = "Survey Factors Report" WHERE short_code = "SUR-FR";');

        $this->addSql('UPDATE reports SET description = "View average GPA over time, overall and by risk.  View percent of students with GPA < 2.0.  Export to csv, print to pdf." WHERE short_code = "GPA";');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
