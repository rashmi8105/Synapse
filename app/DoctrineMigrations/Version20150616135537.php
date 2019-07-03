<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150616135537 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE proxy_log ADD person_id INT NOT NULL, CHANGE ebi_users_id ebi_users_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE proxy_log ADD CONSTRAINT FK_7582DEAB217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX fk_proxy_log_person1_idx ON proxy_log (person_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE proxy_log DROP FOREIGN KEY FK_7582DEAB217BBB47');
        $this->addSql('DROP INDEX fk_proxy_log_person1_idx ON proxy_log');
        $this->addSql('ALTER TABLE proxy_log DROP person_id, CHANGE ebi_users_id ebi_users_id INT NOT NULL');
    }
}
