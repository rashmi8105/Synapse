<?php
namespace Synapse\ReportsBundle\Util\Constants;

class ReportsConstants
{
    const REPORTS_QUEUE = 'reports';

    const S3_REPORT_CSV_EXPORT_DIRECTORY = 'report_downloads';

    const AGGREGATION_THRESHOLD = 5;

    const OUR_STUDENT_REPORT_NUMBER_OF_ISSUES = 5;


    // Messages To User

    const NO_GPA_SCORES_MESSAGE = "The selected students do not have any End Term GPA profile data for the academic terms in this report. Please upload End Term GPA data in the student file, or change the filter criteria, and run the report again.";

    const REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_CODE = 'R1001';

    const REPORT_NO_DATA_CODE = 'R1002';

    const REPORT_NO_DATA_MESSAGE = 'There are no students that fit your selected criteria. Please refine your criteria and run your report again.';

    const REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_MESSAGE = "The optional filters you selected returned no results. Try refining your optional filters.";

    const NO_STUDENTS_AVAILABLE_FOR_RETENTION_TRACKING_YEAR = "There are no students available for the retention tracking year selected.";

    // All constants above this comment are intentionally included in this class, following the coding standards.
    // They are useful constants, are used in multiple classes, and are only used in ReportsBundle.
    // ToDo: As reports are refactored, constants below this comment should gradually be removed or moved above this comment.


    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const REPORTS_REPO = 'SynapseReportsBundle:Reports';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_REPO = 'SynapseCoreBundle:Person';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_REPO = "SynapseCoreBundle:Organization";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_LANG_REPO = 'SynapseCoreBundle:OrganizationLang';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const PERSISTENCE_RETENTION_SERVICE = 'persistence_retention_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_CALC_FLAGS_STUD_REPORTS_REPO = 'SynapseReportsBundle:OrgCalcFlagsStudentReports';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const REPORTS_CALCULATED_REPO = 'SynapseReportsBundle:ReportCalculatedValues';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_STUD_SURVEY_LINK_REPO = 'SynapseSurveyBundle:OrgPersonStudentSurveyLink';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_ACADEMIC_YEAR_REPO = 'SynapseAcademicBundle:OrgAcademicYear';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const YEAR_REPO = 'SynapseAcademicBundle:Year';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const WESS_LINK_REPO = 'SynapseSurveyBundle:WessLink';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_STUDENT_REPO = 'SynapseCoreBundle:OrgPersonStudent';

    const ID = 'id';

    const REPORT_NAME = 'report_name';

    const REPORT_DESCRIPTION = 'report_description';

    const REPORT_IS_BATCH_JOB = 'is_batch_job';

    const REPORT_IS_COORDINATOR_REPORT = 'is_coordinator_report';

    const REPORT_SHORT_CODE = 'short_code';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const REPORT_SECTION_REPO = 'SynapseReportsBundle:ReportSections';

    const ERROR_REPORT_NOT_FOUND = 'Reports Not Found';

    const ERROR_KEY_REPORT_NOT_FOUND = 'reports_not_found';

    const ERROR_SECTION_NOT_FOUND = 'Section Not Found';

    const ERROR_KEY_SECTION_NOT_FOUND = 'section_not_found';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const REPORT_SECTION_ELEMENT_REPO = 'SynapseReportsBundle:ReportSectionElements';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const REPORT_ELEMENT_BUCKET_REPO = 'SynapseReportsBundle:ReportElementBuckets';

    const ERROR_ELEMENT_NOT_FOUND = 'Element Not Found';

    const ERROR_KEY_ELEMENT_NOT_FOUND = 'element_not_found';

    const ERROR_PERSON_NOT_FOUND = "Person Not Found.";

    const ERROR_PERSON_NOT_FOUND_KEY = "person_not_found";

    const ORGANIZATION_NOT_FOUND_CODE = 'organization_not_found';

    const ORGANIZATION_NOT_FOUND = 'Organization ID Not Found';

    const REPORT_NOT_FOUND_CODE = 'report_not_found';

    const REPORT_NOT_FOUND = 'Report ID Not Found';

    const PERSON_CONTACTS = 'p.report_calculated_values';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SURVEY_REPO = 'SynapseCoreBundle:Survey';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SURVEY_LANG_REPO = 'SynapseCoreBundle:SurveyLang';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const REPORT_TIPS_REPO = 'SynapseReportsBundle:ReportTips';

    const NAME = 'name';

    const STUDENT_REPORT = 'student-report';

    const YELLOW_COLOR = 'yellow';

    const RED_COLOR = 'red';

    const DB_FIELD_PERSON = 'person';

    const DB_FIELD_ORGANIZATION = 'organization';

    const DB_FIELD_COHORT_CODE = 'cohortCode';

    const DB_FIELD_REPORTS = 'reports';

    const SURVEY_STATUS_COMPLETED = 'CompletedAll';

    const PAGE_NO = 1;

    const OFFSET = 25;

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const REPORT_RUNNING_STATUS_REPO = 'SynapseReportsBundle:ReportsRunningStatus';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const REPORT_TEMPLATE_REPO = "SynapseReportsBundle:ReportsTemplate";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SURVEY_RESPONSE_REPO = "SynapseSurveyBundle:SurveyResponse";

    const ACTIVITY_ID = 'activity_id';

    const ACTIVITY_TYPE = 'activity_type';

    const ACTIVITY_STATUS = 'activity_status';

    const ACTIVITY_CREATED_BY = 'activity_created_by';

    const ACTIVITY_CREATED_ON = 'activity_created_on';

    const STUDENT_NAME = 'student_name';

    const ACTIVITY_DETAILS = 'activity_details';

    const TOTAL_RECORDS = 'total_records';

    const TOTAL_PAGES = 'total_pages';

    const RECORDS_PER_PAGE = 'records_per_page';

    const CURRENT_PAGE = 'current_page';

    const ACTIVITY_FILTER_DATE = 'activity_filter_date';

    const ACTIVITIES = 'activities';

    const REFERAL_STATUS_SHOT_CODE_CLOSED = 'C';

    const REFERAL_STATUS_CLOSED = 'Closed';

    const REFERAL_STATUS_OPEN = 'Open';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACADEMIC_YEAR_REPO = "SynapseAcademicBundle:OrgAcademicYear";

    const ACADEMIC_YEAR_ID = 'yearId';

    const ACADEMIC_YEAR_NOT_FOUND = 'Academic Year Not Found';

    const ACADEMIC_YEAR_NOT_FOUND_CODE = 'Academic_Year_Not_Found';

    const DATE_FORMAT = 'Y-m-d';

    const ACTIVITIES_TYPE_EMAIL = 'email';

    const ACTIVITIES_TYPE_NOTE = 'note';

    const ACTIVITIES_TYPE_CONTACT = 'contact';

    const ACTIVITIES_TYPE_REF = 'referrals';

    const ACTIVITIES_TYPE_APP = 'appointment';

    const INVALID_ACTIVITY_TYPE = 'Invalid activities type';

    const INVALID_ACTIVITY_TYPE_CODE = 'Invalid_activities_type';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const METADATA_REPO = "SynapseCoreBundle:MetadataListValues";

    const ORG_NOT_FOUND = "Organization Not Found.";

    const ORG_NOT_FOUND_KEY = 'organization_not_found';

    const YMD = 'Y-m-d H:i:s';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ALERT_SERVICE = 'alertNotifications_service';

    const ACTIVITY_DOWNLOAD_DESCRIPTION = 'Your Activity Download has completed';

    const ACTIVITY_DOWNLOAD = 'Activity_Download';

    const SURVEY_COHORTS = 'survey_cohorts';

    const COHORT_CODE = 'cohort_code';

    const SURVEY_NAME = 'survey_name';

    const SURVEY_ID = 'survey_id';

    const SURVEY = 'survey';

    const COHORT = 'cohort';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBI_META_DATE_LIST_REPO = 'SynapseCoreBundle:EbiMetadataListValues';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SYNAPSE_EBI_CONFIG_ENTITY = 'SynapseCoreBundle:EbiConfig';

    const SURVEY_COHORT_ID = 'survey_cohort_id';

    const SURVEY_COHORT_NAME = 'survey_cohort_name';

    const COHORT_ID = 'cohort_id';

    const DOWNLOAD_KEY_PATH = 'download_key_path';

    const SURVEY_DOWNLOAD_DESCRIPTION = 'Your Survey download has completed';

    const SURVEY_DOWNLOAD = 'Survey_Download';

    const COHORT_DETAILS_NOT_FOUND = 'Cohort Details Not Found';

    const COHORT_DETAILS_NOT_FOUND_CODE = 'Cohort_Details_Not_Found';

    const MAX_SCALE_POSTFIX = ' -- maxscale route to server slave2';

    const ENTITY_TYPE_STUDENT = 'student';

    const PERSON = 'person';

    const DATE_TIME = 'DateTime';

    const SURVEYCOHORT = 'SurveyCohort';

    const RECIEVESURVEY = 'ReceiveSurvey';

    const PRIMARY_CONNECT = 'PrimaryConnect';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_EBI_METADATA_REPO = 'SynapseCoreBundle:PersonEbiMetadata';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_ORG_METADATA_REPO = 'SynapseCoreBundle:PersonOrgMetadata';

    const DATETIME = 'Y-m-d H:i:s';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SURVEY_QUESTIONS_REPOR = 'SynapseSurveyBundle:SurveyQuestions';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ORGANIZATION_SERVICE = "org_service";

    const QUERY_ERROR = 'System encountered an unexpected error. Please contact Mapworks support team.';

    const SECTION_EXIST = 'Section Name alreay exists';

    const SECTION_EXIST_KEY = 'section_name_exist';

    const SECTION_ELEMENT_NOT_FOUND = 'Section Element Not Found';

    const SECTION_ELEMENT_NOT_FOUND_KEY = 'section_element_not_found';

    const TIP_NOT_FOUND = 'Tip Not Found';

    const TIP_NOT_FOUND_KEY = 'tip_not_found';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const DATABLOCK_QUESTION_REPO = 'SynapseCoreBundle:DatablockQuestions';

    const INPUT_CHAR_SET = 'UTF-8';

    const OUTPUT_CHAR_SET = 'ISO-8859-2';

    const KEY_CATEGORY = 'D';

    const CATEGORY = 'Category';

    const KEY_SCALED = 'Q';

    const SCALED = 'Scaled';

    const KEY_MR = 'MR';

    const MULTIRESPONSE = 'Multiple Response';

    const KEY_LA = 'LA';

    const LONGANSWER = 'Long Answer';

    const KEY_SA = 'SA';

    const SHORTANSWER = 'Short Answer';

    const KEY_NUMERIC = 'NA';

    const NUMERIC = 'Numeric';

    const ORG_QUESTION_TYPE = 'org_question_type';

    const ORG_QUESTION_ID = 'org_question_id';

    const ORG_QUESTION_TEXT = 'org_ques_text';

    const ORG_OPTION_VALUE = 'org_option_value';

    const EBI_QUESTION_ID = 'ebi_option_id';

    const ORG_OPTION_TEXT = 'org_option_text';

    const FACTOR_ID = 'factor_id';

    const FACTOR_NAME = 'factor_name';

    const REPOSITORY_RESOLVER = 'repository_resolver';

    const EXPORT_CSV = "export_csvs";

    const UPLOAD_HISTORY_PAGE_DOWNLOAD = 'Upload_History_Page_Download';

    const UPLOAD_HISTORY_PAGE_DESCRIPTION = 'Your upload history page download has completed';

    const ORG_OPTION_ID = 'org_option_id';

    const SEQUENCE = 'sequence';

    const EBI_CONFIG_R_SCRIPT_HOST_KEY = 'R_Host';

    const EBI_CONFIG_R_SCRIPT_SYSTEM_PATH_KEY = 'R_Script_Path';

    const ISP_FULL_NAME = 'Institution-Specific Profile Item';

    const PROFILE_ITEM_FULL_NAME = 'Profile Item';

    const ISQ_FULL_NAME = 'Institution-Specific Survey Question';

    const SURVEY_FULL_NAME = 'Survey Question';

    const REPORT_CSV_ERROR = 'The report does not have required information to generate a CSV file. Please contact Skyfactor Client Services.';

    const INVALID_METADATA_TYPE_ERROR_MESSAGE = 'Invalid metadata type';
}

