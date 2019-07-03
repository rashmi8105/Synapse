<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150827140456 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
		$this->addSql('ALTER TABLE report_tips CHANGE COLUMN title `title` VARCHAR(45) NULL DEFAULT NULL') ;
		$this->addSql('SET @emtid := (SELECT id FROM email_template where email_key=\'Email_PDF_Report_Student\');
UPDATE `synapse`.`email_template_lang` SET `body`=\'<!DOCTYPE html>
<html>
<body>
<style> 
	body {
		background: none repeat scroll 0 0# f4f4f4;
	}
	div {
		display: block;
		padding: 15px;
		width: 100%;
	}
	p {
		font - family: helvetica, arial, verdana, san - serif;
		font - size: 13px;
		color: #333;
	}
</style>
<div>
	<p>Hi $$studentname$$,</p>
	<p>Your Student report is now available. Please click the link below to access and view your results.</p>
	<p>$$pdf_report$$<p>
	<p>If you believe that you received this email in error or if you have any questions, please contact Mapworks support at support@map-works.com.</p>
	<p>Thank you from the Skyfactor team.</br>
	<img src="$$Skyfactor_Mapworks_logo$$" alt ="Skyfactor Mapworks logo" title ="Skyfactor Mapworks logo" /><p>
</div>
</body>
</html>\' WHERE `email_template_id`=@emtid;');

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
