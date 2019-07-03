<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class minimumAccessOnlyCest extends SynapseRestfulTestBase
{

    //ACTIONS DATA
    private $orgId = 542;

    // This staff member has the permission set Hogwarts_MinimumAccess

    private $minimumAccessOnlyStaff = [
        'email' => 'remus.lupin@mailinator.com',
        'password' => 'password1!',
        'id' => 99710,
        'orgId' => 542,
        'langId' => 1
    ];

    // A student in Dumbledore's Army (where Remus has min access)
    private $studentInSearch = [
        'user_id' => 99429,
        'student_id' => 'S020',
        'user_firstname' => 'Neville ',
        'user_lastname' => 'Longbottom',
        'user_email' => 'neville.longbottom@mailinator.com',
        'student_status' => '1'
    ];

    private $student = [
        'id' => 99429,
        'student_external_id' => 'S020',
        'student_first_name' => 'Neville ',
        'student_last_name' => 'Longbottom',
        'primary_email' => 'neville.longbottom@mailinator.com',
        'student_status' => '1',
        'risk_indicator_access' => false,
        'intent_to_leave_access' => false
    ];

    private $studentWithProfileItems = [
        'id' => 99422
    ];

    private $viewStudentProfileResponse = [
        'errors' => [
            0 => [
                'code' => '403',
                'user_message' => 'Access Denied',
                'developer_message' => '',
                'info' => []
            ]
        ]
    ];

    private $viewProfileItemsForStudentResponse =[
        'errors' => [
            0 => [
                'code' => '403',
                'user_message' => 'Access Denied',
                'developer_message' => '',
                'info' => []
            ]
        ]
    ];

    //FEATURES DATA
    // For viewing a list of all notes/contacts/appointments/referrals for the student.
    private $getStudentNotes = [
        'student-id' => 99429,
        'category' => 'note',
        'is-interaction' => false
    ];

    private $getActivityStream = [
        'student-id' => 99429,
        'category' => 'all',
        'is-interaction' => false
    ];

    private $publicNote = [
        'organization_id' => 542,
        'staff_id' => 99710,
        'notes_student_id' => 99429,
        'comment'=> "Public note created by Remus Lupin",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateNote = [
        'organization_id' => 542,
        'staff_id' => 99710,
        'notes_student_id' => 99429,
        'comment'=> "Private note created by Remus Lupin",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicContact = [
        'organization_id' => 542,
        'person_student_id' => 99429,
        'person_staff_id' => 99710,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateContact = [
        'organization_id' => 542,
        'person_student_id' => 99429,
        'person_staff_id' => 99710,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test private contact',
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $appointment = [
        'organization_id' => 542,
        'person_id' => 99710,
        'attendees' => [['student_id' => 99429]],
        'location' => 'Charms Classroom',
        'detail_id' => 30,
        'slot_start' => '2015-05-23 09:00:00',
        'slot_end' => '2015-05-23 10:00:00'
    ];

    private $publicReferral = [
        'organization_id' => 542,
        'person_student_id' => 99429,
        'person_staff_id' => 99710,
        'assigned_to_user_id' => 99710,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateReferral = [
        'organization_id' => 542,
        'person_student_id' => 99429,
        'person_staff_id' => 99710,
        'assigned_to_user_id' => 99710,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];


    //ACTIONS
    public function testStaffSearchForStudent(ApiAuthTester $I)
    {
        $I->wantTo('Search for student information for a student in my group.');
        $testData = [
            ['organization_id' => 542],
            $this->studentInSearch
        ];
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff,
            'permission/'.$this->orgId.'/mystudents?personId='.$this->minimumAccessOnlyStaff['id'],
            [], 200, $testData);
    }

    public function testStaffViewStudentProfile(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View student information of a student in my group.');
        // faculty having access to the student
        $testData = [
        'id' => 99429,
        'student_external_id' => 'S020',
        'student_first_name' => 'Neville ',
        'student_last_name' => 'Longbottom',
        'primary_email' => 'neville.longbottom@mailinator.com',
        'student_risk_status' => 'gray',
        'student_intent_to_leave' => 'dark gray',
        'student_status' => '1',
        'risk_indicator_access' => false,
        'intent_to_leave_access' => false
        ];
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff, 'students/'.$this->student['id'], [], 200, [$testData]);
    }

    /*
    // Talking points are not specifically in the permission sets, so we don't know the desired behavior.
    public function testStaffViewTalkingPointsForStudent(ApiAuthTester $I)
    {
        $I->wantTo('View talking points for a student in my group.');
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff, 'students/'.$this->student['id'].'/talking_point', [], 403, []);
    }
    */

    // We're putting in null as the desired response code because what we really care about is that it doesn't return any data.
    // We imagine this could happen with a 403, or with a 200 and an empty array.
    public function testStaffViewProfileItemsForStudent(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View profile items for a student in my group');
        $testData = ["person_student_id" => $this->studentWithProfileItems['id'],
        "person_staff_id" => $this->minimumAccessOnlyStaff['id'],
        "profile" => []
        ];
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff, 'students/'.$this->studentWithProfileItems['id'].'/studentdetails', [], 200, [$testData]);
    }


    //FEATURES
    public function testStaffViewActivityStreamForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View activity stream (list of notes, contacts, appointments, referrals) for a student in my group.');
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff, 'students/activity', $this->getActivityStream, null, [['data' => []]]);
    }

    public function testStaffViewAllNotesForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available notes for a student in my group.');
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff, 'students/activity', $this->getStudentNotes, null, [['data' => []]]);
    }

    public function testStaffViewAllContactsForAccessibleStudent(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View a list of all available contacts for a student in my group.');
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff, 'students/'. $this->student['id'] .'/contacts', [], 400, []);
    }

    public function testStaffViewAllAppointmentsForAccessibleStudent(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('View a list of all available appointments for a student in my group.');
        $testData = [
            "person_student_id" => $this->student['id'],
            "person_staff_id"=> $this->minimumAccessOnlyStaff['id'],
            "total_appointments"=> 0,
            "total_appointments_by_me"=> 0,
            "total_same_day_appointments_by_me"=> 0,
            "appointments"=> []
        ];
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff, 'students/'. $this->student['id'] .'/appointments', [], 200, [$testData]);
    }

    public function testStaffViewAllReferralsForAccessibleStudent(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('View a list of all available referrals for a student in my group.');
        $testData = [
        "person_student_id" => $this->student['id'],
        "person_staff_id"=> $this->minimumAccessOnlyStaff['id'],
        "total_referrals_count"=> 0,
        "total_open_referrals_count"=> 0,
        "total_open_referrals_assigned_to_me"=> 0
        ];
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff, 'students/'. $this->student['id'] .'/referrals', [], 200, [$testData]);
    }

    public function testStaffCreatePublicNote(ApiAuthTester $I)
    {
        $I->wantTo('Create a public note for a student (as a staff member without the appropriate permission).');
        $this->_postAPITestRunner($I, $this->minimumAccessOnlyStaff, 'notes', $this->publicNote, 403, []);
    }

    public function testStaffCreatePrivateNote(ApiAuthTester $I)
    {
        $I->wantTo('Create a private note for a student (as a staff member without the appropriate permission).');
        $this->_postAPITestRunner($I, $this->minimumAccessOnlyStaff, 'notes', $this->privateNote, 403, []);
    }

    /*
    public function testStaffCreateTeamNote(ApiAuthTester $I)
    {
        $I->wantTo('Create a team note for a student (as a staff member without the appropriate permission).');
        $this->_postAPITestRunner($I, $this->minimumAccessOnlyStaff, 'notes', $this->teamNote, 201, [$this->teamNote]);
    }*/

    public function testStaffCreatePublicContact(ApiAuthTester $I)
    {
        $I->wantTo('Create a public contact for a student (as a staff member without the appropriate permission).');
        $this->_postAPITestRunner($I, $this->minimumAccessOnlyStaff, 'contacts', $this->publicContact, 403, []);
    }

    public function testStaffCreatePrivateContact(ApiAuthTester $I)
    {
        $I->wantTo('Create a private contact for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->minimumAccessOnlyStaff, 'contacts', $this->privateContact, 403, []);
    }

    /*
    public function testStaffCreateTeamContact(ApiAuthTester $I)
    {
        $I->wantTo('Create a team contact for a student (as a staff member without the appropriate permission).');
        $this->_postAPITestRunner($I, $this->minimumAccessOnlyStaff, 'contacts', $this->teamContact, 201, [$this->teamContact]);
    }*/

    public function testStaffCreateAppointment(ApiAuthTester $I)
    {
        $I->wantTo('Create an appointment for a student (as a staff member without the appropriate permission).');
        $params = array_merge($this->appointment, ['office_hours_id' => null]);
        $this->_postAPITestRunner($I, $this->minimumAccessOnlyStaff, 'appointments/'.$this->orgId.'/'.$this->minimumAccessOnlyStaff['id'], $params, 403, []);
    }

    public function testStaffCreatePublicReferral(ApiAuthTester $I)
    {
        $I->wantTo('Create a public referral for a student (as a staff member without the appropriate permission).');
        $this->_postAPITestRunner($I, $this->minimumAccessOnlyStaff, 'referrals', $this->publicReferral, 403, []);
    }

    public function testStaffCreatePrivateReferral(ApiAuthTester $I)
    {
        $I->wantTo('Create a private referral for a student (as a staff member without the appropriate permission).');
        $this->_postAPITestRunner($I, $this->minimumAccessOnlyStaff, 'referrals', $this->privateReferral, 403, []);
    }

    /*
    public function testStaffCreateTeamReferral(ApiAuthTester $I)
    {
        $I->wantTo('Create a team referral for a student (as a staff member without the appropriate permission).');
        $this->_postAPITestRunner($I, $this->minimumAccessOnlyStaff, 'referrals', $this->teamReferral, 201, [$this->teamReferral]);
    }*/

    public function testStaffViewPublicNote(ApiAuthTester $I)
    {
        $I->wantTo('View a specific public note created by a staff member in my group regarding a student in my group.');
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff, 'notes/222', [], 403, []);
    }

    public function testStaffViewPrivateNote(ApiAuthTester $I)
    {
        $I->wantTo('View a specific private note created by a staff member in my group regarding a student in my group.');
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff, 'notes/223', [], 403, []);
    }

    public function testStaffViewPublicContact(ApiAuthTester $I)
    {
        $I->wantTo('View a specific public contact created by a staff member in my group regarding a student in my group.');
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff, 'contacts/263', [], 403, []);
    }

    public function testStaffViewPrivateContact(ApiAuthTester $I)
    {
        $I->wantTo('View a specific private contact created by a staff member in my group regarding a student in my group.');
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff, 'contacts/264', [], 403, []);
    }

    public function testStaffViewPublicReferral(ApiAuthTester $I)
    {
        $I->wantTo('View a specific public referral created by a staff member in my group regarding a student in my group.');
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff, 'referrals/680', [], 403, []);
    }

    public function testStaffViewPrivateReferral(ApiAuthTester $I)
    {
        $I->wantTo('View a specific private referral created by a staff member in my group regarding a student in my group.');
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff, 'referrals/681', [], 403, []);
    }

    public function testStaffViewAppointment(ApiAuthTester $I)
    {
        $I->wantTo('View a specific appointment created by a staff member in my group regarding a student in my group.');
        $this->_getAPITestRunner($I, $this->minimumAccessOnlyStaff,
            'appointments/'.$this->orgId.'/'.$this->minimumAccessOnlyStaff['id'].'/appointmentId?appointmentId=988', [], 403, []);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }


}
