<?php
namespace Synapse\CoreBundle\Util\Constants;

class AcademicUpdateConstant
{

    const ORG_NOT_FOUND = "Organization Not Found.";

    const ORG_NOT_FOUND_KEY = 'organization_not_found';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ORG_SERVICE = "org_service";

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const PERSON_SERVICE = "person_service";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_STUDENT_REPO = "SynapseCoreBundle:OrgPersonStudent";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_ROLE_REPO = "SynapseCoreBundle:OrganizationRole";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const METADATA_REPO = "SynapseCoreBundle:MetadataListValues";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_REPO = "SynapseCoreBundle:Person";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_COURSES_REPO = "SynapseAcademicBundle:OrgCourses";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const AU_REQUEST_REPO = "SynapseAcademicUpdateBundle:AcademicUpdateRequest";

    /**
     * When your hammer is PHP, everything begins to look like a thumb.
     *
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const AU_UPDATE_REPO = "SynapseAcademicUpdateBundle:AcademicUpdate";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_COURSES_STUDENT_REPO = "SynapseAcademicBundle:OrgCourseStudent";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_COURSES_FACULTY_REPO = "SynapseAcademicBundle:OrgCourseFaculty";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_FACULTY_REPO = "SynapseCoreBundle:OrgPersonFaculty";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_GROUP_REPO = "SynapseCoreBundle:OrgGroup";

    const AU_NOT_FOUND = "Academic Update Request Not Found.";

    const AU_NOT_FOUND_KEY = 'academic_update_request_not_found';

    const AU_CLOSED_STATUS = 'closed';

    const AU_CANCEL_EMAIL_KEY = 'Academic_Update_Cancel_to_Faculty';

    const AU_REMINDER_EMAIL_KEY = 'Academic_Update_Reminder_to_Faculty';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBICONFIG_REPO = "SynapseCoreBundle:EbiConfig";

    const AU_REMINDER_SUBMISSION = 'submissionPage';

    const AU_EMAIL_ACTION = 'action';

    /**
     * What is the object-oriented way to become wealthy?
     * Inheritance.
     *
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const AU_ASSIGNED_FACULTY_REPO = "SynapseAcademicUpdateBundle:AcademicUpdateAssignedFaculty";

    const AU_REQUEST_ID = "requestId";

    const PARAM_FILTER = "filter";

    const STR_USER_TYPE = "faculty";

    const SELECT_TYPE_INDIVIDUAL = 'individual';

    const SELECT_TYPE_NONE = 'none';

    const SELECT_TYPE_ALL = 'all';

    const AUR_NOT_FOUND = "Academic Update Request Not Found.";

    const AUR_NOT_FOUND_KEY = 'academic_update_request_not_found';

    static $selectedIdName = [
        'students' => 'getSelectedStudentIds',
        'staff' => 'getSelectedStaffIds',
        'courses' => 'getSelectedCourseIds',
        'groups' => 'getSelectedGroupIds',
        'staticList' => 'getSelectedStaticIds'
    ];

    const STUDENT_ID = 'student_id';

    const KEY_FIRSTNAME = 'firstname';

    const KEY_LASTNAME = 'lastname';

    const COORDINATOR_ACCESS_DENIED = "You do not have coordinator access";

    const COORDINATOR_ACCESS_DENIED_KEY = "coordinator_access_denined";

    const KEY_REQUEST_ID = 'request_id';

    const KEY_REQUEST_NAME = 'request_name';

    const KEY_REQUEST_STATUS = 'request_status';

    const KEY_REQUEST_DESC = 'request_description';

    const KEY_REQUEST_CREATED = 'request_created';

    const KEY_REQUEST_DUE = 'request_due';

    const KEY_REQUEST_DETAILS = 'request_details';

    const KEY_COURSE_NAME = 'course_name';

    const KEY_SUBJECT_COURSE = 'subject_course';

    const KEY_COURSE_SEC_NAME = 'course_section_name';

    const KEY_DEPARTMENT_NAME = 'department_name';

    const DATE_FORMAT_MDY = 'm/d/Y';

    const KEY_ACADEMIC_YEAR_NAME = 'academic_year_name';

    const KEY_ACADEMIC_TERM_NAME = 'academic_term_name';

    const LT_STRING = 'string';

    const LT_BOOL = 'bool';

    const KEY_ORGID = 'orgId';

    const KEY_LOGGED_IN_USERID = 'loggedInUserId';

    const KEY_REQUEST = 'request';

    const KEY_FACULTY_EMAIL = 'faculty_email';

    const KEY_SUBJECT = 'subject';

    const KEY_EMAILKEY = 'emailKey';

    const AU_FACULTY_ASSIGNED_JOIN = 'fa.academicUpdate = au.id';

    const AU_AUR_LINK = 'aur.id = au.academicUpdateRequest';

    const AUR_PR_LINK = 'pr.id = aur.person';

    const AU_ORGID = 'au.org = :orgId';

    const AUR_ORGID = 'aur.org = :orgId';

    const LBL_STUDENT_ID = 'studentId';

    const LBL_METADATA_ID_QUERY = '_metadata_id';

    const LBL_COURSE_ID = 'courseId';

    const HELP_EMAIL_DETAIL = 'email_detail';

    const ORG_ACADEMIC_REPO = "SynapseAcademicBundle:OrgAcademicYear";

    const ACADEMIC_UPDATE_REQUEST_EQUALS_REQUESTID = 'aurs.academicUpdateRequest = :requestid';

    const YMD = 'Y-m-d';

    const REQUESTCREATED = 'requestCreated';

    const REQUESTDUE = 'requestDue';

    const STUDENT_ABSENCES = 'student_absences';

    const STUDENT_RISK = 'student_risk';

    const STUDENT_COMMENTS = 'student_comments';

    const STUDENT_GRADE = 'student_grade';

    const REQUESTID = 'requestid';

    const EBI_SYSTEM_URL = "System_URL";

    const AU_VIEW_URL = "#/academic-updates/update/";

    const AU_VIEW_CLOSE_URL = "#/academic-updates/view/";

    const AU_ABSENCE_LOWER_LIMIT = 0;

    const AU_ABSENCE_HIGHER_LIMIT = 99;

    const ACADEMIC_UPDATE_IN_PROGRESS_GRADE_VALUES = 'A,B,C,D,F,Pass';

    const ACADEMIC_UPDATE_FINAL_GRADE_VALUES = 'A,A-,B+,B,B-,C+,C,C-,D+,D,D-,F/No Pass,Pass,Withdraw,Incomplete,In Progress,Not for Credit';

    const AU_COMMENT_STRING_LIMIT = 300;
}