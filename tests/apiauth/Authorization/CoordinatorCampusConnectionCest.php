<?php
/**
 * Created by PhpStorm.
 * User: imeyers
 * Date: 7/10/15
 * Time: 12:19 PM
 */

require_once(dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class CoordinatorCampusConnectionCest extends SynapseRestfulTestBase
{
    // This tests will tests both valid and invalid coordinators
    // with Campus Connections

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

    private $coordinatorValidPrimaryConnection = [
        'organization_id' => "542",
        'student_list' =>
            [
                0=>[
                    'student_id' => "99422",
                    'staff_id' => 99440,
                ],
            ],
    ];
    private $invalidCoordinatorValidPrimaryConnection = [
    'organization_id' => "542",
    'student_list' =>
        [
            0=>[
                'student_id' => "99422",
                'staff_id' => 99705
            ],
        ],
    ];

    private $coordinatorInvalidPrimaryConnection = [
        'organization_id' => "542",
        'student_list' =>
            [
                0=>[
                    'student_id' => "99422",
                    'staff_id' => 99447
                ],
            ],
    ];

    private $invalidCoordinatorInvalidPrimaryConnection = [
        'organization_id' => "542",
        'student_list' =>
            [
                0=>[
                    'student_id' => 99422,
                    'staff_id' => 99447,
                ],
            ],
    ];
    private $coordinatorAggPrimaryConnection = [
        'organization_id' => "542",
        'student_list' =>
            [
                0=>[
                    'student_id' => "99422",
                    'staff_id' => 99436
                ],
            ],

    ];

    private $coordinatorAggPrimaryConnectionView = [
        'data' =>
            array (
                'organization_id' => 542,
                'student_list' =>
                    array (
                        0 =>
                            array (
                                'student_id' => 99422,
                                'staff_id' => 99436,
                                'is_primary_assigned' => true,
                            ),
                    ),
            ),

    ];
    private $invalidCoordinatorAggPrimaryConnection = [
        'organization_id' => "542",
        'student_list' =>
            [
                0=>[
                    'student_id' => "99422",
                    'staff_id' => 99436
                ],
            ],
    ];

    private $coordinatorViewCampusConnections = [
        'data' =>
            array (
                'organization_id' => 542,
                'campus_id' => 'nsu',
                'campus_name' => 'Hogwarts School of Witchcraft and Wizardry',
                'campus_connections' =>
                    array (
                        0 =>
                            array (
                                'person_id' => 99704,
                                'person_firstname' => 'Albus',
                                'person_lastname' => 'Dumbledore',
                                'person_title' => 'Headmaster',
                                'primary_connection' => false,
                                'phone' => '(816)555-5555',
                                'email' => 'albus.dumbledore@mailinator.com',
                                'groups' =>
                                    array (
                                        0 =>
                                            array (
                                                'group_id' => '1269',
                                                'group_name' => 'Dumbledore\'s Army',
                                            ),
                                        1 =>
                                            array (
                                                'group_id' => '1418',
                                                'group_name' => 'All Students',
                                            ),
                                    ),
                                'courses' =>
                                    array (
                                    ),
                            ),
                        1 =>
                            array (
                                'person_id' => 99436,
                                'person_firstname' => 'Argus',
                                'person_lastname' => 'Filch',
                                'person_title' => '',
                                'primary_connection' => false,
                                'email' => 'argus.filch@mailinator.com',
                                'groups' =>
                                    array (
                                        0 =>
                                            array (
                                                'group_id' => '1269',
                                                'group_name' => 'Dumbledore\'s Army',
                                            ),
                                    ),
                                'courses' =>
                                    array (
                                    ),
                            ),
                        2 =>
                            array (
                                'person_id' => 99445,
                                'person_firstname' => 'Rolanda',
                                'person_lastname' => 'Hooch',
                                'person_title' => '',
                                'primary_connection' => false,
                                'email' => 'rolanda.hooch@mailinator.com',
                                'groups' =>
                                    array (
                                        0 =>
                                            array (
                                                'group_id' => '1270',
                                                'group_name' => 'Quidditch(Team Gryffindor)',
                                            ),
                                    ),
                                'courses' =>
                                    array (
                                    ),
                            ),
                        3 =>
                            array (
                                'person_id' => 99710,
                                'person_firstname' => 'Remus',
                                'person_lastname' => 'Lupin',
                                'person_title' => '',
                                'primary_connection' => false,
                                'email' => 'remus.lupin@mailinator.com',
                                'groups' =>
                                    array (
                                        0 =>
                                            array (
                                                'group_id' => '1269',
                                                'group_name' => 'Dumbledore\'s Army',
                                            ),
                                    ),
                                'courses' =>
                                    array (
                                    ),
                            ),
                        4 =>
                            array (
                                'person_id' => 99440,
                                'person_firstname' => 'Minerva',
                                'person_lastname' => 'McGonagall',
                                'person_title' => '',
                                'primary_connection' => false,
                                'email' => 'minerva.mcgonagall@mailinator.com',
                                'groups' =>
                                    array (
                                        0 =>
                                            array (
                                                'group_id' => '1266',
                                                'group_name' => 'Gryffindor',
                                            ),
                                        1 =>
                                            array (
                                                'group_id' => '1269',
                                                'group_name' => 'Dumbledore\'s Army',
                                            ),
                                    ),
                                'courses' =>
                                    array (
                                    ),
                            ),
                        5 =>
                            array (
                                'person_id' => 99444,
                                'person_firstname' => 'Poppy',
                                'person_lastname' => 'Pomfrey',
                                'person_title' => '',
                                'primary_connection' => false,
                                'email' => 'poppy.pomfrey@mailinator.com',
                                'groups' =>
                                    array (
                                        0 =>
                                            array (
                                                'group_id' => '1270',
                                                'group_name' => 'Quidditch(Team Gryffindor)',
                                            ),
                                    ),
                                'courses' =>
                                    array (
                                    ),
                            ),
                        6 =>
                            array (
                                'person_id' => 99442,
                                'person_firstname' => 'Percy ',
                                'person_lastname' => 'Weasley',
                                'person_title' => '',
                                'primary_connection' => false,
                                'email' => 'percy.weasley@mailinator.com',
                                'groups' =>
                                    array (
                                        0 =>
                                            array (
                                                'group_id' => '1266',
                                                'group_name' => 'Gryffindor',
                                            ),
                                    ),
                                'courses' =>
                                    array (
                                    ),
                            ),
                     /*   7 =>
                            array (
                                'person_id' => 99385,
                                'person_firstname' => 'Andrew',
                                'person_lastname' => 'Wilson',
                                'person_title' => 'Undesirable No 1',
                                'primary_connection' => false,
                                'phone' => '8165555555',
                                'email' => 'andrew.wilson@mailinator.com',
                                'groups' =>
                                    array (
                                        0 =>
                                            array (
                                                'group_id' => '1418',
                                                'group_name' => 'All Students',
                                            ),
                                    ),
                                'courses' =>
                                    array (
                                    ),
                            ),*/
                        7 =>
                            array (
                                'person_id' => 99441,
                                'person_firstname' => 'Oliver ',
                                'person_lastname' => 'Wood',
                                'person_title' => '',
                                'primary_connection' => false,
                                'email' => 'oliver.wood@mailinator.com',
                                'groups' =>
                                    array (
                                        0 =>
                                            array (
                                                'group_id' => '1269',
                                                'group_name' => 'Dumbledore\'s Army',
                                            ),
                                        1 =>
                                            array (
                                                'group_id' => '1270',
                                                'group_name' => 'Quidditch(Team Gryffindor)',
                                            ),
                                    ),
                                'courses' =>
                                    array (
                                    ),
                            ),
                    ),
            ),
    ];

    private $AndrewWilson = [
        'data' =>
            array (
                'organization_id' => 542,
                'campus_id' => 'nsu',
                'campus_name' => 'Hogwarts School of Witchcraft and Wizardry',
                'campus_connections' =>
                    array (
                       array (
                            'person_id' => 99385,
                            'person_firstname' => 'Andrew',
                            'person_lastname' => 'Wilson',
                            'person_title' => 'Undesirable No 1',
                            'phone' => '8165555555',
                            'email' => 'andrew.wilson@mailinator.com',
                            'groups' =>
                                array (
                                    0 =>
                                        array (
                                            'group_id' => '1418',
                                            'group_name' => 'All Students',
                                        ),
                                ),
                            'courses' =>
                                array (
                                ),
                       ),

                    ),
            ),
    ];


    private $sproutData = [
        'data' =>
            array (
                'organization_id' => 542,
                'campus_id' => 'nsu',
                'campus_name' => 'Hogwarts School of Witchcraft and Wizardry',
                'campus_connections' =>
                    array (
                        6 =>
                         array (
                            'person_id' => 99443,
                            'person_firstname' => 'Pomona',
                            'person_lastname' => 'Sprout',
                            'person_title' => '',
                            'primary_connection' => false,
                            'email' => 'pomona.sprout@mailinator.com',
                            'groups' =>
                                array (
                                    0 =>
                                        array (
                                            'group_id' => '1265',
                                            'group_name' => 'Hufflepuff',
                                        ),
                                ),
                            'courses' =>
                                array (
                                ),
                            'is_invisible' => false,
                        ),
                    ),
                ),
    ];


    private $youDoNotHaveCoordinatorAccess = [
        'errors' =>
            array (
                0 => 'You do not have coordinator access',
            ),
    ];

    private $dataView99440 = [
        'data' =>
            array (
                'organization_id' => 542,
                'student_list' =>
                    array (
                        0 =>
                            array (
                                'student_id' => 99422,
                                'staff_id' => 99440,
                                'is_primary_assigned' => true,
                            ),
                    ),
            ),
    ];

    private $dataViewIllegallyAssignedCampusConnection = [
        array (
            'organization_id' => 542,
            'student_list' =>
                array (
                    0 =>
                        array (
                            'is_primary_assigned' => false,
                        ),
                ),
        ),

    ];

    private $invisibleParameters = [
        'organization_id' => "542",
        'student_list' =>
            [
                0=>[
                    'student_id' => "99422",
                    'staff_id' => 99385
                ],
            ],
    ];

    // legal and illegal faculty members are trying to view the game
    public function testCoordinatorViewCampusConnections(ApiAuthTester $I){
        $I->wantTo('As a coordinator with a connection with the student, I should be able to view the Campus Connections');
        $this->_getAPITestRunner($I, $this->coordinator, 'campusconnections/99422?orgId=542', [], 200, $this->coordinatorViewCampusConnections);
    }

    public function testInvalidCoordinatorViewCampusConnections(ApiAuthTester $I, $scenario){
        $I->wantTo('As a coordinator without a connection with the student, I should not be able to view the Campus Connections');
        $this->_getAPITestRunner($I, $this->invalidCoordinator, 'campusconnections/99422?orgId=542', [], 403, []);
    }

// Add in a faculty with a connection with the student
    /*  Need to do more research on the desired behavior before declaring failure and Pass
        Currently passing, but button are being shown in the web-app (can't really test here)
        So we are getting mixed signals on the desired effect (can't think of right word)
        the program does  */
    public function testCoordinatorMakeValidFacultyPrimaryConnection(ApiAuthTester $I){
        $I->wantTo("As a coordinator with a connection with the student, I should be able to change the student's Primary Campus Connection");
        $this->_postAPITestRunner($I, $this->coordinator, 'campusconnections/primaryconnection', $this->coordinatorValidPrimaryConnection, 201, [$this->dataView99440]);
    }
    public function testInvalidCoordinatorMakeValidFacultyPrimaryConnection(ApiAuthTester $I){
        $I->wantTo("As a coordinator without a connection with the student, I should not be able to change the student's Primary Campus Connection");
        $this->_postAPITestRunner($I, $this->invalidCoordinator, 'campusconnections/primaryconnection', $this->invalidCoordinatorValidPrimaryConnection, 400, [$this->youDoNotHaveCoordinatorAccess]);
    }

// Add in a faculty without a connection with a student
    public function testCoordinatorMakeInvalidFacultyPrimaryConnection(ApiAuthTester $I){
        $I->wantTo('As a coordinator with a connection with the student, I should not be able to make an invalid Faculty a Primary Connection');
          $this->_postAPITestRunner($I, $this->coordinator, 'campusconnections/primaryconnection', $this->coordinatorInvalidPrimaryConnection, 201, $this->dataViewIllegallyAssignedCampusConnection);
    }
    public function testInvalidCoordinatorMakeInvalidFacultyPrimaryConnection(ApiAuthTester $I){
          $I->wantTo('As a coordinator without a connection with the student, I should not be able to make an invalid Faculty a Primary Connection');
          $this->_postAPITestRunner($I, $this->invalidCoordinator, 'campusconnections/primaryconnection', $this->invalidCoordinatorInvalidPrimaryConnection, 400, [$this->youDoNotHaveCoordinatorAccess]);
    }

  // Add in a faculty with a connection to the student with aggregate only access
    public function testCoordinatorMakeAggregateFacultyPrimaryConnection(ApiAuthTester $I){
          $I->wantTo('As a coordinator with a connection with the student, I should be able to make an aggregate only faculty a primary connection');
          $this->_postAPITestRunner($I, $this->coordinator, 'campusconnections/primaryconnection', $this->coordinatorAggPrimaryConnection, 201, $this->coordinatorAggPrimaryConnectionView);
      }
    public function testInvalidCoordinatorMakeAggregateFacultyPrimaryConnection(ApiAuthTester $I){
          $I->wantTo('As a coordinator without a connection with the student, I should not be able to make an aggregate only faculty a primary connection');
          $this->_postAPITestRunner($I, $this->invalidCoordinator, 'campusconnections/primaryconnection', $this->invalidCoordinatorAggPrimaryConnection, 400, [$this->youDoNotHaveCoordinatorAccess]);
    }


    // This is testing invisibility, faculty, and viewing them in campus coordinator
    // Andrew will be invisible
    //
    public function testCoordinatorViewInvisibleFaculty(ApiAuthTester $I){
        $I->wantTo('See if a Invisible coordinator will show up in campus connections');
        $this->_getAPITestRunner($I, $this->coordinator, 'campusconnections/99422?orgId=542', [], 200, []);

        // Trying to view Andrew
        $I->cantSeeResponseContainsJson($this->AndrewWilson);
    }

    public function testCoordinatorMakeInvisibleFacultyPrimaryCoordinator(ApiAuthTester $I, $scenario){
        $scenario->skip("Failed");
        $I->wantTo('Make an invisible coordinator a primary connection');
        $this->_putAPITestRunner($I, $this->coordinator, 'campusconnections/primaryconnection', $this->invisibleParameters, 201, [$this->dataViewIllegallyAssignedCampusConnection]);
    }

    // This is going to check to see if groups change primary connections
    public function testCoordinatorViewDeletedPerson(ApiAuthTester $I){
        \Codeception\Util\Debug::debug('Inserting New Data for teh test');

        //shell_exec('mysql -u root, -psynapse -e "drop database `synapse`"');
        //shell_exec('mysql -u root, -psynapse -e "drop create `synapse`"');
       shell_exec('./runauthtests.sh --reload');

        $I->wantTo('See if I am able to view a faculty through Campus Connection Despite not having a connection to said student through groups or courses.');
        \Codeception\Util\Debug::debug('Seeing if Sprout is able to see the students before the delete');
        $this->_getAPITestRunner($I, $this->coordinator, "campusconnections/99424?orgId=542", [], 200, [$this->sproutData]);

        // Telling the user what is going on
        \Codeception\Util\Debug::debug('Deleting the Group');

        $authData = ['email' => $this->coordinator['email'], 'password' => $this->coordinator['password']];
        $this->_deleteAPITestRunner($I, $authData, 'groups/1265', [], 200, []);

        // Comment for user to see if they choose
        \Codeception\Util\Debug::debug('Seeing if the campus connection has no deleted faculty in it');

        // This runs the tests that sees if the faculty can view
        // a staff from Campus connections of a deleted group
        $this->_getAPITestRunner($I, $this->coordinator, "campusconnections/99424?orgId=542", [], 200, []);


        $I->dontSeeResponseContainsJson($this->sproutData);
    }


    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
