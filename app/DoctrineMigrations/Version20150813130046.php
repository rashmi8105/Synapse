<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150813130046 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $query = <<<CDATA
ALTER TABLE `synapse`.`survey_questions` 
ADD COLUMN `org_question_id` INT NULL after ebi_question_id,
  ADD CONSTRAINT 
    FOREIGN KEY (`org_question_id`)
    REFERENCES `org_question` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION;
CREATE INDEX `fk_survey_questions_org_question1_idx` ON `survey_questions` (`org_question_id` ASC);
ALTER TABLE survey_questions MODIFY type ENUM(\'bank\',\'isq\');
CDATA;
        
        $this->addSql($query);
        
        $query1 = <<<CDATA
ALTER TABLE `synapse`.`survey_questions` 
DROP FOREIGN KEY `FK_2F8A16F851DCB924`;
ALTER TABLE `synapse`.`survey_questions` 
DROP COLUMN `ind_question_id`,
DROP INDEX `fk_survey_questions_independent_question1_idx`;
CDATA;
        $this->addSql($query1);
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
