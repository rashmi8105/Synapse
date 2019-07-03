<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161101132451 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO `ebi_config` (`key`, `value`) VALUES ('Google_Client_Id', '435452515316-ldaj947qe0831e56ktcstcl985fau1tp.apps.googleusercontent.com')");
        $this->addSql("INSERT INTO `ebi_config` (`key`, `value`) VALUES ('Google_Client_Secret', 'W_GHZ5oPsLoBdXAZjiLIgNpp')");
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
    }
}
