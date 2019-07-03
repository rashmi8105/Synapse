Feature: CoordinatorPermission


  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24293
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Check Cancel button functionality-@testcaseID=19767
    Given user is on "<Page>" page
    When user clicks on "<ManagePermission_Button>" button
    And user clicks on add permission template button on the page
    And user fills "<Permission_Name>" as permission template name
    And user clicks on cancel button displayed on permission template modal win
    Then user is not able to see the permission template in the application

    Examples:
      | Page       | ManagePermission_Button | Add_PermissionButton                     | Permission_Name      |
      | #/overview | Permission              | Click Here to Add Another Permission Set | Automated_Permission |


  Scenario Outline: Ensure that a new permission set is created successfully and is visible on the page-@testcaseID=19765
    Given user is on "<Page>" page
    When user clicks on add permission template button on the page
    And user fills "<Permission_Name>" as permission template name
    And user clicks on save Permission button displayed on permission template modal win
    Then user is able to see "<Success_message>" in the alert
    And user is able to see permission template in the application

    Examples:
      | Page          | Add_PermissionButton | Permission_Name          | Success_message               |
      | #/permissions | Permission Sets      | New_Automated_Permission | Permission saved successfully |


  Scenario Outline: Ensure that the newly created permission set is edited successfully and is visible on the page-@testcaseID=19766
    Given user is on "<Page>" page
    When user edits the already created permission template "<Permission_Name>" with Name "<Edited_Permission_Name>"
    And user clicks on save Permission button displayed on permission template modal win
    Then user is able to see "<Success_message>" in the alert
    And user is able to see permission template in the application

    Examples:
      | Page          | Permission_Name          | Edited_Permission_Name      | Success_message                 |
      | #/permissions | New_Automated_Permission | Automated_Permission_edited | Permission updated successfully |