<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150712163522 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

		      
       $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Appointment_Cancel_Staff_to_Student');
        
		UPDATE `email_template_lang` SET `body`='<html>
			<head>
		 <style>body {
			   background: none repeat scroll 0 0 #f4f4f4;
				
		   }table {
			   padding: 21px;
			   width: 799px;
			font-family: helvetica,arial,verdana,san-serif;
			font-size:13px;
			color:#333;
		  }
			</style>
			</head>
			<body>
         
 		<table cellpadding=\"10\" style=\"background:#ffffff;\" cellspacing=\"0\">
 			<tbody>
         
 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi \$\$student_name\$\$,</td></tr>
 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Your booked appointment with
 				\$\$staff_name\$\$ on \$\$app_datetime\$\$ has been cancelled.
 				To book a new appointment, please log in to your Mapworks dashboard and visit <a class=\"external-link\" href=\"$\$student_dashboard\$\$\" target=\"_blank\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">Mapworks student dashboard book appointment module</a>.</td></tr>
 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/><img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>', `subject`='Appointment cancelled via Mapworks' WHERE `email_template_id`=@emtid;");
  
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}