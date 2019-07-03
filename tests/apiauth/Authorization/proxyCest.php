<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';

class proxyCest extends SynapseRestfulTestBase
{
    private $primaryCoordinatorProxyUser = [
        'email' => 'albus.dumbledore@mailinator.com',
        'password' => 'password1!',
        'id' => 99704,
        'orgId' => 542,
        'langId' => 1
    ];

    private $technicalCoordinatorProxyUser = [
        'email' => 'armando.dippet@mailinator.com',
        'password' => 'password1!',
        'id' => 99707,
        'orgId' => 542,
        'langId' => 1
    ];

    private $nonTechnicalCoordinatorProxyUser = [
        'email' => 'phineas.nigellus.black@mailinator.com',
        'password' => 'password1!',
        'id' => 99708,
        'orgId' => 542,
        'langId' => 1
    ];

    private $mapworksAdminProxyUser = [
        'email' => 'andrew.wilson@mailinator.com',
        'password' => 'password1!',
        'id' => 99385,
        'orgId' => 542,
        'langId' => 1
    ];

    private $staffProxyUser = [
        'email' => 'minerva.mcgonagall@mailinator.com',
        'password' => 'password1!',
        'id' => 99440,
        'orgId' => 542,
        'langId' => 1
    ];

    private $primaryCoordinatorDifferentUniversityProxyUser = [
        'email' => 'bad.guy@mailinator.com',
        'password' => 'password1!',
        'id' => 99705,
        'orgId' => 543,
        'langId' => 1
    ];



    private $primaryCoordinatorProxyData = [
        'campus_id' => 542,
        'id' => 1,
        'proxy_user_id' => 99440,
        'user_id' => 99704
    ];

    private $technicalCoordinatorProxyData = [
        'campus_id' => 542,
        'id' => 1,
        'proxy_user_id' => 99440,
        'user_id' => 99707
    ];

    private $nonTechnicalCoordinatorProxyData = [
        'campus_id' => 542,
        'id' => 1,
        'proxy_user_id' => 99440,
        'user_id' => 99708
    ];

    private $mapworksAdminProxyData = [
        'campus_id' => 542,
        'id' => 1,
        'proxy_user_id' => 99440,
        'user_id' => 99385
    ];

    private $staffProxyData = [
        'campus_id' => 542,
        'id' => 1,
        'proxy_user_id' => 99441, //Oliver
        'user_id' => 99440
    ];

    private $primaryCoordinatorDifferentUniversityProxyData = [
        'campus_id' => 542,
        'id' => 1,
        'proxy_user_id' => 99440,
        'user_id' => 99705
    ];

    private $accessDeniedResponse = [
        'errors' => [
            0 => "You do not have EBI Admin/Coordinator Access"
        ]
    ];


    public function testProxyPrimaryCoordinator(ApiAuthTester $I)
    {
        $I->wantTo('Proxy as a staff in my university as the primary coordinator');
        $this->_postAPITestRunner($I, $this->primaryCoordinatorProxyUser, 'proxy', $this->primaryCoordinatorProxyData, 201, []);
    }

    public function testProxyTechnicalCoordinator(ApiAuthTester $I, $scenario)
    {
        
        $I->wantTo('Try (and fail) to proxy staff in my same university without being the primary coordinator (technical coordinator should not work');
        $this->_postAPITestRunner($I, $this->technicalCoordinatorProxyUser, 'proxy', $this->technicalCoordinatorProxyData, 400, [$this->accessDeniedResponse]);
    }

    public function testProxyNonTechnicalCoordinator(ApiAuthTester $I, $scenario)
    {

        $I->wantTo('Try (and fail) to proxy staff in my same university without being the primary coordinator (non-technical coordinator should not work');
        $this->_postAPITestRunner($I, $this->nonTechnicalCoordinatorProxyUser, 'proxy', $this->nonTechnicalCoordinatorProxyData, 400, [$this->accessDeniedResponse]);
    }

    public function testProxyMapworksAdmin(ApiAuthTester $I)
    {
        $I->wantTo('Proxy as a staff in a university as a mapworks admin');
        $this->_postAPITestRunner($I, $this->mapworksAdminProxyUser, 'proxy', $this->mapworksAdminProxyData, 201, []);
    }

    public function testProxyStaff(ApiAuthTester $I)
    {
        $I->wantTo('Try (and fail) to proxy staff in my same university as a non coordinator staff memeber');
        $this->_postAPITestRunner($I, $this->staffProxyUser, 'proxy', $this->staffProxyData, 400, $this->accessDeniedResponse);
    }

    public function testProxyPrimaryCoordinatorDifferentUniversity(ApiAuthTester $I)
    {
        $I->wantTo('Try (and fail) to proxy a user from a different university');
        $this->_postAPITestRunner($I, $this->primaryCoordinatorDifferentUniversityProxyUser, 'proxy', $this->primaryCoordinatorDifferentUniversityProxyData, 400, $this->accessDeniedResponse);
    }






    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
