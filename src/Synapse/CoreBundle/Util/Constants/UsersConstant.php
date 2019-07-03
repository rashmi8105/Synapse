<?php
namespace Synapse\CoreBundle\Util\Constants;

class UsersConstant
{

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ROLE_LANG_REPO = 'SynapseCoreBundle:RoleLang';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_ROLE_REPO = "SynapseCoreBundle:OrganizationRole";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ROLE_REPO = "SynapseCoreBundle:Role";

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
    const ORG_ACADEMIC_REPO = "SynapseAcademicBundle:OrgAcademicYear";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_FACULTY_REPO = 'SynapseCoreBundle:OrgPersonFaculty';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_STUDENT_REPO = "SynapseCoreBundle:OrgPersonStudent";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBICONFIG_REPO = 'SynapseCoreBundle:EbiConfig';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EMAIL_TEMP_REPO = 'SynapseCoreBundle:EmailTemplateLang';

    const SUPPORT_HELPDESK = "Support_Helpdesk_Email_Address";

    const FIELD_SUBJECT = "subject";

    const EMAIL_ADDRESS = "email_detail";

    const FIELD_EMAILKEY = "emailKey";

    const FIELD_ORGID = "organizationId";

    const FIELD_PERID = "personId";

    const FIELD_EXTERNAL_ID = 'externalId';

    const FILTER_STANDALONE = 'standalone';

    const FIELD_MESSAGE = "message";

    const PROFILE_NOT_FOUND = 'Profile not found .';

    const FIELD_COORDINATORS = 'coordinators';

    const FIELD_PERSON = "person";

    const FIELD_ROLEID = "roleid";

    const FIELD_ID = "id";

    const ERROR_ROLE_NOT_FOUND = "Role Not Found.";

    const EXTERNALID_ALREADY_FOUND = "User already exists in the system";

    const EXTERNALID_ALREADY_FOUND_KEY = "user_already_exists_in_the_system";

    const FIELD_ROLE = "role";

    const FIELD_TYPE = "user_type";

    const FIELD_ORGANIZATION = 'organization';

    const FIELD_FIRSTNAME = 'firstname';

    const FIELD_LASTNAME = 'lastname';

    const FILED_TITLE = 'title';

    const FIELD_EMAIL = 'email';

    const FIELD_ACTIVE = 'is_active';

    const FIELD_EXTERNALID = 'externalid';

    const STATUS = 'status';

    const ORG_NOT_FOUND = 'Organization Not Found.';

    const FIELD_WELCOME_EMAIL_SENT_DATE = "welcome_email_sentDate";

    const FIELD_PHONE = "phone";

    const FIELD_ISMOBILE = "ismobile";

    const FIELD_LASTUPDATED = 'last_updated';

    const FIELD_LDAP_USERNAME = 'ldap_username';

    const ERROR_PERSON_NOT_FOUND = "Person Not Found.";

    const ERROR_PERSON_NOT_FOUND_KEY = "person_not_found";

    const ERROR_PERSON_LOCKED = "We are unable to delete users who have activity or academic data associated with their Mapworks account.";

    const ERROR_PERSON_LOCKED_KEY = "person_locked";

    const ERROR_PERSON_NOT_COORDINATOR = "Person Not a Coodinator.";

    const ERROR_PERSON_NOT_COORDINATOR_KEY = "person_not_a_coodinato";

    const ERROR_INVALID_TYPE_FOUND = "Invalid Type";

    const ERROR_INVALID_TYPE_FOUND_KEY = "invalid_type_key";

    const FILTER_COORDINATOR = "coordinator";

    const FILTER_FACULTY = "faculty";

    const FILTER_STUDENT = "student";

    const ORGANIZATION_NOT_FOUND_CODE = 'organization_not_found';

    const ORGANIZATION_NOT_FOUND = 'Organization ID Not Found';

    const EMAIL_TEMPLATE_KEY = 'Welcome_To_Mapworks';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_USERS_REPO = "SynapseMultiCampusBundle:OrgUsers";

    const ERROR_NOT_TIER_USER = 'Not a Tier User';

    const ERROR_NOT_TIER = 'Not a Tier';

    const CONFLICT_RECORDS_NOT_FOUND = 'No Conflict Records Found';

    const ERROR_INVALID_PARAMS = "Invalid Params Passed";

    const ERROR_INVALID_PARAMS_KEY = "invalid_params_key";

    const CURRENT_ACADEMIC_YEAR_NOT_FOUND = "Current Academic Year not found";

    const CURRENT_ACADEMIC_YEAR_NOT_FOUND_KEY = "current_academic_year_not_found";

    const NO_OF_USER_INVITED = 'no_of_users_invited';

    const FORM_VALIDATOR = 'validator';

    const PERSON_CONTACT = 'contacts';

    const ORG_ID = 'orgId';

    const ORGANIZATION_ID = 'organization_id';

    const ORGANIZATION = 'organization';

    const EXTERNAL_ID = 'external_id';

    const PRIMARY_EMAIL = 'primary_email';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_LANG = 'SynapseCoreBundle:OrganizationLang';

    const ORG_LAN = 'orglan';

    const ORGID_ORGLAN_ORG = 'org.id = orglan.organization';

    const CAMPUSID = 'campus_id';

    const TIER_LEVEL = 'tier_level';

    const PRIMARY_TIER_NAME = 'primary_tier_name';

    const SECONDARY_TIER_NAME = 'secondary_tier_name';

    const CAMPUS_NAME = 'campus_name';

    const PRIMARYNAME = 'primaryName';

    const SECONDARYNAME = 'secondaryName';

    const CAMPUSNAME = 'campusName';

    const CAMPUS = 'campus';

    const ARRAY_MERGE = 'array_merge';

    const TIER_ID = 'tier-id';

    const CAMPUS_I_D = 'campus-id';

    const PERMISSION_TEMPLATES = 'permission_template_names';

    const PERMISSION_TEMPLATE_NAME = 'permission_template_name';

    const COORDINATOR_ACCESS_DENIED = "You do not have coordinator access";

    const FACULTY_STAFF = 'Staff/Faculty';

    const OTHER_CAMPUS_PERMISSION_DENIED = 'Your Not Allowed to Send Invitation to other Campus';

    const OTHER_CAMPUS_PERMISSION_DENIED_KEY = 'Your_Not_Allowed_to_Send_Invitation_to_other_Campus';

    const EBI_ADMIN_ORG_ID = '-1';

    const TYPE = 'type';

    const ERROR_PERSON_STUDENT_NOT_FOUND = 'Student Role not found';

    const ERROR_PERSON_FACULTY_NOT_FOUND = 'Faculty Role not found';

    const ERROR_PERSON_COORDINATOR_NOT_FOUND = 'Coordinator Role not found';

	const ERROR_NOT_VALIDE_ORG_PERSON = 'Person is not belongs to this organization';

	const ERROR_NOT_VALIDE_ORG_PERSON_KEY = 'Person_is_not_belongs_to_this_organization';

    const PAGE_NO = 1;

    const OFFSET = 25;
    
    const EXTERNALID_EXISTS = "ID already exist.";
    
    const EXTERNALID_EXISTS_KEY = "ID_already_exist.";
    
    const PERSON_EMAIL_EXISTS = "email already exist.";
    
    const PERSON_EMAIL_EXISTS_KEY = "email_already_exist.";

    const FIELD_USER_NAME = 'username';
    
}