Feature: CoordinatorStudentUpload

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24078
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify Student is able to upload student in the application-@testcaseID=19784
    Given user is on "<Page>" page
    When user clicks on "<ManageStudent_Button>" button
    And user uploads "<NumberofStudents>" of valid student user
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table

    Examples:
      | Page       | ManageStudent_Button | NumberofStudents | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError |
      | #/overview | Students             | 3                | 3                 | 3         | 0           | 0          |

  Scenario Outline: Verify user is able to upload faculty in the application-@testcaseID=19785
    Given user is on "<Page>" page
    When user clicks on "<ManageFaculty_Button>" button
    And user uploads "<NumberofFaculty>" of valid faculty user
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | Page       | ManageFaculty_Button | NumberofFaculty | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError |
      | #/overview | Faculty              | 1               | 1                 | 1         | 0           | 0          |

  Scenario Outline: Verify user is not able to upload invalid faculty in the application-@testcaseID=19786
    Given user is on "<Page>" page
    When user clicks on "<ManageFaculty_Button>" button
    And user uploads "<NumberofFaculty>" of invalid faculty user
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table

    Examples:
      | Page       | ManageFaculty_Button | NumberofFaculty | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError |
      | #/overview | Faculty              | 1               | 1                 | 0         | 0           | 1          |

  Scenario Outline: Ensure user is able to create ISP with Text Data Type-@testcaseID=19787
    Given user is on "<institute_profile_Page>" page
    When users clicks on Add another profile item button
    And select "<Text_Data_Type>" from dropdown
    And fill details for "<Text_Data_Type>" type ISP with details "<ISPName>", "<ISPDescription>"
    And select "<Calendar_assignment_None>" for ISP
    And click on Save button to save ISP
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<Text_Data_Type>" ISP in ISP list

    Examples:
      | institute_profile_Page   | Text_Data_Type | Success_message                     | Calendar_assignment_None | ISPName | ISPDescription     |
      | #/institutionprofileitem | Text           | Profile Item submitted Successfully | None                     | TextISP | TextISPDescription |

  Scenario Outline: Verify Student is able to upload student with profile in the application-@testcaseID=19788
    Given user is on "<Page>" page
    When user clicks on "<ManageStudent_Button>" button
    And user uploads "<NumberofStudents>" of valid student user with profile "<Text_Data_Type>" type
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table

    Examples:
      | Page       | ManageStudent_Button | NumberofStudents | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | Text_Data_Type |
      | #/overview | Students             | 1                | 1                 | 1         | 0           | 0          | Text           |

  Scenario Outline: Verify Student is able to upload inactive student in the application-@testcaseID=19789
    Given user is on "<Page>" page
    When user clicks on "<ManageStudent_Button>" button
    And user uploads "<Inactive>" type student having "<External_ID>","<First_Name>","<Last_Name>" and "<Primary_Email>"
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table

    Examples:
      | Page       | ManageStudent_Button | Inactive | External_ID | First_Name | Last_Name | Primary_Email           | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError |
      | #/overview | Students             | inactive | 1123        | Adam       | B         | autoqa01@mailinator.com | 1                 | 0         | 1           | 0          |


  Scenario Outline: Verify user is able to view INACTIVE status on student dashboard-@testcaseID=19790
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    Then user is able to navigate to the "<Student_name>" profile page
    And user is able to view INACTIVE status on the page

    Examples:
      | Search_Tab | Student_name |
      | Search     | Adam         |

  Scenario Outline: Verify Student is able to upload active student in the application-@testcaseID=19791
    Given user is on "<Page>" page
    When user clicks on "<ManageStudent_Button>" button
    And user uploads "<active>" type student having "<External_ID>","<First_Name>","<Last_Name>" and "<Primary_Email>"
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table

    Examples:
      | Page       | ManageStudent_Button | Inactive | External_ID | First_Name | Last_Name | Primary_Email           | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError |
      | #/overview | Students             | inactive | 1123        | Adam       | B         | autoqa01@mailinator.com | 1                 | 0         | 1           | 0          |

  Scenario Outline: Verify user is not able to view INACTIVE status on student dashboard-@testcaseID=19792
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    Then user is able to navigate to the "<Student_name>" profile page
    And user is not able to view INACTIVE status on the page

    Examples:
      | Search_Tab | Student_name |
      | Search     | Adam         |