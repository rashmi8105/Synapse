<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class AuthReferralCest extends SynapseRestfulTestBase
{
    // In all these tests, Minerva McGonagall is creating referrals assigned to other staff members,
    // or assigned to herself with another staff member as an interested party.  A staff member should only be able
    // to be assigned a referral or be listed as an interested party on a referral if he/she has permission to
    // receive referrals and has a connection to the student.

    private $staffM = [
        'email' => 'minerva.mcgonagall@mailinator.com',
        'password' => 'password1!',
        'id' => 99440,
        'orgId' => 542,
        'langId' => 1
    ];

   private $noPermissionAccessStaff = [
        'email' => 'armando.dippet@mailinator.com',
        'password' => 'password1!',
        'id' => 99707,
        'orgId' => 542,
        'langId' => 1
    ];
    
    private $publicReferralP = [
        'organization_id' => 542,
        'person_student_id' => '99422',
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99442,     // Percy Weasley (full access permission set, with connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateReferralP = [
        'organization_id' => 542,
        'person_student_id' => '99422',
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99442,     // Percy Weasley (full access permission set, with connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicReferralS = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99447,     // Severus Snape (full access permission set, with no connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateReferralS = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99447,     // Severus Snape (full access permission set, with no connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicReferralR = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99707,     // 99710 Remus Lupin (minimum access permission set, with connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateReferralR = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99707,     // 99710 Remus Lupin (minimum access permission set, with connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicReferralA = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99436,     // Argus Filch (aggregate only permission set, with connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateReferralA = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99436,     // Argus Filch (aggregate only permission set, with connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicReferralB = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99705,     // Bad Guy (full access permission set, from another institution)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateReferralB = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99705,     // Bad Guy (full access permission set, from another institution)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicInterestedP = [
        'organization_id' => 542,
        'person_student_id' => '99422',
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'interested_parties' => [['id' => 99442]],     // Percy Weasley (full access permission set, with connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateInterestedP = [
        'organization_id' => 542,
        'person_student_id' => '99422',
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'interested_parties' => [['id' => 99442]],     // Percy Weasley (full access permission set, with connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicInterestedS = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'interested_parties' => [['id' => 99447]],     // Severus Snape (full access permission set, with no connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateInterestedS = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'interested_parties' => [['id' => 99447]],     // Severus Snape (full access permission set, with no connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicInterestedR = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'interested_parties' => [['id' => 99707]],     // 99710 Remus Lupin (minimum access permission set, with connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateInterestedR = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'interested_parties' => [['id' => 99707]],     // 99710 Remus Lupin (minimum access permission set, with connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicInterestedA = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'interested_parties' => [['id' => 99436]],     // Argus Filch (aggregate only permission set, with connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateInterestedA = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'interested_parties' => [['id' => 99436]],     // Argus Filch (aggregate only permission set, with connection to student)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $publicInterestedB = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'interested_parties' => [['id' => 99705]],     // Bad Guy (full access permission set, from another institution)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $privateInterestedB = [
        'organization_id' => 542,
        'person_student_id' => 99422,
        'person_staff_id' => 99440,
        'assigned_to_user_id' => 99440,
        'interested_parties' => [['id' => 99705]],     // Bad Guy (full access permission set, from another institution)
        'reason_category_subitem_id' => 19,
        'share_options' => [['private_share' => true, 'public_share' => false, 'teams_share' => false, 'team_ids' => []]]
    ];


    public function testCreatePublicReferralAssignedToFullAccessStaffWithConnectionToStudent(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Verify a public referral can be assigned to a staff member with a connection to the student and permission to receive referrals.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->publicReferralP, 201, [$this->publicReferralP]);
    }

    public function testCreatePrivateReferralAssignedToFullAccessStaffWithConnectionToStudent(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Verify a private referral can be assigned to a staff member with a connection to the student and permission to receive referrals.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->privateReferralP, 201, [$this->privateReferralP]);
    }

    public function testCreatePublicReferralAssignedToFullAccessStaffWithoutConnectionToStudent(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a public referral cannot be assigned to a staff member with permission to receive referrals, but with no connection to the student.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->publicReferralS, 403, []);
    }

    public function testCreatePrivateReferralAssignedToFullAccessStaffWithoutConnectionToStudent(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a private referral cannot be assigned to a staff member with permission to receive referrals, but with no connection to the student.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->privateReferralS, 403, []);
    }

    public function testCreatePublicReferralAssignedToMinAccessStaff(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a public referral cannot be assigned to a staff member with permission see the student, but without permission to receive referrals.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->publicReferralR, 403, []);
    }

    public function testCreatePrivateReferralAssignedToMinAccessStaff(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a private referral cannot be assigned to a staff member with permission see the student, but without permission to receive referrals.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->privateReferralR, 403, []);
    }

    public function testCreatePublicReferralAssignedToAggOnlyStaff(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a public referral cannot be assigned to a staff member with aggregate only permission set.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->publicReferralA, 403, []);
    }

    public function testCreatePrivateReferralAssignedToAggOnlyStaff(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a private referral cannot be assigned to a staff member with aggregate only permission set.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->privateReferralA, 403, []);
    }

    public function testCreatePublicReferralAssignedToStaffFromAnotherInstitution(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a public referral cannot be assigned to a staff member at another institution.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->publicReferralB, 403, []);
    }

    public function testCreatePrivateReferralAssignedToStaffFromAnotherInstitution(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a private referral cannot be assigned to a staff member at another institution.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->privateReferralB, 403, []);
    }

    public function testCreatePublicReferralInterestedFullAccessStaffWithConnectionToStudent(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Verify a staff member with a connection to the student and permission to receive referrals can be an interested party on a public referral.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->publicInterestedP, 201, [$this->publicInterestedP]);
    }

    public function testCreatePrivateReferralInterestedFullAccessStaffWithConnectionToStudent(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Verify a staff member with a connection to the student and permission to receive referrals can be an interested party on a private referral.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->privateInterestedP, 201, [$this->privateInterestedP]);
    }

    public function testCreatePublicReferralInterestedFullAccessStaffWithoutConnectionToStudent(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a staff member without a connection to the student cannot be an interested party on a public referral.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->publicInterestedS, 403, []);
    }

    public function testCreatePrivateReferralInterestedFullAccessStaffWithoutConnectionToStudent(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a staff member without a connection to the student cannot be an interested party on a private referral.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->privateInterestedS, 403, []);
    }

    public function testCreatePublicReferralInterestedMinAccessStaff(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a staff member with a connection to the student, but without permission to receive referrals, cannot be an interested party on a public referral.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->publicInterestedR, 403, []);
    }

    public function testCreatePrivateReferralInterestedMinAccessStaff(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a staff member with a connection to the student, but without permission to receive referrals, cannot be an interested party on a private referral.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->privateInterestedR, 403, []);
    }

    public function testCreatePublicReferralInterestedAggOnlyStaff(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a staff member with a connection to the student, but with aggregate only permission set, cannot be an interested party on a public referral.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->publicInterestedA, 403, []);
    }

    public function testCreatePrivateReferralInterestedAggOnlyStaff(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a staff member with a connection to the student, but with aggregate only permission set, cannot be an interested party on a private referral.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->privateInterestedA, 403, []);
    }

    public function testCreatePublicReferralInterestedStaffFromAnotherInstitution(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a staff member from another institution cannot be an interested party on a public referral.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->publicInterestedB, 403, []);
    }

    public function testCreatePrivateReferralInterestedStaffFromAnotherInstitution(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a staff member from another institution cannot be an interested party on a private referral.');
        $this->_postAPITestRunner($I, $this->staffM, 'referrals', $this->privateInterestedB, 403, []);
    }

    public function databaseReload()
    {
        // Cleaning up data at end of file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }

}
