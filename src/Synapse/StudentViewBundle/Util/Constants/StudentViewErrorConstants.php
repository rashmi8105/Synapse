<?php
namespace Synapse\StudentViewBundle\Util\Constants;

class StudentViewErrorConstants
{

    const STUDENT_VIEW_101 = "Person Not Found.";

    const STUDENT_VIEW_102 = "Student Not Found.";
    
    /*
     * Variable name constants
     */
    const VAR_STUDENT_ID = "studentId";

    const VAR_ORG_ID = "orgId";

    const VAR_FACULTY_ID = "facultyId";

    const VAR_FILTER = "filter";
    
    const VAR_STARTDATE = "startDate";
    
    const VAR_ENDDATE = "endDate";
    
    const VAR_APPID = "appointment id";
    
    /*
     * View and manage agenda in student view constants
     */
    const ERR_STUDENT_VIEW_AGENDA_003 = "Student View - Upcoming appointments - ERR_STUDENT_VIEW_AGENDA_003 - studentId - ";

    const ERR_STUDENT_VIEW_AGENDA_004 = "Student View - Upcoming agenda - ERR_STUDENT_VIEW_AGENDA_004 - getStudentsUpcomingAppointments() in appointment_recepient_and_status repo - studentId - ";

    const ERR_STUDENT_VIEW_AGENDA_005 = "Student View - Upcoming appointments ERR_STUDENT_VIEW_AGENDA_005 - listing is completed";

    const ERR_STUDENT_VIEW_AGENDA_006 = "Student View - Upcoming appointments - ";

    const ERR_STUDENT_VIEW_AGENDA_007 = "Student View - List of available campus for the given student - ERR_STUDENT_VIEW_AGENDA_007 - studentId - ";

    const ERR_STUDENT_VIEW_AGENDA_008 = "Student View - List of available campus for the given student - ERR_STUDENT_VIEW_AGENDA_008 - listing completed";

    const ERR_STUDENT_VIEW_AGENDA_009 = "Student View - List of available campus for the given student - ";

    const ERR_STUDENT_VIEW_AGENDA_010 = "ERR_STUDENT_VIEW_AGENDA_010 - getStudentCampuses() in org_person_student repo - studentId - ";

    const ERR_STUDENT_VIEW_AGENDA_011 = "Student View - List of connections for the given student and organization - ";

    const ERR_STUDENT_VIEW_AGENDA_012 = "ERR_STUDENT_VIEW_AGENDA_010 - getStudentCampusConnection() in org_person_faculty repo - studentId - ";

    const ERR_STUDENT_VIEW_AGENDA_013 = "Student View - List of connections for the given student and organization - listing is completed";

    const ERR_STUDENT_VIEW_AGENDA_014 = "Student View - List of office hour slots for given faculty and organization - ";
    
    const ERR_STUDENT_VIEW_AGENDA_015 = "Student View - List of office hour slots for given faculty and organization - ERR_STUDENT_VIEW_AGENDA_015 - listing is completed ";
    
    const ERR_STUDENT_VIEW_AGENDA_016 = "Student View - List of office hour slots - ERR_STUDENT_VIEW_AGENDA_016 - getFacultyOpenOfficeHour() in office_hours repo - ";
    
    const ERR_STUDENT_VIEW_AGENDA_017 = "Student View - Create an appointment by a student - ";
    
    const ERR_STUDENT_VIEW_AGENDA_018 = "Student View - Create an appointment by a student - ERR_STUDENT_VIEW_AGENDA_018 - Appointment to be created";
    
    const ERR_STUDENT_VIEW_AGENDA_019 = "Student View - Create an appointment by a student - ERR_STUDENT_VIEW_AGENDA_019 - Alert Notification";
    
    const ERR_STUDENT_VIEW_AGENDA_020 = "Student View - Create an appointment by a student - ERR_STUDENT_VIEW_AGENDA_020 - Email Notification";
    
    const ERR_STUDENT_VIEW_AGENDA_021 = "Student View - Create an appointment by a student - ERR_STUDENT_VIEW_AGENDA_021 - Reque Job";
    
    const ERR_STUDENT_VIEW_AGENDA_022 = "Student View - Create an appointment by a student - ERR_STUDENT_VIEW_AGENDA_022 - Appointment created";
    
    const ERR_STUDENT_VIEW_AGENDA_023 = "Student View - Cancel an appointment by a student - ";
    
    const ERR_STUDENT_VIEW_AGENDA_024 = "Student View - Cancel an appointment by a student - ERR_STUDENT_VIEW_AGENDA_024 - Appointment to be cancelled";
    
    const ERR_STUDENT_VIEW_AGENDA_025 = "Student View - Cancel an appointment by a student - ERR_STUDENT_VIEW_AGENDA_025 - Appointment cancelled";
    
    const ERR_STUDENT_VIEW_AGENDA_026 = "Student View - Cancel an appointment by a student - ERR_STUDENT_VIEW_AGENDA_026 - Email Notification";
}