<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151020210246 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
	$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
	
	$this->addSql('DROP PROCEDURE IF EXISTS `IssueCalcTempTables`;');

	$this->addSql('CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `IssueCalcTempTables`()
BEGIN
DROP TABLE IF EXISTS issues_temp_calc_perm;
CREATE TABLE `issues_temp_calc_perm` (
`org_id` int(11) DEFAULT NULL,
`staff_id` int(11) DEFAULT NULL,
`student_id` int(11) DEFAULT NULL,
INDEX `org_staff_student` (`org_id` ASC, `staff_id` ASC, `student_id` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS issues_temp_calc_num;
CREATE TABLE `issues_temp_calc_num` (
`org_id` int(11) DEFAULT NULL,
`staff_id` int(11) DEFAULT NULL,
`survey_id` int(11) DEFAULT NULL,
`issue_id` int(11) DEFAULT NULL,
`cohort_code` int(11) DEFAULT NULL,
`student_id` int(11) DEFAULT NULL,
`survey_questions_id` int(11) DEFAULT NULL,
`factor_id` int(11) DEFAULT NULL,
`response_type` varchar(50) DEFAULT NULL,
`decimal_value` decimal(10,4) DEFAULT NULL,
`char_value` int(11) DEFAULT NULL,
`charmax_value` int(11) DEFAULT NULL,
INDEX `org_staff_student_survey_factor` (`org_id` ASC, `staff_id` ASC, `student_id` ASC, `survey_id` ASC, `factor_id` ASC),
INDEX `org_staff_student_survey_issue` (`org_id` ASC, `staff_id` ASC, `student_id` ASC, `survey_id` ASC, `issue_id` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS issues_temp_calc_den;
CREATE TABLE `issues_temp_calc_den` (
`org_id` int(11) DEFAULT NULL,
`issue_id` int(11) DEFAULT NULL,
`count_students` int(11) DEFAULT NULL,
`staff_id` int(11) DEFAULT NULL, 
INDEX  `org_staff_issue` (`org_id` ASC, `staff_id` ASC, `issue_id` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS issues_temp_calc_done;
CREATE TABLE `issues_temp_calc_done` (
`org_id` int(11) DEFAULT NULL,
`staff_id` int(11) DEFAULT NULL,
INDEX `org_staff` (`org_id` ASC, `staff_id` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
END');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
