<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class MultiCourseStaffCest extends SynapseRestfulTestBase
{

    //NOTE: All Team Items are commented out, because Cuthbert is not on a team
    //Cuthbert has minimum access in Care of Magical Creatures and full access to History of Magic
    //The difference between this and minimumAccessOnlyCest is that this focuses on a student that is not
    //shared between courses and therefore should not be inheriting any permissions.  These intend to show
    //that a staff is not limited by a permission set in one course if they have higher access to a student
    //in another course while showing that just the fact of having higher permissions does not grant you
    //higher access to students in other courses that are not in the higher access course


    //RON WEASLEY SINGLE COURSE STUDENT
    /****************************************************************/
    private $orgId = 542;
    
    private $noPermissionAccessStaff = [
        'email' => 'armando.dippet@mailinator.com',
        'password' => 'password1!',
        'id' => 99707,
        'orgId' => 542,
        'langId' => 1
    ];

    // This staff member has the permission set Hogwarts_MinimumAccess

    private $TwoCourseAccessStaff = [
        'email' => 'cuthbert.binns@mailinator.com',
        'password' => 'password1!',
        'id' => 99437,
        'orgId' => 542,
        'langId' => 1
    ];

    //Alternative Staff for referrals
    private $staffH = [
        'email' => 'rubeus.hagrid@mailinator.com',
        'password' => 'password1!',
        'id' => 99446,
        'orgId' => 542,
        'langId' => 1
    ];

    //Alternative Staff for Harry Potter
    private $staffA = [
        'email' => 'albus.dumbledore@mailinator.com',
        'password' => 'password1!',
        'id' => 99704,
        'orgId' => 542,
        'langId' => 1
    ];

    private $studentInSearch = [
        'user_id' => 99432,
        'student_id' => 'S023',
        'user_firstname' => 'Ron ',
        'user_lastname' => 'Weasley',
        'user_email' => 'ron.weasley@mailinator.com',
        'student_status' => '1'
    ];

    private $student = [
        'id' => 99432,
        'student_external_id' => 'S023',
        'student_first_name' => 'Ron ',
        'student_last_name' => 'Weasley',
        'primary_email' => 'ron.weasley@mailinator.com',
        //'last_viewed_date' => '07/01/2015',
        'student_status' => '1',
        'risk_indicator_access' => false,
        'intent_to_leave_access' => false,
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
    "request_name" => null,
    "request_description"=> null,
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
                    "academic_update_id" => 2,
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
        'student-id' => 99432,
        'category' => 'note',
        'is-interaction' => false
    ];

    private $getActivityStream = [
        'student-id' => 99432,
        'category' => 'all',
        'is-interaction' => false
    ];


    //FOR VIEWING FEATURES
    private $publicNoteToView = [
        'organization_id' => 542,
        'notes_id' => 232,
        'notes_student_id' => 99432,
        'staff_id' => 99437
    ];

    private $privateNoteToView = [
        'organization_id' => 542,
        'notes_id' => 231,
        'notes_student_id' => 99432,
        'staff_id' => 99437
    ];

    private $publicContactToView = [
        'organization_id' => 542,
        'contact_id' => 271,
        'person_student_id' => 99432,
        'person_staff_id' => 99437
    ];

    private $privateContactToView = [
        'organization_id' => 542,
        'contact_id' => 270,
        'person_student_id' => 99432,
        'person_staff_id' => 99437
    ];


    private $appointmentToView = [
        'organization_id' => '542',
        'appointment_id' => 992,
        'person_id' => '99437'
    ];

    private $publicReferralToView = [
        'organization_id' => 542,
        'referral_id' => 692,
        'person_student_id' => 99432,
        'person_staff_id' => 99437
    ];

    private $privateReferralToView = [
        'organization_id' => 542,
        'referral_id' => 691,
        'person_student_id' => 99432,
        'person_staff_id' => 99437
    ];


    //FOR CREATION FEATURES
    private $publicNote = [
        'organization_id' => 542,
        'staff_id' => 99437,
        'notes_student_id' => 99432,
        'comment'=> "Public note created by Oliver",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateNote = [
        'organization_id' => 542,
        'staff_id' => 99437,
        'notes_student_id' => 99432,
        'comment'=> "Private note created by Oliver",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicContact = [
        'organization_id' => 542,
        'person_student_id' => 99432,
        'person_staff_id' => 99437,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateContact = [
        'organization_id' => 542,
        'person_student_id' => 99432,
        'person_staff_id' => 99437,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $appointment = [
        'organization_id' => 542,
        'person_id' => 99437,
        'attendees' => [['student_id' => 99432]],
        'location' => 'Charms Classroom',
        'detail_id' => 30,
        'slot_start' => '2015-05-23 09:00:00',
        'slot_end' => '2015-05-23 10:00:00',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicReferral = [
        'organization_id' => 542,
        'person_student_id' => 99432,
        'person_staff_id' => 99437,
        'assigned_to_user_id' => 99704,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateReferral = [
        'organization_id' => 542,
        'person_student_id' => 99432,
        'person_staff_id' => 99437,
        'assigned_to_user_id' => 99441,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];
    /******************************************************************************/



    //HARRY POTTER TWO COURSE STUDENT
    /******************************************************************************/
    private $studentInSearchHarry = [
        'user_id' => 99422,
        'student_id' => 'S013',
        'user_firstname' => 'Harry ',
        'user_lastname' => 'Potter',
        'user_email' => 'harry.potter@mailinator.com',
        'student_status' => '1'
    ];

    private $studentHarry = [
        'id' => 99422,
        'student_external_id' => 'S013',
        'student_first_name' => 'Harry ',
        'student_last_name' => 'Potter',
        'primary_email' => 'harry.potter@mailinator.com',
        'student_status' => '1',
        'risk_indicator_access' => true,
        'intent_to_leave_access' => true,
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


    //FEATURES DATA
    // For viewing a list of all notes/contacts/appointments/referrals for the student.
    private $getStudentNotesHarry = [
        'student-id' => 99422,
        'category' => 'note',
        'is-interaction' => false
    ];

    private $getActivityStreamHarry = [
        'student-id' => 99422,
        'category' => 'all',
        'is-interaction' => false
    ];

    private $publicNoteHarry = [
        'organization_id' => 542,
        'staff_id' => 99437,
        'notes_student_id' => 99422,
        'comment'=> "Public note created by Oliver",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateNoteHarry = [
        'organization_id' => 542,
        'staff_id' => 99437,
        'notes_student_id' => 99422,
        'comment'=> "Private note created by Oliver",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicContactHarry = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99437,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateContactHarry = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99437,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $appointmentHarry = [
        'organization_id' => 542,
        'person_id' => 99437,
        'attendees' => [['student_id' => 99422]],
        'location' => 'Charms Classroom',
        'detail_id' => 30,
        'slot_start' => '2015-05-23 09:00:00',
        'slot_end' => '2015-05-23 10:00:00',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicReferralHarry = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99437,
        'assigned_to_user_id' => 99437,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateReferralHarry = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    //FOR VIEWING FEATURES
    private $publicNoteToViewHarry = [
        'organization_id' => 542,
        'notes_id' => 213,
        //'notes_student_id' => 99422,
        'staff_id' => 99445
    ];

    private $publicContactToViewHarry = [
        'organization_id' => 542,
        'contact_id' => 256,
        //'person_student_id' => 99422,
        'person_staff_id' => 99445
    ];

    private $appointmentToViewHarry = [
        'organization_id' => '542',
        'appointment_id' => 993,
        'person_id' => '99704'
    ];

    private $publicReferralToViewHarry = [
        'organization_id' => 542,
        'referral_id' => 671,
        //'person_student_id' => 99422,
        'person_staff_id' => 99445
    ];
    private $academicUpdatePrepHarry = [

        "organization_id" => "542",
        "is_adhoc" => true,
        "is_create" => true,
        "students" => [
            "is_all"=> false,
            "selected_student_ids" => ""
        ],
        "courses" => [
            "is_all" => false,
            "selected_course_ids" => 436
        ]
    ];

    private $academicUpdateHarry = [
        "request_id" => "0",
        "request_name" => null,
        "request_description"=> null,
        "request_complete_status" => 0,
        "save_type" => "send",
        "request_details" =>
            [
                "subject_course" => "HIST1",
                "department_name" => "HISTD",
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
                        "academic_update_id" => 2,
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

    /*************************SINGLE COURSE STUDENT: Ron Weasley****************************/
    //ACTIONS
    public function testStaffViewRiskIndicatorIntentToLeaveMinAccess(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('MultiPermission Staff cannot view risk indicator or intent to leave flag with minimum access');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/' . $this->student['id'], [], 200, [$this->student]);
        //$I->dontSeeResponseContains('"student_risk_status":');
        //$I->dontSeeResponseContains('"student_intent_to_leave":');
        $I->seeResponseContainsJson ( array (
		'student_risk_status' => 'gray' 
		) );
        $I->seeResponseContainsJson ( array (
        		'student_intent_to_leave' => 'dark gray'
        ) );
    }

    public function testStaffSearchForStudentMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff searches for student information in my course with minimum access.');
        $testData = [
            ['organization_id' => 542],
            $this->studentInSearch
        ];
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff,
            'permission/'.$this->orgId.'/mystudents?personId='.$this->TwoCourseAccessStaff['id'],
            [], 200, $testData);
    }

    public function testStaffViewStudentProfileMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff views student information in my course with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/'.$this->student['id'], [], 200, [$this->student]);
    }

    public function testStaffViewProfileItemsForStudentMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a profile items in the same course with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/'.$this->student['id'].'/studentdetails', [], null, [['profile' => []]]);
    }

    /*
    public function testStaffViewTalkingPointsForStudentMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('Staff views talking points for a student in my group with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/'.$this->student['id'].'/talking_point', null, 403, [['data' => []]]);
    }
    */


    //Cuthbert Trying with Min Access to do features on Students He shouldn't have permission


    public function testStaffFromSameCourseViewPrivateContactMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a private contact created by a staff member in the same course with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'contacts/270', [], 403, []);
    }

    public function testStaffFromSameCourseViewPublicContactMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a public contact created by a staff member in the same course with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'contacts/271', [], 403, []);
    }

    public function testStaffFromSameCourseViewAppointmentMinAccess(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('MultiPermission Staff cannot view an appointment created by a staff member in the same course with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff,
            'appointments/'.$this->orgId.'/'.$this->staffH['id'].'/appointmentId?appointmentId=992',
            [], 403, []);
    }

    public function testStaffFromSameCourseViewPublicReferralMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a public referral created by a staff member in the same course with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'referrals/691', [], 403, []);
    }

    public function testStaffFromSameCourseViewPrivateReferralMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a private referral created by a staff member in the same course with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'referrals/692', [], 403, []);
    }

    public function testStaffFromSameCourseViewPrivateNoteMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a private note created by a staff member in the same course with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'notes/231', [], 403, []);
    }

    public function testStaffFromSameCourseViewPublicNoteMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a public note created by a staff member in the same course with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'notes/232', [], 403, []);
    }

    //FEATURES

    public function testStaffViewActivityStreamForAccessibleStudentMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view activities in the same course with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/activity',[], null, [['data' => []]]);
    }

    public function testStaffViewAllNotesForAccessibleStudentMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a list of notes in the same course with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/activity', [], null, [['data' => []]]);
    }

    public function testStaffViewAllContactsForAccessibleStudentMinAccess(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('MultiPermission Staff cannot view a list of contacts in the same course with minimum access.');
        $this->_getAPITestRunner($I, $this->noPermissionAccessStaff, 'students/'. $this->student['id'] .'/contacts', [], 403, []);
    }

    public function testStaffViewAllAppointmentsForAccessibleStudentMinAccess(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('MultiPermission Staff cannot view a list of appointments in the same course with minimum access.');
        $this->_getAPITestRunner($I, $this->noPermissionAccessStaff, 'students/'. $this->student['id'] .'/appointments', [], 403, []);
    }

    public function testStaffViewAllReferralsForAccessibleStudentMinAccess(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('MultiPermission Staff cannot view a list of referrals in the same course with minimum access.');
        $this->_getAPITestRunner($I, $this->noPermissionAccessStaff, 'students/'. $this->student['id'] .'/referrals', [], 403, []);
    }

    public function testStaffCreatePublicNoteMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a public note for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'notes', $this->publicNote, 403, []);
    }

    public function testStaffCreatePrivateNoteMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a private note for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'notes', $this->privateNote, 403, []);
    }

    /*
    public function testStaffCreateTeamNoteMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a team note for a student with minimum access.'');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'notes', $this->teamNote, 201, [$this->teamNote]);
    }*/

    public function testStaffCreatePublicContactMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a public contact for a course with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'contacts', $this->publicContact, 403, []);
    }

    public function testStaffCreatePrivateContactMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a private contact for a course with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'contacts', $this->privateContact, 403, []);
    }

    /*
    public function testStaffCreateTeamContactMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a team contact for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'contacts', $this->teamContact, 201, [$this->teamContact]);
    }*/

    public function testStaffCreateAppointmentMinAccess(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('MultiPermission Staff cannot create an appointment for a student with minimum access.');
        $params = array_merge($this->appointment, ['office_hours_id' => null]);
        $this->_postAPITestRunner($I, $this->noPermissionAccessStaff, 'appointments/'.$this->orgId.'/'.$this->TwoCourseAccessStaff['id'], $params, 403, []);
    }

    public function testStaffCreatePublicReferralMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a public referral for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'referrals', $this->publicReferral, 403, []);
    }

    public function testStaffCreatePrivateReferralMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a private referral for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'referrals', $this->privateReferral, 403, []);
    }

    /*
    public function testStaffCreateTeamReferralMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a team referral for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'referrals', $this->teamReferral, 201, [$this->teamReferral]);
    }*/

    //SPECIAL COURSE ONLY PERMISSIONS

    public function testStaffAcademicUpdateMinAccess(ApiAuthTester $I, $scenario)
    {
        
        $I->wantTo('MultiPermission Staff cannot create an academic update for students with minimum access');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'academicupdates', $this->academicUpdatePrep, 403, []);
        //$this->_putAPITestRunner($I, $this->TwoCourseAccessStaff, 'academicupdates', $this->academicUpdate, 403, []);
    }


    /******************************************************************************************///

    /*****************************TWO COURSE STUDENT: HARRY POTTER*********************************/

    //ACTIONS
    public function testStaffViewRiskIndicatorIntentToLeaveFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view risk indicator or intent to leave flag with minimum access');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/' . $this->studentHarry['id'], [], 200, [$this->studentHarry]);
        //$I->SeeResponseContains('"student_risk_status":');
        //$I->SeeResponseContains('"student_intent_to_leave":');
    }

    public function testStaffSearchForStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff searches for multicourse student information in my course with unioned Full Access.');
        $testData = [
            ['organization_id' => 542],
            $this->studentInSearchHarry
        ];
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff,
            'permission/'.$this->orgId.'/mystudents?personId='.$this->TwoCourseAccessStaff['id'],
            [], 200, $testData);
    }

    public function testStaffViewStudentProfileFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff views student information of a multicourse student in my course with unioned Full Access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/'.$this->studentHarry['id'], [], 200, [$this->studentHarry]);
    }

    /*
    public function testStaffViewTalkingPointsForStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view talking points for a student in a group with full access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/'.$this->studentHarry['id'].'/talking_point', [], 200, []);
    }
    */

    public function testStaffViewProfileItemsForStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view a list of profile items for a student in a course with full access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/'.$this->studentHarry['id'].'/studentdetails', [], 200, $this->studentProfileItems);
    }


    //Oliver Trying with Full Access to do features on Students He should have access to

    public function testViewOwnPrivateContactFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view his own private contact.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'contacts/272', [], 200, []);
    }

    public function testStaffFromSameCourseViewPublicContactFullAccess(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('MultiPermission Staff can view a public contact created by a staff member in the same course with full access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'contacts/256', [], 200, [$this->publicContactToViewHarry]);
    }

    public function testStaffFromSameCourseViewAppointmentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view an appointment created by a staff member in the same course with full access.');
        $this->_getAPITestRunner($I, $this->staffA,
            'appointments/'.$this->orgId.'/'.$this->staffA['id'].'/appointmentId?appointmentId=993',
            [], 200, [$this->appointmentToViewHarry]);
    }

    public function testStaffFromSameCourseViewPublicReferralFullAccess(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('MultiPermission Staff can view a public referral created by a staff member in the same course with full access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'referrals/671', [], 200, [$this->publicReferralToViewHarry]);
    }

    public function testViewOwnPrivateReferralFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view his own private referral.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'referrals/693', [], 200, []);
    }

    public function testViewOwnPrivateNoteFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view his own private note.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'notes/233', [], 200, []);
    }

    public function testStaffFromSameCourseViewPublicNoteFullAccess(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('MultiPermission Staff can view a public note created by a staff member in the same course with full access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'notes/213', [], 200, [$this->publicNoteToViewHarry]);
    }

    //FEATURES

    public function testStaffViewActivityStreamForAccessibleStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view activities for a student in a course with full access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/activity', $this->getActivityStreamHarry, 200, []);
    }

    public function testStaffViewAllNotesForAccessibleStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view all notes for a student in a course with full access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/activity', $this->getStudentNotesHarry, 200, []);
    }

    public function testStaffViewAllContactsForAccessibleStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view all contacts for a student in a course with full access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/'. $this->studentHarry['id'] .'/contacts', [], 200, []);
    }

    public function testStaffViewAllAppointmentsForAccessibleStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view all appointments for a student in a course with full access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/'. $this->studentHarry['id'] .'/appointments', [], 200, []);
    }

    public function testStaffViewAllReferralsForAccessibleStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view all referrals for a student in a course with full access.');
        $this->_getAPITestRunner($I, $this->TwoCourseAccessStaff, 'students/'. $this->studentHarry['id'] .'/referrals', [], 200, []);
    }

    public function testStaffCreatePublicNoteFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a public note for a student in a course with full access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'notes', $this->publicNoteHarry, 201, []);
    }

    public function testStaffCreatePrivateNoteFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a private note for a student in a course with full access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'notes', $this->privateNoteHarry, 201, []);
    }

    /*
    public function testStaffCreateTeamNoteFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a team note for a student in a course with full access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'notes', $this->teamNoteHarry, 201, [$this->teamNoteHarry]);
    }*/

    public function testStaffCreatePublicContactFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a public contact for a student in a course with full access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'contacts', $this->publicContactHarry, 201, []);
    }

    public function testStaffCreatePrivateContactFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a private contact for a student in a course with full access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'contacts', $this->privateContactHarry, 201, []);
    }

    /*
    public function testStaffCreateTeamContactFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a team note for a student in a group with full access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'contacts', $this->teamContactHarry, 201, [$this->teamContactHarry]);
    }*/

    public function testStaffCreateAppointmentFullAccess(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('MultiPermission Staff can create an appointment for a student in a course with full access.');
        $params = array_merge($this->appointmentHarry, ['office_hours_id' => null]);
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'appointments/'.$this->orgId.'/'.$this->TwoCourseAccessStaff['id'], $params, 201, []);
    }

    public function testStaffCreatePublicReferralFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a public referral for a student in a course with full access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'referrals', $this->publicReferralHarry, 201, []);
    }

    public function testStaffCreatePrivateReferralFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a private referral for a student in a course with full access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'referrals', $this->privateReferralHarry, 201, []);
    }

    /*
    public function testStaffCreateTeamReferralFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a team note for a student in a course with full access.');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'referrals', $this->teamReferralHarry, 201, [$this->teamReferralHarry]);
    }*/

    public function testStaffAcademicUpdateFullAccess(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('MultiPermission Staff cannot create an academic update for students with minimum access');
        $this->_postAPITestRunner($I, $this->TwoCourseAccessStaff, 'academicupdates', $this->academicUpdatePrepHarry, 201, []);

        $this->_putAPITestRunner($I, $this->TwoCourseAccessStaff, 'academicupdates', $this->academicUpdateHarry, 200, []);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
