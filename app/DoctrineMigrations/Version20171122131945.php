<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15889 Migration script added to modify email template for 'Email_PDF_Report_Student' key to add $$coordinator variables
 */
class Version20171122131945 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key = "Email_PDF_Report_Student");
UPDATE `synapse`.`email_template_lang` SET `body`=\'<!DOCTYPE html>
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
	<p>Hi $$student_first_name$$ $$student_last_name$$,</p>
	<p>Your Student report is now available. Please click the link below to access and view your results.</p>
	<p><a href ="$$pdf_report$$">Report view</a><p>
	<p>If you believe that you received this email in error or if you have any questions, please contact
        $$coordinator_first_name$$ $$coordinator_last_name$$ at $$coordinator_email_address$$.</p>
	<p>Thank you.</br>
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
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
