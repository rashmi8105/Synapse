<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-11370
 *
 * After further discussion with Annette, it appears we are going to need to
 * update all risk model levels and continuous risk variable buckets so they
 * conform to changes in ESPRJ-6109.   This is eliminating the gaps between
 * risk variable buckets if they exist and gaps between risk indicators
 *
 * Risk Variables and Risk Models are not allowed to be updated normally
 * through WebApp Admin, hence the migration script
 *
 * This list was provided by Annette and updated by Josh Stark
 */
class Version20160727143708 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        //UPDATING RISK VARIABLES

        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2011.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='35';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2013.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='35';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2016.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='35';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='331.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='36';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='431.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='36';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='501.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='36';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='550.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='36';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='616.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='36';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='706.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='36';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='801.0000' WHERE `bucket_value`='7' AND `risk_variable_id`='36';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='341.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='37';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='445.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='37';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='511.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='37';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='560.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='37';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='626.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='37';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='725.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='37';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='801.0000' WHERE `bucket_value`='7' AND `risk_variable_id`='37';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='15.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='38';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='19.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='38';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='23.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='38';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='25.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='38';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='28.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='38';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='33.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='38';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='37.0000' WHERE `bucket_value`='7' AND `risk_variable_id`='38';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.2000' WHERE `bucket_value`='1' AND `risk_variable_id`='39';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.7000' WHERE `bucket_value`='2' AND `risk_variable_id`='39';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='39';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.4000' WHERE `bucket_value`='4' AND `risk_variable_id`='39';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.7000' WHERE `bucket_value`='5' AND `risk_variable_id`='39';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='39';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='7.0000' WHERE `bucket_value`='7' AND `risk_variable_id`='39';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='10.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='40';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='12.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='40';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='15.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='40';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='30.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='40';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='5.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='41';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='30.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='41';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='64';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='31.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='64';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='65';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2011.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='66';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2013.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='66';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2016.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='66';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='331.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='67';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='431.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='67';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='501.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='67';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='550.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='67';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='616.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='67';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='706.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='67';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='801.0000' WHERE `bucket_value`='7' AND `risk_variable_id`='67';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='341.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='68';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='445.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='68';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='511.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='68';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='560.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='68';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='626.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='68';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='725.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='68';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='801.0000' WHERE `bucket_value`='7' AND `risk_variable_id`='68';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='15.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='69';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='19.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='69';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='23.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='69';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='25.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='69';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='28.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='69';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='33.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='69';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='37.0000' WHERE `bucket_value`='7' AND `risk_variable_id`='69';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.2000' WHERE `bucket_value`='1' AND `risk_variable_id`='70';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.7000' WHERE `bucket_value`='2' AND `risk_variable_id`='70';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='70';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.4000' WHERE `bucket_value`='4' AND `risk_variable_id`='70';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.7000' WHERE `bucket_value`='5' AND `risk_variable_id`='70';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='70';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='6.1000' WHERE `bucket_value`='7' AND `risk_variable_id`='70';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='12.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='71';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='15.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='71';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='30.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='71';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='5.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='72';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='30.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='72';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='87';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='31.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='87';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='88';");

        // risk_variable_ids 97 through 122
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='1' AND  `risk_variable_id` BETWEEN '97' AND '122';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='2' AND  `risk_variable_id` BETWEEN '97' AND '122';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4.0000' WHERE `bucket_value`='3' AND  `risk_variable_id` BETWEEN '97' AND '122';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='5.0000' WHERE `bucket_value`='4' AND  `risk_variable_id` BETWEEN '97' AND '122';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='6.0000' WHERE `bucket_value`='5' AND  `risk_variable_id` BETWEEN '97' AND '122';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='7.0000' WHERE `bucket_value`='6' AND  `risk_variable_id` BETWEEN '97' AND '122';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `min`='7.0000', `max`='7.1000' WHERE `bucket_value`='7' AND  `risk_variable_id` BETWEEN '97' AND '122';");

        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='12.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='123';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='15.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='123';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='60.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='123';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='124';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='6.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='124';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='11.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='124';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='125';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.5000' WHERE `bucket_value`='3' AND `risk_variable_id`='125';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='125';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.5000' WHERE `bucket_value`='5' AND `risk_variable_id`='125';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='6.1000' WHERE `bucket_value`='6' AND `risk_variable_id`='125';");

        //factor again, only one needed here
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='126';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='126';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='126';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='5.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='126';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='6.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='126';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='7.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='126';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `min`='7.0000', `max`='7.1000' WHERE `bucket_value`='7' AND `risk_variable_id`='126';");


        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='21.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='128';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='26.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='128';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='40.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='128';");

        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='1.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='133';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4500.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='133';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='999999.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='133';");

        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='134';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='7.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='134';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='10.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='134';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='101.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='134';");

        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='135';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='11.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='135';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='101.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='135';");

        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='16.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='139';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='21.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='139';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='23.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='139';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='37.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='139';");

        //factor do for risk variables 140 thru 144
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='1' AND `risk_variable_id` BETWEEN '140' AND '144';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='2' AND `risk_variable_id` BETWEEN '140' AND '144';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4.0000' WHERE `bucket_value`='3' AND `risk_variable_id` BETWEEN '140' AND '144';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='5.0000' WHERE `bucket_value`='4' AND `risk_variable_id` BETWEEN '140' AND '144';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='6.0000' WHERE `bucket_value`='5' AND `risk_variable_id` BETWEEN '140' AND '144';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='7.0000' WHERE `bucket_value`='6' AND `risk_variable_id` BETWEEN '140' AND '144';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `min`='7.0000', `max`='7.1000' WHERE `bucket_value`='7' AND `risk_variable_id` BETWEEN '140' AND '144';");

        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='12.0000' WHERE `bucket_value`='4' AND `risk_variable_id`='145';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='15.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='145';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='60.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='145';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='146';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.5000' WHERE `bucket_value`='4' AND `risk_variable_id`='146';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='5' AND `risk_variable_id`='146';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='6.1000' WHERE `bucket_value`='6' AND `risk_variable_id`='146';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='149';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='6.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='149';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='9.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='149';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='150';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='5.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='152';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='10.1000' WHERE `bucket_value`='2' AND `risk_variable_id`='152';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='153';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='5.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='155';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='10.1000' WHERE `bucket_value`='2' AND `risk_variable_id`='155';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='156';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='5.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='158';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='10.1000' WHERE `bucket_value`='2' AND `risk_variable_id`='158';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='159';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='160';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='5.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='161';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='10.1000' WHERE `bucket_value`='2' AND `risk_variable_id`='161';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='162';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='163';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='5.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='164';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='10.1000' WHERE `bucket_value`='2' AND `risk_variable_id`='164';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='165';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='166';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.5000' WHERE `bucket_value`='5' AND `risk_variable_id`='167';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='168';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.5000' WHERE `bucket_value`='5' AND `risk_variable_id`='170';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='171';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.5999' WHERE `bucket_value`='5' AND `risk_variable_id`='172';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.5999' WHERE `bucket_value`='5' AND `risk_variable_id`='181';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.5999' WHERE `bucket_value`='5' AND `risk_variable_id`='178';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.5999' WHERE `bucket_value`='5' AND `risk_variable_id`='175';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='184';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.2000' WHERE `bucket_value`='4' AND `risk_variable_id`='184';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.5000' WHERE `bucket_value`='5' AND `risk_variable_id`='184';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='184';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='187';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.2000' WHERE `bucket_value`='4' AND `risk_variable_id`='187';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.5000' WHERE `bucket_value`='5' AND `risk_variable_id`='187';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='187';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4.1000' WHERE `bucket_value`='7' AND `risk_variable_id`='187';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='190';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.2000' WHERE `bucket_value`='4' AND `risk_variable_id`='190';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.5000' WHERE `bucket_value`='5' AND `risk_variable_id`='190';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='190';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4.1000' WHERE `bucket_value`='7' AND `risk_variable_id`='190';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='191';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.2000' WHERE `bucket_value`='4' AND `risk_variable_id`='191';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.5000' WHERE `bucket_value`='5' AND `risk_variable_id`='191';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='191';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4.1000' WHERE `bucket_value`='7' AND `risk_variable_id`='191';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='192';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='31.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='192';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='194';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.2000' WHERE `bucket_value`='4' AND `risk_variable_id`='194';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.5000' WHERE `bucket_value`='5' AND `risk_variable_id`='194';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='194';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='195';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='31.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='195';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='197';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.2000' WHERE `bucket_value`='4' AND `risk_variable_id`='197';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.5000' WHERE `bucket_value`='5' AND `risk_variable_id`='197';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='197';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='198';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='31.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='198';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='200';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4.1000' WHERE `bucket_value`='7' AND `risk_variable_id`='200';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='201';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='31.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='201';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='203';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4.1000' WHERE `bucket_value`='7' AND `risk_variable_id`='203';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='205';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4.1000' WHERE `bucket_value`='7' AND `risk_variable_id`='206';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='207';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4.1000' WHERE `bucket_value`='7' AND `risk_variable_id`='207';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='208';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4.1000' WHERE `bucket_value`='7' AND `risk_variable_id`='208';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='209';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='6.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='209';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='9.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='209';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='37.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='209';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='210';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='6.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='210';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='9.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='210';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='37.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='210';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='211';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='6.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='211';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='9.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='211';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='37.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='211';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='212';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='6.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='212';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='9.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='212';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='37.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='212';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='1' AND `risk_variable_id`='213';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='6.0000' WHERE `bucket_value`='2' AND `risk_variable_id`='213';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='9.0000' WHERE `bucket_value`='3' AND `risk_variable_id`='213';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='37.0000' WHERE `bucket_value`='6' AND `risk_variable_id`='213';");

        //factors risk_variable_id 214 thru 236
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='2.0000' WHERE `bucket_value`='1' AND `risk_variable_id` BETWEEN '214' AND '236';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='3.0000' WHERE `bucket_value`='2' AND `risk_variable_id` BETWEEN '214' AND '236';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='4.0000' WHERE `bucket_value`='3' AND `risk_variable_id` BETWEEN '214' AND '236';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='5.0000' WHERE `bucket_value`='4' AND `risk_variable_id` BETWEEN '214' AND '236';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='6.0000' WHERE `bucket_value`='5' AND `risk_variable_id` BETWEEN '214' AND '236';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `max`='7.0000' WHERE `bucket_value`='6' AND `risk_variable_id` BETWEEN '214' AND '236';");
        $this->addSql(" UPDATE `synapse`.`risk_variable_range` SET `min`='7.0000', `max`='7.1000' WHERE `bucket_value`='7' AND `risk_variable_id` BETWEEN '214' AND '236';");

        //UPDATING RISK MODEL LEVELS

        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.9999' WHERE `risk_model_id`='1' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.5000' WHERE `risk_model_id`='1' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.0000' WHERE `risk_model_id`='1' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='1' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.5000' WHERE `risk_model_id`='2' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.2000' WHERE `risk_model_id`='2' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.9000' WHERE `risk_model_id`='2' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='2' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.8000' WHERE `risk_model_id`='3' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4000' WHERE `risk_model_id`='3' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.9000' WHERE `risk_model_id`='3' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='3' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='4' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.6000' WHERE `risk_model_id`='4' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.1000' WHERE `risk_model_id`='4' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='4' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.6000' WHERE `risk_model_id`='5' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.2000' WHERE `risk_model_id`='5' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.6000' WHERE `risk_model_id`='5' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='5' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.8000' WHERE `risk_model_id`='6' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4000' WHERE `risk_model_id`='6' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.8000' WHERE `risk_model_id`='6' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='6' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='7' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4000' WHERE `risk_model_id`='7' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.7000' WHERE `risk_model_id`='7' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='7' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.9000' WHERE `risk_model_id`='8' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.5000' WHERE `risk_model_id`='8' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.0000' WHERE `risk_model_id`='8' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='8' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.5000' WHERE `risk_model_id`='9' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.2000' WHERE `risk_model_id`='9' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.9000' WHERE `risk_model_id`='9' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='9' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.0000' WHERE `risk_model_id`='10' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.5000' WHERE `risk_model_id`='10' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.0000' WHERE `risk_model_id`='10' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='10' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='2.9000' WHERE `risk_model_id`='11' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.8000' WHERE `risk_model_id`='11' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.6000' WHERE `risk_model_id`='11' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='11' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.2000' WHERE `risk_model_id`='12' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.7000' WHERE `risk_model_id`='12' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4000' WHERE `risk_model_id`='12' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.2000' WHERE `risk_model_id`='13' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.7000' WHERE `risk_model_id`='13' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.1000' WHERE `risk_model_id`='13' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='13' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.7000' WHERE `risk_model_id`='14' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4000' WHERE `risk_model_id`='14' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.9000' WHERE `risk_model_id`='14' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='14' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.8000' WHERE `risk_model_id`='15' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.7000' WHERE `risk_model_id`='15' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.1000' WHERE `risk_model_id`='15' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='15' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='16' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.8000' WHERE `risk_model_id`='16' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.1000' WHERE `risk_model_id`='16' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='16' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.8000' WHERE `risk_model_id`='17' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.3000' WHERE `risk_model_id`='17' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.0000' WHERE `risk_model_id`='17' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='17' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.9000' WHERE `risk_model_id`='18' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.5000' WHERE `risk_model_id`='18' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.0000' WHERE `risk_model_id`='18' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='18' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.3000' WHERE `risk_model_id`='19' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.6000' WHERE `risk_model_id`='19' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.2000' WHERE `risk_model_id`='19' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='19' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.0000' WHERE `risk_model_id`='20' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4000' WHERE `risk_model_id`='20' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.9000' WHERE `risk_model_id`='20' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='20' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.8000' WHERE `risk_model_id`='21' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4000' WHERE `risk_model_id`='21' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.9000' WHERE `risk_model_id`='21' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='21' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='22' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.6000' WHERE `risk_model_id`='22' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.1000' WHERE `risk_model_id`='22' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='22' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='23' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4000' WHERE `risk_model_id`='23' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.9000' WHERE `risk_model_id`='23' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='23' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4000' WHERE `risk_model_id`='24' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.8000' WHERE `risk_model_id`='24' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.2000' WHERE `risk_model_id`='24' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='24' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.3500' WHERE `risk_model_id`='25' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.7250' WHERE `risk_model_id`='25' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.2800' WHERE `risk_model_id`='25' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='25' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.6000' WHERE `risk_model_id`='26' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='26' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4500' WHERE `risk_model_id`='26' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='26' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.5000' WHERE `risk_model_id`='27' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='27' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.6000' WHERE `risk_model_id`='27' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='27' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='2.6000' WHERE `risk_model_id`='28' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.0000' WHERE `risk_model_id`='28' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.9000' WHERE `risk_model_id`='28' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='28' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='1.6000' WHERE `risk_model_id`='29' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.1000' WHERE `risk_model_id`='29' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.0000' WHERE `risk_model_id`='29' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='29' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.0000' WHERE `risk_model_id`='30' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.5000' WHERE `risk_model_id`='30' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='30' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='30' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.0000' WHERE `risk_model_id`='31' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.6000' WHERE `risk_model_id`='31' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.1000' WHERE `risk_model_id`='31' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.8000' WHERE `risk_model_id`='32' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='32' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4000' WHERE `risk_model_id`='32' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='32' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.1000' WHERE `risk_model_id`='33' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='33' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.7000' WHERE `risk_model_id`='33' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='33' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.2000' WHERE `risk_model_id`='34' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.6000' WHERE `risk_model_id`='34' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.8000' WHERE `risk_model_id`='34' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='34' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.3000' WHERE `risk_model_id`='35' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.3000' WHERE `risk_model_id`='35' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.1000' WHERE `risk_model_id`='35' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='35' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.6000' WHERE `risk_model_id`='36' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='36' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4000' WHERE `risk_model_id`='36' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='36' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.6000' WHERE `risk_model_id`='37' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.3000' WHERE `risk_model_id`='37' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.8000' WHERE `risk_model_id`='37' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='37' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.6000' WHERE `risk_model_id`='38' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='38' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4000' WHERE `risk_model_id`='38' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='38' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.7000' WHERE `risk_model_id`='39' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4000' WHERE `risk_model_id`='39' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.8000' WHERE `risk_model_id`='39' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='39' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='2.3000' WHERE `risk_model_id`='40' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.9692' WHERE `risk_model_id`='40' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.6560' WHERE `risk_model_id`='40' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='40' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='41' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.5000' WHERE `risk_model_id`='41' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.9000' WHERE `risk_model_id`='41' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='41' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.8000' WHERE `risk_model_id`='42' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.2000' WHERE `risk_model_id`='42' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.7500' WHERE `risk_model_id`='42' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='42' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.3000' WHERE `risk_model_id`='43' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.3200' WHERE `risk_model_id`='43' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.8100' WHERE `risk_model_id`='43' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='43' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.8200' WHERE `risk_model_id`='44' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.5000' WHERE `risk_model_id`='44' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.1000' WHERE `risk_model_id`='44' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='44' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.8200' WHERE `risk_model_id`='45' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.5000' WHERE `risk_model_id`='45' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='5.1000' WHERE `risk_model_id`='45' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='45' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.6000' WHERE `risk_model_id`='46' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.2000' WHERE `risk_model_id`='46' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.6000' WHERE `risk_model_id`='46' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='46' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.6700' WHERE `risk_model_id`='47' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.7000' WHERE `risk_model_id`='47' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `min`='5.0000', `max`='7.1000' WHERE `risk_model_id`='47' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.8000' WHERE `risk_model_id`='48' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='48' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.4000' WHERE `risk_model_id`='48' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='7.1000' WHERE `risk_model_id`='48' AND`risk_level`='4';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='1.0600' WHERE `risk_model_id`='49' AND`risk_level`='1';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='2.0600' WHERE `risk_model_id`='49' AND`risk_level`='2';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='3.0600' WHERE `risk_model_id`='49' AND`risk_level`='3';");
        $this->addSql(" UPDATE `synapse`.`risk_model_levels` SET `max`='4.1000' WHERE `risk_model_id`='49' AND`risk_level`='4';");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
