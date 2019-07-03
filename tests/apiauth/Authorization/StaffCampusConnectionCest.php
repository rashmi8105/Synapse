<?php
/**
 * Created by PhpStorm.
 * User: imeyers
 * Date: 7/10/15
 * Time: 12:18 PM
 */

require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class StaffCampusConnectionCest extends SynapseRestfulTestBase
{
    // This faculty will test all of the Campus Connections
    // The valid coordinator has a connection with the student
    // the invalid coordinator does not have a connection with
    // the student
    private $validFaculty = [
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

    private $aggOnly = [
        'email' => 'argus.filch@mailinator.com',
        'password' => 'password1!',
        'id' => 99436,
        'orgId' => 542,
        'langId' => 1
    ];

    private $validFacultyValidPrimaryConnection = [
        'organization_id' => "542",
        'student_list' =>
            [
                0=>[
                    'student_id' => "99422",
                    'staff_id' => 99440
                ],
            ],

    ];
    private $invalidFacultyValidPrimaryConnection = [
        'organization_id' => "542",
        'student_list' =>
            [
                0=>[
                    'student_id' => "99422",
                    'staff_id' => 99705
                ],
            ],
    ];
    private $validFacultyInvalidPrimaryConnection = [
        'organization_id' => "542",
        'student_list' =>
            [
                0=>[
                    'student_id' => "99422",
                    'staff_id' => 99447
                ],
            ],
    ];


    private $invalidFacultyInvalidPrimaryConnection = [
        'organization_id' => "542",
        'student_list' =>
            [
                0=>[
                    'student_id' => "99422",
                    'staff_id' => 99447
                ],
            ],
    ];

    private $facultyAggPrimaryConnection = [
        'organization_id' => "542",
        'student_list' =>
            [
                0=>[
                    'student_id' => "99422",
                    'staff_id' => 9936
                ],
            ],
    ];
    private $facultyInvalidAggPrimaryConnection = [
        'organization_id' => "542",
        'student_list' =>
            [
                0=>[
                    'student_id' => "99422",
                    'staff_id' => 99436
                ],
            ],
    ];


    private $campusConnection = [
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
                                                'group_id' => '1418',
                                                'group_name' => 'All Students',
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
                                'is_invisible' => false,
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
                                'is_invisible' => false,
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
                                'is_invisible' => false,
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
                                'is_invisible' => false,
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
                                'is_invisible' => false,
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
                                'is_invisible' => false,
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
                                'is_invisible' => false,
                            ),
                        7 =>
                          /*  array (
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
                                'is_invisible' => false,
                            ),
                        8 =>*/
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
                                'is_invisible' => false,
                            ),
                    ),
            ),

    ];


    private $validErrorMessage = [
        'errors' =>
            array (
                0 => 'You do not have coordinator access',
            ),
    ];


    // View the page
    public function testFacultyViewCampusConnections(ApiAuthTester $I)
    {
        $I->wantTo('As a faculty, I should be able to view my Campus Connections');
        $this->_getAPITestRunner($I, $this->validFaculty, 'campusconnections/99422?orgId=542', [], 200, $this->campusConnection);
    }

    public function testInvalidFacultyViewCampusConnections(ApiAuthTester $I, $scenario)
    {
        $I->wantTo("As a faculty with no connection to the student, I should not be able to view the student's Campus Connections");
        $this->_getAPITestRunner($I, $this->invalidFaculty, 'campusconnections/99422?orgId=542', [], 403, []);
    }

    public function testAggOnlyViewCampusConnections(ApiAuthTester $I, $scenario)
    {
        $I->wantTo("As a faculty with Aggregate only connection to the student,  I should not be able to view the student's Campus Connections");
        $this->_getAPITestRunner($I, $this->aggOnly, 'campusconnections/99422?orgId=542', [], 403, []);
    }

    // Add in a faculty with a connection with the student
    public function testFacultyMakeValidFacultyPrimaryConnection(ApiAuthTester $I){
        $I->wantTo("As a faculty, I should not be able to change a student's Primary Campus Connection");
        $this->_postAPITestRunner($I, $this->validFaculty, 'campusconnections/primaryconnection', $this->validFacultyValidPrimaryConnection, 400, [$this->validErrorMessage]);
    }

    public function testInvalidFacultyMakeValidFacultyPrimaryConnection(ApiAuthTester $I){
        $I->wantTo("As a faculty with no connection to the student, I should not be able to change the student's Primary Campus Connection");
        $this->_postAPITestRunner($I, $this->invalidFaculty, 'campusconnections/primaryconnection', $this->invalidFacultyValidPrimaryConnection, 400, [$this->validErrorMessage]);
    }

    // Add in a faculty without a connection with a student
    public function testFacultyMakeInvalidFacultyPrimaryConnection(ApiAuthTester $I){
        $I->wantTo("As a faculty, I should not be able to change a student's Primary Campus Connection to an invalid faculty");
        $this->_postAPITestRunner($I, $this->validFaculty, 'campusconnections/primaryconnection', $this->validFacultyInvalidPrimaryConnection, 400, [$this->validErrorMessage]);
    }

    public function testInvalidFacultyMakeInvalidFacultyPrimaryConnection(ApiAuthTester $I){
        $I->wantTo("As a faculty with no connection to the student, I should not be able to change the student's Primary Campus Connection to an invalid faculty");
        $this->_postAPITestRunner($I, $this->invalidFaculty, 'campusconnections/primaryconnection', $this->invalidFacultyInvalidPrimaryConnection, 400, [$this->validErrorMessage]);
    }

    // Add in a faculty with a connection to the student with aggregate only access
    public function testFacultyMakeAggregateFacultyPrimaryConnection(ApiAuthTester $I){
        $I->wantTo("As a faculty, I should not be able to change a student's Primary Campus Connection to an aggregate only faculty");
        $this->_postAPITestRunner($I, $this->validFaculty, 'campusconnections/primaryconnection', $this->facultyAggPrimaryConnection, 400, [$this->validErrorMessage]);
    }

    public function testInvalidFacultyMakeAggregateFacultyPrimaryConnection(ApiAuthTester $I){
        $I->wantTo("As a faculty with no connection to the student, I should not be able to change the student's Primary Campus Connection to an Aggregate only faculty");
        $this->_postAPITestRunner($I, $this->invalidFaculty, 'campusconnections/primaryconnection', $this->facultyInvalidAggPrimaryConnection, 400, [$this->validErrorMessage]);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
