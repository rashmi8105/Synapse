<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';

class nestingCest extends SynapseRestfulTestBase
{
    private $staffMember = [
        'email' => 'minerva.mcgonagall@mailinator.com',
        'password' => 'password1!',
        'id' => 99440,
        'orgId' => 542,
        'langId' => 1
    ];

    private $privateNote = [
        'organization_id' => 542,
        'notes_id' => 225,
        'notes_student_id' => 99422,
        'staff_id' => 99704
    ];

    private $publicNote = [
        'organization_id' => 542,
        'notes_id' => 224,
        'notes_student_id' => 99422,
        'staff_id' => 99704
    ];


    public function testNonCreatorViewNestedPrivateNote(ApiAuthTester $I)
    {
        $I->wantTo('As a Faculty I am unable to view a private note even though it is nested under a public referral');
        $this->_getAPITestRunner($I, $this->staffMember, 'notes/225', $this->privateNote, 403, []);
    }

    public function testNonCreatorViewNestedPublicNote(ApiAuthTester $I)
    {
        $I->wantTo('As a Faculty I am able to view a public note, even though it is nested under a private referral');
        $this->_getAPITestRunner($I, $this->staffMember, 'notes/224', $this->publicNote, 200, []);
    }
    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}