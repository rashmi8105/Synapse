Feature: FacultyCourseAndAcademicUpdate

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24223
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:Verify coordinator is able to upload a course in the application-@testcaseID=22911
    When user clicks on "<Manage_Courses>" button
    And user clicks on "<upload_Courses>" button on course page
    And user uploads course with details "<YearId>", "<TermId>", "<UniqueCourseSectionId>", "<SubjectCode>", "<CourseNumber>", "<SectionNumber>", "<CourseName>", "<CreditHours>", "<CollegeCode>", "<DeptCode>", "<Days/Times>", "<Location>"
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | upload_Course | Manage_Courses | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | Manage_Course | upload_Courses | YearId | TermId | UniqueCourseSectionId | SubjectCode | CourseNumber | SectionNumber | CourseName        | CreditHours | CollegeCode | DeptCode | Days/Times | Location |
      | Courses       | Course         | 1                 | 1         | 0           | 0          | Course        | Course         | 201617 | 123    | CSecID                | subCode     | CNumber      | SecNum        | FacultyCourseName | 40          | 290         | 10       | 40         | Delhi    |

  Scenario Outline: Verify user is able to see course on roster view-@testcaseID=22912
    When user clicks on course link on course page
    And user clicks on roster view of "<created>" course
    Then user is able to see "<Section_Number>" and "<Course_Name_ID>" of "<created>" course in roster view of course
    Examples:
      | Section_Number | Course_Name_ID | created           |
      | Section Number | Course Name ID | CreatedForFaculty |

  Scenario Outline:Verify Coordinator is able to upload Faculty in the course-@testcaseID=22913
    Given user is on "<courses>" page
    When user clicks on "<upload_Faculty>" button on course page
    And user uploads faculty with details "<FacultyID>", "<PermissionSet>", "<Remove>" to "<created>" course
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | created           | courses   | Faculty Name | upload_Faculty | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | FacultyID | PermissionSet             | Remove |
      | CreatedForFaculty | #/courses | E,Johnson    | Faculty        | 1                 | 1         | 0           | 0          | 4553      | QAIDataPermissionTemplate | 0      |

  Scenario Outline: Verify Coordinator is able to see faculty in faculty section in roster view of course-@testcaseID=22914
    When user clicks on course link on course page
    And user clicks on roster view of "<created>" course
    Then user is able to see "<Faculty Name>" in faculty section of course
    Examples:
      | Faculty Name | created           |
      | E,Johnson    | CreatedForFaculty |

  Scenario Outline:Verify coordinator is able to upload Student to course-@testcaseID=22915
    Given user is on "<courses>" page
    When user clicks on "<upload_Student>" button on course page
    And user uploads student with details "<StudentId>", "<Remove>" to "<created>" course
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | created           | courses   | Student Name | upload_Student | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | StudentId | Remove |
      | CreatedForFaculty | #/courses | N,Deborah    | Student        | 1                 | 1         | 0           | 0          | 1135      | 0      |

  Scenario Outline:verify user is able to see uploaded student in roaster view of course-@testcaseID=22916
    When user clicks on course link on course page
    And user clicks on roster view of "<created>" course
    Then user is able to see "<Student_Name>" in student section of course
    Examples:
      | Student_Name | created           |
      | N,Deborah    | CreatedForFaculty |

  Scenario Outline: Verify user is able to ON all the features under Feature panel-@testcaseID=22918
    Given user is on "<Overview_Page>" page
    When user clicks on "<Settings>" link under additional setup
    And user is able to expand "<Feature_Panel>" panel
    And user selects radiobuttons for "<Referral_Option>","<Note_Option>","<LogContact_Option>","<Appointments_Option>" and "<Email_Option>" under Features Panel
    And user selects "<Student_ReferralNotification>","<PrimaryCampus>" and "<Reason_Routing>" under Referrals
    And click on save button on settings page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Feature_Panel | Settings | Overview_Page | Referral_Option | Note_Option | LogContact_Option | Appointments_Option | Email_Option | Success_message               | Student_ReferralNotification | PrimaryCampus         | Reason_Routing |
      | Features      | Settings | #/overview    | referral-on     | notes-on    | logContact-on     | booking-on          | sendEmail-on | Successfully set the Settings | notification-on              | primary-connection-on | reason-on      |

  Scenario Outline:Verify Coordinator is able to ON all Option under Academic update panel-@testcaseID=24268
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

  Scenario Outline: Verify Coordinator is able to create Academic Update for the created course-@testcaseID=22917
    Given user is on "<Overview>" page
    When user clicks on "<Academic_Update>" subtab
    And user clicks on add academic button
    And user fills "<name>", "<Description>" for academic request
    And user clicks on continue button
    And user chooses "<Course>" in Course filter
    And user clicks on continue button
    And user sends academic update with "<Subject>", "<optional_Message>" for email in academic request
    And user is able to see "<Success_message>" in the alert
    Then user is able to see "<created>" academic update on academic update page
    Examples:
      | created           | Overview   | Academic_Update  | name                     | Description | subject   | optional_Message               | Course            | Subject   | Success_message                                                                              |
      | createdForFaculty | #/overview | Academic Updates | AcademicUpdateForFaculty | Description | AURequest | This is an an Academic request | CoursesForFaculty | AUSubject | Your requests have been submitted, we will send you a notification once they have been sent. |


  Scenario Outline:Verify Faculty can access the skyfactor application-@testcaseID=24269
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then Faculty land on Dashboard page
    Examples:
      | UserName                   | Password |
      | qaFaculty08@mailinator.com | Qait@123 |

  Scenario Outline: Verify Faculty is able to see created academic update on Academic Update Open section-@testcaseID=22919
    When user clicks on "<Course>" tab
    And user clicks on academic update link
    Then user is able to see "<created>" academic request in open academic update list
    Examples:
      | Course  | created           |
      | Courses | createdForFaculty |

  Scenario Outline: Verify faculty is able to provide grades to student via created Academic Update-@testcaseID=22920
    When Faculty clicks on update these student link
    And user provide grade to "<Std_name>" with details "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    And user clicks submit button on Academic update page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Std_name  | Risk | Progress_grade | Absences | Comments | ReferToAssitance | SendToStudent | Success_message                         |
      | N,Deborah | Low  | A              | 44       | Comment  | yes              | yes           | Your academic update has been submitted |

  Scenario Outline:verify Faculty is able to refer student for academic assistance-@testcaseID=22921
    When user refer student for academic assistance
    And user is able to see "<refrral_Success_message>" in the alert
    Examples:
      | refrral_Success_message       |
      | Referral Created Successfully |

  Scenario Outline: Verify Faculty is able to see grades of student provided using Academic Update on student dashboard provided-@testcaseID=22922
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    And user is able to navigate to the "<Student_name>" profile page
    And user clicks on "<Details>" tab on student page
    And user clicks on "<Course>" link under student details tab
    And user click on history link for "<created>" course
    Then user is able to see grade details  "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    Examples:
      | created           | Details | Search_Tab | Student_name | Course | Risk | Progress_grade | Absences | Comments | ReferToAssitance | SendToStudent |
      | createdForFaculty | Details | Search     | Deborah      | Course | Low  | A              | 44       | Comment  | yes              | yes           |

  Scenario Outline: Verify Faculty is able to see Academic update in Closed Section-@testcaseID=22923
    Given user is on "<Course>" page
    When user clicks on academic update link
    Then user is able to see "<created>" academic request in Closed academic update list
    Examples:
      | Course      | created           |
      | #/my-course | createdForFaculty |


  Scenario Outline: Verify user is able to provide grades to student using Adhoc Academic Update-@testcaseID=22924
    When user clicks on "<Course>" tab
    And user clicks on adhoc academic update icon for "<created>" course
    And user provide grade to "<Student_Name>" with details "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    And user clicks submit button on Academic update page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | created           | Student_Name | Risk | Progress_grade | Absences | Comments            | ReferToAssitance | SendToStudent | Course  | Success_message                         |
      | createdForFaculty | N,Deborah    | High | B              | 45       | Average Performance | No               | No            | Courses | Your academic update has been submitted |


  Scenario Outline: Verify user is able to see grades of student created using Adhoc Academic Update on student dashboard page-@testcaseID=22925
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    And user is able to navigate to the "<Student_name>" profile page
    And user clicks on "<Details>" tab on student page
    And user clicks on "<Course>" link under student details tab
    And user click on history link for "<created>" course
    Then user is able to see grade details  "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    Examples:
      | created           | Details | Search_Tab | Student_name | Course | Risk | Progress_grade | Absences | Comments            | ReferToAssitance | SendToStudent |
      | createdForFaculty | Details | Search     | Deborah      | Course | High | B              | 45       | Average Performance | No               | No            |


  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24270
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |


  Scenario Outline:Verify user is able to delete Faculty from course-@testcaseID=24271
    Given user is on "<courses>" page
    When user clicks on roster view of "<created>" course
    And user clicks on delete icon to delete "<Faculty Name>" from course
    And  user click on confirm Remove button on course page
    Then user is able to see "<Success_Message>" in the alert
    And user is not able to see "<Faculty Name>" in faculty section of course
    Examples:
      | created           | courses   | Faculty Name | Success_Message              |
      | createdForFaculty | #/courses | E,Johnson    | Faculty deleted successfully |

  Scenario Outline:Verify user is able to delete Student from course-@testcaseID=24272
    When user clicks on delete icon to delete "<Student Name>" from course
    And  user click on confirm Remove button on course page
    Then user is able to see "<Success_Message>" in the alert
    And user is not able to see "<Student Name>" in student section of course
    Examples:
      | Student Name | Success_Message              |
      | N,Deborah    | Student deleted successfully |
