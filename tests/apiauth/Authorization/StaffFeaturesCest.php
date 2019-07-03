<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';

class StaffFeaturesCest extends SynapseRestfulTestBase
{

    // All these staff members have permission set Hogwarts_FullAccess.
    // Minerva McGonagall and Percy Weasley are together in the group Gryffindor. Alicia Spinnet (id 99410) is a student in this group.
    // Severus Snape is in the group Slytherin.  He should not have access to Alicia Spinnet.
    // Team Gryffindor (TeamG) has two members which are also in the same group (Minerva McGonagall and Percy Weasley).
    // Team Heads of Houses (TeamH) has two members which are in different groups (Minerva McGonagall and Severus Snape).
    // Angelina Johnson (id 99411) is a student that is in both groups (even though this may not make sense...);
    // she is used to test the interaction between groups and teams.

    private $orgId = 542;

    // Staff member who creates notes, appointments, etc.
    private $staffM = [
        'email' => 'minerva.mcgonagall@mailinator.com',
        'password' => 'password1!',
        'id' => 99440,
        'orgId' => 542,
        'langId' => 1
    ];

    // Staff member in the same group.
    private $staffP = [
        'email' => 'percy.weasley@mailinator.com',
        'password' => 'password1!',
        'id' => 99442,
        'orgId' => 542,
        'langId' => 1
    ];

    // Staff member in another group.
    private $staffS = [
        'email' => 'severus.snape@mailinator.com',
        'password' => 'password1!',
        'id' => 99447,
        'orgId' => 542,
        'langId' => 1
    ];

    // For viewing a list of all notes/contacts/appointments/referrals for the student.
    private $student = [
        'person_student_id' => 99410
    ];

    private $getStudentNotes = [
        'student-id' => 99410,
        'category' => 'note',
        'is-interaction' => false
    ];

    private $getActivityStream = [
        'student-id' => 99410,
        'category' => 'all',
        'is-interaction' => false
    ];

    private $publicNote = [
        'organization_id' => 542,
        'staff_id' => 99440,
        'notes_student_id' => '99410',
        'comment'=> "Public note created by Minerva McGonagall",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateNote = [
        'organization_id' => 542,
        'staff_id' => 99440,
        'notes_student_id' => '99410',
        'comment'=> "Private note created by Minerva McGonagall",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $teamNote = [
        'organization_id' => 542,
        'staff_id' => 99440,
        'notes_student_id' => '99410',
        'comment'=> "Team note created by Minerva McGonagall",
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => false, 'teams_share' => true,
            'team_ids' => [['id'=>379, 'is_team_selected'=>true]]]]
    ];

    private $publicContact = [
        'organization_id' => 542,
        'person_student_id' => '99410',
        'person_staff_id' => 99440,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateContact = [
        'organization_id' => 542,
        'person_student_id' => '99410',
        'person_staff_id' => 99440,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $teamContact = [
        'organization_id' => 542,
        'person_student_id' => '99410',
        'person_staff_id' => 99440,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'share_options' => [['private_share' => false, 'public_share' => false, 'teams_share' => true,
            'team_ids' => [['id'=>379, 'is_team_selected'=>true]]]]
    ];

    private $appointment = [
        'organization_id' => 542,
        'person_id' => 99440,
        'attendees' => [['student_id' => 99410]],
        'location' => 'Charms Classroom',
        'detail_id' => 30,
        'slot_start' => '2015-05-23 09:00:00',
        'slot_end' => '2015-05-23 10:00:00',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicReferral = [
        'organization_id' => 542,
        'person_student_id' => '99410',
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateReferral = [
        'organization_id' => 542,
        'person_student_id' => '99410',
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $teamReferral = [
        'organization_id' => 542,
        'person_student_id' => '99410',
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => false, 'teams_share' => true,
            'team_ids' => [['id'=>379, 'is_team_selected'=>true]]]]
    ];

    private $publicNoteToView = [
        'organization_id' => 542,
        'notes_id' => 215,
        'notes_student_id' => '99410',
        'staff_id' => 99440
    ];

    private $privateNoteToView = [
        'organization_id' => 542,
        'notes_id' => 216,
        'notes_student_id' => '99410',
        'staff_id' => 99440
    ];

    private $teamGNoteToView = [
        'organization_id' => 542,
        'notes_id' => 217,
        'notes_student_id' => '99410',
        'staff_id' => 99440
    ];

    private $teamHNoteToView = [
        'organization_id' => 542,
        'notes_id' => 219,
        'notes_student_id' => '99411',
        'staff_id' => 99440
    ];

    private $publicContactToView = [
        'organization_id' => 542,
        'contact_id' => 258,
        'person_student_id' => '99410',
        'person_staff_id' => 99440
    ];

    private $privateContactToView = [
        'organization_id' => 542,
        'contact_id' => 259,
        'person_student_id' => '99410',
        'person_staff_id' => 99440
    ];

    private $teamGContactToView = [
        'organization_id' => 542,
        'contact_id' => 260,
        'person_student_id' => '99410',
        'person_staff_id' => 99440
    ];

    private $teamHContactToView = [
        'organization_id' => 542,
        'contact_id' => 262,
        'person_student_id' => '99411',
        'person_staff_id' => 99440
    ];

    private $appointmentToView = [
        'organization_id' => '542',
        'appointment_id' => 985,
        'person_id' => '99440'
    ];

    private $publicReferralToView = [
        'organization_id' => 542,
        'referral_id' => 673,
        'person_student_id' => '99410',
        'person_staff_id' => 99440
    ];

    private $privateReferralToView = [
        'organization_id' => 542,
        'referral_id' => 674,
        'person_student_id' => '99410',
        'person_staff_id' => 99440
    ];

    private $teamGReferralToView = [
        'organization_id' => 542,
        'referral_id' => 675,
        'person_student_id' => '99410',
        'person_staff_id' => 99440
    ];

    private $teamHReferralToView = [
        'organization_id' => 542,
        'referral_id' => 677,
        'person_student_id' => '99411',
        'person_staff_id' => 99440
    ];


    public function testStaffViewActivityStreamForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View activity stream (list of notes, contacts, appointments, referrals) for a student in my group.');
        $this->_getAPITestRunner($I, $this->staffM, 'students/activity', $this->getActivityStream, 200,
            [['person_id' => $this->student['person_student_id']]]);
    }

    public function testStaffViewActivityStreamForInaccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View activity stream (list of notes, contacts, appointments, referrals) for a student in none of my groups.');
        $this->_getAPITestRunner($I, $this->staffS, 'students/activity', $this->getActivityStream, 403, []);
    }

    public function testStaffViewAllNotesForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available notes for a student in my group.');
        $this->_getAPITestRunner($I, $this->staffM, 'students/activity', $this->getStudentNotes, 200,
            [['person_id' => $this->student['person_student_id']]]);
    }

    public function testStaffViewAllNotesForInaccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available notes for a student in none of my groups.');
        $this->_getAPITestRunner($I, $this->staffS, 'students/activity', $this->getStudentNotes, 403, []);
    }

    public function testStaffViewAllContactsForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available contacts for a student in my group.');
        $this->_getAPITestRunner($I, $this->staffM, 'students/'. $this->student['person_student_id'] .'/contacts', [], 200, [$this->student]);
    }

    public function testStaffViewAllContactsForInaccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available contacts for a student in none of my groups.');
        $this->_getAPITestRunner($I, $this->staffS, 'students/'. $this->student['person_student_id'] .'/contacts', [], 403, []);
    }

    public function testStaffViewAllAppointmentsForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available appointments for a student in my group.');
        $this->_getAPITestRunner($I, $this->staffM, 'students/'. $this->student['person_student_id'] .'/appointments', [], 200, [$this->student]);
    }

    public function testStaffViewAllAppointmentsForInaccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available appointments for a student in none of my groups.');
        $this->_getAPITestRunner($I, $this->staffS, 'students/'. $this->student['person_student_id'] .'/appointments', [], 403, []);
    }

    public function testStaffViewAllReferralsForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available referrals for a student in my group.');
        $this->_getAPITestRunner($I, $this->staffM, 'students/'. $this->student['person_student_id'] .'/referrals', [], 200, [$this->student]);
    }

    public function testStaffViewAllReferralsForInaccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View a list of all available referrals for a student in none of my groups.');
        $this->_getAPITestRunner($I, $this->staffS, 'students/'. $this->student['person_student_id'] .'/referrals', [], 403, []);
    }

    public function testStaffCreatePublicNote(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Create a public note for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->staffM, 'notes', $this->publicNote, 201, [$this->publicNote]);
    }

    public function testStaffCreatePrivateNote(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Create a private note for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->staffM, 'notes', $this->privateNote, 201, [$this->privateNote]);
    }

    public function testStaffCreateTeamNote(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Create a team note for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->staffM, 'notes', $this->teamNote, 201, [$this->teamNote]);
    }

    public function testStaffCreatePublicContact(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Create a public contact for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->staffM, 'contacts', $this->publicContact, 201, [$this->publicContact]);
    }

    public function testStaffCreatePrivateContact(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Create a private contact for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->staffM, 'contacts', $this->privateContact, 201, [$this->privateContact]);
    }

    public function testStaffCreateTeamContact(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Create a team contact for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->staffM, 'contacts', $this->teamContact, 201, [$this->teamContact]);
    }

    public function testStaffCreateAppointment(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Create an appointment for a student (as a staff member with the appropriate permission).');
        $params = array_merge($this->appointment, ['office_hours_id' => null]);
        $this->_postAPITestRunner($I, $this->staffM, 'appointments/'.$this->orgId.'/'.$this->staffM['id'], $params, 201, [$this->appointment]);
    }

    public function testStaffCreatePublicReferral(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Create a public referral for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->publicReferral, 201, [$this->publicReferral]);
    }

    public function testStaffCreatePrivateReferral(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Create a private referral for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->privateReferral, 201, [$this->privateReferral]);
    }

    public function testStaffCreateTeamReferral(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Create a team referral for a student (as a staff member with the appropriate permission).');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->teamReferral, 201, [$this->teamReferral]);
    }

    public function testStaffViewOwnPublicNote(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View my own public note.');
        $this->_getAPITestRunner($I, $this->staffM, 'notes/215', [], 200, [$this->publicNoteToView]);
    }

    public function testStaffFromSameGroupViewPublicNote(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View a public note created by a staff member in the same group.');
        $this->_getAPITestRunner($I, $this->staffP, 'notes/215', [], 200, [$this->publicNoteToView]);
    }

    public function testStaffFromOtherGroupViewPublicNote(ApiAuthTester $I)
    {
        $I->wantTo('View a public note created by a staff member in another group.');
        $this->_getAPITestRunner($I, $this->staffS, 'notes/215', [], 403, []);
    }

    public function testStaffViewOwnPrivateNote(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View my own private note.');
        $this->_getAPITestRunner($I, $this->staffM, 'notes/216', [], 200, [$this->privateNoteToView]);
    }

    public function testStaffFromSameGroupViewPrivateNote(ApiAuthTester $I)
    {
        $I->wantTo('View a private note created by a staff member in the same group.');
        $this->_getAPITestRunner($I, $this->staffP, 'notes/216', [], 403, []);
    }

    public function testStaffFromOtherGroupViewPrivateNote(ApiAuthTester $I)
    {
        $I->wantTo('View a private note created by a staff member in another group.');
        $this->_getAPITestRunner($I, $this->staffS, 'notes/216', [], 403, []);
    }

    public function testStaffViewOwnTeamNote(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View my own team note.');
        $this->_getAPITestRunner($I, $this->staffM, 'notes/217', [], 200, [$this->teamGNoteToView]);
    }

    public function testStaffFromSameGroupViewTeamGNote(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View a team note, as a staff member in the same group and on the team.');
        $this->_getAPITestRunner($I, $this->staffP, 'notes/217', [], 200, [$this->teamGNoteToView]);
    }

    public function testStaffFromOtherGroupViewTeamGNote(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View a team note, as a staff member in another group and not on the team.');
        $this->_getAPITestRunner($I, $this->staffS, 'notes/217', [], 403, []);
    }

    public function testStaffFromSameGroupViewTeamHNote(ApiAuthTester $I)
    {
        $I->wantTo('View a team note, as a staff member in the same group but not on the team.');
        $this->_getAPITestRunner($I, $this->staffP, 'notes/218', [], 403, []);
    }

    public function testStaffFromOtherGroupViewTeamHNote(ApiAuthTester $I)
    {
        $I->wantTo('View a team note, as a staff member in another group but on the team.');
        $this->_getAPITestRunner($I, $this->staffS, 'notes/218', [], 403, []);
    }

    public function testStaffFromOtherGroupViewTeamHNoteAboutAccessibleStudent(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View a team note, as a staff member in another group but on the team and with access to the student.');
        $this->_getAPITestRunner($I, $this->staffS, 'notes/219', [], 200, [$this->teamHNoteToView]);
    }

    public function testStaffViewOwnPublicContact(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View my own public contact.');
        $this->_getAPITestRunner($I, $this->staffM, 'contacts/258', [], 200, [$this->publicContactToView]);
    }

    public function testStaffFromSameGroupViewPublicContact(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View a public contact created by a staff member in the same group.');
        $this->_getAPITestRunner($I, $this->staffP, 'contacts/258', [], 200, [$this->publicContactToView]);
    }

    public function testStaffFromOtherGroupViewPublicContact(ApiAuthTester $I)
    {
        $I->wantTo('View a public contact created by a staff member in another group.');
        $this->_getAPITestRunner($I, $this->staffS, 'contacts/258', [], 403, []);
    }

    public function testStaffViewOwnPrivateContact(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View my own private contact.');
        $this->_getAPITestRunner($I, $this->staffM, 'contacts/259', [], 200, [$this->privateContactToView]);
    }

    public function testStaffFromSameGroupViewPrivateContact(ApiAuthTester $I)
    {
        $I->wantTo('View a private contact created by a staff member in the same group.');
        $this->_getAPITestRunner($I, $this->staffP, 'contacts/259', [], 403, []);
    }

    public function testStaffFromOtherGroupViewPrivateContact(ApiAuthTester $I)
    {
        $I->wantTo('View a private contact created by a staff member in another group.');
        $this->_getAPITestRunner($I, $this->staffS, 'contacts/259', [], 403, []);
    }

    public function testStaffViewOwnTeamContact(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View my own team contact.');
        $this->_getAPITestRunner($I, $this->staffM, 'contacts/260', [], 200, [$this->teamGContactToView]);
    }

    public function testStaffFromSameGroupViewTeamGContact(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View a team contact, as a staff member in the same group and on the team.');
        $this->_getAPITestRunner($I, $this->staffP, 'contacts/260', [], 200, [$this->teamGContactToView]);
    }

    public function testStaffFromOtherGroupViewTeamGContact(ApiAuthTester $I)
    {
        $I->wantTo('View a team contact, as a staff member in another group and not on the team.');
        $this->_getAPITestRunner($I, $this->staffS, 'contacts/260', [], 403, []);
    }

    public function testStaffFromSameGroupViewTeamHContact(ApiAuthTester $I)
    {
        $I->wantTo('View a team contact, as a staff member in the same group but not on the team.');
        $this->_getAPITestRunner($I, $this->staffP, 'contacts/261', [], 403, []);
    }

    public function testStaffFromOtherGroupViewTeamHContact(ApiAuthTester $I)
    {
        $I->wantTo('View a team contact, as a staff member in another group but on the team.');
        $this->_getAPITestRunner($I, $this->staffS, 'contacts/261', [], 403, []);
    }

    public function testStaffFromOtherGroupViewTeamHContactAboutAccessibleStudent(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View a team contact, as a staff member in another group but on the team and with access to the student.');
        $this->_getAPITestRunner($I, $this->staffS, 'contacts/262', [], 200, [$this->teamHContactToView]);
    }

    public function testStaffViewOwnAppointment(ApiAuthTester $I)
    {
        $I->wantTo('View my own appointment.');
        $this->_getAPITestRunner($I, $this->staffM,
            'appointments/'.$this->orgId.'/'.$this->staffM['id'].'/appointmentId?appointmentId=985',
            [], 200, [$this->appointmentToView]);
    }

    // Our current understanding is that all appointments should behave as if they were public.
    public function testStaffFromSameGroupViewAppointment(ApiAuthTester $I)
    {
        $I->wantTo('View an appointment, as a staff member with access to the student.');
        $this->_getAPITestRunner($I, $this->staffP,
            'appointments/'.$this->orgId.'/'.$this->staffM['id'].'/appointmentId?appointmentId=985',
            [], 403, []);
    }

    // Our current understanding is that all appointments should behave as if they were public.
    public function testStaffFromOtherGroupViewAppointment(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('View an appointment, as a staff member with no access to the student.');
        $this->_getAPITestRunner($I, $this->staffS,
            'appointments/'.$this->orgId.'/'.$this->staffM['id'].'/appointmentId?appointmentId=985',
            [], 403, []);
    }

    public function testStaffViewOwnPublicReferral(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View my own public referral.');
        $this->_getAPITestRunner($I, $this->staffM, 'referrals/673', [], 200, [$this->publicReferralToView]);
    }

    public function testStaffFromSameGroupViewPublicReferral(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View a public referral created by a staff member in the same group.');
        $this->_getAPITestRunner($I, $this->staffP, 'referrals/673', [], 200, [$this->publicReferralToView]);
    }

    public function testStaffFromOtherGroupViewPublicReferral(ApiAuthTester $I)
    {
        $I->wantTo('View a public referral created by a staff member in another group.');
        $this->_getAPITestRunner($I, $this->staffS, 'referrals/673', [], 403, []);
    }

    public function testStaffViewOwnPrivateReferral(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View my own private referral.');
        $this->_getAPITestRunner($I, $this->staffM, 'referrals/674', [], 200, [$this->privateReferralToView]);
    }

    public function testStaffFromSameGroupViewPrivateReferral(ApiAuthTester $I)
    {
        $I->wantTo('View a private referral created by a staff member in the same group.');
        $this->_getAPITestRunner($I, $this->staffP, 'referrals/674', [], 403, []);
    }

    public function testStaffFromOtherGroupViewPrivateReferral(ApiAuthTester $I)
    {
        $I->wantTo('View a private referral created by a staff member in another group.');
        $this->_getAPITestRunner($I, $this->staffS, 'referrals/674', [], 403, []);
    }

    public function testStaffViewOwnTeamReferral(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View my own team referral.');
        $this->_getAPITestRunner($I, $this->staffM, 'referrals/675', [], 200, [$this->teamGReferralToView]);
    }

    public function testStaffFromSameGroupViewTeamGReferral(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View a team referral, as a staff member in the same group and on the team.');
        $this->_getAPITestRunner($I, $this->staffP, 'referrals/675', [], 200, [$this->teamGReferralToView]);
    }

    public function testStaffFromOtherGroupViewTeamGReferral(ApiAuthTester $I)
    {
        $I->wantTo('View a team referral, as a staff member in another group and not on the team.');
        $this->_getAPITestRunner($I, $this->staffS, 'referrals/675', [], 403, []);
    }

    public function testStaffFromSameGroupViewTeamHReferral(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View a team referral, as a staff member in the same group but not on the team.');
        $this->_getAPITestRunner($I, $this->staffP, 'referrals/676', [], 403, []);
    }

    public function testStaffFromOtherGroupViewTeamHReferral(ApiAuthTester $I)
    {
        $I->wantTo('View a team referral, as a staff member in another group but on the team.');
        $this->_getAPITestRunner($I, $this->staffS, 'referrals/676', [], 403, []);
    }

    public function testStaffFromOtherGroupViewTeamHReferralAboutAccessibleStudent(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View a team referral, as a staff member in another group but on the team and with access to the student.');
        $this->_getAPITestRunner($I, $this->staffS, 'referrals/677', [], 200, [$this->teamHReferralToView]);
    }
    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }

}
