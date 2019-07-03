<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script to populate  retention completion variables
 */
class Version20170117064330 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your

        $this->addSql("INSERT INTO `retention_completion_variable_name` (`years_from_retention_track`, `type`, `name_text`, `created_by`, `created_at`, `modified_at`) VALUES ('0', 'completion', 'Completed Degree in 1 Year', '-25' , NOW(), NOW())");
        $this->addSql("INSERT INTO `retention_completion_variable_name` (`years_from_retention_track`, `type`, `name_text`, `created_by`, `created_at`, `modified_at`) VALUES ('1', 'completion', 'Completed Degree in 2 Years', '-25', NOW(), NOW())");
        $this->addSql("INSERT INTO `retention_completion_variable_name` (`years_from_retention_track`, `type`, `name_text`, `created_by`, `created_at`, `modified_at`) VALUES ('2', 'completion', 'Completed Degree in 3 Years', '-25', NOW(), NOW())");
        $this->addSql("INSERT INTO `retention_completion_variable_name` (`years_from_retention_track`, `type`, `name_text`, `created_by`, `created_at`, `modified_at`) VALUES ('3', 'completion', 'Completed Degree in 4 Years', '-25', NOW(), NOW())");
        $this->addSql("INSERT INTO `retention_completion_variable_name` (`years_from_retention_track`, `type`, `name_text`, `created_by`, `created_at`, `modified_at`) VALUES ('4', 'completion', 'Completed Degree in 5 Years', '-25', NOW(), NOW())");
        $this->addSql("INSERT INTO `retention_completion_variable_name` (`years_from_retention_track`, `type`, `name_text`, `created_by`, `created_at`, `modified_at`) VALUES ('5', 'completion', 'Completed Degree in 6 Years', '-25', NOW(), NOW())");
        $this->addSql("INSERT INTO `retention_completion_variable_name` (`years_from_retention_track`, `type`, `name_text`, `created_by`, `created_at`, `modified_at`) VALUES ('0', 'enrolledMidYear', 'Retained to Midyear Year 1', '-25', NOW(), NOW())");
        $this->addSql("INSERT INTO `retention_completion_variable_name` (`years_from_retention_track`, `type`, `name_text`, `created_by`, `created_at`, `modified_at`) VALUES ('1', 'enrolledMidYear', 'Retained to Midyear Year 2', '-25', NOW(), NOW())");
        $this->addSql("INSERT INTO `retention_completion_variable_name` (`years_from_retention_track`, `type`, `name_text`, `created_by`, `created_at`, `modified_at`) VALUES ('2', 'enrolledMidYear', 'Retained to Midyear Year 3', '-25', NOW(), NOW())");
        $this->addSql("INSERT INTO `retention_completion_variable_name` (`years_from_retention_track`, `type`, `name_text`, `created_by`, `created_at`, `modified_at`) VALUES ('3', 'enrolledMidYear', 'Retained to Midyear Year 4', '-25', NOW(), NOW())");
        $this->addSql("INSERT INTO `retention_completion_variable_name` (`years_from_retention_track`, `type`, `name_text`, `created_by`, `created_at`, `modified_at`) VALUES ('1', 'enrolledBegYear', 'Retained to Start of Year 2', '-25', NOW(), NOW())");
        $this->addSql("INSERT INTO `retention_completion_variable_name` (`years_from_retention_track`, `type`, `name_text`, `created_by`, `created_at`, `modified_at`) VALUES ('2', 'enrolledBegYear', 'Retained to Start of Year 3', '-25', NOW(), NOW())");
        $this->addSql("INSERT INTO `retention_completion_variable_name` (`years_from_retention_track`, `type`, `name_text`, `created_by`, `created_at`, `modified_at`) VALUES ('3', 'enrolledBegYear', 'Retained to Start of Year 4', '-25', NOW(), NOW())");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
