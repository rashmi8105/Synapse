Feature: FacultySearch

  Scenario Outline:Verify Faculty can access the skyfactor application-@testcaseID=24224
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then Faculty land on Dashboard page

    Examples:
      | UserName                   | Password |
      | qaFaculty08@mailinator.com | Qait@123 |

  Scenario Outline: Verify Faculty is able to create and view custom search-@testcaseID=22904
    Given user is on "<Dashboard_Page>" page
    When user clicks on "<Search_Tab>" tab
    And user clicks on "<Custom_Search>" subtab
    And user clicks "<Risk_Panel>" panel
    And user selects "<Risk_level>" risk level
    And user clicks on SaveSearchBtn button
    And user fills "<Search_Name>" in search field
    And Click on save Button displayed in small window
    Then user is able to see "<Success_message>" in the alert
    And user navigated to "<SavedSearch>" page
    And user is able to view created search "<Search_Name>"

    Examples:
      | Dashboard_Page | Search_Tab | Custom_Search | Risk_Panel | Risk_level | Save_SearchBtn | Search_Name       | Success_message           | SavedSearch   |
      | #/dashboard    | Search     | Custom Search | Risk level | gray       | SaveSearch     | Automation_Search | Search saved successfully | #/savedsearch |


  Scenario Outline: Verify Faculty is able to edit and view custom search-@testcaseID=22905
    When user clicks on edit search icon in front of "<created_Search_Name>"
    And user clicks "<Activity_Panel>" panel
    And user selects "<OptionSelected>" option from Group on custom search window
    And user clicks on SaveSearchBtn button
    And user fills "<Search_Name>" in search field
    And Click on save Button displayed in small window
    Then user is able to see "<Success_message>" in the alert
    And user is able to view edited search "<Search_Name>"
    Examples:
      | created_Search_Name | Activity_Panel | OptionSelected | Search_Name  | Success_message             |
      | Automation_Search   | Groups         | All Students   | editedsearch | Search updated successfully |

  Scenario Outline: Verify Faculty is able to share custom search-@testcaseID=22906
    When user clicks on share search icon in front of "<edited_Search_Name>"
    And user fills "<Search_Name>" in search field
    And user fills and selects "<faculty_Name>" faculty to share with
    And user clicks on Share button
    Then user is able to see "<Success_message>" in the alert

    Examples:
      | Search_Name  | faculty_Name | Success_message            | edited_Search_Name |
      | SharedSearch | qaparas      | Search shared successfully | editedsearch       |

  Scenario Outline: Verify Faculty is able to view shared custom search under shared subtab-@testcaseID=22907
    When user clicks on "<Shared_Search>" subtab
    Then user is able to view shared search "<Search_Name>"

    Examples:
      | Search_Name  | Shared_Search   |
      | SharedSearch | Shared Searches |

  Scenario Outline: Verify Faculty is able to view the list of students on cliking on the searchName-@testcaseID=22908
    When user clicks on "<Saved_Search>" subtab
    And clicks on "<Search_Name>" search name
    Then user is able to view the list of the students

    Examples:
      | Search_Name     | Saved_Search   |
      | Edited_Searches | Saved Searches |

  Scenario Outline: Ensure that Faculty navigates to student profile page on clicking on student name displayed in the list-@testcaseID=22909
    When user clicks on the student name "<Student_name>"
    Then user is able to navigate to the "<Student_name>" profile page

    Examples:
      | Student_name |
      | Adam         |

  Scenario Outline:Verify Faculty is able to delete the search and is not able to view it-@testcaseID=22910
    When user clicks on "<Search_Tab>" tab
    And user clicks on "<Saved_Search>" subtab
    And user delete search icon in front of "<Search_Name>"
    And clicks on delete button displayed on modal window
    Then user is able to see "<Success_message>" in the alert
    And user is not able to view the search "<Search_Name>"

    Examples:
      | Search_Tab | Saved_Search   | Success_message             | Search_Name  |
      | Search     | Saved Searches | Search deleted successfully | editedsearch |
