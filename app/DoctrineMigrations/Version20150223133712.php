<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150223133712 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_course_faculty (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, org_courses_id INT DEFAULT NULL, person_id INT DEFAULT NULL, org_permissionset_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_1660B3F4DE12AB56 (created_by), INDEX IDX_1660B3F425F94802 (modified_by), INDEX IDX_1660B3F41F6FA0AF (deleted_by), INDEX fk_course_faculty_organization1_idx (organization_id), INDEX fk_course_faculty_org_courses1_idx (org_courses_id), INDEX fk_course_faculty_person1_idx (person_id), INDEX fk_org_course_faculty_org_permissionset1_idx (org_permissionset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_courses (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT NOT NULL, org_academic_year_id INT NOT NULL, org_academic_terms_id INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, course_section_id VARCHAR(15) DEFAULT NULL, college_code VARCHAR(10) DEFAULT NULL, dept_code VARCHAR(10) DEFAULT NULL, subject_code VARCHAR(10) DEFAULT NULL, course_number VARCHAR(10) DEFAULT NULL, course_name VARCHAR(200) DEFAULT NULL, section_number VARCHAR(10) DEFAULT NULL, days_times VARCHAR(45) DEFAULT NULL, location VARCHAR(45) DEFAULT NULL, credit_hours NUMERIC(5, 2) DEFAULT NULL, externalId VARCHAR(15) DEFAULT NULL, INDEX IDX_DADA0E82DE12AB56 (created_by), INDEX IDX_DADA0E8225F94802 (modified_by), INDEX IDX_DADA0E821F6FA0AF (deleted_by), INDEX fk_org_courses_organization1_idx (organization_id), INDEX fk_org_courses_org_academic_year1_idx (org_academic_year_id), INDEX fk_org_courses_org_academic_terms1_idx (org_academic_terms_id), INDEX uniquecoursesectionid (organization_id, course_section_id), INDEX idx_year (organization_id, org_academic_year_id), INDEX idx_term (organization_id, org_academic_year_id, org_academic_terms_id), INDEX idx_college (organization_id, org_academic_year_id, org_academic_terms_id, college_code), INDEX idx_dept (organization_id, org_academic_year_id, org_academic_terms_id, college_code, dept_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_course_student (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, org_courses_id INT DEFAULT NULL, person_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_B6D57C84DE12AB56 (created_by), INDEX IDX_B6D57C8425F94802 (modified_by), INDEX IDX_B6D57C841F6FA0AF (deleted_by), INDEX fk_course_student_organization1_idx (organization_id), INDEX fk_course_student_org_courses1_idx (org_courses_id), INDEX fk_course_student_person1_idx (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE year (id VARCHAR(10) NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_BB827337DE12AB56 (created_by), INDEX IDX_BB82733725F94802 (modified_by), INDEX IDX_BB8273371F6FA0AF (deleted_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_course_faculty ADD CONSTRAINT FK_1660B3F4DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_course_faculty ADD CONSTRAINT FK_1660B3F425F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_course_faculty ADD CONSTRAINT FK_1660B3F41F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_course_faculty ADD CONSTRAINT FK_1660B3F432C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_course_faculty ADD CONSTRAINT FK_1660B3F47C751C40 FOREIGN KEY (org_courses_id) REFERENCES org_courses (id)');
        $this->addSql('ALTER TABLE org_course_faculty ADD CONSTRAINT FK_1660B3F4217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_course_faculty ADD CONSTRAINT FK_1660B3F47ABB76BC FOREIGN KEY (org_permissionset_id) REFERENCES org_permissionset (id)');
        $this->addSql('ALTER TABLE org_courses ADD CONSTRAINT FK_DADA0E82DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_courses ADD CONSTRAINT FK_DADA0E8225F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_courses ADD CONSTRAINT FK_DADA0E821F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_courses ADD CONSTRAINT FK_DADA0E8232C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_courses ADD CONSTRAINT FK_DADA0E82F3B0CE4A FOREIGN KEY (org_academic_year_id) REFERENCES org_academic_year (id)');
        $this->addSql('ALTER TABLE org_courses ADD CONSTRAINT FK_DADA0E828D7CC0D2 FOREIGN KEY (org_academic_terms_id) REFERENCES org_academic_terms (id)');
        $this->addSql('ALTER TABLE org_course_student ADD CONSTRAINT FK_B6D57C84DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_course_student ADD CONSTRAINT FK_B6D57C8425F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_course_student ADD CONSTRAINT FK_B6D57C841F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_course_student ADD CONSTRAINT FK_B6D57C8432C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_course_student ADD CONSTRAINT FK_B6D57C847C751C40 FOREIGN KEY (org_courses_id) REFERENCES org_courses (id)');
        $this->addSql('ALTER TABLE org_course_student ADD CONSTRAINT FK_B6D57C84217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE year ADD CONSTRAINT FK_BB827337DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE year ADD CONSTRAINT FK_BB82733725F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE year ADD CONSTRAINT FK_BB8273371F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_academic_terms DROP FOREIGN KEY FK_75DF84EFF3B0CE4A');
        $this->addSql('ALTER TABLE org_academic_terms DROP FOREIGN KEY FK_75DF84EF32C8A3DE');
        $this->addSql('ALTER TABLE org_academic_terms ADD term_code INT DEFAULT NULL, DROP short_code');
        $this->addSql('DROP INDEX idx_75df84ef32c8a3de ON org_academic_terms');
        $this->addSql('CREATE INDEX fk_academicperiod_organizationid ON org_academic_terms (organization_id)');
        $this->addSql('DROP INDEX idx_75df84eff3b0ce4a ON org_academic_terms');
        $this->addSql('CREATE INDEX fk_academicperiod_academicyearid ON org_academic_terms (org_academic_year_id)');
        $this->addSql('ALTER TABLE org_academic_terms ADD CONSTRAINT FK_75DF84EFF3B0CE4A FOREIGN KEY (org_academic_year_id) REFERENCES org_academic_year (id)');
        $this->addSql('ALTER TABLE org_academic_terms ADD CONSTRAINT FK_75DF84EF32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_academic_year DROP FOREIGN KEY FK_A4C0972D32C8A3DE');
        $this->addSql('ALTER TABLE org_academic_year CHANGE short_code year_id VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE org_academic_year ADD CONSTRAINT FK_A4C0972D40C1FEA7 FOREIGN KEY (year_id) REFERENCES year (id)');
        $this->addSql('CREATE INDEX fk_org_academic_year_year1_idx ON org_academic_year (year_id)');
        $this->addSql('CREATE UNIQUE INDEX uk_yearid ON org_academic_year (organization_id, year_id)');
        $this->addSql('DROP INDEX idx_a4c0972d32c8a3de ON org_academic_year');
        $this->addSql('CREATE INDEX relationship9 ON org_academic_year (organization_id)');
        $this->addSql('ALTER TABLE org_academic_year ADD CONSTRAINT FK_A4C0972D32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_course_faculty DROP FOREIGN KEY FK_1660B3F47C751C40');
        $this->addSql('ALTER TABLE org_course_student DROP FOREIGN KEY FK_B6D57C847C751C40');
        $this->addSql('ALTER TABLE org_academic_year DROP FOREIGN KEY FK_A4C0972D40C1FEA7');
        $this->addSql('DROP TABLE org_course_faculty');
        $this->addSql('DROP TABLE org_courses');
        $this->addSql('DROP TABLE org_course_student');
        $this->addSql('DROP TABLE year');
        
    }
}
