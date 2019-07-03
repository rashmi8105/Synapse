<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-17514 -  Fix to rectify bad data
 */
class Version20180409061547 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE referrals SET is_reason_routed = 1 WHERE person_id_assigned_to IS NULL AND ( user_key = 'Central Coordinator' OR user_key = '' OR user_key = ' ' OR user_key IS NULL );");

        $this->addSql("UPDATE 
                                referrals 
                            SET 
                                person_id_assigned_to = IF(
                                                            SUBSTRING_INDEX(SUBSTRING_INDEX(trim(user_key), '-', 2), '-', - 1) = '', 
                                                            null, 
                                                            SUBSTRING_INDEX(SUBSTRING_INDEX(trim(user_key), '-', 2), '-', - 1)
                                                        )
                            WHERE 
                                user_key != 'Central Coordinator' 
                                AND user_key IS NOT NULL 
                                AND person_id_assigned_to IS NULL;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
