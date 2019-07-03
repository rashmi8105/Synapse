<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class StaffStaticListCest extends SynapseRestfulTestBase
{

    /******
     * This test is very specific, which means it has
     * it's own file. It will take Pomona Sprout and
     * reload a specific file that make a static list
     * that contains all hufflepuff students. It then
     ******/

    // Sprout for which the test takes place
    private $Sprout = [
        'email' => 'pomona.sprout@mailinator.com',
        'password' => 'password1!',
        'id' => 99443,
        'orgId' => 542,
        'langId' => 1
    ];

    // Coordinator to delete the group
    // So I can test the Situation
    private $coordinator = [
        'email' => 'albus.dumbledore@mailinator.com',
        'password' => 'password1!',
        'id' => 99704,
        'orgId' => 542,
        'langId' => 1
    ];

    private $faculty = [
        'email' => 'minerva.mcgonagall@mailinator.com',
        'password' => 'password1!',
        'id' => 99440,
        'orgId' => 542,
        'langId' => 1
    ];

    private $invalidFaculty = [
        'email' => 'severus.snape@mailinator.com',
        'password' => 'password1!',
        'id' => 99447,
        'orgId' => 542,
        'langId' => 1
    ];

    // the next three data sets are to
    // add in students into the Static
    // list//DO NOT DELETE\\
    private $addJustin = [
        'org_id' => "542",
        'staticlist_id' => 1,
        'student_edit_type' => "add",
        'student_id' => "S015",
    ];
    private $addCedric = [
    'org_id' => "542",
    'staticlist_id' => 1,
    'student_edit_type' => "add",
    'student_id' => "S004",
    ];
    private $addHannah = [
    'org_id' => "542",
    'staticlist_id' => 1,
    'student_edit_type' => "add",
    'student_id' => "S012",
    ];

    // Create the Static List...
    private $createStaticList = [
        "org_id"=>"542",
        "staticlist_description"=>"Static list to test if a faculty can view a student even though the professor has no link with the student",
        "staticlist_name"=>"Hufflepuff",
    ];

    // I should receive no students after all of  test
    private $noStudents = [
        "total_students_list"=> [],
    ];

    // I should see all students before the Group is deleted
    private $allStudents = [
        'data' =>
          array (
            'total_students' => 3,
            'red2' => '0',
            'red1' => '0',
            'yellow' => '0',
            'green' => '0',
            'staticlist_name' => 'Hufflepuff',
            'staticlist_description' => 'Static list to test if a faculty can view a student even though the professor has no link with the student',
            'total_students_list' =>
            array (
              0 =>
              array (
                'student_id' => 99413,
                'student_first_name' => 'Cedric ',
                'student_last_name' => 'Diggory',
                'primary_email' => 'cedric.diggory@mailinator.com',
                  ),
              1 =>
              array (
                'student_id' => 99421,
                'student_first_name' => 'Hannah ',
                'student_last_name' => 'Abbott',
                'primary_email' => 'hannah.abbott@mailinator.com',
                  ),
              2 =>
              array (
                'student_id' => 99424,
                'student_first_name' => 'Justin ',
                'student_last_name' => 'Finch-Fletchley',
                'primary_email' => 'justin.finch-fletchley@mailinator.com',
                  ),
                ),
              ),
    ];

    private $facultyStaticListsList = [
        'data' =>
            array (
                'static_list_details' =>
                    array (
                        0 =>
                            array (
                                'staticlist_id' => 7,
                                'created_by' => '99440',
                                'created_by_user_name' => 'Minerva McGonagall',
                                'staticlist_name' => 'Students that can go to Hogsmead',
                                'staticlist_description' => 'This list will be tested by an invalid faculty member',
                                'student_count' => 3,
                            ),
                        1 =>
                            array (
                                'staticlist_id' => 6,
                                'created_by' => '99440',
                                'created_by_user_name' => 'Minerva McGonagall',
                                'staticlist_name' => 'The Main Characters',
                                'staticlist_description' => 'This List will be tested on my Minerva McGonagall',
                                'student_count' => 3,
                            ),
                        2 =>
                            array (
                                'staticlist_id' => 5,
                                'created_by' => '99704',
                                'created_by_user_name' => 'Albus Dumbledore',
                                'staticlist_name' => 'O.W.L. Students',
                                'staticlist_description' => 'This list will be shared with Minerva McGonagall',
                                'student_count' => 2,
                            ),
                    ),
            ),

    ];

    private $facultyCreateStaticList = [
        "org_id"=>"542",
        "staticlist_description"=>"McGonagall's Static List",
        "staticlist_name"=>"McGonagall's Created Static List",
    ];

    private $facultyCreateStaticListView = [
        'staticlist_id' => 9,
        'staticlist_name' => "McGonagall's Created Static List",
        'staticlist_description' => "McGonagall's Static List",
        'person_id' => 99440,
    ];

    private $createStaticListView = [
        'data' =>
            array (
                'staticlist_id' => 11,
                'staticlist_name' => 'Hufflepuff',
                'staticlist_description' => 'Static list to test if a faculty can view a student even though the professor has no link with the student',
                'person_id' => 99704,
            ),
    ];

    private $facultySeeSingleStaticList = [
        'data' =>
            array (
                'total_students' => 2,
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
                                'student_id' => 99422,
                                'student_first_name' => 'Harry ',
                                'student_last_name' => 'Potter',
                                'primary_email' => 'harry.potter@mailinator.com',
                            ),
                        1 =>
                            array (
                                'student_id' => 99429,
                                'student_first_name' => 'Neville ',
                                'primary_email' => 'neville.longbottom@mailinator.com',
                            ),
                    ),
            ),
    ];

    private $addStudent = [
        'org_id' => "542",
        'staticlist_id' => 5,
        'student_edit_type' => "add",
        'student_id' => "S010",
        //'student_id' => "n001",
    ];

    private $addInvalidStudent = [
        'org_id' => "542",
        'staticlist_id' => 5,
        'student_edit_type' => "add",
        'student_id' => "S025",
    ];

    private $shareAStaticList = [
        "org_id" => "542",
        "person_id" => 99447,
    ];

    private $shareAStaticListView =[
        //'staticlist_id' => 10,
        'person_id' => 99447,
        'org_id' => 542,
        'shared_person_name' => 'The Main Characters',
        'shared_person_email' => 'minerva.mcgonagall@mailinator.com',    ];

    // I might want to see about edits that are > than
    // 120 character name, and 350 character description
    private $editStaticList = [
        'org_id'=> "542",
        'staticlist_description'=> "This List Has been changed",
        'staticlist_id'=> 5,
        'staticlist_name'=> "New O.W.L. Static Lists",
    ];

    private $editStaticListView = [
        'staticlist_description'=> "This List Has been changed",
        'staticlist_id'=> 5,
        'staticlist_name'=> "New O.W.L. Static Lists",
    ];

    private $deleteAStudent = [
        'org_id' => "542",
        'static_list_details' => [[
            'student_id' => 99422
        ]],
        'staticlist_id' => 5,
    ];

    // Can delete either student the commented out
    // student is Draco Malfoy who Snape is associated with
    // and the other one is Neville Longbottom, who snape
    // does not have an association with
    private $deleteAnInvalidStudent = [
        'org_id' => "542",
        'static_list_details' => [[
            'student_id' => 99416,
            //'student_id' => 99429,

        ]],
        'staticlist_id' => 5,
    ];


    private $editInvalidStaticList = [
        'org_id'=> "542",
        'staticlist_description'=> "Snape Was here",
        'staticlist_id'=> 5,
        'staticlist_name'=> "The Half-Blood Prince",
    ];

    private $addIllegalStudent =[
        'org_id' => "542",
        'staticlist_id' => 5,
        'student_edit_type' => "add",
        'student_id' => "S011",
    ];

    private $validErrorResponse = [
      'data'=>[

      ],
      'sideLoaded' => [

        ]
    ];


    public function testViewStaticListTab(ApiAuthTester $I){
        $I->wantTo('As a faculty from institution A, I want to view My Static Lists');
        $this->_getAPITestRunner($I, $this->faculty, 'staticlists?org_id=542', [], 200, [$this->facultyStaticListsList]);

    }

    public function testCreateStaticLists(ApiAuthTester $I){
        $I->wantTo('As a faculty from institution A, I should be able to create a new Static Lists');
        $this->_postAPITestRunner($I, $this->faculty, 'staticlists', $this->facultyCreateStaticList, 201, [$this->facultyCreateStaticListView]);
    }

    public function testViewASingleStaticList(ApiAuthTester $I){
        $I->wantTo('As a faculty form Institution A, I should be able to view a single  static list');
        $this->_getAPITestRunner($I, $this->faculty, 'staticlists/5?org_id=542', [], 200, [$this->facultySeeSingleStaticList]);
    }


    //////////////////////////////////IMPORTANT NOTICE TO ALL PROGRAMMERS LOOKING AT THIS FUNCTION\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    // IT SEEMS AS IF ALL THIS TEST RESPONSE WITH IS THE UNIQUE ID WITHIN THE org_static_list_students TABLE. THIS MEANS THAT
    // EVERY TIME ANY PERSON IS ADDED INTO ANY TABLE, YOU ARE GOING TO NEED TO CHANGE THE RESPONSE. THE SAME IS TRUE FOR THE STATIC
    // LISTS BUT THOSE SHOULD NOT CHANGE AS THEY ARE ALREADY MADE WITH THEIR NUMBERS

    // and yes... it just wants the number as far as I can tell

    public function testInsertStudentIntoStaticList(ApiAuthTester $I, $scenario){
        $I->wantTo('I want to insert a new student into my O.W.L. Static List');
        
        $this->_putAPITestRunner($I, $this->faculty, 'staticlists/5/students', $this->addStudent, 200, []);
    }

    public function testShareStaticListWithFaculty(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo('As a faculty from institution A, I want to share my static list with a faculty member');
        $this->_postAPITestRunner($I, $this->faculty, 'staticlists/6/shares', $this->shareAStaticList, 201, [$this->shareAStaticListView]);
    }

    public function testEditAStaticListsInformation(ApiAuthTester $I){
        $I->wantTo("As a faculty from instituiton A, I want to Edit my List's Name and Information");
        $this->_putAPITestRunner($I, $this->faculty, 'staticlists/5', $this->editStaticList, 201, [$this->editStaticListView]);
    }
    
    public function testDeleteStudentFromStaticList(ApiAuthTester $I){
        $I->wantTo('As a faculty from institution A, I want to delete a student from my O.W.L. Static List');
        $this->_putAPITestRunner($I, $this->faculty, 'students/staticlists/5', $this->deleteAStudent, 204, []);
    }


    public function testDeleteEntireList(ApiAuthTester $I){
        $I->wantTo('As a Coordinator from Institution A, I should be able to delete my own Static List');
        $this->_deleteAPITestRunner($I, $this->faculty, 'staticlists/6/542', [], 204, []);
    }


    public function testViewASingleStaticListWithStudentsAProfessorHasNoConnectionWith(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        \Codeception\Util\Debug::debug(
            '
            This test will only pass if you make sure you hit the API. Please make sure your
            api.suite.yml is set up so you are hitting the api on your tests if
            the test is failing. Please visit:
            https://jira-mnv.atlassian.net/wiki/display/SKYFACTOR/Synapse+API+Testing
            for more information.
            ');

        $I->wantTo('See if I am able to view a student through a Static List Despite not having a connection to said student through groups or courses.');
        \Codeception\Util\Debug::debug('Seeing if Sprout is able to see the students before the delete');
        $this->_getAPITestRunner($I, $this->Sprout, "staticlists/1?org_id=542", [], 200, [$this->allStudents]);

         // Telling the user what is going on
        \Codeception\Util\Debug::debug('Deleting the Group');

        $authData = ['email' => $this->coordinator['email'], 'password' => $this->coordinator['password']];
        $this->_deleteAPITestRunner($I, $authData, 'groups/1265', [], 200, []);

        // Comment for user to see if they choose
        \Codeception\Util\Debug::debug('Seeing if the static list has no students in it');

        // This runs the tests that sees if the faculty can view
        // a student through a static list of a deleted group
        $this->_getAPITestRunner($I, $this->Sprout, "staticlists/1?org_id=542", [], 200, [$this->noStudents]); 
    }

/*/

    // Please do not uncomment this section: the tests are designed to create a static list and students for
    // for the test! They should not be used ever again, but if they are here it is just in case

    // Okay currently I have to run a "test" to insert students
    // into the Hufflepuff static list for sprout, keep this in case of errors
    // Make sure your Static Lists work, and that there are no static lists so you can
    // create one and add students to it

    public function testInvalidCoordinatorCreateStaticLists(ApiAuthTester $I){
        $I->wantTo('As a Coordinator from institution B, I should not be able to create a new Static Lists');
        $this->_postAPITestRunner($I, $this->Sprout, 'staticlists', $this->createStaticList, 201, []);
    }

    public function testInsertStudentIntoStaticList(ApiAuthTester $I){
        $I->wantTo('Add students to my list');
        $this->_putAPITestRunner($I, $this->Sprout, 'staticlists/1/students', $this->addCedric, 200, []);
        $this->_putAPITestRunner($I, $this->Sprout, 'staticlists/1/students', $this->addHannah, 200, []);
        $this->_putAPITestRunner($I, $this->Sprout, 'staticlists/1/students', $this->addJustin, 200, []);
    }
/*
    public function testCoordinatorDeleteGroup(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can delete groups for Institute A.');
        $authData = ['email' => $this->coordinator['email'], 'password' => $this->coordinator['password']];

        $this->_deleteAPITestRunner($I, $authData, 'groups/1265', [], 200, []);
    }

//*/


    public function testCreateDuplicateStaticLists(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo('I should be able to create a static list regardless of whether or not another faculty has a list with the same name');
        $this->_postAPITestRunner($I, $this->coordinator, 'staticlists', $this->createStaticList, 201, [$this->createStaticListView]);
    }



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    /* This is testing weather or not Static List Security will whether a barrage of attacks from one faculty to another
       With how this is set up, I am not sure how to tests everything. The main problem is that each person's lists of
       static lists are attached to their number and their organization. So viewing general and similar tests are
       pointless. I will not test viewing a list, and creating a list because all the tests will do is create a list
       within the other faculty member's static list location. This is pointless and will be skipped. I will test anything
       that will be specific to the valid faculty's (or McGonagall's) Static Lists */


    public function testInvalidViewASingleStaticList(ApiAuthTester $I){
        $I->wantTo('As a different Faculty, I should not be able to view a single static list that my colleague owns');
        $this->_getAPITestRunner($I, $this->invalidFaculty, 'staticlists/5?org_id=542', [],403, []);
    }


    //////////////////////////////////IMPORTANT NOTICE TO ALL PROGRAMMERS LOOKING AT THIS FUNCTION\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    // IT SEEMS AS IF ALL THIS TEST RESPONSE WITH IS THE UNIQUE ID WITHIN THE org_static_list_students TABLE. THIS MEANS THAT
    // EVERY TIME ANY PERSON IS ADDED INTO ANY TABLE, YOU ARE GOING TO NEED TO CHANGE THE RESPONSE. THE SAME IS TRUE FOR THE STATIC
    // LISTS BUT THOSE SHOULD NOT CHANGE AS THEY ARE ALREADY MADE WITH THEIR NUMBERS

    // and yes... it just wants the number as far as I can tell

    public function testInvalidInsertStudentIntoStaticList(ApiAuthTester $I){
        $I->wantTo('As a different Faculty, I should not be able to insert a student into a list that my colleague owns');
        $this->_putAPITestRunner($I, $this->invalidFaculty, 'staticlists/5/students', $this->addInvalidStudent, 403, []);
    }

    public function testInvalidShareStaticListWithFaculty(ApiAuthTester $I){
        $I->wantTo('As a different Faculty, I should not be able to share a list that my colleague owns');
        $this->_postAPITestRunner($I, $this->invalidFaculty, 'staticlists/5/shares', $this->shareAStaticList, 400, []);
    }

    public function testInvalidDeleteStudentFromStaticList(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo('As a different Faculty, I should not be able to delete a student from a list that my colleague owns');
        $this->_putAPITestRunner($I, $this->invalidFaculty, 'students/staticlists/5', $this->deleteAnInvalidStudent, 400, []);
    }


    public function testInvalidEditAStaticListsInformation(ApiAuthTester $I){
        $I->wantTo('As a different Faculty, I should not be able to edit a list that my colleague owns');
        $this->_putAPITestRunner($I, $this->invalidFaculty, 'staticlists/5', $this->editInvalidStaticList, 403, []);
    }

    public function testInvalidDeleteEntireList(ApiAuthTester $I){
        $I->wantTo('As a different Faculty, I should not be able to delete a list that my colleague owns');
        $this->_deleteAPITestRunner($I, $this->invalidFaculty, 'staticlists/7/542', [], 403, []);
    }

    // I am going to test an evil faculty trying to get new different students into the list
    // Then I am going to test deleting faculty students from

    // At this point I have messed around with the lists way too much I am going ot ned to reload
    // the database

    public function testIllegalStudentDeletion(ApiAuthTester $I)
    {
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

        $I->wantTo('Because I am deleting a student that I should not know exists, I should not be able to delete this student');
        $this->_putAPITestRunner($I, $this->faculty, 'students/staticlists/5', $this->deleteAnInvalidStudent, 403, []);
    }

    public function testIllegalStudentAddition(ApiAuthTester $I){
        $I->wantTo('As a faculty member, I should not be able to add a student I have no connection with to my list.');
        $this->_putAPITestRunner($I, $this->faculty, 'staticlists/5/students', $this->addIllegalStudent, 403, [$this->validErrorResponse]);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }


}
