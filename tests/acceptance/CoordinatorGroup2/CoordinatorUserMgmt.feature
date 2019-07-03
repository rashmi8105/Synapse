Feature: CoordinatorUserMgmt

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24079
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to validate the buttons and link present under Coordinator panel-@testcaseID=24080
    Given user is on "<Overview_Page>" page
    When user clicks on "<Settings>" link under additional setup
    And user is able to expand "<Coordinator>" panel
    Then user is able to see expanded "<Coordinator>" panel
    And user is able to view Add new button and or choose an existing user link

    Examples:
      | Overview_Page | Settings | Coordinator  |
      | #/overview    | Settings | Coordinators |

  Scenario Outline: Verify user is able check the Cancel button functionality on Add new Coordinator-@testcaseID=24081
    When user grabs count and clicks on Add New button
    And user fills "<FirstName>" as first Name
    And user fills "<LastName>" as last Name
    And user fills "<Title>" as title
    And user fills "<ContactInfo>" as contactInfo
    And user fills "<Phone>" as phoneNumber
    And user fills "<ID>" as ID
    And user selects "<Mobile_checked>" option
    And user selects "<Type>" as Coordinator Type
    And user clicks on Cancel button
    Then user is not able to see the added coordinator
    And Count of the coordinator remains same

    Examples:
      | FirstName | LastName | Title | ContactInfo | Phone     | Mobile_checked | ID | Type                |
      | FisrtUser | LastUser | Mr    | Test        | 979799797 | yes            | 6  | Primary coordinator |

  Scenario Outline: Verify user is able to Add New Coordinator-@testcaseID=24082
    When user grabs count and clicks on Add New button
    And user fills "<FirstName>" as first Name
    And user fills "<LastName>" as last Name
    And user fills "<Title>" as title
    And user fills "<ContactInfo>" as contactInfo
    And user fills "<Phone>" as phoneNumber
    And user fills "<ID>" as ID
    And user selects "<Mobile_checked>" option
    And user selects "<Type>" as Coordinator Type
    And user clicks on save button to save user
    Then user is able to see "<Success_message>" in the alert
    And user is able to see the added coordinator in the list
    And count of the coordinator increases by one

    Examples:
      | FirstName | LastName | Title | ContactInfo | Phone     | Mobile_checked | ID | Type                | Success_message                         |
      | FisrtUser | LastUser | Mr    | Test        | 979799797 | Yes            | 6  | Primary coordinator | Campus coordinator created successfully |

  Scenario Outline: Verify user is able to edit Coordinator-@testcaseID=24083
    When user clicks on Edit icon
    And user fills "<FirstName>" as first Name
    And user fills "<LastName>" as last Name
    And user fills "<Title>" as title
    And user fills "<ContactInfo>" as contactInfo
    And user fills "<Phone>" as phoneNumber
    And user fills "<ID>" as ID
    And user selects "<Mobile_checked>" option
    And user selects "<Type>" as Coordinator Type
    And user clicks on save button to save user
    Then user is able to see "<Success_message>" in the alert
    And user is able to see the edited coordinator in the list
    And Count of the coordinator remains same

    Examples:
      | FirstName       | LastName        | Title     | ContactInfo | Phone     | Mobile_checked | ID | Type                | Edit | Success_message                             |
      | EditedFirstUser | Edited_LastUser | Edited_Mr | Edited_Test | 979799797 | Yes            | 6  | Primary coordinator | edit | Successfully Updated the campus coordinator |

  Scenario Outline: Verify user is unable to delete the coordinator-@testcaseID=24084
    When user clicks on Delete icon
    And Click on Remove button displayed on the modal window
    Then user is able to see "<Error_message>" in the alert

    Examples:
      | Error_message                                                                                                                    |
      | User cannot be deleted. We are unable to delete users who have activity or academic data associated with their Mapworks account. |












  

