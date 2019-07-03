<?php
namespace Synapse\CoreBundle\Util\Constants;

class AppointmentsConstant
{

    const ORGANIZATIONID = 'organization_id';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const OFFICEHOURS_REPO = "SynapseCoreBundle:OfficeHours";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACTIVITY_CATEGORY_REPO = "SynapseCoreBundle:ActivityCategory";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const APPOINTMENT_REPO = "SynapseCoreBundle:Appointments";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const METADATA_REPO = "SynapseCoreBundle:MetadataListValues";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBICONFIG_REPO = "SynapseCoreBundle:EbiConfig";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const APP_RECEPIENT_REPO = "SynapseCoreBundle:AppointmentRecepientAndStatus";

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const PERSON_SERVICE = "person_service";

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ORG_SERVICE = 'org_service';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ACTIVITY_LOG_SERVICE = "activitylog_service";

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ALERT_NOTIFICATION_SERVICE = "alertNotifications_service";

    const ORG_NOT_FOUND = 'Organization Not Found.';

    const ORG_NOT_FOUND_KEY = 'organization_not_found';

    const PERSON_NOT_FOUND = 'Person Not Found.';

    const PERSON_NOT_FOUND_KEY = 'Person_not_found';

    const APPOINTMENT_NOT_FOUND = 'Appointment Not Found.';

    const APPOINTMENT_NOT_FOUND_KEY = 'appointment_not_found';

    const SUBJECT = 'subject';

    const EMAIL_KEY = 'email_key';

    const EBI_CONGIF_KEY = 'ebi_config_key';

    const STUDENT_DASHBOARD = 'student_dashboard';

    const LOCATION = 'location';

    const FROM_DATE = 'fromDate';

    const TO_DATE = 'toDate';

    const DATE_FORMAT = 'Y-m-d H:i:s';

    const FIELD_APPOINTMENT = "appointments";

    const EMAIL_KEY_STUDENTDASHBOARD = "StudentDashboard_AppointmentPage";

    const FIELD_STAFF = "staff_name";

    const FIELD_APPDATETIME = "app_datetime";

    const DATETIME_FORMAT = "m/d/Y h:ia";

    const FIELD_STUDENTNAME = "student_name";

    const DATE_PASTDATE = "pastDate";

    const FIELD_APPOINTMENTID = "appointment_id";

    const PERSON_ID = "person_id";

    const FIELD_OFFICEHOURSID = "office_hours_id";

    const FIELD_ATTENDEES = "attendees";

    const FIELD_SLOTSTART = "slot_start";

    const FIELD_SLOTEND = "slot_end";

    const FIELD_APPOINTMENTSID = "appointments_id";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_STUDENT_REPO = 'SynapseCoreBundle:OrgPersonStudent';

    const TO_STUDENT = '_to_Student';

    const TITLE = 'title';

    const STATUS = 'status';

    const CALENDAR_TIMESLOT = 'calendar_time_slots';

    const ORG_ID = 'orgId';

    const FIELD_LOCATION = 'location';

    const DATEONLY_FORMAT = "Y-m-d";

    const APPOINTMENT_SLOT_NOT_FOUND = "The appointment slot you have selected is already booked or removed";

    const APPOINTMENT_SLOT_NOT_FOUND_KEY = "appointment_slot_available";

    const STUDENT_NOT_FOUND = "Student Not Found.";

    const STUDENT_NOT_FOUND_KEY = "Student_Not_Found";

    const PERSON_ID_STUDENT = "personIdStudent";

    const APPOINTMENT_STUDENT_NOT_FOUND = "Student not found in the appointment";

    const APPOINTMENT_STUDENT_NOT_FOUND_KEY = "Appointment_Student_Not_Found";

    const APP_STUDENT_EBI_CONFIG = "Gateway_Staff_Landing_Page";

    const APP_BOOK_STUDENT_TO_STAFF_EMAIL_KEY = "Appointment_Book_Student_to_Staff";

    const APP_CANCEL_STUDENT_TO_STAFF_EMAIL_KEY = "Appointment_Cancel_Student_to_Staff";

    const APP_STAFF_EMAIL = "staff_email";

    const EMAIL_DETAIL = 'email_detail';

    const EMAILKEY_KEY = "emailKey";

    const EMAIL_STAFF_DASHBOARD = "staff_dashboard";

    const KEY_ORGANIZATION_ID = "organizationId";

    const APP_EMAIL_TYPE = "emailType";

    const APP_DATE_TIME = "appDateTime";

    const EMAIL_SKY_LOGO = "Skyfactor_Mapworks_logo";

    const EBI_SYSTEM_URL = "System_URL";

    const SKY_LOGO_PATH = "images/Skyfactor-Mapworks-login.png";

    const PERSON_ID_STUDENT_EQUALS_STUDENT_ID = 'ars.personIdStudent = :studentId';

    const STUDENT_ID = 'studentId';
    
    const TIME235959 = " 23:59:59";
    
    const SHARE_OPTIONS = 'share_options';

    const APPOINTMENT_FEATURE_NAME = 'Booking';
}
    