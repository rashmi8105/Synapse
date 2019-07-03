Feature: FacultyLogin

  Scenario Outline: Ensure that error message is displayed while login using invalid credentials-@testcaseID=22953
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User should be able to see the error message on the page

    Examples:
      | UserName               | Password |
      | invalid@mailinator.com | Qait@123 |


  Scenario Outline: Verify Faculty can access the skyfactor application with valid credentials-@testcaseID=22954
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then Faculty land on Dashboard page

    Examples:
      | UserName                   | Password |
      | qaFaculty08@mailinator.com | Qait@123 |

  Scenario Outline: Ensure that the password can be updated through My Account-@testcaseID=22955
    Given user is on "<Overview_Page>" page
    When user clicks on account dropdown
    And Click on "<My_Account>" link
    Then user is able to update the "<New_Password>" for my user

    Examples:
      | Overview_Page | My_Account | New_Password |
      | #/overview    | My Account | Qait@123     |

  Scenario Outline: Verify Faculty can access the application with new Password-@testcaseID=22956
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<New_Password>"
    Then Faculty land on Dashboard page

    Examples:
      | UserName                   | New_Password |
      | qaFaculty08@mailinator.com | Qait@123     |

  Scenario Outline: Ensure that the phone can be updated through My Account-@testcaseID=22957
    Given user is on "<Overview_Page>" page
    When user clicks on account dropdown
    And Click on "<My_Account>" link
    Then user is able to update the "<New_Phone>" for my user

    Examples:
      | Overview_Page | My_Account | New_Phone  |
      | #/overview    | My Account | 1234567890 |

