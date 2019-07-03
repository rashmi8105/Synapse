Feature: StudentReferrals

  Background:

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24234
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:Verify coordinator is able to OFF referral options from Setting page-@testcaseID=22963
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


  Scenario Outline:Verify Coordinator is able to create a Referral for student with "Notify to student" detail option checked-@testcaseID=22964
    When user grabs the value of number of notification
    And user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    And user is able to navigate to the "<Student_name>" profile page
    And user clicks on "<Activity_Stream>" tab on student page
    And user clicks on Add New Activity link
    And user clicks on "<Referral_tab>" tab on window
    And user select and fills following fields "<Reason_type>","<Assign_To>","<Interested_Party>" and "<Description>" in field
    And user select "<Detail_option>" details checkbox
    And user select "<Sharing_option>" sharing option for "<Team_Name>"
    And clicks on Create a Referral button
    Then user is able to see "<Success_message>" in the alert
    And user should be able to see the created "<Referral>" with "<Description>" in the list
    Examples:
      | Search_Tab | Student_name | Activity_Stream | Referral_tab | Reason_type               | Assign_To | Interested_Party | Description       | Sharing_option | Team_Name  | Success_message             | Referral | Detail_option              |
      | Search     | Arnab        | Activity Stream | Referral     | Class attendance positive | qanupur   | qavishvajeet     | Added referral on | Team           | SELECT ALL | Referral saved successfully | Referral | Notify Student of Referral |


  Scenario Outline:Verify Student can access the skyfactor application-@testcaseID=24235
    Given user is on skyfactor login page
    When Student login into the application with "<EmailID>"
    Then Student lands on Survey page

    Examples:
      | EmailID                 |
      | autoqa08@mailinator.com |

  Scenario Outline:Ensure that the created Referral is displayed at student end-@testcaseID=22965
    When student clicks on "<Referral>" tab
    Then student see "<Referral>" as header on student page
    And student is able to see referrals with details "<Reason_type>", "<Assign_To>" and "<createdBy>"
    Examples:
      | Referral  | Assign_To | Reason_type               | createdBy |
      | Referrals | qanupur   | Class attendance positive | qanupur   |

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24233
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify coordinator is able to OFF referral options from Setting page-@testcaseID=22966
    Given user is on "<Overview_Page>" page
    When user clicks on "<Settings>" link under additional setup
    And user is able to expand "<Feature_Panel>" panel
    And user selects radiobuttons for "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" under Features Panel
    And user selects "<Student_ReferralNotification>","<PrimaryCampus>" and "<Reason_Routing>" under Referrals
    And click on save button on settings page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Overview_Page | Settings | Feature_Panel | Referral_Option | Note_Option | LogContact_Option | Appointments_Option | Email_Option  | Success_message               | Student_ReferralNotification | PrimaryCampus         | Reason_Routing |
      | #/overview    | Settings | Features      | referral-on     | notes-off   | logContact-off    | booking-off         | sendEmail-off | Successfully set the Settings | notification-off             | primary-connection-on | reason-on      |

  Scenario Outline: Verify Coordinator is able to create a Referral for student without selecting "Notify to student" detail option-@testcaseID=22967
    When user grabs the value of number of notification
    And user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    And user is able to navigate to the "<Student_name>" profile page
    And user clicks on "<Activity_Stream>" tab on student page
    And user clicks on Add New Activity link
    And user clicks on "<Referral_tab>" tab on window
    And user select and fills following fields "<Reason_type>","<Assign_To>","<Interested_Party>" and "<Description>" in field
    And user select "<Detail_option>" details checkbox
    And user select "<Sharing_option>" sharing option for "<Team_Name>"
    And clicks on Create a Referral button
    Then user is able to see "<Success_message>" in the alert
    And user should be able to see the created "<Referral>" with "<Description>" in the list
    Examples:
      | Search_Tab | Student_name | Activity_Stream | Referral_tab | Reason_type               | Assign_To | Interested_Party | Description       | Sharing_option | Team_Name  | Success_message             | Referral | Detail_option |
      | Search     | Arnab        | Activity Stream | Referral     | Class attendance positive | qanupur   | qavishvajeet     | Added referral on | Team           | SELECT ALL | Referral saved successfully | Referral | None          |

  Scenario Outline:Verify Student can access the skyfactor application-@testcaseID=24236
    Given user is on skyfactor login page
    When Student login into the application with "<EmailID>"
    Then Student lands on Survey page

    Examples:
      | EmailID                 |
      | autoqa08@mailinator.com |

  Scenario Outline: Ensure that the created Referral is not displaying at student end-@testcaseID=22968
    When student clicks on "<Referral>" tab
    Then student see "<Referral>" as header on student page
    And  student is not able see referrals
    Examples:
      | Referral  | Assign_To | Reason_type               |
      | Referrals | qanupur   | Class attendance positive |