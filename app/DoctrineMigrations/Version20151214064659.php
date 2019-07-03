<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151214064659 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
  public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
 
        $this->addSql("set @unclassified := (SELECT id FROM synapse.activity_category where short_name = 'Unclassified');
        set @mapworksIssue := (SELECT id FROM synapse.activity_category where short_name = 'MAP-Works Issues');
        update contacts  set activity_category_id  = @mapworksIssue   WHERE activity_category_id  = @unclassified;
        update Appointments  set activity_category_id  = @mapworksIssue   WHERE activity_category_id  = @unclassified;
        update referrals  set activity_category_id  = @mapworksIssue   WHERE activity_category_id  = @unclassified;
        update note  set activity_category_id  = @mapworksIssue   WHERE activity_category_id  = @unclassified;
        update email  set activity_category_id  = @mapworksIssue   WHERE activity_category_id  = @unclassified;
        update referral_routing_rules  set activity_category_id  = @mapworksIssue   WHERE activity_category_id  = @unclassified;
        delete from activity_category_lang where activity_category_id = @unclassified;
        delete from activity_category where id  = @unclassified");
        
    }
    
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    
    }}
