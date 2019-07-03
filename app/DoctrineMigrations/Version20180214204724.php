<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-16577 Fixing racetime condition in risk
 */
class Version20180214204724 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $this->addSQL("DROP PROCEDURE IF EXISTS `Academic_Update_Grade_Fixer`;");
        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Academic_Update_Grade_Fixer`()
                        BEGIN
                            UPDATE academic_update SET grade = 'F', modified_by = -25 WHERE grade = 'F/No Pass';
                            UPDATE academic_update SET grade = 'P', modified_by = -25 WHERE grade = 'Pass';
                        END");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
