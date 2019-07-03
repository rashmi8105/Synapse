Feature: CoordinatorAboutTheStudent

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24140
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to validate the buttons and link present under Feature panel-@testcaseID=24141
    Given user is on "<Overview_Page>" page
    When user clicks on "<Settings>" link under additional setup
    And user is able to expand "<Feature_Panel>" panel
    Then user is able to see expanded "<Feature_Panel>" panel
    Examples:
      | Overview_Page | Settings | Feature_Panel |
      | #/overview    | Settings | Features      |

  Scenario Outline: Verify user is able to OFF all the features under Feature panel-@testcaseID=19700
    When user selects radiobuttons for "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" under Features Panel
    And click on save button on settings page
    Then user is able to see "<Success_message>" in the alert
    And user is able to see the following "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" radio button as selected

    Examples:
      | Referral_Option | Note_Option | LogContact_Option | Appointments_Option | Email_Option  | Success_message               |
      | referral-off    | notes-off   | logContact-off    | booking-off         | sendEmail-off | Successfully set the Settings |

  Scenario Outline: Verify user is not able to view widgets on the student profile page-@testcaseID=19701
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    Then user is able to navigate to the "<Student_name>" profile page
    And user is able to student specific data "<Student_name>", "<ID>", "<Email>", "<Phone>" and "<MobilePhone>"
    And user is not able to see "<Referral>", "<Appointment>" and "<Contacts>" panel

    Examples:
      | Search_Tab | Student_name | ID   | Email                    | Phone | MobilePhone | Referral | Appointment | Contacts |
      | Search     | Deborah      | 1135 | autoqa013@mailinator.com |       |             | Referral | Appointment | Contact  |

  @Background
  Scenario Outline: Verify user is able to validate the buttons and link present under Feature panel-@testcaseID=19702
    Given user is on "<Overview_Page>" page
    When user clicks on "<Settings>" link under additional setup
    And user is able to expand "<Feature_Panel>" panel
    Then user is able to see expanded "<Feature_Panel>" panel
    Examples:
      | Overview_Page | Settings | Feature_Panel |
      | #/overview    | Settings | Features      |

  Scenario Outline:Verify user is able to ON all the features under Feature panel-@testcaseID=19703
    When user selects radiobuttons for "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" under Features Panel
    And user selects "<Student_ReferralNotification>","<PrimaryCampus>" and "<Reason_Routing>" under Referrals
    And click on save button on settings page
    Then user is able to see "<Success_message>" in the alert
    And user is able to see the following "<Student_ReferralNotification>","<PrimaryCampus>" and "<Reason_Routing>" radio button as selected under Referrals

    Examples:
      | Referral_Option | Note_Option | LogContact_Option | Appointments_Option | Email_Option | Success_message               | Student_ReferralNotification | PrimaryCampus         | Reason_Routing |
      | referral-on     | notes-on    | logContact-on     | booking-on          | sendEmail-on | Successfully set the Settings | notification-on              | primary-connection-on | reason-on      |

  Scenario Outline: Verify user is able to view widgets on the student profile page-@testcaseID=19704
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    Then user is able to navigate to the "<Student_name>" profile page
    And user is able to student specific data "<Student_name>", "<ID>", "<Email>", "<Phone>" and "<MobilePhone>"
    And user is able to see "<Referral>", "<Appointment>" and "<Contacts>" panel

    Examples:
      | Search_Tab | Student_name | ID   | Email                    | Phone | MobilePhone | Referral | Appointment | Contacts |
      | Search     | Deborah      | 1135 | autoqa013@mailinator.com |       |             | Referral | Appointment | Contact  |

  Scenario Outline: Verify user is able to create a contact-@testcaseID=19705
    When user clicks on "<Activity_Stream>" tab on student page
    And user clicks on Add New Activity link
    And user clicks on "<Contacts_tab>" tab on window
    And user select following fields "<Reason_type>", "<Contact_type>","<ContactDate>" and "<Description>"
    And user select "<Detail_option>" details checkbox
    And user select "<Sharing_option>" sharing option for "<Team_Name>"
    And clicks on Create a contact a button
    Then user is able to see "<Success_message>" in the alert
    And user should be able to see the created "<Contact>" with "<Description>" in the list

    Examples:
      | Activity_Stream | Contacts_tab | Reason_type               | Contact_type       | ContactDate | Description      | Detail_option         | Sharing_option | Team_Name  | Success_message            | Contact |
      | Activity Stream | Contact      | Class attendance positive | Phone conversation | current     | Added contact on | High priority concern | Team           | SELECT ALL | Contact saved successfully | Contact |

  Scenario Outline: Verify user is able to create Note-@testcaseID=19706
    When user clicks on Add New Activity link
    And user clicks on "<Notes_tab>" tab on window
    And user select and fill reason from "<Reason_type>" dropdown and "<Description>" in field
    And user select "<Sharing_option>" sharing option for "<Team_Name>"
    And clicks on Create a Note button
    Then user is able to see "<Success_message>" in the alert
    And user should be able to see the created "<Note>" with "<Description>" in the list

    Examples:
      | Activity_Stream | Notes_tab | Reason_type               | Description   | Sharing_option | Team_Name  | Success_message         | Note |
      | Activity Stream | Note      | Class attendance positive | Added note on | Team           | SELECT ALL | Note saved successfully | Note |

  Scenario Outline: Verify user is able to create Referral-@testcaseID=19707
    When user clicks on Add New Activity link
    And user clicks on "<Referral_tab>" tab on window
    And user select and fills following fields "<Reason_type>","<Assign_To>","<Interested_Party>" and "<Description>" in field
    And user select "<Detail_option>" details checkbox
    And user select "<Sharing_option>" sharing option for "<Team_Name>"
    And clicks on Create a Referral button
    Then user is able to see "<Success_message>" in the alert
    And user should be able to see the created "<Referral>" with "<Description>" in the list
    Examples:
      | Activity_Stream | Referral_tab | Reason_type               | Assign_To | Interested_Party | Description       | Sharing_option | Team_Name  | Success_message             | Referral | Detail_option              |
      | Activity Stream | Referral     | Class attendance positive | qaparas   | qavishvajeet     | Added referral on | Team           | SELECT ALL | Referral saved successfully | Referral | Notify Student of Referral |

  Scenario Outline: Verify user is able to create Appointment-@testcaseID=19708
    When user clicks on Add New Activity link
    And user clicks on "<Appointment_tab>" tab on window
    And user select and fills following fields "<Reason_type>","<Start_Date>","<End_Date>","<Location>" and "<Description>" in the field
    And user select "<Sharing_option>" sharing option for "<Team_Name>"
    And click on BookAppointment button
    Then user is able to see "<Success_message>" in the alert
    And user should be able to see the created "<Appointment>" with "<Description>" in the list

    Examples:
      | Activity_Stream | Appointment_tab | Reason_type               | Start_Date | End_Date | Location | Description          | Sharing_option | Team_Name  | Success_message                  | Appointment |
      | Activity Stream | Appointment     | Class attendance positive | current    | current  | Noida    | Added appointment on | Team           | SELECT ALL | Appointment created successfully | Appointment |

