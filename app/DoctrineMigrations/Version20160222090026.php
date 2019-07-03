<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160222090026 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    /**
     * Duplicate entry in metadata_list_values for list_name = 'Samoa'
     * and resulted in incorrect value for organization - this script changes list_name of one entry 
     * more specific to list_value id & list_name added as part of where clause to avoid any data 
     * integrity issue across environments
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $query = <<<CDATA
             update metadata_list_values set list_name = 'SamoaPacific' where id = 463 and list_name = 'Samoa';
CDATA;

$this->addSql($query);
       
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
