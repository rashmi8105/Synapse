Feature: CoordinatorAppointments

  Background:

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24054
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Verify user is able to ON all the features under Feature panel-@testcaseID=24055
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

  Scenario Outline: Verify Coordinator can access the skyfactor application-@testcaseID=24305
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    Then User land on Overview page
    Examples:
      | UserName               | Password |
      | qanupur@mailinator.com | Qait@123 |

  Scenario Outline: Ensure that the Nevermind button works fine on Book Appointment modal-@testcaseID=24056
    Given user is on "<Overview_Page>" page
    When user clicks on "<Appointment_Tab>" tab
    And user clicks on "<Schedule_Appointment>" link on appointment page
    And fill all the mandatory fields "<Reason_for_Appointment>","<ScheduleDate>","<Location>","<SharingOption>","<Attendees1>","<Attendees2>" to "<Action>" appointment
    And click on cancel button
    Then user is not able to view appointment on the page

    Examples:
      | Overview_Page | Schedule_Appointment | Appointment_Tab | Reason_for_Appointment   | ScheduleDate | Location | SharingOption | Attendees1 | Attendees2   | Action |
      | #/overview    | Schedule Appointment | Appointments    | Class attendance concern | current      | Noida    | private       | Kavitha    | Karthikeyani | Create |


  Scenario Outline: Ensure that user is able to create appointment-@testcaseID=24057
    When user clicks on "<Schedule_Appointment>" link on appointment page
    And fill all the mandatory fields "<Reason_for_Appointment>","<ScheduleDate>","<Location>","<SharingOption>","<Attendees1>","<Attendees2>" to "<Action>" appointment
    And click on BookAppointment button
    Then user is able to see "<Success_message>" in the alert
    And user is able to view appointment on the page

    Examples:
      | Schedule_Appointment | Success_message                  | Reason_for_Appointment   | ScheduleDate | Location | SharingOption | Attendees1 | Attendees2   | Action |
      | Schedule Appointment | Appointment created successfully | Class attendance concern | current      | Noida    | public        | Kavitha    | Karthikeyani | Create |

  Scenario Outline: Ensure that user is able to edit the created appointment-@testcaseID=24058
    When user clicks on Edit Appointment text under Menu icon
    And fill all the mandatory fields "<Reason_for_Appointment>","<ScheduleDate>","<Location>","<SharingOption>","<Attendees1>","<Attendees2>" to "<Action>" appointment
    And click on BookAppointment button
    Then user is able to see "<Success_message>" in the alert
    And user is able to view the edited appointment on the page

    Examples:
      | Success_message                  | Reason_for_Appointment   | ScheduleDate | Location | SharingOption | Attendees1 | Attendees2   | Action |
      | Appointment updated successfully | Class attendance concern | current      | Noida    | public        | Kavitha    | Karthikeyani | Edit   |

  Scenario Outline: Ensure that user is able to cancel the appointment-@testcaseID=24059
    When user clicks on Cancel and Remove text under Menu icon
    Then user is able to see "<Success_message>" in the alert
    And user is not able to view the cancelled appointment on the page

    Examples:
      | Success_message                    |
      | Appointment cancelled successfully |

  Scenario Outline: Ensure that user is able to cancel the office hours-@testcaseID=24060
    When user clicks on "<Appointment_Tab>" tab
    And user clicks on "<Add_Officehrs>" link on appointment page
    And fill all the mandatory fields to "<Action>" one time office hr for "<Slot_Time>" time, "<Location>" location and "<CurrentDate>"
    And click on cancel button
    Then user is not able to view Office hr on the page

    Examples:
      | Appointment_Tab | Add_Officehrs    | Slot_Time | CurrentDate | Action |
      | Appointments    | Add Office Hours | 15 min    | current     | Create |

  Scenario Outline: Ensure that user is able to create one time office hour-@testcaseID=24061
    When user clicks on "<Appointment_Tab>" tab
    And user clicks on "<Add_Officehrs>" link on appointment page
    And fill all the mandatory fields to "<Action>" one time office hr for "<Slot_Time>" time, "<Location>" location and "<CurrentDate>"
    And click on save button
    Then user is able to see "<Success_message>" in the alert
    Then user is able to view Office hr on the page

    Examples:
      | Appointment_Tab | Add_Officehrs    | Slot_Time | CurrentDate | Success_message                | Action | Location |
      | Appointments    | Add Office Hours | 15 min    | current     | office Hour added successfully | Create | Boston   |


  Scenario Outline: Ensure that user is able to Book Appointment from one time office hour-@testcaseID=24062
    When user clicks BookIcon icon in front of the office hour
    And fill all the mandatory fields "<Reason_for_Appointment>","<ScheduleDate>","<Location>","<SharingOption>","<Attendees1>","<Attendees2>" to "<Action>" appointment
    And click on BookAppointment button
    Then user is able to see "<Success_message>" in the alert
    And user is able to view Office hr appointment on the page

    Examples:
      | Success_message                  | Reason_for_Appointment   | ScheduleDate | Location | SharingOption | Attendees1 | Attendees2   | Action                      |
      | Appointment created successfully | Class attendance concern | current      | Noida    | public        | Adam       | Karthikeyani | BookAppointmentFromOfficeHr |


  Scenario Outline: Ensure that user is able to Cancel Appointment from one time office hour-@testcaseID=24063
    When user clicks CancelIcon icon in front of the office hour
    And confirm its cancellation
    Then user is able to see "<Success_message>" in the alert
    And user is able to view Office hr on the page

    Examples:
      | Success_message                    |
      | Appointment cancelled successfully |

  Scenario Outline: Ensure that user is able to edit one time office hour-@testcaseID=24064
    When user clicks on "<Appointment_Tab>" tab
    When user clicks on Manage this slot text under Menu icon
    And fill all the mandatory fields to "<Action>" one time office hr for "<Slot_Time>" time, "<Location>" location and "<CurrentDate>"
    And click on save button
    Then user is able to see "<Success_message>" in the alert
    Then user is able to view edited Office hr on the page
    Examples:
      | Appointment_Tab | Add_Officehrs    | Slot_Time | CurrentDate | Success_message                  | Action | Location |
      | Appointments    | Add Office Hours | 15 min    | current     | Office Hour updated successfully | Edit   | Boston   |


  Scenario Outline: Ensure that user is able to remove office hr-@testcaseID=24065
    When user clicks Remove Office Hour text under Menu icon for "<TypeOfOfficeHr>"
    Then user is able to see "<Success_message>" in the alert
    And user is not able to view the cancelled one time office hr on the page

    Examples:
      | Success_message                    | TypeOfOfficeHr |
      | Office Hour cancelled successfully | onetime        |

  Scenario Outline: Ensure that user is able to create Series time office hour-@testcaseID=24066
    When user clicks on "<Appointment_Tab>" tab
    And user clicks on "<Add_Officehrs>" link on appointment page
    And fill all the mandatory fields to "<Action>" series office hr with values "<Slot_Time_Duration>", "<Location>", "<Repeat_frequency>", "<RepeatDays>","<IncludeSatSun>","<StartDate>","<EndBy>","<EndDate>","<EndAfter>",<EndAfterOccurence>
    And click on save button
    Then user is able to see "<Success_message>" in the alert
    And user is able to view Series Office hr on the page

    Examples:
      | Appointment_Tab | Add_Officehrs    | Slot_Time_Duration | Location | Repeat_frequency | RepeatDays | IncludeSatSun | StartDate | EndBy | EndDate | EndAfter | EndAfterOccurence | Action | Success_message                                     |
      | Appointments    | Add Office Hours | 15 min             | Meerut   | Daily            | 1          | true          | current   | true  | current | true     | 1                 | Create | Your office hours series has finished being created |


  Scenario Outline: Ensure that user is able to remove Series office hr-@testcaseID=24067
    When user clicks Remove Office Hour text under Menu icon for "<TypeOfOfficeHr>"
    Then user is able to see "<Success_message>" in the alert
    And user is not able to view the cancelled series office hr on the page

    Examples:
      | Success_message                    | TypeOfOfficeHr |
      | Office Hour cancelled successfully | Series         |

  Scenario Outline: Ensure that user is able to create a delegate user-@testcaseID=24068
    When user clicks on "<Appointment_Tab>" tab
    And user clicks on "<Manage_Delegates>" link on appointment page
    And add a faculty "<Faculty_Name>" as delegate
    Then user is able to see "<Success_message>" in the alert

    Examples:
      | Manage_Delegates | Success_message                  | Faculty_Name | Appointment_Tab |
      | Manage Delegates | Delegate is created successfully | qavishvajeet | Appointments    |
#
  Scenario Outline: Verify Faculty is able to view coordinator Agenda-@testcaseID=24069
    Given user is on skyfactor login page
    When user login into the application with "<UserName>" and "<Password>"
    And user clicks on "<Delegate_Access>" tab
    Then user is able to view "<Coordinator_Name>" on the page

    Examples:
      | UserName                    | Password | Delegate_Access | Coordinator_Name |
      | qavishvajeet@mailinator.com | Qait@123 | Delegate Access | qanupur          |

  Scenario Outline: Ensure that delegate user is able to create appointment-@testcaseID=24070
    When user clicks on "<Schedule_Appointment>" link on appointment page
    And fill all the mandatory fields "<Reason_for_Appointment>","<ScheduleDate>","<Location>","<SharingOption>","<Attendees1>","<Attendees2>" to "<Action>" appointment
    And click on BookAppointment button
    Then user is able to see "<Success_message>" in the alert
    And user is able to view appointment on the page

    Examples:
      | Schedule_Appointment | Success_message                  | Reason_for_Appointment   | ScheduleDate | Location | SharingOption | Attendees1 | Attendees2   | Action |
      | Schedule Appointment | Appointment created successfully | Class attendance concern | current      | Noida    | public        | Kavitha    | Karthikeyani | Create |

  Scenario Outline: Ensure that delegete user is able to edit the created appointment-@testcaseID=24071
    When user clicks on Edit Appointment text under Menu icon
    And fill all the mandatory fields "<Reason_for_Appointment>","<ScheduleDate>","<Location>","<SharingOption>","<Attendees1>","<Attendees2>" to "<Action>" appointment
    And click on BookAppointment button
    Then user is able to see "<Success_message>" in the alert
    And user is able to view the edited appointment on the page

    Examples:
      | Success_message                  | Reason_for_Appointment   | ScheduleDate | Location | SharingOption | Attendees1 | Attendees2   | Action |
      | Appointment updated successfully | Class attendance concern | current      | Noida    | public        | Kavitha    | Karthikeyani | Edit   |

  Scenario Outline: Ensure that Delegate user is able to cancel the appointment-@testcaseID=24072
    When user clicks on Cancel and Remove text under Menu icon
    Then user is able to see "<Success_message>" in the alert
    And user is not able to view the cancelled appointment on the page

    Examples:
      | Success_message                    |
      | Appointment cancelled successfully |

  Scenario Outline: Ensure that Delegate user is able to create one time office hour-@testcaseID=24073
    And user clicks on "<Add_Officehrs>" link on appointment page
    And fill all the mandatory fields to "<Action>" one time office hr for "<Slot_Time>" time, "<Location>" location and "<CurrentDate>"
    And click on save button
    Then user is able to see "<Success_message>" in the alert
    Then user is able to view Office hr on the page

    Examples:
      | Appointment_Tab | Add_Officehrs    | Slot_Time | CurrentDate | Success_message                | Action | Location |
      | Appointments    | Add Office Hours | 15 min    | current     | office Hour added successfully | Create | Boston   |

  Scenario Outline: Ensure that Delegate user is able to Book Appointment from one time office hour-@testcaseID=24074
    When user clicks BookIcon icon in front of the office hour
    And fill all the mandatory fields "<Reason_for_Appointment>","<ScheduleDate>","<Location>","<SharingOption>","<Attendees1>","<Attendees2>" to "<Action>" appointment
    And click on BookAppointment button
    Then user is able to see "<Success_message>" in the alert
    And user is able to view Office hr appointment on the page

    Examples:
      | Success_message                  | Reason_for_Appointment   | ScheduleDate | Location | SharingOption | Attendees1 | Attendees2   | Action                      |
      | Appointment created successfully | Class attendance concern | current      | Noida    | public        | Adam       | Karthikeyani | BookAppointmentFromOfficeHr |


  Scenario Outline: Ensure that Delegate user is able to Cancel Appointment from one time office hour-@testcaseID=24075
    When user clicks CancelIcon icon in front of the office hour
    And confirm its cancellation
    Then user is able to see "<Success_message>" in the alert
    And user is able to view Office hr on the page

    Examples:
      | Success_message                    |
      | Appointment cancelled successfully |

  Scenario Outline: Ensure that Delegate user is able to edit one time office hour-@testcaseID=24076
    When user clicks on Manage this slot text under Menu icon
    And fill all the mandatory fields to "<Action>" one time office hr for "<Slot_Time>" time, "<Location>" location and "<CurrentDate>"
    And click on save button
    Then user is able to see "<Success_message>" in the alert
    Then user is able to view edited Office hr on the page

    Examples:
      | Appointment_Tab | Add_Officehrs    | Slot_Time | CurrentDate | Success_message                  | Action | Location |
      | Appointments    | Add Office Hours | 15 min    | current     | Office Hour updated successfully | Edit   | Boston   |


  Scenario Outline: Ensure that Delegate user is able to remove office hr-@testcaseID=24077
    When user clicks Remove Office Hour text under Menu icon for "<TypeOfOfficeHr>"
    Then user is able to see "<Success_message>" in the alert
    And user is not able to view the cancelled one time office hr on the page

    Examples:
      | Success_message                    | TypeOfOfficeHr |
      | Office Hour cancelled successfully | Onetime        |



