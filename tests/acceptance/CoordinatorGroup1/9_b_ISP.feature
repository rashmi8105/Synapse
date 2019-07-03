Feature: CoordinatorISP

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24292
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline:Ensure user is able to create ISP  with category Data Type-@testcaseID=19749
    Given user is on "<Overview_page>" page
    When user clicks on "<ISP>" link under additional setup
    And users clicks on Add another profile item button
    And select "<Category Data Type>" from dropdown
    And fill details for "<Category Data Type>" ISP with  "<ISPName>"
    And select "<Calendar_assignment_None>" for ISP
    And click on Save button to save ISP
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<Category Data Type>" ISP in ISP list
    Examples:
      | Overview_page | ISP  | Category Data Type | Success_message                     | Calendar_assignment_None | ISPName |
      | #/overview    | ISPs | Category           | Profile Item submitted Successfully | None                     | Gender  |

  Scenario Outline:Ensure user is able to edit ISP with category Data Type-@testcaseID=19750
    Given user is on "<institute_profile_Page>" page
    When user clicks on "<Edit_Icon>" for "<Category Data Type>" ISP
    And  user edits  the "<Category Data Type>" ISP with new details "<EditedISPName>"
    And click on Save button to save ISP
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<Category Data Type>" ISP in ISP list

    Examples:
      | institute_profile_Page   | Edit_Icon | Success_message                  | Category Data Type | EditedISPName |
      | #/institutionprofileitem | edit      | Profile Item Edited Successfully | Category           | GenderOfUser  |

  Scenario Outline:Ensure user is able to delete ISP with category Data Type-@testcaseID=19751
    Given user is on "<institute_profile_Page>" page
    When user clicks on "<Delete_Icon>" for "<Category Data Type>" ISP
    And user clicks on Delete button on DialogBox
    Then user is able to see "<Success_message>" in the alert
    And user is not able to see "<Category Data Type>" ISP in ISP list
    Examples:
      | institute_profile_Page   | Delete_Icon | Success_message                   | Category Data Type |
      | #/institutionprofileitem | delete      | Profile Item deleted Successfully | Category           |

  Scenario Outline: Ensure user is able to create ISP with date data type-@testcaseID=22690
    Given user is on "<institute_profile_Page>" page
    When  users clicks on Add another profile item button
    And select "<Date Data Type>" from dropdown
    And fill details for "<Date Data Type>" type ISP with details "<ISPName>", "<ISPDescription>"
    And select "<Calendar_assignment_None>" for ISP
    And click on Save button to save ISP
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<Date Data Type>" ISP in ISP list
    Examples:
      | institute_profile_Page   | LinkName | Date Data Type | Success_message                     | Calendar_assignment_None | ISPName       | ISPDescription     |
      | #/institutionprofileitem | ISPs     | Date           | Profile Item submitted Successfully | None                     | DateOfJoining | DateISPDescription |

  Scenario Outline:Ensure user is able to edit ISP with date data type-@testcaseID=22691
    Given user is on "<institute_profile_Page>" page
    When user clicks on "<Edit_Icon>" for "<Date Data Type>" ISP
    And  user edits  the "<Date Data Type>" ISP with new details "<EditedISPName>", "<EditedISPISPDescription>"
    And click on Save button to save ISP
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<Date Data Type>" ISP in ISP list
    Examples:
      | institute_profile_Page   | Edit_Icon | Success_message                  | Date Data Type | EditedISPName | EditedISPISPDescription  |
      | #/institutionprofileitem | edit      | Profile Item Edited Successfully | Date           | DateOfLeaving | EditeddateISPDescription |

  Scenario Outline: Ensure user is able to delete ISP with date data type-@testcaseID=22692
    Given user is on "<institute_profile_Page>" page
    When user clicks on "<Delete_Icon>" for "<Date Data Type>" ISP
    And user clicks on Delete button on DialogBox
    Then user is able to see "<Success_message>" in the alert
    And user is not able to see "<Date Data Type>" ISP in ISP list
    Examples:
      | institute_profile_Page   | Delete_Icon | Success_message                   | Date Data Type |
      | #/institutionprofileitem | delete      | Profile Item deleted Successfully | Date           |


  Scenario Outline:Ensure user is able to create ISP with number data type-@testcaseID=22693
    Given user is on "<institute_profile_Page>" page
    When users clicks on Add another profile item button
    And select "<Number Data Type>" from dropdown
    And fill details for "<Number Data Type>" type ISP with details "<ISPName>", "<ISPDescription>", "<MinmunValue>", "<MaximumValue>", "<decimalPoint>"
    And select "<Calendar_assignment_None>" for ISP
    And click on Save button to save ISP
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<Number Data Type>" ISP in ISP list
    Examples:
      | institute_profile_Page   | LinkName | Number Data Type | Success_message                     | Calendar_assignment_None | ISPName   | ISPDescription       | MinmunValue | MaximumValue | decimalPoint |
      | #/institutionprofileitem | ISPs     | Number           | Profile Item submitted Successfully | None                     | NumberISP | NumberISPDescription | 10          | 100          | 0            |

  Scenario Outline:Ensure user is able to edit ISP with number data type-@testcaseID=22694
    Given user is on "<institute_profile_Page>" page
    When user clicks on "<Edit_Icon>" for "<Number Data Type>" ISP
    And  user edits  the "<Number Data Type>" ISP with new details "<EditedISPName>", "<EditedISPISPDescription>", "<MinmunValue>", "<MaximumValue>", "<decimalPoint>"
    And click on Save button to save ISP
    Then  user is able to see "<Success_message>" in the alert
    And user is able to see "<Number Data Type>" ISP in ISP list

    Examples:
      | institute_profile_Page   | Edit_Icon | Success_message                  | Number Data Type | ISPName   | EditedISPName   | EditedISPISPDescription    | MinmunValue | MaximumValue | decimalPoint |
      | #/institutionprofileitem | edit      | Profile Item Edited Successfully | Number           | NumberISP | EditednumberISP | EditednumberISPDescription | 10          | 100          | 0            |

  Scenario Outline:Ensure user is able to delete ISP with number data type-@testcaseID=22695
    Given user is on "<institute_profile_Page>" page
    When user clicks on "<Delete_Icon>" for "<Number Data Type>" ISP
    And user clicks on Delete button on DialogBox
    Then  user is able to see "<Success_message>" in the alert
    And user is not able to see "<Number Data Type>" ISP in ISP list
    Examples:
      | institute_profile_Page   | Delete_Icon | Success_message                   | Number Data Type |
      | #/institutionprofileitem | delete      | Profile Item deleted Successfully | Number           |

  Scenario Outline: Ensure user is able to create ISP with text data type-@testcaseID=22696
    Given user is on "<institute_profile_Page>" page
    When users clicks on Add another profile item button
    And select "<Text Data Type>" from dropdown
    And fill details for "<Text Data Type>" type ISP with details "<ISPName>", "<ISPDescription>"
    And select "<Calendar_assignment_None>" for ISP
    And click on Save button to save ISP
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<Text Data Type>" ISP in ISP list
    Examples:
      | institute_profile_Page   | Text Data Type | Success_message                     | Calendar_assignment_None | ISPName | ISPDescription     |
      | #/institutionprofileitem | Text           | Profile Item submitted Successfully | None                     | TextISP | TextISPDescription |

  Scenario Outline: Ensure user is able to edit ISP with text data type-@testcaseID=22697
    Given user is on "<institute_profile_Page>" page
    When user clicks on "<Edit_Icon>" for "<Text Data Type>" ISP
    And  user edits  the "<Text Data Type>" ISP with new details "<EditedISPName>", "<EditedISPISPDescription>"
    And click on Save button to save ISP
    Then user is able to see "<Success_message>" in the alert
    And user is able to see "<Text Data Type>" ISP in ISP list

    Examples:
      | institute_profile_Page   | Edit_Icon | Success_message                  | Text Data Type | EditedISPName | EditedISPISPDescription  |
      | #/institutionprofileitem | edit      | Profile Item Edited Successfully | Text           | EditedtextISP | EditedtextISPDescription |

  Scenario Outline:Ensure user is able to delete ISP with text data type-@testcaseID=22698
    Given  user is on "<institute_profile_Page>" page
    When user clicks on "<Delete_Icon>" for "<Text Data Type>" ISP
    And user clicks on Delete button on DialogBox
    Then user is able to see "<Success_message>" in the alert
    And user is not able to see "<Text Data Type>" ISP in ISP list
    Examples:
      | institute_profile_Page   | Delete_Icon | Success_message                   | Text Data Type |
      | #/institutionprofileitem | delete      | Profile Item deleted Successfully | Text           |