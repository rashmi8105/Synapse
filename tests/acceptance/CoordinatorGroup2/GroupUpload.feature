Feature: CoordinatorGroups

  Background:

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24102
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Ensure that user is able to create a new group-@testcaseID=24103
    When user clicks on "<Manage_Groups>" button
    And user clicks on Add Another Group button
    And user fills "<Created_Group_Name>" in GroupName field
    And user fills "<Created_Group_ID>" in GroupId field
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Manage_Groups | Created_Group_Name | Created_Group_ID | Success_message                  |
      | Groups        | CreatedGroupName   | CreatedGroupID   | The Group is added Successfully. |

  Scenario Outline: Ensure that user is able to upload student to group-@testcaseID=24104
    When user clicks on "<upload students>" button on group summary page
    And user adds "<NumberOfStudents>" student to "<Created_Group>" group via upload
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | upload students | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | groupName | NumberOfStudents | Created_Group |
      | student         | 1                 | 0         | 1           | 0          | groupName | 1                | Created       |

  Scenario Outline: User is able to see Student count increased in group-@testcaseID=24105
    When user clicks on groups link to navigate to group summary page
    And user is able to see "<NumberOfStudents>" as number of students in "<Created_Group>" group
    Examples:
      | GroupSummaryPage | NumberOfStudents | Created_Group |
      | #/groupsummary   | 1                | Created       |


  Scenario Outline: Ensure that user is able to remove student from group-@testcaseID=24106
    Given user is on "<GroupSummaryPage>" page
    And user clicks on "<upload students>" button on group summary page
    And user removes "<NumberOfStudents>" student from "<Created_Group>" group via upload
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table

    Examples:
      | GroupSummaryPage | upload students | NumberOfStudents | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | Created_Group |
      | #/groupsummary   | student         | 1                | 1                 | 0         | 1           | 0          | Created       |

  Scenario Outline: User is able to see Student count decreased in group-@testcaseID=24107
    When user clicks on groups link to navigate to group summary page
    And user is able to see "<NumberOfStudents>" as number of students in "<Created_Group>" group
    Examples:
      | GroupSummaryPage | NumberOfStudents | Created_Group |
      | #/groupsummary   | 0                | Created       |


  Scenario Outline: Ensure that user is not able to upload a subgroup file with invalid format-@testcaseID=24108
    Given user is on "<groupsummary>" page
    And user clicks on "<upload subgroup>" button on group summary page
    And user uploads an invalid format file
    Then user is able to see an "<error Message>"
    Examples:
      | groupsummary   | upload subgroup | error Message                   |
      | #/groupsummary | subgroup        | Please enter a valid file type. |

  Scenario Outline: Ensure user is able to add subgroup to group via upload-@testcaseID=24109
    Given user is on "<groupsummary>" page
    And user clicks on "<upload subgroup>" button on group summary page
    And user uploads a subgroup with "<subgroup name>" and "<subgroup ID>" to "<Created_Group>" group
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | groupsummary   | upload subgroup | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | subgroup name       | subgroup ID       | Created_Group |
      | #/groupsummary | subgroup        | 1                 | 1         | 0           | 0          | CreatedSubGroupName | CreatedSubGroupID | Created       |

  Scenario Outline: Ensure that user is able to see subgroup on group summary page-@testcaseID=24110
    When user clicks on groups link to navigate to group summary page
    And user clicks on expand icon of "<Created_Group>" group
    Then user is able to see "<CreatedName>" of subgroup on group summary page
    And user is able to see "<CreatedID>" of subgroup on group summary page
    Examples:
      | groupsummary   | Created_Group | CreatedName         | CreatedID         |
      | #/groupsummary | Created       | CreatedSubGroupName | CreatedSubGroupID |

  Scenario Outline: Ensure that user is able to upload student to Subgroup-@testcaseID=24111
    When user clicks on "<upload students>" button on group summary page
    And user adds "<NumberOfStudents>" student to "<Created>" subgroup via upload
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | upload students | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | Created | NumberOfStudents |
      | student         | 1                 | 0         | 1           | 0          | Created | 1                |

  Scenario Outline: User is able to see Student count increased in Subgroup-@testcaseID=24112
    When user clicks on groups link to navigate to group summary page
    And user clicks on expand icon of "<Created>" group
    And user clicks on "<Created>" subgroup
    Then user is able to see "<NumberOfStudents>" as number of students in "<Created>" Subgroup
    Examples:
      | GroupSummaryPage | NumberOfStudents | Created |
      | #/groupsummary   | 1                | Created |

  Scenario Outline: Ensure that user is able to removes student from Subgroup via upload-@testcaseID=24113
    Given user is on "<GroupSummaryPage>" page
    When user clicks on "<upload students>" button on group summary page
    And user removes "<NumberOfStudents>" student from "<Created>" subgroup via upload
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | GroupSummaryPage | upload students | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | NumberOfStudents | Created |
      | #/groupsummary   | student         | 1                 | 0         | 1           | 0          | 1                | Created |

  Scenario Outline: User is able to see Student count decreased in Subgroup-@testcaseID=24114
    When user clicks on groups link to navigate to group summary page
    And user clicks on expand icon of "<Created>" group
    And user clicks on "<Created>" subgroup
    Then user is able to see "<NumberOfStudents>" as number of students in "<Created>" Subgroup
    Examples:
      | GroupSummaryPage | NumberOfStudents | Created |
      | #/groupsummary   | 0                | Created |

  Scenario Outline: Ensure that user is not  able to upload a Student without externalID-@testcaseID=24115
    Given user is on "<groupsummary>" page
    When user clicks on "<upload students>" button on group summary page
    And user uploads a file without externalID
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table

    Examples:
      | groupsummary   | upload students | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError |
      | #/groupsummary | student         | 1                 | 0         | 0           | 1          |

  Scenario Outline: Ensure user is  able to upload  Faculty to  Group-@testcaseID=24116
    Given user is on "<groupsummary>" page
    When user clicks on "<upload Faculty>" button on group summary page
    And user adds Faculty with details "<ExternalID>", "<FirstName>" , "<LastName>", "<Email>" , "<Permission>" , "<invisible>" , "<Remove>" to "<Created>" group via upload
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | groupsummary   | upload Faculty | 1 | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | Created | ExternalID | FirstName | LastName | Email                  | Permission | invisible | Remove |
      | #/groupsummary | faculty        | 1 | 1                 | 1         | 0           | 0          | Created | 12         | qanupur   | qagoel   | qanupur@mailinator.com | AllAccess  | 0         | 0      |

  Scenario Outline: Ensure user is able to see Faculty in Faculty Section-@testcaseID=24117
    When user clicks on groups link to navigate to group summary page
    And user clicks on "<Created>" group on group summary page
    Then user is able to see "<Faculty_Name>" in faculty section
    Examples:
      | groupsummary   | Created | Faculty_Name |
      | #/groupsummary | Created | qanupur      |

  Scenario Outline: Ensure user is  able to upload invisible Faculty to Group-@testcaseID=24118
    Given user is on "<groupsummary>" page
    When user clicks on "<upload Faculty>" button on group summary page
    And user adds Faculty with details "<ExternalID>", "<FirstName>" , "<LastName>", "<Email>" , "<Permission>" , "<invisible>" , "<Remove>" to "<Created>" group via upload
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | groupsummary   | Faculty_Name | upload Faculty | 1 | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | groupName | groupHeader | created | ExternalID | FirstName | LastName | Email                  | GroupID | Permission | invisible | Remove | Faculty_visible |
      | #/groupsummary | qanupur      | faculty        | 1 | 1                 | 0         | 1           | 0          | groupName | GroupHeader | created | 12         | qanupur   | qagoel   | qanupur@mailinator.com |         | AllAccess  | 1         | 0      | True            |

  Scenario Outline:User is able to see invisible checkbox checked for faculty in group-@testcaseID=24119
    When user clicks on groups link to navigate to group summary page
    And user clicks on "<Created>" group on group summary page
    Then user is able to see Faculty as invisible
    Examples:
      | groupsummary   | Created |
      | #/groupsummary | Created |


  Scenario Outline: Ensure user is  able to remove  Faculty from  Group via upload-@testcaseID=24120
    Given user is on "<groupsummary>" page
    And user clicks on "<upload Faculty>" button on group summary page
    And user removes Faculty with details "<ExternalID>", "<FirstName>" , "<LastName>", "<Email>" , "<Permission>" , "<invisible>" , "<Remove>" from "<Created>" group via upload
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | groupsummary   | upload Faculty | 1 | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | Created | ExternalID | FirstName | LastName | Email                  | Permission | invisible | Remove |
      | #/groupsummary | faculty        | 1 | 1                 | 0         | 1           | 0          | Created | 12         | qanupur   | qagoel   | qanupur@mailinator.com | AllAccess  | 0         | 1      |

  Scenario Outline: Ensure user is not able to see Faculty in Faculty Section-@testcaseID=24121
    When user clicks on groups link to navigate to group summary page
    And user clicks on "<Created>" group on group summary page
    Then user is not able to see "<Faculty_Name>" in faculty section
    Examples:
      | groupsummary   | Created | Faculty_Name |
      | #/groupsummary | Created | qanupur      |

  Scenario Outline: Ensure user is not able to add subgroup to group via upload with invalid Parent_Group_ID-@testcaseID=24122
    Given user is on "<groupsummary>" page
    When user clicks on "<upload subgroup>" button on group summary page
    And user uploads a subgroup with "<subgroup name>" and "<subgroup ID>" to group with invalid Parent group ID
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | groupsummary   | upload subgroup | created | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | subgroup name        | subgroup ID          |
      | #/groupsummary | subgroup        | created | 1                 | 0         | 0           | 1          | QaAutomationSubGroup | QaAutomationSubGroup |

  Scenario Outline: Ensure user is not able to add duplicate subgroup to group-@testcaseID=24123
    Given user is on "<groupsummary>" page
    When user clicks on "<upload subgroup>" button on group summary page
    And user uploads a duplicate subgroup
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | groupsummary   | upload subgroup | created | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | subgroup name              | subgroup ID          |
      | #/groupsummary | subgroup        | created | 1                 | 0         | 1           | 0          | QaAutomationSubGroupUpload | QaAutomationSubGroup |


  Scenario Outline: Ensure user is not able to Update subgroupID of subgroup via upload-@testcaseID=24124
    Given user is on "<groupsummary>" page
    When user clicks on "<upload subgroup>" button on group summary page
    And user uploads a subgroup with "<subgroup name>" and "<subgroup ID>" to "<Created>" group
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | groupsummary   | upload subgroup | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | subgroup name | subgroup ID | Created |
      | #/groupsummary | subgroup        | 1                 | 1         | 0           | 0          | Created       | Edited      | Created |


  Scenario Outline: Ensure that user is able to see created subgroupID on group summary page-@testcaseID=24125
    When user clicks on groups link to navigate to group summary page
    And user clicks on expand icon of "<Created_Group>" group
    Then user is able to see "<CreatedName>" of subgroup on group summary page
    And user is able to see "<CreatedID>" of subgroup on group summary page
    Examples:
      | groupsummary   | Created_Group | CreatedName         | CreatedID         |
      | #/groupsummary | Created       | CreatedSubGroupName | CreatedSubGroupID |

  Scenario Outline: Ensure user is  able to Update subgroupName of Subgroup via upload-@testcaseID=24126
    Given user is on "<groupsummary>" page
    When user clicks on "<upload subgroup>" button on group summary page
    And user uploads a subgroup with "<subgroup_name>" and "<subgroup_ID>" to "<Created>" group
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | groupsummary   | upload subgroup | created | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | subgroup_name | subgroup_ID |
      | #/groupsummary | subgroup        | created | 1                 | 0         | 1           | 0          | Edited        | Created     |

  Scenario Outline: Ensure user is able to see updated name of subgroup name-@testcaseID=24127
    When user clicks on groups link to navigate to group summary page
    And user clicks on expand icon of "<Created_Group>" group
    Then user is able to see "<EditedName>" of subgroup on group summary page
    And user is able to see "<CreatedID>" of subgroup on group summary page
    Examples:
      | groupsummary   | CreatedID | EditedName | Created_Group |
      | #/groupsummary | CreatedID | EditedName | Created       |

