Feature: CoordinatorStaticList

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24294
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user can create Static List-@testcaseID=19694
    Given user is on "<Overview_Page>" page
    When user clicks on "<StaticList_Tab>" tab
    And user clicks on Create New List button on static list page
    And user fills data for "<StaticList_Name>" and "<StaticList_Description>"
    Then user is able to see "<Success_message>" in the alert
    And user is able to view created static List in list

    Examples:
      | Overview_Page | StaticList_Name | StaticList_Description | StaticList_Tab | Success_message                  |
      | #/overview    | Static List     | StaticList Description | Static Lists   | Static List created successfully |

  Scenario Outline: Verify user can edit Static List-@testcaseID=19695
    When user edits Static List with "<StaticList_Name>" and "<StaticList_Description>"
    Then user is able to see "<Success_message>" in the alert
    And user is able to view the edited static List on the page

    Examples:
      | StaticList_Name   | StaticList_Description | Success_message                  |
      | Edited StaticList | Edited Description     | Static List updated successfully |

  Scenario Outline: Verify user can share Static List-@testcaseID=19696
    When user shares Static List with "<User_Name>"
    Then user is able to see "<Success_message>" in the alert

    Examples:
      | StaticList_Page | User_Name | Success_message                 |
      | #/static-lists  | qaparas   | Static List shared successfully |

  Scenario Outline: Verify user is able to add student to the staticList-@testcaseID=19697
    When user clicks on "<StaticList_Name>"
    And user add student "<Student_Name>" to the list
    Then user is able to see "<Success_message>" in the alert
    And user is able to view student "<Student_Name>" in the list
    And user is able to see "<Student_count>" for the "<StaticList_Name>" in the table

    Examples:
      | StaticList_Name   | Student_Name | Success_message                    | Student_count |
      | Edited StaticList | Deborah      | Student added to list successfully | 1             |

  Scenario Outline: Verify user can delete Static List-@testcaseID=19698
    When user deletes the static List with "<StaticList_Name>"
    Then user is able to see "<Success_message>" in the alert
    And user is not able to view the deleted static List on page

    Examples:
      | StaticList_Name   | Success_message                  | StaticList_Page |
      | Edited StaticList | Static List deleted successfully | #/static-lists  |



