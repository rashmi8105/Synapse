<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150907114128 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('SET @refassstaff := (SELECT id FROM email_template where email_key="Referral_Assign_to_staff");
            UPDATE `email_template_lang` SET  `body`= \'<html>
	<head>
		<style>
		body {
    background: none repeat scroll 0 0 #f4f4f4;
        
}
		table {
    padding: 21px;
    width: 799px;
	font-family: helvetica,arial,verdana,san-serif;
	font-size:13px;
	color:#333;
	}
		</style>
	</head>
	<body>
        
		<table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
			<tbody>
        
				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $$firstname$$,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>A referral was recently assigned to you in Mapworks. Please sign in to your account to view and take action on this referral.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>\'  WHERE `email_template_id`=@refassstaff;"
            ');
        	
        	
        $this->addSql("SET @appremstaffstud := (SELECT id FROM email_template where email_key='Appointment_Reminder_Staff_to_Student');
        
				UPDATE `email_template_lang` SET `body`='<html>
					 <head>
						 <style>
							 body {
								background: none repeat scroll 0 0;
				
							}
							table {
								padding: 21px;
								width: 799px;
								font-family: Helvetica,Arial,Verdana,San-serif;
								font-size:13px;
								color:#333;
							}
					</style>
					 </head>
					 <body>
						 <table cellpadding=\"10\" style=\"background:#ffffff;\"  cellspacing=\"0\">
							 <tbody>
								 <tr style=\"background:#fff;border-collapse:collapse;\">
									 <td>Hi \$\$student_name\$\$:</td>
								 </tr>
								 <tr style=\"background:#fff;border-collapse:collapse;\">
									 <td style=\"line-height: 1.6;\">This is a reminder that you have an appointment with \$\$staff_name\$\$ on \$\$app_datetime\$\$. <br/><br/> To view the appointment details, please log in to your Mapworks dashboard and visit
									<a style=\"color: #0033CC;\" href=\"\$\$student_dashboard\$\$\">Mapworks student dashboard view appointment module</a>.
									</td>
								 </tr>
								 <tr style=\"background:#fff;border-collapse:collapse;\">
									<td>Thank you.<br/><img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></td>
								 </tr>
								 <tr style=\"background:#fff;border-collapse:collapse;\">
									<td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td>
								 </tr>
							 </tbody>
						 </table>
					 </body>
				 </html>' WHERE `email_template_id`=@appremstaffstud;");
        	
        	
        $this->addSql('SET @refinterstaff := (SELECT id FROM email_template where email_key="Referral_InterestedParties_Staff");
            UPDATE `email_template_lang` SET  `body`= \'<html>
      <head>
             <style>
             body {
     background: none repeat scroll 0 0 #f4f4f4;
        
 }
             table {
     padding: 21px;
     width: 799px;
      font-family: helvetica,arial,verdana,san-serif;
      font-size:13px;
      color:#333;
      }
             </style>
      </head>
      <body>
        
              <table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
                    <tbody>
        
                           <tr style="background:#fff;border-collapse:collapse;"><td>Hi $$staff_firstname$$,</td></tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td>A faculty or staff member has referred $$student_name$$ to a campus resource through the Mapworks system and added you as an interested party. To view the referral details, please log in to Mapworks and visit <a class="external-link" href="$$staff_referralpage$$" target="_blank" style="color: rgb(41, 114, 155); text-decoration: underline;">Mapworks student dashboard view referral module</a>.</tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td> If you have any questions, please contact ($$coordinator_name$$,$$coordinator_title$$,$$coordinator_email$$ ).</td></tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td>Thank you.</td></tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td><img src="$$Skyfactor_Mapworks_logo$$" title="Skyfactor Mapworks logo" alt="Skyfactor Mapworks logo"/></td></tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
                     </tbody>
             </table>
      </body>
</html>\'  WHERE `email_template_id`=@refinterstaff;"
            ');
        
        
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Course_Upload_Notification');
        
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
  </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">
  <tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi \$\$user_first_name\$\$,</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>
  Your course upload has finished importing. </td></tr>
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td>
 \$\$download_failed_log_file\$\$</td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td></td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you.<br/>
 </td></tr>
        
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td>
 <img src = \"\$\$Skyfactor_Mapworks_logo\$\$\" alt = \"Skyfactor Mapworks logo\" title = \"Skyfactor Mapworks logo\" />
 </td></tr>
        
 <tr style = \"background:#fff;border-collapse:collapse;\"> <td> This email is an auto-generated message.Replies to automated messages are not monitored. </td></tr> </tbody>
      </table> </body></html>' WHERE `email_template_id`=@emtid;");
        
        
        $this->addSql("SET @coursefacuploadnot := (SELECT id FROM email_template where email_key='Course_Faculty_Upload_Notification');
        
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
  </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">
  <tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi \$\$user_first_name\$\$,</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>
  Your faculty upload has finished importing.</td></tr>
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td>
 \$\$download_failed_log_file\$\$</td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td></td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you.<br/>
 </td></tr>
        
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td>
 <img src = \"\$\$Skyfactor_Mapworks_logo\$\$\" alt = \"Skyfactor Mapworks logo\" title = \"Skyfactor Mapworks logo\" />
 </td></tr>
        
 <tr style = \"background:#fff;border-collapse:collapse;\"> <td> This email is an auto-generated message.Replies to automated messages are not monitored. </td></tr> </tbody>
      </table> </body></html>' WHERE `email_template_id`=@coursefacuploadnot;");
         
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Course_Student_Upload_Notification');
        
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
  </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">
  <tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi \$\$user_first_name\$\$,</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>
  Your student upload has finished importing.</td></tr>
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td>
 \$\$download_failed_log_file\$\$</td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td></td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you.<br/>
 </td></tr>
        
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td>
 <img src = \"\$\$Skyfactor_Mapworks_logo\$\$\" alt = \"Skyfactor Mapworks logo\" title = \"Skyfactor Mapworks logo\" />
 </td></tr>
        
 <tr style = \"background:#fff;border-collapse:collapse;\"> <td> This email is an auto-generated message.Replies to automated messages are not monitored. </td></tr> </tbody>
      </table> </body></html>' WHERE `email_template_id`=@emtid;");
        
         
        	
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
