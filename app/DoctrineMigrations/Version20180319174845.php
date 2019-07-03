<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-13222; added base information into the new tables
 */
class Version20180319174845 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        // update column names to the readable variety
        $this->addSql("Update `synapse`.`upload_column_header` SET upload_column_display_name = upload_column_name;");

        // insert initial ebi_download_types into the database
        $this->addSql("INSERT INTO `synapse`.`ebi_download_type` (`id`, `download_type`, `download_display_name`) VALUES ('1', 'data_dump', 'Data Dump');                          ");
        $this->addSql("INSERT INTO `synapse`.`ebi_download_type` (`id`, `download_type`, `download_display_name`) VALUES ('2', 'data_definition_file', 'Data Definition File');     ");
        $this->addSql("INSERT INTO `synapse`.`ebi_download_type` (`id`, `download_type`, `download_display_name`) VALUES ('3', 'template', 'Template');                            ");

        // insert auth keys and Privacy Policy Items
        $this->addSql("INSERT INTO `synapse`.`upload_column_header` (`upload_column_name`, `upload_column_display_name`) VALUES ('IsPrivacyPolicyAccepted', 'IsPrivacyPolicyAccepted');    ");
        $this->addSql("INSERT INTO `synapse`.`upload_column_header` (`upload_column_name`, `upload_column_display_name`) VALUES ('StudentAuthKey', 'StudentAuthKey');                      ");
        $this->addSql("INSERT INTO `synapse`.`upload_column_header` (`upload_column_name`, `upload_column_display_name`) VALUES ('PrivacyPolicyAcceptedDate', 'PrivacyPolicyAcceptedDate');");
        $this->addSql("INSERT INTO `synapse`.`upload_column_header` (`upload_column_name`, `upload_column_display_name`) VALUES ('FacultyAuthKey', 'FacultyAuthKey');                      ");

        // renaming table to something that better describes what is going on
        $this->addSql("ALTER TABLE `synapse`.`upload_column_header_map` RENAME TO  `synapse`.`upload_column_header_download_map` ;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
