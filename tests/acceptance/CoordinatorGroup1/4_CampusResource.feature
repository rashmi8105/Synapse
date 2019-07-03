Feature: CoordinatorCampus Resource

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24145
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:Ensure User is able to Create Campus Resource-@testcaseID=19741
    Given user is on "<Overview_Page>" page
    When user clicks on "<Campus Resource>" link under additional setup
    And user clicks on add campus resource button
    And fill details for campus resource with "<CampusResourceName>" , "<StaffName>", "<Phone>", "<Email>", "<Location>", "<URL>", "<Description>", "<Can Viewed by student>", "<can Recieve Refferal>"
    And click on save button to save Campus Resource
    Then user is able to see "<Success_message>" in the alert
    And user is able to view Campus Resource in campus Resources list
    Examples:
      | Overview_Page | Campus Resource | CampusResourceName | StaffName | Phone   | Email      | Location | URL                     | Description       | Can Viewed by student | can Recieve Refferal | Success_message                    |
      | #/overview    | Campus Resource | Staff              | qanupur   | 9876565 | Staffemail | Delhi    | https://www.synapse.com | health DepartMent | yes                   | yes                  | Campus Resource saved successfully |

  Scenario Outline:Ensure User is able to edit Campus Resource-@testcaseID=19742
    When user clicks on "<edit>" icon on campus resource page
    And fill details for campus resource with "<CampusResourceName>" , "<StaffName>", "<Phone>", "<Email>", "<Location>", "<URL>", "<Description>", "<Can Viewed by student>", "<can Recieve Refferal>"
    And click on save button to save Campus Resource
    Then user is able to see "<Success_message>" in the alert
    And user is able to view Campus Resource in campus Resources list
    Examples:
      | Campus Resource  | edit | CampusResourceName | StaffName | Phone     | Email         | Location | URL                     | Description       | Can Viewed by student | can Recieve Refferal | Success_message                       |
      | #/campusresource | edit | Staff              | qaparas   | 987656566 | Staffemail345 | Delhi    | https://www.synapse.com | health DepartMent | No                    | No                   | Campus Resource Updated successfully. |

  Scenario Outline:Ensure User is able to Delete Campus Resource-@testcaseID=19743
    When user clicks on "<delete>" icon on campus resource page
    And user clicks on Delete button on DialogBox
    Then user is able to see "<Success_message>" in the alert
    And user is not able to view Campus Resource  in campus Resources list
    Examples:
      | Campus Resource  | delete | Success_message                       | CampusResourceName |
      | #/campusresource | delete | Campus Resource Deleted successfully. | Staff              |




 
