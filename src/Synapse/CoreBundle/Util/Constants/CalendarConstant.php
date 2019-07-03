<?php
namespace Synapse\CoreBundle\Util\Constants;

class CalendarConstant
{

    const ORGANIZATION = 'organization';

    const PERSON_NOT_FOUND = "Person Not Found";

    const PERSON = 'person';
    
    const ERR_CALENDAR_SYNC_001 = "Calender Sync - MAF to Google";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const APPOINTMENT_REPO = "SynapseCoreBundle:Appointments";

    const EBI_SYSTEM_URL = "System_URL";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBICONFIG_REPO = "SynapseCoreBundle:EbiConfig";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const OFFICEHOURS_REPO = "SynapseCoreBundle:OfficeHours";

    const API_EVENT_CREATE = "create";

    const API_EVENT_EDIT = "edit";

    const API_EVENT_CANCEL = "cancel";

    const API_STATUS_200 = "200";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const OFFICEHOURS_SERIES_REPO = "SynapseCoreBundle:OfficeHoursSeries";

    const KEY_OFFICEHOURSERIES = "officeHoursSeries";

    const APP_TYPE_OFFICEHOURS = "officehours";

    const APP_TYPE_APPOINTMENT = "appointment";

    const APP_TYPE_OFFICEHOURSERIES = 'officehourseries';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_REPO = 'SynapseCoreBundle:Person';

    const LIT_TENTATIVE = 'tentative';

    const LIT_ACCEPTED = 'accepted';

    const LIT_APPOINTMENTID = 'AppointmentId';

    const DATE_FORMAT = 'Y-m-d H:i:s A';

    const LIT_EMAIL = 'Email';

    const LIT_RESPONSE_STATUS = 'ResponseStatus';

    const LIT_HEADERS = 'headers';

    const LIT_REQUIRED_ATTENDEES = 'RequiredAttendees';

    const APP_URL = 'appURL';

    const API_URL = 'apiURL';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const APP_RECEPIENT_REPO = "SynapseCoreBundle:AppointmentRecepientAndStatus";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_FACULTY_REPO = 'SynapseCoreBundle:OrgPersonFaculty';
    
    const KEY_CAMPUS_STATUS = 'campusStatus';
    
    const KEY_CAMPUS_SETTINGS = 'campusSettings';
    
    const KEY_FACULTY_MAFTOPCS = 'facultyMAFTOPCS';
    
    const KEY_FACULTY_PCSTOMAF = 'facultyPCSTOMAF';
    
    const KEY_GOOGLE_CLIENT = 'googleClientId';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_REPO = "SynapseCoreBundle:Organization";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const METADATA_LIST_REPO = 'SynapseCoreBundle:MetadataListValues';
    
    const FIELD_APPOINTMENT = "appointments";

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ORG_SERVICE = "org_service";
    
    const ORG_NOT_FOUND = "Organization Not Found.";
    
    const ORG_NOT_FOUND_KEY = 'organization_not_found';

    const GOOGLE_SCOPE_CALENDAR = 'https://www.googleapis.com/auth/calendar';

    const GOOGLE_SCOPE_PROFILE = 'https://www.googleapis.com/auth/userinfo.profile';

    const GOOGLE_SCOPE_EMAIL = 'https://www.googleapis.com/auth/userinfo.email';
}