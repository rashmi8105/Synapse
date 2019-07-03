Feature: CoordinatorBulk Referral

  Background:

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24022
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Ensure that user is able to add faculty to group-@testcaseID=24023
    Given user is on "<Group_Summary_Page>" page
    When user clicks on AllStudent group on group summary page
    And user adds "<Faculty_Name>" with permission "<PermissionName>" to group
    And user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Group_Summary_Page | Manage_Groups | Success_message                | PermissionName      | Faculty_Name |
      | #/groupsummary     | Groups        | Successfully Updated the Group | QAIACESS_ESPRJ11677 | qajaspal     |

  Scenario Outline: Verify coordinator is able to ON referral options from Setting page-@testcaseID=24024
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


  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24025
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName                | Password |
      | qajaspal@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to create and view  search with gray risk level-@testcaseID=24026
    Given user is on "<Overview_Page>" page
    When user clicks on "<Search_Tab>" tab
    And user clicks on "<Custom_Search>" subtab
    And user clicks "<Risk_Panel>" panel
    And user selects "<Risk_level>" risk level
    And user clicks on search button
    Then user is able to view the list of the students

    Examples:
      | Overview_Page | Search_Tab | Custom_Search | Risk_Panel | Risk_level | Save_SearchBtn | Search_Name       | Success_message           | SavedSearch   |
      | #/overview    | Search     | Custom Search | Risk level | gray       | SaveSearch     | Automation_Search | Search saved successfully | #/savedsearch |

  Scenario Outline: Verify user is able to create bulk Referral-@testcaseID=24027
    When user selects selectall option
    And user selects "<Referral>" from bulk option
    And user select and fills following fields "<Reason_type>","<Assign_To>","<Interested_Party>" and "<Description>" in field
    And user select "<Detail_option>" details checkbox
    And user select "<Sharing_option>" sharing option for "<Team_Name>"
    And clicks on Create a Referral button
    Then user is able to see "<Success_message>" in the alert

    Examples:
      | Referral_tab | Reason_type               | Assign_To           | Interested_Party | Description       | Sharing_option | Team_Name  | Success_message                                                                        | Referral | Detail_option |
      | Referral     | Class attendance positive | Central Coordinator | None             | Added referral on | Public         | SELECT ALL | Your request has been submitted, we will send you a notification once it has completed | Referral | None          |

  Scenario Outline: Verify created referral can be verified at one of the student end-@testcaseID=24028
    When user clicks on the student name "<Student_Name>"
    And user clicks on "<Activity_Stream>" tab on student page
    Then user should be able to see the created "<Referral>" with "<Description>" in the list
    Examples:
      | Student_Name | Activity_Stream | Referral | Description       |
      | Adam         | Activity Stream | Referral | Added referral on |

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24029
    Given  user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Ensure that user is able to remove faculty from group-@testcaseID=24030
    Given user is on "<Group_Summary_Page>" page
    And user clicks on AllStudent group on group summary page
    And user deletes "<Faculty>" from the group
    And  user clicks on save button on group page
    Then user is able to see "<Success_message>" in the alert
    Examples:
      | Success_message                | created | Faculty  | Group_Summary_Page |
      | Successfully Updated the Group | created | qajaspal | #/groupsummary     |

 