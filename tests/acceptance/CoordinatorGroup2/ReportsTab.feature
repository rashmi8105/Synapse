Feature: CoordinatorReports

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24130
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Ensure that a new permission set is created successfully with all reports access and is visible on the page-@testcaseID=24131
    Given user is on "<Permission_Page>" page
    When user clicks on add permission template button on the page
    And user fills "<Permission_Name>" as permission template name
    And user clicks on "<Access_level>" link on permission modal window
    And user chooses "<Individual_and_Aggregate>" permission
    And user clicks on "<Courses>" link on permission modal window
    And user chooses "<View_all_Courses>" permission
    And user chooses "<View_all_Academic_Updates_for_all_Courses>" permission
    And user clicks on "<Reports>" link on permission modal window
    And user chooses "<SelectAll>" permission
    And user clicks on save Permission button displayed on permission template modal win
    Then user is able to see "<Success_message>" in the alert
    And user is able to see permission template in the application
    Examples:
      | Permission_Page | Permission_Name          | Success_message               | Access_level | Individual_and_Aggregate | Courses | View_all_Courses | View_all_Academic_Updates_for_all_Courses | Reports | SelectAll  |
      | #/permissions   | New_Automated_Permission | Permission saved successfully | Access level | Individual and Aggregate | Courses | View all Courses | View all Academic Updates for all Courses | Reports | Select all |

  Scenario Outline: Ensure that a new permission set is assigned to Coordinator in All Student Group-@testcaseID=24132
    Given user is on "<GroupPage>" page
    When user clicks on AllStudent group on group summary page
    And user gives "<Coordinator_User>" a new Permission set "<PermissionSet>"
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert

    Examples:
      | GroupPage      | PermissionSet       | Coordinator_User | Success_message                |
      | #/groupsummary | PermissionForReport | qaaditya         | Successfully Updated the Group |

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24133
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName                | Password |
      | qaaditya@mailinator.com | Qait@123 |

  Scenario Outline: Verify Coordinator is able to view all reports under reports tab-@testcaseID=24134
    When user clicks on "<Reports>" tab
    Then user is able to see all reports
    Examples:
      | Reports |
      | Reports |

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24135
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |


  Scenario Outline: Ensure that the newly created permission set is edited successfully to give access to only one report-@testcaseID=24136
    Given user is on "<Permission_Page>" page
    When user clicks on edit icon of "<Permission_Name>" permission template
    And user clicks on "<Reports>" link on permission modal window
    And user chooses "<Group_Response_Report>" permission
    And user clicks on save Permission button displayed on permission template modal win
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Reports | Permission_Page | Permission_Name          | Success_message                 | Group_Response_Report |
      | Reports | #/permissions   | New_Automated_Permission | Permission updated successfully | Group Response Report |

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24137
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName                | Password |
      | qaaditya@mailinator.com | Qait@123 |


  Scenario Outline: Verify Coordinator can see all default report along with one selected Report-@testcaseID=24138
    When user clicks on "<Reports>" tab
    Then user is able to see "<Group_Response_Report>" Report
    Examples:
      | Reports | Group_Response_Report |
      | Reports | Group Response Report |