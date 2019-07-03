<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150629082256 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
	
        $this->addSql('CREATE INDEX start_date_time_idx ON Appointments (start_date_time)');
        $this->addSql('CREATE INDEX end_date_time_idx ON Appointments (end_date_time)');
       
        $this->addSql('ALTER TABLE contact_info CHANGE home_phone home_phone VARCHAR(50) DEFAULT NULL, CHANGE office_phone office_phone VARCHAR(50) DEFAULT NULL');
		
        $this->addSql('CREATE INDEX metadata_listname_idx ON ebi_metadata_list_values (list_name(250))');
		
        $this->addSql('CREATE INDEX list_name_idx ON org_metadata_list_values (list_name)');
		
        $this->addSql('CREATE INDEX permissionset_name_idx ON org_permissionset (permissionset_name)');
		
        $this->addSql('CREATE INDEX username_idx ON person (username)');
        $this->addSql('CREATE INDEX deleted_at_idx ON person (deleted_at)');
		
		$this->addSql('DROP INDEX idx_year ON org_courses');
        $this->addSql('DROP INDEX idx_term ON org_courses');
        $this->addSql('DROP INDEX idx_college ON org_courses');
        $this->addSql('DROP INDEX idx_dept ON org_courses');
        $this->addSql('CREATE INDEX idx_year ON org_courses (org_academic_year_id)');
        $this->addSql('CREATE INDEX idx_term ON org_courses (org_academic_terms_id)');
        $this->addSql('CREATE INDEX idx_college ON org_courses (college_code)');
        $this->addSql('CREATE INDEX idx_dept ON org_courses (dept_code)');
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
		
        $this->addSql('DROP INDEX start_date_time_idx ON Appointments');
        $this->addSql('DROP INDEX end_date_time_idx ON Appointments');
		
        $this->addSql('ALTER TABLE contact_info CHANGE home_phone home_phone VARCHAR(15) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE office_phone office_phone VARCHAR(15) DEFAULT NULL COLLATE utf8_unicode_ci');
		
        $this->addSql('DROP INDEX metadata_listname_idx ON ebi_metadata_list_values');
		
        $this->addSql('DROP INDEX idx_year ON org_courses');
        $this->addSql('DROP INDEX idx_term ON org_courses');
        $this->addSql('DROP INDEX idx_college ON org_courses');
        $this->addSql('DROP INDEX idx_dept ON org_courses');
        $this->addSql('CREATE INDEX idx_year ON org_courses (organization_id, org_academic_year_id)');
        $this->addSql('CREATE INDEX idx_term ON org_courses (organization_id, org_academic_year_id, org_academic_terms_id)');
        $this->addSql('CREATE INDEX idx_college ON org_courses (organization_id, org_academic_year_id, org_academic_terms_id, college_code)');
        $this->addSql('CREATE INDEX idx_dept ON org_courses (organization_id, org_academic_year_id, org_academic_terms_id, college_code, dept_code)');
		
        $this->addSql('DROP INDEX list_name_idx ON org_metadata_list_values');
		
        $this->addSql('DROP INDEX permissionset_name_idx ON org_permissionset');
		
        $this->addSql('DROP INDEX username_idx ON person');
        $this->addSql('DROP INDEX deleted_at_idx ON person');
		
		       
    }
}
