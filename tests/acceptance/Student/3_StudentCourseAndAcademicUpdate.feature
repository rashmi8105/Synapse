Feature: StudentCourseAndAcademicUpdate

  Background:

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24242
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:Verify user is able to upload course for student role tests-@testcaseID=24243
    When user clicks on "<Manage_Courses>" button
    And user clicks on "<upload_Courses>" button on course page
    And user uploads course with details "<YearId>", "<TermId>", "<UniqueCourseSectionId>", "<SubjectCode>", "<CourseNumber>", "<SectionNumber>", "<CourseName>", "<CreditHours>", "<CollegeCode>", "<DeptCode>", "<Days/Times>", "<Location>"
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | upload_Course | Manage_Courses | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | Manage_Course | upload_Courses | YearId | TermId | UniqueCourseSectionId | SubjectCode | CourseNumber | SectionNumber | CourseName        | CreditHours | CollegeCode | DeptCode | Days/Times | Location |
      | Courses       | Course         | 1                 | 1         | 0           | 0          | Course        | Course         | 201617 | 123    | CSecID                | subCode     | CNumber      | SecNum        | StudentCourseName | 40          | 290         | 10       | 40         | Delhi    |

  Scenario Outline: Verify user is able to see course in roster view-@testcaseID=24244
    When user clicks on course link on course page
    And user clicks on roster view of "<created>" course
    Then user is able to see "<Section_Number>" and "<Course_Name_ID>" of "<created>" course in roster view of course
    Examples:
      | Section_Number | Course_Name_ID | created           |
      | Section Number | Course Name ID | CreatedForStudent |

  Scenario Outline:Verify user is able to upload Faculty to course for student role tests-@testcaseID=24245
    Given user is on "<courses>" page
    When user clicks on "<upload_Faculty>" button on course page
    And user uploads faculty with details "<FacultyID>", "<PermissionSet>", "<Remove>" to "<created>" course
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | created           | courses   | Faculty Name   | upload_Faculty | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | FacultyID | PermissionSet             | Remove |
      | CreatedForStudent | #/courses | qagoel,qanupur | Faculty        | 1                 | 1         | 0           | 0          | 12        | QAIDataPermissionTemplate | 0      |

  Scenario Outline: Verify user is able to see faculty in faculty section in roster view of course-@testcaseID=24246
    When user clicks on course link on course page
    And user clicks on roster view of "<created>" course
    Then user is able to see "<Faculty Name>" in faculty section of course
    Examples:
      | Faculty Name   | created           |
      | qagoel,qanupur | CreatedForStudent |

  Scenario Outline:Verify user is able to upload Student to course for student role tests-@testcaseID=24247
    Given user is on "<courses>" page
    When user clicks on "<upload_Student>" button on course page
    And user uploads student with details "<StudentId>", "<Remove>" to "<created>" course
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | created           | courses   | Student Name | upload_Student | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | StudentId | Remove |
      | CreatedForStudent | #/courses | I,Arnab      | Student        | 1                 | 1         | 0           | 0          | 1130      | 0      |

  Scenario Outline:verify user is able to see uploaded student in roaster view of course-@testcaseID=24248
    When user clicks on course link on course page
    And user clicks on roster view of "<created>" course
    Then user is able to see "<Student_Name>" in student section of course
    Examples:
      | Student_Name | created           |
      | I,Arnab      | CreatedForStudent |

  Scenario Outline: Verify user is able to ON all the features under Feature panel-@testcaseID=24249
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

  Scenario Outline: verify Coordinator is able to On all options under Academic Update Panel-@testcaseID=24250
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

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24253
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to provide grades to student using Adhoc Academic Update by referring student-@testcaseID=24254
    When user clicks on "<Course>" tab
    And user clicks on adhoc academic update icon for "<created>" course
    And user provide grade to "<Student_Name>" with details "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    And user clicks submit button on Academic update page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | created           | Student_Name | Risk | Progress_grade | Absences | Comments            | ReferToAssitance | SendToStudent | Course  | Success_message                         |
      | CreatedForStudent | I,Arnab      | High | B              | 44       | Average Performance | yes              | yes           | Courses | Your academic update has been submitted |

  Scenario Outline:verify user is able to refer student for academic assistance-@testcaseID=24255
    When user refer student for academic assistance
    And user is able to see "<refrral_Success_message>" in the alert
    Examples:
      | refrral_Success_message       |
      | Referral Created Successfully |

  Scenario Outline:Verify Student can access the skyfactor application-@testcaseID=24256
    Given user is on skyfactor login page
    When Student login into the application with "<EmailID>"
    Then Student lands on Survey page

    Examples:
      | EmailID                 |
      | autoqa08@mailinator.com |

  Scenario Outline:Verify Student can see Course details with grades-@testcaseID=24257
    When student clicks on "<Course>" tab
    Then student is able to see details of "<created>" course  on student course page with "<FacultyName>", "<DaysTime>" and "<Location>"
    And student is able to see grades with details "<Absences>","<InProgressGrade>" and "<Comment>" on student course page

    Examples:
      | Course | Absences | InProgressGrade | Comment             | FacultyName | DaysTime | Location |
      | Course | 44       | B               | Average Performance | qanupur     | 40       | Delhi    |

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24258
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to provide grades to student using Adhoc Academic Update without referring to student-@testcaseID=24259
    When user clicks on "<Course>" tab
    And user clicks on adhoc academic update icon for "<created>" course
    And user provide grade to "<Student_Name>" with details "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    And user clicks submit button on Academic update page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | created           | Student_Name | Risk | Progress_grade | Absences | Comments         | ReferToAssitance | SendToStudent | Course  | Success_message                         |
      | CreatedForStudent | I,Arnab      | High | A              | 20       | Good Performance | No               | No            | Courses | Your academic update has been submitted |

  Scenario Outline:Verify Student can access the skyfactor application-@testcaseID=24260
    Given user is on skyfactor login page
    When Student login into the application with "<EmailID>"
    Then Student lands on Survey page

    Examples:
      | EmailID                 |
      | autoqa08@mailinator.com |

  Scenario Outline:Verify Student is not able to see grades on student page-@testcaseID=24261
    When student clicks on "<Course>" tab
    And student is not able to see grades with details "<Absences>","<InProgressGrade>" and "<Comment>" on student course page
    Examples:
      | Course | Absences | InProgressGrade | Comment          |
      | Course | 20       | A               | Good Performance |


  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24262
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |


  Scenario Outline:Verify user is able to delete Faculty from course-@testcaseID=24263
    Given user is on "<courses>" page
    When user clicks on roster view of "<created>" course
    And user clicks on delete icon to delete "<Faculty Name>" from course
    And  user click on confirm Remove button on course page
    Then user is able to see "<Success_Message>" in the alert
    And user is not able to see "<Faculty Name>" in faculty section of course
    Examples:
      | created           | courses   | Faculty Name   | Success_Message              |
      | createdForStudent | #/courses | qagoel,qanupur | Faculty deleted successfully |


  Scenario Outline:Verify user is able to delete Student from course-@testcaseID=24264
    When user clicks on delete icon to delete "<Student Name>" from course
    And  user click on confirm Remove button on course page
    Then user is able to see "<Success_Message>" in the alert
    And user is not able to see "<Student Name>" in student section of course
    Examples:
      | Student Name | Success_Message              |
      | I,Arnab      | Student deleted successfully |