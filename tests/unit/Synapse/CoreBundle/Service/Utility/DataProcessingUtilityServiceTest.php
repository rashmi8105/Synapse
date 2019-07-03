<?php
namespace tests\unit\Synapse\CoreBundle\Service\Utility;

use Codeception\Specify;
use Codeception\Test\Unit;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\TotalStudentsListDto;


class DataProcessingUtilityServiceTest extends Unit
{
    use Specify;


    // tests

    public function testFormatListWithConjunction()
    {
        $this->specify("", function ($listAsArray, $conjunction, $expectedResult) {
            $dataProcessingUtilityService = new DataProcessingUtilityService();
            $stringReturned = $dataProcessingUtilityService->formatListWithConjunction($listAsArray, $conjunction);

            verify($stringReturned)->equals($expectedResult);

        }, ['examples' => [
            [[], 'or', ''],
            [['a'], 'and', 'a'],
            [['this', 'that'], 'or', 'this or that'],
            [['a', 'b', 'c'], 'and', 'a, b, and c'],
            [[0, 1, 2, 3], 'or', '0, 1, 2, or 3']
        ]]);
    }


    public function testRecursiveRemovalByArrayKey()
    {
        $this->specify("function returns array with removed element by given key", function ($array, $arrayKey, $expectedResult) {

            $dataProcessingUtility = new DataProcessingUtilityService();
            $createdArray = $dataProcessingUtility->recursiveRemovalByArrayKey($array, $arrayKey);

            $this->assertEquals($expectedResult, $createdArray);
        }, ['examples' => [
            #Scenerio 1 Simple Case
            [['keyToDelete' => 1], 'keyToDelete', []],
            #Scenerio 2  Simple Nested
            [['Key1' => ['keyToDelete' => 1]], 'keyToDelete', ['Key1' => []]],
            #Scenerio 3 Later Element
            [['Key1' => ['Nest1' => 1], 'Key2' => ['Nest2' => 2, 'Nest3' => 3], 'keyToDelete' => ['jeff' => 1]], 'keyToDelete', ['Key1' => ['Nest1' => 1], 'Key2' => ['Nest2' => 2, 'Nest3' => 3]]],
            #Scenerio 4  Deeply Nested
            [['Key1' => ['Nest1' => ['Nest2' => ['Nest3' => ['keyToDelete' => 1]]]]], 'keyToDelete', ['Key1' => ['Nest1' => ['Nest2' => ['Nest3' => []]]]]],
            #Scenerio 5 Different Levels
            [['Key1' => ['Nest1' => ['keyToDelete' => ['Nest3' => ['keyToDelete' => 1]]]]], 'keyToDelete', ['Key1' => ['Nest1' => []]]],
            #Scenerio 6 Invalid Key
            [['dodo' => 1], 'dod', ['dodo' => 1]],
            #Scenerio 7 Invalid Array
            ['dodo', 'dod', 'dodo'],
            #Scenerio 8 Middle of Array
            [['Key1' => ['Nest1' => 1], 'keyToDelete' => ['jeff' => 1], 'Key2' => 4, 'Key3' => ['Nest2' => 2, 'Nest3' => 3]], 'keyToDelete', ['Key1' => ['Nest1' => 1], 'Key2' => 4, 'Key3' => ['Nest2' => 2, 'Nest3' => 3]]],
            #Scenerio 9 Integer Arrays with one string
            [[0 => [0 => 1], 'keyToDelete' => [0 => 1], 1 => 4, 2 => [0 => 2, 1 => 3]], 'keyToDelete', [0 => [0 => 1], 1 => 4, 2 => [0 => 2, 1 => 3]]],
            #Scenerio 10 Integer Keys and Integer arrays
            [[0 => [0 => 1], 3 => [0 => 1], 1 => 4, 2 => [0 => 2, 1 => 3]], 3, [0 => [0 => 1], 1 => 4, 2 => [0 => 2, 1 => 3]]],
            #Scenerio 11
            [['total_students' => 10, 'org_id' => 1, 'gpa_term_summaries_by_year' => [0 => ['gpa_summary_by_term' => ['dummyValue' => 1, "percent_under_2" => 2]]]], "percent_under_2", ['total_students' => 10, 'org_id' => 1, 'gpa_term_summaries_by_year' => [0 => ['gpa_summary_by_term' => ['dummyValue' => 1]]]]]
        ]]);

    }

    public function testFilterMultipleColumnBySingle()
    {

        $sourceArray = [
            ['person_id' => 123, 'gpa' => 2.50, 'academic_year_id' => 2],
            ['person_id' => 4, 'gpa' => 3.50, 'academic_year_id' => 3],
            ['person_id' => 5, 'gpa' => 2.50, 'academic_year_id' => 4],
            ['person_id' => 6, 'gpa' => 2.50, 'academic_year_id' => 5],
            ['person_id' => 7, 'gpa' => 2.50, 'academic_year_id' => 6],
            ['person_id' => 9, 'gpa' => 1.50, 'academic_year_id' => 7]];

        $filterArray = [123, 4, 6, 7, 8];

        $expectedArray = [
            ['person_id' => 123, 'gpa' => 2.50, 'academic_year_id' => 2],
            ['person_id' => 4, 'gpa' => 3.50, 'academic_year_id' => 3],
            ['person_id' => 6, 'gpa' => 2.50, 'academic_year_id' => 5],
            ['person_id' => 7, 'gpa' => 2.50, 'academic_year_id' => 6]
        ];

        $GPAReportService = new DataProcessingUtilityService();

        $gpaTestArray = $GPAReportService->filterMultiColumnSourceBySingleColumnFilter($sourceArray, $filterArray, 'person_id');

        $this->assertEquals($expectedArray, $gpaTestArray);
    }


    public function testRemoveRecords()
    {
        $this->specify("Verify the functionality of the method removeRecords", function ($records, $key, $valuesToRemove, $expectedResult) {
            $dataProcessingUtilityService = new DataProcessingUtilityService();
            $arrayReturned = $dataProcessingUtilityService->removeRecords($records, $key, $valuesToRemove);

            verify($arrayReturned)->equals($expectedResult);

        }, ['examples' =>
            [
                // Example 1:  An empty array is returned untouched.
                [
                    [],
                    'a',
                    [1],
                    []
                ],
                // Example 2:  A single record is removed if its value for the given key matches the value that should be removed.
                [
                    [
                        ['a' => 1, 'b' => 2]
                    ],
                    'a',
                    [1],
                    []
                ],
                // Example 3:  Demonstrates that having extra values in the removal list doesn't affect results.
                [
                    [
                        ['a' => 1, 'b' => 2]
                    ],
                    'a',
                    [1, 2],
                    []
                ],
                // Example 4:  A record is not removed if its value for the given key is not in the removal list (even if some other key has a value in the removal list).
                [
                    [
                        ['a' => 1, 'b' => 2]
                    ],
                    'a',
                    [2],
                    [
                        ['a' => 1, 'b' => 2]
                    ]
                ],
                // Example 5:  If $key is not one of the keys (columns) in $records, the array is returned untouched.
                [
                    [
                        ['a' => 1, 'b' => 2]
                    ],
                    'c',
                    [2],
                    [
                        ['a' => 1, 'b' => 2]
                    ]
                ],
                // Example 6:  Example with two records.
                [
                    [
                        ['a' => 1, 'b' => 2],
                        ['a' => 2, 'b' => 1]
                    ],
                    'b',
                    [1],
                    [
                        ['a' => 1, 'b' => 2]
                    ]
                ],
                // Example 7:  Multiple matching records can be removed.  (This example would fail if we used unset() rather than assembling a new array.)
                [
                    [
                        ['a' => 1, 'b' => 2],
                        ['a' => 2, 'b' => 1],
                        ['a' => 3, 'b' => 3]
                    ],
                    'a',
                    [1, 2],
                    [
                        ['a' => 3, 'b' => 3]
                    ]
                ],
                // Example 8:  Multiple records with the same value can be removed.
                [
                    [
                        ['a' => 1, 'b' => 2],
                        ['a' => 2, 'b' => 1],
                        ['a' => 1, 'b' => 3]
                    ],
                    'a',
                    [1],
                    [
                        ['a' => 2, 'b' => 1]
                    ]
                ]

            ]
        ]);
    }

    public function testValidatePasswordStrength()
    {
        $this->specify("Test validatePasswordStrength function to return true or false", function ($passwordString, $expectedSortByString) {

            $dataProcessingUtilityService = new DataProcessingUtilityService();
            $response = $dataProcessingUtilityService->validatePasswordStrength($passwordString);
            $this->assertInternalType('bool', $response);
            $this->assertEquals($expectedSortByString, $response);


        }, ['examples' =>
            [
                // Example 1 valid password
                ['Qait@123', true],
                // Example 2 invalid password
                ['Qa123', false]]

        ]);
    }

    public function testSetErrorMessageOrValueInArray()
    {
        $this->specify("Verify the functionality of the method setErrorMessageOrValueInArray", function ($records, $errorArray, $expectedResult) {
            $dataProcessingUtilityService = new DataProcessingUtilityService();
            $arrayReturned = $dataProcessingUtilityService->setErrorMessageOrValueInArray($records, $errorArray);

            verify($arrayReturned)->equals($expectedResult);
        },
            ['examples' =>
                [
                    //test for records with errors for invalid fields
                    [
                        [
                            'external_id' => "X00344",
                            'mapworks_internal_id' => 4,
                            'auth_username' => "01234stuffing543217",
                            'firstname' => "firstname1",
                            'lastname' => "lastname1",
                            'title' => str_repeat("Mr.", 110),
                            'primary_email' => "mrtestusergfdgdg444655555254455@mailinator.com",
                            'photo_link' => "www.url.com/photo/link1.jpg",
                            'primary_campus_connection_id' => "user23456",
                            'risk_group_id' => 1,
                            'is_student' => null,
                            'is_faculty' => null
                        ],
                        [
                            'title' => "Title cannot be longer than 100 characters",
                            'external_id' => "External ID is already in use at this organization.",
                            'primary_email' => "Primary Email is already in use."
                        ],
                        [
                            'external_id' =>
                                [
                                    'value' => "X00344",
                                    'message' => "External ID is already in use at this organization."
                                ],
                            'mapworks_internal_id' => 4,
                            'auth_username' => "01234stuffing543217",
                            'firstname' => "firstname1",
                            'lastname' => "lastname1",
                            'title' =>
                                [
                                    'value' => str_repeat("Mr.", 110),
                                    'message' => "Title cannot be longer than 100 characters",
                                ],
                            'primary_email' =>
                                [
                                    'value' => "mrtestusergfdgdg444655555254455@mailinator.com",
                                    'message' => "Primary Email is already in use."
                                ],
                            'photo_link' => "www.url.com/photo/link1.jpg",
                            'primary_campus_connection_id' => "user23456",
                            'risk_group_id' => 1,
                            'is_student' => null,
                            'is_faculty' => null
                        ]
                    ],
                    //test for records with empty error array
                    [
                        [
                            'external_id' => "X00345",
                            'mapworks_internal_id' => 5,
                            'auth_username' => "01234stuffing55",
                            'firstname' => "firstname2",
                            'lastname' => "lastname2",
                            'title' => "Mr.",
                            'primary_email' => "testuser@mailinator.com",
                            'photo_link' => "www.url.com/photo/link2.jpg",
                            'primary_campus_connection_id' => "3456",
                            'risk_group_id' => 1,
                            'is_student' => null,
                            'is_faculty' => null
                        ],
                        [],
                        [
                            'external_id' => "X00345",
                            'mapworks_internal_id' => 5,
                            'auth_username' => "01234stuffing55",
                            'firstname' => "firstname2",
                            'lastname' => "lastname2",
                            'title' => "Mr.",
                            'primary_email' => "testuser@mailinator.com",
                            'photo_link' => "www.url.com/photo/link2.jpg",
                            'primary_campus_connection_id' => "3456",
                            'risk_group_id' => 1,
                            'is_student' => null,
                            'is_faculty' => null
                        ]
                    ]
                ]
            ]);
    }

    public function testSerializeObjectToArray()
    {
        $this->specify("Verify the functionality of the method serializeObjectToArray", function ($object, $expectedResult) {
            $dataProcessingUtilityService = new DataProcessingUtilityService();
            $result = $dataProcessingUtilityService->serializeObjectToArray($object);
            verify($result)->equals($expectedResult);
        },
            ['examples' =>
                [
//                    Example 1: For Valid data
                    [
                        $this->getStudentsListDto(
                            [
                                'studentId' => 4956349,
                                'externalId' => 4956349,
                                'studentFirstName' => 'Carlee',
                                'studentLastName' => 'Zimmerman',
                                'referredById' => '',
                                'referredByFirstName' => '',
                                'referredByLastName' => '',
                                'studentRiskStatus' => 'green',
                                'studentRiskLevel' => '',
                                'studentIntentToLeave' => '',
                                'studentCohorts' => '',
                                'studentClasslevel' => '1st Year/Freshman',
                                'studentLogins' => 0,
                                'lastActivity' => '',
                                'studentStatus' => 1,
                                'studentIntentToLeaveImageName' => 'leave-intent-stay-stated.png',
                                'studentRiskImageName' => 'risk-level-icon-g.png',
                                'primaryEmail' => 'MapworksBetaUser04956349@mailinator.com',
                                'studentRiskColor' => '',
                                'studentIntentColor' => '',
                                'hasPrimaryConnection' => '',
                                'studentIntentToLeaveText' => 'green'
                            ]
                        ),
                        [
                            'studentId' => 4956349,
                            'externalId' => 4956349,
                            'studentFirstName' => 'Carlee',
                            'studentLastName' => 'Zimmerman',
                            'referredById' => '',
                            'referredByFirstName' => '',
                            'referredByLastName' => '',
                            'studentRiskStatus' => 'green',
                            'studentRiskLevel' => '',
                            'studentIntentToLeave' => '',
                            'studentCohorts' => '',
                            'studentClasslevel' => '1st Year/Freshman',
                            'studentLogins' => 0,
                            'lastActivity' => '',
                            'studentStatus' => 1,
                            'studentIntentToLeaveImageName' => 'leave-intent-stay-stated.png',
                            'studentRiskImageName' => 'risk-level-icon-g.png',
                            'primaryEmail' => 'MapworksBetaUser04956349@mailinator.com',
                            'studentRiskColor' => '',
                            'studentIntentColor' => '',
                            'hasPrimaryConnection' => '',
                            'studentIntentToLeaveText' => 'green'
                        ]
                    ],
//                    Example 2: For null values
                    [
                        $this->getStudentsListDto(null),
                        [
                            'studentId' => null,
                            'externalId' => null,
                            'studentFirstName' => '',
                            'studentLastName' => '',
                            'referredById' => '',
                            'referredByFirstName' => '',
                            'referredByLastName' => '',
                            'studentRiskStatus' => '',
                            'studentRiskLevel' => '',
                            'studentIntentToLeave' => '',
                            'studentCohorts' => '',
                            'studentClasslevel' => '',
                            'studentLogins' => null,
                            'lastActivity' => '',
                            'studentStatus' => null,
                            'studentIntentToLeaveImageName' => '',
                            'studentRiskImageName' => '',
                            'primaryEmail' => '',
                            'studentRiskColor' => '',
                            'studentIntentColor' => '',
                            'hasPrimaryConnection' => '',
                            'studentIntentToLeaveText' => ''
                        ]
                    ],
                ]
            ]);
    }

    private function getStudentsListDto($student)
    {
        $totalStudentListDto = new TotalStudentsListDto();
        $totalStudentListDto->setStudentId($student['studentId']);
        $totalStudentListDto->setExternalId($student['externalId']);
        $totalStudentListDto->setStudentFirstName($student['studentFirstName']);
        $totalStudentListDto->setStudentLastName($student['studentLastName']);
        $totalStudentListDto->setReferredById($student['referredById']);
        $totalStudentListDto->setReferredByFirstName($student['referredByFirstName']);
        $totalStudentListDto->setReferredByLastName($student['referredByLastName']);
        $totalStudentListDto->setStudentRiskStatus($student['studentRiskStatus']);
        $totalStudentListDto->setStudentRiskLevel($student['studentRiskLevel']);
        $totalStudentListDto->setStudentIntentToLeave($student['studentIntentToLeave']);
        $totalStudentListDto->setStudentCohorts($student['studentCohorts']);
        $totalStudentListDto->setStudentClasslevel($student['studentClasslevel']);
        $totalStudentListDto->setStudentLogins($student['studentLogins']);
        $totalStudentListDto->setLastActivity($student['lastActivity']);
        $totalStudentListDto->setStudentStatus($student['studentStatus']);
        $totalStudentListDto->setStudentIntentToLeaveImageName($student['studentIntentToLeaveImageName']);
        $totalStudentListDto->setStudentRiskImageName($student['studentRiskImageName']);
        $totalStudentListDto->setPrimaryEmail($student['primaryEmail']);
        $totalStudentListDto->setStudentRiskColor($student['studentRiskColor']);
        $totalStudentListDto->setStudentIntentColor($student['studentIntentColor']);
        $totalStudentListDto->setHasPrimaryConnection($student['hasPrimaryConnection']);
        $totalStudentListDto->setStudentIntentToLeaveText($student['studentIntentToLeaveText']);
        return $totalStudentListDto;
    }

    // tests function convertCamelCasedStringToUnderscoredString
    public function testConvertCamelCasedStringToUnderscoredString()
    {
        $this->specify("convert CamelCase string To Underscore string", function ($camelCaseKey, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);
            $mockContainer = $this->getMock('Container', ['get']);

            $dataProcessingUtilityService = new DataProcessingUtilityService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $dataProcessingUtilityService->convertCamelCasedStringToUnderscoredString($camelCaseKey);
            $this->assertEquals($result, $expectedResult);

        }, [
                'examples' => [
                    // test0
                    [
                        'collegeCode',
                        'college_code'
                    ],
                    // test1
                    [
                        null,
                        null
                    ]
                ]
            ]
        );
    }

    public function testEncrypt()
    {
        $this->specify("Verify the functionality of the method encrypt", function ($textToEncrypt, $encryptionMethod, $secretHash, $expectedResult) {
            $dataProcessingUtilityService = new DataProcessingUtilityService();
            $result = $dataProcessingUtilityService->encrypt($textToEncrypt, $encryptionMethod, $secretHash);
            $this->assertEquals($result, $expectedResult);
        },
            [
                'examples' =>
                    [
                        // Example 1: Test for valid Data
                        [
                            'MapworksBetaUser04992038@mailinator.com',
                            SynapseConstant::ENCRYPTION_METHOD,
                            SynapseConstant::ENCRYPTION_HASH,
                            'xq+TBhfjHxMa6D/2X+Y9fo1Rzv3lxikTx61yXTmqVvU4CWzR2HQ3Prff1rWiKMau'
                        ],
                        // Example 2: Test for null data
                        [
                            '',
                            SynapseConstant::ENCRYPTION_METHOD,
                            SynapseConstant::ENCRYPTION_HASH,
                            'nQhaTwM38GrxhzGgQrPoDQ=='
                        ]
                    ]
            ]);
    }

    public function testRemoveDuplicateElements()
    {
        $this->specify("Verify the functionality of the method removeDuplicateElements", function ($arrayToRemoveDuplicateElements, $key, $expectedResult) {
            $dataProcessingUtilityService = new DataProcessingUtilityService();
            $arrayReturned = $dataProcessingUtilityService->removeDuplicateElements($arrayToRemoveDuplicateElements, $key);
            verify($arrayReturned)->equals($expectedResult);

        }, ['examples' =>
            [
                // example 1: Basic test case
                [
                    // array
                    [
                        0 => ['id' => 0, 'first_name' => 'John', 'lastname' => 'Doe'],
                        1 => ['id' => 0, 'first_name' => 'John', 'lastname' => 'Doe'],
                        2 => ['id' => 1, 'first_name' => 'Jane', 'lastname' => 'Doe']
                    ],
                    // key
                    'id',
                    // expected Results
                    [
                        0 => ['id' => 0, 'first_name' => 'John', 'lastname' => 'Doe'],
                        1 => ['id' => 1, 'first_name' => 'Jane', 'lastname' => 'Doe']
                    ],
                ],
                // example 2: Where key does not exist
                [
                    // array
                    [
                        0 => ['id' => 0, 'first_name' => 'John', 'lastname' => 'Doe'],
                        1 => ['id' => 0, 'first_name' => 'John', 'lastname' => 'Doe'],
                        2 => ['id' => 1, 'first_name' => 'Jane', 'lastname' => 'Doe']
                    ],
                    // key
                    'not exist',
                    // expected Results
                    [
                        0 => ['id' => 0, 'first_name' => 'John', 'lastname' => 'Doe'],
                        1 => ['id' => 0, 'first_name' => 'John', 'lastname' => 'Doe'],
                        2 => ['id' => 1, 'first_name' => 'Jane', 'lastname' => 'Doe']
                    ],
                ],
                // example 3: Where there are duplicates scattered around the array
                [
                    // array
                    [
                        0 => ['id' => 0, 'first_name' => 'John', 'lastname' => 'Doe'],
                        1 => ['id' => 0, 'first_name' => 'John', 'lastname' => 'Doe'],
                        2 => ['id' => 1, 'first_name' => 'Jane', 'lastname' => 'Doe'],
                        4 => ['id' => 0, 'first_name' => 'John', 'lastname' => 'Doe'],
                        5 => ['id' => 0, 'first_name' => 'John', 'lastname' => 'Doe'],
                        3 => ['id' => 0, 'first_name' => 'John', 'lastname' => 'Doe'],

                    ],
                    // key
                    'id',
                    // expected Results
                    [
                        0 => ['id' => 0, 'first_name' => 'John', 'lastname' => 'Doe'],
                        1 => ['id' => 1, 'first_name' => 'Jane', 'lastname' => 'Doe']
                    ],
                ],
                // example 4: Where there are duplicates scattered around the array and string comparison vs numeric
                [
                    // array
                    [
                        0 => ['id' => 'Zero', 'first_name' => 'John', 'lastname' => 'Doe'],
                        1 => ['id' => 'Zero', 'first_name' => 'John', 'lastname' => 'Doe'],
                        2 => ['id' => 'One', 'first_name' => 'Jane', 'lastname' => 'Doe'],
                        4 => ['id' => 'One', 'first_name' => 'John', 'lastname' => 'Doe'],
                        5 => ['id' => 'Zero', 'first_name' => 'John', 'lastname' => 'Doe'],
                        3 => ['id' => 'Zero', 'first_name' => 'John', 'lastname' => 'Doe'],

                    ],
                    // key
                    'id',
                    // expected Results
                    [
                        0 => ['id' => 'Zero', 'first_name' => 'John', 'lastname' => 'Doe'],
                        1 => ['id' => 'One', 'first_name' => 'Jane', 'lastname' => 'Doe']
                    ],
                ],
                // example 5: if you give it a blank array you get a blank array back
                [
                    // array
                    [],
                    // key
                    'not exist',
                    // expected Results
                    [],
                ]
            ]
        ]);
    }

    public function testSortBasedOnSortKey()
    {
        $this->specify("test sortBasedOnSortKey", function ($arrayToBeSorted, $sortKey, $columnToSortOn, $expectedResults) {
            $dataProcessingUtilityService = new DataProcessingUtilityService();
            $arrayReturned = $dataProcessingUtilityService->sortBasedOnSortKey($arrayToBeSorted, $sortKey, $columnToSortOn);
            verify($arrayReturned)->equals($expectedResults);

        },
            ['examples' =>
                [
                    [// Example 1: Basic test case
                        [// array to be sorted
                            0 => [
                                'this element is' => '0',
                                'this element should be' => '2'
                            ],
                            1 => [
                                'this element is' => '1',
                                'this element should be' => '1'
                            ],
                            2 => [
                                'this element is' => '2',
                                'this element should be' => '0'
                            ],
                            3 => [
                                'this element is' => '3',
                                'this element should be' => '3'
                            ],
                        ],
                        // sort key
                        [
                            0 => '0',
                            1 => '1',
                            2 => '2',
                            3 => '3'
                        ],
                        // column to sort on
                        'this element should be',
                        // expected results
                        [
                            0 => [
                                'this element is' => '2',
                                'this element should be' => '0'
                            ],
                            1 => [
                                'this element is' => '1',
                                'this element should be' => '1'
                            ],
                            2 => [
                                'this element is' => '0',
                                'this element should be' => '2'
                            ],
                            3 => [
                                'this element is' => '3',
                                'this element should be' => '3'
                            ]
                        ]
                    ],

                    [ // Example 2 with extras
                        [// array to be sorted
                            0 => [
                                'this element is' => '0',
                                'this element should be' => 'extra 0'
                            ],
                            1 => [
                                'this element is' => '1',
                                'this element should be' => '0'
                            ],
                            2 => [
                                'this element is' => '2',
                                'this element should be' => '1'
                            ],
                            3 => [
                                'this element is' => '3',
                                'this element should be' => 'extra 3'
                            ],
                        ],
                        // sort key
                        [
                            0 => '0',
                            1 => '1',

                        ],
                        // column to sort on
                        'this element should be',
                        // expected results
                        [
                            0 => [
                                'this element is' => '1',
                                'this element should be' => '0'
                            ],
                            1 => [
                                'this element is' => '2',
                                'this element should be' => '1'
                            ],
                            2 => [
                                'this element is' => '0',
                                'this element should be' => 'extra 0'
                            ],
                            3 => [
                                'this element is' => '3',
                                'this element should be' => 'extra 3'
                            ]
                        ]
                    ],
                    [ // Example 3 all extras
                        [// array to be sorted
                            0 => [
                                'this element is' => '0',
                                'this element should be' => 'extra 4'
                            ],
                            1 => [
                                'this element is' => '1',
                                'this element should be' => 'extra 3'
                            ],
                            2 => [
                                'this element is' => '2',
                                'this element should be' => 'extra 2'
                            ],
                            3 => [
                                'this element is' => '3',
                                'this element should be' => 'extra 1'
                            ],
                        ],
                        // sort key
                        [
                            0 => '0',
                            1 => '1',
                            2 => '2',
                            3 => '3',
                        ],
                        // column to sort on
                        'this element should be',
                        // expected results
                        [
                            0 => [
                                'this element is' => '0',
                                'this element should be' => 'extra 4'
                            ],
                            1 => [
                                'this element is' => '1',
                                'this element should be' => 'extra 3'
                            ],
                            2 => [
                                'this element is' => '2',
                                'this element should be' => 'extra 2'
                            ],
                            3 => [
                                'this element is' => '3',
                                'this element should be' => 'extra 1'
                            ]
                        ]
                    ],
                    [ // Example 4 sorting on something that does not exist
                        [// array to be sorted
                            0 => [
                                'this element is' => '0',
                                'this element should be' => 'extra 4'
                            ],
                            1 => [
                                'this element is' => '1',
                                'this element should be' => 'extra 3'
                            ],
                            2 => [
                                'this element is' => '2',
                                'this element should be' => 'extra 2'
                            ],
                            3 => [
                                'this element is' => '3',
                                'this element should be' => 'extra 1'
                            ],
                        ],
                        // sort key
                        [
                            0 => '0',
                            1 => '1',
                            2 => '2',
                            3 => '3',
                        ],
                        // column to sort on
                        'this element that is not there',
                        // expected results
                        [
                            0 => [
                                'this element is' => '0',
                                'this element should be' => 'extra 4'
                            ],
                            1 => [
                                'this element is' => '1',
                                'this element should be' => 'extra 3'
                            ],
                            2 => [
                                'this element is' => '2',
                                'this element should be' => 'extra 2'
                            ],
                            3 => [
                                'this element is' => '3',
                                'this element should be' => 'extra 1'
                            ]
                        ]
                    ],
                ]
            ]
        );
    }


    public function testArrayStringToLowerNullSafeAll()
    {
        $this->specify("test sortBasedOnSortKey", function ($arrayToBeSorted, $expectedResults) {
            $dataProcessingUtilityService = new DataProcessingUtilityService();
            $arrayReturned = $dataProcessingUtilityService->arrayStringToLowerNullSafeAll($arrayToBeSorted);
            verify($arrayReturned)->equals($expectedResults);

        }, [
            'examples' => [
                // 1 average test
                [
                    $array = ['KEY' => 'VALUE', 'KEY1' => 'VALUE', 'KEY2' => ['KEY' => 'VALUE', 'KEY1' => 'VALUE']],
                    $expectedResults = array('key' => 'value', 'key1' => 'value', 'key2' => array('key' => 'value', 'key1' => 'value',))
                ],
                // 2 integers as keys test
                [
                    $array = [1 => 'VALUE', 2 => 'VALUE', 3 => [4 => 'VALUE', 1 => 'VALUE']],
                    $expectedResults = array(1 => 'value', 2 => 'value', 3 => array(4 => 'value', 1 => 'value',))
                ],
                // 3 null safe test
                [
                    $array = [1 => '', 2 => NULL, 3 => [4 => 'VALUE', 1 => NULL]],
                    $expectedResults = array(1 => '', 2 => NULL, 3 => array(4 => 'value', 1 => NULL,))
                ],
                //4 keyless test
                [
                    $array = [1, 2, 3, 4, 5, 6, 5, 'FiVe'],
                    $expectedResults = [1, 2, 3, 4, 5, 6, 5, 'five']
                ]
            ]
        ]);
    }

    public function testNullSafeEqualsArrayDiff()
    {
        $this->specify("test sortBasedOnSortKey", function ($array1, $array2, $expectedResults) {
            $dataProcessingUtilityService = new DataProcessingUtilityService();
            $arrayReturned = $dataProcessingUtilityService->nullSafeEqualsArrayDiff($array1, $array2);
            verify($arrayReturned)->equals($expectedResults);

        }, [
            'examples' => [
                // 1 average test
                [
                    $array1 = [2, 4, 6, 8, 10, 12],
                    $array2 = [1, 1, 2, 3, 5, 8, 13],
                    $expectedResults = [4, 6, 10, 12]
                ],
                // 2 average test
                [
                    $array1 = [1, 1, 2, 3, 5, 8, 13, 21],
                    $array2 = [1, 3, 5, 7, 9, 11, 13],
                    $expectedResults = [2, 8, 21]
                ],
                // 3 null safe test
                [
                    $array1 = [null, 1, 1, 2, 3, 5, 8, 13],
                    $array2 = ["", 1, 1, 2, 3, 5, 8, 13],
                    $expectedResults = [null]
                ],
                // 4 second null safe test
                [
                    $array1 = ["", 1, 1, 2, 3, 5, 8, 13],
                    $array2 = [null, 1, 1, 2, 3, 5, 8, 13],
                    $expectedResults = [""]
                ],
                // 5 string test
                [
                    $array1 = ["one", "ONE", "two", "THREE"],
                    $array2 = ["ONE", "one", "TWO", "three"],
                    $expectedResults = ["two", "THREE"]
                ]
            ]
        ]);
    }
}
