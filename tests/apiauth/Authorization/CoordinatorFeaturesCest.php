<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';

class CoordinatorFeaturesCest extends SynapseRestfulTestBase
{
    // Note: There is currently no validation for uniqueness (e.g., two appointments can be created for
    // the same person at the same time). In these tests, we used this fact to be lazy: the valid and
    // invalid coordinator create the same items as each other.

    private $coordinator = [
        'email' => 'albus.dumbledore@mailinator.com',
        'password' => 'password1!',
        'id' => 99704,
        'orgId' => 542,
        'langId' => 1
    ];

    private $invalidCoordinator = [
        'email' => 'bad.guy@mailinator.com',
        'password' => 'password1!',

        'langId' => 1
    ];
    /*
    private $invalidCoordinator = [
        'email' => 'igor.karkaroff@drumstrang.com',
        'password' => 'password1!',
        'id' => 99705,
        'orgId' => 543,
        'langId' => 1
    ];
    */

    private $student = [
        'person_student_id' => 99422
    ];

    private $getStudentNotes = [
        'student-id' => 99422,
        'category' => 'note',
        'is-interaction' => false
    ];

    private $createStudentNote = [

        'comment'=> "Once Again, I must ask too much of you",
        'notes_student_id' => '99422',
        'organization_id' => 542,
        'reason_category_subitem_id' => 19,
        'share_options' => [0 => ['private_share' => false, 'public_share'=> true, 'teams_share' => false, 'team_ids' => []]],
        'staff_id' => 99704
    ];

    private $contact = [
        'organization_id' => 542,
        'person_student_id' => '99422',
        'person_staff_id' => 99704,
        'reason_category_subitem_id' => 33,
        'contact_type_id' => 13,
        'comment' => 'test public contact',
        'date_of_contact' => '10/10/2015',
        'share_options' => [['private_share' => false, 'public_share'=> true, 'teams_share' => false, 'team_ids' => []]],
    ];

    private $appointment = [
        'organization_id' => 542,
        'person_id' => 99704,
        'attendees' => [['student_id' => 99422]],
        'location' => 'Charms Classroom',
        'detail_id' => 30,
        'slot_start' => '2015-05-23 09:00:00',
        'slot_end' => '2015-05-23 10:00:00',
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $referral = [
        'organization_id' => 542,
        'person_student_id' => '99422',
        'person_staff_id' => 99704,
        'assigned_to_user_id' => 99440,
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share'=> true, 'teams_share' => false, 'team_ids' => []]],
    ];

    private $noteToView = [
        'organization_id' => 542,
        'notes_id' => 213,
        'notes_student_id' => '99422',
        'staff_id' => 99445
    ];

    private $contactToView = [
        'organization_id' => 542,
        'contact_id' => 256,
        'person_student_id' => '99422',
        'person_staff_id' => 99445
    ];

    private $appointmentToView = [
        'organization_id' => '542',
        'appointment_id' => 994,
        'person_id' => '99704'
    ];

    private $referralToView = [
        'organization_id' => 542,
        'referral_id' => 671,
        //'person_student_id' => 99422,

        'person_staff_id' => 99445
    ];

    
    public function testCoordinatorViewNotes(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator at Institute A, I can view notes for my student at Institute A');
        $authData = ['email' => $this->coordinator['email'], 'password' => $this->coordinator['password']];

        $this->_getAPITestRunner($I, $authData, 'students/activity', $this->getStudentNotes, 200, ['data' =>['person_id' => $this->getStudentNotes['student-id']]]);

    }
    public function testInvalidCoordinatorViewNotes(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view notes of a Student at Institute A' );
        $authData = ['email' => $this->invalidCoordinator['email'], 'password' => $this->invalidCoordinator['password']];

        $this->_getAPITestRunner($I, $authData, 'students/activity', $this->getStudentNotes, 403, ['data' =>[]]);

    }
    public function testCoordinatorViewContacts(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view contacts for my student at Institute A.');
        $authData = ['email' => $this->coordinator['email'], 'password' => $this->coordinator['password']];

        $this->_getAPITestRunner($I, $authData, 'students/'. $this->student['person_student_id'] .'/contacts', [], 200, ['data' => $this->student]);

    }
    public function testInvalidCoordinatorViewContacts(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view contacts of a student at Institute A.');
        $authData = ['email' => $this->invalidCoordinator['email'], 'password' => $this->invalidCoordinator['password']];

        $this->_getAPITestRunner($I, $authData, 'students/'. $this->student['person_student_id'] .'/contacts', [], 403, ['data' => []]);

    }
    public function testCoordinatorViewAppointments(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view appointments for my student at Institute A.');
        $authData = ['email' => $this->coordinator['email'], 'password' => $this->coordinator['password']];

        $this->_getAPITestRunner($I, $authData, 'students/'. $this->student['person_student_id'] .'/appointments', [], 200, ['data' => $this->student]);

    }
    public function testInvalidCoordinatorViewAppointments(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view appointments for students at Institute A.');
        $authData = ['email' => $this->invalidCoordinator['email'], 'password' => $this->invalidCoordinator['password']];

        $this->_getAPITestRunner($I, $authData, 'students/'. $this->student['person_student_id'] .'/appointments', [], 403, ['data' =>[]]);

    }
    public function testCoordinatorViewReferrals(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view referrals for my student at Institute A.');
        $authData = ['email' => $this->coordinator['email'], 'password' => $this->coordinator['password']];

        $this->_getAPITestRunner($I, $authData, 'students/'. $this->student['person_student_id'] .'/referrals', [], 200, ['data' => $this->student]);

    }
    public function testInvalidCoordinatorViewReferrals(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view referrals for students at Institute A.');
        $authData = ['email' => $this->invalidCoordinator['email'], 'password' => $this->invalidCoordinator['password']];

        $this->_getAPITestRunner($I, $authData, 'students/'. $this->student['person_student_id'] .'/referrals', [], 403, ['data' => []]);

    }

    public function testCoordinatorCreateNotes(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('As a Coordinator for Institute A, I can create a note for my student at Institute A.');
        $authData = ['email' => $this->coordinator['email'], 'password' => $this->coordinator['password']];

        $this->_postAPITestRunner($I, $authData, 'notes', $this->createStudentNote, 201, [$this->createStudentNote]);
    }

    // Created because of unusual API used on the front end for retrieving notes
    // Dependent on the previous CreateNotes Test
    public function testCoordinatorVerifyCreatedNote(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('As a Coordinator for Institute A, I can verify that my created note was added to Institute A');
        $authData = ['email' => $this->coordinator['email'], 'password' => $this->coordinator['password']];
        $this->_getAPITestRunner($I, $authData, 'students/activity?student-id='. $this->createStudentNote['notes_student_id'] . '&category=note&is-interaction=false', [], 200, ['data' =>['activity_description' => $this->createStudentNote['comment']]]);
    }

    public function testInvalidCoordinatorCreateNotes(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to create notes for students at Institute A.');
        $authData = ['email' => $this->invalidCoordinator['email'], 'password' => $this->invalidCoordinator['password']];

        $this->_postAPITestRunner($I, $authData, 'notes', $this->createStudentNote, 403, ['data' => []]);
    }

    public function testCoordinatorCreateContact(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('As a Coordinator for Institute A, I can create a contact for my student at Institute A.');
        $this->_postAPITestRunner($I, $this->coordinator, 'contacts', $this->contact, 201, [$this->contact]);
    }

    public function testInvalidCoordinatorCreateContact(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to create a contact for students at Institute A.');
        $this->_postAPITestRunner($I, $this->invalidCoordinator, 'contacts', $this->contact, 403, ['data' => []]);
    }

    public function testCoordinatorCreateAppointment(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('As a Coordinator for Institute A, I can create an appointment for myself.');
        $params = array_merge($this->appointment, ['office_hours_id' => null]);
        $this->_postAPITestRunner($I, $this->coordinator,
            'appointments/'.$this->coordinator['orgId'].'/'.$this->coordinator['id'],
            $params, 201, [$this->appointment]);
    }

    public function testInvalidCoordinatorCreateAppointment(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to create appointments for other staff at Institute A.');
        $params = array_merge($this->appointment, ['office_hours_id' => null]);
        $this->_postAPITestRunner($I, $this->invalidCoordinator,
            'appointments/'.$this->coordinator['orgId'].'/'.$this->coordinator['id'],
            $params, 403, ['data' => []]);
    }

    public function testCoordinatorCreateReferral(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('As a Coordinator for Institute A, I can create an referral for my student at Institute A.');
        $this->_postAPITestRunner($I, $this->coordinator, 'referrals', $this->referral, 201, [$this->referral]);
    }

    public function testInvalidCoordinatorCreateReferral(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to create referrals for students at Institute A.');
        $this->_postAPITestRunner($I, $this->invalidCoordinator, 'referrals', $this->referral, 403, ['data' => []]);
    }

    public function testCoordinatorViewNote(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view a specific note for my student at Institute A.');
        $this->_getAPITestRunner($I, $this->coordinator, 'notes/213', [], 200, [$this->noteToView]);
    }

    public function testInvalidCoordinatorViewNote(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view a specific note for a student at Institute A.');
        $this->_getAPITestRunner($I, $this->invalidCoordinator, 'notes/213', [], 403, ['data' => []]);
    }

    public function testCoordinatorViewContact(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view a specific contact for my student at Institute A.');
        $this->_getAPITestRunner($I, $this->coordinator, 'contacts/256', [], 200, [$this->contactToView]);
    }

    public function testInvalidCoordinatorViewContact(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view a specific contact for a student at Institute A.');
        $this->_getAPITestRunner($I, $this->invalidCoordinator, 'contacts/256', [], 403, ['data' => []]);
    }

    public function testCoordinatorViewAppointment(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('As a Coordinator for Institute A, I can view a specific appointment for myself at Institute A.');
        $this->_getAPITestRunner($I, $this->coordinator,
            'appointments/'.$this->coordinator['orgId'].'/'.$this->coordinator['id'].'/appointmentId?appointmentId=994',
            [], 200, [$this->appointmentToView]);
    }

    public function testInvalidCoordinatorViewAppointment(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view a specific appointment for staff at Institute A.');
        $this->_getAPITestRunner($I, $this->invalidCoordinator,
            'appointments/'.$this->coordinator['orgId'].'/'.$this->coordinator['id'].'/appointmentId?appointmentId=984',
            [], 403, ['data' => []]);
    }

    public function testCoordinatorViewReferral(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view a specific referral for my student at Institute A.');
        $this->_getAPITestRunner($I, $this->coordinator, 'referrals/671', [], 200, [$this->referralToView]);
    }

    public function testInvalidCoordinatorViewReferral(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view a specific referral for students at Institute A.');
        $this->_getAPITestRunner($I, $this->invalidCoordinator, 'referrals/671', [], 403, ['data' => []]);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
