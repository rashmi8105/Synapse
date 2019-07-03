Feature: StudentCampusConnection

  Background:

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24240
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:Verify that coordinator is able to set a campus connection for a student-@testcaseID=22975
    Given user is on "<Overview_Page>" page
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    And user is able to navigate to the "<Student_name>" profile page
    And user clicks on "<Details>" tab on student page
    And user clicks on "<Campus_Connections>" link under student details tab
    And user sets "<Faculty>" as Campus Connection
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<Primary_connection_Name>", "<primary_connection_email>" and "<Phone_Number>" on campus connection page
    Examples:
      | Details | Overview_Page | Search_Tab | Student_name | Campus_Connections | Faculty | Success_message                         | Primary_connection_Name | primary_connection_email | Phone_Number |
      | Details | #/overview    | Search     | Arnab        | Campus Connections | qaparas | Primary Campus Connection has been set. | qaparas                 | qaparas@mailinator.com   | 1234567890   |

  Scenario Outline:Verify user is able to ON all the features under Feature panel-@testcaseID=24281
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

  Scenario Outline:Verify Campus Connection Coordinator can access the skyfactor application-@testcaseID=24238
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qaparas@mailinator.com | Qait@123 |

  Scenario Outline:Verify that Campus Connection Coordinator  is able to create one time ofice hr for the student-@testcaseID=22976
    When user clicks on "<Appointment_Tab>" tab
    And user clicks on "<Add_Officehrs>" link on appointment page
    And fill all the mandatory fields to "<Action>" one time office hr for "<Slot_Time>" time, "<Location>" location and "<CurrentDate>"
    And click on save button
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Appointment_Tab | Add_Officehrs    | Slot_Time | CurrentDate | Success_message                | Action                    | Location |
      | Appointments    | Add Office Hours | 15 min    | current     | office Hour added successfully | CreateForCampusConnection | Boston   |


  Scenario Outline:Verify Student can access the skyfactor application-@testcaseID=24241
    Given user is on skyfactor login page
    When Student login into the application with "<EmailID>"
    Then Student lands on Survey page
    Examples:
      | EmailID                 |
      | autoqa08@mailinator.com |

  Scenario Outline:Ensure that assigned faculties and cooridnators are displayed at student end-@testcaseID=22977
    When student clicks on "<Campus_Connection>" tab
    Then student see "<Campus_Connection>" as header on student page
    And Student is able to see Faculty "<Faculty1>" as campus connection
    And Student is able to see Faculty "<Faculty2>" as campus connection
    And Student is able to see Faculty "<Faculty3>" as campus connection
    Examples:
      | Campus_Connection  | Faculty1        | Faculty2              | Faculty3         |
      | Campus Connections | qagoel, qanupur | qakumar, qavishvajeet | qagupta, qaparas |

  Scenario Outline:Ensure that correct details are displaying at student end for asigned primary campus-@testcaseID=22978
    Given user is on "<Campus_Connection>" page
    Then student is able to see primary connection heading for "<Primary_Connection_Created>"
    And student is able to see "<CampusConnectionName>" for primary connection
    Examples:
      | Campus_Connection            | CampusConnectionName | Primary_Connection_Created |
      | #/student-campus-connections | qagupta, qaparas     | qaparas                    |

  Scenario Outline: Ensure that student is able to create an appointment from the created office hr-@testcaseID=22979
    When student clicks on schedule Appointment button  for Campus Connection faculty "<Faculty_Name>"
    And student schedule appointment with reason "<Reason_For_Appointment>" and description "<Description>"
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Faculty_Name | Reason_For_Appointment    | Success_message                    | Description                    |
      | qaparas      | Class attendance positive | Appointment scheduled successfully | AppointmentForCampusConnection |

  Scenario Outline: Ensure that created appointment is displaying under Student>Appointment page-@testcaseID=22980
    When  student clicks on "<Appointments>" tab
    Then  student is able to see "<Created>" appointment on student appointment page with "<FacultyName>" from office hours
    Examples:
      | Appointments | FacultyName | Created                    |
      | Appointments | qaparas     | CreatedForCampusConnection |

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24237
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:Verify that coordinator is able to remove primary campus connection for a student-@testcaseID=22981
    Given user is on "<Overview_Page>" page
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    And user is able to navigate to the "<Student_name>" profile page
    And user clicks on "<Details>" tab on student page
    And user clicks on "<Campus Connections>" link under student details tab
    And user removes "<Faculty>" as Campus Connection
    Then user is able to see "<Success_message>" in the alert
    And user is not able to see "<Faculty>" as primary campus connection
    Examples:
      | Details | Overview_Page | Search_Tab | Student_name | Campus Connections | Success_message                        | Faculty |
      | Details | #/overview    | Search     | Arnab        | Campus Connections | Campus Connection deleted Successfully | qaparas |

  Scenario Outline:Verify Student can access the skyfactor application-@testcaseID=24239
    Given user is on skyfactor login page
    When Student login into the application with "<EmailID>"
    Then Student lands on Survey page
    Examples:
      | EmailID                 |
      | autoqa08@mailinator.com |

  Scenario Outline: Ensure that primary campus conection is not displaying at student end-@testcaseID=22982
    Given user is on "<Student_Campus_Connection>" page
    Then student is not able to see primary connection heading for "<Faculty>"
    Examples:
      | Student_Campus_Connection    | Faculty          |
      | #/student-campus-connections | qagupta, qaparas |