Feature: CoordinatorAddFacultyStudentThroughUI

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24142
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to add student through UI-@testcaseID=19709
    Given user is on "<Page>" page
    When user clicks on "<ManageStudent_Button>" button
    And user grabs student,participant and active participant count
    And user clicks on AddStudent button
    And user fills "<FirstName>" as first Name
    And user fills "<LastName>" as last Name
    And user fills "<Title>" as title
    And user fills "<ContactInfo>" as contactInfo
    And user fills "<Phone>" as phoneNumber
    And user fills "<ID>" as ID
    And user fills "<LDAP>" as LDAPUserName
    And user selects "<Mobile_checked>" option
    And user selects "<Inactive>" as inactive option
    And user selects "<NotParticipating>" as NonParticipating option
    And user clicks on save button to save user
    Then user is able to see "<Success_message>" in the alert
    And count of the student,participant and active participant increases by one

    Examples:
      | Page       | ManageStudent_Button | FirstName     | LastName     | Title | ContactInfo | Phone | ID | LDAP     | Mobile_checked | Inactive | NotParticipating | Success_message              |
      | #/overview | Students             | Std_FirstName | Std_LastName | Miss  | Std_Test    | 67    | 09 | Std_Test | yes            | no       | no               | student created successfully |


  Scenario Outline: Verify user is able to search the created student through UI-@testcaseID=19710
    When user clicks on Search button
    And user searches the "<FirstName>" user
    Then user is able to view the user's created data

    Examples:
      | FirstName     |
      | Std_FirstName |

  Scenario Outline: Verify user is able to edit student through UI-@testcaseID=19711
    When user clicks on Edit icon
    And user fills "<FirstName>" as first Name
    And user fills "<LastName>" as last Name
    And user fills "<Title>" as title
    And user fills "<ContactInfo>" as contactInfo
    And user fills "<Phone>" as phoneNumber
    And user fills "<ID>" as ID
    And user fills "<LDAP>" as LDAPUserName
    And user selects "<Mobile_checked>" option
    And user selects "<Inactive>" as inactive option
    And user selects "<NotParticipating>" as NonParticipating option
    And user clicks on save button to save user
    Then user is able to see "<Success_message>" in the alert

    Examples:
      | FirstName       | LastName       | Title | ContactInfo | Phone | ID | LDAP       | Mobile_checked | Inactive | NotParticipating | Success_message                     |
      | EditedFirstName | EditedLastName | Miss  | EditedTest  | 67    | 09 | EditedTest | yes            | no       | no               | Student record updated successfully |

  Scenario Outline: Verify user is able to search the edited student through UI-@testcaseID=19712
    When user searches the "<FirstName>" user
    Then user is able to view the user's edited data

    Examples:
      | FirstName       |
      | EditedFirstName |

  Scenario Outline: Verify user is not able to delete the student-@testcaseID=19713
    When user clicks on Delete icon
    And Click on Remove button displayed on the modal window
    Then user is able to see "<Error_message>" in the alert

    Examples:
      | Error_message                                                                                            |
      | We are unable to delete users who have activity or academic data associated with their Mapworks account. |

  Scenario Outline: Verify user is able to add faculty through UI-@testcaseID=19714
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


  Scenario Outline: Verify Student is able to search the created Facultys through UI-@testcaseID=19715
    When user clicks on Search button
    And user searches the "<FirstName>" user
    Then user is able to view the user's created data

    Examples:
      | FirstName         |
      | Faculty_FirstName |

  Scenario Outline: Verify user is able to edit Faculty through UI-@testcaseID=19716
    When user clicks on Edit icon
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

    Examples:
      | FirstName       | LastName       | Title | ContactInfo | Phone | ID | LDAP       | Mobile_checked | Inactive | NotParticipating | Success_message                     |
      | EditedFirstName | EditedLastName | Miss  | EditedTest  | 67    | 09 | EditedTest | yes            | no       | no               | Faculty record updated successfully |

  Scenario Outline: Verify user is able to search the edited faculty through UI-@testcaseID=19717
    When user searches the "<FirstName>" user
    Then user is able to view the user's edited data

    Examples:
      | FirstName       |
      | EditedFirstName |

  Scenario Outline: Verify user is not able to delete the faculty-@testcaseID=19718
    When user clicks on Delete icon
    And Click on Remove button displayed on the modal window
    Then user is able to see "<Error_message>" in the alert

    Examples:
      | Error_message                                                                                            |
      | We are unable to delete users who have activity or academic data associated with their Mapworks account. |

