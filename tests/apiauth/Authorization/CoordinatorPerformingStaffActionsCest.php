<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class CoordinatorPerformingStaffActionsCest extends SynapseRestfulTestBase
{

    private $coordinator = [
        'email' => 'albus.dumbledore@mailinator.com',
        'password' => 'password1!',
        'id' => 99704,
        'orgId' => 542,
        'langId' => 1
    ];

    private $invalidCoordinator = [
        'email' => 'bad.guy@mailinator.com',
        'password' => 'password1!'
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



    public function testCoordinatorSearchForStudent(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can find student information at Institute A.');
        $testData = [
            ['organization_id' => 542],
            $this->studentInSearch
        ];
        $this->_getAPITestRunner($I, $this->coordinator,
            'permission/'.$this->coordinator['orgId'].'/mystudents?personId='.$this->coordinator['id'],
            [], 200, $testData);
    }

    public function testInvalidCoordinatorSearchForStudent(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to find student information at Institute A.');
        $this->_getAPITestRunner($I, $this->invalidCoordinator,
            'permission/'.$this->coordinator['orgId'].'/mystudents?personId='.$this->coordinator['id'],
            [], 403, ['data' => []]);
    }

    public function testCoordinatorViewStudentProfile(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view student information at Institute A.');
        $testData = [$this->student];
        $this->_getAPITestRunner($I, $this->coordinator,
            'students/'.$this->student['id'], [], 200, $testData);
    }

    public function testInvalidCoordinatorViewStudentProfile(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view student information at Institute A.');
        $this->_getAPITestRunner($I, $this->invalidCoordinator,
            'students/'.$this->student['id'], [], 403, ['data' => []]);
    }

    // There aren't actually any talking points in our data, so this just tests for code 200.
    public function testCoordinatorViewTalkingPoints(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view talking points for students at Institute A.');
        $this->_getAPITestRunner($I, $this->coordinator,
            'students/'.$this->student['id'].'/talking_point', [], 200, []);
    }

    public function testInvalidCoordinatorViewTalkingPoints(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view talking points for students at Institute A.');
        $this->_getAPITestRunner($I, $this->invalidCoordinator,
            'students/'.$this->student['id'].'/talking_point', [], 403, []);
    }

    public function testCoordinatorViewProfileItems(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view profile items for students at Institute A.');
        $this->_getAPITestRunner($I, $this->coordinator, 'students/'.$this->student['id'].'/studentdetails', [], 200, $this->studentProfileItems);
    }

    public function testInvalidCoordinatorViewProfileItems(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view profile items for students at Institute A.');
        $this->_getAPITestRunner($I, $this->invalidCoordinator, 'students/'.$this->student['id'].'/studentdetails', [], 403, []);
    }
    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }


}