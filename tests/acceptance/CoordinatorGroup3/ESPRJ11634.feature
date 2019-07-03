Feature: CoordinatorESPRJ11634

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=23968
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to add faculty through UI-@testcaseID=23969
    Given user is on "<Page>" page
    When user clicks on "<ManageFaculty_Button>" button
    And user grabs Faculty count
    And user clicks on AddFaculty button
    And user fills "<FirstName>" as first Name
    And user fills "<LastName>" as last Name
    And user fills "<Title>" as title
    And user fills "<ContactInfo>" as contactInfo
    And user fills "<Phone>" as phoneNumber
    And user fills "<ID>" as ID
    And user fills "<LDAP>" as LDAPUserName
    And user selects "<Mobile_checked>" option
    And user selects "<Inactive>" as inactive option
    And user clicks on save button to save user
    Then user is able to see "<Success_message>" in the alert
    And count of the faculty increases by one

    Examples:
      | Page       | ManageFaculty_Button | FirstName         | LastName         | Title | ContactInfo  | Phone | ID | LDAP         | Mobile_checked | Inactive | NotParticipating | Success_message                     |
      | #/overview | Faculty              | Faculty_FirstName | Faculty_LastName | Miss  | Faculty_Test | 67    | 09 | Faculty_Test | yes            | no       | no               | Faculty/Staff created successfully. |

  Scenario Outline:Ensure User is able to Create Campus Resource-@testcaseID=23970
    Given user is on "<Overview_Page>" page
    When user clicks on "<Campus Resource>" link under additional setup
    And user clicks on add campus resource button
    And fill details for campus resource with "<CampusResourceName>" , "<StaffName>", "<Phone>", "<Email>", "<Location>", "<URL>", "<Description>", "<Can Viewed by student>", "<can Recieve Refferal>"
    And click on save button to save Campus Resource
    Then user is able to see "<Success_message>" in the alert
    And user is able to view Campus Resource in campus Resources list
    Examples:
      | Overview_Page | Campus Resource | CampusResourceName | StaffName                         | Phone   | Email      | Location | URL                     | Description       | Can Viewed by student | can Recieve Refferal | Success_message                    |
      | #/overview    | Campus Resource | Staff              | StaffForReferralAssigneeBehaviors | 9876565 | Staffemail | Delhi    | https://www.synapse.com | health DepartMent | yes                   | yes                  | Campus Resource saved successfully |

  Scenario Outline: Ensure that user is able to create a new group-@testcaseID=23971
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
      | groupsummary   | Manage_Groups | Created_Group_Name | Created_Group_ID | Success_message                  |
      | #/groupsummary | Groups        | CreatedGroupName   | CreatedGroupID   | The Group is added Successfully. |

  Scenario Outline: Ensure that user is able to upload student to group-@testcaseID=23972
    Given user is on "<groupsummary>" page
    When user clicks on "<upload students>" button on group summary page
    And user adds "<NumberOfStudents>" student to "<Created_Group>" group via upload
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | groupsummary   | upload students | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | groupName | NumberOfStudents | Created_Group |
      | #/groupsummary | student         | 1                 | 0         | 1           | 0          | groupName | 1                | Created       |

  Scenario Outline: User is able to see Student count increased in group-@testcaseID=23973
    When user is on "<GroupSummaryPage>" page
    And user is able to see "<NumberOfStudents>" as number of students in "<Created_Group>" group
    Examples:
      | GroupSummaryPage | NumberOfStudents | Created_Group |
      | #/groupsummary   | 1                | Created       |

  Scenario Outline: Ensure that user is able to add faculty to group with Permission-@testcaseID=23974
    When user adds "<Faculty_Name>" with permission "<PermissionName>" to group
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert

    Examples:
      | Overview_page | Success_message                | PermissionName | Faculty_Name                      |
      | #/overview    | Successfully Updated the Group | AllAccess      | StaffForReferralAssigneeBehaviors |

  Scenario Outline:Ensure user is able to see faculty in faculty section-@testcaseID=23975
    When user clicks on "<Created_Group>" group on group summary page
    And user is able to see "<Faculty_Name>" in faculty section
    Examples:
      | Created_Group | Faculty_Name                      |
      | Created       | StaffForReferralAssigneeBehaviors |


  Scenario Outline: Verify coordinator is able to ON referral options from Setting page-@testcaseID=23976
    Given user is on "<Overview_Page>" page
    When user clicks on "<Settings>" link under additional setup
    And user is able to expand "<Feature_Panel>" panel
    And user selects radiobuttons for "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" under Features Panel
    And user selects "<Student_ReferralNotification>","<PrimaryCampus>" and "<Reason_Routing>" under Referrals
    And click on save button on settings page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Overview_Page | Settings | Feature_Panel | Referral_Option | Note_Option | LogContact_Option | Appointments_Option | Email_Option | Success_message               | Student_ReferralNotification | PrimaryCampus         | Reason_Routing |
      | #/overview    | Settings | Features      | referral-on     | notes-on    | logContact-on     | booking-on          | sendEmail-on | Successfully set the Settings | notification-on              | primary-connection-on | reason-on      |

  Scenario Outline: Verify user is able to view widgets on the student profile page-@testcaseID=23977
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    Then user is able to navigate to the "<Student_name>" profile page
    Examples:
      | Search_Tab | Student_name | ID   | Email                   | Phone | MobilePhone | Referral | Appointment | Contacts |
      | Search     | Adam         | 1123 | autoqa01@mailinator.com |       |             | Referral | Appointment | Contact  |

  Scenario Outline: Verify user is able to create Referral with Campus Resource as assingnee-@testcaseID=23978
    When user clicks on "<Activity_Stream>" tab on student page
    And user clicks on Add New Activity link
    And user clicks on "<Referral_tab>" tab on window
    And user select and fills following fields "<Reason_type>","<Assign_To>","<Interested_Party>" and "<Description>" in field
    And user select "<Detail_option>" details checkbox
    And user select "<Sharing_option>" sharing option for "<Team_Name>"
    And clicks on Create a Referral button
    Then user is able to see "<Success_message>" in the alert
    And user should be able to see the created "<Referral>" with "<Description>" in the list
    Examples:
      | Activity_Stream | Referral_tab | Reason_type               | Assign_To             | Interested_Party | Description       | Sharing_option | Team_Name  | Success_message             | Referral | Detail_option              |
      | Activity Stream | Referral     | Class attendance positive | CampusResourceFaculty | None             | Added referral on | Team           | SELECT ALL | Referral saved successfully | Referral | Notify Student of Referral |

  Scenario Outline: Verify user is able to create Referral with Campus Connection as assignee-@testcaseID=23979
    When user clicks on Add New Activity link
    And user clicks on "<Referral_tab>" tab on window
    And user select and fills following fields "<Reason_type>","<Assign_To>","<Interested_Party>" and "<Description>" in field
    And user select "<Detail_option>" details checkbox
    And user select "<Sharing_option>" sharing option for "<Team_Name>"
    And clicks on Create a Referral button
    Then user is able to see "<Success_message>" in the alert
    And user should be able to see the created "<Referral>" with "<Description>" in the list
    Examples:
      | Activity_Stream | Referral_tab | Reason_type               | Assign_To                         | Interested_Party | Description       | Sharing_option | Team_Name  | Success_message             | Referral | Detail_option              |
      | Activity Stream | Referral     | Class attendance positive | StaffForReferralAssigneeBehaviors | qavishvajeet     | Added referral on | Team           | SELECT ALL | Referral saved successfully | Referral | Notify Student of Referral |


  Scenario Outline: Ensure user is  able to mark  Faculty invisible Group-@testcaseID=23980
    Given user is on "<groupsummary>" page
    When user clicks on "<Created>" group on group summary page
    And user sets faculty "<FacultyName>" as invisble
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert

    Examples:
      | groupsummary   | FacultyName                       | created | Success_message                |
      | #/groupsummary | StaffForReferralAssigneeBehaviors | Created | Successfully Updated the Group |

  Scenario Outline: Verify user is able to view widgets on the student profile page-@testcaseID=23981
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    Then user is able to navigate to the "<Student_name>" profile page
    Examples:
      | Search_Tab | Student_name | ID   | Email                   | Phone | MobilePhone | Referral | Appointment | Contacts |
      | Search     | Adam         | 1123 | autoqa01@mailinator.com |       |             | Referral | Appointment | Contact  |

  Scenario Outline: Verify user is able to create Referral with Campus Resource as assignee-@testcaseID=23982
    When user clicks on "<Activity_Stream>" tab on student page
    And user clicks on Add New Activity link
    And user clicks on "<Referral_tab>" tab on window
    And user select and fills following fields "<Reason_type>","<Assign_To>","<Interested_Party>" and "<Description>" in field
    And user select "<Detail_option>" details checkbox
    And user select "<Sharing_option>" sharing option for "<Team_Name>"
    And clicks on Create a Referral button
    Then user is able to see "<Success_message>" in the alert
    And user should be able to see the created "<Referral>" with "<Description>" in the list
    Examples:
      | Activity_Stream | Referral_tab | Reason_type               | Assign_To      | Interested_Party | Description       | Sharing_option | Team_Name  | Success_message             | Referral | Detail_option              |
      | Activity Stream | Referral     | Class attendance positive | CampusResource | None             | Added referral on | Team           | SELECT ALL | Referral saved successfully | Referral | Notify Student of Referral |
