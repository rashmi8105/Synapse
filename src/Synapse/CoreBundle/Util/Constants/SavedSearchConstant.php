<?php
namespace Synapse\CoreBundle\Util\Constants;

class SavedSearchConstant
{

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_SEARCH_SHARED_REPO = "SynapseSearchBundle:OrgSearchShared";

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ORGANIZATION_SERVICE = 'org_service';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const PERSON_SERVICE = 'person_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORGSEARCH_REPO = 'SynapseSearchBundle:OrgSearch';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const METADATA_REPO = 'SynapseCoreBundle:MetadataListValues';

    const SAVEDQUERY_ERROR = 'Saved Query Not Found.';

    const SAVEDQUERY_ERKEY = 'savedquery_not_found';

    const ORGN_ERROR = 'Organization Not Found.';

    const ORGN_ERKEY = 'organization_not_found';

    const PERSON_ERROR = 'Person Not Found.';

    const PERSON_ERKEY = 'Person_not_found';

    const NAMEFIELD_EMPTY = 'Name field cannot be empty';

    const NAMELIMIT_CHAR = 'Name cannot be more than 120 character limit';

    const ORGN = 'organization';

    const COURSES = 'courses';

    const META_DATA_TYPE = 'item_data_type';

    const META_DATA_VALUE = ' and metadata_value between ';

    const FIELD_AND = 'AND ';

    const META_DATA_VALUE_EQUALS = ' AND metadata_value = ';

    const DATABLOCKS = 'datablocks';

    const WHERE_ORG_MDATAID = ' where org_metadata_id =';

    const FIELD_ENDDATE = 'end_date';

    const FIELD_STARTDATE = 'start_date';

    const ISPS = 'isps';

    const FIELD_OR = 'OR ';

    const RISKID = 'risk_indicator_ids';

    const INTENTID = 'intent_to_leave_ids';

    const FIELD_START_DATE = 'start_date';

    const FIELD_END_DATE = 'end_date';

    const WHERE_EBI_MDATAID = ' where ebi_metadata_id =';

    const CAT_TYPE = 'category_type';

    const SING_VAL = 'single_value';

    const MIN_DIGITS = 'min_digits';

    const MAX_DIGITS = 'max_digits';

    const DATE_YMD = 'Y-m-d';

    const PROFILE_ITEMS = 'profile_items';

    const WHERE_MDATA_VAL_IN = ' where metadata_value in(';

    const P_RISK_LEVEL = ' and p.risk_level';

    const P_INTENT_TO_LEAVE = ' and p.intent_to_leave';

    const OGS_ORG_GROUPID = ' and ogs.org_group_id';

    const GROUP_IDS = 'group_ids';

    const REF_STATUS = 'referral_status';

    const REFSTATUS = "referralstatus";

    const CONTACT_TYPE = 'contact_types';

    const CONTACTTYPES = "contacttypes";

    const PARENT_CON_TYPES_ID = ' and (ct.parent_contact_types_id';

    const OR_R_STATUS = ' or r.status';

    const CT_PARENT_CONTYPES_ID = ' and ct.parent_contact_types_id';

    const R_STATUS = ' and r.status';

    const PROFILE_BLOCK_ID = 'profile_block_id';

    const GF_PERSON_ID = " and gf.person_id=";

    const GROUP_BY_PID = " group by (p.id)";
}