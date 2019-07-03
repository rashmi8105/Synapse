Feature: CoordinatorSystemAlert

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24295
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user can Create System Alert-@testcaseID=19793
    Given user is on "<Page>" page
    When user clicks on "<TabName>" tab
    And user clicks on Create New Message button on System Alert page
    And Fill "<Message>" and "<MessageType>" and "<StopDate/Time_option>" in modal window
    Then user is able to see "<Success_message>" in the alert
    And user is able to view the system alert with message "<Message>" in the list
    Examples:
      | Page       | TabName      | Message      | MessageType | StopDate/Time_option | Success_message                   |
      | #/overview | System Alert | System Alert | Banner      | One Day              | System Alert Created Successfully |

  Scenario Outline: Verify user can edit the System Alert-@testcaseID=19794
    When user edits the created system alert "<Message>"
    Then  user is able to see "<Success_message>" in the alert
    And user is able to view the system alert with message "<Message_edited>" in the list

    Examples:
      | Page              | Message      | Message_edited     | Success_message                   |
      | #/system-messages | System Alert | SystemAlert_Edited | System Alert Updated Successfully |

  Scenario Outline: Verify user can delete the System Alert-@testcaseID=19795
    When user deletes the created system alert "<Message_edited>"
    Then  user is able to see "<Success_message>" in the alert
    And user is not able to view the system alert "<Message_edited>" in the list

    Examples:
      | Page              | Message      | Message_edited     | Success_message                   |
      | #/system-messages | System Alert | SystemAlert_Edited | System Alert deleted Successfully |
