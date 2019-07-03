<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This migration script creates new table referral_history
 */
class Version20160920053647 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSQL('DROP TABLE IF EXISTS referral_history;');

        $this->addSql("CREATE TABLE referral_history (
            id INT AUTO_INCREMENT NOT NULL,
            referral_id INT NOT NULL,
            action ENUM('create','update','close','reopen','reassign','interested party') NOT NULL,
            created_by INT NOT NULL,
            created_at DATETIME NOT NULL,
            person_id_assigned_to INT DEFAULT NULL,
            activity_category_id INT DEFAULT NULL,
            note TEXT DEFAULT NULL,
            status VARCHAR(1) DEFAULT NULL,
            is_leaving TINYINT(1) DEFAULT NULL,
            is_discussed TINYINT(1) DEFAULT NULL,
            referrer_permission TINYINT(1) DEFAULT NULL,
            is_high_priority TINYINT(1) DEFAULT NULL,
            notify_student TINYINT(1) DEFAULT NULL,
            access_private TINYINT(1) DEFAULT NULL,
            access_public TINYINT(1) DEFAULT NULL,
            access_team TINYINT(1) DEFAULT NULL,
            is_reason_routed TINYINT(1) DEFAULT NULL,
            user_key VARCHAR(100) DEFAULT NULL,
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

        $this->addSql('ALTER TABLE referral_history 
            ADD CONSTRAINT fk_referral_history_referral_id FOREIGN KEY (referral_id) REFERENCES referrals (id),
            ADD CONSTRAINT fk_referral_history_created_by FOREIGN KEY (created_by) REFERENCES person (id),
            ADD CONSTRAINT fk_referral_history_person_id_assigned_to FOREIGN KEY (person_id_assigned_to) REFERENCES person (id),
            ADD CONSTRAINT fk_referral_history_activity_category_id FOREIGN KEY (activity_category_id) REFERENCES activity_category (id);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE referral_history');
    }
}
