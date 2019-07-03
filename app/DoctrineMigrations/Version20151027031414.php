<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151027031414 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("update person set last_contact_date = null");
        $this->addSql("UPDATE synapse.person AS p
        INNER JOIN
    (SELECT 
        person_id_student, MAX(created_at) last_contact_date
    FROM
        contacts
    WHERE
        deleted_at IS NULL
            AND contact_types_id IN (SELECT 
                id
            FROM
                synapse.contact_types
            WHERE
                parent_contact_types_id = 1)
    GROUP BY person_id_student) sc ON p.id = sc.person_id_student 
SET 
    p.last_contact_date = sc.last_contact_date
WHERE
    p.deleted_at IS NULL
            ");
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
