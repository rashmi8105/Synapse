<?php
namespace Synapse\CoreBundle\Util\Constants;

class SurveyConstant
{

    const FIELD_STARTDATE = "start_date";

    const FIELD_ENDDATE = "end_date";

    const COHORT_NAME = "cohort_name";

    const FIELD_TOTALSTUDENTS = "total_students";

    const FIELD_RESPONDED = "responded";

    const FIELD_NOTRESPONDED = "not_responded";

    const DATE_FORMAT = "Y-m-d H:i:s";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SYNAPSE_PERSON_ENTITY = 'SynapseCoreBundle:Person';

    const PERSON_NOT_FOUND = 'Person Not Found.';

    const PERSON_NOT_FOUND_KEY = 'person_not_found.';

    const STUDENT_NOT_FOUND = 'Student Not Found.';

    const STUDENT_NOT_FOUND_KEY = 'student_not_found.';

    const WESSLINK_NOT_FOUND = 'WessLink Not Found.';

    const WESSLINK_NOT_FOUND_KEY = 'wesslink_not_found.';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SURVEY_RESPONSE_ENTITY = 'SynapseSurveyBundle:SurveyResponse';

    const SURVEY_ID = 'survey_id';

    const SURVEY_NAME = 'survey_name';

    const SURVEY_NOT_FOUND = 'Survey Not Found.';

    const SURVEY_NOT_FOUND_KEY = 'survey_not_found.';

    const SURVEY_QUE_ID = 'survey_que_id';

    const QUESTION_TEXT = 'question_text';

    const RESPONSE_TYPE = 'response_type';

    const DECIMAL = 'decimal';

    const DECIMAL_VALUE = 'decimalValue';

    const DECIMAL_VAL = 'decimal_value';

    const CHAR = 'char';

    const COHORT_CODE = 'cohort_code';

    const ID = 'id';

    const OPEN_DATE = 'open_date';

    const STATUS = 'status';

    const CLOSE_DATE = 'close_date';

    const WESS_LINK = 'wess_admin_link';

    const SURVEY = 'survey';

    const SURVEY_START_DATE = 'survey_start_date';

    const SURVEY_END_DATE = 'survey_end_date';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const WESS_LINK_ENTITY = 'SynapseSurveyBundle:WessLink';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SYNAPSE_PERSON_STUDENT_ENTITY = 'SynapseCoreBundle:OrgPersonStudent';

    const ORGANIZATION = 'organization';

    const PERSON = 'person';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SYNAPSE_ACADEMIC_YEAR_ENTITY = 'SynapseAcademicBundle:OrgAcademicYear';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SYNAPSE_EBI_CONFIG_ENTITY = 'SynapseCoreBundle:EbiConfig';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SYNAPSE_ORGANIZATION_ENTITY = 'SynapseCoreBundle:Organization';

    const CAMPUS_ID = 'campusId';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SYNAPSE_SURVEY_ENTITY = 'SynapseCoreBundle:Survey';

    const INVALID_ORGANIZATION = "Invalid Organization";

    const NO_ACADEMIC_YEAR_FOUND = "No Academic year found";

    const INVALID_PERSON_ID = "Invalid Person Id";

    const INVALID_SURVEY_ID = "Invalid Survey Id";

    const AND_ORG_ID = " and OrgId";

    const INVALID_ACADEMIC_TERM_FOR_YEAR = "Invalid Academic term for year:";
    
    const COURSES_ACCESS = 'coursesAccess';
}
