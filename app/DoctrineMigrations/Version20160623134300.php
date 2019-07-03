<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160623134300 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE synapse.org_person_student DROP COLUMN receivesurvey');
        $this->addSql('ALTER TABLE synapse.org_person_student DROP COLUMN surveycohort');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
