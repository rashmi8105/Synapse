<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160622165500 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('alter table person MODIFY is_locked enum(\'y\',\'n\') NOT NULL DEFAULT \'y\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
