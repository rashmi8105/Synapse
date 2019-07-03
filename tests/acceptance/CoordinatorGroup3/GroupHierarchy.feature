Feature: CoordinatorGroupHierarchy

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=23949
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Ensure that user is able to create a new group-@testcaseID=23950
    Given user is on "<groupsummary>" page
    When user clicks on Add Another Group button
    And user fills "<Created_Group_Name>" in GroupName field
    And user fills "<Created_Group_ID>" in GroupId field
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<Created_Group_Name>" on group summary page
    And user is able to see "<Created_Group_ID>" on group summary page
    And user is able to see "<Created_Group_ID>" against "<Created_Group_Name>" in Group list

    Examples:
      | groupsummary   | Manage_Groups | Created_Group_Name                | Created_Group_ID                | Success_message                  |
      | #/groupsummary | Groups        | CreatedGroupNameForGroupHierarchy | CreatedGroupIDForGroupHierarchy | The Group is added Successfully. |


  Scenario Outline: Ensure that user is able to add faculty to group with Permission-@testcaseID=23951
    Given user is on "<groupsummary>" page
    When user clicks on "<Created_Group>" group on group summary page
    And user adds "<Faculty_Name>" with permission "<PermissionName>" to group
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | groupsummary   | Overview_page | Success_message                | PermissionName | Faculty_Name | Created_Group                 |
      | #/groupsummary | #/overview    | Successfully Updated the Group | AllAccess      | Kevin        | CreatedGroupForGroupHierarchy |


  Scenario Outline:User is able to see Faculty in Faculty Section-@testcaseID=23952
    Given user is on "<groupsummary>" page
    When user clicks on "<Edited_Group>" group on group summary page
    Then user is able to see "<Faculty_Name>" in faculty section
    Examples:
      | groupsummary   | Edited_Group                  | Faculty_Name |
      | #/groupsummary | CreatedGroupForGroupHierarchy | Kevin        |
#
  Scenario Outline: Ensure that user is able to upload student to group-@testcaseID=23953
    Given user is on "<groupsummary>" page
    When user clicks on "<upload students>" button on group summary page
    And user adds "<NumberOfStudents>" student to "<Created_Group>" group via upload
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | groupsummary   | upload students | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | groupName | NumberOfStudents | Created_Group                 |
      | #/groupsummary | student         | 3                 | 0         | 3           | 0          | groupName | 3                | CreatedGroupForGroupHierarchy |

  Scenario Outline: User is able to see Student count increased in group-@testcaseID=23954
    When user clicks on groups link to navigate to group summary page
    Then user is able to see "<NumberOfStudents>" as number of students in "<Created_Group>" group
    Examples:
      | GroupSummaryPage | NumberOfStudents | Created_Group                 |
      | #/groupsummary   | 3                | CreatedGroupForGroupHierarchy |

  Scenario Outline: Ensure that user is able to add Subgroup-@testcaseID=23955
    When user clicks on Add Subgroup button inside the group
    And user fills "<Created_SubGroup_Name>" in GroupName field
    And user fills "<Created_SubGroup_ID>" in GroupId field
    And user clicks on save button on subgroup page
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<Created_SubGroup_Name>" subgroup on group page
    And user is able to see "<Created_SubGroup_ID>" subgroup on group page
    Examples:
      | Created_SubGroup_Name                | Created_SubGroup_ID                | Success_message                   |
      | CreatedSubGroupNameForGroupHierarchy | CreatedSubGroupIDForGroupHierarchy | Successfully created the subgroup |

  Scenario Outline: Ensure that user is able to upload student to Subgroup-@testcaseID=23956
    When user clicks on groups link to navigate to group summary page
    And user clicks on "<upload students>" button on group summary page
    And user adds "<NumberOfStudents>" student to "<Created_Sub_Group>" subgroup via upload
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | upload students | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | groupName | NumberOfStudents | Created_Sub_Group                |
      | student         | 3                 | 0         | 3           | 0          | groupName | 3                | CreatedSubGroupForGroupHierarchy |

  Scenario Outline: User is able to see Student count increased in Subgroup-@testcaseID=23957
    When user clicks on groups link to navigate to group summary page
    And user clicks on expand icon of "<Created_Group>" group
    And user clicks on "<Created_Sub_Group>" subgroup
    Then user is able to see "<NumberOfStudents>" as number of students in "<Created_Sub_Group>" Subgroup
    Examples:
      | GroupSummaryPage | NumberOfStudents | Created_Group                 | Created_Sub_Group                |
      | #/groupsummary   | 3                | CreatedGroupForGroupHierarchy | CreatedSubGroupForGroupHierarchy |

  Scenario Outline: Ensure that user is able to add faculty to subgroup with Permission-@testcaseID=23958
    When user adds "<Faculty_Name>" with permission "<PermissionName>" to group
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | groupsummary   | Overview_page | Success_message                | PermissionName | Faculty_Name | Edited_Group |
      | #/groupsummary | #/overview    | Successfully Updated the Group | AllAccess      | Michael      | Edited_Group |


  Scenario Outline:User is able to see Faculty in Faculty Section-@testcaseID=23959
    Given user is on "<groupsummary>" page
    When user clicks on expand icon of "<Created_Group>" group
    And user clicks on "<Created_Sub_Group>" subgroup
    Then user is able to see "<Faculty_Name>" in faculty section
    Examples:
      | groupsummary   | Created_Group                 | Faculty_Name | Created_Sub_Group                |
      | #/groupsummary | CreatedGroupForGroupHierarchy | Michael      | CreatedSubGroupForGroupHierarchy |

  Scenario Outline: Verify Faculty can access the skyfactor application-@testcaseID=23960
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then  Faculty land on Dashboard page
    Examples:
      | UserName                   | Password |
      | qaFaculty10@mailinator.com | Qait@123 |

  Scenario Outline:Verify Main Group Faculty  Can Access student from Main Group-@testcaseID=23961
    Given user is on "<Overview_Page>" page
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    Then user is able to navigate to the "<Student_name>" profile page
    Examples:
      | Overview_Page | Search_Tab | Student_name |
      | #/overview    | Search     | Adam         |

  Scenario Outline:Verify Main Group Facukty  Can Access student from sub Group-@testcaseID=23962
    Given user is on "<Overview_Page>" page
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    Then user is able to navigate to the "<Student_name>" profile page
    Examples:
      | Overview_Page | Search_Tab | Student_name |
      | #/overview    | Search     | Anderson     |

  Scenario Outline: Verify Faculty can access the skyfactor application-@testcaseID=23963
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then  Faculty land on Dashboard page
    Examples:
      | UserName                   | Password |
      | qaFaculty11@mailinator.com | Qait@123 |

  Scenario Outline:Verify Faculty from subgroup can access student from subgroup-@testcaseID=23964
    Given user is on "<Overview_Page>" page
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    And user is able to navigate to the "<Student_name>" profile page
    Examples:
      | Overview_Page | Search_Tab | Student_name |
      | #/overview    | Search     | Anderson     |

  Scenario Outline:Verify Faculty from subgroup can not  access student from main  group-@testcaseID=23965
    Given user is on "<Overview_Page>" page
    When user clicks on "<Search_Tab>" tab
    And user fills "<Student_name>" in the search field
    And user is not able to navigate to the "<Student_name>" profile page
    Examples:
      | Overview_Page | Search_Tab | Student_name |
      | #/overview    | Search     | Adam         |

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=23966
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Ensure as a Coordinator,user can  delete group-@testcaseID=23967
    Given user is on "<groupsummary>" page
    When user clicks on "<Created_Group>" group on group summary page
    And user clicks on delete group button
    And user click on confirm button on Dialog box
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | groupsummary   | Created_Group                 | Success_message                         |
      | #/groupsummary | CreatedGroupForGroupHierarchy | Successfully Deleted the Group/Subgroup |