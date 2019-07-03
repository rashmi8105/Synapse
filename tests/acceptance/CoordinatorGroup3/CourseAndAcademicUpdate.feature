Feature: CoordinatorCourseAndAcademicUpdate

  Background:

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24001
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:Verify user is able to upload course for coordinator role tests-@testcaseID=24002
    When user clicks on "<Manage_Courses>" button
    And user clicks on "<upload_Courses>" button on course page
    And user uploads course with details "<YearId>", "<TermId>", "<UniqueCourseSectionId>", "<SubjectCode>", "<CourseNumber>", "<SectionNumber>", "<CourseName>", "<CreditHours>", "<CollegeCode>", "<DeptCode>", "<Days/Times>", "<Location>"
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | upload_Course | Manage_Courses | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | Manage_Course | upload_Courses | YearId | TermId | UniqueCourseSectionId | SubjectCode | CourseNumber | SectionNumber | CourseName            | CreditHours | CollegeCode | DeptCode | Days/Times | Location |
      | Courses       | Course         | 1                 | 1         | 0           | 0          | Course        | Course         | 201617 | 123    | CSecID                | subCode     | CNumber      | SecNum        | CoordinatorCourseName | 40          | 290         | 10       | 40         | Delhi    |

  Scenario Outline: Verify user is able to see course on roster view for coordinator role tests-@testcaseID=24003
    When user clicks on course link on course page
    And user clicks on roster view of "<created>" course
    Then user is able to see "<Section_Number>" and "<Course_Name_ID>" of "<created>" course in roster view of course
    Examples:
      | Section_Number | Course_Name_ID | created               |
      | Section Number | Course Name ID | CreatedForCoordinator |

  Scenario Outline:Verify user is able to upload Faculty to course for coordinator role tests-@testcaseID=24004
    Given user is on "<courses>" page
    When user clicks on "<upload_Faculty>" button on course page
    And user uploads faculty with details "<FacultyID>", "<PermissionSet>", "<Remove>" to "<created>" course
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | created               | courses   | Faculty Name   | upload_Faculty | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | FacultyID | PermissionSet             | Remove |
      | CreatedForCoordinator | #/courses | qagoel,qanupur | Faculty        | 1                 | 1         | 0           | 0          | 12        | QAIDataPermissionTemplate | 0      |

  Scenario Outline: Verify user is able to see faculty in faculty section in roster view of course-@testcaseID=24005
    When user clicks on course link on course page
    And user clicks on roster view of "<created>" course
    Then user is able to see "<Faculty Name>" in faculty section of course
    Examples:
      | Faculty Name   | created               |
      | qagoel,qanupur | CreatedForCoordinator |

  Scenario Outline:Verify user is able to upload Student to course for coordinator role tests-@testcaseID=24006
    Given user is on "<courses>" page
    When user clicks on "<upload_Student>" button on course page
    And user uploads student with details "<StudentId>", "<Remove>" to "<created>" course
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | created               | courses   | Student Name | upload_Student | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError | StudentId | Remove |
      | CreatedForCoordinator | #/courses | N,Deborah    | Student        | 1                 | 1         | 0           | 0          | 1135      | 0      |

  Scenario Outline:verify user is able to see uploaded student in roaster view of course-@testcaseID=24007
    When user clicks on course link on course page
    And user clicks on roster view of "<created>" course
    Then user is able to see "<Student_Name>" in student section of course
    Examples:
      | Student_Name | created               |
      | N,Deborah    | CreatedForCoordinator |

  Scenario Outline: Verify user is able to ON all the features under Feature panel-@testcaseID=24008
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

  Scenario Outline: verify Coordinator is able to On all Academic Update Option-@testcaseID=24009
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

  Scenario Outline: Verify user is able to create Academic Update-@testcaseID=24010
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
      | created               | Overview   | Academic_Update  | name                         | Description | subject   | optional_Message               | Course                | Subject   | Success_message                                                                              |
      | createdForCoordinator | #/overview | Academic Updates | AcademicUpdateForCoordinator | Description | AURequest | This is an an Academic request | CoursesForCoordinator | AUSubject | Your requests have been submitted, we will send you a notification once they have been sent. |

  Scenario Outline: Verify user is able to see created academic update on Academic Update Open section-@testcaseID=24011
    When user clicks on "<Course>" tab
    And user clicks on academic update link
    Then user is able to see "<created>" academic request in open academic update list
    Examples:
      | Course  | created               |
      | Courses | createdForCoordinator |

  Scenario Outline: Verify user is able to provide grades to student using created Academic Update-@testcaseID=24012
    When user clicks on update these student link
    And user provide grade to "<Std_name>" with details "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    And user clicks submit button on Academic update page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Std_name  | Risk | Progress_grade | Absences | Comments | ReferToAssitance | SendToStudent | Success_message                         |
      | N,Deborah | Low  | A              | 44       | you are  | yes              | yes           | Your academic update has been submitted |

  Scenario Outline:verify user is able to refer student for academic assistance-@testcaseID=24013
    When user refer student for academic assistance
    And user is able to see "<refrral_Success_message>" in the alert
    Examples:
      | refrral_Success_message       |
      | Referral Created Successfully |

  Scenario Outline: Verify user is able to see grades of student provided using Academic Update-@testcaseID=24014
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    And user is able to navigate to the "<Student_name>" profile page
    And user clicks on "<Details>" tab on student page
    And user clicks on "<Course>" link under student details tab
    And user click on history link for "<created>" course
    Then user is able to see grade details  "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    Examples:
      | created               | Details | Search_Tab | Student_name | Course | Risk | Progress_grade | Absences | Comments | ReferToAssitance | SendToStudent |
      | createdForCoordinator | Details | Search     | Deborah      | Course | Low  | A              | 44       | you are  | yes              | yes           |


  Scenario Outline: Verify user is able to see Academic update in Closed Section-@testcaseID=24015
    Given user is on "<Course>" page
    When user clicks on academic update link
    Then user is able to see "<created>" academic request in Closed academic update list
    Examples:
      | Course      | created               |
      | #/my-course | createdForCoordinator |

  Scenario Outline: Verify user is able to provide grades to student using Adhoc Academic Update-@testcaseID=24016
    When user clicks on "<Course>" tab
    And user clicks on adhoc academic update icon for "<created>" course
    And user provide grade to "<Student_Name>" with details "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    And user clicks submit button on Academic update page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | created               | Student_Name | Risk | Progress_grade | Absences | Comments            | ReferToAssitance | SendToStudent | Course  | Success_message                         |
      | createdForCoordinator | N,Deborah    | High | B              | 44       | Average Performance | No               | No            | Courses | Your academic update has been submitted |


  Scenario Outline: Verify user is able to see grades of student created using Adhoc Academic Update-@testcaseID=24017
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    And user is able to navigate to the "<Student_name>" profile page
    And user clicks on "<Details>" tab on student page
    And user clicks on "<Course>" link under student details tab
    And user click on history link for "<created>" course
    Then user is able to see grade details  "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    Examples:
      | created               | Details | Search_Tab | Student_name | Course | Risk | Progress_grade | Absences | Comments            | ReferToAssitance | SendToStudent |
      | createdForCoordinator | Details | Search     | Deborah      | Course | High | B              | 44       | Average Performance | No               | No            |

  Scenario Outline: Verify user is able to upload Academic Update for an student-@testcaseID=24018
    Given user is on "<overview>" page
    When user clicks on "<Academic_Updates>" subtab
    And user clicks on upload academic update button
    And user uploads grade with details "<ExternalID>", "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>"
    Then user is able to see correct values "<TotalRowsUploaded>""<RowsAdded>""<RowsUpdated>""<TotalError>" in the displayed table
    Examples:
      | overview   | Academic_Updates | Risk | Progress_grade | Absences | Comments         | ExternalID | TotalRowsUploaded | RowsAdded | RowsUpdated | TotalError |
      | #/overview | Academic Updates | High | C              | 48       | Good performance | 1135       | 1                 | 1         | 0           | 0          |

  Scenario Outline: Verify user is able to see grades of student created using Upload-@testcaseID=24019
    When user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    And user is able to navigate to the "<Student_name>" profile page
    And user clicks on "<Details>" tab on student page
    And user clicks on "<Course>" link under student details tab
    And user click on history link for "<created>" course
    Then user is able to see grade details  "<Progress_grade>", "<Risk>", "<Absences>", "<Comments>" "<ReferToAssitance>", "<SendToStudent>"
    Examples:
      | created               | Details | Search_Tab | Student_name | Course | Risk | Progress_grade | Absences | Comments         | ReferToAssitance | SendToStudent |
      | createdForCoordinator | Details | Search     | Deborah      | Course | High | C              | 48       | Good performance | No               | No            |

  Scenario Outline:Verify user is able to delete Faculty from course-@testcaseID=24020
    Given user is on "<courses>" page
    When user clicks on roster view of "<created>" course
    And user clicks on delete icon to delete "<Faculty Name>" from course
    And  user click on confirm Remove button on course page
    Then user is able to see "<Success_Message>" in the alert
    And user is not able to see "<Faculty Name>" in faculty section of course
    Examples:
      | created               | courses   | Faculty Name   | Success_Message              | created               |
      | createdForCoordinator | #/courses | qanupur,qagoel | Faculty deleted successfully | CreatedForCoordinator |

  Scenario Outline:Verify user is able to delete Student from course-@testcaseID=24021
    When user clicks on delete icon to delete "<Student Name>" from course
    And  user click on confirm Remove button on course page
    Then user is able to see "<Success_Message>" in the alert
    And user is not able to see "<Student Name>" in student section of course
    Examples:
      | Student Name | Success_Message              |
      | N,Deborah    | Student deleted successfully |
