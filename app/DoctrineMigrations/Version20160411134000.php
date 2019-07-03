<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Function: ESPRJ-8907 for increasing phone related fields
 */
class Version20160411134000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE contact_info 
                        MODIFY COLUMN primary_mobile VARCHAR(32) COLLATE UTF8_UNICODE_CI DEFAULT NULL, 
                        MODIFY COLUMN  alternate_mobile VARCHAR(32) COLLATE UTF8_UNICODE_CI DEFAULT NULL;"
                        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
