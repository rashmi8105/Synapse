Feature: CoordinatorMyActivityDownload.feature

  Background:

  Scenario Outline:Verify Coordinator can access the skyfactor application-@testcaseID=24128
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page

    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to see created Referral on activity download Page-@testcaseID=19757
    Given user is on "<Overview_Page>" page
    When user clicks on "<Report_Tab>" tab
    And user clicks on "<Activity_Download>" subtab
    Then user is able to view created "<Referral_Created>" on the page

    Examples:
      | Overview_Page | Report_Tab | Activity_Download    | Referral_Created |
      | #/overview    | Reports    | My Activity Download | Referral         |

  Scenario Outline: Verify user is able to view created Referral by clicking on the view link-@testcaseID=19758
    When user clicks on view link in front of created "<Referral_Created>"
    Then user is able to view the "<Referral_Created>" on the modal window

    Examples:
      | Referral_Created |
      | Referral         |

  Scenario Outline: Verify user is able to see created Contact on activity download Page-@testcaseID=19759
    Given user is on "<Activity>" page
    Then user is able to view created "<Contact_Created>" on the page

    Examples:
      | Activity   | Contact_Created |
      | #/activity | Contact         |

  Scenario Outline: Verify user is able to view created Contact by clicking on the view link-@testcaseID=19760
    When user clicks on view link in front of created "<Contact_Created>"
    Then user is able to view the "<Contact_Created>" on the modal window

    Examples:
      | Contact_Created |
      | Contact         |

  Scenario Outline: Verify user is able to see created Note on activity download Page-@testcaseID=19761
    Given user is on "<Activity>" page
    Then user is able to view created "<Note_Created>" on the page

    Examples:
      | Activity   | Note_Created |
      | #/activity | Note         |

  Scenario Outline: Verify user is able to view created Note by clicking on the view link-@testcaseID=19762
    When user clicks on view link in front of created "<Note_Created>"
    Then user is able to view the "<Note_Created>" on the modal window

    Examples:
      | Note_Created |
      | Note         |

  Scenario Outline: Verify user is able to see created Appointment on activity download Page-@testcaseID=19763
    When user is on "<Activity>" page
    Then user is able to view created "<Appointment_Created>" on the page

    Examples:
      | Activity   | Appointment_Created |
      | #/activity | Appointment         |

  Scenario Outline: Verify user is able to view created Appointment by clicking on the view link-@testcaseID=19764
    When user clicks on view link in front of created "<Appointment_Created>"
    Then user is able to view the "<Appointment_Created>" on the modal window

    Examples:
      | Appointment_Created |
      | Appointment         |



