<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151103153638 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $query = <<<CDATA
SELECT id into @person_id FROM person where organization_id = '-2' and username = 'art.member@gmail.com';
 
Insert into contact_info set `primary_email` = 'art.member@gmail.com',created_at = now(),modified_at = now();
select max(id) into @contact_id from contact_info;
Insert into person_contact_info set `contact_id` = @contact_id , `person_id` = @person_id;
CDATA;
        $this->addSql($query);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
