<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Function: ESPRJ-10714
 * Changing uploaded_columns data type from VARCHAR(6000) to TEXT
 */
class Version20160622151921 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE upload_file_log CHANGE COLUMN uploaded_columns uploaded_columns TEXT CHARACTER SET 'utf8' NULL DEFAULT NULL ;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
