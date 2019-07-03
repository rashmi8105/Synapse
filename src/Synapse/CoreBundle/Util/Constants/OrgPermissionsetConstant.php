<?php
namespace Synapse\CoreBundle\Util\Constants;

class OrgPermissionsetConstant
{

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERMISSIONSET_REPO = "SynapseCoreBundle:OrgPermissionset";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const FEATURE_MASTER_REPO = "SynapseCoreBundle:FeatureMaster";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_METADAT_REPO = "SynapseCoreBundle:OrgMetadata";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const DATABLOCK_MASTER_REPO = "SynapseCoreBundle:DatablockMaster";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const DATABLOCK_MASTER_LANG_REPO = "SynapseCoreBundle:DatablockMasterLang";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERMISSIONSET_DATABLOCK_REPO = "SynapseCoreBundle:OrgPermissionsetDatablock";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_QUESTION_REPO = "SynapseCoreBundle:OrgQuestion";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERMISSIONSET_METADATA_REPO = "SynapseCoreBundle:OrgPermissionsetMetadata";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERMISSIONSET_QUESTION_REPO = "SynapseCoreBundle:OrgPermissionsetQuestion";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_FEATURES_REPO = "SynapseCoreBundle:OrgFeatures";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERMISSIONSET_FEATURES_REPO = "SynapseCoreBundle:OrgPermissionsetFeatures";

    const ORG_GROUP_FACULTY_REPO = "SynapseCoreBundle:OrgGroupFaculty";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_GROUP_STUDENT_REPO = "SynapseCoreBundle:OrgGroupStudents";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_COURSE_FACULTY_REPO = "SynapseAcademicBundle:OrgCourseFaculty";

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const RBC_MANAGER = "tinyrbac.manager";

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ORG_SERVICE = "org_service";

    const ERROR_PERMISSIONSET_NOT_FOUND = "Permissionset Not Found";

    const ERROR_KEY = "EFEA001";

    const ERROR_DATABLOCK_NOT_FOUND = "DataBlock Not Found";

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const PERMISSION_SET_SERVICE = "permissionset_service";

    const PERMISSION_TEMPLATE = 'permission_template';

    const ORG_PERMISSION_SET = 'orgPermissionset';

    const FEATURE = "feature";

    const ORGANIZATION = "organization";

    const ORGANIZATION_ID = 'organization_id';

    const PERMISSION_TEMPLATE_LAST_UPDATED = 'permission_template_last_updated';

    const PERMISSION_TEMPLATES = 'permission_templates';

    const PROFILE = 'profile';

    const SURVEY = 'survey';

    const DATA_BLOCKS = 'data_blocks';

    const ACCESS_LEVEL = 'accessLevel';

    const PROFILE_BLOCKS = 'profileBlocks';

    const SURVEY_BLOCKS = 'surveyBlocks';

    const FEATURES = 'features';

    const BLOCK_ID = 'block_id';

    const BLOCK_NAME = 'block_name';

    const PERMISSION_SET = 'permission_set';

    const ACCESSLEVEL = 'access_level';

    const PUBLIC_SHARE = 'public_share';

    const PRIVATE_SHARE = 'private_share';

    const TEAMS_SHARE = 'teams_share';

    const PUBLIC_VIEW = 'public-view';

    const PRIVATE_VIEW = 'private-view';

    const TEAMS_VIEW = 'teams-view';

    const PUBLIC_CREATE = 'public-create';

    const PRIVATE_CREATE = 'private-create';

    const TEAMS_CREATE = 'teams-create';

    const CREATE = 'create';

    const ISP_NOT_FOUND = 'ISP Not Found';

    const FEATURE_NOT_FOUND = 'Feature Not Found';

    const FIELD_VALUE = 'value';

    const INDIVIDUAL_AND_AGGREGATE = 'individualAndAggregate';

    const AGGREGATE_ONLY = 'aggregateOnly';

    const INTENTTOLEAVE = 'intentToLeave';

    const INTENT_TO_LEAVE = 'intent_to_leave';

    const RISK_INDICATOR = 'risk_indicator';

    const RISK_INDICATOR_VAL = 'riskIndicatorVal';

    const RECIEVE_REFERRALS = 'receive_referrals';

    const PERSON_SERVICE = 'person_service';

    const COURSES_ACCESS = 'coursesAccess';
    
    const VIEW_COURSES = 'viewCourses';
    
    const CREATE_VIEW_ACADEMIC_UPDATE = 'createViewAcademicUpdate';
    
    const VIEW_ALL_ACADEMIC_UPDATE_COURSES = 'viewAllAcademicUpdateCourses';
    
    const VIEW_ALL_FINAL_GRADES = 'viewAllFinalGrades';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERMISSIONSET_REPORTACCESS_REPO = "SynapseCoreBundle:OrgReportPermissions";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const REPORTS_REPO = "SynapseReportsBundle:Reports";
    
    const ERROR_REPORT_NOT_FOUND = "Report Not Found";

    const REFERRAL_FEATURE_ID = 1;
}