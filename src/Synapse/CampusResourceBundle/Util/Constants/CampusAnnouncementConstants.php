<?php
namespace Synapse\CampusResourceBundle\Util\Constants;

class CampusAnnouncementConstants
{

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_ROLE_REPO = "SynapseCoreBundle:OrganizationRole";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const META_LIST_REPO = "SynapseCoreBundle:MetadataListValues";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_ANNOUNCEMENT_REPO = "SynapseCampusResourceBundle:OrgAnnouncements";

    /**
     * If you put a million monkeys at a million keyboards, one of them will
     * eventually write a C program.
     * The rest write PHP programs.
     *
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_ANNOUNCEMENT_LANG_REPO = "SynapseCampusResourceBundle:OrgAnnouncementsLang";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const LANG_MASTER_REPO = "SynapseCoreBundle:LanguageMaster";

    const ORG_NOT_FOUND = "Organization Not Found.";

    const ORG_NOT_FOUND_KEY = "organization_not_found.";

    const COORDINATOR_NOT_FOUND = "Coordinator Not Found.";

    const COORDINATOR_NOT_FOUND_KEY = "coordinator_not_found.";

    const PERSON_NOT_FOUND = "Person not found.";

    const PERSON_NOT_FOUND_KEY = "person_not_found.";

    const ACADEMIC_DATE_ERROR = "End-Date should be grater than Start-Date";

    const ACADEMIC_DATE_ERROR_KEY = "End-date_should_be_grater_than_start-date";

    const LANG_NOT_FOUND = "Language not found";

    const LANG_NOT_FOUND_KEY = "language_not_found";

    const CAMPUS_ANNOUNCEMENT_NOT_FOUND = "Campus announcement not found";

    const CAMPUS_ANNOUNCEMENT_NOT_FOUND_KEY = "campus_announcement_not_found";

    const START_DATE_TIME = 'start_date_time';

    const END_DATE_TIME = 'end_date_time';

    const MESSAGE = 'message';

    const MESSAGE_TYPE = 'message_type';

    const DATE_FORMAT = 'Y-m-d H:i:s';

    const ID = 'id';
    
    const CRAETE_CAMPUS_MSG = 'Create Campus Announcement';
    
    const LIST_CAMPUS = 'List Campus Announcements';
    
    const EDIT_CAMPUS_MSG = 'Edit Campus Announcement';
    
    const DELETE_CAMPUS_MSG = 'Delete Campus Announcement';
    
    const GET_CAMPUS = 'Get Campus Announcement';  
    
    const FACULTY_NOT_FOUND = "Faculty not found.";
    
    const FACULTY_NOT_FOUND_KEY = "faculty_not_found.";
    
    const CANCEL_CAMPUS = 'Cancel Campus Announcement';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_REPO = 'SynapseCoreBundle:Person';
    
    const LOGGEDINUSER = '-loggedInUser-';
    
    const PERSON_OBJ = 'person';
    
    const ORGID_OBJ = 'orgId';
    
    const ORGANIZATION_OBJ = 'organization';
    
    const CREATOR_PERSON = 'creatorPersonId';
    
    const OA_ID = 'oa.id';
    
    const OAL_MESSAGE = 'oal.message as message';
    
    const OA_DISPLAY_TYPE = 'oa.displayType as message_type';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_ANNOUNCEMENTS_REPO = 'SynapseCampusResourceBundle:OrgAnnouncements';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_ANNOUNCEMENTS_LANG_REPO = 'SynapseCampusResourceBundle:OrgAnnouncementsLang';
    
    const OA_CREATOR_PERSON_ID = 'oa.creatorPersonId = :personId';
    
    const ORGANIZATIN_ID = 'organizationid';
    
    const PERSON_ID = 'personId';
    
    const CUR_DATE_TIME = 'currentDateTime';
    
    const ID_EQUALS_ORG_ANNOUNCEMENTS = 'oa.id = oal.orgAnnouncements';

    const EDIT_ERROR = 'Currently-running alert start date time can not be edited';
    
    const EDIT_ERROR_KEY = 'Currently_running_alert_start_date_time_can_not_be_edited';
    
    const OA_MESSAGE_DURATION = 'oa.messageDuration as message_duration';
    
    const MESSAGE_DURATION = 'message_duration';
    
    const OA_ORGANIZATION_ID = 'oa.orgId = :organizationid';
    
}