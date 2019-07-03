<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150825152530 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE report_calculated_values ADD element_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE report_calculated_values ADD CONSTRAINT FK_CB8DFCDC1F1F2A24 FOREIGN KEY (element_id) REFERENCES report_section_elements (id)');
        $this->addSql('CREATE INDEX IDX_CB8DFCDC1F1F2A24 ON report_calculated_values (element_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE report_calculated_values DROP FOREIGN KEY FK_CB8DFCDC1F1F2A24');
        $this->addSql('DROP INDEX IDX_CB8DFCDC1F1F2A24 ON report_calculated_values');
        $this->addSql('ALTER TABLE report_calculated_values DROP element_id');
        
    }
}
