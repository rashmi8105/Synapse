<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151201165743 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        // 
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-09-01 06:00:00', calculation_end_date='2016-09-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =40;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-09-01 06:00:00', calculation_end_date='2016-09-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =41;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-08-01 06:00:00', calculation_end_date='2016-01-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =42;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-08-01 06:00:00', calculation_end_date='2016-01-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =43;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-09-01 06:00:00', calculation_end_date='2016-01-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =54;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-09-01 06:00:00', calculation_end_date='2016-01-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =55;");

    $this->addSQL("update risk_variable SET calculation_start_date ='2015-09-01 06:00:00', calculation_end_date='2016-09-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =71;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-09-01 06:00:00', calculation_end_date='2016-09-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =72;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-08-01 06:00:00', calculation_end_date='2016-01-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =73;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-08-01 06:00:00', calculation_end_date='2016-01-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =74;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-09-01 06:00:00', calculation_end_date='2016-01-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =85;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-09-01 06:00:00', calculation_end_date='2016-01-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =86;");

    $this->addSQL("update risk_variable SET calculation_start_date ='2015-09-01 06:00:00', calculation_end_date='2016-09-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =123;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-09-01 06:00:00', calculation_end_date='2016-09-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =124;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-09-01 06:00:00', calculation_end_date='2016-09-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =125;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-09-01 06:00:00', calculation_end_date='2016-09-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =127;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-08-01 06:00:00', calculation_end_date='2015-12-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =133;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-09-01 06:00:00', calculation_end_date='2016-09-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =145;");
    $this->addSQL("update risk_variable SET calculation_start_date ='2015-09-01 06:00:00', calculation_end_date='2016-09-01 06:00:00', is_calculated =1, calc_type='Most Recent', modified_at=NOW() WHERE id =146;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
