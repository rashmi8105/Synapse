<?php
require_once(dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';

class AdminSystemAlertCest extends SynapseRestfulTestBase
{
    private $admin = [
        'email' => 'david.warner@gmail.com',
        'password' => 'ramesh@1974',
        'id' => 99706,
        'orgId' => -1,
        'langId' => 1,
        'type' => 'admin'
    ];

    private $postSystemAlert = [
        'end_date' => "2015-08-05T00:00:00-05:00",
        'end_date_time' => "2015-08-05 11:00 PM",
        'end_time' => "11:00 PM",
        'is_enabled' => "1",
        'message' => "System Alert Test Message",
        'start_date' => "2015-08-05T00:00:00-05:00",
        'start_date_time' => "2015-08-05 10:00 PM",
        'start_time' => "10:00 PM"
    ];

    private $postSystemAlertResponse = [
        'end_date_time' => "2015-08-05 11:00 PM",
        'id' => 1,
        'message' => "System Alert Test Message",
        'start_date_time' => "2015-08-05 10:00 PM"
    ];

    //POST System Alert
    public function testSendSystemAlert(ApiAuthTester $I)
    {
        $I->wantTo('Send a system alert to all Mapworks users');
        $this->_postAPITestRunner($I, $this->admin, 'alerts', $this->postSystemAlert, 201, [$this->postSystemAlertResponse]);
    }
}
