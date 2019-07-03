<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This migration script adds one new column referral_history_id in referrals_interested_parties table
 */
class Version20160920060642 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE referrals_interested_parties
                        ADD referral_history_id INT DEFAULT NULL AFTER referrals_id');
        $this->addSql('ALTER TABLE referrals_interested_parties
                        ADD CONSTRAINT fk_referrals_interested_parties_referral_history_id FOREIGN KEY (referral_history_id) REFERENCES referral_history (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE referrals_interested_parties DROP referral_history_id');

    }
}
