Feature: FacultyHelp

  Background:

  Scenario Outline: Verify Faculty can access the skyfactor application-@testcaseID=24227
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then Faculty land on Dashboard page

    Examples:
      | UserName                   | Password |
      | qaFaculty08@mailinator.com | Qait@123 |


  Scenario Outline: Verify Faculty is able to view knowledge base link-@testcaseID=22948
    When users click on help icon on overview page
    Then user is able to see knowledge base link
    And user is able to see "<Coordinator_Email_ID>" on Help page
    Examples:
      | Coordinator_Email_ID   |
      | qanupur@mailinator.com |
