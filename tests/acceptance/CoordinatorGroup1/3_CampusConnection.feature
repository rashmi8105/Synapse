Feature: CoordinatorCampusConnection

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24203
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:Verify Coordinator can set a Campus coonection for a student-@testcaseID=24204
    Given user is on "<Overview_Page>" page
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    And user is able to navigate to the "<Student_name>" profile page
    And user clicks on "<Details>" tab on student page
    And user clicks on "<Campus Connections>" link under student details tab
    And user sets "<Faculty>" as Campus Connection
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<Primary_connection_Name>", "<primary_connection_email>" and "<Phone_Number>" on campus connection page
    Examples:
      | Details | Overview_Page | Search_Tab | Student_name | Campus Connections | Faculty | Success_message                         | Primary_connection_Name | primary_connection_email | Phone_Number |
      | Details | #/overview    | Search     | Deborah      | Campus Connections | qanupur | Primary Campus Connection has been set. | qanupur                 | qanupur@mailinator.com   | 1234567890   |
   


 