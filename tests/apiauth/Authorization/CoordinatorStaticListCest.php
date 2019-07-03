<?php

require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';

class CoordinatorStaticListCest extends SynapseRestfulTestBase
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
    private $staticLists = [
        'data' =>
            array (
                'static_list_details' =>
                    array (
                        0 =>
                            array (
                                'staticlist_id' => 4,
                                'created_by' => '99704',
                                'created_by_user_name' => 'Albus Dumbledore',
                                'staticlist_name' => 'Gryffindor Student',
                                'staticlist_description' => 'The Invalid Coordinator will attempt to edit this list',
                                'modified_by_user_name' => '',
                                'student_count' => 3,
                            ),
                        1 =>
                            array (
                                'staticlist_id' => 3,
                                'created_by' => '99704',
                                'created_by_user_name' => 'Albus Dumbledore',
                                'staticlist_name' => 'N.E.W.T. Students',
                                'staticlist_description' => 'This list will be tested on by Albus Dumbledore',
                                'modified_by_user_name' => '',
                                'student_count' => 3,
                            ),
                        2 =>
                            array (
                                'staticlist_id' => 2,
                                'created_by' => '99704',
                                'created_by_user_name' => 'Albus Dumbledore',
                                'staticlist_name' => 'O.W.L. Students',
                                'staticlist_description' => 'This list will be shared with Minerva McGonagall',
                                'modified_by_user_name' => '',
                                'student_count' => 3,
                            ),
                    ),
            ),
    ];

    private $viewSingleStaticList = [
        'data' =>
            array (
                'total_students' => 3,
                'red2' => '0',
                'red1' => '0',
                'yellow' => '0',
                'green' => '0',
                'staticlist_name' => 'O.W.L. Students',
                'staticlist_description' => 'This list will be shared with Minerva McGonagall',
                'total_students_list' =>
                    array (
                        0 =>
                            array (
                                'student_id' => 99416,
                                'student_first_name' => 'Draco ',
                                'student_last_name' => 'Malfoy',
                                'primary_email' => 'draco.malfoy@mailinator.com',
                            ),
                        1 =>
                            array (
                                'student_id' => 99422,
                                'student_first_name' => 'Harry ',
                                'student_last_name' => 'Potter',
                                'primary_email' => 'harry.potter@mailinator.com',
                            ),
                        2 =>
                            array (
                                'student_id' => 99429,
                                'student_first_name' => 'Neville ',
                                'student_last_name' => 'Longbottom',
                                'primary_email' => 'neville.longbottom@mailinator.com',
                            ),
                    ),
            ),
    ];

    private $createStaticList = [
        "org_id"=>"542",
        "staticlist_description"=>"Albus Dumbledore's New Static List",
        "staticlist_name"=>"Students in the Slug Club",
    ];

    private $createInvalidStaticList = [
        "org_id" => "542",
        "staticlist_description"=>"Invalid Coordnator's New Static List",
        "staticlist_name"=>"The Top Students in the Class"

    ];

    private $createStaticListView = [
        'staticlist_id' => 9,
        'staticlist_name' => 'Students in the Slug Club',
        'staticlist_description' => "Albus Dumbledore's New Static List",
        'person_id' => 99704,
    ];

    // I might want to see about edits that are > than
    // 120 character name, and 350 character description
    private $editStaticList = [
        'org_id'=> "542",
        'staticlist_description'=> "This List Has been changed",
        'staticlist_id'=> 2,
        'staticlist_name'=> "New O.W.L. Static Lists",
    ];



    private $editStaticListView = [
        'staticlist_description'=> "This List Has been changed",
        'staticlist_id'=> 2,
        'staticlist_name'=> "New O.W.L. Static Lists",
    ];

    // I might want to see about edits that are > than
    // 120 character name, and 350 character description
    private $editInvalidStaticList = [
        "org_id"=> "542",
        "staticlist_name"=> "The Bad Guy is Changing this Name",
        "staticlist_description"=> "The Bad Guy is Changing this description",
        'staticlist_id'=> 4
    ];

    private $addStudent = [
        'org_id' => "542",
        'staticlist_id' => 2,
        'student_edit_type' => "add",
        'student_id' => "S010",
    ];
    private $addInvalidStudent = [
        'org_id' => "542",
        'staticlist_id' => 4,
        'student_edit_type' => "add",
        'student_id' => "S010",
    ];

    private $deleteAStudent = [
        'org_id' => "542",
        'static_list_details' => [[
            'student_id' => 99422
        ]],
        'staticlist_id' => 2,
    ];

    private $deleteAnInvalidStudent = [
        'org_id' => "542",
        'static_list_details' => [[
            'student_id' => 99418
        ]],
        'staticlist_id' => 4,
    ];

    private $shareAStaticList = [
        "org_id" => "542",
        "person_id" => 99440,
    ];
    private $shareAnInvalidStaticList = [
        "org_id" => "542",
        "person_id" => 99447,
    ];

    private $sharAStaticListView =[
        'staticlist_id' => 10,
        'person_id' => 99440,
        'org_id' => 542,

        'shared_person_name' => 'N.E.W.T. Students',
        'shared_person_email' => 'albus.dumbledore@mailinator.com',
    ];

    private $InvalidCoordinatorsViewOfTheOrganizationsStaticLists = [
      //Basically The Invalid Organization can view the Static Lists
      //They own within the Org, but they have none to view so they should
      // view nothing
        'data' =>
            array (
            ),
        'sideLoaded' =>
            array (
            ),
    ];


    private $fourZeroThreeError = [
        'errors' =>
            array (
                0 =>
                    array (
                        'code' => '403',
                        //'user_message' => 'Access Denied',
                        //'message' => 'You do not have the necessary permissions',
                        //'developer_message' => '',
                        //'info' =>
                        //    array (
                        //    ),
                    ),
            ),
    ];


    public function testViewStaticListTab(ApiAuthTester $I){
        $I->wantTo('As a Coordinator from institution A, I want to view My Static Lists');
        $this->_getAPITestRunner($I, $this->coordinator, 'staticlists?org_id=542', [], 200, [$this->staticLists]);
    }

    public function testInvalidCoordinatorViewStaticListTab(ApiAuthTester $I, $scenario){
        $I->wantTo('As a Coordinator from institution B, I should not be able to view My Static Lists');
        $this->_getAPITestRunner($I, $this->invalidCoordinator, 'staticlists?org_id=542', [], 400, [$this->InvalidCoordinatorsViewOfTheOrganizationsStaticLists]);
    }

    public function testCreateStaticLists(ApiAuthTester $I){
        $I->wantTo('As a Coordinator from institution A, I should be able to create a new Static Lists');
        $this->_postAPITestRunner($I, $this->coordinator, 'staticlists', $this->createStaticList, 201, [$this->createStaticListView]);
    }

    public function testInvalidCoordinatorCreateStaticLists(ApiAuthTester $I){
        $I->wantTo('As a Coordinator from institution B, I should not be able to create a new Static Lists');
        $this->_postAPITestRunner($I, $this->invalidCoordinator, 'staticlists', $this->createInvalidStaticList, 400, [$this->InvalidCoordinatorsViewOfTheOrganizationsStaticLists]);
    }

    /*

     * I am currently having a problem with this test
     * The the ability to view a static list can be
     * viewed through the test but is not able to be
     * viewed through the front end nor through postman

     */

    public function testViewASingleStaticList(ApiAuthTester $I){
        $I->wantTo('As a Coordinator form Institution A, I should be able to view a single  static list');
        $this->_getAPITestRunner($I, $this->coordinator, 'staticlists/2?org_id=542', [], 200, [$this->viewSingleStaticList]);
    }


    /*                                                *\

     * Similar to above. The Coordinator can view any *
     * Static List that they have made or been shared *
     * with... So it fails for the wrong reason...    *

    \*                                                 */

    public function testInvalidCoordinatorViewASingleStaticList(ApiAuthTester $I){
        $I->wantTo('As a Coordinator form Institution B, I should not be able to view a single  static list');
        $this->_getAPITestRunner($I, $this->invalidCoordinator, 'staticlists/2?org_id=542', [], 403, $this->fourZeroThreeError);
    }



    //////////////////////////////////IMPORTANT NOTICE TO ALL PROGRAMMERS LOOKING AT THIS FUNCTION\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    // IT SEEMS AS IF ALL THIS TEST RESPONSE WITH IS THE UNIQUE ID WITHIN THE org_static_list_students TABLE. THIS MEANS THAT       \\
    // EVERY TIME ANY PERSON IS ADDED INTO ANY TABLE, YOU ARE GOING TO NEED TO CHANGE THE RESPONSE. THE SAME IS TRUE FOR THE STATIC \\
    // LISTS BUT THOSE SHOULD NOT CHANGE AS THEY ARE ALREADY MADE WITH THEIR NUMBERS                                                \\

    // and yes... it just wants the number as far as I can tell                                                                    \\

    public function testInsertStudentIntoStaticList(ApiAuthTester $I, $scenario){
        $I->wantTo('I want to insert a new student into my N.E.W.T. Static List');
        $this->_putAPITestRunner($I, $this->coordinator, 'staticlists/2/students', $this->addStudent, 200, [['data'=> [22]]]);
    }


    public function testInvalidCoordinatorInsertStudentIntoStaticList(ApiAuthTester $I){
        $I->wantTo('As a coordinator of institution B, I should not be able to insert a student into a Static from institution A.');
        $this->_putAPITestRunner($I, $this->invalidCoordinator, 'staticlists/4/students', $this->addInvalidStudent, 403, []);
    }

    public function testShareStaticListWithFaculty(ApiAuthTester $I){
        $I->wantTo('As a coordinator from institution A, I want to share my static list with a faculty member');
        $this->_postAPITestRunner($I, $this->coordinator, 'staticlists/3/shares', $this->shareAStaticList, 201, [$this->sharAStaticListView]);
    }

    public function testInvalidShareStaticListWithFaculty(ApiAuthTester $I){
        $I->wantTo('As a coordinator from institution B, I should not be able to share Coordinator As static list with another faculty member');
        $this->_postAPITestRunner($I, $this->invalidCoordinator, 'staticlists/4/shares', $this->shareAnInvalidStaticList, 400, [$this->InvalidCoordinatorsViewOfTheOrganizationsStaticLists]);
    }

    public function testEditAStaticListsInformation(ApiAuthTester $I){
        $I->wantTo("As a coordinator from instituiton A, I want to Edit my List's Name and Information");
        $this->_putAPITestRunner($I, $this->coordinator, 'staticlists/2', $this->editStaticList, 201, [$this->editStaticListView]);
    }

    public function testEditAnInvalidStaticListsInformation(ApiAuthTester $I, $scenario){
        \Codeception\Util\Debug::debug('Inserting new data for the Test');

        // This will reload and load the database before this test
        // drops the database, and creates the database, it will then
        // add in the data that has Sprout's Static Lists and makes sure
        // that there are no changes to the data
        shell_exec('mysql -u root -psynapse -e "drop database synapse"');

        shell_exec('mysql -u root -psynapse -e "create database synapse"');

        $dataLoadOutput = shell_exec(' mysql -u root -psynapse synapse < tests/_data/auth-test-single-file.sql');


        if ($dataLoadOutput != null){
            // shows if the database reload failed
            codecept_debug($dataLoadOutput);
        }
        else {
            // Tells user that something good happened
            \Codeception\Util\Debug::debug('Data inserted for the Test');
        }

        $I->wantTo("As an coordinator of institution B, I should not be able to edit a Static List from institution A");
        $this->_putAPITestRunner($I, $this->invalidCoordinator, 'staticlists/4', $this->editInvalidStaticList, 400, []);
    }

    public function testDeleteStudentFromStaticList(ApiAuthTester $I){
        $I->wantTo('As a coordinator from institution A, I want to delete a student from my O.W.L. Static List');
        $this->_putAPITestRunner($I, $this->coordinator, 'students/staticlists/2', $this->deleteAStudent, 204, []);
    }

    public function testDeleteInvalidStudentFromStaticList(ApiAuthTester $I){
        \Codeception\Util\Debug::debug('Inserting new data for the Test');

        // This will reload and load the database before this test
        // drops the database, and creates the database, it will then
        // add in the data that has Sprout's Static Lists and makes sure
        // that there are no changes to the data
        shell_exec('mysql -u root -psynapse -e "drop database synapse"');

        shell_exec('mysql -u root -psynapse -e "create database synapse"');

        $dataLoadOutput = shell_exec(' mysql -u root -psynapse synapse < tests/_data/auth-test-single-file.sql');


        if ($dataLoadOutput != null){

            // shows if the database reload failed
            codecept_debug($dataLoadOutput);
        }
        else {
            // Tells user that something good happened
            \Codeception\Util\Debug::debug('Data inserted for the Test');
        }

        $I->wantTo('As a Coordinator for institution B, I should not be able to delete a student from Coordinators A O.W.L. Static List');
        $this->_putAPITestRunner($I, $this->invalidCoordinator, 'students/staticlists/4', $this->deleteAnInvalidStudent, 403, []);
    }

    public function testDeleteEntireList(ApiAuthTester $I){
        $I->wantTo('As a Coordinator from Institution A, I should be able to delete my own Static List');
        $this->_deleteAPITestRunner($I, $this->coordinator, 'staticlists/3/542', [], 204, []);
    }

    public function testInvalidDeleteEntireList(ApiAuthTester $I){
        \Codeception\Util\Debug::debug('Inserting new data for the Test');

        // This will reload and load the database before this test
        // drops the database, and creates the database, it will then
        // add in the data that has Sprout's Static Lists and makes sure
        // that there are no changes to the data
        shell_exec('mysql -u root -psynapse -e "drop database synapse"');

        shell_exec('mysql -u root -psynapse -e "create database synapse"');

        $dataLoadOutput = shell_exec(' mysql -u root -psynapse synapse < tests/_data/auth-test-single-file.sql');


        if ($dataLoadOutput != null){
            // shows if the database reload failed
            codecept_debug($dataLoadOutput);
        }
        else {
            // Tells user that something good happened
            \Codeception\Util\Debug::debug('Data inserted for the Test');
        }

        $I->wantTo('As a Coordinator from Institution B, I should not be able to delete my competitors Static List');
        $this->_deleteAPITestRunner($I, $this->invalidCoordinator, 'staticlists/4/542', [], 403, []);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }

}
