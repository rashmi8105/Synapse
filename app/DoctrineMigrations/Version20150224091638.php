<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150224091638 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE upload_file_log ADD course_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE upload_file_log ADD CONSTRAINT FK_E70B1E39DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE upload_file_log ADD CONSTRAINT FK_E70B1E3925F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE upload_file_log ADD CONSTRAINT FK_E70B1E391F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_E70B1E39DE12AB56 ON upload_file_log (created_by)');
        $this->addSql('CREATE INDEX IDX_E70B1E3925F94802 ON upload_file_log (modified_by)');
        $this->addSql('CREATE INDEX IDX_E70B1E391F6FA0AF ON upload_file_log (deleted_by)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE upload_file_log DROP FOREIGN KEY FK_E70B1E39DE12AB56');
        $this->addSql('ALTER TABLE upload_file_log DROP FOREIGN KEY FK_E70B1E3925F94802');
        $this->addSql('ALTER TABLE upload_file_log DROP FOREIGN KEY FK_E70B1E391F6FA0AF');
        $this->addSql('DROP INDEX IDX_E70B1E39DE12AB56 ON upload_file_log');
        $this->addSql('DROP INDEX IDX_E70B1E3925F94802 ON upload_file_log');
        $this->addSql('DROP INDEX IDX_E70B1E391F6FA0AF ON upload_file_log');
        $this->addSql('ALTER TABLE upload_file_log DROP course_id');
    }
}
