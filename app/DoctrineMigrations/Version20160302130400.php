<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Remove indices on deleted_at only field at six tables
 */
class Version20160302130400 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $this->addSql('ALTER TABLE org_person_faculty DROP INDEX deleted_at_idx;');
        $this->addSql('ALTER TABLE org_person_student DROP INDEX deleted_at_idx;');
        $this->addSql('ALTER TABLE org_group_faculty DROP INDEX deleted_at_idx;');
        $this->addSql('ALTER TABLE org_group_students DROP INDEX deleted_at_idx;');
        $this->addSql('ALTER TABLE org_course_faculty DROP INDEX deleted_at_idx;');
        $this->addSql('ALTER TABLE org_course_student DROP INDEX deleted_at_idx;');
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
