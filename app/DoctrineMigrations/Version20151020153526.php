<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151020153526 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('ALTER TABLE `synapse`.`survey_response` 
		DROP INDEX `fk_survey_response_survey_questions1_idx` ,
		ADD INDEX `fk_survey_response_survey_questions1_idx` (`survey_questions_id` ASC, `person_id` ASC);');
        $this->addSQL('ALTER TABLE `synapse`.`person_factor_calculated` 
		DROP INDEX `org_person_factor_uniq_idx` ,
		ADD UNIQUE INDEX `org_person_factor_uniq_idx` (`organization_id` ASC, `person_id` ASC,`survey_id` ASC,`factor_id` ASC, `modified_at` ASC );');

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
