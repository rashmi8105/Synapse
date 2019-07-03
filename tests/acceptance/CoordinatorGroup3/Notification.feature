Feature: CoordinatorNotification

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=23944
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |


  Scenario Outline: Verify coordinator is able to ON referral options from Setting page-@testcaseID=23945
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


  Scenario Outline: Verify user is able to create a Referral for student-@testcaseID=23946
    Given user is on "<Overview_Page>" page
    When user grabs the value of number of notification
    And user clicks on "<Search_Tab>" tab
    And user fills and clicks the "<Student_name>" in the search field
    And user is able to navigate to the "<Student_name>" profile page
    And user clicks on "<Activity_Stream>" tab on student page
    And user clicks on Add New Activity link
    And user clicks on "<Referral_tab>" tab on window
    And user select and fills following fields "<Reason_type>","<Assign_To>","<Interested_Party>" and "<Description>" in field
    And user select "<Detail_option>" details checkbox
    And user select "<Sharing_option>" sharing option for "<Team_Name>"
    And clicks on Create a Referral button
    Then user is able to see "<Success_message>" in the alert
    And user should be able to see the created "<Referral>" with "<Description>" in the list
    And user should see notifcation with notification count
    Examples:
      | Overview_Page | Search_Tab | Student_name | Activity_Stream | Referral_tab | Reason_type               | Assign_To | Interested_Party | Description                        | Sharing_option | Team_Name  | Success_message             | Referral | Detail_option              |
      | #/overview    | Search     | Deborah      | Activity Stream | Referral     | Class attendance positive | qanupur   | qavishvajeet     | Added referral on for Notification | Team           | SELECT ALL | Referral saved successfully | Referral | Notify Student of Referral |


  Scenario: Verify user is able to see notification  window on hovering over notification-@testcaseID=23947
    When user hover over notification
    Then user see notfication window

  Scenario Outline: Verify user is able to see notification  window on hovering over notification-@testcaseID=23948
    When user clicks on notfication
    And user clicks on "<Referral>" created by "<Faculty_Name>" to "<StudentName>"
    Then user should see comment for "<Activity>" activity
    Examples:
      | Referral | Faculty_Name | StudentName | Activity             |
      | Referral | qanupur      | N, Deborah  | ReferralNotification |


