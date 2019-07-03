<?php
namespace Synapse\CoreBundle\Util\Constants;

class SearchConstant
{

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ORGANIZATION_SERVICE = "org_service";

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const PERSON_SERVICE = 'person_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORGSEARCH_REPO = 'SynapseSearchBundle:OrgSearch';

    const ORGN_ERROR = 'Organization Not Found.';

    const ORGN_ERKEY = 'organization_not_found';

    const COURSES = 'courses';
    
    const META_DATA_TYPE = 'item_data_type';

    const META_DATA_VALUE = ' AND metadata_value between ';

    const FIELD_AND = 'AND ';

    const META_DATA_VALUE_EQUALS = ' AND metadata_value = ';

    const FIELD_START_DATE = 'start_date';

    const FIELD_END_DATE = 'end_date';

    const WHERE_ORG_MDATAID = ' where org_metadata_id =';

    const WHERE_EBI_MDATAID = ' where ebi_metadata_id =';

    const ISPS = 'isps';

    const DATABLOCKS = 'datablocks';

    const FIELD_OR = 'AND ';

    const RISKID = 'risk_indicator_ids';

    const INTENTID = 'intent_to_leave_ids';

    const CAT_TYPE = 'category_type';

    const SING_VAL = 'single_value';

    const MIN_DIGITS = 'min_digits';

    const MAX_DIGITS = 'max_digits';
    
    const STUDENT_IDS = 'student_ids';

    const DATE_YMD = 'j/n/Y';

    const PROFILE_ITEMS = 'profile_items';

    const QUERY_ERROR = 'System encountered an unexpected error. Please contact Mapworks support team.';
}