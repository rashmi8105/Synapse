<?php
namespace Synapse\CoreBundle\Util\Constants;

class PersonConstant
{

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_REPO = 'SynapseCoreBundle:Person';

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
    const ORG_REPO = "SynapseCoreBundle:Organization";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const CONTACT_REPO = "SynapseCoreBundle:ContactInfo";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EMAIL_TEMPLATE_LANG_REPO = "SynapseCoreBundle:EmailTemplateLang";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const LANGUAGE_MASTER_REPO = "SynapseCoreBundle:LanguageMaster";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_FACULTY_REPO = "SynapseCoreBundle:OrgPersonFaculty";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_STUDENT = "SynapseCoreBundle:OrgPersonStudent";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ROLE_REPO = "SynapseCoreBundle:Role";

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ORG_LANG_SERVICE = 'organizationlang_service';

    const ERROR_PERSON_NOT_FOUND = "Person Not Found.";

    const ERROR_PERSON_NOT_FOUND_KEY = "person_not_found";

    const ERROR_ROLE_NOT_FOUND = "Role Not Found.";

    const FIELD_PERSON = "person";

    const FIELD_ROLEID = "roleid";

    const FIELD_ORGANIZATION = 'organization';

    const FIELD_FIRSTNAME = 'firstname';

    const FIELD_WELCOME_EMAIL_SENT_DATE = "welcome_email_sentDate";

    const FIELD_PHONE = "phone";

    const FIELD_ISMOBILE = "ismobile";

    const FIELD_LASTUPDATED = 'last_updated';

    const FIELD_COORDINATORS = 'coordinators';

    const FIELD_LASTNAME = 'lastname';

    const FILED_TITLE = 'title';

    const FIELD_EMAIL = 'email';

    const ORG_NOT_FOUND = 'Organization Not Found.';

    const PERSON_LASTNAME = 'p.lastname';

    const PERSON_FIRSTNAME = 'p.firstname';

    const PERSON_ENTITIES = 'p.entities';

    const PERSON_ORG = 'p.organization';

    const ID_EQUAL_ENTITY = 'e.id = :entity';

    const ID_EQUAL_ORG = 'o.id = :organization';

    const ENTITY = 'entity';

    const PERSON_CONTACTS = 'p.contacts';

    const PERSON_EQUAL_ID = 'op.person = p.id';

    const REFERRALS = 'referrals';

    const NOTES = 'notes';

    const LOG_CONTACTS = 'log_contacts';

    const BOOKING = 'booking';

    const FILTER_PRIMARY = 'primary';

    const PRIMARY_COORDINATOR_ROLE_NAME = 'primary coordinator';

    const RISK_TEXT = 'risk_text';

    const INTENT_TEXT = 'intent_text';

    const LOGIN_COUNT = 'login_cnt';

    const LAST_ACTIVITY = 'last_activity';

    const EXT_ID = 'externalId';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_FACULTY = 'SynapseCoreBundle:OrgPersonFaculty';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_STUDENT = 'SynapseCoreBundle:OrgPersonStudent';

    const PERSON = 'person';

    const OPF_ORGANIZATON_EQUAL_ORGID = 'opf.organization = :orgId';

    const ORG_ID = 'orgId';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SYNAPSE_ORG_META_DATA_ENTITY = 'SynapseCoreBundle:OrgMetadata';

    const OM_ID = 'om.id';

    const ID = 'id';

    const ROLE_NAME = 'roleName';

    const FILTER_SECONDARY = 'secondary';

    const STAFF = "Staff";

    const STUDENT = "Student";

    const PERMISSION = 'permission';

    const ORGANIZATION_ID = 'organizationid';

    const ORGID_OPFID_EQUAL = 'o.id = op.organization';

    const ORGIDEQUAL_OP = 'op.organization=:organization';

    const OP_PERSON_ORG = 'op.organization';

}
