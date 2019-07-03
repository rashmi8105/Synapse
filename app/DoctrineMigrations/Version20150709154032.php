<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150709154032 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // person table related changes
//         $person_table_changes_query = <<<CDATA
// # person table related changes
// ALTER TABLE person
// CHANGE COLUMN firstname firstname VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ,
// CHANGE COLUMN lastname lastname VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ,
// CHANGE COLUMN username username VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
// CDATA;
//         $this->addSql($person_table_changes_query);

//         // contact_info table related changes
//         $contact_info_table_changes_query = <<<CDATA
// ALTER TABLE contact_info
// CHANGE COLUMN address_1 address_1 VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ,
// CHANGE COLUMN address_2 address_2 VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ,
// CHANGE COLUMN city city VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ,
// CHANGE COLUMN home_phone home_phone VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ,
// CHANGE COLUMN office_phone office_phone VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ,
// CHANGE COLUMN primary_email primary_email VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ,
// CHANGE COLUMN alternate_email alternate_email VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ;
// CDATA;
//         $this->addSql($contact_info_table_changes_query);

//         // ebi_question_options table related changes
//         $ebi_question_options_table_changes_query = <<<CDATA
// # ebi_question_options table related changes
// ALTER TABLE ebi_question_options
// ADD COLUMN option_rpt VARCHAR(1000) NULL AFTER sequence,
// ADD COLUMN external_id VARCHAR(45) NULL AFTER option_rpt;
// CDATA;
//         $this->addSql($ebi_question_options_table_changes_query);

//         // org_question table related changes
//         $org_question_table_changes_query = <<<CDATA
// # org_question table related changes
// ALTER TABLE org_question
// ADD COLUMN external_id VARCHAR(45) NULL AFTER question_text;
// CDATA;
//         $this->addSql($org_question_table_changes_query);

//         // org_question_options table related changes
//         $org_question_options_table_changes_query = <<<CDATA
// # org_question_options table related changes
// ALTER TABLE org_question_options
// ADD COLUMN external_id VARCHAR(45) NULL AFTER sequence;
// CDATA;
//         $this->addSql($org_question_options_table_changes_query);

//         // survey_questions table related changes
//         $survey_questions_table_changes_query = <<<CDATA
// # survey_questions table related changes
// ALTER TABLE survey_questions
// ADD COLUMN external_id VARCHAR(45) NULL AFTER qnbr;
// CDATA;
//         $this->addSql($survey_questions_table_changes_query);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
