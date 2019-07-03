<?php
namespace Synapse\StaticListBundle\Util\Constants;

class StaticListConstant
{

    const ORGANIZATION_NOT_FOUND = "Organization not found";

    const ORGANIZATION_NOT_FOUND_KEY = 'organization_not_found';

    const FACULTY_NOT_FOUND = "Faculty not found";

    const FACULTY_NOT_FOUND_KEY = 'faculty_not_found';

    const ORG_NOT_FOUND = "Organization Not Found";

    const ORG_NOT_FOUND_KEY = 'organization_not_found';

    const STUDENT_NOT_FOUND = "Student Not Found";

    const STUDENT_NOT_FOUND_KEY = "student_not_found";

    const STATICLIST_NOT_FOUND = "Staticlist Not Found";

    const STATICLIST_NOT_FOUND_KEY = "staticlist_not_found";

    const STATICLIST_120_CHARS_VALIDATION = "Static List Name should not be greater than 120 characters. ";

    const STATICLIST_120_CHARS_VALIDATION_KEY = "static_list_name_should_not_be_greater_than_120_characters";

    const STATICLIST_DESC_VALIDATION = "Static List Description should not be greater than 350 characters. ";

    const STATICLIST_DESC_VALIDATION_KEY = "Static_List_Description should not be greater than 350_characters";

    const NOT_AUTH_PERSON = "You are not authorized person! ";

    const NOT_AUTH_PERSON_KEY = "You_are_not_authorized_person!";

    const STATICLIST_ALREADY_SHARED = "You have already shared this Static List. ";

    const STATICLIST_ALREADY_SHARED_KEY = "You_have_already_shared_this_Static_List";

    const STATICLIST_EMPTY_VALIDATE = "This list name should not be empty. ";

    const STATICLIST_EMPTY_VALIDATE_KEY = "This_list_name_should_not_be_empty";

    const STATICLIST_DUPLICATE_VALIDATE = "This list already exists. Please give the list a unique name. ";

    const STATICLIST_DUPLICATE_VALIDATE_KEY = "This list already exists. Please give the list a unique name. ";

    const INVALID_STUDENT = "Invalid Student ID. ";

    const INVALID_STUDENT_KEY = "Invalid_Student_ID";

    const STATICLIST_ALREADY_ADDED = "This Student has already been added to the Static List ";

    const STATICLIST_ALREADY_ADDED_KEY = "You_have_already_added_the_student_to_the_Static_List";

    const RECORD_DOESNT_EXIST = "'Record doesn't exist'";

    const RECORD_DOESNT_EXIST_KEY = "Record_doesnt_exist";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_REPO = "SynapseCoreBundle:Person";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_STUDENT_REPO = "SynapseCoreBundle:OrgPersonStudent";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_FACULTY_REPO = "SynapseCoreBundle:OrgPersonFaculty";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORGANIZATION_REPO = "SynapseCoreBundle:Organization";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const STATICLIST_REPO = "SynapseStaticListBundle:OrgStaticList";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const STATICLIST_STUDENTS_REPO = "SynapseStaticListBundle:OrgStaticListStudents";

    const STATICLIST_RISK_LEVEL = 'risk_level';

    const STATICLIST_INTENT_TEXT = 'intent_text';

    const STATICLIST_RISK_IMAGENAME = 'risk_imagename';

    const STATICLIST_RISK_TEXT = 'risk_text';

    const ERRORS = 'errors';

    const VALUE = 'value';

    const ORGANIZATION = "Organization";

    const ORGID = "orgId";

    const USERID = "userId";

    const STATICLISTID = "staticListId";

    const UPLOADID = "uploadId";

    const JOBSTATUS = "jobStatus";

    const FINISHED = "finished";

    const PERSON = 'person';

    const ORGN = 'organization';

    const NAME = 'name';

    const CREATED_BY = 'createdBy';

    const MODIFIED_BY = 'modified_by';

    const USER_FIRSTNAME = 'user_firstname';

    const USER_LASTNAME = 'user_lastname';

    const ORG_STATIC_LIST_EQUALS_ID = 'osls.orgStaticList = osl.id';

    const STATIC_LIST_ACTIVITIES_DESCRIPTION = "Your Static List download has completed";
}