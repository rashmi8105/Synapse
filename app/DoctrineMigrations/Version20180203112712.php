<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 *  Migration script for ESPRJ-10535
 */
class Version20180203112712 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE upload (
                              id INT AUTO_INCREMENT NOT NULL,
                              upload_name VARCHAR(255) NOT NULL, 
                              created_by int(11) DEFAULT NULL,
                              created_at datetime DEFAULT NULL,
                              modified_by int(11) DEFAULT NULL,
                              modified_at datetime DEFAULT NULL,
                              deleted_by int(11) DEFAULT NULL,
                              deleted_at datetime DEFAULT NULL, 
                              INDEX IDX_17BDE61FDE12AB56 (created_by), 
                              INDEX IDX_17BDE61F25F94802 (modified_by), 
                              INDEX IDX_17BDE61F1F6FA0AF (deleted_by), 
                              PRIMARY KEY(id)) 
                              DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');


        $this->addSql('CREATE TABLE upload_column_header (
                                id INT AUTO_INCREMENT NOT NULL,
                                upload_column_name VARCHAR(255) NOT NULL, 
                                created_by int(11) DEFAULT NULL,
                                created_at datetime DEFAULT NULL,
                                modified_by int(11) DEFAULT NULL,
                                modified_at datetime DEFAULT NULL,
                                deleted_by int(11) DEFAULT NULL,
                                deleted_at datetime DEFAULT NULL,  
                                INDEX IDX_32C36C6CDE12AB56 (created_by), 
                                INDEX IDX_32C36C6C25F94802 (modified_by), 
                                INDEX IDX_32C36C6C1F6FA0AF (deleted_by), 
                                PRIMARY KEY(id)) 
                                DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');


        $this->addSql('CREATE TABLE upload_column_header_map (
                              id INT AUTO_INCREMENT NOT NULL,
                              upload_id INT DEFAULT NULL, 
                              upload_column_header_id INT DEFAULT NULL,
                              sort_order int(11) DEFAULT NULL,
                              created_by int(11) DEFAULT NULL,
                              created_at datetime DEFAULT NULL,
                              modified_by int(11) DEFAULT NULL,
                              modified_at datetime DEFAULT NULL,
                              deleted_by int(11) DEFAULT NULL,
                              deleted_at datetime DEFAULT NULL, 
                              INDEX IDX_C6FB1CB3DE12AB56 (created_by), 
                              INDEX IDX_C6FB1CB325F94802 (modified_by), 
                              INDEX IDX_C6FB1CB31F6FA0AF (deleted_by), 
                              INDEX IDX_C6FB1CB3CCCFBA31 (upload_id), 
                              INDEX IDX_C6FB1CB332C36C6C (upload_column_header_id),
                              PRIMARY KEY(id)) 
                              DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');


        $this->addSql('ALTER TABLE upload ADD CONSTRAINT FK_17BDE61FDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE upload ADD CONSTRAINT FK_17BDE61F25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE upload ADD CONSTRAINT FK_17BDE61F1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE upload_column_header ADD CONSTRAINT FK_32C36C6CDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE upload_column_header ADD CONSTRAINT FK_32C36C6C25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE upload_column_header ADD CONSTRAINT FK_32C36C6C1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE upload_column_header_map ADD CONSTRAINT FK_C6FB1CB3DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE upload_column_header_map ADD CONSTRAINT FK_C6FB1CB325F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE upload_column_header_map ADD CONSTRAINT FK_C6FB1CB31F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE upload_column_header_map ADD CONSTRAINT FK_C6FB1CB3CCCFBA31 FOREIGN KEY (upload_id) REFERENCES upload (id)');
        $this->addSql('ALTER TABLE upload_column_header_map ADD CONSTRAINT FK_C6FB1CB332C36C6C FOREIGN KEY (upload_column_header_id) REFERENCES upload_column_header (id)');


        $this->addSql("INSERT INTO `synapse`.`upload` (`upload_name`,  `created_at`, `modified_at`)  VALUES ('faculty', now(), now())");
        $this->addSql("INSERT INTO `synapse`.`upload` (`upload_name`,  `created_at`, `modified_at`)  VALUES ('student', now(), now())");


        //Faculty and student Upload Columns
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('ExternalId', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('AuthUsername', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('Firstname', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('Lastname', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('Title', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('Address1', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('Address2', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('City', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('Zip', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('State', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('Country', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('PrimaryMobile', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('AlternateMobile', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('OfficePhone', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('PrimaryEmail', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('AlternateEmail', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('IsActive', now(),now())");


        //Student Upload specific columns
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('StudentPhoto', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('SurveyCohort', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('TransitionOneReceiveSurvey', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('CheckupOneReceiveSurvey', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('TransitionTwoReceiveSurvey', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('CheckupTwoReceiveSurvey', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('YearId', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('TermId', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('PrimaryConnect', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('RiskGroupId', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('Participating', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('RetentionTrack', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('EnrolledAtMidpointOfAcademicYear', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('EnrolledAtBeginningOfAcademicYear', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('CompletedADegree', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('HomePhone', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('PrimaryMobileProvider', now(),now())");
        $this->addSql("INSERT INTO `upload_column_header` (`upload_column_name`,`created_at`, `modified_at`) VALUES ('AlternateMobileProvider', now(),now())");


        // Faculty  Upload column Map
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '1', '1', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '2', '2', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '3', '3', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '4', '4', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '5', '5', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '6', '6', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '7', '7', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '8', '8', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '9', '9', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '10', '10', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '11', '11', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '12', '12', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '13', '13', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '14', '14', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '15', '15', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '16', '16', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('1', '17', '17', now(), now())");



        //Student Upload Column Map

        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '1', '1', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '2', '2', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '3', '3', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '4', '4', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '5', '5', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '18', '6', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '17', '7', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '19', '8', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '20', '9', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '21', '10', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '22', '11', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '23', '12', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '24', '13', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '25', '14', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '26', '15', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '27', '16', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '28', '17', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '29', '18', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '30', '19', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '31', '20', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '32', '21', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '6', '22', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '7', '23', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '8', '24', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '9', '25', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '10', '26', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '11', '27', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '12', '28', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '13', '27', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '33', '30', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '15', '31', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '16', '32', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '34', '33', now(), now())");
        $this->addSql("INSERT INTO `upload_column_header_map` (`upload_id`, `upload_column_header_id`, `sort_order`,`created_at`, `modified_at`) VALUES ('2', '35', '34', now(), now())");


    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


    }
}
