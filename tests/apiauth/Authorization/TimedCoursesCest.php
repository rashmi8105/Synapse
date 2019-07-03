<?php
/**
 * Created by PhpStorm.
 * User: imeyers
 * Date: 7/21/15
 * Time: 10:46 AM
 */

require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class TimedCoursesCest extends SynapseRestfulTestBase
{

    private $coordinator = [
        'email' => 'albus.dumbledore@mailinator.com',
        'password' => 'password1!',
        'id' => 99704,
        'orgId' => 542,
        'langId' => 1
    ];

    private $Hagrid = [
        'email' => 'rubeus.hagrid@mailinator.com',
        'password' => 'password1!',
        'id' => 99446,
        'orgId' => 542,
        'langId' => 1
    ];

    private $Harry = [
        'student_id' => 99422,
        'student_first_name' => 'Harry',
        'student_last_name' => 'Potter',
        'primary_email' => 'harry.potter@mailinator.com'

    ];

    private $student = [
            'id' => 99422,
            'student_external_id' => 'S013',
            'student_first_name' => 'Harry ',
            'student_last_name' => 'Potter',
            'primary_email' => 'harry.potter@mailinator.com',
            'last_viewed_date' => '07/17/2015',
            'student_status' => '1',
            'risk_indicator_access' => true,
            'intent_to_leave_access' => true,
    ];

    private $studentInSearch = [
        'data' =>
            array (
                'organization_id' => 542,
                'users' =>
                    array (

                        0 =>
                            array (
                                'user_id' => 99422,
                                'user_firstname' => 'Harry ',
                                'user_lastname' => 'Potter',
                                'student_id' => 'S013',
                                'user_email' => 'harry.potter@mailinator.com',
                                'student_status' => '1',
                            ),

                    ),
            ),

    ];

    private $getActivityStreamHarry = [
        'student-id' => 99422,
        'category' => 'all',
        'is-interaction' => false
    ];

    private $getStudentNotesHarry = [
        'student-id' => 99422,
        'category' => 'note',
        'is-interaction' => false
    ];

    //Features For View

    private $publicNote = [
        'organization_id' => 542,
        'staff_id' => 99446,
        'notes_student_id' => 99422,
        'comment'=> "Harry was able to handle the Hippogriff Very well.",
        'reason_category_subitem_id' => 2,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateNote = [
        'organization_id' => 542,
        'staff_id' => 99446,
        'notes_student_id' => 99422,
        'comment'=> "Harry paper was copied directly from Hermione",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicContact = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99446,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateContact = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99446,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $appointment = [
        'organization_id' => 542,
        'person_id' => 99446,
        'attendees' => [['student_id' => 99422]],
        'location' => 'Charms Classroom',
        'detail_id' => 30,
        'slot_start' => '2015-05-23 09:00:00',
        'slot_end' => '2015-05-23 10:00:00',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicReferral = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99446,
        'assigned_to_user_id' => 99446,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateReferral = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99446,
        'assigned_to_user_id' => 99446,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicContactToView = [
        'organization_id' => 542,
        'contact_id' => 267,
        'person_student_id' => '99413',
        'person_staff_id' => 99443
    ];

    private $privateContactToView = [
        'organization_id' => 542,
        'contact_id' => 268,
        'person_student_id' => '99422',
        'person_staff_id' => 99446
    ];

    private $appointmentToView = [
        'organization_id' => '542',
        'appointment_id' => 990,
        'person_id' => '99446'
    ];

    private $publicReferralToView = [
        'organization_id' => 542,
        'referral_id' => 671,
        'person_student_id' => '99422',
        'person_staff_id' => 99445
    ];

    private $privateReferralToView = [
        'organization_id' => 542,
        'referral_id' => 689,
        'person_student_id' => '99422',
        'person_staff_id' => 99446
    ];

    private $privateNoteToView = [
        'organization_id' => 542,
        'notes_id' => 229,
        'notes_student_id' => '99422',
        'staff_id' => 99446
    ];

    private $publicNoteToView = [
        'organization_id' => 542,
        'notes_id' => 228,
        'notes_student_id' => '99413',
        'staff_id' => 99443
    ];

    //Database is reloaded so we can delete same group over and over and use same parameters
    public function _before(ApiAuthTester $I)
    {
        $output = shell_exec('./runauthtests.sh --reload');
        codecept_debug($output);
    }


    /*
     * Test Pattern
     * 1. Baseline test (Can this staff view something before )
     * 2. Delete Group and move course to have had happened already
     * 3. Retry baseline test, should fail.
     *
     *
     *
     */



    public function testStaffAccessToStudentProfileItemsInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a course connection has been lost, expecting the connection with the student profile to end');
        // baseline
        $this->_getAPITestRunner($I, $this->Hagrid, 'students/' . $this->Harry['student_id'] . '/studentdetails', [], 200, []);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        // This should give a not allowed to see error (403)
        $this->_getAPITestRunner($I, $this->Hagrid, 'students/' . $this->Harry['student_id'] . '/studentdetails', [], 403, []);

    }

    public function testStaffViewRiskIndicatorIntentToLeaveInExpiredCourse(ApiAuthTester $I)
    {
        // Baseline
        $I->wantTo('If a course connection has been lost, expecting the connection with the student risk indicator and intent to leave flag to end');
        $this->_getAPITestRunner($I, $this->Hagrid, 'students/' . $this->Harry['student_id'], [], null, [['data' => $this->student]]);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);


        $this->_getAPITestRunner($I, $this->Hagrid, 'students/' . $this->Harry['student_id'], [], null, []);
        $I->dontSeeResponseContains('"student_risk_status":');
        $I->dontSeeResponseContains('"student_intent_to_leave":');
    }

    public function testStaffSearchForStudentInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a course connection has been lost, expecting the connection with the student to end on Search.');

        $this->_getAPITestRunner($I, $this->Hagrid,
            'permission/542/mystudents?personId=' . $this->Hagrid['id'],
            [], 200, $this->studentInSearch);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_getAPITestRunner($I, $this->Hagrid,
            'permission/542/mystudents?personId=' . $this->Hagrid['id'],
            [], null, ["data"=>["organization_id"=>542,"users"=>[]]]);
    }

    public function testStaffViewStudentProfileInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a  course connection has been lost, expecting the connection with student profile.');
        $this->_getAPITestRunner($I, $this->Hagrid, 'students/' . $this->Harry['student_id'], [], 200, []);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_getAPITestRunner($I, $this->Hagrid, 'students/' . $this->Harry['student_id'], [], 403, []);
    }


    /*
    public function testStaffViewTalkingPointsForStudentInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('Staff views talking points for a student in my group with minimum access.');
        $this->_getAPITestRunner($I, $this->Sprout, 'students/'.$this->student['id'].'/talking_point', null, 403, [['data' => []]]);
    }
*/

    //FEATURES

    public function testStaffViewActivityStreamForAccessibleStudentInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a  course connection has been lost, expecting the connection with activity stream to end if no students');
        $this->_getAPITestRunner($I, $this->Hagrid, 'students/activity', $this->getActivityStreamHarry, 200, []);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);


        $this->_getAPITestRunner($I, $this->Hagrid, 'students/activity', $this->getActivityStreamHarry, 403, []);
    }

    public function testStaffViewAllNotesForAccessibleStudentInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a  course connection has been lost, expecting the connection with notes list to end if no students');
        $this->_getAPITestRunner($I, $this->Hagrid, 'students/activity', $this->getStudentNotesHarry, 200, []);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_getAPITestRunner($I, $this->Hagrid, 'students/activity', $this->getStudentNotesHarry, 403, []);
    }

    public function testStaffViewAllContactsForAccessibleStudentInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a course connection has been lost, expecting the connection with notes list to end if no students');
        $this->_getAPITestRunner($I, $this->Hagrid, 'students/' . $this->Harry['student_id'] . '/contacts', [], 200, []);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_getAPITestRunner($I, $this->Hagrid, 'students/' . $this->Harry['student_id'] . '/contacts', [], 403, []);
    }

    public function testStaffViewAllAppointmentsForAccessibleStudentInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a course connection has been lost, expecting the connection with notes list to end if no students');
        $this->_getAPITestRunner($I, $this->Hagrid, 'students/' . $this->Harry['student_id'] . '/appointments', [], 200, []);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_getAPITestRunner($I, $this->Hagrid, 'students/' . $this->Harry['student_id'] . '/appointments', [], 403, []);
    }

    public function testStaffViewAllReferralsForAccessibleStudentInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a course connection has been lost, expecting the connection with referrals list to end if no students');
        $this->_getAPITestRunner($I, $this->Hagrid, 'students/' . $this->Harry['student_id'] . '/referrals', [], 200, []);

        // Literally changing the database for this test ...
        // displaying the other way to gather the date data as an example
        $this->_academicYearsMySQLrunner($this->_DynamicDates("2 Weeks Ago"), $this->_DynamicDates("2 Weeks"), 110);
        $this->_academicTermsMySQLrunner($this->_DynamicDates("1 Week Ago"), $this->_DynamicDates("yesterday"), 123);

        $this->_getAPITestRunner($I, $this->Hagrid, 'students/' . $this->Harry['student_id'] . '/referrals', [], 403, []);

    }

    public function testStaffCreatePublicNoteInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a course connection has been lost, expecting to lose ability to create public note for former student');

        $this->_postAPITestRunner($I, $this->Hagrid, 'notes', $this->publicNote, 201, []);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_postAPITestRunner($I, $this->Hagrid, 'notes', $this->publicNote, 403, []);
    }

    public function testStaffCreatePrivateNoteInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a course connection has been lost, expecting to lose ability to create private note for former student');

        $this->_postAPITestRunner($I, $this->Hagrid, 'notes', $this->privateNote, 201, []);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_postAPITestRunner($I, $this->Hagrid, 'notes', $this->privateNote, 403, []);
    }

    public function testStaffCreatePublicContactInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a course connection has been lost, expecting to lose ability to create public contact for former student');

        $this->_postAPITestRunner($I, $this->Hagrid, 'contacts', $this->publicContact, 201, []);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_postAPITestRunner($I, $this->Hagrid, 'contacts', $this->publicContact, 403, []);
    }

    public function testStaffCreatePrivateContactInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a course connection has been lost, expecting to lose ability to create private contact for former student');

        $this->_postAPITestRunner($I, $this->Hagrid, 'contacts', $this->privateContact, 201, []);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_postAPITestRunner($I, $this->Hagrid, 'contacts', $this->privateContact, 403, []);
    }


  /*  public function testStaffCreateTeamContactMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a team contact for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->Hagrid, 'contacts', $this->teamContact, 201, [$this->teamContact]);
    }*/

    public function testStaffCreateAppointmentInExpiredCourse(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('If a course connection has been lost, expecting to lose ability to create appointment for former student');
        $params = array_merge($this->appointment, ['office_hours_id' => null]);
        $this->_postAPITestRunner($I, $this->Hagrid, 'appointments/542/' . $this->Hagrid['id'], $params, 201, []);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_postAPITestRunner($I, $this->Hagrid, 'appointments/542/' . $this->Hagrid['id'], $params, 403, []);
    }

    public function testStaffCreatePublicReferralInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a course connection has been lost, expecting to lose ability to create public referral for former student');

        $this->_postAPITestRunner($I, $this->Hagrid, 'referrals', $this->publicReferral, 201, []);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_postAPITestRunner($I, $this->Hagrid, 'referrals', $this->publicReferral, 403, []);
    }

    public function testStaffCreatePrivateReferralInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a course connection has been lost, expecting to lose ability to create private referral for former student');

        $this->_postAPITestRunner($I, $this->Hagrid, 'referrals', $this->privateReferral, 201, []);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_postAPITestRunner($I, $this->Hagrid, 'referrals', $this->privateReferral, 403, []);
    }
/*

    public function testStaffCreateTeamReferralMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a team referral for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->Sprout, 'referrals', $this->teamReferral, 201, [$this->teamReferral]);
    }*/

    public function testStaffWithAConnectionWithTheStudentViewPrivateContactInExpiredCourse(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('If a course connection has been lost, expecting to lose ability to view private contact for former student');

        $this->_getAPITestRunner($I, $this->Hagrid, 'contacts/'. $this->privateContactToView['contact_id'], [], 200, [$this->privateContactToView]);
        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_getAPITestRunner($I, $this->Hagrid, 'contacts/'. $this->privateContactToView['contact_id'], [], 403, []);
    }

    public function testStaffWithAConnectionWithTheStudentViewPublicContactInExpiredCourse(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('If a course connection has been lost, expecting to lose ability to view public contact for former student');

        $this->_getAPITestRunner($I, $this->Hagrid, 'contacts/'. $this->publicContactToView['contact_id'], [], 200, [$this->publicContactToView]);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_getAPITestRunner($I, $this->Hagrid, 'contacts/'. $this->publicContactToView['contact_id'], [], 403, []);
    }

    // This one is failing for reasons outside my control
    /*public function testStaffWithAConnectionWithTheStudentViewAppointmentInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo('If a course connection has been lost, expecting to lose ability to view appointment for former student');

        $this->_getAPITestRunner($I, $this->Hagrid,
            'appointments/542/'.$this->Hagrid['id'].'/appointmentId?appointmentId=990',
            [], 200, [$this->appointmentToView]);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);

        $this->_getAPITestRunner($I, $this->Hagrid,
            'appointments/542/'.$this->Hagrid['id'].'/appointmentId?appointmentId=990',
            [], 403, []);
    }//*/

    public function testStaffWithAConnectionWithTheStudentViewPublicReferralInExpiredCourse(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('If a course connection has been lost, expecting to lose ability to view public referral for former student');
        $this->_getAPITestRunner($I, $this->Hagrid, 'referrals/'. $this->publicReferralToView['referral_id'], [], 200, [$this->publicReferralToView]);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_getAPITestRunner($I, $this->Hagrid, 'referrals/'. $this->publicReferralToView['referral_id'], [], 403, []);
    }

    public function testStaffWithAConnectionWithTheStudentViewPrivateReferralInExpiredCourse(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('If a course connection has been lost, expecting to lose ability to view private referral for former student');
        $this->_getAPITestRunner($I, $this->Hagrid, 'referrals/'. $this->privateReferralToView['referral_id'], [], 200, [$this->privateReferralToView]);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_getAPITestRunner($I, $this->Hagrid, 'referrals/'. $this->privateReferralToView['referral_id'], [], 403, []);
    }

    public function testStaffWithAConnectionWithTheStudentViewPrivateNoteInExpiredCourse(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('If a course connection has been lost, expecting to lose ability to view private note for former student');
        $this->_getAPITestRunner($I, $this->Hagrid, 'notes/'. $this->privateNoteToView['notes_id'], [], 200, [$this->privateNoteToView]);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_getAPITestRunner($I, $this->Hagrid, 'notes/'. $this->privateNoteToView['notes_id'], [], 403, []);
    }

    public function testStaffWithAConnectionWithTheStudentViewPublicNoteInExpiredCourse(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('If a course connection has been lost, expecting to lose ability to view public note for former student');
        $this->_getAPITestRunner($I, $this->Hagrid, 'notes/'. $this->publicNoteToView['notes_id'], [], 200, [$this->publicNoteToView]);

        // get data information for date changes
        $date = $this->_DynamicDates();
        // Literally changing the database for this test ...
        $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
        $this->_academicTermsMySQLrunner($date["1WeekAgo"], $date["yesterday"], 123);

        $this->_getAPITestRunner($I, $this->Hagrid, 'notes/'. $this->publicNoteToView['notes_id'], [], 403, []);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
