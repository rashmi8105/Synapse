<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class CreateNotViewPermissionSetCest extends SynapseRestfulTestBase
{

    private $orgId = 542;

    // This staff member has permission to create notes, contacts, appointments, and referrals, but not view them.
    private $staffK = [
        'email' => 'kingsley.shacklebolt@mailinator.com',
        'password' => 'password1!',
        'id' => 99711,
        'orgId' => 542,
        'langId' => 1
    ];


    public function testViewPublicNote(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a staff member with permission to create but not view notes cannot view a public note.');
        $this->_getAPITestRunner($I, $this->staffK, 'notes/222', [], 403, []);
    }

    public function testViewPublicContact(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a staff member with permission to create but not view contacts cannot view a public contact.');
        $this->_getAPITestRunner($I, $this->staffK, 'contacts/263', [], 403, []);
    }

    public function testViewAppointment(ApiAuthTester $I)
    {
        $I->wantTo('Verify a staff member with permission to create but not view appointments cannot view an appointment.');
        $this->_getAPITestRunner($I, $this->staffK,
            'appointments/'.$this->orgId.'/'.$this->staffK['id'].'/appointmentId?appointmentId=988', [], 403, []);
    }

    public function testViewPublicReferral(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Verify a staff member with permission to create but not view referrals cannot view a public referral.');
        $this->_getAPITestRunner($I, $this->staffK, 'referrals/680', [], 403, []);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }


}
