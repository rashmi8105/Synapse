<?php

namespace Synapse\CoreBundle;


class SynapseConstant
{

    //client keys constant
    const WEB_APP = '382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s';

    const WEB_APP_ADMIN = '14tx5vbsnois4ggg0ok0c4gog8kg0ww488gwkg88044cog4884';

    const ART_DATA_POPULATION = '2y0ku7f5748wwwskkk84o00ssgwsgkokks8ogs08ckscckcskg';


    // EBI Profile item character length
    const CAMPUS_PROFILE_ITEM_CHARACTER_LENGTH =  255; //Used for the ebi items campusstate,campuszip,campuscountry,campusaddress,campuscity


    // Numeric Constants
    const DEFAULT_INACTIVITY_TIMEOUT = 3600;

    const ADMIN_ORGANIZATION_ID = -1;

    const DEFAULT_PAGE_NUMBER = 1;

    const DEFAULT_RECORD_COUNT = 25;

    const DEFAULT_UPLOAD_BATCH_SIZE = 30;

    const EXPRESS_QUEUE_COUNT = 100;

    const STUDENT_STATUS_ARCHIVED = -1;

    const STUDENT_STATUS_INACTIVE = 0;

    const STUDENT_STATUS_ACTIVE = 1;

    const RESET_PASSWORD_EXPIRY_HOURS = 1;

    const ONE_HUNDRED_PERCENT = 100;

    const REFERRAL_FEATURE_ID = 1; // referral id from the feature_master table

    const DEFAULT_DATE_FORMAT = "Y-m-d";

    const DATE_FORMAT = "m/d/Y";

    const TWO_DIGIT_YEAR_DATE_FORMAT = "m/d/y";

    const DAY_MONTH_YEAR_TWELVE_HOUR_TIMEZONE_DATE_FORMAT = 'd-M-Y h:i A T';

    //Vendor DI Keys

    const REDIS_CLASS_KEY = 'synapse_redis_cache';

    const RESQUE_CLASS_KEY = 'bcc_resque.resque';

    const SECURITY_CONTEXT_CLASS_KEY = 'security.context';

    const DOCTRINE_CLASS_KEY = 'doctrine';

    const JMS_SERIALIZER_CLASS_KEY = 'jms_serializer';

    const REPOSITORY_RESOLVER_CLASS_KEY = 'repository_resolver';

    const MAILER_KEY = 'mailer';

    const SWIFT_MAILER_TRANSPORT_REAL_KEY = 'swiftmailer.transport.real';

    const VALIDATOR = "validator";

    const TINYRBAC_MANAGER = "tinyrbac.manager";

    const OAUTH_SERVICE_KEY = "fos_oauth_server.server";

    const CONTROLLER_LOGGING_CHANNEL = 'monolog.logger.api';

    const CLIENT_MANAGER_CLASS_KEY = "fos_oauth_server.client_manager.default";

    const SYMFONY2_MODULE_KEY = 'Symfony2';

    const LOGGER_KEY = 'logger';

    const KERNEL_ROOT_DIRECTORY = 'kernel.root_dir';

    // Messages To User
    const DOWNLOAD_IN_PROGRESS_MESSAGE = 'You may continue to use Mapworks while your download completes. We will notify you when it is available.';

    const FILE_SUCCESSFULLY_SAVED_IN_S3_BUCKET_MESSAGE = 'Successfully saved to S3 bucket and DB Updated with details.';


    // S3 Constants
    const S3_ROOT = 'data://';

    const S3_COURSE_STUDENT_UPLOADS_DIRECTORY = 'course_student_uploads';

    const S3_CSV_EXPORT_DIRECTORY = 'export_csvs';

    const S3_STUDENT_SURVEY_REPORT_DIRECTORY = 'student_reports_uploads';

    const S3_REPORT_DOWNLOADS_DIRECTORY = 'report_downloads';

    const S3_ROASTER_UPLOAD_DIRECTORY = 'roaster_uploads';


    // Static Web Application Resource Paths
    const SKYFACTOR_LOGO_IMAGE_PATH = 'images/Skyfactor-Mapworks-login.png';


    // String Constants
    const SERVICE_ACCOUNT_ROLE_NAME = "API Coordinator";

    const APPOINTMENT_FEATURE_NAME_IN_FEATURE_MASTER_LANG = 'Booking';

    const APPOINTMENT_FEATURE_NAME_IN_RBAC = 'booking';

    const DEFAULT_QUEUE = 'default';

    const EXPRESS_QUEUE = 'express';

    const GROUP_STUDENT_UPLOAD_PATH = 'group_uploads/';

    const GROUP_STUDENT_UPLOAD_ERROR_PATH = 'group_uploads/errors/';

    const GROUP_STUDENT_UPLOAD_DUMP_FILE = '-latest-student-dump.csv';

    const GROUP_STUDENT_UPLOAD_ERROR_FILE = '-upload-errors.csv';

    const INACTIVE_ORGANIZATION_STATUS = "I";

    const SKYFACTOR_DEFAULT_TIMEZONE = "US/Central";

    const DEFAULT_SYSTEM_ERROR_MESSAGE = "There was an error with your request. Please try your request again. If the problem persists, please contact Mapworks Client Services.";

    const DATE_INTERVAL = "P0DT0H30M0S";


    // Appointments Constants

    const APPOINTMENTS_PUBLIC_CREATE_PERMISSION = 'booking-public-create';

    const APPOINTMENTS_PUBLIC_VIEW_PERMISSION = 'booking-public-view';

    const APPOINTMENTS_PRIVATE_CREATE_PERMISSION = 'booking-private-create';

    const APPOINTMENTS_PRIVATE_VIEW_PERMISSION = 'booking-private-view';

    const APPOINTMENTS_TEAM_CREATE_PERMISSION = 'booking-teams-create';

    const APPOINTMENTS_TEAMS_VIEW_PERMISSION = 'booking-teams-view';

    // Academic Updates Grade
    const ACADEMIC_UPDATE_GRADES = "A,B,C,D,F,P,N/A";


    // Feature Master Switches
    const API_INTEGRATION_MASTER_SWITCH_KEY = 'API_integration_master_switch';

    // API Integration
    const API_MAX_ERROR_COUNT_KEY = 'max_API_error_count_on_interval';

    const API_ERROR_INTERVAL_KEY = 'API_error_interval_in_minutes';

    const POST_PUT_MAX_RECORD_COUNT = 'post_put_body_max_record_count';

    // Participant Id obfuscation constants
    const RANDOM_MULTIPLIER_SALT_FOR_PARTICIPANT_ID = 151;

    const ADDED_SALT_FOR_PARTICIPANT_ID = 100;

    // Date format constants for compare report
    const METADATA_TYPE_DATE_FORMAT = '%m/%d/%Y';

    const METADATA_TYPE_DEFAULT_DATE_FORMAT = '%Y-%m-%d';

    const STUDENT_LIST_DATE_FORMAT = '%Y/%m/%d';

    const SUBPOPULATION_DATE_FORMAT = '%m-%d-%Y';

    // Bad request http error code
    const BAD_REQUEST_HTTP_ERROR_CODE = 400;

    //Regular Expressions
    const REGEX_STRING_CONTAINS_NO_LETTERS = "/[^0-9]/";

    const REGEX_STRING_FIND_UPPERCASE = '/[A-Z]([A-Z](?![a-z]))*/';

    const REGEX_UNDERSCORE_VARIABLE = '_$0';

    // Cronofy Calendar Provider

    const CRONOFY_CALENDAR_ICLOUD_PROVIDER = 'Apple iCalendar';

    const CRONOFY_CALENDAR_MICROSOFT_PROVIDER = 'Microsoft';

    const CRONOFY_CALENDAR_OUTLOOK_PROVIDER = 'Outlook';

    const CRONOFY_CALENDAR_GOOGLE_PROVIDER = 'Google';

    const DEFAULT_BATCH_SIZE = 30;

    const MINIMUM_THRESHOLD_OF_STUDENTS_FOR_TOP_ISSUES = 9;

    const CRONOFY_TIME_ZONE_KEY = 'tzid';

    const CRONOFY_STATUS_ACCEPTED = '202';

    // Resque Job Errors

    const RESQUE_JOB_CALENDAR_ERROR = "Mapworks is currently updating your external calendar. You cannot change the sync settings until it has completed. Please try again later.";

    const RESQUE_NO_JOB_FOUND = "No Job found";

    const JOB_STATUS_COMPLETED = "Completed";

    const JOB_STATUS_INPROGRESS = "In Progress";

    const JOB_STATUS_QUEUED = 'Queued';

    const JOB_STATUS_SUCCESS = 'Success';

    const JOB_STATUS_FAILURE = 'Failure';

    const ORG_PENDING_JOB_ERROR = "One or more users are still performing a calendar sync or unsync. You cannot change the sync settings globally until they have finished. Please try again later.";

    const INTERNAL_SERVER_ERROR_CODE = 500;

    const ACCESS_DENIED_ERROR_CODE = 403;

    // Calendar Jobs
    const ORG_CALENDAR_JOB = 'SwitchOrgCalendarJob';

    const JOB_KEY_RECURRENT_EVENT = 'RecurrentEventJob';

    const JOB_KEY_REMOVE_EVENT = 'RemoveEventJob';

    const PDF_CONTENT_TYPE = 'Content-Type: application/pdf';

    // Security Constants

    const ENCRYPTION_HASH = '25c6c7ff35b9979b151f2136cd13b0ff';

    const ENCRYPTION_METHOD = 'AES-256-CBC';

    // Date Constants
    const DEFAULT_TIME_FORMAT = "H:i:s";

    const METADATA_TYPE_DEFAULT_TIME_FORMAT = '%H:%i:%s';

    const DATE_TIME_ZONE_FORMAT = 'Y-m-d\TH:i:s\Z';

    const DATE_MDY_SLASHED_FORMAT = "m/d/Y";

    const DEFAULT_CSV_COLUMN_DATETIME_FORMAT = 'm/d/Y h:iA T';

    const DEFAULT_DATETIME_FORMAT = "Y-m-d H:i:s";

    const DATE_FORMAT_WITH_TIMEZONE = "Y-m-d\TH:i:sO";

    const DATE_YMD_FORMAT = "Y-m-d";

    const DATETIME_FORMAT_CSV = 'Ymd_HisT';

    const DEFAULT_CSV_FILENAME_DATETIME_FORMAT = 'Ymd_His';

    const DATE_AT_TIME_TIMEZONE_FORMAT = 'm/d/Y \a\t h:i:s A T';

    const DAY_OF_WEEK_DATETIME_FORMAT = 'w';

    const YEAR_DATETIME_FORMAT = 'Y';

    const MONTH_DATETIME_FORMAT = 'm';

    const ONE_MONTH_DURATION = 'P1M';

    const DAYS_IN_WEEK = '7';

    const FIRST_DATE_IN_MONTH = '01';

}
