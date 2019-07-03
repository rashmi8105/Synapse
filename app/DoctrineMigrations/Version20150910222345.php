<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150910222345 extends AbstractMigration
{
	public function up(Schema $schema)
    {
		$this->addSql("DELETE FROM reports WHERE short_code IN ('ACT-R', 'SUR-SR')");
	}
	
	public function down(Schema $schema)
    {
		
	}
}
	