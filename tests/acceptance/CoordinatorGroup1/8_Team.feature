Feature: CoordinatorTeam

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24296
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |
#
  Scenario Outline:Ensure that the Nevermind button works fine in add team workflow-@testcaseID=19796
    Given user is on "<Overview_Page>" page
    When user clicks on "<TeamManage_Button>" button
    And user clicks on add another team button
    And fill "<Team_Name>" and "<FacultyName1>","<FacultyName2>" with Role "<Role1>" and "<Role2>" respectively
    And click on cancel team button
    Then user is not able to view the team on the page

    Examples:
      | Overview_Page | TeamManage_Button | Team_Name     | FacultyName1 | FacultyName2 | Role1  | Role2  |
      | #/overview    | Teams             | AutomatedTeam | qanupur      | qavishvajeet | Member | Leader |

  Scenario Outline:Ensure that user is able to view the message text when team have no leader assigned in it-@testcaseID=19797
    When user clicks on add another team button
    And fill "<Team_Name>" and "<FacultyName1>","<FacultyName2>" with Role "<Role1>" and "<Role2>" respectively
    And click on save team button
    Then user is able to see "<Success_message>" in the alert
    And user is able to view the team on the page
    And user is able to view the "<Message_Text>" on the page

    Examples:
      | Overview_Page | TeamManage_Button | Team_Name     | FacultyName1 | FacultyName2 | Role1  | Role2  | Message_Text       | Success_message              |
      | #/overview    | Teams             | AutomatedTeam | qanupur      | qavishvajeet | Member | Member | No leader assigned | Team is Created successfully |

  Scenario Outline:Verify Member is not able to view Widget on the Dashboard page-@testcaseID=19798
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    And user clicks on "<TabName>" tab
    Then user should not be able to view Team widget

    Examples:
      | UserName                | Password | TabName   |
      | qaaditya@mailinator.com | Qait@123 | Dashboard |

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24299

    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |


  Scenario Outline:Ensure that user is able to view the message text when team have no member assigned in it-@testcaseID=22683
    Given user is on "<Team_Page>" page
    When user click on edit icon in front of the created team
    And fill "<Team_Name>" and "<FacultyName1>","<FacultyName2>" with Role "<Role1>" and "<Role2>" respectively
    And click on save team button
    Then user is able to see "<Success_message>" in the alert
    And user is able to view the team on the page
    And user is able to view the "<Message_Text>" on the page
    Examples:
      | Team_Page | Team_Name     | FacultyName1 | FacultyName2 | Role1  | Role2  | Message_Text       | Success_message               |
      | #/mteam   | AutomatedTeam | qanupur      | qavishvajeet | Leader | Leader | No member assigned | Successfully Updated The Team |


  Scenario Outline:Verify Leader is able to view Widget on the Dashboard page-@testcaseID=22684
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    And user clicks on "<TabName>" tab
    Then user should be able to view Team widget

    Examples:
      | UserName                    | Password | TabName   |
      | qavishvajeet@mailinator.com | Qait@123 | Dashboard |


  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24297

    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:Ensure that user is not able to view any message when team have 1 member and 1 leader assigned in it-@testcaseID=22685
    Given user is on "<Team_Page>" page
    When user click on edit icon in front of the created team
    And fill "<Team_Name>" and "<FacultyName1>","<FacultyName2>" with Role "<Role1>" and "<Role2>" respectively
    And click on save team button
    Then user is able to see "<Success_message>" in the alert
    And user is able to view the team on the page
    And user is not able to view the "<Message_Text>" on the page
    Examples:
      | Team_Page | Team_Name     | FacultyName1 | FacultyName2 | Role1  | Role2  | Message_Text       | Success_message               |
      | #/mteam   | AutomatedTeam | qanupur      | qavishvajeet | Leader | Member | No member assigned | Successfully Updated The Team |

  Scenario Outline:Ensure that user is able to remove the faculties assigned in it-@testcaseID=22686
    When user click on edit icon in front of the created team
    And remove the faculties "<FacultyName1>" and "<FacultyName2>" assigned in the team
    And click on save team button
    Then user is able to see "<Success_message>" in the alert
    And user is able to view the team on the page
    And user is able to view the "<Message_Text>" on the page
    Examples:
      | Overview_Page | FacultyName1 | FacultyName2 | Role1  | Role2  | Message_Text                          | Success_message               |
      | #/overview    | qanupur      | qavishvajeet | Leader | Member | No leader assigned No member assigned | Successfully Updated The Team |

  Scenario Outline:Ensure that user is able to delete the team-@testcaseID=22687
    When user click on delete icon in front of the created team
    And click on delete button displayed on the dialog-box
    Then user is able to see "<Success_message>" in the alert
    And user is not able to view the team on the page
    Examples:
      | Overview_Page | TeamManage_Button | Team_Name     | FacultyName1 | FacultyName2 | Role1  | Role2  | Message_Text                          | Success_message                  |
      | #/overview    | Teams             | AutomatedTeam | qanupur      | qavishvajeet | Leader | Member | No leader assigned No member assigned | The Team is Deleted Successfully |


