Feature: CoordinatorESPRJ6356

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=23983
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |


  Scenario Outline: Verify coordinator is able to ON referral options from Setting page-@testcaseID=23984
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

  Scenario Outline: Ensure that a new permission set is created successfully and is visible on the page-@testcaseID=23985
    Given user is on "<Page>" page
    When user clicks on add permission template button on the page
    And user fills "<Permission_Name>" as permission template name
    And user clicks on "<Access level>" link on permission modal window
    And user chooses "<Individual and Aggregate>" permission
    And user clicks on "<features>" link on permission modal window
    And user clicks on "<Referral>" link on feature permission window
    And user chooses "<Create>" permission for Direct Referral with sharing option "<Public>"
    And user chooses "<View>" permission for Direct Referral with sharing option "<Public>"
    And user clicks on save Permission button displayed on permission template modal win
    Then user is able to see "<Success_message>" in the alert
    And user is able to see permission template in the application
    Examples:
      | Public | Page          | Add_PermissionButton | Permission_Name          | Success_message               | Access level | Individual and Aggregate | features | Referral  | Create | View | Direct Referral |
      | Public | #/permissions | Permission Sets      | New_Automated_Permission | Permission saved successfully | Access level | Individual and Aggregate | Features | Referrals | Create | View | Direct Referral |

  Scenario Outline: Ensure that user is able to create a new group-@testcaseID=23986
    Given user is on "<Group_Summary_Page>" page
    When user clicks on Add Another Group button
    And user fills "<Group_Name>" in GroupName field
    And user fills "<Group_ID>" in GroupId field
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Group_Summary_Page | Manage_Groups | Group_Name       | Group_ID       | Success_message                  | created |
      | #/groupsummary     | Groups        | CreatedGroupName | CreatedGrouoID | The Group is added Successfully. | created |

  Scenario Outline: Ensure that user is able to add faculty to group-@testcaseID=23987
    When user clicks on "<Created_Group>" group on group summary page
    And  user adds "<Faculty_Name>" with permission "<Permission_Name>" to group
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert

    Examples:
      | Created_Group | Overview_page | Manage_Groups | Group_Name      | Group_ID        | Success_message                | created | Permission_Name        | Faculty_Name |
      | CreatedGroup  | #/overview    | Groups        | AutomationGroup | AutomationGroup | Successfully Updated the Group | created | PermissionForESPRJ6356 | qanupur      |

  Scenario Outline: Ensure that user is able to see Referral module-@testcaseID=23988
    When user clicks on "<Dashboard>" tab
    Then user is able to see "<Referral>" module
    Examples:
      | Dashboard | Referral |
      | Dashboard | referral |

  Scenario Outline: Verify coordinator is able to OFF features  options from Setting page-@testcaseID=23989
    Given user is on "<Overview_Page>" page
    When user clicks on "<Settings>" link under additional setup
    And user is able to expand "<Feature_Panel>" panel
    And user selects radiobuttons for "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" under Features Panel
    And user selects "<Student_ReferralNotification>","<PrimaryCampus>" and "<Reason_Routing>" under Referrals
    And click on save button on settings page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Overview_Page | Settings | Feature_Panel | Referral_Option | Note_Option | LogContact_Option | Appointments_Option | Email_Option  | Success_message               | Student_ReferralNotification | PrimaryCampus         | Reason_Routing |
      | #/overview    | Settings | Features      | referral-on     | notes-on    | logContact-on     | booking-off         | sendEmail-off | Successfully set the Settings | notification-on              | primary-connection-on | reason-on      |


  Scenario Outline: Ensure that a new permission set is created successfully and is visible on the page-@testcaseID=23990
    Given user is on "<Page>" page
    When user clicks on add permission template button on the page
    And user fills "<Permission_Name>" as permission template name
    And user clicks on "<Access level>" link on permission modal window
    And user chooses "<Individual and Aggregate>" permission
    And user clicks on "<features>" link on permission modal window
    And user clicks on "<Referral>" link on feature permission window
    And user chooses "<Create>" permission for Direct Referral with sharing option "<Public>"
    And user chooses "<View>" permission for Direct Referral with sharing option "<Public>"
    And user clicks on save Permission button displayed on permission template modal win
    Then user is able to see "<Success_message>" in the alert
    And user is able to see permission template in the application
    Examples:
      | Public | Page          | Add_PermissionButton | Permission_Name          | Success_message               | Access level | Individual and Aggregate | features | Referral  | Create | View | Direct Referral |
      | Public | #/permissions | Permission Sets      | New_Automated_Permission | Permission saved successfully | Access level | Individual and Aggregate | Features | Referrals | Create | View | Direct Referral |


  Scenario Outline: Ensure that user is able to create a new group-@testcaseID=23991
    Given user is on "<Group_Summary_Page>" page
    When user clicks on Add Another Group button
    And user fills "<Group_Name>" in GroupName field
    And user fills "<Group_ID>" in GroupId field
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Group_Summary_Page | Manage_Groups | Group_Name       | Group_ID       | Success_message                  | created |
      | #/groupsummary     | Groups        | CreatedGroupName | CreatedGrouoID | The Group is added Successfully. | created |


  Scenario Outline: Ensure that user is able to add faculty to group-@testcaseID=23992
    When user clicks on "<Created_Group>" group on group summary page
    And  user adds "<Faculty_Name>" with permission "<Permission_Name>" to group
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert

    Examples:
      | Created_Group | Overview_page | Manage_Groups | Group_Name      | Group_ID        | Success_message                | created | Permission_Name        | Faculty_Name |
      | CreatedGroup  | #/overview    | Groups        | AutomationGroup | AutomationGroup | Successfully Updated the Group | created | PermissionForESPRJ6356 | qaaditya     |

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=23993
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName                | Password |
      | qaaditya@mailinator.com | Qait@123 |

  Scenario Outline: Ensure that user is able to see Referral module-@testcaseID=23994
    When user clicks on "<Dashboard>" tab
    Then user is able to see "<Referral>" module
    Examples:
      | Dashboard | Referral |
      | Dashboard | referral |