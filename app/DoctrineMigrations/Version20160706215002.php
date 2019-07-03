<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-10971
 *
 * Using Safe Index Builder to Apply this index
 * ALTER TABLE `synapse`.`org_calc_flags_student_reports`
 * ADD UNIQUE INDEX `unique_idx` (`org_id` ASC, `person_id` ASC, `survey_id` ASC);
 */
class Version20160706215002 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        //Adding Index to Table, second boolean means its UNIQUE
        $this->addSQL("SET @myquery = safe_index_builder('org_calc_flags_student_reports', 'unique_idx', '(`org_id` ASC, `person_id` ASC, `survey_id` ASC)', true, true, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
