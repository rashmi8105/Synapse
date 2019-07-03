<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150825130406 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_calc_flags_student_reports (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT DEFAULT NULL, person_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, calculated_at DATETIME DEFAULT NULL, INDEX IDX_F4644A69DE12AB56 (created_by), INDEX IDX_F4644A6925F94802 (modified_by), INDEX IDX_F4644A691F6FA0AF (deleted_by), INDEX IDX_F4644A69F4837C1B (org_id), INDEX IDX_F4644A69217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
       
        $this->addSql('ALTER TABLE org_calc_flags_student_reports ADD CONSTRAINT FK_F4644A69DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_calc_flags_student_reports ADD CONSTRAINT FK_F4644A6925F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_calc_flags_student_reports ADD CONSTRAINT FK_F4644A691F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_calc_flags_student_reports ADD CONSTRAINT FK_F4644A69F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_calc_flags_student_reports ADD CONSTRAINT FK_F4644A69217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_calc_flags_student_reports');
    }
}
