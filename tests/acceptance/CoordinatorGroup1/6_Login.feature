Feature: CoordinatorLogin

  Scenario Outline: Ensure that error message is displayed while login using invalid credentials-@testcaseID=19752
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User should be able to see the error message on the page

    Examples:
      | UserName               | Password |
      | invalid@mailinator.com | Qait@123 |


  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=19753
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Ensure that the password can be updated through My Account-@testcaseID=19754
    Given user is on "<Overview_Page>" page
    When user clicks on account dropdown
    And Click on "<My_Account>" link
    Then user is able to update the "<New_Password>" for my user

    Examples:
      | Overview_Page | My_Account | New_Password |
      | #/overview    | My Account | Qait@123     |

  Scenario Outline: Verify Coordinator can access the application with new Password-@testcaseID=19755
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<New_Password>"
    Then User land on Overview page

    Examples:
      | UserName               | New_Password |
      | qanupur@mailinator.com | Qait@123     |

  Scenario Outline: Ensure that the phone can be updated through My Account-@testcaseID=19756
    Given user is on "<Overview_Page>" page
    When user clicks on account dropdown
    And Click on "<My_Account>" link
    Then user is able to update the "<New_Phone>" for my user

    Examples:
      | Overview_Page | My_Account | New_Phone  |
      | #/overview    | My Account | 1234567890 |

