Feature: FacultyDashboard

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24228
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to OFF all the features under Feature panel-@testcaseID=22949
    Given user is on "<Overview_Page>" page
    When user clicks on "<Settings>" link under additional setup
    And user is able to expand "<Feature_Panel>" panel
    And user is able to see expanded "<Feature_Panel>" panel
    And user is able to view "<Referral_label>","<Note_label>","<LogContact_label>","<Appointments_label>" and "<Email_label>" labels
    And user selects radiobuttons for "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" under Features Panel
    And click on save button on settings page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Referral_label | Note_label | LogContact_label | Appointments_label | Email_label | Overview_Page | Settings | Feature_Panel | Referral_Option | Note_Option | LogContact_Option | Appointments_Option | Email_Option  | Success_message               | Dashboard |
      | Referrals      | Notes      | Log Contacts     | Appointments       | Email       | #/overview    | Settings | Features      | referral-off    | notes-off   | logContact-off    | booking-off         | sendEmail-off | Successfully set the Settings | Dashboard |

  Scenario Outline:Verify Faculty can access the skyfactor application-@testcaseID=24274
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then Faculty land on Dashboard page
    Examples:
      | UserName                   | Password |
      | qaFaculty08@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is not able to see the modules on dashboard page once the features are off-@testcaseID=22950
    When user clicks on "<Dashboard>" tab
    Then user is not able to see "<Referral>" module
    And user is not able to see "<Agenda>" module

    Examples:
      | Dashboard | Referral | Agenda |
      | Dashboard | referral | agenda |


  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24273
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to ON all the features under Feature panel-@testcaseID=22951
    Given user is on "<Overview_Page>" page
    When user clicks on "<Settings>" link under additional setup
    And user is able to expand "<Feature_Panel>" panel
    And user selects radiobuttons for "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" under Features Panel
    And user selects "<Student_ReferralNotification>","<PrimaryCampus>" and "<Reason_Routing>" under Referrals
    And click on save button on settings page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Feature_Panel | Settings | Overview_Page | Referral_Option | Note_Option | LogContact_Option | Appointments_Option | Email_Option | Success_message               | Student_ReferralNotification | PrimaryCampus         | Reason_Routing |
      | Features      | Settings | #/overview    | referral-on     | notes-on    | logContact-on     | booking-on          | sendEmail-on | Successfully set the Settings | notification-on              | primary-connection-on | reason-on      |


  Scenario Outline:Verify Faculty can access the skyfactor application-@testcaseID=24275
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then Faculty land on Dashboard page
    Examples:
      | UserName                   | Password |
      | qaFaculty08@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to see the modules on dashboard page once the features are ON-@testcaseID=22952
    When user clicks on "<Dashboard>" tab
    Then user is able to see "<Referral>" module
    And user is able to see "<Agenda>" module

    Examples:
      | Dashboard | Referral | Agenda |
      | Dashboard | referral | agenda |



