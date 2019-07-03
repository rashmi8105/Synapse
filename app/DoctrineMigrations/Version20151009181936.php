<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151009181936 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
	
	$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    //Commenting script due to migration script fail
        /*
        $this->addSql('CREATE TABLE IF NOT EXISTS `etldata`.`last_response_update`
                        (
                            id INT PRIMARY KEY AUTO_INCREMENT,
                            last_update_person_id INT,
                            last_update_ts DATETIME
                        );');

        $this->addSql('INSERT INTO etldata.last_response_update (last_update_ts) VALUES (\'2015-10-08 00:00:00\');');

    	$this->addSql('DROP PROCEDURE IF EXISTS \`survey_data_transfer\` ');
        $this->addSql('DROP EVENT IF EXISTS \'trigger_survey_data_transfer\'');

        $this->addSql('CREATE DEFINER=`synapsemaster`@`%` PROCEDURE survey_data_transfer() 
                        BEGIN 
                        INSERT IGNORE INTO synapse.survey_response 
                        (SELECT * FROM etldata.survey_response WHERE modified_at > (SELECT MAX(modified_at) FROM synapse.survey_response));

                        UPDATE synapse.survey_response sr
                        LEFT OUTER JOIN
                        etldata.survey_response ers ON 
                        (ers.person_id , ers.org_id, ers.survey_id, ers.survey_questions_id) = 
                        (sr.person_id , sr.org_id, sr.survey_id, sr.survey_questions_id) 
                        SET 
                            sr.decimal_value = ers.decimal_value,
                            sr.char_value = ers.char_value,
                            sr.charmax_value = ers.charmax_value, 
                            sr.modified_at = NOW()
                        WHERE
                            (sr.decimal_value <> ers.decimal_value
                                OR sr.char_value <> ers.char_value
                                OR sr.charmax_value <> ers.charmax_value)
                                AND sr.modified_at <= ers.modified_at
                                AND ers.modified_at > (SELECT MAX(last_update_ts) FROM etldata.last_response_update LIMIT 1);


                        UPDATE etldata.last_response_update SET last_update_ts = NOW();
                        END
                        ');
        $this->addSql('CREATE DEFINER=`synapsemaster`@`%` EVENT `trigger_survey_data_transfer` ON SCHEDULE EVERY 2 MINUTE STARTS \'2015-09-14 22:00:00\' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN CALL survey_data_transfer(); END');
        */
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
