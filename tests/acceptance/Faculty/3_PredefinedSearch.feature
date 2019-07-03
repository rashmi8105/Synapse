Feature: FacultyPredefinedSearch

  Scenario Outline:Verify Faculty can access the skyfactor application-@testcaseID=24229
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then Faculty land on Dashboard page

    Examples:
      | UserName                   | Password |
      | qaFaculty08@mailinator.com | Qait@123 |

  Scenario Outline:Verify Faculty can see the list of predefined searches under the Populations of Students category-@testcaseID=22958
    Given user is on "<Dashboard_Page>" page
    When user clicks on "<Search_Tab>" tab
    And user clicks on "<Predefined_Search>" subtab
    And user clicks on "<Population_panel>" on Right Panel under Predefined Search
    Then user is able to view "<Link1>" link on the page
    And user is able to view "<Link2>" link on the page
    And user is able to view "<Link3>" link on the page
    And user is able to view "<Link4>" link on the page
    And user is able to view "<Link5>" link on the page

    Examples:
      | Dashboard_Page | Search_Tab | Predefined_Search   | Population_panel        | Link1           | Link2                         | Link3            | Link4                                | Link5                  |
      | #/dashboard    | Search     | Predefined Searches | Populations of Students | All my students | My primary campus connections | At-risk students | Students with a high intent to leave | High priority students |

  Scenario Outline:Verify Faculty can see the list of predefined searches under Academic Performance category-@testcaseID=22959
    When user clicks on "<Academic_panel>" on Right Panel under Predefined Search
    Then user is able to view "<Link1>" link on the page
    And user is able to view "<Link2>" link on the page
    And user is able to view "<Link3>" link on the page
    And user is able to view "<Link4>" link on the page
    And user is able to view "<Link5>" link on the page
    And user is able to view "<Link6>" link on the page
    And user is able to view "<Link7>" link on the page
    And user is able to view "<Link8>" link on the page

    Examples:
      | Academic_panel       | Link1                | Link2                 | Link3                           | Link4                           | Link5                                        | Link6                     | Link7                     | Link8                                  |
      | Academic Performance | High risk of failure | Four or more absences | In-progress grade of C or below | In-progress grade of D or below | Two or more in-progress grades of D or below | Final grade of C or below | Final grade of D or below | Two or more final grades of D or below |

  Scenario Outline:Verify Faculty can see the list of predefined searches under Activity category-@testcaseID=22960
    When user clicks on "<Activity_panel>" on Right Panel under Predefined Search
    Then user is able to view "<Link1>" link on the page
    And user is able to view "<Link2>" link on the page
    And user is able to view "<Link3>" link on the page

    Examples:
      | Activity_panel | Link1                              | Link2                                     | Link3                                                              |
      | Activity       | Students with interaction contacts | Students without any interaction contacts | Students who have not been reviewed by me since their risk changed |

  Scenario Outline:Verify Faculty can see the text "Never" in front of the links that are not yet visited once-@testcaseID=22961
    Given user is on "<Dashboard_Page>" page
    When user clicks on "<Search_Tab>" tab
    And user clicks on "<Predefined_Search>" subtab
    And user clicks on "<Activity_panel>" on Right Panel under Predefined Search
    Then user is able to see Never text in front "<Search_In_Activity>" link under Activity panel
    Examples:
      | Dashboard_Page | Search_Tab | Predefined_Search   | Activity_panel | Search_URL | Search_In_Activity                        |
      | #/dashboard    | Search     | Predefined Searches | Activity       | #/search   | Students without any interaction contacts |


  Scenario Outline:Verfiy Faculty is able to run a Predefined search and is able to see updated date in last run column-@testcaseID=22962
    When user clicks on "<Academic_panel>" on Right Panel under Predefined Search
    Then user clicks on "<Search_Name>" search link
    When user clicks on "<Search_Tab>" tab
    And user clicks on "<Predefined_Search>" subtab
    And user clicks on "<Academic_panel>" on Right Panel under Predefined Search
    Then user is able to see updated last run column with current date for "<Search_Name>" search

    Examples:
      | Academic_panel       | Search_Name          | Search_Tab | Predefined_Search   |
      | Academic Performance | High risk of failure | Search     | Predefined Searches |


