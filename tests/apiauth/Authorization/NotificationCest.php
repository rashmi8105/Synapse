<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class NotificationCest extends SynapseRestfulTestBase
{

    private $staffM = [
        'email' => 'minerva.mcgonagall@mailinator.com',
        'password' => 'password1!',
        'id' => 99440,
        'orgId' => 542,
        'langId' => 1
    ];

    private $facultyWithUploadedInformation = [
        'email' => 'andrew.wilson@mailinator.com',
        'password' => 'password1!'
    ];

    private $invalidCoordinator = [
        'email' => 'bad.guy@mailinator.com',
        'password' => 'password1!'
    ];

    private $referralNotificationM = [
        'alert_id' => 2730,
        'activity_type' => 'Referral',
        'activity_id' => 671,
        'reason' => 'Class attendance concern ',
        'students' => [
            'student_id' => 99422,
            'student_first_name' => 'Harry ',
            'student_last_name' => 'Potter',
            'student_status' => '1'
        ]
    ];


    // Seeing if it is possible to see your notifications

    public function testReferralNotification(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Verify a staff member who has permission to receive referrals gets a notification of her referral.');
        $this->_getAPITestRunner($I, $this->staffM, 'notification', [], 200, [$this->referralNotificationM]);
    }

    // Delete (or say you have seen) your notifications
    public function testDeleteNotification(ApiAuthTester $I){
        $I->wantTo('Delete my notifications, so they do not notify me anymore');
        // All notification of staffM
        //2728,2730,2732,2733,2736,2737,2739,2740,2742,2743,2745,2746,2748,2749,2756,2757,2761,2767,2778
        $this->_deleteAPITestRunner($I, $this->staffM, 'notification?alert-id=2756,2757,2761,2767,2778', [], 204, []);
    }

    // And let's do that to another faculty
    public function testDeleteAnotherFacultysNotification(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo('Delete another facultys notifications, I should not be able to do this');
        $this->_deleteAPITestRunner($I, $this->staffM, 'notification?alert-id=2730,2729', [], 400, []);
    }

    // and again for another university
    public function testDeleteAnotherCampusesNotifications(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo('I want Delete another facultys notifications from a different instituton, so they do not notify me anymore');
        $this->_deleteAPITestRunner($I, $this->invalidCoordinator, 'notification?alert-id=2736,2738', [], 400, []);
    }

    // This is making sure that there are no downloading url in the notifications
    public function testViewNotificationWithWrongAPI(ApiAuthTester $I, $scenario){
        $scenario->skip("Failed");
        $I->wantTo('Make sure that there are no URLs being passed through the Notifications');
        $this->_getAPITestRunner($I, $this->facultyWithUploadedInformation, 'notification', [], 200, []);

        $I->dontSeeResponseContainsJson(['org_course_upload_file' => 'https://ebi-synapse-bucket.s3.amazonaws.com/course-faculty/errors/542-3390-upload-errors.csv',]);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }

}
