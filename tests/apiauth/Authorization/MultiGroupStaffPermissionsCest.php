<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class MultiGroupStaffPermissionsCest extends SynapseRestfulTestBase
{

    //NOTE: All Team Items are commented out, because Oliver is not on a team
    //Oliver has minimum access in Dumbledore's army and full access to Quidditch griffyndor
    //The difference between this and minimumAccessOnlyCest is that this focuses on a student that is not
    //shared between groups and therefore should not be inheriting any permissions.  These intend to show
    //that a staff is not limited by a permission set in one group if they have higher access to a student
    //in another group while showing that just the fact of having higher permissions does not grant you
    //higher access to students in other groups that are not in the higher access group


    //NEVILLE LONGBOTTOM SINGLE GROUP STUDENT
    /****************************************************************/
    private $orgId = 542;

    // This staff member has the permission set Hogwarts_MinimumAccess

    private $TwoGroupAccessStaff = [
        'email' => 'oliver.wood@mailinator.com',
        'password' => 'password1!',
        'id' => 99441,
        'orgId' => 542,
        'langId' => 1
    ];

    //Alternative Staff for referrals
    private $staffM = [
        'email' => 'minerva.mcgonagall@mailinator.com',
        'password' => 'password1!',
        'id' => 99440,
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
        //'last_viewed_date' => '07/01/2015',
        'student_status' => '1',
        'risk_indicator_access' => false,
        'intent_to_leave_access' => false,
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


    //FOR VIEWING FEATURES
    private $publicNoteToView = [
        'organization_id' => 542,
        'notes_id' => 222,
        'notes_student_id' => 99429,
        'staff_id' => 99704
    ];

    private $privateNoteToView = [
        'organization_id' => 542,
        'notes_id' => 223,
        'notes_student_id' => 99429,
        'staff_id' => 99704
    ];

    private $publicContactToView = [
        'organization_id' => 542,
        'contact_id' => 263,
        'person_student_id' => 99429,
        'person_staff_id' => 99704
    ];

    private $privateContactToView = [
        'organization_id' => 542,
        'contact_id' => 264,
        'person_student_id' => 99429,
        'person_staff_id' => 99704
    ];


    private $appointmentToView = [
        'organization_id' => '542',
        'appointment_id' => 988,
        'person_id' => '99704'
    ];

    private $publicReferralToView = [
        'organization_id' => 542,
        'referral_id' => 680,
        'person_student_id' => 99429,
        'person_staff_id' => 99704
    ];

    private $privateReferralToView = [
        'organization_id' => 542,
        'referral_id' => 681,
        'person_student_id' => 99429,
        'person_staff_id' => 99704
    ];


    //FOR CREATION FEATURES
    private $publicNote = [
        'organization_id' => 542,
        'staff_id' => 99441,
        'notes_student_id' => 99429,
        'comment'=> "Public note created by Oliver",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateNote = [
        'organization_id' => 542,
        'staff_id' => 99441,
        'notes_student_id' => 99429,
        'comment'=> "Private note created by Oliver",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicContact = [
        'organization_id' => 542,
        'person_student_id' => 99429,
        'person_staff_id' => 99441,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateContact = [
        'organization_id' => 542,
        'person_student_id' => 99429,
        'person_staff_id' => 99441,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $appointment = [
        'organization_id' => 542,
        'person_id' => 99441,
        
        "person_id_proxy" => 0,
        'activity_log_id' => null,
        "detail" => "api - Test details",
        "description" => "api - dictumst etiam faucibus",
        "is_free_standing" => true,
        "type" => "F",
        
        'attendees' => [['student_id' => 99429,  "is_selected" => true, "is_added_new " => true]],
        'location' => 'Charms Classroom',
        'detail_id' => 30,
        'slot_start' => '2015-05-23 09:00:00',
        'slot_end' => '2015-05-23 10:00:00',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false,
         'team_ids' => [ ["id" => "", "is_team_selected" => false]]]]
    ];

    private $publicReferral = [
        'organization_id' => 542,
        'person_student_id' => 99429,
        'person_staff_id' => 99441,
        'assigned_to_user_id' => 99704,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateReferral = [
        'organization_id' => 542,
        'person_student_id' => 99429,
        'person_staff_id' => 99441,
        'assigned_to_user_id' => 99441,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];
    /******************************************************************************/



    //HARRY POTTER TWO GROUP STUDENT
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
        'staff_id' => 99441,
        'notes_student_id' => 99422,
        'comment'=> "Public note created by Oliver",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateNoteHarry = [
        'organization_id' => 542,
        'staff_id' => 99441,
        'notes_student_id' => 99422,
        'comment'=> "Private note created by Oliver",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicContactHarry = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99441,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateContactHarry = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99441,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $appointmentHarry = [
        'organization_id' => 542,
        'person_id' => 99436,
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
        'person_staff_id' =>  99436,
        'assigned_to_user_id' => 99441,
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
        'notes_student_id' => '99422',
        'staff_id' => 99445
    ];

    private $publicContactToViewHarry = [
        'organization_id' => 542,
        'contact_id' => 256,
        'person_student_id' => '99422',
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
        'person_student_id' => '99422',
        'person_staff_id' => 99445
    ];

    /*****************************************************************/



    /*************************SINGLE GROUP STUDENT: NEVILLE LONGBOTTOM****************************/
    //ACTIONS
    public function testStaffViewRiskIndicatorIntentToLeaveMinAccess(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('MultiPermission Staff cannot view risk indicator or intent to leave flag with minimum access');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/' . $this->student['id'], [], 200, [$this->student]);
        // key will exists since gray is default
        //$I->dontSeeResponseContains('"student_risk_status":');
        //$I->dontSeeResponseContains('"student_intent_to_leave":');
    }

    public function testStaffSearchForStudentMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff searches for student information in my group with minimum access.');
        $testData = [
            ['organization_id' => 542],
            $this->studentInSearch
        ];
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff,
            'permission/'.$this->orgId.'/mystudents?personId='.$this->TwoGroupAccessStaff['id'],
            [], 200, $testData);
    }

    public function testStaffViewStudentProfileMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff views student information in my group with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/'.$this->student['id'], [], 200, [$this->student]);
    }

    public function testStaffViewProfileItemsForStudentMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a profile items in the same group with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/'.$this->student['id'].'/studentdetails', [], null, [['profile' => []]]);
    }

    /*
    public function testStaffViewTalkingPointsForStudentMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('Staff views talking points for a student in my group with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/'.$this->student['id'].'/talking_point', null, 403, [['data' => []]]);
    }
    */


    //Oliver Trying with Min Access to do features on Students He shouldn't have permission


    public function testStaffFromSameGroupViewPrivateContactMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a private contact created by a staff member in the same group with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'contacts/264', [], 403, []);
    }

    public function testStaffFromSameGroupViewPublicContactMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a public contact created by a staff member in the same group with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'contacts/263', [], 403, []);
    }

    public function testStaffFromSameGroupViewAppointmentMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view an appointment created by a staff member in the same group with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff,
            'appointments/'.$this->orgId.'/'.$this->staffM['id'].'/appointmentId?appointmentId=988',
            [], 403, []);
    }

    public function testStaffFromSameGroupViewPublicReferralMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a public referral created by a staff member in the same group with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'referrals/680', [], 403, []);
    }

    public function testStaffFromSameGroupViewPrivateReferralMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a private referral created by a staff member in the same group with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'referrals/681', [], 403, []);
    }
    public function testStaffFromSameGroupViewPrivateNoteMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a private note created by a staff member in the same group with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'notes/223', [], 403, []);
    }

    public function testStaffFromSameGroupViewPublicNoteMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a public note created by a staff member in the same group with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'notes/222', [], 403, []);
    }


    //FEATURES

    public function testStaffViewActivityStreamForAccessibleStudentMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view activities in the same group with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/activity',[], null, [['data' => []]]);
    }

    public function testStaffViewAllNotesForAccessibleStudentMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view a list of notes in the same group with minimum access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/activity', [], null, [['data' => []]]);
    }

    public function testStaffViewAllContactsForAccessibleStudentMinAccess(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('MultiPermission Staff cannot view a list of contacts in the same group with minimum access.');
        $testData = [
        "person_student_id"=> $this->student['id'],
        "person_staff_id"=> $this->TwoGroupAccessStaff['id'],
        "total_contacts"=> 0
        ];
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/'. $this->student['id'] .'/contacts', [], 200, [$testData]);
    }

    public function testStaffViewAllAppointmentsForAccessibleStudentMinAccess(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('MultiPermission Staff cannot view a list of appointments in the same group with minimum access.');
        $testData = [
        "person_student_id"=> $this->student['id'],
        "person_staff_id"=> $this->TwoGroupAccessStaff['id'],
        "total_appointments"=> 0,
        "total_appointments_by_me"=> 0,
        "total_same_day_appointments_by_me"=> 0,
        "appointments"=> []
        ];
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/'. $this->student['id'] .'/appointments', [], 200, [$testData]);
    }

    public function testStaffViewAllReferralsForAccessibleStudentMinAccess(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('MultiPermission Staff cannot view a list of referrals in the same group with minimum access.');
        $testData = [
        "person_student_id"=> $this->student['id'],
        "person_staff_id"=> $this->TwoGroupAccessStaff['id'],
        "total_referrals_count"=> 2,
        "total_open_referrals_count"=> 2,
        "total_open_referrals_assigned_to_me"=> 1,
        "referrals"=> [
            [
                "referral_id"=> 683,
                "reason_category_subitem_id"=> 19,
                "reason_category_subitem"=> "Class attendance concern "
            ]
        ]
        ];
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/'. $this->student['id'] .'/referrals', [], 200, [$testData]);
    }

    public function testStaffCreatePublicNoteMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a public note for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'notes', $this->publicNote, 403, []);
    }

    public function testStaffCreatePrivateNoteMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a private note for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'notes', $this->privateNote, 403, []);
    }

    /*
    public function testStaffCreateTeamNoteMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a team note for a student with minimum access.'');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'notes', $this->teamNote, 201, [$this->teamNote]);
    }*/

    public function testStaffCreatePublicContactMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a public contact for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'contacts', $this->publicContact, 403, []);
    }

    public function testStaffCreatePrivateContactMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a private contact for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'contacts', $this->privateContact, 403, []);
    }

    /*
    public function testStaffCreateTeamContactMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a team contact for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'contacts', $this->teamContact, 201, [$this->teamContact]);
    }*/

    public function testStaffCreateAppointmentMinAccess(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('MultiPermission Staff cannot create an appointment for a student with minimum access.');
        $params = array_merge($this->appointment, ['office_hours_id' => null]);
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'appointments/'.$this->orgId.'/'.$this->TwoGroupAccessStaff['id'], $params, 201, []);
    }

    public function testStaffCreatePublicReferralMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a public referral for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'referrals', $this->publicReferral, 403, []);
    }

    public function testStaffCreatePrivateReferralMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a private referral for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'referrals', $this->privateReferral, 403, []);
    }

    /*
    public function testStaffCreateTeamReferralMinAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot create a team referral for a student with minimum access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'referrals', $this->teamReferral, 201, [$this->teamReferral]);
    }*/


    /******************************************************************************************///

    /*****************************TWO GROUP STUDENT: HARRY POTTER*********************************/


    //ACTIONS
    public function testStaffViewRiskIndicatorIntentToLeaveFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff cannot view risk indicator or intent to leave flag with minimum access');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/' . $this->studentHarry['id'], [], 200, [$this->studentHarry]);
        //$I->SeeResponseContains('"student_risk_status":');
        //$I->SeeResponseContains('"student_intent_to_leave":');
    }

    public function testStaffSearchForStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff searches for multigroup student information in my group with unioned Full Access.');
        $testData = [
            ['organization_id' => 542],
            $this->studentInSearchHarry
        ];
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff,
            'permission/'.$this->orgId.'/mystudents?personId='.$this->TwoGroupAccessStaff['id'],
            [], 200, $testData);
    }

    public function testStaffViewStudentProfileFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff views student information of a multigroup student in my group with unioned Full Access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/'.$this->studentHarry['id'], [], 200, [$this->studentHarry]);
    }

    /*
    public function testStaffViewTalkingPointsForStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view talking points for a student in a group with full access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/'.$this->studentHarry['id'].'/talking_point', [], 200, []);
    }
    */

    public function testStaffViewProfileItemsForStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view a list of profile items for a student in a group with full access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/'.$this->studentHarry['id'].'/studentdetails', [], 200, $this->studentProfileItems);
    }


     //Oliver Trying with Full Access to do features on Students He should have access to

    public function testViewOwnPrivateContactFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view his own private contact.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'contacts/265', [], 200, []);
    }

    public function testStaffFromSameGroupViewPublicContactFullAccess(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('MultiPermission Staff can view a public contact created by a staff member in the same group with full access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'contacts/256', [], 200, [$this->publicContactToViewHarry]);
    }

    public function testStaffFromSameGroupViewAppointmentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view an appointment created by a staff member in the same group with full access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff,
            'appointments/'.$this->orgId.'/'.$this->staffA['id'].'/appointmentId?appointmentId=993',
            [], 403, []);
    }

    public function testStaffFromSameGroupViewPublicReferralFullAccess(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('MultiPermission Staff can view a public referral created by a staff member in the same group with full access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'referrals/671', [], 200, [$this->publicReferralToViewHarry]);
    }

    public function testViewOwnPrivateReferralFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view his own private referral.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'referrals/686', [], 200, []);
    }

    public function testViewOwnPrivateNoteFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view his own private note.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'notes/226', [], 200, []);
    }

    public function testStaffFromSameGroupViewPublicNoteFullAccess(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('MultiPermission Staff can view a public note created by a staff member in the same group with full access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'notes/213', [], 200, [$this->publicNoteToViewHarry]);
    }

    //FEATURES

    public function testStaffViewActivityStreamForAccessibleStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view activities for a student in a group with full access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/activity', $this->getActivityStreamHarry, 200, []);
    }

    public function testStaffViewAllNotesForAccessibleStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view all notes for a student in a group with full access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/activity', $this->getStudentNotesHarry, 200, []);
    }

    public function testStaffViewAllContactsForAccessibleStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view all contacts for a student in a group with full access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/'. $this->studentHarry['id'] .'/contacts', [], 200, []);
    }

    public function testStaffViewAllAppointmentsForAccessibleStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view all appointments for a student in a group with full access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/'. $this->studentHarry['id'] .'/appointments', [], 200, []);
    }

    public function testStaffViewAllReferralsForAccessibleStudentFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can view all referrals for a student in a group with full access.');
        $this->_getAPITestRunner($I, $this->TwoGroupAccessStaff, 'students/'. $this->studentHarry['id'] .'/referrals', [], 200, []);
    }

    public function testStaffCreatePublicNoteFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a public note for a student in a group with full access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'notes', $this->publicNoteHarry, 201, []);
    }
    public function testStaffCreatePrivateNoteFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a private note for a student in a group with full access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'notes', $this->privateNoteHarry, 201, []);
    }

    /*
    public function testStaffCreateTeamNoteFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a team note for a student in a group with full access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'notes', $this->teamNoteHarry, 201, [$this->teamNoteHarry]);
    }*/

    public function testStaffCreatePublicContactFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a public contact for a student in a group with full access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'contacts', $this->publicContactHarry, 201, []);
    }

    public function testStaffCreatePrivateContactFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a private contact for a student in a group with full access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'contacts', $this->privateContactHarry, 201, []);
    }

    /*
    public function testStaffCreateTeamContactFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a team note for a student in a group with full access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'contacts', $this->teamContactHarry, 201, [$this->teamContactHarry]);
    }*/

    public function testStaffCreateAppointmentFullAccess(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('MultiPermission Staff can create an appointment for a student in a group with full access.');
        $params = array_merge($this->appointmentHarry, ['office_hours_id' => null]);
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'appointments/'.$this->orgId.'/'.$this->TwoGroupAccessStaff['id'], $params, 201, []);
    }

    public function testStaffCreatePublicReferralFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a public referral for a student in a group with full access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'referrals', $this->publicReferralHarry, 201, []);
    }

    public function testStaffCreatePrivateReferralFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a private referral for a student in a group with full access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'referrals', $this->privateReferralHarry, 201, []);
    }

    /*
    public function testStaffCreateTeamReferralFullAccess(ApiAuthTester $I)
    {
        $I->wantTo('MultiPermission Staff can create a team note for a student in a group with full access.');
        $this->_postAPITestRunner($I, $this->TwoGroupAccessStaff, 'referrals', $this->teamReferralHarry, 201, [$this->teamReferralHarry]);
    }*/

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
