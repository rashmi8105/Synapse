<?php
namespace Synapse\UploadBundle\Util\Constants;

class UploadConstant
{
    /*
     * Supported uploads
     */
    Const UPLOAD_TYPE_ACADEMIC_UPDATES = 'A'; // #01
    Const UPLOAD_TYPE_COURSES = 'C'; // #02
    Const UPLOAD_TYPE_FACULTIES = 'F'; // #03
    Const UPLOAD_TYPE_GROUPS = 'G'; // #04
    Const UPLOAD_TYPE_STUDENTS = 'S'; // #05
    Const UPLOAD_TYPE_COURSE_STUDENTS = 'T'; // #06
    Const UPLOAD_TYPE_COURSE_FACULTIES = 'P'; // #07
    Const UPLOAD_TYPE_SURVEY_BLOCKS = 'SB'; // #08
    Const UPLOAD_TYPE_SURVEY_MARKERS = 'SM'; // #09
    Const UPLOAD_TYPE_TALKING_POINTS = 'TP'; // #10
    Const UPLOAD_TYPE_HELP = 'H'; // #11
    Const UPLOAD_TYPE_RISK_VARIABLES = 'RV'; // #12
    Const UPLOAD_TYPE_RISK_MODELS = 'RM'; // #13
    Const UPLOAD_TYPE_RISK_MODEL_ASSOC = 'RMA'; // #14
    Const UPLOAD_TYPE_STATIC_LIST = 'SL'; // #15
    Const UPLOAD_TYPE_FACTOR = 'FA'; // #16
    Const UPLOAD_TYPE_STUDENT_GROUP = 'S2G'; // #17
    const ERRORS = 'errors';

    const VALUE = 'value';

    const ORGANIZATION = "Organization";

    const ORGN = 'organization';

    const ORG = "Organization";

    const ORGID = 'orgId';

    const USERID = 'userId';

    const STATICLISTID = "staticListId";

    const UPLOADID = 'uploadId';

    const JOBSTATUS = 'jobStatus';

    const FINISHED = "finished";

    const DATA = 'data';

    const UPLOADTYPE = 'uploadType';

    const CLASS_CONST = 'class';

    const UPLOAD_FILE_LOG_SERVICE = "upload_file_log_service";

    const PLUS15MIN = "+ 15 minutes";

    const EXPIRATION = "expiration";

    const POLICY = 'policy';

    const SIGNATURE = 'signature';

    const EXTENSION = 'extension';

    const DATETIMEZONE = 'Y-m-d\TG:i:s\Z';

    const KERNEL = 'kernel.cache_dir';

    const UPLOAD = 'upload';

    const CONTENT_TYPE_CSV = 'Content-Type: text/csv';

    const BLOCK = 'block';

    const SECURITY_CONTEXT = 'security.context';

    const JOB_NUM = 'jobNumber';

    const RESQUE = 'bcc_resque.resque';

    const EXTERNALID = 'ExternalId';

    const IS_AUTH_FULLY = 'IS_AUTHENTICATED_FULLY';

    const STUD_PHOTO = 'StudentPhoto';

    const DATETIME = 'Y-m-d H:i:s';

    const MDY = 'm/d/Y';

    const PRIMARY_CONNECT = 'PrimaryConnect';

    const SENTTOSTUDENT = 'SentToStudent';

    const FAILURERISK = 'FailureRisk';

    const IN_PROGRESS_GRADE = 'InProgressGrade';

    const FINAL_GRADE = 'FinalGrade';

    const PRIMARY_EMAIL = 'PrimaryEmail';

    const STATUS = "status";

    const DECIMAL_POINTS = 'decimal_points';

    const NUM_TYPE = 'number_type';

    const MIN_DIGITS = 'min_digits';

    const MAX_DIGITS = 'max_digits';

    const CONTAINS_INVALID_VALUE = 'Contains an invalid value';

    const QUES_PROFILE_ITEM = 'QuestionProfileItem';

    const WEAKNESS_TEXT = 'WeaknessText';

    const MIN_WEAKNESS_TEXT = 'minWeaknessRange';

    const MAX_WEAKNESS_TEXT = 'maxWeaknessRange';

    const STRENGTH_TEXT = 'StrengthText';

    const MIN_STRENGTH_RANGE = 'minStrengthRange';

    const MAX_STRENGTH_RANGE = 'maxStrengthRange';

    const LOGGER = 'logger';

    const PERSON = 'person';

    const STUDENT = "Student";

    const FACULTY = "Faculty";

    const SURVEYCOHORT = 'SurveyCohort';

    const RECIEVESURVEY = 'ReceiveSurvey';

    const UPLOAD_ERRORS = 'Upload Errors';

    const BUCKET_OVERLAP = 'Buckets overlap';

    const USER_TYPE = 'user_type';

    const GRANT_TYPE = 'grant_type';

    const DATA_NOT_VALID = 'Data not valid! Record does not match existing values';

    const DATE_TIME = 'DateTime';

    const ORGANIZATION_ID = 'organizationId';

    const AWS_BUCKET = 'AWS_Bucket';

    const SESSION = 'session';

    const SWITCH_CAMPUS = 'switchToCampus';

    const PASSWORD = 'password';

    const INVALID_VALUE = "is an invalid value. ";

    const DATA_SLASH = "data://";

    const AWS_SECRET = '6gHgrpMsa1Ty6ntBFloJ0WKOWY54GmLYGpzVz+zF';

    const REQUEST = 'request';

    const YEARID = 'YearId';

    const TERMID = 'TermId';

    const RISK_GROUP_ID = 'riskGroupId';

    const CLEAR_FIELD = "#clear";

    const OUR_STUD_REPORT_UPLOAD_DIR = "report_calculation";

    const OUR_STUD_REPORT_COL_LONGITUDINALID = "longitudinalid";

    const OUR_STUD_REPORT_COL_SURVID = "survid";

    const OUR_STUD_REPORT_COL_FACTORID = "factorid";

    const OUR_STUD_REPORT_COL_REPORTSECTIONID = "reportsectionid";

    const OUR_STUD_REPORT_COL_REPORTSECTIONNAME = "reportsectionname";

    const OUR_STUD_REPORT_COL_DISPLAYLABEL = "displaylabel";

    const OUR_STUD_REPORT_COL_NUMERATORHIGH = "numeratorhigh";

    const OUR_STUD_REPORT_COL_NUMERATORLOW = "numeratorlow";

    const OUR_STUD_REPORT_COL_DENOMINATORHIGH = "denominatorhigh";

    const OUR_STUD_REPORT_COL_DENOMINATORLOW = "denominatorlow";

    const OUR_STUD_REPORT_COL_NUMERATORCHOICES = "numeratorchoices";

    const OUR_STUD_REPORT_COL_DENOMINATORCHOICES = "denominatorchoices";

    const FACULTY_UPLOAD_STATUS = 'IsActive';

    const IS_ACTIVE = 'IsActive';

    const GROUPID = 'GroupId';

    const DATEOFBIRTH = 'DateofBirth';

    const FIRSTNAME = 'Firstname';

    const LASTNAME = 'Lastname';

    const CAMPUSID = 'CampusId';

    const STUDENTID = 'StudentId';

    const REMOVE = 'Remove';

    const UNIQUECOURSESECTIONID = "UniqueCourseSectionId";

    const GROUP_EXTERNAL = 'GroupID';

    const CSV_QUEUE = "csvqueue";

    const FILE_NOT_FOUND = "The requested file was not found.";

    const MAX_CHARACTER_LENGTH = 45;

    const REQUIRED_FIELD_ERROR = "The file is missing one or more required column headers: UniqueCourseSectionId, StudentId";

    const INVALID_FIELD_ERROR = "The file contains one or more invalid column headers: ";

    const EXPRESS_QUEUE = "express";

    const EXPRESS_QUEUE_COUNT = 100;

    const DUPLICATE_FIELDS_ERROR = "The file contains duplicate columns: ";

    const REMOVE_COLUMN_ERROR = "'Remove' column accepts only 'remove' or 'Remove' text.";

}
