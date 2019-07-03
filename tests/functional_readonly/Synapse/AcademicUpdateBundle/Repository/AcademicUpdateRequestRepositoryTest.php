<?php

use Codeception\TestCase\Test;


class AcademicUpdateRequestRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestRepository
     */
    private $academicUpdateRequestRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->academicUpdateRequestRepository = $this->repositoryResolver->getRepository("SynapseAcademicUpdateBundle:AcademicUpdateRequest");
    }

    public function testGetAcademicUpdatesInOpenRequestsForStudent()
    {
        $this->specify('test get academic updates in open requests for student ', function ($courseId, $organizationId, $studentId, $facultyId, $dateTimeString, $expectedResults) {
            $functionResults = $this->academicUpdateRequestRepository->getAcademicUpdatesInOpenRequestsForStudent($courseId, $organizationId, $studentId, $dateTimeString, $facultyId);
            verify($functionResults)->equals($expectedResults);
        }, ['examples' => [
            [
                //Student with one open academic update request.
                17515,
                196,
                4627393,
                null,
                '2010-01-01 00:00:00',
                [
                    0 => [
                        'academic_update_id' => '16313',
                        'academic_update_request_id' => '26'
                    ]
                ]
            ],
            [
                //Student with one open academic update request, and one closed academic update request.
                17470,
                196,
                4627113,
                null,
                '2010-01-01 00:00:00',
                [
                    0 => [
                        'academic_update_id' => '16563',
                        'academic_update_request_id' => '26'
                    ]
                ]

            ],
            [
                //Student with two open academic update requests.
                18051,
                196,
                4627666,
                null,
                '2010-01-01 00:00:00',
                [
                    0 => [
                        'academic_update_id' => '15514',
                        'academic_update_request_id' => '26'
                    ],
                    1 => [
                        'academic_update_id' => '599618',
                        'academic_update_request_id' => '109'
                    ]
                ]
            ],
            // Fetching academic updates for a specific student which are assigned to a specific faculty.
            [
                17470,
                196,
                4627113,
                4622524,
                '2010-01-01 00:00:00',
                [
                    0 => [
                        'academic_update_id' => '16563',
                        'academic_update_request_id' => '26'
                    ]
                ]

            ],
            // Student with lapsed academic update(s)
            [
                16054,
                195,
                4613995,
                null,
                '2015-10-18 00:00:00',
                [
                    0 => [
                        'academic_update_id' => '1023059',
                        'academic_update_request_id' => '140'
                    ]
                ]
            ]
        ]]);
    }

    public function testGetAcademicUpdateStatusCountsByRequest()
    {
        $this->specify('test getAcademicUpdateStatusCountsByRequest', function ($academicUpdateRequestId, $orgAcademicYearId, $expectedResults) {
            $functionResults = $this->academicUpdateRequestRepository->getAcademicUpdateStatusCountsByRequest($academicUpdateRequestId, $orgAcademicYearId);
            $this->assertEquals($functionResults, $expectedResults);
        }, [
            'examples' => [
                [
                    206,
                    171,
                    [
                        'open' => 3970,
                        'closed' => 238,
                        'saved' => 116
                    ]
                ],
                [
                    154,
                    163,
                    [
                        'open' => 3,
                        'closed' => 2
                    ]
                ],
                [
                    120,
                    169,
                    [
                        'closed' => 44
                    ]
                ]
            ]
        ]);
    }

    public function testGetAcademicUpdatesCountByRequestId()
    {
        $this->specify('test getAcademicUpdatesCountByRequestId', function ($academicUpdateRequestId, $orgAcademicYearId, $facultyRelatedStudentsFlag, $facultyId, $filter, $nonParticipatingFlag, $academicUpdateCountFlag, $expectedResults) {
            $functionResults = $this->academicUpdateRequestRepository->getAcademicUpdatesCountByRequestId($academicUpdateRequestId, $orgAcademicYearId, $facultyRelatedStudentsFlag, $facultyId, $filter, $nonParticipatingFlag, $academicUpdateCountFlag);
            $this->assertEquals($functionResults, $expectedResults);

        }, ['examples' => [

            //Test count of all participating students' academic updates
            [
                72,
                80,
                false,
                null,
                'all',
                false,
                true,
                5
            ],
            //Test count of all participating students' academic updates with data submitted against them.
            [
                72,
                80,
                false,
                null,
                'datasubmitted',
                false,
                true,
                4
            ],
            //Test count of all participating students' academic updates with no data submitted against them.
            [
                72,
                80,
                false,
                null,
                'nodata',
                false,
                true,
                1
            ],
            //Test count of all participating students' academic updates for a specific faculty
            [
                125,
                80,
                true,
                215279,
                'all',
                false,
                true,
                4
            ],
            //Test count of all participating students' academic updates with data submitted against them for a specific faculty.
            [
                125,
                80,
                true,
                229201,
                'datasubmitted',
                false,
                true,
                0
            ],
            //Test count of all participating students' academic updates with no data submitted against them for a specific faculty.
            [
                235,
                80,
                true,
                4737711,
                'nodata',
                false,
                true,
                4
            ],
            //Test count of all participating students with academic updates
            [
                44,
                92,
                false,
                null,
                'all',
                false,
                false,
                452
            ],
            //Test count of all participating students with academic updates with data submitted against them.
            [
                44,
                92,
                false,
                null,
                'datasubmitted',
                false,
                false,
                450
            ],
            //Test count of all participating students with academic updates with no data submitted against them.
            [
                8,
                92,
                false,
                null,
                'nodata',
                false,
                false,
                0
            ],
            //Test count of all participating students with academic updates for a specific faculty
            [
                44,
                92,
                true,
                49952,
                'all',
                false,
                false,
                9
            ],
            //Test count of all participating students with academic updates with data submitted against them for a specific faculty.
            [
                44,
                92,
                true,
                49952,
                'datasubmitted',
                false,
                false,
                9
            ],
            //Test count of all participating students with academic updates with no data submitted against them for a specific faculty.
            [
                44,
                92,
                true,
                49952,
                'nodata',
                false,
                false,
                0
            ],
            //Test count of all non-participating students with academic updates
            [
                44,
                92,
                false,
                null,
                'all',
                true,
                false,
                0
            ],
            //Test count of all non-participating students with academic updates with data submitted against them.
            [
                44,
                92,
                false,
                null,
                'datasubmitted',
                true,
                false,
                0
            ],
            //Test count of all non-participating students with academic updates with no data submitted against them.
            [
                44,
                92,
                false,
                null,
                'nodata',
                true,
                false,
                0
            ],
            //Test count of all non-participating students with academic updates for a specific faculty
            [
                44,
                92,
                true,
                49952,
                'all',
                true,
                false,
                0
            ],
            //Test count of all non-participating students with academic updates with data submitted against them for a specific faculty.
            [
                44,
                92,
                true,
                49952,
                'datasubmitted',
                true,
                false,
                0
            ],
            //Test count of all non-participating students with academic updates with no data submitted against them for a specific faculty.
            [
                44,
                92,
                true,
                49952,
                'nodata',
                true,
                false,
                0
            ],
            //Invalid value for the academic update request ID
            [
                "White",
                92,
                true,
                49952,
                'nodata',
                true,
                false,
                0
            ],
            //Null value for the academic update request ID
            [
                null,
                92,
                true,
                49952,
                'nodata',
                true,
                false,
                0
            ],
            //Invalid value for the academic year ID
            [
                44,
                "Smith",
                true,
                49952,
                'nodata',
                true,
                false,
                0
            ],
            //Null value for the academic year ID
            [
                44,
                null,
                true,
                49952,
                'nodata',
                true,
                false,
                0
            ],
            //Invalid value for the faculty flag
            [
                44,
                92,
                "Watt",
                49952,
                'nodata',
                true,
                false,
                0
            ],
            //Null value for the faculty flag
            [
                44,
                92,
                null,
                49952,
                'nodata',
                true,
                false,
                0
            ],
            //Invalid value for the faculty ID
            [
                44,
                92,
                true,
                "Freeney",
                'nodata',
                true,
                false,
                0
            ],
            //Null value for the faculty ID
            [
                44,
                92,
                true,
                null,
                'nodata',
                true,
                false,
                0
            ],
            //Invalid value for the filter
            [
                44,
                92,
                true,
                49952,
                "Hendricks",
                true,
                false,
                0
            ],
            //Null value for the filter
            [
                44,
                92,
                true,
                49952,
                null,
                true,
                false,
                0
            ],
            //Invalid value for the participant flag
            [
                44,
                92,
                true,
                49952,
                'nodata',
                "Taylor",
                false,
                0
            ],
            //Null value for the participant flag
            [
                44,
                92,
                true,
                49952,
                'nodata',
                null,
                false,
                0
            ],
            //Invalid value for the count flag
            [
                44,
                92,
                true,
                49952,
                'nodata',
                true,
                "Dawkins",
                0
            ],
            //Null value for the count flag
            [
                44,
                92,
                true,
                49952,
                'nodata',
                true,
                null,
                0
            ]
        ]]);
    }

    public function testGetAllAcademicUpdateRequestDetailsById()
    {
        $this->specify("test getAllAcademicUpdateRequestDetailsById", function ($expectedResults, $auRequestId, $currentAcademicYear, $currentDate, $filter = 'all', $offset = null, $rowsReturned = null, $outputFormat = null) {
            $results = $this->academicUpdateRequestRepository->getAllAcademicUpdateRequestDetailsById($auRequestId, $currentAcademicYear, $currentDate, $filter, $offset, $rowsReturned, $outputFormat);

            $this->assertEquals($expectedResults, $results);
        }, ['examples' => [


            [
                // Paginated list of results  participant students (active and Inactive) in request ID 15.
                [

                    [
                        'request_id' => "15",
                        'request_name' => "Some AU Name",
                        'request_description' => "This description is useless",
                        'request_created' => "08/28/2015",
                        'request_due' => "08/30/2015",
                        'request_status' => "closed",
                        'request_from_firstname' => "Gianni",
                        'request_from_lastname' => "Chang",
                        'request_from' => "Gianni Chang",
                        'is_bypassed' => "",
                        'academic_update_id' => "1900",
                        'student_id' => "4557182",
                        'student_external_id' => "4557182",
                        'student_firstname' => "Lawrence",
                        'student_lastname' => "Payne",
                        'student_status' => 1,
                        'academic_update_status' => "open",
                        'student_risk' => "",
                        'student_grade' => "",
                        'student_absences' => "",
                        'student_comments' => "",
                        'student_refer' => "",
                        'student_send' => "",
                        'course_section_id' => "Fall COR 1 F",
                        'course_id' => "8881",
                        'subject_code' => "FOT",
                        'course_number' => "43",
                        'course_name' => "Test Course",
                        'course_section_name' => "IH",
                        'department_name' => "FOT",
                        'academic_year_name' => "2015-2016",
                        'academic_term_name' => "Fall 2015"
                    ],
                    [
                        'request_id' => "15",
                        'request_name' => "Some AU Name",
                        'request_description' => "This description is useless",
                        'request_created' => "08/28/2015",
                        'request_due' => "08/30/2015",
                        'request_status' => "closed",
                        'request_from_firstname' => "Gianni",
                        'request_from_lastname' => "Chang",
                        'request_from' => "Gianni Chang",
                        'is_bypassed' => "",
                        'academic_update_id' => "1898",
                        'student_id' => "4557372",
                        'student_external_id' => "4557372",
                        'student_firstname' => "Byron",
                        'student_lastname' => "Ramsey",
                        'student_status' => 1,
                        'academic_update_status' => "open",
                        'student_risk' => "",
                        'student_grade' => "",
                        'student_absences' => "",
                        'student_comments' => "",
                        'student_refer' => "",
                        'student_send' => "",
                        'course_section_id' => "Fall COR 1 F",
                        'course_id' => "8881",
                        'subject_code' => "FOT",
                        'course_number' => "43",
                        'course_name' => "Test Course",
                        'course_section_name' => "IH",
                        'department_name' => "FOT",
                        'academic_year_name' => "2015-2016",
                        'academic_term_name' => "Fall 2015"
                    ],
                    [
                        'request_id' => "15",
                        'request_name' => "Some AU Name",
                        'request_description' => "This description is useless",
                        'request_created' => "08/28/2015",
                        'request_due' => "08/30/2015",
                        'request_status' => "closed",
                        'request_from_firstname' => "Gianni",
                        'request_from_lastname' => "Chang",
                        'request_from' => "Gianni Chang",
                        'is_bypassed' => "",
                        'academic_update_id' => "1908",
                        'student_id' => "4557091",
                        'student_external_id' => "4557091",
                        'student_firstname' => "Kash",
                        'student_lastname' => "Sullivan",
                        'student_status' => 0,
                        'academic_update_status' => "open",
                        'student_risk' => "",
                        'student_grade' => "",
                        'student_absences' => "",
                        'student_comments' => "",
                        'student_refer' => "",
                        'student_send' => "",
                        'course_section_id' => "Fall COR 1 F",
                        'course_id' => "8881",
                        'subject_code' => "FOT",
                        'course_number' => "43",
                        'course_name' => "Test Course",
                        'course_section_name' => "IH",
                        'department_name' => "FOT",
                        'academic_year_name' => "2015-2016",
                        'academic_term_name' => "Fall 2015"
                    ]
                ],
                15,
                171,
                "2016-10-01 00:00:00",
                'all',
                5,
                3
            ],

            [
                // Paginated list of results for current term participant students in request ID 41
                [
                    0 => [
                        'request_id' => '41',
                        'request_name' => 'Some AU Name',
                        'request_description' => 'This description is useless',
                        'request_created' => '09/21/2015',
                        'request_due' => '10/03/2015',
                        'request_status' => 'open',
                        'request_from_firstname' => 'Jesus',
                        'request_from_lastname' => 'Nelson',
                        'is_bypassed' => '0',
                        'academic_update_id' => '79816',
                        'student_id' => '972883',
                        'student_external_id' => '972883',
                        'student_firstname' => 'Atticus',
                        'student_lastname' => 'Maynard',
                        'student_status' => '1',
                        'academic_update_status' => 'closed',
                        'student_risk' => null,
                        'student_grade' => 'D',
                        'student_absences' => null,
                        'student_comments' => 'Currently a D+ after 25% of class points.',
                        'student_refer' => null,
                        'student_send' => null,
                        'course_section_id' => 'ACCT 101_01',
                        'course_id' => '99559',
                        'subject_code' => 'DCCV',
                        'course_number' => '403',
                        'course_name' => 'Test Course',
                        'course_section_name' => '33',
                        'department_name' => 'DCCV',
                        'academic_year_name' => '2015-2016',
                        'academic_term_name' => 'FA2015',
                        'request_from' => 'Jesus Nelson'
                    ],
                    1 => [
                        'request_id' => '41',
                        'request_name' => 'Some AU Name',
                        'request_description' => 'This description is useless',
                        'request_created' => '09/21/2015',
                        'request_due' => '10/03/2015',
                        'request_status' => 'open',
                        'request_from_firstname' => 'Jesus',
                        'request_from_lastname' => 'Nelson',
                        'is_bypassed' => '0',
                        'academic_update_id' => '79568',
                        'student_id' => '4621811',
                        'student_external_id' => '4621811',
                        'student_firstname' => 'Lilly',
                        'student_lastname' => 'Ayers',
                        'student_status' => '1',
                        'academic_update_status' => 'closed',
                        'student_risk' => null,
                        'student_grade' => 'F',
                        'student_absences' => null,
                        'student_comments' => 'Only took first quiz - we have had a second quiz and a test - probably has withdrawn from the course.',
                        'student_refer' => null,
                        'student_send' => null,
                        'course_section_id' => 'ACCT 101_02',
                        'course_id' => '99572',
                        'subject_code' => 'DCCV',
                        'course_number' => '403',
                        'course_name' => 'Test Course',
                        'course_section_name' => '34',
                        'department_name' => 'DCCV',
                        'academic_year_name' => '2015-2016',
                        'academic_term_name' => 'FA2015',
                        'request_from' => 'Jesus Nelson'
                    ],
                    2 => [
                        'request_id' => '41',
                        'request_name' => 'Some AU Name',
                        'request_description' => 'This description is useless',
                        'request_created' => '09/21/2015',
                        'request_due' => '10/03/2015',
                        'request_status' => 'open',
                        'request_from_firstname' => 'Jesus',
                        'request_from_lastname' => 'Nelson',
                        'is_bypassed' => '0',
                        'academic_update_id' => '79742',
                        'student_id' => '4621472',
                        'student_external_id' => '4621472',
                        'student_firstname' => 'Leandro',
                        'student_lastname' => 'French',
                        'student_status' => '1',
                        'academic_update_status' => 'closed',
                        'student_risk' => null,
                        'student_grade' => 'C',
                        'student_absences' => null,
                        'student_comments' => 'Currently a C+ after 25% of class points',
                        'student_refer' => null,
                        'student_send' => null,
                        'course_section_id' => 'ACCT 101_02',
                        'course_id' => '99572',
                        'subject_code' => 'DCCV',
                        'course_number' => '403',
                        'course_name' => 'Test Course',
                        'course_section_name' => '34',
                        'department_name' => 'DCCV',
                        'academic_year_name' => '2015-2016',
                        'academic_term_name' => 'FA2015',
                        'request_from' => 'Jesus Nelson'
                    ],
                    3 => [
                        'request_id' => '41',
                        'request_name' => 'Some AU Name',
                        'request_description' => 'This description is useless',
                        'request_created' => '09/21/2015',
                        'request_due' => '10/03/2015',
                        'request_status' => 'open',
                        'request_from_firstname' => 'Jesus',
                        'request_from_lastname' => 'Nelson',
                        'is_bypassed' => null,
                        'academic_update_id' => '79696',
                        'student_id' => '972899',
                        'student_external_id' => '972899',
                        'student_firstname' => 'Brennan',
                        'student_lastname' => 'Costa',
                        'student_status' => '1',
                        'academic_update_status' => 'closed',
                        'student_risk' => null,
                        'student_grade' => 'D',
                        'student_absences' => null,
                        'student_comments' => null,
                        'student_refer' => null,
                        'student_send' => null,
                        'course_section_id' => 'ACCT 102_02',
                        'course_id' => '100549',
                        'subject_code' => 'DCCV',
                        'course_number' => '404',
                        'course_name' => 'Test Course',
                        'course_section_name' => '34',
                        'department_name' => 'DCCV',
                        'academic_year_name' => '2015-2016',
                        'academic_term_name' => 'FA2015',
                        'request_from' => 'Jesus Nelson'
                    ],
                    4 => [
                        'request_id' => '41',
                        'request_name' => 'Some AU Name',
                        'request_description' => 'This description is useless',
                        'request_created' => '09/21/2015',
                        'request_due' => '10/03/2015',
                        'request_status' => 'open',
                        'request_from_firstname' => 'Jesus',
                        'request_from_lastname' => 'Nelson',
                        'is_bypassed' => '0',
                        'academic_update_id' => '79800',
                        'student_id' => '644262',
                        'student_external_id' => '644262',
                        'student_firstname' => 'Lainey',
                        'student_lastname' => 'Reid',
                        'student_status' => '1',
                        'academic_update_status' => 'closed',
                        'student_risk' => null,
                        'student_grade' => 'D',
                        'student_absences' => null,
                        'student_comments' => 'D grade after 25% of class points.',
                        'student_refer' => null,
                        'student_send' => null,
                        'course_section_id' => 'ACCT 211_01',
                        'course_id' => '99595',
                        'subject_code' => 'DCCV',
                        'course_number' => '513',
                        'course_name' => 'Test Course',
                        'course_section_name' => '33',
                        'department_name' => 'DCCV',
                        'academic_year_name' => '2015-2016',
                        'academic_term_name' => 'FA2015',
                        'request_from' => 'Jesus Nelson'
                    ]
                ],
                41,
                99,
                "2015-10-01 00:00:00",
                'all',
                0,
                5
            ],
            [
                // Paginated list of results for current term participant students in request ID 41. Invalid year ID
                [],
                41,
                "Jones",
                "2015-10-01 00:00:00",
                'all',
                0,
                5
            ],
            [
                // Paginated list of results for current term participant students in invalid request id
                [],
                "Shields",
                99,
                "2015-10-01 00:00:00",
                'all',
                0,
                5
            ],
            [
                // Paginated list of results for current term participant students in request id 41. Invalid filter condition
                [
                    0 => [
                        'request_id' => '41',
                        'request_name' => 'Some AU Name',
                        'request_description' => 'This description is useless',
                        'request_created' => '09/21/2015',
                        'request_due' => '10/03/2015',
                        'request_status' => 'open',
                        'request_from_firstname' => 'Jesus',
                        'request_from_lastname' => 'Nelson',
                        'is_bypassed' => '0',
                        'academic_update_id' => '79816',
                        'student_id' => '972883',
                        'student_external_id' => '972883',
                        'student_firstname' => 'Atticus',
                        'student_lastname' => 'Maynard',
                        'student_status' => '1',
                        'academic_update_status' => 'closed',
                        'student_risk' => null,
                        'student_grade' => 'D',
                        'student_absences' => null,
                        'student_comments' => 'Currently a D+ after 25% of class points.',
                        'student_refer' => null,
                        'student_send' => null,
                        'course_section_id' => 'ACCT 101_01',
                        'course_id' => '99559',
                        'subject_code' => 'DCCV',
                        'course_number' => '403',
                        'course_name' => 'Test Course',
                        'course_section_name' => '33',
                        'department_name' => 'DCCV',
                        'academic_year_name' => '2015-2016',
                        'academic_term_name' => 'FA2015',
                        'request_from' => 'Jesus Nelson'
                    ],
                ],
                41,
                99,
                "2015-10-01 00:00:00",
                'all',
                0,
                1,
                "Ogden"
            ],
            [
                // Paginated list of results for students in request id 41. Earlier participation year ID.
                [
                    0 => [
                        'request_id' => '41',
                        'request_name' => 'Some AU Name',
                        'request_description' => 'This description is useless',
                        'request_created' => '09/21/2015',
                        'request_due' => '10/03/2015',
                        'request_status' => 'open',
                        'request_from_firstname' => 'Jesus',
                        'request_from_lastname' => 'Nelson',
                        'is_bypassed' => '0',
                        'academic_update_id' => '79816',
                        'student_id' => '972883',
                        'student_external_id' => '972883',
                        'student_firstname' => 'Atticus',
                        'student_lastname' => 'Maynard',
                        'student_status' => '1',
                        'academic_update_status' => 'closed',
                        'student_risk' => null,
                        'student_grade' => 'D',
                        'student_absences' => null,
                        'student_comments' => 'Currently a D+ after 25% of class points.',
                        'student_refer' => null,
                        'student_send' => null,
                        'course_section_id' => 'ACCT 101_01',
                        'course_id' => '99559',
                        'subject_code' => 'DCCV',
                        'course_number' => '403',
                        'course_name' => 'Test Course',
                        'course_section_name' => '33',
                        'department_name' => 'DCCV',
                        'academic_year_name' => '2015-2016',
                        'academic_term_name' => 'FA2015',
                        'request_from' => 'Jesus Nelson'
                    ],
                ],
                41,
                59,
                "2015-10-01 00:00:00",
                'all',
                0,
                1
            ],
            [
                // Paginated list of results for current term participant students in request id 41. Changed filter condition
                [
                    0 => [
                        'request_id' => '41',
                        'request_name' => 'Some AU Name',
                        'request_description' => 'This description is useless',
                        'request_created' => '09/21/2015',
                        'request_due' => '10/03/2015',
                        'request_status' => 'open',
                        'request_from_firstname' => 'Jesus',
                        'request_from_lastname' => 'Nelson',
                        'is_bypassed' => '0',
                        'academic_update_id' => '79816',
                        'student_id' => '972883',
                        'student_external_id' => '972883',
                        'student_firstname' => 'Atticus',
                        'student_lastname' => 'Maynard',
                        'student_status' => '1',
                        'academic_update_status' => 'closed',
                        'student_risk' => null,
                        'student_grade' => 'D',
                        'student_absences' => null,
                        'student_comments' => 'Currently a D+ after 25% of class points.',
                        'student_refer' => null,
                        'student_send' => null,
                        'course_section_id' => 'ACCT 101_01',
                        'course_id' => '99559',
                        'subject_code' => 'DCCV',
                        'course_number' => '403',
                        'course_name' => 'Test Course',
                        'course_section_name' => '33',
                        'department_name' => 'DCCV',
                        'academic_year_name' => '2015-2016',
                        'academic_term_name' => 'FA2015',
                        'request_from' => 'Jesus Nelson'
                    ],
                ],
                41,
                99,
                "2015-10-01 00:00:00",
                'datasubmitted',
                0,
                1
            ],
            [
                // Paginated list of results for current term participant students in request id 41. Changed filter condition
                [
                    0 => [
                        'request_id' => '41',
                        'request_name' => 'Some AU Name',
                        'request_description' => 'This description is useless',
                        'request_created' => '09/21/2015',
                        'request_due' => '10/03/2015',
                        'request_status' => 'open',
                        'request_from_firstname' => 'Jesus',
                        'request_from_lastname' => 'Nelson',
                        'is_bypassed' => null,
                        'academic_update_id' => '79644',
                        'student_id' => '4621284',
                        'student_external_id' => '4621284',
                        'student_firstname' => 'Raphael',
                        'student_lastname' => 'Rios',
                        'student_status' => '1',
                        'academic_update_status' => 'open',
                        'student_risk' => null,
                        'student_grade' => null,
                        'student_absences' => null,
                        'student_comments' => null,
                        'student_refer' => null,
                        'student_send' => null,
                        'course_section_id' => 'CHEM 103L_06',
                        'course_id' => '99777',
                        'subject_code' => 'FHEO',
                        'course_number' => '403N',
                        'course_name' => 'Test Course',
                        'course_section_name' => '38',
                        'department_name' => 'FHEO',
                        'academic_year_name' => '2015-2016',
                        'academic_term_name' => 'FA2015',
                        'request_from' => 'Jesus Nelson'
                    ],
                ],
                41,
                99,
                "2015-10-01 00:00:00",
                'nodata',
                0,
                1
            ],

        ]]);
    }


    public function testGetAcademicUpdateRequestCompletionStatistics()
    {
        $this->specify('', function ($expectedResults, $academicUpdateRequestId = null, $facultyId = null) {
            $functionResults = $this->academicUpdateRequestRepository->getAcademicUpdateRequestCompletionStatistics($academicUpdateRequestId, $facultyId);
            verify($expectedResults)->equals($functionResults);
        }, ['examples' => [
            //Example 1: Valid academic update request ID
            [
                [
                    'closed_academic_updates' => '5',
                    'open_academic_updates' => '0',
                    'saved_academic_updates' => '0',
                    'total_academic_updates' => '5',
                    'completion_percentage' => '100.00'
                ],
                72
            ],
            //Example 2: Valid academic update request ID
            [
                [
                    'closed_academic_updates' => '46',
                    'open_academic_updates' => '0',
                    'saved_academic_updates' => '0',
                    'total_academic_updates' => '46',
                    'completion_percentage' => '100.00'
                ],
                129
            ],
            //Example 3: Valid academic update request ID
            [
                [
                    'closed_academic_updates' => '6888',
                    'open_academic_updates' => '17582',
                    'saved_academic_updates' => '347',
                    'total_academic_updates' => '24817',
                    'completion_percentage' => '27.76'
                ],
                245
            ]
            ,
            //Example 4: No academic update request ID
            [
                [
                    'closed_academic_updates' => null,
                    'open_academic_updates' => null,
                    'saved_academic_updates' => null,
                    'total_academic_updates' => '0',
                    'completion_percentage' => null
                ]
            ],
            //Example 5: Invalid academic update request ID
            [
                [
                    'closed_academic_updates' => null,
                    'open_academic_updates' => null,
                    'saved_academic_updates' => null,
                    'total_academic_updates' => '0',
                    'completion_percentage' => null
                ],
                "I'm not an integer"
            ],
            //Example 6: Specific faculty assigned - 76500
            [
                [
                    'closed_academic_updates' => '33',
                    'open_academic_updates' => '0',
                    'saved_academic_updates' => '0',
                    'total_academic_updates' => '33',
                    'completion_percentage' => '100'
                ],
                245,
                76500
            ],
            //Example 7: Invalid faculty ID
            [
                [
                    'closed_academic_updates' => null,
                    'open_academic_updates' => null,
                    'saved_academic_updates' => null,
                    'total_academic_updates' => '0',
                    'completion_percentage' => null
                ],
                245,
                1111111111111111111111111
            ],
        ]]);
    }
}