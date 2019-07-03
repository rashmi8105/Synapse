<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150617102207 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO `contact_info` (`address_1`,`address_2`,`city`,`zip`,`state`,`country`,`primary_mobile`,`alternate_mobile`,`home_phone`,`office_phone`,`primary_email`,`alternate_email`,`primary_mobile_provider`,`alternate_mobile_provider`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'9591900663',NULL,NULL,NULL,'david.warner@gmail.com',NULL,'9224852114',NULL,NULL,NULL,NULL,'2014-10-15 12:34:01',NULL,NULL);
                        SET @contactId := (select max(id) from contact_info);
                            SET @personId := (select id from person where username = 'david.warner@gmail.com');
                                INSERT INTO `person_contact_info` (`person_id`,`contact_id`,`status`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES (@personId,@contactId,'A',NULL,NULL,NULL,NULL,NULL,NULL);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
    }
}
