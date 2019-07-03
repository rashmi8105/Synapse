<?php

require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';

class StaffActionsDeletedGroupCest extends SynapseRestfulTestBase
{
    private $orgId = 542;





    // Sprout for which the test takes place
    private $Sprout = [
        'email' => 'pomona.sprout@mailinator.com',
        'password' => 'password1!',
        'id' => 99443,
        'orgId' => 542,
        'langId' => 1
    ];

    private $coordinator = [
        'email' => 'albus.dumbledore@mailinator.com',
        'password' => 'password1!',
        'id' => 99704,
        'orgId' => 542,
        'langId' => 1
    ];

    private $Cedric = [
        'student_id' => 99413,
        'student_first_name' => 'Cedric ',
        'student_last_name' => 'Diggory',
        'primary_email' => 'cedric.diggory@mailinator.com'

    ];


    private $studentInSearch = [
        'user_id' => 99413,
        'student_id' => 'S004',
        'user_firstname' => 'Cedric ',
        'user_lastname' => 'Diggory',
        'user_email' => 'cedric.diggory@mailinator.com',
        'student_status' => '1'
    ];

    private $student = [
        'id' => 99413,
        'student_external_id' => 'S004',
        'student_first_name' => 'Cedric ',
        'student_last_name' => 'Diggory',
        'primary_email' => 'cedric.diggory@mailinator.com',
        //'last_viewed_date' => '07/01/2015',
        'student_status' => '1',
        'risk_indicator_access' => true,     // true in data returned...
        'intent_to_leave_access' => true,
    ];


    private $getActivityStreamCedric = [
        'student-id' => 99413,
        'category' => 'all',
        'is-interaction' => false
    ];

    private $getStudentNotesCedric = [
        'student-id' => 99413,
        'category' => 'note',
        'is-interaction' => false
    ];

    //Features For View
    private $publicNoteToView = [
        'organization_id' => 542,
        'notes_id' => 228,
        'notes_student_id' => 99413,
        'staff_id' => 99443
    ];

    private $privateNoteToView = [
        'organization_id' => 542,
        'notes_id' => 227,
        'notes_student_id' => 99413,
        'staff_id' => 99443
    ];

    private $publicContactToView = [
        'organization_id' => 542,
        'contact_id' => 267,
        'person_student_id' => 99413,
        'person_staff_id' => 99443
    ];

    private $privateContactToView = [
        'organization_id' => 542,
        'contact_id' => 266,
        'person_student_id' => 99413,
        'person_staff_id' => 99443
    ];


    private $appointmentToView = [
        'organization_id' => '542',
        'appointment_id' => 990,
        'person_id' => '99443'
    ];

    private $publicReferralToView = [
        'organization_id' => 542,
        'referral_id' => 688,
        'person_student_id' => 99413,
        'person_staff_id' => 99443
    ];

    private $privateReferralToView = [
        'organization_id' => 542,
        'referral_id' => 687,
        'person_student_id' => 99413,
        'person_staff_id' => 99443
    ];



    //FOR CREATION FEATURES
    private $publicNote = [
        'organization_id' => 542,
        'staff_id' => 99443,
        'notes_student_id' => 99413,
        'comment'=> "Public note created by Pomona",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateNote = [
        'organization_id' => 542,
        'staff_id' => 99443,
        'notes_student_id' => 99413,
        'comment'=> "Private note created by Pomona",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicContact = [
        'organization_id' => 542,
        'person_student_id' => 99413,
        'person_staff_id' => 99443,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateContact = [
        'organization_id' => 542,
        'person_student_id' => 99413,
        'person_staff_id' => 99443,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $appointment = [
        'organization_id' => 542,
        'person_id' => 99443,
        'attendees' => [['student_id' => 99413]],
        'location' => 'Charms Classroom',
        'detail_id' => 30,
        'slot_start' => '2015-05-23 09:00:00',
        'slot_end' => '2015-05-23 10:00:00',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicReferral = [
        'organization_id' => 542,
        'person_student_id' => 99413,
        'person_staff_id' => 99443,
        'assigned_to_user_id' => 99443,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateReferral = [
        'organization_id' => 542,
        'person_student_id' => 99413,
        'person_staff_id' => 99443,
        'assigned_to_user_id' => 99443,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
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
     * 2. Delete Group
     * 3. Retry baseline test, should fail.
     *
     *
     *
     */





    public function testStaffAccessToStudentProfileItemsInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting the connection with the student profile to end');

        $this->_getAPITestRunner($I, $this->Sprout, 'students/' . $this->Cedric['student_id'] . '/studentdetails', [], 200, []);
        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);
        $this->_getAPITestRunner($I, $this->Sprout, 'students/' . $this->Cedric['student_id'] . '/studentdetails', [], 403, []);

    }

    public function testStaffViewRiskIndicatorIntentToLeaveInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting the connection with the student risk indicator and intent to leave flag to end');
        $this->_getAPITestRunner($I, $this->Sprout, 'students/' . $this->Cedric['student_id'], [], null, ['data' => $this->student]);
        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);
        $this->_getAPITestRunner($I, $this->Sprout, 'students/' . $this->Cedric['student_id'], [], null, []);
        $I->dontSeeResponseContains('"student_risk_status":');
        $I->dontSeeResponseContains('"student_intent_to_leave":');
    }

    public function testStaffSearchForStudentInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting the connection with the student to end on Search.');
        $testData = [
            ['organization_id' => 542],
            $this->studentInSearch
        ];
        $this->_getAPITestRunner($I, $this->Sprout,
            'permission/' . $this->orgId . '/mystudents?personId=' . $this->Sprout['id'],
            [], 200, $testData);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);

        $this->_getAPITestRunner($I, $this->Sprout,
            'permission/' . $this->orgId . '/mystudents?personId=' . $this->Sprout['id'],
            [], null, ["data"=>["organization_id"=>542,"users"=>[]]]);
    }

    public function testStaffViewStudentProfileInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting the connection with student profile.');
        $this->_getAPITestRunner($I, $this->Sprout, 'students/' . $this->Cedric['student_id'], [], 200, []);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);

        $this->_getAPITestRunner($I, $this->Sprout, 'students/' . $this->Cedric['student_id'], [], 403, []);
    }


    /*
    public function testStaffViewTalkingPointsForStudentInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('Staff views talking points for a student in my group with minimum access.');
        $this->_getAPITestRunner($I, $this->Sprout, 'students/'.$this->student['id'].'/talking_point', null, 403, [['data' => []]]);
    }
    */

    //FEATURES

    public function testStaffViewActivityStreamForAccessibleStudentInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting the connection with activity stream to end if no students');

        $this->_getAPITestRunner($I, $this->Sprout, 'students/activity', $this->getActivityStreamCedric, 200, []);
        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);


        $this->_getAPITestRunner($I, $this->Sprout, 'students/activity', $this->getActivityStreamCedric, 403, []);
    }

    public function testStaffViewAllNotesForAccessibleStudentInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting the connection with notes list to end if no students');
        $this->_getAPITestRunner($I, $this->Sprout, 'students/activity', $this->getStudentNotesCedric, 200, []);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);

        $this->_getAPITestRunner($I, $this->Sprout, 'students/activity', $this->getStudentNotesCedric, 403, []);
    }

    public function testStaffViewAllContactsForAccessibleStudentInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting the connection with notes list to end if no students');
        $this->_getAPITestRunner($I, $this->Sprout, 'students/' . $this->Cedric['student_id'] . '/contacts', [], 200, []);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);

        $this->_getAPITestRunner($I, $this->Sprout, 'students/' . $this->Cedric['student_id'] . '/contacts', [], 403, []);
    }

    public function testStaffViewAllAppointmentsForAccessibleStudentInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting the connection with notes list to end if no students');
        $this->_getAPITestRunner($I, $this->Sprout, 'students/' . $this->Cedric['student_id'] . '/appointments', [], 200, []);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);


        $this->_getAPITestRunner($I, $this->Sprout, 'students/' . $this->Cedric['student_id'] . '/appointments', [], 403, []);
    }

    public function testStaffViewAllReferralsForAccessibleStudentInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting the connection with referrals list to end if no students');
        $this->_getAPITestRunner($I, $this->Sprout, 'students/' . $this->Cedric['student_id'] . '/referrals', [], 200, []);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);

        $this->_getAPITestRunner($I, $this->Sprout, 'students/' . $this->Cedric['student_id'] . '/referrals', [], 403, []);
    }

    public function testStaffCreatePublicNoteInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting to lose ability to create public note for former student');

        $this->_postAPITestRunner($I, $this->Sprout, 'notes', $this->publicNote, 201, []);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);
        $this->_postAPITestRunner($I, $this->Sprout, 'notes', $this->publicNote, 403, []);
    }

    public function testStaffCreatePrivateNoteInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting to lose ability to create private note for former student');

        $this->_postAPITestRunner($I, $this->Sprout, 'notes', $this->privateNote, 201, []);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);
        $this->_postAPITestRunner($I, $this->Sprout, 'notes', $this->privateNote, 403, []);
    }

    /*
    public function testStaffCreateTeamNoteMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a team note for a student with minimum access.'');
        $this->_postAPITestRunner($I, $this->Sprout, 'notes', $this->teamNote, 201, [$this->teamNote]);
    }*/

    public function testStaffCreatePublicContactInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting to lose ability to create public contact for former student');

        $this->_postAPITestRunner($I, $this->Sprout, 'contacts', $this->publicContact, 201, []);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);
        $this->_postAPITestRunner($I, $this->Sprout, 'contacts', $this->publicContact, 403, []);
    }

    public function testStaffCreatePrivateContactInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting to lose ability to create private contact for former student');

        $this->_postAPITestRunner($I, $this->Sprout, 'contacts', $this->privateContact, 201, []);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);
        $this->_postAPITestRunner($I, $this->Sprout, 'contacts', $this->privateContact, 403, []);
    }

    /*
    public function testStaffCreateTeamContactMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a team contact for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->Sprout, 'contacts', $this->teamContact, 201, [$this->teamContact]);
    }*/

    public function testStaffCreateAppointmentInDeletedGroup(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('If a group is deleted, expecting to lose ability to create appointment for former student');
        $params = array_merge($this->appointment, ['office_hours_id' => null]);
        $this->_postAPITestRunner($I, $this->Sprout, 'appointments/' . $this->orgId . '/' . $this->Sprout['id'], $params, 201, []);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);

        $this->_postAPITestRunner($I, $this->Sprout, 'appointments/' . $this->orgId . '/' . $this->Sprout['id'], $params, 403, []);
    }

    public function testStaffCreatePublicReferralInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting to lose ability to create public referral for former student');

        $this->_postAPITestRunner($I, $this->Sprout, 'referrals', $this->publicReferral, 201, []);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);
        $this->_postAPITestRunner($I, $this->Sprout, 'referrals', $this->publicReferral, 403, []);
    }

    public function testStaffCreatePrivateReferralInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting to lose ability to create private referral for former student');

        $this->_postAPITestRunner($I, $this->Sprout, 'referrals', $this->privateReferral, 201, []);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);
        $this->_postAPITestRunner($I, $this->Sprout, 'referrals', $this->privateReferral, 403, []);
    }

    /*
    public function testStaffCreateTeamReferralMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a team referral for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->Sprout, 'referrals', $this->teamReferral, 201, [$this->teamReferral]);
    }*/

    public function testStaffFromSameGroupViewPrivateContactInDeletedGroup(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('If a group is deleted, expecting to lose ability to view private contact for former student');

        $this->_getAPITestRunner($I, $this->Sprout, 'contacts/'. $this->privateContactToView['contact_id'], [], 200, [$this->privateContactToView]);
        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);
        $this->_getAPITestRunner($I, $this->Sprout, 'contacts/'. $this->privateContactToView['contact_id'], [], 403, []);
    }

    public function testStaffFromSameGroupViewPublicContactInDeletedGroup(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('If a group is deleted, expecting to lose ability to view public contact for former student');

        $this->_getAPITestRunner($I, $this->Sprout, 'contacts/'. $this->publicContactToView['contact_id'], [], 200, [$this->publicContactToView]);
        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);
        $this->_getAPITestRunner($I, $this->Sprout, 'contacts/'. $this->publicContactToView['contact_id'], [], 403, []);
    }

    public function testStaffFromSameGroupViewAppointmentInDeletedGroup(ApiAuthTester $I)
    {
        $I->wantTo('If a group is deleted, expecting to lose ability to view appointment for former student');

        $this->_getAPITestRunner($I, $this->Sprout,
            'appointments/'.$this->orgId.'/'.$this->Sprout['id'].'/appointmentId?appointmentId=990',
            [], 200, [$this->appointmentToView]);

        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);

        $this->_getAPITestRunner($I, $this->Sprout,
            'appointments/'.$this->orgId.'/'.$this->Sprout['id'].'/appointmentId?appointmentId=990',
            [], 403, []);
    }

    public function testStaffFromSameGroupViewPublicReferralInDeletedGroup(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('If a group is deleted, expecting to lose ability to view public referral for former student');
        $this->_getAPITestRunner($I, $this->Sprout, 'referrals/'. $this->publicReferralToView['referral_id'], [], 200, [$this->publicReferralToView]);
        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);
        $this->_getAPITestRunner($I, $this->Sprout, 'referrals/'. $this->publicReferralToView['referral_id'], [], 403, []);
    }

    public function testStaffFromSameGroupViewPrivateReferralInDeletedGroup(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('If a group is deleted, expecting to lose ability to view private referral for former student');
        $this->_getAPITestRunner($I, $this->Sprout, 'referrals/'. $this->privateReferralToView['referral_id'], [], 200, [$this->privateReferralToView]);
        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);
        $this->_getAPITestRunner($I, $this->Sprout, 'referrals/'. $this->privateReferralToView['referral_id'], [], 403, []);
    }

    public function testStaffFromSameGroupViewPrivateNoteInDeletedGroup(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('If a group is deleted, expecting to lose ability to view private note for former student');
        $this->_getAPITestRunner($I, $this->Sprout, 'notes/'. $this->privateNoteToView['notes_id'], [], 200, [$this->privateNoteToView]);
        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);
        $this->_getAPITestRunner($I, $this->Sprout, 'notes/'. $this->privateNoteToView['notes_id'], [], 403, []);
    }

    public function testStaffFromSameGroupViewPublicNoteInDeletedGroup(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('If a group is deleted, expecting to lose ability to view public note for former student');
        $this->_getAPITestRunner($I, $this->Sprout, 'notes/'. $this->publicNoteToView['notes_id'], [], 200, [$this->publicNoteToView]);
        //Delete API should be returning 204, but 200 is used here
        $this->_deleteAPITestRunner($I, $this->coordinator, 'groups/1265', [], 200, []);
        $this->_getAPITestRunner($I, $this->Sprout, 'notes/'. $this->publicNoteToView['notes_id'], [], 403, []);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
    
    
    
    
    
    
}
