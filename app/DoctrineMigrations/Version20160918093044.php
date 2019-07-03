<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Soft-deletes repetitive system data for the Our Students Report.
 * The relationship between report_section_elements and factors is the same for each survey.
 */
class Version20160918093044 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql("UPDATE report_element_buckets
                        SET deleted_at = NOW()
                        WHERE element_id IN (
                            SELECT id FROM report_section_elements
                            WHERE section_id IN (4,5,6,7)
                            AND survey_id <> 11
                            AND factor_id IS NOT NULL
                            AND deleted_at IS NULL
                        )
                        AND deleted_at IS NULL;");

        $this->addSql("UPDATE report_section_elements
                        SET deleted_at = NOW()
                        WHERE section_id IN (4,5,6,7)
                        AND survey_id <> 11
                        AND factor_id IS NOT NULL
                        AND deleted_at IS NULL;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');
    }
}
