<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150623072833 extends AbstractMigration
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
        
        $this->addSql('ALTER TABLE org_metadata DROP FOREIGN KEY FK_33BBA4F732C8A3DE');
        $this->addSql('ALTER TABLE org_metadata ADD status ENUM(\'active\',\'archived\')');
        $this->addSql('DROP INDEX idx_33bba4f732c8a3de ON org_metadata');
        $this->addSql('CREATE INDEX fk_org_metadata_organization1_idx ON org_metadata (organization_id)');
        $this->addSql('ALTER TABLE org_metadata ADD CONSTRAINT FK_33BBA4F732C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE ebi_metadata ADD status ENUM(\'active\',\'archived\')');
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
        
        $this->addSql('ALTER TABLE org_metadata DROP FOREIGN KEY FK_33BBA4F7DE12AB56');
        $this->addSql('ALTER TABLE org_metadata DROP FOREIGN KEY FK_33BBA4F725F94802');
        $this->addSql('ALTER TABLE org_metadata DROP FOREIGN KEY FK_33BBA4F71F6FA0AF');
        $this->addSql('DROP INDEX IDX_33BBA4F7DE12AB56 ON org_metadata');
        $this->addSql('DROP INDEX IDX_33BBA4F725F94802 ON org_metadata');
        $this->addSql('DROP INDEX IDX_33BBA4F71F6FA0AF ON org_metadata');
        $this->addSql('ALTER TABLE org_metadata DROP FOREIGN KEY FK_33BBA4F732C8A3DE');
        $this->addSql('ALTER TABLE org_metadata DROP status');
        $this->addSql('DROP INDEX fk_org_metadata_organization1_idx ON org_metadata');
        $this->addSql('CREATE INDEX IDX_33BBA4F732C8A3DE ON org_metadata (organization_id)');
        $this->addSql('ALTER TABLE org_metadata ADD CONSTRAINT FK_33BBA4F732C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE ebi_metadata DROP status');
    }
}
