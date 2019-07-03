Feature: CoordinatorHelp

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24291
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |


  Scenario: Verify Coordinator is able to view knowledge base link-@testcaseID=22747
    When users click on help icon on overview page
    Then user is able to see knowledge base link

  Scenario Outline: Verify Coordinator is able to add link-@testcaseID=22748
    When user clicks on "<add link>" button on help page
    And user fill the url field with "<URL>"
    And user fills title and description with "<Title>" and "<Description>" for Link
    And user clicks on save button on help page
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<created>" link in table
    Examples:
      | add link | Success_message                  | URL            | Title | Description | created |
      | Link     | Link has been added successfully | www.google.com | Title | Description | create  |

  Scenario Outline: Verify Coordinator is able to edit link-@testcaseID=22749
    When user clicks on "<Edit>" icon of link
    And user edits the url field with "<URL>"
    And user edits title and description with "<Title>" and "<Description>" for Link
    And user clicks on save button on help page
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<edited>" link in table
    Examples:
      | Edit | Success_message                    | URL            | Title | Description | edited |
      | edit | Link has been updated successfully | www.google.com | Title | Description | edited |

  Scenario Outline: Verify Coordinator is able to remove  link-@testcaseID=22750
    When user clicks on "<Remove>" icon of link
    And user clicks confirm remove button
    Then user is able to see "<Success_message>" in the alert
    And user is not able to see "<Edited>" link in table
    Examples:
      | Remove | Success_message                                | Edited |
      | Remove | Document or Link has been deleted successfully | Edited |


  Scenario Outline: Verify Coordinator is able to add Document-@testcaseID=22751
    When user clicks on "<add Doc>" button on help page
    And user attach a file
    And user fills title and description with "<Title>" and "<Description>" for document
    And user clicks on save button on help page
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<created>" document in table
    Examples:
      | add Doc  | Success_message                         | URL            | Title | Description | created |
      | Document | Document has been uploaded successfully | www.google.com | Title | Description | create  |


  Scenario Outline: Verify Coordinator is able to edit Document-@testcaseID=22752
    When user clicks on "<Edit>" icon of document
    And user edits title and description with "<Title>" and "<Description>" for document
    And user clicks on save button on help page
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<edited>" document in table
    Examples:
      | Edit | Success_message                        | URL            | Title | Description | edited |
      | edit | Document has been updated successfully | www.google.com | Title | Description | edited |


  Scenario Outline: Verify Coordinator is able to remove  Document-@testcaseID=22753
    When user clicks on "<Remove>" icon of document
    And user clicks confirm remove button
    Then user is able to see "<Success_message>" in the alert
    And user is not able to see "<Edited>" document in table
    Examples:
      | Remove | Success_message                                | Edited |
      | Remove | Document or Link has been deleted successfully | Edited |


  Scenario Outline: Verify Coordinator is able to file ticket-@testcaseID=22754
    When user clicks on file ticket link
    And user fills the details for ticket with "<Category>", "<Subject>" , "<Description>" , "<Screenshot>"
    And user clicks on file ticket Button
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Category         | Subject | Description | Screenshot | Success_message                            |
      | Data files / FTP | subject | Description | test.png   | Support Ticket has been added successfully |