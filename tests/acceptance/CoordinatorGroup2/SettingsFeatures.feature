Feature: CoordinatorSettingsFeatures

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24215

    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to validate the buttons and link present under Feature panel-@testcaseID=24216
    Given user is on "<Overview_Page>" page
    When user clicks on "<Settings>" link under additional setup
    And user is able to expand "<Feature_Panel>" panel
    Then user is able to see expanded "<Feature_Panel>" panel
    And user is able to view "<Referral_label>","<Note_label>","<LogContact_label>","<Appointments_label>" and "<Email_label>" labels

    Examples:
      | Overview_Page | Settings | Feature_Panel | Referral_label | Note_label | LogContact_label | Appointments_label | Email_label |
      | #/overview    | Settings | Features      | Referrals      | Notes      | Log Contacts     | Appointments       | Email       |

  Scenario Outline: Verify user is able to ON all the features under Feature panel-@testcaseID=24217
    When user selects radiobuttons for "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" under Features Panel
    And click on save button on settings page
    And user is able to see "<Success_message>" in the alert
    And user is able to see the following "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" radio button as selected

    Examples:
      | Referral_Option | Note_Option | LogContact_Option | Appointments_Option | Email_Option | Success_message               |
      | referral-on     | notes-on    | logContact-on     | booking-on          | sendEmail-on | Successfully set the Settings |

  Scenario Outline: Verify user is able to OFF all the features under Feature panel-@testcaseID=24218
    When user selects radiobuttons for "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" under Features Panel
    And click on save button on settings page
    Then user is able to see "<Success_message>" in the alert
    And user is able to see the following "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" radio button as selected

    Examples:
      | Referral_Option | Note_Option | LogContact_Option | Appointments_Option | Email_Option  | Success_message               |
      | referral-off    | notes-off   | logContact-off    | booking-off         | sendEmail-off | Successfully set the Settings |

  Scenario Outline: Verify user is able to select radio button under Referral when Referrals is selected-@testcaseID=24219
    When user selects radiobuttons for "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" under Features Panel
    And user selects "<Student_ReferralNotification>","<PrimaryCampus>" and "<Reason_Routing>" under Referrals
    And click on save button on settings page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Referral_Option | Note_Option | LogContact_Option | Appointments_Option | Email_Option  | Success_message               | Student_ReferralNotification | PrimaryCampus         | Reason_Routing |
      | referral-on     | notes-off   | logContact-off    | booking-off         | sendEmail-off | Successfully set the Settings | notification-on              | primary-connection-on | reason-on      |

