<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150930063356 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE report_section_elements ADD survey_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE report_section_elements ADD CONSTRAINT FK_91D6E5F5B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('CREATE INDEX IDX_91D6E5F5B3FE509D ON report_section_elements (survey_id)');
        $this->addSql('ALTER TABLE report_sections CHANGE section_query section_query LONGTEXT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE report_section_elements DROP FOREIGN KEY FK_91D6E5F5B3FE509D');
        $this->addSql('DROP INDEX IDX_91D6E5F5B3FE509D ON report_section_elements');
        $this->addSql('ALTER TABLE report_section_elements DROP survey_id');
        $this->addSql('ALTER TABLE report_sections CHANGE section_query section_query LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
       
    }
}
