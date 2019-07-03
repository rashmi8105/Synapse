<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150806094619 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("UPDATE `ebi_config` SET `value`='risk_uploads,talking_points,roaster_uploads,survey_uploads,talking_points_uploads,factor_uploads' WHERE `key`='Ebi_Upload_Dir'");
        
    }
   public function down(Schema $schema)
   {
       // this down() migration is auto-generated, please modify it to your needs
       $this->abortIf($this->connection->getDatabasePlatform()
           ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

   }
}
