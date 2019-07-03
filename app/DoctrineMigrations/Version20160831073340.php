<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Soft-deletes the predefined searches which are being removed.
 */
class Version20160831073340 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql("UPDATE ebi_search
                        SET deleted_at = NOW(), deleted_by = -25
                        WHERE query_key IN ('Class_Level', 'Respondents_To_Current_Survey', 'Non_Respondents_To_Current_Survey', 'Accessed_Current_Survey_Report', 'Not_Accessed_Current_Survey_Report');");
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
