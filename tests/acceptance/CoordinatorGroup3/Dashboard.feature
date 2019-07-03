Feature: CoordinatorDashboard

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=23995

    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to OFF all the features under Feature panel-@testcaseID=23996
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

  Scenario Outline: Verify user is not able to see the moduls on dashboard page once the features are off-@testcaseID=23997
    When user clicks on "<Dashboard>" tab
    Then user is not able to see "<Referral>" module
    And user is not able to see "<Agenda>" module

    Examples:
      | Dashboard | Referral | Agenda |
      | Dashboard | referral | agenda |

  Scenario Outline: Verify user is able to validate the buttons and link present under Feature panel-@testcaseID=23998
    Given user is on "<Overview_Page>" page
    When user clicks on "<Settings>" link under additional setup
    And user is able to expand "<Feature_Panel>" panel
    Then user is able to see expanded "<Feature_Panel>" panel
    And user is able to view "<Referral_label>","<Note_label>","<LogContact_label>","<Appointments_label>" and "<Email_label>" labels

    Examples:
      | Overview_Page | Settings | Feature_Panel | Referral_label | Note_label | LogContact_label | Appointments_label | Email_label |
      | #/overview    | Settings | Features      | Referrals      | Notes      | Log Contacts     | Appointments       | Email       |

  Scenario Outline: Verify user is able ON all the featurs under feature panel-@testcaseID=23999
    When user selects radiobuttons for "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" under Features Panel
    And click on save button on settings page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Referral_Option | Note_Option | LogContact_Option | Appointments_Option | Email_Option | Success_message               |
      | referral-on     | notes-off   | logContact-off    | booking-on          | sendEmail-on | Successfully set the Settings |

  Scenario Outline: Verify user is able to see the modules on dashboard page once the features are ON-@testcaseID=24000
    When user clicks on "<Dashboard>" tab
    Then user is able to see "<Referral>" module
    And user is able to see "<Agenda>" module

    Examples:
      | Dashboard | Referral | Agenda |
      | Dashboard | referral | agenda |


