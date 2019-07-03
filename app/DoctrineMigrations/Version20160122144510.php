<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160122144510 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
    	$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    	$this->addSQL("insert into reports(name, description, is_batch_job, is_coordinator_report, short_code, is_active)
		VALUES('GPA Report', 'See aggregate view of GPA by term and by risk.  Can print to pdf, and download the data table as CSV', 'n', 'y', 'GPA', 'y');");
		$this->addSQL('ALTER TABLE `synapse`.`risk_level` 
		ADD COLUMN `report_sequence` INT(11) NULL AFTER `color_hex`;');
		$this->addSQL("update `risk_level` SET `report_sequence` = 1 where `risk_text` = 'green';");
		$this->addSQL("update `risk_level` SET `report_sequence` = 2 where `risk_text` = 'yellow';");
		$this->addSQL("update `risk_level` SET `report_sequence` = 3 where `risk_text` = 'red';");
		$this->addSQL("update `risk_level` SET `report_sequence` = 4 where `risk_text` = 'red2';");
		$this->addSQL("update `risk_level` SET `report_sequence` = 5 where `risk_text` = 'gray';");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


    }
}
