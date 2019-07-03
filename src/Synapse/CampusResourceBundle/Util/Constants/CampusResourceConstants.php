<?php
namespace Synapse\CampusResourceBundle\Util\Constants;

class CampusResourceConstants
{

    /**
     * The fantastic element that explains the appeal of games to many
     * developers is neither the fire-breathing dragons nor the
     * scantily clad sirens, it is the experience of carrying out a task
     * from start to finish without any change in user requirements.
     *
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_CAMPUS_RESOURCE_ENT = 'SynapseCampusResourceBundle:OrgCampusResource';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORGANIZATION_ENT = 'SynapseCoreBundle:Organization';

    const ORGANIZATION_NOT_FOUND_CODE = 'organization_not_found';

    const ORGANIZATION_NOT_FOUND = 'Organization ID Not Found';

    const CAMPUS_RESOURCE_DB_EXCEPTION = "Database Error";

    const CAMPUS_RESOURCE_DB_EXCEPTION_CODE = "db_error";

    const CAMPUS_RESC_NOT_FOUND = 'Campus Resource is Not Found';

    const CAMPUS_RESC_NOT_FOUND_CODE = "campus_resource_not_found";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_REPO = 'SynapseCoreBundle:Person';

    const PERSON_NOT_FOUND = 'Person Not Found';

    const PERSON_NOT_FOUND_CODE = 'person_key_not_found';

    const RESOURCE_NAME_ALREADY_FOUND = 'Resource Name already found';

    const RESOURCE_NAME_ALREADY_FOUND_CODE = 'resource_name_already_found';

    const RESOURCE_CREATE_URL = 'campusresources';

    const STUDENT_RESOURCE_URL = 'students';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_STUDENT_REPO = 'SynapseCoreBundle:OrgPersonStudent';

    const PERSON_KEY = 'person';

    const CAMPUS_RES_ERR_2570 = "Campus Resources -  Add campus resources - CAMPUS_RES_ERR_2570";

    const CAMPUS_RES_ERR_2570_UPDATE = "Campus Resources -  Update campus resources - CAMPUS_RES_ERR_2570";

    const CAMPUS_RES_ERR_2570_DELETE = "Campus Resources -  Delete campus resources - CAMPUS_RES_ERR_2570";

    const CAMPUS_RES_ERR_2570_GET = "Campus Resources -  Get Single campus resources - CAMPUS_RES_ERR_2570";

    const CAMPUS_RES_ERR_2153 = "Campus Resources -  Get a list of campus resources - CAMPUS_RES_ERR_2153";

    const STUDENT_VIEW_ESPRJ_2122 = "Student View - Get all campus resources for a student - STUDENT_VIEW_ESPRJ_2122";

    const MESSAGE_COMPLETED = " - Completed";

    const FIELD_FIRSTNAME = "firstname";

    const FIELD_LASTNAME = "lastname";

    const FIELD_RESOURCE_PHONE_NUMBER = "resource_phone_number";

    const FIELD_RESOURCE_EMAIL = "resource_email";

    const FIELD_RESOURCE_LOCATION = "resource_location";

    const FIELD_RESOURCE_URL = "resource_url";

    const FIELD_RESOURCE_DESCRIPTION = "resource_description";

    const FIELD_RECEIVE_REFERALS = "receive_referals";

    const FIELD_VISIABLE_TO_STUDENETS = "visible_to_students";

    const FIELD_ID = "id";

    const FIELD_RESOURCE_NAME = "resource_name";

    const ORGANIZATION_ID = "organizationId";

    const ORG_ID = 'orgId';

    const OCR_ORG_ID = 'ocr.orgId = :orgId';

    const P_FIRST_NAME = 'p.firstname as firstname';

    const P_LAST_NAME = 'p.lastname as lastname';
	
	const OCR_NAME = 'ocr.name as resource_name';

    const P_STAFF_ID = 'p.id as staff_id';

    const OCRPERSON_ID = 'p.id = ocr.personId';

    const OCRORG_ID = 'o.id = ocr.orgId';
    
    const FIELD_NAME = 'name';
}