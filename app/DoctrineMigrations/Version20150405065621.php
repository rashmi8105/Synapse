<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150405065621 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE academic_update (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT DEFAULT NULL, org_courses_id INT DEFAULT NULL, academic_update_request_id INT DEFAULT NULL, person_id_faculty_responded INT DEFAULT NULL, person_id_student INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, update_type ENUM(\'bulk\',\'targeted\',\'adhoc\',\'ftp\') DEFAULT NULL, status ENUM(\'open\',\'closed\',\'cancelled\',\'saved\') DEFAULT NULL, request_date DATETIME DEFAULT NULL, due_date DATETIME DEFAULT NULL, update_date DATETIME DEFAULT NULL, failure_risk_level VARCHAR(10) DEFAULT NULL, grade VARCHAR(10) DEFAULT NULL, absence INT DEFAULT NULL, comment VARCHAR(300) DEFAULT NULL, refer_for_assistance TINYINT(1) DEFAULT NULL, send_to_student TINYINT(1) DEFAULT NULL, is_upload TINYINT(1) DEFAULT NULL, is_adhoc TINYINT(1) DEFAULT NULL, final_grade VARCHAR(10) DEFAULT NULL, INDEX IDX_73DF4B2ADE12AB56 (created_by), INDEX IDX_73DF4B2A25F94802 (modified_by), INDEX IDX_73DF4B2A1F6FA0AF (deleted_by), INDEX fk_academic_update_organization1_idx (org_id), INDEX fk_academic_update_org_courses1_idx (org_courses_id), INDEX fk_academic_update_academic_update_request1_idx (academic_update_request_id), INDEX fk_academic_update_person2_idx (person_id_faculty_responded), INDEX fk_academic_update_person3_idx (person_id_student), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        
        $this->addSql('CREATE TABLE academic_update_assigned_faculty (id INT AUTO_INCREMENT NOT NULL,person_id_faculty_assigned INT NOT NULL, org_id INT NOT NULL, academic_update_id INT NOT NULL, INDEX fk_academic_update_assigned_faculty_person1_idx (person_id_faculty_assigned), INDEX fk_academic_update_assigned_faculty_organization1_idx (org_id), INDEX fk_academic_update_assigned_faculty_academic_update1_idx (academic_update_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        
        $this->addSql('CREATE TABLE academic_update_request (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT DEFAULT NULL, person_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, update_type ENUM(\'bulk\',\'targeted\') DEFAULT NULL, request_date DATETIME DEFAULT NULL, name VARCHAR(100) DEFAULT NULL, description VARCHAR(4000) DEFAULT NULL, status ENUM(\'open\',\'closed\',\'cancelled\') DEFAULT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, due_date DATETIME DEFAULT NULL, subject VARCHAR(400) DEFAULT NULL, email_optional_msg VARCHAR(2000) DEFAULT NULL, INDEX IDX_3C7F051ADE12AB56 (created_by), INDEX IDX_3C7F051A25F94802 (modified_by), INDEX IDX_3C7F051A1F6FA0AF (deleted_by), INDEX fk_academic_update_request_organization1_idx (org_id), INDEX fk_academic_update_request_person1_idx (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        
        $this->addSql('CREATE TABLE academic_update_request_course (id INT AUTO_INCREMENT NOT NULL,org_courses_id INT NOT NULL, org_id INT NOT NULL, academic_update_request_id INT NOT NULL, INDEX fk_academic_update_request_course_org_courses1_idx (org_courses_id), INDEX fk_academic_update_request_course_organization1_idx (org_id), INDEX fk_academic_update_request_course_academic_update_request1_idx (academic_update_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE academic_update_request_faculty (id INT AUTO_INCREMENT NOT NULL,person_id INT NOT NULL, org_id INT NOT NULL, academic_update_request_id INT NOT NULL, INDEX fk_academic_update_request_faculty_person1_idx (person_id), INDEX fk_academic_update_request_faculty_organization1_idx (org_id), INDEX fk_academic_update_request_faculty_academic_update_request1_idx (academic_update_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE academic_update_request_group (id INT AUTO_INCREMENT NOT NULL,org_group_id INT NOT NULL, org_id INT NOT NULL, academic_update_request_id INT NOT NULL, INDEX fk_academic_update_request_group_org_group1_idx (org_group_id), INDEX fk_academic_update_request_group_organization1_idx (org_id), INDEX fk_academic_update_request_group_academic_update_request1_idx (academic_update_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        
        $this->addSql('CREATE TABLE academic_update_request_metadata (id INT AUTO_INCREMENT NOT NULL, ebi_metadata_id INT DEFAULT NULL, org_metadata_id INT DEFAULT NULL, org_id INT DEFAULT NULL, academic_update_request_id INT DEFAULT NULL, search_value VARCHAR(2000) DEFAULT NULL, INDEX fk_academic_update_request_metadata_ebi_metadata1_idx (ebi_metadata_id), INDEX fk_academic_update_request_metadata_org_metadata1_idx (org_metadata_id), INDEX fk_academic_update_request_metadata_organization1_idx (org_id), INDEX fk_academic_update_request_metadata_academic_update_request_idx (academic_update_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE academic_update_request_student (id INT AUTO_INCREMENT NOT NULL,person_id INT NOT NULL, org_id INT NOT NULL, academic_update_request_id INT NOT NULL, INDEX fk_academic_update_request_student_person1_idx (person_id), INDEX fk_academic_update_request_student_organization1_idx (org_id), INDEX fk_academic_update_request_student_academic_update_request1_idx (academic_update_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE academic_update ADD CONSTRAINT FK_73DF4B2ADE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update ADD CONSTRAINT FK_73DF4B2A25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update ADD CONSTRAINT FK_73DF4B2A1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update ADD CONSTRAINT FK_73DF4B2AF4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE academic_update ADD CONSTRAINT FK_73DF4B2A7C751C40 FOREIGN KEY (org_courses_id) REFERENCES org_courses (id)');
        $this->addSql('ALTER TABLE academic_update ADD CONSTRAINT FK_73DF4B2ACA3D7B42 FOREIGN KEY (academic_update_request_id) REFERENCES academic_update_request (id)');
        $this->addSql('ALTER TABLE academic_update ADD CONSTRAINT FK_73DF4B2A9170E9C9 FOREIGN KEY (person_id_faculty_responded) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update ADD CONSTRAINT FK_73DF4B2A5F056556 FOREIGN KEY (person_id_student) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update_assigned_faculty ADD CONSTRAINT FK_DE7DDC162B8D6DFB FOREIGN KEY (person_id_faculty_assigned) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update_assigned_faculty ADD CONSTRAINT FK_DE7DDC16F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE academic_update_assigned_faculty ADD CONSTRAINT FK_DE7DDC16567A5FFE FOREIGN KEY (academic_update_id) REFERENCES academic_update (id)');
        $this->addSql('ALTER TABLE academic_update_request ADD CONSTRAINT FK_3C7F051ADE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update_request ADD CONSTRAINT FK_3C7F051A25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update_request ADD CONSTRAINT FK_3C7F051A1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update_request ADD CONSTRAINT FK_3C7F051AF4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE academic_update_request ADD CONSTRAINT FK_3C7F051A217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update_request_course ADD CONSTRAINT FK_BDB1EA0E7C751C40 FOREIGN KEY (org_courses_id) REFERENCES org_courses (id)');
        $this->addSql('ALTER TABLE academic_update_request_course ADD CONSTRAINT FK_BDB1EA0EF4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE academic_update_request_course ADD CONSTRAINT FK_BDB1EA0ECA3D7B42 FOREIGN KEY (academic_update_request_id) REFERENCES academic_update_request (id)');
        $this->addSql('ALTER TABLE academic_update_request_faculty ADD CONSTRAINT FK_423869E9217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update_request_faculty ADD CONSTRAINT FK_423869E9F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE academic_update_request_faculty ADD CONSTRAINT FK_423869E9CA3D7B42 FOREIGN KEY (academic_update_request_id) REFERENCES academic_update_request (id)');
        $this->addSql('ALTER TABLE academic_update_request_group ADD CONSTRAINT FK_9378AAEA82FB49A4 FOREIGN KEY (org_group_id) REFERENCES org_group (id)');
        $this->addSql('ALTER TABLE academic_update_request_group ADD CONSTRAINT FK_9378AAEAF4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE academic_update_request_group ADD CONSTRAINT FK_9378AAEACA3D7B42 FOREIGN KEY (academic_update_request_id) REFERENCES academic_update_request (id)');
        $this->addSql('ALTER TABLE academic_update_request_metadata ADD CONSTRAINT FK_7942D0EBBB49FE75 FOREIGN KEY (ebi_metadata_id) REFERENCES ebi_metadata (id)');
        $this->addSql('ALTER TABLE academic_update_request_metadata ADD CONSTRAINT FK_7942D0EB4012B3BF FOREIGN KEY (org_metadata_id) REFERENCES org_metadata (id)');
        $this->addSql('ALTER TABLE academic_update_request_metadata ADD CONSTRAINT FK_7942D0EBF4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE academic_update_request_metadata ADD CONSTRAINT FK_7942D0EBCA3D7B42 FOREIGN KEY (academic_update_request_id) REFERENCES academic_update_request (id)');
        $this->addSql('ALTER TABLE academic_update_request_student ADD CONSTRAINT FK_E28DA699217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update_request_student ADD CONSTRAINT FK_E28DA699F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE academic_update_request_student ADD CONSTRAINT FK_E28DA699CA3D7B42 FOREIGN KEY (academic_update_request_id) REFERENCES academic_update_request (id)');
        
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE academic_update_assigned_faculty DROP FOREIGN KEY FK_DE7DDC16567A5FFE');
        $this->addSql('ALTER TABLE academic_update DROP FOREIGN KEY FK_73DF4B2ACA3D7B42');
        $this->addSql('ALTER TABLE academic_update_request_course DROP FOREIGN KEY FK_BDB1EA0ECA3D7B42');
        $this->addSql('ALTER TABLE academic_update_request_faculty DROP FOREIGN KEY FK_423869E9CA3D7B42');
        $this->addSql('ALTER TABLE academic_update_request_group DROP FOREIGN KEY FK_9378AAEACA3D7B42');
        $this->addSql('ALTER TABLE academic_update_request_metadata DROP FOREIGN KEY FK_7942D0EBCA3D7B42');
        $this->addSql('ALTER TABLE academic_update_request_student DROP FOREIGN KEY FK_E28DA699CA3D7B42');
    
        $this->addSql('DROP TABLE academic_update');
        $this->addSql('DROP TABLE academic_update_assigned_faculty');
        $this->addSql('DROP TABLE academic_update_request');
        $this->addSql('DROP TABLE academic_update_request_course');
        $this->addSql('DROP TABLE academic_update_request_faculty');
        $this->addSql('DROP TABLE academic_update_request_group');
        $this->addSql('DROP TABLE academic_update_request_metadata');
        $this->addSql('DROP TABLE academic_update_request_student');
        
        
    }
}
