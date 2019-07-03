Feature: FacultyStaticList

  Scenario Outline:Verify Faculty can access the skyfactor application-@testcaseID=24265
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then Faculty land on Dashboard page

    Examples:
      | UserName                   | Password |
      | qaFaculty08@mailinator.com | Qait@123 |

  Scenario Outline: Verify Faculty can create Static List-@testcaseID=22899
    Given user is on "<Dashboard_Page>" page
    When user clicks on "<StaticList_Tab>" tab
    And user clicks on Create New List button on static list page
    And user fills data for "<StaticList_Name>" and "<StaticList_Description>"
    Then user is able to see "<Success_message>" in the alert
    And user is able to view created static List in list

    Examples:
      | Dashboard_Page | StaticList_Name | StaticList_Description | StaticList_Tab | Success_message                  |
      | #/dashboard    | Static List     | StaticList Description | Static Lists   | Static List created successfully |

  Scenario Outline: Verify Faculty can edit the created Static List-@testcaseID=22900
    When user edits Static List with "<StaticList_Name>" and "<StaticList_Description>"
    Then user is able to see "<Success_message>" in the alert
    And user is able to view the edited static List on the page

    Examples:
      | StaticList_Name   | StaticList_Description | Success_message                  |
      | Edited StaticList | Edited Description     | Static List updated successfully |

  Scenario Outline: Verify Faculty can share the Static List-@testcaseID=22901
    When user shares Static List with "<User_Name>"
    Then user is able to see "<Success_message>" in the alert

    Examples:
      | StaticList_Page | User_Name | Success_message                 |
      | #/static-lists  | qaparas   | Static List shared successfully |

  Scenario Outline: Verify Faculty is able to add student to the staticList-@testcaseID=22902
    When user clicks on "<StaticList_Name>"
    And user add student "<Student_Name>" to the list
    Then user is able to see "<Success_message>" in the alert
    And user is able to view student "<Student_Name>" in the list
    And user is able to see "<Student_count>" for the "<StaticList_Name>" in the table

    Examples:
      | StaticList_Name   | Student_Name | Success_message                    | Student_count |
      | Edited StaticList | Deborah      | Student added to list successfully | 1             |

  Scenario Outline: Verify Faculty can delete Static List-@testcaseID=22903
    When user deletes the static List with "<StaticList_Name>"
    Then user is able to see "<Success_message>" in the alert
    And user is not able to view the deleted static List on page

    Examples:
      | StaticList_Name   | Success_message                  | StaticList_Page |
      | Edited StaticList | Static List deleted successfully | #/static-lists  |



