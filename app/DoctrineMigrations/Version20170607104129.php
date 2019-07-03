<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15122 - Updating the subject for the  email sent to the interested part  during bulk action
 */
class Version20170607104129 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("SET @emailTemplateId := (SELECT id  FROM email_template WHERE email_key = 'referral_bulk_action_interested_party')");
        $this->addSql("UPDATE email_template_lang SET subject = 'Interested party for a Mapworks referral' WHERE email_template_id = @emailTemplateId");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
