Feature: StudentCampus Resource

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24231
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:Ensure User is able to Create Campus Resource-@testcaseID=22969
    Given user is on "<Overview_Page>" page
    When user clicks on "<Campus Resource>" link under additional setup
    And user clicks on add campus resource button
    And fill details for campus resource with "<CampusResourceName>" , "<StaffName>", "<Phone>", "<Email>", "<Location>", "<URL>", "<Description>", "<Can Viewed by student>", "<can Recieve Refferal>"
    And click on save button to save Campus Resource
    Then user is able to see "<Success_message>" in the alert
    And user is able to view Campus Resource in campus Resources list
    Examples:
      | Overview_Page | Campus Resource | CampusResourceName | StaffName | Phone   | Email      | Location | URL                     | Description       | Can Viewed by student | can Recieve Refferal | Success_message                    |
      | #/overview    | Campus Resource | Staff              | qanupur   | 9876565 | Staffemail | Delhi    | http://www.synapse.com/ | health DepartMent | yes                   | yes                  | Campus Resource saved successfully |

  Scenario Outline:Verify Student can access the skyfactor application-@testcaseID=22970
    Given user is on skyfactor login page
    When Student login into the application with "<EmailID>"
    Then Student lands on Survey page

    Examples:
      | EmailID                 |
      | autoqa08@mailinator.com |

  Scenario Outline: Ensure that campus resource is displaying at student end-@testcaseID=22971
    When student clicks on "<Campus_Resource>" tab
    Then student see "<Campus_Resource>" as header on student page
    And student is able to see campus resource details
    Examples:
      | Campus_Resource  |
      | Campus Resources |

  Scenario: Verify that student is able to access link added in campus resources-@testcaseID=22972
    When Student clicks on website link on student page
    Then Student is able to navigate to Campus Resources website


  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24282
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:Ensure that coordinator is able to Delete Campus Resource-@testcaseID=22973
    Given user is on "<Overview_Page>" page
    When user clicks on "<Campus Resource>" link under additional setup
    When user clicks on "<delete>" icon on campus resource page
    And user clicks on Delete button on DialogBox
    Then user is able to see "<Success_message>" in the alert
    And user is not able to view Campus Resource  in campus Resources list
    Examples:
      | Overview_Page | Campus Resource | delete | Success_message                       |
      | #/overview    | Campus Resource | delete | Campus Resource Deleted successfully. |

  Scenario Outline:Verify Student can access the skyfactor application-@testcaseID=24232
    Given user is on skyfactor login page
    When Student login into the application with "<EmailID>"
    Then Student lands on Survey page

    Examples:
      | EmailID                 |
      | autoqa08@mailinator.com |

  Scenario Outline:Ensure that campus resource is not displaying at student end-@testcaseID=22974
    When student clicks on "<Campus_Resource>" tab
    Then student see "<Campus_Resource>" as header on student page
    And student is not able see campus resource details

    Examples:
      | Campus_Resource  |
      | Campus Resources |