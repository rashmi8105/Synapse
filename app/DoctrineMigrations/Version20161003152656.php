<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-11723 - Changing the order of intent_to_leave values in the DB, and reassigning associated ID values in the person table.
 */
class Version20161003152656 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //Disable the event that triggers intent to leave calculation
        $this->addSql("ALTER EVENT Survey_Risk_Event DISABLE;");

        //Update the metadata table references to green and yellow
        $this->addSql("UPDATE synapse.intent_to_leave SET text = 'yellow', image_name = 'leave-intent-leave-implied.png', color_hex = '#fec82a', min_value = 4.0000, max_value = 5.0000 WHERE id = 2;");
        $this->addSql("UPDATE synapse.intent_to_leave SET text = 'green', image_name = 'leave-intent-stay-stated.png', color_hex = '#95cd3c', min_value = 6.0000, max_value = 7.0000 WHERE id = 3;");

        //Create an archive table for the records that were changed
        $this->addSql("DROP TABLE IF EXISTS archive_synapse.person_ESPRJ_11723;");

        $this->addSql("CREATE TABLE archive_synapse.person_ESPRJ_11723 LIKE synapse.person; ");

        $this->addSql("INSERT INTO archive_synapse.person_ESPRJ_11723
                        SELECT DISTINCT
                            p.*
                        FROM
                            synapse.person p
                                JOIN
                            synapse.org_person_student ops ON p.organization_id = ops.organization_id
                                AND p.id = ops.person_id
                        WHERE
                            p.intent_to_leave IN (2 , 3);");

        //Update the person table records to match what the new ID value is for their intent_to_leave
        $this->addSql("
                        UPDATE
                            synapse.person p
                                JOIN
                            archive_synapse.person_ESPRJ_11723 ap ON p.organization_id = ap.organization_id AND p.id = ap.id
                        SET
                            p.intent_to_leave = 3
                        WHERE
                            ap.intent_to_leave = 2;");

        $this->addSql("
                        UPDATE
                            synapse.person p
                                JOIN
                            archive_synapse.person_ESPRJ_11723 ap ON p.organization_id = ap.organization_id AND p.id = ap.id
                        SET
                            p.intent_to_leave = 2
                        WHERE
                            ap.intent_to_leave = 3;");

        //Enable the event that triggers intent to leave calculation
        $this->addSql("ALTER EVENT Survey_Risk_Event ENABLE;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
