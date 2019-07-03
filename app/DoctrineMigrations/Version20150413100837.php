<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150413100837 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE org_person_student DROP FOREIGN KEY FK_9C88CAE1217BBB47');
        $this->addSql('ALTER TABLE org_person_student DROP FOREIGN KEY FK_9C88CAE132C8A3DE');
        $this->addSql('ALTER TABLE org_person_student ADD receivesurvey VARCHAR(1) DEFAULT NULL, ADD surveycohort VARCHAR(45) DEFAULT NULL');
        $this->addSql('ALTER TABLE org_person_student ADD CONSTRAINT FK_9C88CAE1DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student ADD CONSTRAINT FK_9C88CAE125F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student ADD CONSTRAINT FK_9C88CAE11F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_9C88CAE1DE12AB56 ON org_person_student (created_by)');
        $this->addSql('CREATE INDEX IDX_9C88CAE125F94802 ON org_person_student (modified_by)');
        $this->addSql('CREATE INDEX IDX_9C88CAE11F6FA0AF ON org_person_student (deleted_by)');
        $this->addSql('DROP INDEX idx_9c88cae132c8a3de ON org_person_student');
        $this->addSql('CREATE INDEX fk_org_person_student_organization1 ON org_person_student (organization_id)');
        $this->addSql('DROP INDEX idx_9c88cae1217bbb47 ON org_person_student');
        $this->addSql('CREATE INDEX fk_org_person_student_person1 ON org_person_student (person_id)');
        $this->addSql('ALTER TABLE org_person_student ADD CONSTRAINT FK_9C88CAE1217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student ADD CONSTRAINT FK_9C88CAE132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE wess_link DROP FOREIGN KEY FK_175DBF438D7CC0D2');
        $this->addSql('ALTER TABLE wess_link DROP FOREIGN KEY FK_175DBF43F3B0CE4A');
        $this->addSql('DROP INDEX fk_wess_link_org_academic_year1_idx ON wess_link');
        $this->addSql('DROP INDEX fk_wess_link_org_academic_terms1_idx ON wess_link');
        $this->addSql('ALTER TABLE wess_link ADD year_id VARCHAR(10) DEFAULT NULL, DROP org_academic_terms_id, DROP org_academic_year_id');
        $this->addSql('ALTER TABLE wess_link ADD CONSTRAINT FK_175DBF4340C1FEA7 FOREIGN KEY (year_id) REFERENCES year (id)');
        $this->addSql('CREATE INDEX fk_wess_link_year1_idx ON wess_link (year_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_person_student DROP FOREIGN KEY FK_9C88CAE1DE12AB56');
        $this->addSql('ALTER TABLE org_person_student DROP FOREIGN KEY FK_9C88CAE125F94802');
        $this->addSql('ALTER TABLE org_person_student DROP FOREIGN KEY FK_9C88CAE11F6FA0AF');
        $this->addSql('DROP INDEX IDX_9C88CAE1DE12AB56 ON org_person_student');
        $this->addSql('DROP INDEX IDX_9C88CAE125F94802 ON org_person_student');
        $this->addSql('DROP INDEX IDX_9C88CAE11F6FA0AF ON org_person_student');
        $this->addSql('ALTER TABLE org_person_student DROP FOREIGN KEY FK_9C88CAE132C8A3DE');
        $this->addSql('ALTER TABLE org_person_student DROP FOREIGN KEY FK_9C88CAE1217BBB47');
        $this->addSql('ALTER TABLE org_person_student DROP receivesurvey, DROP surveycohort');
        $this->addSql('DROP INDEX fk_org_person_student_organization1 ON org_person_student');
        $this->addSql('CREATE INDEX IDX_9C88CAE132C8A3DE ON org_person_student (organization_id)');
        $this->addSql('DROP INDEX fk_org_person_student_person1 ON org_person_student');
        $this->addSql('CREATE INDEX IDX_9C88CAE1217BBB47 ON org_person_student (person_id)');
        $this->addSql('ALTER TABLE org_person_student ADD CONSTRAINT FK_9C88CAE132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_person_student ADD CONSTRAINT FK_9C88CAE1217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE wess_link DROP FOREIGN KEY FK_175DBF4340C1FEA7');
        $this->addSql('DROP INDEX fk_wess_link_year1_idx ON wess_link');
        $this->addSql('ALTER TABLE wess_link ADD org_academic_terms_id INT DEFAULT NULL, ADD org_academic_year_id INT DEFAULT NULL, DROP year_id');
        $this->addSql('ALTER TABLE wess_link ADD CONSTRAINT FK_175DBF438D7CC0D2 FOREIGN KEY (org_academic_terms_id) REFERENCES org_academic_terms (id)');
        $this->addSql('ALTER TABLE wess_link ADD CONSTRAINT FK_175DBF43F3B0CE4A FOREIGN KEY (org_academic_year_id) REFERENCES org_academic_year (id)');
        $this->addSql('CREATE INDEX fk_wess_link_org_academic_year1_idx ON wess_link (org_academic_year_id)');
        $this->addSql('CREATE INDEX fk_wess_link_org_academic_terms1_idx ON wess_link (org_academic_terms_id)');
    }
}
