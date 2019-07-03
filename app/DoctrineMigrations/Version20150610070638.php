<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150610070638 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        /*
         * ESPRJ- 3070
        */
        $query = <<<CDATA
update email_template_lang as etl 
join email_template as et
on etl.email_template_id=et.id and email_key='Academic_Update_Cancel_to_Faculty'
set etl.subject = 'Cancelled:';
CDATA;
        $this->addSql($query);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $query = <<<CDATA
update email_template_lang as etl
join email_template as et
on etl.email_template_id=et.id and email_key='Academic_Update_Cancel_to_Faculty'
set etl.subject = 'Reminder:';
CDATA;
        $this->addSql($query);
    }
}
