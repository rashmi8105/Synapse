<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Query;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160414192003 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        /**
         * This query will delete the metadata list values for
         * the PersistMidYear, RetainYear2, & RetainYear3 where
         * the metadata option value is 2. Then it will replace
         * the metadata_value of 2 with 1 in the person_ebi_metadata 
         * table.
         */
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
         
        /* This will remove the value of two form the PersistMidYear, RetainYear2, & RetainYear3 */
        $this->addSql('
            UPDATE `synapse`.`ebi_metadata_list_values`
                      INNER JOIN
                    `synapse`.`ebi_metadata` on `ebi_metadata`.`id` = `ebi_metadata_list_values`.`ebi_metadata_id`

                SET
                    `ebi_metadata_list_values`.`deleted_at` = NOW()
                WHERE
                    `ebi_metadata`.`meta_key` IN (\'PersistMidYear\' , \'RetainYear2\', \'RetainYear3\')
                      	AND `ebi_metadata_list_values`.`list_value` = 2;
        ');

         /* This will change the person's value in the person_ebi_metadata */
        $this->addSql(' 
       
        UPDATE `synapse`.`person_ebi_metadata`
                  INNER JOIN
                `synapse`.`ebi_metadata` on `ebi_metadata`.`id` = `person_ebi_metadata`.`ebi_metadata_id`
           SET
                `person_ebi_metadata`.`metadata_value` = 1,
                `person_ebi_metadata`.`modified_at` = NOW()
            WHERE
                `ebi_metadata`.`meta_key` IN (\'PersistMidYear\' , \'RetainYear2\', \'RetainYear3\')
                      AND `person_ebi_metadata`.`metadata_value` = 2;
         ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
