Feature: CoordinatorGroups

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24146
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Ensure  that the Nevermind button on Add Groups workflow works fine-@testcaseID=22730
    Given user is on "<Overview_page>" page
    When user clicks on "<Manage_Groups>" button
    And user clicks on Add Another Group button
    And user fills "<Group_Name>" in GroupName field
    And user fills "<Group_ID>" in GroupId field
    And user clicks on cancel button on group page
    Then user is not able to see "<Created_Group_ID>" on group summary page
    And user is not able to see "<Created_Group_Name>" on group summary page
    Examples:
      | Overview_page | Manage_Groups | Group_Name       | Group_ID       | created | Created_Group_ID | Created_Group_Name |
      | #/overview    | Groups        | CreatedGroupName | CreatedGroupID | created | CreatedGroupID   | CreatedGroupName   |

  Scenario Outline:Ensure that user is not allowed to enter a group ID of more than 100 character length-@testcaseID=22731
    When user clicks on Add Another Group button
    And user fills "<Group_ID_WithExceedChar>" in GroupId field
    Then user is able to see "<Error_Message>"

    Examples:
      | Group_ID_WithExceedChar                                                                                          | Error_Message                              |
      | GorupIDWithExceddingLengthKEbnckBQcJuTJELCBvHpTwLYSwxuTDqvqiXENJHwhVjxJvRnfJonavLfcYGmpTQXAJsHxdNQAIlRJjPVtBRnkQ | Group ID should not exceed 100 characters. |

  Scenario Outline: Ensure as a Coordinator,user is not able to enter Space in Group ID and GroupName-@testcaseID=22732
    When user fills "<invalid_Group_Name>" in GroupName field
    And user fills "<invalid_Group_ID>" in GroupId field
    And user clicks on save button on group page
    Then user is able to see "<invalid_groupName_error>"
    And user is able to see "<invalid_groupID_error>"
    Examples:
      | invalid_Group_Name | invalid_Group_ID | invalid_groupName_error                        | invalid_groupID_error                        |
      | Group Name         | group ID         | Please enter a unique Group Name without space | Please enter a unique Group ID without space |

  Scenario Outline: Ensure as a Coordinator,user can see All Student group-@testcaseID=22733
    Given user is on "<Group_Summary_Page>" page
    Then user is able to see "<AllStudents>" group
    Examples:
      | Group_Summary_Page | AllStudents  |
      | #/groupsummary     | All Students |

  Scenario Outline: Ensure that user is able to create a new group-@testcaseID=22734
    When user clicks on Add Another Group button
    And user fills "<Created_Group_Name>" in GroupName field
    And user fills "<Created_Group_ID>" in GroupId field
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<Created_Group_Name>" on group summary page
    And user is able to see "<Created_Group_ID>" on group summary page
    And user is able to see "<Created_Group_ID>" against "<Created_Group_Name>" in Group list

    Examples:
      | Overview_page | Manage_Groups | Created_Group_Name | Created_Group_ID | Success_message                  |
      | #/overview    | Groups        | CreatedGroupName   | CreatedGroupID   | The Group is added Successfully. |

  Scenario Outline: Verify user is not able to edit the group if s/he click Nevermind when attempting to edit a group-@testcaseID=22740
    Given user is on "<Group_Summary_Page>" page
    When user clicks on "<Created_Group>" group on group summary page
    And user fills "<Edited_Group_ID>" in GroupId field
    And user fills "<Edited_Group_Name>" in GroupName field
    And user clicks on cancel button on group page
    Then user is not able to see "<Edited_Group_ID>" on group summary page
    And user is not able to see "<Edited_Group_Name>" on group summary page
    And user is able to see "<Created_Group_ID>" on group summary page
    And user is able to see "<Created_Group_Name>" on group summary page
    Examples:
      | Group_Summary_Page | Edited_Group_Name | Edited_Group_ID | Created_Group_Name | Created_Group_ID | Created_Group |
      | #/groupsummary     | EditedGroupName   | EditedGroupID   | CreatedGroupName   | CreatedGroupID   | Created_Group |

  Scenario Outline: Verify user is able to edit the group-@testcaseID=24284
    Given user is on "<Group_Summary_Page>" page
    When user clicks on "<Created_Group>" group on group summary page
    And user fills "<Edited_Group_ID>" in GroupId field
    And user fills "<Edited_Group_Name>" in GroupName field
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert
    Then user is able to see "<Edited_Group_ID>" on group summary page
    And user is able to see "<Edited_Group_Name>" on group summary page
    Examples:
      | Group_Summary_Page | Edited_Group_Name | Edited_Group_ID | Created_Group | Success_message                |
      | #/groupsummary     | EditedGroupName   | EditedGroupID   | Created_Group | Successfully Updated the Group |

  Scenario Outline: Verify user is able to add  student to group through Upload from inside the group-@testcaseID=24285
    Given user is on "<GroupSummaryPage>" page
    When user clicks on "<Edited_Group>" group on group summary page
    And  user clicks on upload student button on group edit page
    And user add "<NumOfStd>" student with "<ExternalID>" to "<Edited_Group>" group via upload from inside the group
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    And user is able to see "<NumOfStd>" as number of students in "<Edited_Group>" group
    Examples:
      | ExternalID | Edited_Group | GroupSummaryPage | upload students | NumOfStd | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | created |
      | 1135       | EditedGroup  | #/groupsummary   | student         | 1        | 1                 | 1         | 0           | 0          | Created |


  Scenario Outline:Verify Coordinator can see group in which student is added from student profile page-@testcaseID=24286
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    And user is able to navigate to the "<Student_name>" profile page
    And user clicks on "<Details>" tab on student page
    And user clicks on "<Groups>" link under student details tab
    Then user is able to see "<Edited_Group>" on student profile page
    Examples:
      | Search_Tab | Student_name | Details | Groups | Edited_Group |
      | Search     | Deborah      | Details | Groups | Edited_Group |


  Scenario Outline: Verify user is able to add faculty to group with Permission-@testcaseID=22736
    Given user is on "<groupsummary>" page
    When user clicks on "<Edited_Group>" group on group summary page
    And user adds "<Faculty_Name>" with permission "<PermissionName>" to group
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | groupsummary   | Overview_page | Success_message                | PermissionName | Faculty_Name | Edited_Group |
      | #/groupsummary | #/overview    | Successfully Updated the Group | AllAccess      | qanupur      | Edited_Group |

  Scenario Outline:Ensure that user is able to see Faculty in Faculty in Faculty Section-@testcaseID=24287
    Given user is on "<groupsummary>" page
    When user clicks on "<Edited_Group>" group on group summary page
    Then user is able to see "<Faculty_Name>" in faculty section
    Examples:
      | groupsummary   | Edited_Group | Faculty_Name |
      | #/groupsummary | Edited       | qanupur      |


  Scenario Outline: Ensure that user is able to remove faculty from group-@testcaseID=22737
    Given user is on "<groupsummary>" page
    When user clicks on "<Edited_Group>" group on group summary page
    And user deletes "<Faculty_Name>" from the group
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | groupsummary   | Edited_Group | Faculty_Name | Success_message                |
      | #/groupsummary | Edited_Group | qanupur      | Successfully Updated the Group |

  Scenario Outline: Ensure that user is not able to see faculty in Group-@testcaseID=24290
    Given user is on "<groupsummary>" page
    When user clicks on "<Edited_Group>" group on group summary page
    Then user is not able to see "<Faculty_Name>" in faculty section
    Examples:
      | groupsummary   | Edited_Group | Faculty_Name |
      | #/groupsummary | Edited_Group | qanupur      |

  Scenario Outline: Ensure that user is able to add Subgroup-@testcaseID=22738
    When user clicks on Add Subgroup button inside the group
    And user fills "<Created_SubGroup_Name>" in GroupName field
    And user fills "<Created_SubGroup_ID>" in GroupId field
    And user clicks on save button on subgroup page
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<Created_SubGroup_Name>" subgroup on group page
    And user is able to see "<Created_SubGroup_ID>" subgroup on group page

    Examples:
      | Created_SubGroup_Name | Created_SubGroup_ID | Success_message                   |
      | CreatedSubGroupName   | CreatedSubGroupID   | Successfully created the subgroup |

  Scenario Outline: Ensure that user is able to see subgroup on group summary page-@testcaseID=24288
    Given user is on "<groupsummary>" page
    When user clicks on expand icon of "<Edited_Group>" group
    Then user is able to see "<CreatedName>" of subgroup on group summary page
    And user is able to see "<CreatedID>" of subgroup on group summary page
    Examples:
      | groupsummary   | Edited_Group | CreatedName         | CreatedID         |
      | #/groupsummary | Edited_Group | CreatedSubGroupName | CreatedSubGroupID |

  Scenario Outline: Ensure as a Coordinator,user can edit groupID and groupName of subgroup-@testcaseID=22739
    When user clicks on "<Created>" subgroup
    And user fills "<Edited_SubGroup_Name>" in GroupName field
    And user fills "<Edited_SubGroup_ID>" in GroupId field
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Created | Success_message                | Edited_SubGroup_Name | Edited_SubGroup_ID |
      | Created | Successfully Updated the Group | Edited_SubGroup_Name | Edited_SubGroup_ID |

  Scenario Outline: Ensure as a Coordinator,user can see edited subgroup-@testcaseID=24289
    Given user is on "<groupsummary>" page
    When user clicks on expand icon of "<Edited_Group>" group
    Then user is able to see "<EditedSubGroupID>" of subgroup on group summary page
    And user is able to see "<EditedSubGroupName>" of subgroup on group summary page
    Examples:
      | EditedSubGroupID | Edited_Group | groupsummary   | EditedSubGroupName |
      | EditedSubGroupID | Edited_Group | #/groupsummary | EditedSubGroupName |

  Scenario Outline: Ensure as a Coordinator,user can  delete group-@testcaseID=22743
    When user clicks on "<Edited_Group>" group on group summary page
    And  user clicks on delete group button
    And user click on confirm button on Dialog box
    Then user is able to see "<Success_message>" in the alert
    And user is not able to see "<Edited>" group with groupid on group summary page
    Examples:
      | Edited | Success_message                         | Edited_Group |
      | Edited | Successfully Deleted the Group/Subgroup | EditedGroup  |


 


 


    














