<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151102151515 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO synapse.organization (`id`,`subdomain`) values ('-2','ART user')");
		
        $this->addSql("INSERT INTO synapse.person (`firstname`,`lastname`,`external_id`,`username`,`password`,`organization_id`) values ('art','member','Art123','art.member@gmail.com','$2y$13\$GOZ4jneZjjgknooNxJEzwOBJ3URrsRmHVSgKudF0nsHnOb0F4/8ue','-2')");
		
        $this->addSql("INSERT into Client (`random_id`,`redirect_uris`,`secret`,`allowed_grant_types`) values ('2y0ku7f5748wwwskkk84o00ssgwsgkokks8ogs08ckscckcskg','a:0:{}','365m9433y94w40wccow04g8wwkscccg00gsw44skgw0448c8k4','a:2:{i:0;s:8:\"password\";i:1;s:13:\"refresh_token\";}')");
		
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}



