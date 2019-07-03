--
-- data for course API
-- Date: 2015-03-13 
--


INSERT INTO `org_academic_year` (`id`, `created_by`, `modified_by`, `deleted_by`, `organization_id`, `created_at`, `modified_at`, `deleted_at`, `name`, `year_id`, `start_date`, `end_date`) VALUES (1,NULL,NULL,NULL,1,NULL,NULL,NULL,'Academic year','201415','2014-02-16','2015-02-16'),(2,NULL,NULL,NULL,1,NULL,NULL,NULL,'Educational Year','202627','2015-02-16','2016-02-16');


INSERT INTO `org_academic_terms` (`id`, `created_by`, `modified_by`, `deleted_by`, `organization_id`, `org_academic_year_id`, `created_at`, `modified_at`, `deleted_at`, `name`, `start_date`, `end_date`, `term_code`) VALUES (1,NULL,NULL,NULL,1,1,NULL,NULL,NULL,'Term 1','2015-02-16','2015-06-16','0'),(2,NULL,NULL,NULL,1,1,NULL,NULL,NULL,'Term 2','2015-06-17','2016-02-16','0');
/*!40000 ALTER TABLE `org_academic_terms` ENABLE KEYS */;
UNLOCK TABLES;


INSERT INTO `org_courses` (`id`, `created_by`, `modified_by`, `deleted_by`, `organization_id`, `org_academic_year_id`, `org_academic_terms_id`, `created_at`, `modified_at`, `deleted_at`, `course_section_id`, `college_code`, `dept_code`, `subject_code`, `course_number`, `course_name`, `section_number`, `days_times`, `location`, `credit_hours`, `externalId`) VALUES (1,NULL,NULL,NULL,1,1,1,NULL,NULL,NULL,'SEC A','IIT','IT','SEC001','0087','Computer Networks','SEC 1','12','Banglore',12.00,'327811'),(2,NULL,NULL,NULL,1,1,2,NULL,NULL,NULL,'SEC B','RMK','CSE','SEC987','6755','Science','SEC 2','12','Banglore',12.00,'327813'),(3,NULL,NULL,NULL,1,1,1,NULL,NULL,NULL,'327813','CAS','ANAT','BIO','111','ART 111','1','50','Moderson Hall 105',50.00,'327815'),(4,NULL,NULL,NULL,1,1,2,NULL,NULL,NULL,'327817','CAS','ART','ARTH','120','BIO 101','4','12','Moderson Hall 108',12.00,'327816');

INSERT INTO `org_course_faculty` (`id`, `created_by`, `modified_by`, `deleted_by`, `organization_id`, `org_courses_id`, `person_id`, `org_permissionset_id`, `created_at`, `modified_at`, `deleted_at`) VALUES (1,NULL,NULL,NULL,1,1,1,1,NULL,NULL,NULL),(2,NULL,NULL,NULL,1,2,1,1,NULL,NULL,NULL),(3,NULL,NULL,NULL,1,3,1,1,NULL,NULL,NULL);


INSERT INTO `org_course_student` (`id`, `created_by`, `modified_by`, `deleted_by`, `organization_id`, `org_courses_id`, `person_id`, `created_at`, `modified_at`, `deleted_at`) VALUES (1,NULL,NULL,NULL,1,1,2,NULL,NULL,NULL),(2,NULL,NULL,NULL,1,2,2,NULL,NULL,NULL),(3,NULL,NULL,NULL,1,2,3,NULL,NULL,NULL);