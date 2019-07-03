<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class InvalidCoordinatorStaticListCest extends SynapseRestfulTestBase
{
    // This function will test what happens if a Invalid Coordinator has a Static List
    // And then tries to add students from another Organization, and similar tests
    // Different variations of this needs to be added for more tests

    private $invalidCoordinator = [
        'email' => 'bad.guy@mailinator.com',
        'password' => 'password1!',
        'id' => 99705,
        'orgId' => 543,
        'langId' => 1
    ];

    private $addStudent = [
        'org_id' => "542",
        'staticlist_id' => 8,
        'student_edit_type' => "add",
        'student_id' => "S004",
    ];
    private $addWrongStudentWithinOrganization = [
        'org_id' => "543",
        'staticlist_id' => 8,
        'student_edit_type' => "add",
        'student_id' => "S007",
    ];

    private $shareAStaticList = [
        "org_id" => "542",
        "person_id" => 99447,
    ];

    private $shareAStaticListWithinOrg = [
      "org_id" => "543",
      "person_id" => 99447,
    ];

    private $viewsMyStaticLists = [
        'data' =>
            array (
                'static_list_details' =>
                    array (
                        0 =>
                            array (
                                'staticlist_id' => 8,
                                'created_by' => '99705',
                                'created_by_user_name' => 'Bad Guy',
                                'staticlist_name' => 'Bad Guy Static List',
                                'staticlist_description' => 'This List will be tested on by The Bad Guy',
                                'student_count' => 0,
                            ),
                    ),
            ),

    ];

    private $StaticListNotFound = ['errors' =>
        array (
            0 => 'Staticlist Not Found',
        ),];


   /*||**********************************************************||
     || These tests have a weird set up. Adding and sharing both ||
     || require the organization ID which is what is used for    ||
     || the addition of students into the Static Lists, and the  ||
     || sharing of static lists. This means that the coordinator ||
     || that errors are simply saying that there are not lists in||
     || this organization for the bad guy coordinator to ues     ||
     ||**********************************************************||*/

    public function testViewStaticListTab(ApiAuthTester $I){
        $I->wantTo('I want to view my own Static Lists');
        $this->_getAPITestRunner($I, $this->invalidCoordinator, 'staticlists?org_id=543', [], 200, [$this->viewsMyStaticLists]);

    }

    // The invalid Coordinator will try to add a student from a different organization
    public function testAddIllegalStudent(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('I want to insert a new student into my Static List... From another Organization');
        $this->_putAPITestRunner($I, $this->invalidCoordinator, 'staticlists/8/students', $this->addStudent, 403, []);
    }

    public function testAddIllegalStudentWithinOrg(ApiAuthTester $I){
        $I->wantTo('I want to insert a new student into my Static List... From another Organization But saying they are from my organization');
        $this->_putAPITestRunner($I, $this->invalidCoordinator, 'staticlists/8/students', $this->addWrongStudentWithinOrganization, 400, ['errors' => array (0 => 'Student Not Found',),]);
    }

    public function testShareIllegalStaticListWithFacultyFromAnotherUniversity(ApiAuthTester $I, $scenario){
        $I->wantTo('I am attempting to share my list with a coordinator from another organization');
        $this->_postAPITestRunner($I, $this->invalidCoordinator, 'staticlists/8/shares', $this->shareAStaticList, 400, []);
    }

    public function testShareIllegalStaticListWithFacultyFromAnotherUniversityWithinMyUniversity(ApiAuthTester $I){

        $I->wantTo('I am attempting to share my list with a coordinator from another organization saying that they are in my organization');
        $this->_postAPITestRunner($I, $this->invalidCoordinator, 'staticlists/8/shares', $this->shareAStaticListWithinOrg, 400, []);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }



}
