<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150711184109 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

       
        
        
        $this->addSql('ALTER TABLE risk_group_person_history DROP KEY `PRIMARY`');
        $this->addSql('ALTER TABLE risk_group_person_history ADD id INT primary key AUTO_INCREMENT NOT NULL, CHANGE person_id person_id INT DEFAULT NULL, CHANGE risk_group_id risk_group_id INT DEFAULT NULL, CHANGE assignment_date assignment_date DATETIME DEFAULT NULL');
        //$this->addSql('ALTER TABLE risk_group_person_history ADD PRIMARY KEY (id)');
        
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

   
        
        $this->addSql('ALTER TABLE risk_group_person_history DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE risk_group_person_history DROP id, CHANGE person_id person_id INT NOT NULL, CHANGE risk_group_id risk_group_id INT NOT NULL, CHANGE assignment_date assignment_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE risk_group_person_history ADD PRIMARY KEY (person_id, risk_group_id, assignment_date)');
        
        
            }
}
