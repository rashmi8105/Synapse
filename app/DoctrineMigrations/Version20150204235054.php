<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150204235054 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE organization ADD inactivity_timeout INT DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE organization DROP inactivity_timeout');
    }
}
