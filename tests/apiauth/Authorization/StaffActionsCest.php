<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class StaffActionsCest extends SynapseRestfulTestBase
{
    private $orgId = 542;

    // Both these staff members have permission set Hogwarts_FullAccess.

    private $staffM = [
        'email' => 'minerva.mcgonagall@mailinator.com',
        'password' => 'password1!',
        'id' => 99440,
        'orgId' => 542,
        'langId' => 1
    ];

    private $staffS = [
        'email' => 'severus.snape@mailinator.com',
        'password' => 'password1!',
        'id' => 99447,
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

    public function testStaffSearchForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('Search for student information for a student in my group.');
        $testData = [
            ['organization_id' => 542],
            $this->studentInSearch
        ];
        $this->_getAPITestRunner($I, $this->staffM,
            'permission/'.$this->orgId.'/mystudents?personId='.$this->staffM['id'],
            [], 200, $testData);
    }

    public function testStaffSearchForStudentsOfAnotherStaff(ApiAuthTester $I)
    {
        $I->wantTo("Try to access another staff member's list of students.");
        $this->_getAPITestRunner($I, $this->staffS,
            'permission/'.$this->orgId.'/mystudents?personId='.$this->staffM['id'],
            [], 403, []);
    }

    public function testStaffViewAccessibleStudentProfile(ApiAuthTester $I)
    {
        $I->wantTo('View student information of a student in my group.');
        $this->_getAPITestRunner($I, $this->staffM, 'students/'.$this->student['id'], [], 200, [$this->student]);
    }

    public function testStaffViewInaccessibleStudentProfile(ApiAuthTester $I)
    {
        $I->wantTo('View student information of a student in none of my groups.');
        $this->_getAPITestRunner($I, $this->staffS, 'students/'.$this->student['id'], [], 403, []);
    }

    // There aren't actually any talking points in our data, so this just tests for code 200.
    public function testStaffViewTalkingPointsForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View talking points for a student in my group.');
        $this->_getAPITestRunner($I, $this->staffM, 'students/'.$this->student['id'].'/talking_point', [], 200, []);
    }

    public function testStaffViewTalkingPointsForInaccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View talking points for a student in none of my groups.');
        $this->_getAPITestRunner($I, $this->staffS, 'students/'.$this->student['id'].'/talking_point', [], 403, []);
    }

    public function testStaffViewProfileItemsForAccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View profile items for a student in my group.');
        $this->_getAPITestRunner($I, $this->staffM, 'students/'.$this->student['id'].'/studentdetails', [], 200, $this->studentProfileItems);
    }

    public function testStaffViewProfileItemsForInaccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo('View profile items for a student in none of my groups.');
        $this->_getAPITestRunner($I, $this->staffS, 'students/'.$this->student['id'].'/studentdetails', [], 403, []);
    }
    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }


}