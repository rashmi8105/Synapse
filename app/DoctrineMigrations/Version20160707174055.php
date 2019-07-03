<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Populates missing values in the org_permissionset_question table.
 */
class Version20160707174055 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql("SET SQL_SAFE_UPDATES = 0;");

        $this->addSql("UPDATE org_permissionset_question opq
                        INNER JOIN org_question oq
                            ON oq.id = opq.org_question_id
                        SET opq.cohort_code = oq.cohort
                        WHERE opq.cohort_code IS NULL;");

        $this->addSql("UPDATE org_permissionset_question opq
                        INNER JOIN org_question oq
                            ON oq.id = opq.org_question_id
                        SET opq.survey_id = oq.survey_id
                        WHERE opq.survey_id IS NULL;");

        // Reorder columns.
        $this->addSql("ALTER TABLE org_permissionset_question CHANGE COLUMN survey_id survey_id INT AFTER org_question_id;");

        $this->addSql("ALTER TABLE org_permissionset_question CHANGE COLUMN cohort_code cohort_code INT AFTER survey_id;");
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
