<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140729233858 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE person_entity (Person_id INT NOT NULL, Entity_id INT NOT NULL, INDEX IDX_928D74DEA38A39E4 (Person_id), INDEX IDX_928D74DE3D4FFFE (Entity_id), PRIMARY KEY(Person_id, Entity_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE person_entity ADD CONSTRAINT FK_928D74DEA38A39E4 FOREIGN KEY (Person_id) REFERENCES person (personid)");
        $this->addSql("ALTER TABLE person_entity ADD CONSTRAINT FK_928D74DE3D4FFFE FOREIGN KEY (Entity_id) REFERENCES entity (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE person_entity");
    }
}
