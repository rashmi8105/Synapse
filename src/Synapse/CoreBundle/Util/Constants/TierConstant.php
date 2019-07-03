<?php
namespace Synapse\CoreBundle\Util\Constants;

class TierConstant
{

    const PRIMARY_TIER = "primary";

    const SECONDARY_TIER = "secondary";

    const TIER_NOT_FOUND = "Tier Not Found.";

    const ERROR_PRIMARY_TIER_NOT_FOUND = "Primary Tier Not Found.";

    const ERROR_SECONDARY_TIER_NOT_FOUND = "Secondary Tier Not Found.";

    const ERROR_TIER_NOT_FOUND_KEY = "tier_not_found";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_CONFLICT_REPO = 'SynapseMultiCampusBundle:OrgConflict';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EMAILTEMPLATE_REPO = 'SynapseCoreBundle:EmailTemplateLang';

    const ERROR_PRIMARY_TIER_NOT_FOUND_KEY = "primary_tier_not_found";

    const ERROR_SECONDARY_TIER_NOT_FOUND_KEY = "secondary_tier_not_found";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const TIER_REPO = "SynapseCoreBundle:Organization";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const TIER_DET_REPO = "SynapseCoreBundle:OrganizationLang";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const TIER_ORG_USERS = "SynapseMultiCampusBundle:OrgUsers";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON = "SynapseCoreBundle:Person";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const CONTACT_INFO = "SynapseCoreBundle:ContactInfo";

    const NAMEFIELD_EMPTY = 'Name field cannot be empty';

    const NAMELIMIT_CHAR = 'Name cannot be more than 140 character limit';

    const STRING_ACTIVE = 'Active';

    const STRING_VALIDATOR = 'validator';

    const ERROR_KEY_CAMPUS = 'create_campus_error';

    const ERROR_CAMPUS_NOT_FOUND = 'Campus not found';

    const ORGID = 'orgId';

    const CAMPUSID = 'campusId';

    const ORGNAME = 'organizationName';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_ROLE_REPO = "SynapseCoreBundle:OrganizationRole";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_FACULTY_REPO = 'SynapseCoreBundle:OrgPersonFaculty';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_STUDENT_REPO = 'SynapseCoreBundle:OrgPersonStudent';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const PERSON_SERVICE = 'person_service';

    const ERROR_TIER_NOT_FOUND = "Tier Not Found";

    const ERROR_CAMPUS_NOT_FOUND_KEY = 'campus_not_found';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_CHANGE_REQUEST_REPO = 'SynapseMultiCampusBundle:OrgChangeRequest';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ROLE_LANG_REPO = 'SynapseCoreBundle:RoleLang';

    const INVALID_PERSON = 'Invalid requested person';

    const PERSON_FIELD = 'person';

    const ERROR_PERSON_NOT_FOUND = 'Person Not Found';

    const ERROR_PERSON_NOT_FOUND_KEY = 'Person_not_found';

    const INVALID_SOURCE_CAMPUS = 'Invalid source campus';

    const INVALID_SOURCE_CAMPUS_KEY = 'Invalid_source_campus';

    const INVALID_DEST_CAMPUS = 'Invalid destination campus';

    const INVALID_DEST_CAMPUS_KEY = 'invalid_destination_campus';

    const INVALID_REQUESTED_PERSON = 'invalid_requested_person';

    const INVALID_REQUESTED_ID = 'Invalid request ID';

    const INVALID_REQUESTED_ID_KEY = 'invalid_request_id';

    const STUDENT_ALREADY_EXIST = 'Student already belongs to this Campus';

    const STUDENT_ALREADY_EXIST_KEY = 'student_already_found';

    const INVALID_CAMPUS = 'Invalid Campus';

    const INVALID_CAMPUS_KEY = 'Invalid_campus_key';

    const INVALID_PARENT_TIER = 'Invalid Parent tier';

    const INVALID_PARENT_TIER_KEY = 'invalid_parent_tier_error';

    const CAMPUS_NOT_EXIST = 'Campus does not exist';

    const CAMPUS_NOT_EXIST_ERROR = 'campus_not_exist_error';

    const EMAIL_DETAIL = 'email_detail';

    const EMAIL_TEMPLATE_KEY = 'Send_Invitation_to_User';

    const SUBDOMAIN = 'subdomain';

    const FUNC_ARRAY_MERGE = 'array_merge';

    const FIELD_FIRSTNAME = 'firstname';

    const FIELD_LASTNAME = 'lastname';

    const FIELD_EMAIL = 'email';

    const FIELD_EXTERNALID = 'externalId';

    const FIELD_STUDENT = 'student';

    const FIELD_FACULTY = 'faculty';

    const FIELD_HYBRID = 'hybrid';

    const FIELD_CONSTANT = 'conflicts';

    const FIELD_STAFF = 'staff';

    const FIELD_CONFLICTID = 'conflict_id';

    const FIELD_USERTYPE = 'user_type';

    const FIELD_PERSONID = 'person_id';

    const FIELD_ISSOLO = 'is_solo_campus';

    const FIELD_ORGID = 'org_id';

    const FIELD_CAMPUSID = 'campus_id';

    const FIELD_OTHER = 'other';

    const FIELD_MASTER = 'master';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ORG_SERVICE = 'org_service';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const EMAIL_SERVICE = 'email_service';

    const IS_HOME = 'is_home';

    const IS_HIERARCHY = 'is_hierarchy';

    const IS_MASTER = 'is_master';

    const FIELD_ORGANIZATION = 'organization';

    const FIELD_PERSON = 'person';

    const ORG_ID = 'orgId';

    const FACULTY_ID = 'facultyId';

    const STUDENT_ID = 'studentId';

    const EXISTS = 'exists';

    const MERGED = 'merged';

    const CONFLICTS = 'conflicts';

    const USER_CONFLICTS = 'user_conflicts';

    const CONFLICT_CATEGORY = 'conflict_category';

    const CREATED_ON = 'createdon';

    const YMD = 'Y-m-d';

    const PARENT_ORG_ID = 'parentOrganizationId';

    const CONFLICT_DEST_ORG_ID = 'conflicts.dstOrgId = :destinationId';

    const CONFLICT_SOURCE_ORG_ID = 'conflicts.srcOrgId = :sourceId';

    const SOURCE_ID = 'sourceId';

    const DESTINATION_ID = 'destinationId';

    const CONTACTS = 'p.contacts';

    const CONFLICTS_P_C = 'conflicts, p, c';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_CONFLICT_ENTITY = 'SynapseMultiCampusBundle:OrgConflict';

    const TIERLEVEL = 'tier-level';

    const CONFLICT_RECORDS = 'conflict_records';
}