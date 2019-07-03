Feature: StudentAppointments

  Background:

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24230
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:Verify user is able to ON all the features under Feature panel-@testcaseID=24276
    Given user is on "<Overview_Page>" page
    When user clicks on "<Settings>" link under additional setup
    And user is able to expand "<Feature_Panel>" panel
    And user selects radiobuttons for "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" under Features Panel
    And user selects "<Student_ReferralNotification>","<PrimaryCampus>" and "<Reason_Routing>" under Referrals
    And click on save button on settings page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Overview_Page | Settings | Feature_Panel | Referral_Option | Note_Option | LogContact_Option | Appointments_Option | Email_Option | Success_message               | Student_ReferralNotification | PrimaryCampus         | Reason_Routing |
      | #/overview    | Settings | Features      | referral-on     | notes-on    | logContact-on     | booking-on          | sendEmail-on | Successfully set the Settings | notification-on              | primary-connection-on | reason-on      |

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24277
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:  Ensure that coordinator is able to create an appointment for a student-@testcaseID=22983
    When user clicks on "<Appointment_Tab>" tab
    And user clicks on "<Schedule_Appointment>" link on appointment page
    And fill all the mandatory fields "<Reason_for_Appointment>","<ScheduleDate>","<Location>","<SharingOption>","<Attendees1>","<Attendees2>" to "<Action>" appointment
    And click on BookAppointment button
    Then user is able to see "<Success_message>" in the alert
    And user is able to view appointment on the page
    Examples:
      | Appointment_Tab | Schedule_Appointment | Success_message                  | Reason_for_Appointment   | ScheduleDate | Location | SharingOption | Attendees1 | Attendees2 | Action |
      | Appointments    | Schedule Appointment | Appointment created successfully | Class attendance concern | current      | Noida    | public        | Arnab      | None       | Create |


  Scenario Outline:Verify Student can access the skyfactor application-@testcaseID=24278
    Given user is on skyfactor login page
    When Student login into the application with "<EmailID>"
    Then Student lands on Survey page

    Examples:
      | EmailID                 |
      | autoqa08@mailinator.com |

  Scenario Outline:Ensure that created appointment is displaying at student end-@testcaseID=22984
    When student clicks on "<Appointment>" tab
    Then student see "<Appointment>" as header on student page
    And student is able to see scheduled appointment with "<Faculty_Name>"
    Examples:
      | Appointment  | Faculty_Name |
      | Appointments | qanupur      |


  Scenario Outline:Verify that student is able to cancel the created appointment-@testcaseID=22985
    When student clicks on cancel appointment button
    And student clicks on cancel appointment confirm button
    Then user is able to see "<Success_message>" in the alert
    And student is not able to see appointment on student appointment page with "<FacultyName>"

    Examples:
      | Success_message                    | FacultyName |
      | Appointment cancelled successfully | qanupur     |

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24279
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify that coordinator is able to create one time office hr-@testcaseID=22986
    When user clicks on "<Appointment_Tab>" tab
    And user clicks on "<Add_Officehrs>" link on appointment page
    And fill all the mandatory fields to "<Action>" one time office hr for "<Slot_Time>" time, "<Location>" location and "<CurrentDate>"
    And click on save button
    Then user is able to see "<Success_message>" in the alert
    And user is able to view Office hr on the page

    Examples:
      | Appointment_Tab | Add_Officehrs    | Slot_Time | CurrentDate | Success_message                | Action | Location |
      | Appointments    | Add Office Hours | 15 min    | current     | office Hour added successfully | Create | Boston   |

  Scenario Outline:Verify Student can access the skyfactor application-@testcaseID=24280
    Given user is on skyfactor login page
    When Student login into the application with "<EmailID>"
    Then Student lands on Survey page
    Examples:
      | EmailID                 |
      | autoqa08@mailinator.com |

  Scenario Outline:Ensure that student is able to view the modal window on clicking schedule appointment button-@testcaseID=22987
    When student clicks on "<Appointment>" tab
    And student clicks on schedule appointment button on student appointment page
    Then student is able to see select a person window
    Examples:
      | Appointment  |
      | Appointments |

  Scenario Outline:Ensure that created office hr will display on the window when student clicks on the creator name-@testcaseID=22988
    When student chooses "<FacultyName>" faculty from select a person window
    Then student is able to see created office hour
    Examples:
      | FacultyName |
      | qanupur     |

  Scenario Outline: Ensure that student is able to create appointment from that office hr-@testcaseID=22989
    When student schedule appointment with reason "<Reason_For_Appointment>" and description "<Description>"
    Then user is able to see "<Success_message>" in the alert
    And student is able to see "<Created>" appointment on student appointment page with "<FacultyName>" from office hours
    Examples:
      | Success_message                    | FacultyName | Reason_For_Appointment    | Created | Description            |
      | Appointment scheduled successfully | qanupur     | Class attendance positive | Created | AppointmentWithFaculty |

  Scenario Outline:  Verify that student is not able to view the office hr if any appointment is created from it-@testcaseID=22990
    When student clicks on schedule appointment button on student appointment page
    And student chooses "<FacultyName>" faculty from select a person window
    Then student is not able to see created office hour
    Examples:
      | FacultyName |
      | qanupur     |

  Scenario Outline: Ensure that student is able to cancel the created appointment-@testcaseID=22991
    Given user is on "<Appointment>" page
    When student clicks on cancel appointment created from office hour
    And student clicks on cancel appointment confirm button
    Then user is able to see "<Success_message>" in the alert
    And student is not able to see appointment on student appointment page with "<FacultyName>" from office hours
    Examples:
      | Appointment      | Success_message                    | FacultyName |
      | #/student-agenda | Appointment cancelled successfully | qanupur     |


  Scenario Outline:Verify that student is able to view the office hr as soon the appointment created from it has been cancelled-@testcaseID=22992
    When student clicks on schedule appointment button on student appointment page
    And student chooses "<FacultyName>" faculty from select a person window
    And student is able to see created office hour
    Examples:
      | FacultyName |
      | qanupur     |
