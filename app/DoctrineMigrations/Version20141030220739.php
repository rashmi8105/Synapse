<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141030220739 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE referrals_interested_parties (id INT AUTO_INCREMENT NOT NULL, referrals_id INT DEFAULT NULL, person_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_2C4E0EEBB24851AE (referrals_id), INDEX IDX_2C4E0EEB217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referrals_teams (id INT AUTO_INCREMENT NOT NULL, referrals_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, Teams_id INT DEFAULT NULL, INDEX IDX_8786AC502F403D44 (Teams_id), INDEX IDX_8786AC50B24851AE (referrals_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE referrals_interested_parties ADD CONSTRAINT FK_2C4E0EEBB24851AE FOREIGN KEY (referrals_id) REFERENCES referrals (id)');
        $this->addSql('ALTER TABLE referrals_interested_parties ADD CONSTRAINT FK_2C4E0EEB217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE referrals_teams ADD CONSTRAINT FK_8786AC502F403D44 FOREIGN KEY (Teams_id) REFERENCES Teams (id)');
        $this->addSql('ALTER TABLE referrals_teams ADD CONSTRAINT FK_8786AC50B24851AE FOREIGN KEY (referrals_id) REFERENCES referrals (id)');
        $this->addSql('ALTER TABLE org_permissionset ADD accesslevel_ind_agg TINYINT(1) DEFAULT NULL, ADD accesslevel_agg TINYINT(1) DEFAULT NULL, ADD risk_indicator TINYINT(1) DEFAULT NULL, ADD intent_to_leave TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE referrals_interested_parties');
        $this->addSql('DROP TABLE referrals_teams');
        $this->addSql('ALTER TABLE org_permissionset DROP accesslevel_ind_agg, DROP accesslevel_agg, DROP risk_indicator, DROP intent_to_leave');
    }
}
