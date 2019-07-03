<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160229125205 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        /**
         * changing SQL strict standard to avoide out of range error because this chage will treat as data is out of range
         * so changing form default SQL standard mode from STRICT_TRANS_TABLES to NO_ENGINE_SUBSTITUTION
         */
        $this->addSql('set sql_mode = \'NO_ENGINE_SUBSTITUTION\'');
        // making status field default value to 1
        $this->addSql('ALTER TABLE org_person_student CHANGE status status varchar(1) DEFAULT 1 NOT NULL');
       
     
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE org_person_student CHANGE status status VARCHAR(1) DEFAULT NULL');
      
    }
}
