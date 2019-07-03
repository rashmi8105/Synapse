<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150930195038 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

	   $this->addSql('  CREATE TABLE IF NOT EXISTS synapse.survey_branch
                        (
                            id INT PRIMARY KEY AUTO_INCREMENT, 
                            created_by INT, 
                            modified_by INT, 
                            deleted_by INT, 
                            created_at DATETIME, 
                            modified_at DATETIME, 
                            deleted_at DATETIME, 
                            survey_id INT, 
                            survey_question_id INT, 
                            ebi_question_options_id INT, 
                            survey_pages_id INT, 
                            branch_to_survey_pages_id INT,
                            FOREIGN KEY (created_by) REFERENCES synapse.person(id),
                            FOREIGN KEY (modified_by) REFERENCES synapse.person(id), 
                            FOREIGN KEY (deleted_by) REFERENCES synapse.person(id), 
                            FOREIGN KEY (survey_id) REFERENCES synapse.survey(id), 
                            FOREIGN KEY (survey_question_id) REFERENCES synapse.survey_questions(id), 
                            FOREIGN KEY (ebi_question_options_id) REFERENCES synapse.ebi_question_options(id), 
                            FOREIGN KEY (survey_pages_id) REFERENCES synapse.survey_pages(id), 
                            FOREIGN KEY (branch_to_survey_pages_id) REFERENCES synapse.survey_pages(id)
                        ); ');

        $this->addSql(' CREATE TABLE IF NOT EXISTS synapse.survey_branch_lang
                        (
                            id INT PRIMARY KEY AUTO_INCREMENT, 
                            survey_branch_id INT, 
                            lang_id INT, 
                            created_by INT, 
                            modified_by INT, 
                            deleted_by INT, 
                            created_at DATETIME, 
                            modified_at DATETIME, 
                            deleted_at DATETIME, 
                            branch_description VARCHAR(200), 
                            FOREIGN KEY (created_by) REFERENCES synapse.person(id),
                            FOREIGN KEY (modified_by) REFERENCES synapse.person(id), 
                            FOREIGN KEY (deleted_by) REFERENCES synapse.person(id), 
                            FOREIGN KEY (survey_branch_id) REFERENCES synapse.survey_branch(id), 
                            FOREIGN KEY (lang_id) REFERENCES synapse.language_master(id)
                        ); ');

        $this->addSql(' CREATE TABLE IF NOT EXISTS synapse.org_question_branch 
                        (
                            id INT PRIMARY KEY AUTO_INCREMENT, 
                            created_by INT, 
                            modified_by INT, 
                            deleted_by INT, 
                            created_at DATETIME, 
                            modified_at DATETIME, 
                            deleted_at DATETIME, 
                            survey_id INT, 
                            org_question_id INT,
                            survey_question_id INT, 
                            ebi_question_options_id INT, 
                            branch_type VARCHAR(5),
                            FOREIGN KEY (created_by) REFERENCES synapse.person(id),
                            FOREIGN KEY (modified_by) REFERENCES synapse.person(id), 
                            FOREIGN KEY (deleted_by) REFERENCES synapse.person(id), 
                            FOREIGN KEY (survey_id) REFERENCES synapse.survey(id), 
                            FOREIGN KEY (survey_question_id) REFERENCES synapse.survey_questions(id), 
                            FOREIGN KEY (ebi_question_options_id) REFERENCES synapse.ebi_question_options(id), 
                            FOREIGN KEY (org_question_id) REFERENCES synapse.org_question(id)
                        ); ');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('');
        
        
       
    }
}
