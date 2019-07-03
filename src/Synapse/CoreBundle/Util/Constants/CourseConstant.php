<?php
namespace Synapse\CoreBundle\Util\Constants;

class CourseConstant
{

    const FACULTY_NOT_FOUND = "Faculty not found";

    const FACULTY_NOT_FOUND_KEY = 'faculty_not_found';

    const COURSE_NOT_FOUND = "Course not found";

    const COURSE_NOT_FOUND_KEY = 'course_not_found';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_FACULTY_REPO = "SynapseAcademicBundle:OrgCourseFaculty";

    const ORG_NOT_FOUND = "Organization Not Found";

    const ORG_NOT_FOUND_KEY = 'organization_not_found';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_STUDENT_REPO = "SynapseAcademicBundle:OrgCourseStudent";

    const STUDENT_NOT_FOUND = "Student Not Found";

    const STUDENT_NOT_FOUND_KEY = "student_not_found";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_COURSES_REPO = "SynapseAcademicBundle:OrgCourses";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const COURSE_REPO = "SynapseAcademicBundle:OrgCourses";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_ROLE_REPO = "SynapseCoreBundle:OrganizationRole";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_REPO = "SynapseCoreBundle:Organization";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACADEMIC_YEAR_REPO = "SynapseAcademicBundle:OrgAcademicYear";

    /**
     * Why do programmers confuse Halloween and Christmas?
     * Because Oct(31) = Dec(25)
     *
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACADEMIC_TERM_REPO = "SynapseAcademicBundle:OrgAcademicTerms";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_REPO = "SynapseCoreBundle:Person";

    const COORDINATOR_NOT_FOUND = "Coordinator Not Found.";

    const COORDINATOR_NOT_FOUND_KEY = "coordinator_not_found.";

    const YEAR_NOT_FOUND = "Year ID Not Found";

    const YEAR_NOT_FOUND_KEY = "year_id_does_not_exist.";

    const TERM_NOT_FOUND = "Term ID Not Found";

    const TERM_NOT_FOUND_KEY = "term_not_found.";

    const ERROR_EMPTY_YEARID = " Year ID is empty";

    const ERROR_EMPTY_YEARID_KEY = "empty_year_id";

    const ERROR_EMPTY_TERMID = " Term ID is empty";

    const ERROR_EMPTY_TERMID_KEY = "empty_term_id";

    const ERROR_EMPTY_COLLEGE = " College is empty";

    const ERROR_EMPTY_COLLEGE_KEY = "empty_college";

    const ERROR_EMPTY_DEPT = " Department is empty";

    const ERROR_EMPTY_DEPT_KEY = "empty_department";

    const INVALID_USER_TYPE = "Invalid User Type";

    const INVALID_USER_TYPE_KEY = "invalid_usertype";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const METADATA_REPO = "SynapseCoreBundle:MetadataListValues";

    const PERSON_SERVICE = 'person_service';

    const ORGANIZATION_SERVICE = 'org_service';

    const PERSON_NOT_FOUND = 'Person Not Found';

    const PERSON_NOT_FOUND_KEY = "person_not_found";

    const LASTNAME = 'LastName';

    const FIRSTNAME = 'FirstName';

    const EMAIL = 'Email';

    const EXTERNALID = 'ExternalID';

    const PERMISSIONTEMP = 'PermissionTemplate';

    const ARRAY_LASTNAME = 'lastname';

    const ARRAY_FIRSTNAME = 'firstname';

    const ARRAY_EMAIL = 'email';

    const ARRAY_EXTERNALID = 'externalId';

    const YEARID = 'YearId';

    const TERMID = 'TermId';

    const TERMNAME = 'TermName';

    const UNIQUECOURSESECID = 'UniqueCourseSectionId';

    const SUBJECTCODE = 'SubjectCode';

    const COURSENO = 'CourseNumber';

    const SECNO = 'SectionNumber';

    const COURSENAME = 'CourseName';

    const COLLGEGCODE = 'CollegeCode';

    const DEPTCODE = 'DeptCode';

    const COUNTOFFACULTY = 'CountofFaculty';

    const COUNTOFSTUD = 'CountofStudents';

    const COURSE = 'courses';

    const IS_ORG_ID = 'c.organization = :orgId';

    const ARRAY_YEARID = 'yearId';

    const ARRAY_TERMID = 'termID';

    const ARRAY_SUBCODE = 'subjectCode';

    const ARRAY_COURSENO = 'courseNumber';

    const ARRAY_SECTIONNO = 'sectionNumber';

    const ARRAY_COLLEGECODE = 'collegeCode';

    const ARRAY_COURSENAME = 'courseName';

    const ARRAY_DEPTCODE = 'deptCode';

    const COORDINATOR = 'coordinator';

    const ARRAY_TERMNAME = 'termName';

    const FACULTY = 'faculty';

    const IS_OWN_ORG = 't.organization = :organization';

    const IS_DELETED = 't.deletedAt IS NULL';

    const ORGANIZATION = 'organization';

    const IS_ORG_COURSE = 'oc.organization = :orgId';

    const ORGID = 'orgId';

    const IS_ACADEMIC_TERM = 'c.orgAcademicTerms = :term';

    const COLLEGE = 'college';

    const IS_COLLEGECODE = 'c.collegeCode = :college';

    const DEPARTMENT = 'department';

    const IS_DEPTCODE = 'c.deptCode = :department';

    const COURSE_FIELD = 'course';

    const ACADEMICBUNDLE = 'SynapseAcademicBundle:';

    const FUNC_ARRAY_MERGE = 'array_merge';

    const PERSON = 'person';

    const USER_ASSIGNED = 'User already assigned to course section';

    const IS_ACADEMICYEAR_MATCH = 'c.orgAcademicYear = y.id';

    const IS_ACADMICTERM_MATCH = 'c.orgAcademicTerms = t.id';

    const IS_PERSONID_MATCH = 'oc.person = :userId';

    const USERID = 'userId';

    const AMAZONSECRET = 'amazon_s3.secret';

    const DATE_FORMAT = 'Y-m-d\TG:i:s\Z';

    const EXPIRATION = '{"expiration": "';

    const POLICY = 'policy';

    const SIGNATURE = 'signature';

    const IS_YEAR_MATCH = 'y.yearId = :year';

    const FILTER = 'filter';

    const FILTER_CONDITION = 'c.subjectCode LIKE :subjectCode OR c.courseNumber LIKE :subjectCode';

    const TERM = 'term';

    const YEAR = 'year';

    const ORGPERMISSION_NOT_FOUND = 'OrgPermissionSet Not Found';

    const ORGPERMISSION_NOT_FOUND_KEY = "orgpermissionset_not_found";

    const FACULTY_NOT_ASSIGNED = "Faculty not assigned for this course";

    const FACULTY_NOT_ASSIGNED_KEY = 'faculty_not_assigned_key';

    const ERROR_PERMISSIONSET_NOT_FOUND = "Permissionset Not Found";

    const ERROR_PERMISSIONSET_NOT_FOUND_KEY = 'Permissionset_not_found';

    const NAVIGATION = 'navigation';

    const YMD_FORMAT = 'Y-m-d';

    const ACADEMICUPDATE_REPO = "SynapseAcademicUpdateBundle:AcademicUpdate";

    const ORGCOURSES = 'orgCourses';

    const ACADEMICUPDATE_REQUEST_REPO = 'SynapseAcademicUpdateBundle:AcademicUpdateRequestCourse';

    const ACADEMICUPDATE_COURSE_ERROR = 'course_academicupdate_submitted';

    const PERSON_STUDENT_REPO = "SynapseCoreBundle:OrgPersonStudent";

    const DAYS_TIMES = 'Days/Times';

    const CREDIT_HOURS = 'CreditHours';

    const LOCATION = 'Location';

    const STUDENTID = 'StudentId';

    const FACULTYID = 'FacultyID';

    const PERMISSIONSET = 'PermissionSet';

    const COURSE_FACULTY_UPLOAD_SERVICE = 'course_faculty_upload_service';

    const ORGPERMISSIONSET_ENTITY = 'SynapseCoreBundle:OrgPermissionset';

    const OC_COURSE_CID = 'oc.course = c.id';

    const TODAY = 'today';

    const YMD = "Y-m-d";

    const CF_COURSE_OCID = 'cf.course = oc.id';

    const CS_COURSE_OCID = 'cs.course = oc.id';

    const ACADEMIC_YEAR = "academic_year";

    const ACADEMIC_TERM = "academic_term";

    const ORG_ID = 'org_id';

    const YEARID_LOWERCASE = 'yearid';

    const TERMID_LOWERCASE = 'termid';

    const TERMNAME_LOWERCASE = 'termname';

    const UNIQUECOURSESECID_LOWERCASE = 'uniquecoursesectionid';

    const SUBJECTCODE_LOWERCASE = 'subjectcode';

    const COURSENO_LOWERCASE = 'coursenumber';

    const SECNO_LOWERCASE = 'sectionnumber';

    const COURSENAME_LOWERCASE = 'coursename';

    const CREDIT_HOURS_LOWERCASE = 'creditHours';

    const COLLGEGCODE_LOWERCASE = 'collegecode';

    const DEPTCODE_LOWERCASE = 'deptcode';

    const DAYS_TIMES_LOWERCASE = 'days/times';

    const LOCATION_LOWERCASE = 'location';


}