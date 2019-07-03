<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141030154643 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE referrals (id INT AUTO_INCREMENT NOT NULL, person_id_faculty INT DEFAULT NULL, person_id_student INT DEFAULT NULL, organization_id INT DEFAULT NULL, activity_category_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, note TEXT DEFAULT NULL, status VARCHAR(1) DEFAULT NULL, is_leaving TINYINT(1) DEFAULT NULL, is_discussed TINYINT(1) DEFAULT NULL, referrer_permission TINYINT(1) DEFAULT NULL, is_high_priority TINYINT(1) DEFAULT NULL, notify_student TINYINT(1) DEFAULT NULL, access_private TINYINT(1) DEFAULT NULL, access_public TINYINT(1) DEFAULT NULL, access_team TINYINT(1) DEFAULT NULL, referral_date DATETIME DEFAULT NULL, INDEX IDX_1B7DC896FFB0AA26 (person_id_faculty), INDEX IDX_1B7DC8965F056556 (person_id_student), INDEX IDX_1B7DC89632C8A3DE (organization_id), INDEX IDX_1B7DC8961CC8F7EE (activity_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE referrals ADD CONSTRAINT FK_1B7DC896FFB0AA26 FOREIGN KEY (person_id_faculty) REFERENCES person (id)');
        $this->addSql('ALTER TABLE referrals ADD CONSTRAINT FK_1B7DC8965F056556 FOREIGN KEY (person_id_student) REFERENCES person (id)');
        $this->addSql('ALTER TABLE referrals ADD CONSTRAINT FK_1B7DC89632C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE referrals ADD CONSTRAINT FK_1B7DC8961CC8F7EE FOREIGN KEY (activity_category_id) REFERENCES activity_category (id)');
        $this->addSql('ALTER TABLE activity_category CHANGE parent_activity_category_id parent_activity_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE org_permissionset DROP accesslevel_ind_agg, DROP accesslevel_agg, DROP risk_indicator, DROP intent_to_leave');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE referrals');
        $this->addSql('ALTER TABLE activity_category CHANGE parent_activity_category_id parent_activity_category_id INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE org_permissionset ADD accesslevel_ind_agg TINYINT(1) DEFAULT NULL, ADD accesslevel_agg TINYINT(1) DEFAULT NULL, ADD risk_indicator TINYINT(1) DEFAULT NULL, ADD intent_to_leave TINYINT(1) DEFAULT NULL');

    }
}
