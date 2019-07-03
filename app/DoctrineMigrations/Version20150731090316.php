<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150731090316 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $query = <<<CDATA
        UPDATE `ebi_config` SET `value` = 'Parent_Group_ID' WHERE `key` = 'SubGroup_Upload_ParentGroup_ColumnName';

UPDATE `ebi_config` SET `value` = 'RiskGroupID' WHERE `key` = 'Student_Upload_RiskGroup_Definition_ColumnName';

UPDATE `ebi_config` SET `value` = 'number' WHERE `key` = 'Student_Upload_RiskGroup_Definition_Type';

UPDATE `ebi_config` SET `value` = 'Please see the Risk tab in Set Up for available risk groups and the risk group IDs' WHERE `key` = 'Student_Upload_RiskGroup_Definition_Desc';
CDATA;
        $this->addSql($query);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
