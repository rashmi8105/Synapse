<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class aggregateOnlyCest extends SynapseRestfulTestBase
{
    //NOTE: All Team Items are commented out, because Argus is not on a team

    //ACTIONS DATA
    private $orgId = 542;


    //staff(Minerva) to create referral, note, contact, and appointment
    //for testing the view permissions of the aggregate only staff
    private $staffM = [
        'email' => 'minerva.mcgonagall@mailinator.com',
        'password' => 'password1!',
        'id' => 99440,
        'orgId' => 542,
        'langId' => 1
    ];

    // This staff member has the permission set Hogwarts_Aggregate
    private $aggOnlyStaff = [
        'email' => 'argus.filch@mailinator.com',
        'password' => 'password1!',
        'id' => 99436,
        'orgId' => 542,
        'langId' => 1
    ];

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
        'staff_id' => 99436,
        'notes_student_id' => 99422,
        'comment'=> "Public note created by Argus Filch",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateNote = [
        'organization_id' => 542,
        'staff_id' => 99436,
        'notes_student_id' => 99422,
        'comment'=> "Private note created by Argus Filch",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    /* Argus is not on a team
    private $teamNote = [
        'organization_id' => 542,
        'staff_id' => 99436,
        'notes_student_id' => 99422,
        'comment'=> "Team note created by Argus Filch",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => false, 'teams_share' => true,
            'team_ids' => [['id'=>379, 'is_team_selected'=>true]]]]
    ];*/

    private $publicContact = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99436,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateContact = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99436,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    /* Argus is not on a team
    private $teamContact = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99436,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => false, 'public_share' => false, 'teams_share' => true,
            'team_ids' => [['id'=>379, 'is_team_selected'=>true]]]]
    ];*/

    private $appointment = [
        'organization_id' => 542,
        'person_id' => 99436,
        'attendees' => [['student_id' => 99422]],
        'location' => 'Charms Classroom',
        'detail_id' => 30,
        'slot_start' => '2015-05-23 09:00:00',
        'slot_end' => '2015-05-23 10:00:00'
    ];

    private $publicReferral = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99436,
        'assigned_to_user_id' => 99436,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateReferral = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    /*
     * Argus is not on a team
     *
    private $teamReferral = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99436,
        'assigned_to_user_id' => 99436,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => false, 'teams_share' => true,
            'team_ids' => [['id'=>379, 'is_team_selected'=>true]]]]
    ];

    private $teamGNoteToView = [
        'organization_id' => 542,
        'notes_id' => 217,
        'notes_student_id' => 99422,
        'staff_id' => 99436
    ];

    private $teamHNoteToView = [
        'organization_id' => 542,
        'notes_id' => 219,
        'notes_student_id' => 99411,
        'staff_id' => 99440
    ];

    private $teamGContactToView = [
        'organization_id' => 542,
        'contact_id' => 260,
        'person_student_id' => 99422,
        'person_staff_id' => 99436
    ];

    private $teamHContactToView = [
        'organization_id' => 542,
        'contact_id' => 262,
        'person_student_id' => 99411,
        'person_staff_id' => 99436
    ];

    private $teamGReferralToView = [
        'organization_id' => 542,
        'referral_id' => 675,
        'person_student_id' => 99422,
        'person_staff_id' => 99436
    ];

    private $teamHReferralToView = [
        'organization_id' => 542,
        'referral_id' => 677,
        'person_student_id' => 99411,
        'person_staff_id' => 99436
    ];
    */


    //ACTIONS
    public function testStaffSearchForStudent(ApiAuthTester $I)
    {
        $I->wantTo('Search for student information for a student in my group.');
        $this->_getAPITestRunner($I, $this->aggOnlyStaff,
            'permission/'.$this->orgId.'/mystudents?personId='.$this->aggOnlyStaff['id'], [], null, ["data"=>["organization_id"=>542,"users"=>[]]]);
    }

    public function testStaffViewStudentProfile(ApiAuthTester $I)
    {
        $I->wantTo('View student information of a student in my group.');
        $this->_getAPITestRunner($I, $this->aggOnlyStaff, 'students/'.$this->student['id'], [], 403, []);
    }

    public function testStaffViewTalkingPointsForStudent(ApiAuthTester $I)
    {
        $I->wantTo('View talking points for a student in my group.');
        $this->_getAPITestRunner($I, $this->aggOnlyStaff, 'students/'.$this->student['id'].'/talking_point', [], 403, []);
    }

    public function testStaffViewProfileItemsForStudent(ApiAuthTester $I)
    {
        $I->wantTo('View profile items for a student in my group');
        $this->_getAPITestRunner($I, $this->aggOnlyStaff, 'students/'.$this->student['id'].'/studentdetails', [], 403, []);
    }



    //FEATURES
    public function testStaffViewActivityStreamForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View activity stream (list of notes, contacts, appointments, referrals) for a student in my group.');
        $this->_getAPITestRunner($I, $this->aggOnlyStaff, 'students/activity', $this->getActivityStream, 403, []);
    }

    public function testStaffViewAllNotesForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available notes for a student in my group.');
        $this->_getAPITestRunner($I, $this->aggOnlyStaff, 'students/activity', $this->getStudentNotes, 403, []);
    }

    public function testStaffViewAllContactsForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available contacts for a student in my group.');
        $this->_getAPITestRunner($I, $this->aggOnlyStaff, 'students/'. $this->student['id'] .'/contacts', [], 403, []);
    }

    public function testStaffViewAllAppointmentsForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available appointments for a student in my group.');
        $this->_getAPITestRunner($I, $this->aggOnlyStaff, 'students/'. $this->student['id'] .'/appointments', [], 403, []);
    }

    public function testStaffViewAllReferralsForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available referrals for a student in my group.');
        $this->_getAPITestRunner($I, $this->aggOnlyStaff, 'students/'. $this->student['id'] .'/referrals', [], 403, []);
    }

    public function testStaffCreatePublicNote(ApiAuthTester $I)
    {
        $I->wantTo('Create a public note for a student.');
        $this->_postAPITestRunner($I, $this->aggOnlyStaff, 'notes', $this->publicNote, 403, []);
    }

    public function testStaffCreatePrivateNote(ApiAuthTester $I)
    {
        $I->wantTo('Create a private note for a student.');
        $this->_postAPITestRunner($I, $this->aggOnlyStaff, 'notes', $this->privateNote, 403, []);
    }

    /*
    public function testStaffCreateTeamNote(ApiAuthTester $I)
    {
        $I->wantTo('Create a team note for a student.');
        $this->_postAPITestRunner($I, $this->aggOnlyStaff, 'notes', $this->teamNote, 201, [$this->teamNote]);
    }*/

    public function testStaffCreatePublicContact(ApiAuthTester $I)
    {
        $I->wantTo('Create a public contact for a student.');
        $this->_postAPITestRunner($I, $this->aggOnlyStaff, 'contacts', $this->publicContact, 403, []);
    }

    public function testStaffCreatePrivateContact(ApiAuthTester $I)
    {
        $I->wantTo('Create a private contact for a student.');
        $this->_postAPITestRunner($I, $this->aggOnlyStaff, 'contacts', $this->privateContact, 403, []);
    }

    /*
    public function testStaffCreateTeamContact(ApiAuthTester $I)
    {
        $I->wantTo('Create a team contact for a student.');
        $this->_postAPITestRunner($I, $this->aggOnlyStaff, 'contacts', $this->teamContact, 201, [$this->teamContact]);
    }*/

    public function testStaffCreateAppointment(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Create an appointment for a student.');
        $params = array_merge($this->appointment, ['office_hours_id' => null]);
        $this->_postAPITestRunner($I, $this->aggOnlyStaff, 'appointments/'.$this->orgId.'/'.$this->aggOnlyStaff['id'], $params, 400, []);
    }

    public function testStaffCreatePublicReferral(ApiAuthTester $I)
    {
        $I->wantTo('Create a public referral for a student.');
        $this->_postAPITestRunner($I, $this->aggOnlyStaff, 'referrals', $this->publicReferral, 403, []);
    }

    public function testStaffCreatePrivateReferral(ApiAuthTester $I)
    {
        $I->wantTo('Create a private referral for a student.');
        $this->_postAPITestRunner($I, $this->aggOnlyStaff, 'referrals', $this->privateReferral, 403, []);
    }

    /*
    public function testStaffCreateTeamReferral(ApiAuthTester $I)
    {
        $I->wantTo('Create a team referral for a student.');
        $this->_postAPITestRunner($I, $this->aggOnlyStaff, 'referrals', $this->teamReferral, 201, [$this->teamReferral]);
    }*/

    
    //VIEW ANOTHER STAFFS FEATURES
    public function testViewNote(ApiAuthTester $I)
    {
        $I->wantTo('Verify aggregate only staff can not view a note');
        $this->_getAPITestRunner($I, $this->aggOnlyStaff, 'notes/222', [], 403, []);
    }

    public function testViewContact(ApiAuthTester $I)
    {
        $I->wantTo('Verify aggregate only staff can not view a contact');
        $this->_getAPITestRunner($I, $this->aggOnlyStaff, 'contacts/263', [], 403, []);
    }

    public function testViewAppointment(ApiAuthTester $I)
    {
        $I->wantTo('Verify aggregate only staff can not view a appointment');
        $this->_getAPITestRunner($I, $this->aggOnlyStaff, 'appointments/'.$this->orgId.'/'.$this->aggOnlyStaff['id'].'/appointmentId?appointmentId=988', [], 403, []);
    }

    public function testViewReferral(ApiAuthTester $I)
    {
        $I->wantTo('Verify aggregate only staff can not view a referral');
        $this->_getAPITestRunner($I, $this->aggOnlyStaff, 'referrals/680', [], 403, []);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
