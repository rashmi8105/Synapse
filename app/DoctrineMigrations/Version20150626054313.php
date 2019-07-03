<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150626054313 extends AbstractMigration
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
        
        $this->addSql('CREATE TABLE referral_routing_rules (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, activity_category_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, person_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, is_primary_coordinator TINYINT(1) DEFAULT NULL, INDEX IDX_D954FA99DE12AB56 (created_by), INDEX IDX_D954FA9925F94802 (modified_by), INDEX IDX_D954FA991F6FA0AF (deleted_by), INDEX fk_referral_routing_rules_activity_category_id_idx (activity_category_id), INDEX fk_referral_routing_rules_organization_id_idx (organization_id), INDEX fk_referral_routing_rules_person_id_idx (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE referral_routing_rules ADD CONSTRAINT FK_D954FA99DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE referral_routing_rules ADD CONSTRAINT FK_D954FA9925F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE referral_routing_rules ADD CONSTRAINT FK_D954FA991F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE referral_routing_rules ADD CONSTRAINT FK_D954FA991CC8F7EE FOREIGN KEY (activity_category_id) REFERENCES activity_category (id)');
        $this->addSql('ALTER TABLE referral_routing_rules ADD CONSTRAINT FK_D954FA9932C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE referral_routing_rules ADD CONSTRAINT FK_D954FA99217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
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
        
        $this->addSql('DROP TABLE referral_routing_rules');
    }
}
