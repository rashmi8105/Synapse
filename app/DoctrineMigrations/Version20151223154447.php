<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151223154447 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('update person set is_locked = \'n\' where is_locked is null');
        $this->addSql('alter table person MODIFY is_locked enum(\'y\',\'n\') NOT NULL DEFAULT \'n\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('alter table person MODIFY is_locked enum(\'y\',\'n\') DEFAULT \'n\'');
    }
}
