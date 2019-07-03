<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150612140439 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE proxy_log DROP FOREIGN KEY FK_7582DEAB217BBB47');
        $this->addSql('ALTER TABLE proxy_log DROP FOREIGN KEY FK_7582DEABF4837C1B');
        $this->addSql('DROP INDEX fk_proxy_log_person1_idx ON proxy_log');
        $this->addSql('DROP INDEX fk_proxy_log_organization1_idx ON proxy_log');
        $this->addSql('ALTER TABLE proxy_log ADD organization_id INT NOT NULL, DROP person_id, DROP org_id');
        $this->addSql('ALTER TABLE proxy_log ADD CONSTRAINT FK_7582DEAB32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX fk_proxy_log_organization1_idx ON proxy_log (organization_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE proxy_log DROP FOREIGN KEY FK_7582DEAB32C8A3DE');
        $this->addSql('DROP INDEX fk_proxy_log_organization1_idx ON proxy_log');
        $this->addSql('ALTER TABLE proxy_log ADD org_id INT NOT NULL, CHANGE organization_id person_id INT NOT NULL');
        $this->addSql('ALTER TABLE proxy_log ADD CONSTRAINT FK_7582DEAB217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE proxy_log ADD CONSTRAINT FK_7582DEABF4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX fk_proxy_log_person1_idx ON proxy_log (person_id)');
        $this->addSql('CREATE INDEX fk_proxy_log_organization1_idx ON proxy_log (org_id)');
    }
}
