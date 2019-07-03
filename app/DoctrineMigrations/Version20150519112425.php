<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150519112425 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE risk_group_person_history (person_id INT NOT NULL, risk_group_id INT NOT NULL, assignment_date DATETIME DEFAULT NULL, INDEX fk_risk_group_person_history_person1_idx (person_id), INDEX fk_risk_group_person_history_risk_group1_idx (risk_group_id), PRIMARY KEY(person_id, risk_group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE risk_group_person_history ADD CONSTRAINT FK_72A1565C217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_group_person_history ADD CONSTRAINT FK_72A1565C187D9A28 FOREIGN KEY (risk_group_id) REFERENCES risk_group (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE risk_group_person_history');
    }
}
