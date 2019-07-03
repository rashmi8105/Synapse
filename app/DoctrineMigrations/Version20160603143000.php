<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Function: ESPRJ-10539 for decreasing contact_info.home_phone and contact.office_phone size to varchar(32)
 */
class Version20160603143000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE contact_info 
                        MODIFY COLUMN home_phone VARCHAR(32) COLLATE UTF8_UNICODE_CI DEFAULT NULL,
                        MODIFY COLUMN office_phone VARCHAR(32) COLLATE UTF8_UNICODE_CI DEFAULT NULL;"
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
