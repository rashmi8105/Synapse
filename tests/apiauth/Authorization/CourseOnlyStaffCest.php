<?php
//ACTIONS DATA
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class CourseOnlyStaffCest extends SynapseRestfulTestBase
{
    private $orgId = 542;

    // This staff member has the permission set Hogwarts_MinimumAccess

    private $CourseOnlyStaff = [
        'email' => 'rubeus.hagrid@mailinator.com',
        'password' => 'password1!',
        'id' => 99446,
        'orgId' => 542,
        'langId' => 1
    ];
 

    // A student in Dumbledore's Army (where Remus has min access)
    private $studentInSearch = [
        'user_id' => 99422,
        'student_id' => 'S013',
        'user_firstname' => 'Harry ',
        'user_lastname' => 'Potter',
        'user_email' => 'harry.potter@mailinator.com',
        'student_status' => '1'
    ];

    private $student = [
        'id' => 99422,
        'student_external_id' => 'S013',
        'student_first_name' => 'Harry ',
        'student_last_name' => 'Potter',
        'primary_email' => 'harry.potter@mailinator.com',
        'student_status' => '1',
        'risk_indicator_access' => true,
        'intent_to_leave_access' => true
    ];

    private $studentProfileItems = [
        'data' => [
            'person_student_id' => 99422,
            'profile' => [['block_name' => 'Demographic',
                'items' => [
                    ['name' => 'Gender',
                        'value' => 'Male'],
                    ['name' => 'BirthYear',
                        'value' => '07/31/1980'],
                    ['name' => 'RaceEthnicity',
                        'value' => 'White']
                ]
            ]]
        ]
    ];
    private $academicUpdatePrep = [

        "organization_id" => "542",
        "is_adhoc" => true,
        "is_create" => true,
        "students" => [
            "is_all"=> false,
            "selected_student_ids" => ""
        ],
        "courses" => [
            "is_all" => false,
            "selected_course_ids" => 435
        ]
    ];

    private $academicUpdate = [
        "request_id" => "0",
        "request_name" => "null",
        "request_description"=> "null",
        "request_complete_status" => 0,
        "save_type" => "send",
        "request_details" =>
            [
                "subject_course" => "CRE1",
                "department_name" => "CRED",
                "academic_year_name" => "Jahr",
                "academic_term_name" => "Herbst",
                "course_section_name" => "101",
                "indexCount" => 0,
                "student_details" => [
                    [
                        "academic_update_id" => 1,
                        "student_id" => 99414,
                        "student_firstname" => "Cho ",
                        "student_lastname" => "Chang",
                        "student_risk" => "low",
                        "student_grade" => "A",
                        "student_absences" => "",
                        "student_comments" => "",
                        "student_refer" => false,
                        "student_send" => false,
                        "is_bypassed" => false,
                        "student_status" => "1",
                        "academic_update_status" => "open",
                        "notSubmited" => false,
                        "student_academic_assist_refer" => false
                    ],

                    [
                        "academic_update_id" => 9,
                        "student_id" => 99422,
                        "student_firstname" => "Harry ",
                        "student_lastname" => "Potter",
                        "student_risk" => "",
                        "student_grade" => "A",
                        "student_absences" => "",
                        "student_comments" => "",
                        "student_refer" => false,
                        "student_send" => false,
                        "is_bypassed" => false,
                        "student_status" => "1",
                        "academic_update_status" => "open",
                        "notSubmited" => false,
                        "student_academic_assist_refer"=> false
                    ]
                ]


            ]

    ];

    //FEATURES DATA
    // For viewing a list of all notes/contacts/appointments/referrals for the student.
    private $getStudentNotes = [
        'student-id' => 99422,
        'category' => 'note',
        'is-interaction' => false
    ];

    private $getActivityStream = [
        'student-id' => 99422,
        'category' => 'all',
        'is-interaction' => false
    ];

    private $publicNote = [
        'organization_id' => 542,
        'staff_id' => 99446,
        'notes_student_id' => 99422,
        'comment'=> "Public note created by Rubius",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateNote = [
        'organization_id' => 542,
        'staff_id' => 99446,
        'notes_student_id' => 99422,
        'comment'=> "Private note created by Rubius",
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
        'comment' => 'test private contact',
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


    //ACTIONS
    public function testStaffSearchForStudent(ApiAuthTester $I)
    {
        $I->wantTo('Search for student information for a student in my group.');
        $testData = [
            ['organization_id' => 542],
            $this->studentInSearch
        ];
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff,
            'permission/'.$this->orgId.'/mystudents?personId='.$this->CourseOnlyStaff['id'],
            [], 200, $testData);
    }

    public function testStaffViewStudentProfile(ApiAuthTester $I)
    {
        $I->wantTo('View student information of a student in my group.');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff, 'students/'.$this->student['id'], [], 200, [$this->student]);
    }

    /*
    // Talking points are not specifically in the permission sets, so we don't know the desired behavior.
    public function testStaffViewTalkingPointsForStudent(ApiAuthTester $I)
    {
        $I->wantTo('View talking points for a student in my group.');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff, 'students/'.$this->student['id'].'/talking_point', [], 403, []);
    }
    */

    //
    public function testStaffViewProfileItemsForStudent(ApiAuthTester $I)
    {
        $I->wantTo('View profile items for a student in my group');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff, 'students/'.$this->studentProfileItems['data']['person_student_id'].'/studentdetails', [], 200, [$this->studentProfileItems]);
    }


    //FEATURES
    public function testStaffViewActivityStreamForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View activity stream (list of notes, contacts, appointments, referrals) for a student in my group.');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff, 'students/activity', $this->getActivityStream, 200, []);
    }

    public function testStaffViewAllNotesForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available notes for a student in my group.');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff, 'students/activity', $this->getStudentNotes, 200, []);
    }

    public function testStaffViewAllContactsForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available contacts for a student in my group.');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff, 'students/'. $this->student['id'] .'/contacts', [], 200, []);
    }

    public function testStaffViewAllAppointmentsForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available appointments for a student in my group.');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff, 'students/'. $this->student['id'] .'/appointments', [], 200, []);
    }

    public function testStaffViewAllReferralsForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available referrals for a student in my group.');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff, 'students/'. $this->student['id'] .'/referrals', [], 200, []);
    }

    public function testStaffCreatePublicNote(ApiAuthTester $I)
    {
        $I->wantTo('Create a public note for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->CourseOnlyStaff, 'notes', $this->publicNote, 201, []);
    }

    public function testStaffCreatePrivateNote(ApiAuthTester $I)
    {
        $I->wantTo('Create a private note for a student (as a staff member witha the appropriate permission).');
        $this->_postAPITestRunner($I, $this->CourseOnlyStaff, 'notes', $this->privateNote, 201, []);
    }

    /*
    public function testStaffCreateTeamNote(ApiAuthTester $I)
    {
        $I->wantTo('Create a team note for a student (as a staff member without the appropriate permission).');
        $this->_postAPITestRunner($I, $this->CourseOnlyStaff, 'notes', $this->teamNote, 201, [$this->teamNote]);
    }*/

    public function testStaffCreatePublicContact(ApiAuthTester $I)
    {
        $I->wantTo('Create a public contact for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->CourseOnlyStaff, 'contacts', $this->publicContact, 201, []);
    }

    public function testStaffCreatePrivateContact(ApiAuthTester $I)
    {
        $I->wantTo('Create a private contact for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->CourseOnlyStaff, 'contacts', $this->privateContact, 201, []);
    }

    /*
    public function testStaffCreateTeamContact(ApiAuthTester $I)
    {
        $I->wantTo('Create a team contact for a student (as a staff member without the appropriate permission).');
        $this->_postAPITestRunner($I, $this->CourseOnlyStaff, 'contacts', $this->teamContact, 201, [$this->teamContact]);
    }*/

    public function testStaffCreateAppointment(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Create an appointment for a student (as a staff member with the appropriate permission).');
        $params = array_merge($this->appointment, ['office_hours_id' => null]);
        $this->_postAPITestRunner($I, $this->CourseOnlyStaff, 'appointments/'.$this->orgId.'/'.$this->CourseOnlyStaff['id'], $params, 201, []);
    }

    public function testStaffCreatePublicReferral(ApiAuthTester $I)
    {
        $I->wantTo('Create a public referral for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->CourseOnlyStaff, 'referrals', $this->publicReferral, 201, []);
    }

    public function testStaffCreatePrivateReferral(ApiAuthTester $I)
    {
        $I->wantTo('Create a private referral for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->CourseOnlyStaff, 'referrals', $this->privateReferral, 201, []);
    }

    /*
    public function testStaffCreateTeamReferral(ApiAuthTester $I)
    {
        $I->wantTo('Create a team referral for a student (as a staff member without the appropriate permission).');
        $this->_postAPITestRunner($I, $this->CourseOnlyStaff, 'referrals', $this->teamReferral, 201, [$this->teamReferral]);
    }*/

    public function testStaffViewPublicNote(ApiAuthTester $I)
    {
        $I->wantTo('View a specific public note created by a staff member in my group regarding a student in my group.');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff, 'notes/229', [], 200, []);
    }

    public function testStaffViewPrivateNote(ApiAuthTester $I)
    {
        $I->wantTo('View a specific private note created by a staff member in my group regarding a student in my group.');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff, 'notes/230', [], 200, []);
    }

    public function testStaffViewPublicContact(ApiAuthTester $I)
    {
        $I->wantTo('View a specific public contact created by a staff member in my group regarding a student in my group.');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff, 'contacts/269', [], 200, []);
    }

    public function testStaffViewPrivateContact(ApiAuthTester $I)
    {
        $I->wantTo('View a specific private contact created by a staff member in my group regarding a student in my group.');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff, 'contacts/268', [], 200, []);
    }

    public function testStaffViewPublicReferral(ApiAuthTester $I)
    {
        $I->wantTo('View a specific public referral created by a staff member in my group regarding a student in my group.');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff, 'referrals/690', [], 200, []);
    }

    public function testStaffViewPrivateReferral(ApiAuthTester $I)
    {
        $I->wantTo('View a specific private referral created by a staff member in my group regarding a student in my group.');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff, 'referrals/689', [], 200, []);
    }

    public function testStaffViewAppointment(ApiAuthTester $I)
    {
        $I->wantTo('View a specific appointment created by a staff member in my course regarding a student in my group.');
        $this->_getAPITestRunner($I, $this->CourseOnlyStaff,
            'appointments/'.$this->orgId.'/'.$this->CourseOnlyStaff['id'].'/appointmentId?appointmentId=991', [], 200, []);
    }

    public function testStaffAcademicUpdate(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('MultiPermission Staff can create an adhoc academic update for students');
        $this->_postAPITestRunner($I, $this->CourseOnlyStaff, 'academicupdates', $this->academicUpdatePrep, 201, []);

        //$this->_putAPITestRunner($I, $this->CourseOnlyStaff, 'academicupdates', $this->academicUpdate, 204, []);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
