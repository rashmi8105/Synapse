<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151021182931 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $calculation_query = <<<CDATA
DROP PROCEDURE IF EXISTS `IssueCalcTempTables`;
            
CREATE TABLE IF NOT EXISTS `issues_calculation_input` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`org_id` int(11) DEFAULT NULL,
`student_id` int(11) DEFAULT NULL,
`created_at` datetime DEFAULT NULL,
`calculated_at` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
INDEX `issues_input_org_id` (`org_id`),
INDEX `issues_input_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
CREATE TABLE IF NOT EXISTS `issues_student_staff_mapping` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`issues_input_id` int(11) NOT NULL,
`org_id` int(11) DEFAULT NULL,
`staff_id` int(11) DEFAULT NULL,
`calculated_at` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
INDEX `issues_student_staff_mapping_org_id` (`org_id`),
INDEX `issues_student_staff_mapping_staff_id` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                        
CREATE PROCEDURE `IssueCalcTempTables`()
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
END;
CDATA;
        $this->addSql($calculation_query);
        
        $calculation_query = <<<CDATA
DROP PROCEDURE IF EXISTS `IssueCalcStudentStaffMapping`;
            
CREATE PROCEDURE `IssueCalcStudentStaffMapping`(in orgId int(11),in studentId int(11),in issuesInputId int(11))
BEGIN
Insert into issues_student_staff_mapping (issues_input_id,org_id,staff_id,calculated_at)
(
select 
	issuesInputId as issues_input_id,
    merged.organization_id,
    merged.person_id as staff_id,
	now()
FROM
    (SELECT 
        F.organization_id,
            F.person_id,
			F.org_permissionset_id AS permissionset_id
	FROM
        org_group_students AS S
    INNER JOIN org_group_faculty AS F ON F.org_group_id = S.org_group_id
        and F.deleted_at is null
    WHERE
        S.deleted_at is null
            AND S.person_id = studentId
    UNION ALL 
    SELECT 
        F.organization_id,
            F.person_id,
			F.org_permissionset_id AS permissionset_id
    FROM
        org_course_student AS S
    INNER JOIN org_courses AS C ON C.id = S.org_courses_id
        AND C.deleted_at is null
    INNER JOIN org_course_faculty AS F ON F.org_courses_id = S.org_courses_id
        AND F.deleted_at is null
    INNER JOIN org_academic_terms AS OAT ON OAT.id = C.org_academic_terms_id
        AND OAT.end_date >= now()
        AND OAT.deleted_at is null
    WHERE
        S.deleted_at is null
            AND S.person_id = studentId
	) AS merged
        INNER JOIN
    person AS P ON P.id = merged.person_id
        AND P.deleted_at IS NULL
        AND P.organization_id = orgId
        INNER JOIN
    org_permissionset OPS ON merged.permissionset_id = OPS.id
);
END;
CDATA;
        $this->addSql($calculation_query);
        
        $calculation_query = <<<CDATA
DROP PROCEDURE IF EXISTS `IssueCalculation`;
CREATE PROCEDURE `IssueCalculation`()
BEGIN
        
#INSERT INTO messages (message,created_at) values('21. --> IssueCalcTempTables',now()) ;
call IssueCalcTempTables();
        
SELECT student_id into @studentId FROM issues_calculation_input where calculated_at is NULL limit 1;
SELECT org_id into @orgId FROM issues_calculation_input where student_id = @studentId and calculated_at is NULL limit 1;
SELECT id into @issuesInputId FROM issues_calculation_input where org_id = @orgId and student_id = @studentId and calculated_at is NULL limit 1;
        
#INSERT INTO messages (message,created_at) values(concat('22. --> calc start for student_id - ', convert( @studentId,char(10)),' and Org id - ', convert( @orgId,char(10))),now()) ;
        
WHILE (@student_id IS NOT NULL) dO
#INSERT INTO messages (message,created_at) values('23. --> IssueCalcStudentStaffMapping',now()) ;
call IssueCalcStudentStaffMapping(@orgId,@studentId,@issuesInputId);
        
SELECT staff_id into @staffId FROM issues_student_staff_mapping where org_id = @orgId and issues_input_id = @issuesInputId and calculated_at is NULL
and staff_id not in (select staff_id from issues_temp_calc_done) limit 1;
        
#INSERT INTO messages (message,created_at) values(concat('24. --> calc start for staff id - ', convert( @staffId,char(10)),' and Org id - ', convert( @orgId,char(10))),now()) ;
        
WHILE (@staffId IS NOT NULL) dO
#INSERT INTO messages (message,created_at) values('25. --> IssueCalcPermissions',now()) ;
call IssueCalcPermissions(@orgId,@staffId);
#INSERT INTO messages (message,created_at) values('26. --> IssueCalcDenominator',now()) ;
call IssueCalcDenominator(@orgId,@staffId);
#INSERT INTO messages (message,created_at) values('27. --> IssueCalcNumerator',now()) ;
call IssueCalcNumerator(@orgId,@staffId);
#INSERT INTO messages (message,created_at) values('28. --> insert into issues_temp_calc_done',now()) ;
        
#insert into tmp table to capture org and staff id
insert into issues_temp_calc_done(org_id,staff_id) values(@orgId,@staffId);
        
update issues_student_staff_mapping set calculated_at =now() where org_id = @orgId and issues_input_id = @issuesInputId and calculated_at is NULL
and staff_id = @staffId;
        
#To fetch staff
set @staffId := null;
SELECT staff_id into @staffId FROM issues_student_staff_mapping where org_id = @orgId and issues_input_id = @issuesInputId and calculated_at is NULL
and staff_id not in (select staff_id from issues_temp_calc_done) limit 1;
        
END WHILE;
        
#INSERT INTO messages (message,created_at) values('29. --> IssueCalcSet',now()) ;
call IssueCalcSet();
        
update issues_calculation_input set calculated_at =now() where student_id = @studentId and org_id = @orgId and calculated_at is NULL;
        
#To fetch for student
set @orgId := null;
set @studentId := null;
SELECT student_id into @studentId FROM issues_calculation_input where calculated_at is NULL limit 1;
SELECT org_id into @orgId FROM issues_calculation_input where student_id = @studentId and calculated_at is NULL limit 1;
        
END WHILE;
END;
CDATA;
        $this->addSql($calculation_query);
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
