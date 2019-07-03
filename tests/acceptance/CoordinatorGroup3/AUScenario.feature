Feature: CoordinatorAcademicUpdate Scenario

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24031
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:Verify user is able to upload course for coordinator role tests-@testcaseID=24032
    When user clicks on "<Manage_Courses>" button
    And user clicks on "<upload_Courses>" button on course page
    And user uploads course with details "<YearId>", "<TermId>", "<UniqueCourseSectionId>", "<SubjectCode>", "<CourseNumber>", "<SectionNumber>", "<CourseName>", "<CreditHours>", "<CollegeCode>", "<DeptCode>", "<Days/Times>", "<Location>"
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | upload_Course | Manage_Courses | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | Manage_Course | upload_Courses | YearId | TermId | UniqueCourseSectionId | SubjectCode | CourseNumber | SectionNumber | CourseName            | CreditHours | CollegeCode | DeptCode | Days/Times | Location |
      | Courses       | Course         | 1                 | 1         | 0           | 0          | Course        | Course         | 201617 | 123    | CSecID                | subCode     | CNumber      | SecNum        | CoordinatorCourseName | 40          | 290         | 10       | 40         | Delhi    |

  Scenario Outline:Verify user is able to upload Coordinator to course for coordinator role tests-@testcaseID=24033
    Given user is on "<courses>" page
    When user clicks on "<upload_Faculty>" button on course page
    And user uploads faculty with details "<FacultyID>", "<PermissionSet>", "<Remove>" to "<created>" course
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | created               | courses   | Faculty Name   | upload_Faculty | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | FacultyID | PermissionSet             | Remove |
      | CreatedForCoordinator | #/courses | qagoel,qanupur | Faculty        | 1                 | 1         | 0           | 0          | 12        | QAIDataPermissionTemplate | 0      |

  Scenario Outline:Verify user is able to upload Student to course for coordinator role tests-@testcaseID=24034
    Given user is on "<courses>" page
    When user clicks on "<upload_Student>" button on course page
    And user uploads "<NumberOfStudent>" student with details "<StudentId>", "<Remove>" to "<created>" course
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | created               | courses   | upload_Student | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | StudentId      | Remove | NumberOfStudent |
      | CreatedForCoordinator | #/courses | Student        | 3                 | 3         | 0           | 0          | 1123-1124-1125 | 0-0-0  | 3               |

  Scenario Outline:Verify coordinator is able to upload a course in the application-@testcaseID=24035
    Given user is on "<courses>" page
    And user clicks on "<upload_Courses>" button on course page
    And user uploads course with details "<YearId>", "<TermId>", "<UniqueCourseSectionId>", "<SubjectCode>", "<CourseNumber>", "<SectionNumber>", "<CourseName>", "<CreditHours>", "<CollegeCode>", "<DeptCode>", "<Days/Times>", "<Location>"
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | courses   | upload_Course | Manage_Courses | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | Manage_Course | upload_Courses | YearId | TermId | UniqueCourseSectionId | SubjectCode | CourseNumber | SectionNumber | CourseName        | CreditHours | CollegeCode | DeptCode | Days/Times | Location |
      | #/courses | Courses       | Course         | 1                 | 1         | 0           | 0          | Course        | Course         | 201617 | 123    | CSecID                | subCode     | CNumber      | SecNum        | FacultyCourseName | 40          | 290         | 10       | 40         | Delhi    |


  Scenario Outline:Verify Coordinator is able to upload Faculty in the course-@testcaseID=24036
    Given user is on "<courses>" page
    When user clicks on "<upload_Faculty>" button on course page
    And user uploads faculty with details "<FacultyID>", "<PermissionSet>", "<Remove>" to "<created>" course
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | created           | courses   | Faculty Name | upload_Faculty | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | FacultyID | PermissionSet             | Remove |
      | CreatedForFaculty | #/courses | S, John      | Faculty        | 1                 | 1         | 0           | 0          | 4552      | QAIDataPermissionTemplate | 0      |

  Scenario Outline:Verify coordinator is able to upload Student to course-@testcaseID=24037
    Given user is on "<courses>" page
    When user clicks on "<upload_Student>" button on course page
    And user uploads "<NumberOfStudent>" student with details "<StudentId>", "<Remove>" to "<created>" course
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | created           | courses   | upload_Student | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | StudentId | Remove | NumberOfStudent |
      | CreatedForFaculty | #/courses | Student        | 2                 | 2         | 0           | 0          | 1126-1127 | 0-0    | 2               |

  Scenario Outline: verify Coordinator is able to On all Academic Update Option-@testcaseID=24038
    Given user is on "<Overview_Page>" page
    When user clicks on "<Settings>" link under additional setup
    And user is able to expand "<Academic_Update>" panel
    And user selects radiobuttons for "<Send_Update_To_Student>" and "<refer_For_Assiatance>" under academic update panel
    And user selects radiobuttons for "<Send_Grade>","<Send_Absence>" and "<Send_Comment>"
    And click on save button on settings page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Overview_Page | Settings | Academic_Update  | Send_Update_To_Student | refer_For_Assiatance | Send_Grade | Send_Comment | Send_Absence | Success_message               |
      | #/overview    | Settings | Academic Updates | on                     | on                   | on         | on           | on           | Successfully set the Settings |

  Scenario Outline: Verify Coordinator is able to create Academic Update for the craeated course-@testcaseID=24039
    Given user is on "<Overview>" page
    When user clicks on "<Academic_Update>" subtab
    And user clicks on add academic button
    And user fills "<name>", "<Description>" for academic request
    And user clicks on continue button
    And user chooses "<Course1>" in Course filter
    And user chooses "<Course2>" in Course filter
    And user clicks on continue button
    And user sends academic update with "<Subject>", "<optional_Message>" for email in academic request
    And user is able to see "<Success_message>" in the alert
    Then user is able to see "<created>" academic update on academic update page
    Examples:
      | created              | Overview   | Academic_Update  | name                        | Description | subject   | optional_Message               | Course1           | Subject   | Success_message                                                                              | Course2              |
      | createdForAUScenario | #/overview | Academic Updates | AcademicUpdateForAUScenario | Description | AURequest | This is an an Academic request | CoursesForFaculty | AUSubject | Your requests have been submitted, we will send you a notification once they have been sent. | CourseForCoordinator |


  Scenario Outline:Verify Faculty can access the skyfactor application-@testcaseID=24040
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then Faculty land on Dashboard page

    Examples:
      | UserName                   | Password |
      | qaFaculty07@mailinator.com | Qait@123 |

  Scenario Outline: Verify Faculty is able to see created academic update on Dashboard-@testcaseID=24041
    When user is on "<Dashboard>" page
    Then user is able to see "<created>" academic request in Acadmic update module
    Examples:
      | Dashboard   | created              |
      | #/dashboard | createdForAUScenario |


  Scenario Outline: Verify Faculty is able to see created academic update on Academic Update Open section-@testcaseID=24042
    When user clicks on "<Course>" tab
    And user clicks on academic update link
    Then user is able to see "<created>" academic request in open academic update list
    Examples:
      | Course  | created              |
      | Courses | createdForAUScenario |

  Scenario Outline: Verify faculty is able to provide grades to student via created Academic Update-@testcaseID=24043
    When user clicks on update these student link for "<created>" Academic Update
    And user provide grade to "<Std_name>" with details "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    And user clicks submit button on Academic update page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Std_name   | Risk | Progress_grade | Absences | Comments | ReferToAssitance | SendToStudent | Success_message                         | created              |
      | E,Anderson | Low  | A              | 44       | Comment  | No               | No            | Your academic update has been submitted | createdForAUScenario |

  Scenario Outline: Verify faculty is able to Verify Status of first student by selecting complete from the dropdown-@testcaseID=24044
    Given user is on "<Academic_Update>" page
    When user clicks on "<created>" academic request in open academic update list
    And user selects "<Option>" from academic update dropdown to see studets status
    Then user is able to see "<Std_name>" with details  "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>"
    Examples:
      | Academic_Update    | Std_name | Risk | Progress_grade | Absences | Comments | Option   | created              |
      | #/academic-updates | Anderson | Low  | A              | 44       | Comment  | Complete | createdForAUScenario |

  Scenario Outline: Verify faculty is able to Verify Status of second student by selecting Incomplete from the dropdown-@testcaseID=24045
    When user selects "<Option>" from academic update dropdown to see studets status
    Then user is able to see "<Std_name>" with Not Submmited Text
    Examples:
      | Std_name | Option     |
      | Ann      | Incomplete |

  Scenario Outline: Verify Faculty is able to see created academic update on Academic Update Open section-@testcaseID=24300
    When user clicks on "<Course>" tab
    And user clicks on academic update link
    Then user is able to see "<created>" academic request in open academic update list
    Examples:
      | Course  | created              |
      | Courses | createdForAUScenario |


  Scenario Outline: Verify Faculty is able to provide grades to second student-@testcaseID=24046
    When user clicks on update these student link for "<created>" Academic Update
    And user provide grade to "<Std_name>" with details "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    And user clicks submit button on Academic update page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Academic_Update    | Std_name | Risk | Progress_grade | Absences | Comments | ReferToAssitance | SendToStudent | Success_message                         | created              |
      | #/academic-updates | F,Ann    | Low  | A              | 44       | Comment  | No               | No            | Your academic update has been submitted | createdForAUScenario |

  Scenario Outline: Verify user is able to see Academic update in Closed Section-@testcaseID=24047
    Given user is on "<Course>" page
    When user clicks on academic update link
    Then user is able to see "<created>" academic request in Closed academic update list
    Examples:
      | Course      | created              |
      | #/my-course | createdForAUScenario |

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24048
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify Coordinator can View Academic Update status as 2/5 in Academic update section-@testcaseID=24049
    When user clicks on "<Academic Update>" subtab
    Then user is able to see status of Academic update with "<Completed>" and "<TotalNumber>" for "<Created>" Academic Update
    Examples:
      | Academic Update  | Completed | TotalNumber | Created              |
      | Academic Updates | 2         | 5           | createdForAUScenario |

  Scenario Outline: Verify user is able to see Academic update in Open Section-@testcaseID=24301
    Given user is on "<Course>" page
    When user clicks on academic update link
    Then user is able to see "<created>" academic request in open academic update list
    Examples:
      | Course      | created              |
      | #/my-course | createdForAUScenario |

  Scenario Outline: Verify user is able to provide grades to student using Adhoc Academic Update-@testcaseID=24051
    When user clicks on update these student link for "<created>" Academic Update
    And user provide grade to "<Student_Name1>" with details "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    And user provide grade to "<Student_Name2>" with details "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    And user provide grade to "<Student_Name3>" with details "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    And user clicks submit button on Academic update page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | created              | Student_Name1 | Risk | Progress_grade | Absences | Comments            | ReferToAssitance | SendToStudent | Course  | Success_message                         | Student_Name2 | Student_Name3 |
      | createdForAUScenario | B,Adam        | High | B              | 44       | Average Performance | No               | No            | Courses | Your academic update has been submitted | C,AFS         | D,AFS         |

  Scenario Outline: Verify user is able to see Academic update in Closed Section-@testcaseID=24050
    Given user is on "<Course>" page
    When user clicks on academic update link
    Then user is able to see "<created>" academic request in Closed academic update list
    Examples:
      | Course      | created              |
      | #/my-course | createdForAUScenario |

  Scenario Outline:Verify user is able to delete Coordinator from course-@testcaseID=24052
    Given user is on "<courses>" page
    When user clicks on "<upload_Faculty>" button on course page
    And user uploads faculty with details "<FacultyID>", "<PermissionSet>", "<Remove>" to "<created>" course
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | created               | courses   | Faculty Name   | upload_Faculty | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | FacultyID | PermissionSet             | Remove |
      | CreatedForCoordinator | #/courses | qagoel,qanupur | Faculty        | 1                 | 0         | 1           | 0          | 12        | QAIDataPermissionTemplate | remove |

  Scenario Outline:Verify user is able to remove  Student from Coordinator's course-@testcaseID=24302
    Given user is on "<courses>" page
    When user clicks on "<upload_Student>" button on course page
    And user uploads "<NumberOfStudent>" student with details "<StudentId>", "<Remove>" to "<created>" course
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | created               | courses   | upload_Student | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | StudentId      | Remove               | NumberOfStudent |
      | CreatedForCoordinator | #/courses | Student        | 3                 | 0         | 3           | 0          | 1123-1124-1125 | remove-remove-remove | 3               |

  Scenario Outline:Verify Coordinator is able to remove Faculty from the course-@testcaseID=24303
    Given user is on "<courses>" page
    When user clicks on "<upload_Faculty>" button on course page
    And user uploads faculty with details "<FacultyID>", "<PermissionSet>", "<Remove>" to "<created>" course
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | created           | courses   | Faculty Name | upload_Faculty | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | FacultyID | PermissionSet             | Remove |
      | CreatedForFaculty | #/courses | S, John      | Faculty        | 1                 | 0         | 1           | 0          | 4552      | QAIDataPermissionTemplate | remove |

  Scenario Outline:Verify coordinator is able to delete Student from Faculty's course-@testcaseID=24304
    Given user is on "<courses>" page
    When user clicks on "<upload_Student>" button on course page
    And user uploads "<NumberOfStudent>" student with details "<StudentId>", "<Remove>" to "<created>" course
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | created           | courses   | upload_Student | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | StudentId | Remove        | NumberOfStudent |
      | CreatedForFaculty | #/courses | Student        | 2                 | 0         | 2           | 0          | 1126-1127 | remove-remove | 2               |
