<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15410  -  migration script to move the push notification set up data to ebi_config table
 */
class Version20170710115950 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PUSH_NOTIFICATION_FIREBASE_API_KEY', 'AIzaSyAipYMx0q-bPnBh8T0D3fpN96sJDWUnQ4c')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PUSH_NOTIFICATION_AUTH_DOMAIN', 'skyfactor-push-notification.firebaseapp.com')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PUSH_NOTIFICATION_DATABASE_URL', 'https://skyfactor-push-notification.firebaseio.com')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PUSH_NOTIFICATION_PROJECT_ID', 'skyfactor-push-notification')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PUSH_NOTIFICATION_STORAGE_BUCKET', 'skyfactor-push-notification.appspot.com')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PUSH_NOTIFICATION_MESSAGING_SENDER_ID', '466332768821')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PUSH_NOTIFICATION_APPLICATION_KEY', 'CO6vUS')");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
