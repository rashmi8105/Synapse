<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-16518 Migration script to add new values for email_pdf_report_student in mapworks_action_variable_description and mapworks_action_variable
 */
class Version20171123102107 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $skyfactor_mapworks_logo = '$$Skyfactor_Mapworks_logo$$';
        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';
        $coordinator_first_name = '$$coordinator_first_name$$';
        $coordinator_last_name = '$$coordinator_last_name$$';
        $coordinator_email_address = '$$coordinator_email_address$$';
        $pdf_report = '$$pdf_report$$';


        // add $$pdf_report$$ in mapworks_action_variable_description
        $this->addSql('INSERT INTO mapworks_action_variable_description(variable,description)VALUES ("$$pdf_report$$","PDF report url.");');

        // Variables for email_pdf_report_student
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action where event_key = 'student_report_create_student')");
        $this->addSql("SET @coordinatorFirstNameVariableDescriptionId = (SELECT id FROM mapworks_action_variable_description WHERE variable = '$coordinator_first_name')");
        $this->addSql("SET @coordinatorLastNameVariableDescriptionId = (SELECT id FROM mapworks_action_variable_description WHERE variable = '$coordinator_last_name')");
        $this->addSql("SET @coordinatorEmailVariableDescriptionId = (SELECT id FROM mapworks_action_variable_description WHERE variable = '$coordinator_email_address')");

        $this->addSql("SET @skyfactorMapworksLogoVariableDescriptionId = (SELECT id FROM mapworks_action_variable_description WHERE variable = '$skyfactor_mapworks_logo')");
        $this->addSql("SET @studentFirstNameVariableDescriptionId = (SELECT id FROM mapworks_action_variable_description WHERE variable = '$student_first_name')");
        $this->addSql("SET @studentLastNameVariableDescriptionId = (SELECT id FROM mapworks_action_variable_description WHERE variable = '$student_last_name')");
        $this->addSql("SET @pdfReportVariableDescriptionId = (SELECT id FROM mapworks_action_variable_description WHERE variable = '$pdf_report')");

        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) 
                            VALUES 
                            (
                                @mapworksActionId,
                                @coordinatorFirstNameVariableDescriptionId
                            ),
                            (
                                @mapworksActionId,
                                @coordinatorLastNameVariableDescriptionId
                            ),
                            (
                                @mapworksActionId,
                                @coordinatorEmailVariableDescriptionId
                            ),
                            (
                                @mapworksActionId,
                                @skyfactorMapworksLogoVariableDescriptionId
                            ),
                            (
                                @mapworksActionId,
                                @studentFirstNameVariableDescriptionId
                            ),
                            (
                                @mapworksActionId,
                                @studentLastNameVariableDescriptionId
                            ),
                            (
                                @mapworksActionId,
                                @pdfReportVariableDescriptionId
                            );");
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
