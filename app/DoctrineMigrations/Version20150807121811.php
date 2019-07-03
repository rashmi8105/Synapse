<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150807121811 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `synapse`.`org_permissionset`
ADD INDEX `accesslevel_ind_agg_idx` (`accesslevel_ind_agg` ASC);

ALTER TABLE `synapse`.`org_permissionset`
ADD INDEX `risk_indicator_idx` (`risk_indicator` ASC);

ALTER TABLE `synapse`.`org_permissionset`
ADD INDEX `intent_to_leave_idx` (`intent_to_leave` ASC);

ALTER TABLE `synapse`.`org_group_students`
ADD INDEX `deleted_at_idx` (`deleted_at` ASC);
            
ALTER TABLE `synapse`.`org_group_faculty`
ADD INDEX `deleted_at_idx` (`deleted_at` ASC);
            
ALTER TABLE `synapse`.`org_course_faculty`
ADD INDEX `deleted_at_idx` (`deleted_at` ASC);
            
ALTER TABLE `synapse`.`org_course_student`
ADD INDEX `deleted_at_idx` (`deleted_at` ASC);
            ');
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
