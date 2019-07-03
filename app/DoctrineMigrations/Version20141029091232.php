<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141029091232 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE ebi_permissionset ADD created_by INT DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD modified_by INT DEFAULT NULL, ADD modified_at DATETIME DEFAULT NULL, ADD deleted_by INT DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL, ADD accesslevel_agg TINYINT(1) DEFAULT NULL, ADD accesslevel_ind_agg TINYINT(1) DEFAULT NULL, DROP access_level');
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE ebi_permissionset ADD access_level VARCHAR(10) DEFAULT NULL, DROP created_by, DROP created_at, DROP modified_by, DROP modified_at, DROP deleted_by, DROP deleted_at, DROP accesslevel_agg, DROP accesslevel_ind_agg');
       
    }
}
