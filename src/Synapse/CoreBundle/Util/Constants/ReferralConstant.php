<?php
namespace Synapse\CoreBundle\Util\Constants;

class ReferralConstant
{

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ORG_SERVICE = 'org_service';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const PERSON_SERVICE = 'person_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACTIVITY_REFERENCE_REPO = 'SynapseCoreBundle:ActivityReferenceUnassigned';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_CAMPUS_RESOURCE_REPO = 'SynapseCampusResourceBundle:OrgCampusResource';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const REFERRALS_REPO = 'SynapseCoreBundle:Referrals';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACTIVITY_CATEGORY_REPO = 'SynapseCoreBundle:ActivityCategory';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACTIVITY_CATEGORY_LANG_REPO = 'SynapseCoreBundle:ActivityCategoryLang';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const METADATA_LIST_REPO = 'SynapseCoreBundle:MetadataListValues';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ALERT_SERVICE = 'alertNotifications_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_GROUP_STUDENT_REPO = 'SynapseCoreBundle:OrgGroupStudents';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_FACULTY_REPO = 'SynapseCoreBundle:OrgGroupFaculty';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const REFERRAL_TEAM_REPO = "SynapseCoreBundle:ReferralsTeams";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const REFERRAL_INTR_PARTIES_REPO = 'SynapseCoreBundle:ReferralsInterestedParties';

    const SECURITY_CONTEXT = 'security.context';

    const ACTIVITY_CATEGORY_ID = 'activityCategoryId';

    const FIRST_NAME = 'firstname';

    const LAST_NAME = 'lastname';

    const ID = 'id';

    const STAFF_ID = 'staff_id';

    const FIELD_STUDENTNAME = "student_name";

    const LANG = 'language';

    const EMAIL_KEY = 'email_key';

    const REFERRAL_ASSIGNED = 'Referral_Assigned';

    const REFERRAL_NOT_FOUND = 'Referral Not Found';

    const LAST_ACTIVITY = 'last_activity';

    const RISK_TEXT = 'risk_text';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBICONFIG_REPO = "SynapseCoreBundle:EbiConfig";

    const EMAIL_KEY_STAFF_REFERRALPAGE = "Staff_ReferralPage";

    const FIELD_COORDINATORNAME = "coordinator_name";

    const FIELD_COORDINATOREMAIL = "coordinator_email";

    const FIELD_COORDINATORTITLE = "coordinator_title";

    const EBI_CONGIF_KEY = 'ebi_config_key';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_STUDENT_REPO = 'SynapseCoreBundle:OrgPersonStudent';

    const ORGANIZATION = 'organization';

    const FIRSTNAME = 'first_name';

    const LASTNAME = 'last_name';

    const PRIMARYCOORDINATOR = 'Primary coordinator';

    const REFERRAL_REOPENED = 'Referral_Reopened';

    const PERSONIDSTUDENT = 'person_id_student';

    const RISKLEVEL = 'risk_level';

    const IMAGENAME = 'image_name';

    const LOGIN_COUNT = 'login_cnt';

    const STAFF_FIRSTNAME = 'staff_firstname';

    const EMAIL_SKY_LOGO = "Skyfactor_Mapworks_logo";

    const EBI_SYSTEM_URL = "System_URL";

    const REFERRAL_CLOSED = 'Referral_Closed';

    const REFERRAL_REASSIGNED = 'Referral_Reassigned';

    const REFERRAL_ASSIGN_TO_STAFF = "Referral_Assign_to_staff";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERMISSION_FEATURES_REPO = "SynapseCoreBundle:OrgPermissionsetFeatures";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const REFERRAL_ROUTE_RULE_REPO = 'SynapseCoreBundle:ReferralRoutingRules';

    const FEATURE_SERVICE = 'feature_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const COURSE_FACULTY_REPO = 'SynapseAcademicBundle:OrgCourseFaculty';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const COURSE_STUDENT_REPO = 'SynapseAcademicBundle:OrgCourseStudent';

	const CAMPUSRESOURCE_NAME = 'resource_name';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
	const ORG_PERMISSIONSET_SERVICE = 'orgpermissionset_service';

	const TITLE = 'title';

	const REFERRAL_DOWNLOAD = "Referral_Download";

	const REFERRAL_DESCRIPTION = "Your referral download has completed";

	const USER_KEY = 'user_key';

    const REFERRAL_CLOSING_ERROR_MSG = 'Access denied. You do not have permission to close the referral. ';

    const REFERRAL_CLOSING_ERROR = 'Access_denied_you_do_not_have_permission_to_close_the_referral';

}