Feature: CoordinatorCustomSearchProfile

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24085
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to upload student with year dependent profile-@testcaseID=24086
    Given user is on "<Page>" page
    When user clicks on "<ManageStudent_Button>" button
    And user uploads student with year dependent profile "<Year_DependentProfile>" with value "<Option_Value>"
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | Page       | ManageStudent_Button | NumberofStudents | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | Year_DependentProfile | Option_Value |
      | #/overview | Students             | 1                | 1                 | 1         | 0           | 0          | RetentionTrack        | 1            |

  Scenario Outline: Verify user is able to see the uploaded student through Custom serach-@testcaseID=24087
    When user clicks on "<Search_Tab>" tab
    And user clicks on "<Custom_Search>" subtab
    And user clicks "<Profile_Panel>" panel
    And user clicks on "<Profile_Block>" link displayed on the page
    And user clicks on "<Profile_Block_Name>" link displayed on the page
    And user clicks on "<Uploded_ProfileBlock>" link displayed on the page
    And user selects "<Option_Value>" checkbox for "<Uploded_ProfileBlock>" profile block
    And user click on SearchButton displayed on the modal window
    Then user should be able to see the uploaded student "<Student_Type>" on the page


    Examples:
      | Search_Tab | Custom_Search | Profile_Panel | Profile_Block | Profile_Block_Name | Uploded_ProfileBlock | Option_Value | Student_Type              |
      | Search     | Custom Search | Profile       | Profile Block | Retention          | RetentionTrack       | Yes          | Uploaded_Year_Profile_std |


  Scenario Outline: verify user is able to navigate to the student page on clicking the student name displayed on serach page-@testcaseID=24088
    When user clicks on "<Student_Type>" displayed on the page
    Then user is able to navigate to the "<Student_Type>" profile page

    Examples:
      | Student_Type              |
      | Uploaded_Year_Profile_std |

  Scenario Outline: Verify user is able to upload student in the application with term dependent profile-@testcaseID=24089
    Given user is on "<Page>" page
    When user clicks on "<ManageStudent_Button>" button
    And user uploads student with term dependent profile "<Term_Dependent_Profile>" with value "<Option_value>"
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | Page       | ManageStudent_Button | NumberofStudents | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | Term_Dependent_Profile | Option_value |
      | #/overview | Students             | 1                | 1                 | 1         | 0           | 0          | CampusResident         | 0            |

  Scenario Outline: Verify user is able to search uploaded term dependent student-@testcaseID=24090
    When user clicks on "<Search_Tab>" tab
    And user clicks on "<Custom_Search>" subtab
    And user clicks "<Profile_Panel>" panel
    And user clicks on "<Profile_Block>" link displayed on the page
    And user clicks on "<Profile_Block_Name>" link displayed on the page
    And user clicks on "<Uploded_ProfileBlock>" link displayed on the page
    And user selects "<Option_Value>" checkbox for "<Uploded_ProfileBlock>" profile block
    And user click on SearchButton displayed on the modal window
    Then user should be able to see the uploaded student "<Student_Type>" on the page
    Examples:
      | Search_Tab | Custom_Search | Profile_Panel | Profile_Block | Profile_Block_Name | Uploded_ProfileBlock | Option_Value | Student_Type             |
      | Search     | Custom Search | Profile       | Profile Block | Demographic Record | CampusResident       | Off          | UploadedTerm_Profile_std |

  Scenario Outline: verify user is able to navigate to the Term dependent student page on clicking the student name displayed on serach page-@testcaseID=24091
    When user clicks on "<Student_Type>" displayed on the page
    Then user is able to navigate to the "<Student_Type>" profile page

    Examples:
      | Student_Type          |
      | Uploaded_Term_Profile |


  Scenario Outline: Verify user is able to upload student in the application with year dependent ISP-@testcaseID=24092
    Given user is on "<Page>" page
    When user clicks on "<ManageStudent_Button>" button
    And user uploads student with year dependent ISP "<Year_Dependent_ISP>" with value "<Date_Value>"
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | Page       | ManageStudent_Button | NumberofStudents | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | Year_Dependent_ISP | Date_Value |
      | #/overview | Students             | 1                | 1                 | 1         | 0           | 0          | Datedonotdelete    | current    |

  Scenario Outline: Verify user is able to search uploaded  year dependent Date dependent student-@testcaseID=24093
    When user clicks on "<Search_Tab>" tab
    And user clicks on "<Custom_Search>" subtab
    And user clicks "<Profile_Panel>" panel
    And user clicks on "<ISP>" link displayed on the page
    And user clicks on "<ISP_Name>" link displayed on the page
    And user clicks on Start Date box
    And user selects start date "<Start_date>" from the calender
    And user clicks on End Date box
    And user selects end date "<End_date>" from the calender
    And user click on SearchButton displayed on the modal window
    Then user should be able to see the uploaded student "<Student_Type>" on the page
    Examples:
      | Search_Tab | Custom_Search | Profile_Panel | ISP | ISP_Name        | Start_date | End_date | Student_Type              |
      | Search     | Custom Search | Profile       | ISP | Datedonotdelete | current    | current  | Uploaded_Term_Profile_std |

  Scenario Outline: verify user is able to navigate to the Yeardependent student page on clicking the student name displayed on serach page-@testcaseID=24094
    When user clicks on "<Student_Type>" displayed on the page
    Then user is able to navigate to the "<Student_Type>" profile page
    Examples:
      | Student_Type      |
      | Uploaded_Year_ISP |


  Scenario Outline: Verify user is able to upload student in the application with term dependent ISP-@testcaseID=24095
    Given user is on "<Page>" page
    When user clicks on "<ManageStudent_Button>" button
    And user uploads student with term dependent ISP "<Term_dependent_ISP>" with value "<Value>"
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | Page       | ManageStudent_Button | NumberofStudents | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | Term_dependent_ISP | Value |
      | #/overview | Students             | 1                | 1                 | 1         | 0           | 0          | NumberDonotdelete  | 2500  |

  Scenario Outline: Verify user is able to search uploaded Student-@testcaseID=24096
    When user clicks on "<Search_Tab>" tab
    And user clicks on "<Custom_Search>" subtab
    And user clicks "<Profile_Panel>" panel
    And user clicks on "<ISP>" link displayed on the page
    And user clicks on "<ISP_Name>" link displayed on the page
    And user fills data "<Value>" in single text box
    And user click on SearchButton displayed on the modal window
    Then user should be able to see the uploaded student "<Student_Type>" on the page
    Examples:
      | Search_Tab | Custom_Search | Profile_Panel | ISP | ISP_Name          | Value | Student_Type              |
      | Search     | Custom Search | Profile       | ISP | NumberDonotdelete | 2500  | Uploaded_Term_Profile_std |

  Scenario Outline: Verify user is able to see term dependent profile on Student Profile Page-@testcaseID=24097
    When user clicks on "<Student_Type>" displayed on the page
    Then user is able to navigate to the "<Student_Type>" profile page
    Examples:
      | Student_Type      |
      | Uploaded_Term_ISP |


