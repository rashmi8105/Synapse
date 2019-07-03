<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150729110928 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE upload_file_log CHANGE upload_type upload_type enum(\'A\', \'C\', \'F\', \'G\', \'S\', \'SB\', \'SM\', \'T\', \'TP\', \'P\',\'H\',\'SL\',\'RV\',\'RM\',\'RMA\',\'CI\',\'FA\',\'GS\',\'GF\',\'S2G\')');		
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE upload_file_log CHANGE upload_type upload_type enum(\'A\', \'C\', \'F\', \'G\', \'S\', \'SB\', \'SM\', \'T\', \'TP\', \'P\',\'H\',\'SL\',\'RV\',\'RM\',\'RMV\')');
    }
}
