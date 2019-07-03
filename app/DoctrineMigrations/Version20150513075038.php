<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150513075038 extends AbstractMigration
{

    /**
     *
     * @param Schema $schema            
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE ebi_search_history (person_id INT NOT NULL, ebi_search_id INT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, last_run DATETIME DEFAULT NULL, INDEX IDX_8608E3D3DE12AB56 (created_by), INDEX IDX_8608E3D325F94802 (modified_by), INDEX IDX_8608E3D31F6FA0AF (deleted_by), INDEX IDX_8608E3D3217BBB47 (person_id), INDEX IDX_8608E3D33849DC27 (ebi_search_id), PRIMARY KEY(person_id, ebi_search_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ebi_search_history ADD CONSTRAINT FK_8608E3D3DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_search_history ADD CONSTRAINT FK_8608E3D325F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_search_history ADD CONSTRAINT FK_8608E3D31F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_search_history ADD CONSTRAINT FK_8608E3D3217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_search_history ADD CONSTRAINT FK_8608E3D33849DC27 FOREIGN KEY (ebi_search_id) REFERENCES ebi_search (id)');
    }

    /**
     *
     * @param Schema $schema            
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE ebi_search_history');
    }
}
