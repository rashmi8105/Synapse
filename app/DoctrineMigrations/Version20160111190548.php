<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160111190548 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='ACTComposite';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='ACTEnglish';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='ACTMath';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='ACTWriting';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='ACTScience';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='ACTReading';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='SATComposite';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='SATEnglish';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='SATMath';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='SATWriting';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='SATCriticalRead';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='COMPASSPreAlgebra';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='COMPASSAlgebra';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='COMPASSCollegeAlgebra';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='COMPASSGeometry';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='COMPASSTrig';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='COMPASSWriting';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='COMPASSReading';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='COMPASSESL';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='ACCUPLACERSentence';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='ACCUPLACERArithmetic';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='ACCUPLACERAlgebra';");

        $this->addSQL("UPDATE `synapse`.`ebi_metadata` SET `no_of_decimals`='0' WHERE `meta_key`='ACCUPLACERReading';");
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
