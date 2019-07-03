<?php

use Codeception\TestCase\Test;


class GroupResponseReportDAOTest extends Test
{
    use \Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\ReportsBundle\DAO\GroupResponseReportDAO
     */
    private $groupResponseReportDAO;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->groupResponseReportDAO = $this->container->get('group_response_report_dao');
    }

    public function testGetOverallCountGroupStudentCountAndResponseRateByFaculty()
    {
        $this->specify("Verify the functionality of the method getOverallCountGroupStudentCountAndResponseRateByFaculty",
        function ($facultyId, $orgAcademicYearId, $cohort, $surveyId, $filterCriteria, $expectedResult)
        {

            $result = $this->groupResponseReportDAO
                ->getOverallCountGroupStudentCountAndResponseRateByFaculty($facultyId, $orgAcademicYearId, $cohort, $surveyId, $filterCriteria);

            verify($result)->equals($expectedResult);
        }, ["examples"=>
                [// Example 1: Overlapping groups; the faculty member is directly in these groups.
                    [4883099, 157, 1, 11, null, [
                            [
                                'student_id_cnt' => '12',
                                'responded' => '12'
                            ]
                        ]
                    ],
                    // Example 2: The faculty member is in Stark Hall.
                    // The report should include both Stark Hall and all subgroups (which the students are actually in).
                    [4883175, 157, 1, 11, null, [
                            [
                                'student_id_cnt' => '21',
                                'responded' => '21'
                            ]
                        ]
                    ],
                    // Example 3: This faculty member is two levels above the students,
                    // and should be able to see all subgroups below (and including) his group.
                    [4883174, 157, 1, 11, null, [
                            [
                                'student_id_cnt' => '55',
                                'responded' => '55'
                            ]
                        ]
                    ],
                    // Example 4a: Response rates of less than 100% (org 57)
                    [67638, 143, 1, 11, null, [
                            [
                                'student_id_cnt' => '205',
                                'responded' => '183'
                            ]
                        ]
                    ],
                    // Example 4b: Overall count with subgroup filter
                    [67638, 143, 1, 11, ['group_selection' => 'sub_groups'], [
                            [
                                'student_id_cnt' => '20',
                                'responded' => '18'
                            ]
                        ]
                    ],
                    // Example 5a: Faculty is directly in both a group (271653) and its subgroup (366324).
                    // ESPRJ-10397 uncovered the issue that students were being double counted in the subgroup
                    // (and not counted at all in the parent group).
                    [111696, 103, 1, 11, null, [
                            [
                                'student_id_cnt' => '393',
                               'responded' => '318'
                            ]
                        ]
                    ],
                    // Example 5b: Overall count/subgroup
                    [111696, 103, 1, 11, ['parent_group' => '13'], [
                            [
                                'student_id_cnt' => '393',
                                'responded' => '318'
                            ]
                        ]
                    ]
                ]
            ]);
    }


    public function testGetGroupStudentCountAndResponseRateByFaculty()
    {
        $this->specify("Verify the functionality of the method getGroupStudentCountAndResponseRateByFaculty",
            function($facultyId, $orgAcademicYearId, $cohort, $surveyId, $filterCriteria, $sortBy, $sortDirection, $expectedResult){

                $result = $this->groupResponseReportDAO
                    ->getGroupStudentCountAndResponseRateByFaculty($facultyId, $orgAcademicYearId, $cohort, $surveyId, $filterCriteria, $sortBy, $sortDirection);

                verify($result)->equals($expectedResult);

            }, ["examples"=>
                [
                    // Example 1a: Overlapping groups; the faculty member is directly in these groups.
                    [4883099, 157, 1, 11, null, null, null, [
                        [
                            'group_name' => 'Cross Country',
                            'org_group_id' => '370658',
                            'external_id' => 'XC',
                            'parent_group' => 'Athletics',
                            'student_id_cnt' => '1',
                            'responded' => '1',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Indoor Track and Field',
                            'org_group_id' => '370659',
                            'external_id' => 'ITF',
                            'parent_group' => 'Athletics',
                            'student_id_cnt' => '9',
                            'responded' => '9',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Outdoor Track and Field',
                            'org_group_id' => '370660',
                            'external_id' => 'OTF',
                            'parent_group' => 'Athletics',
                            'student_id_cnt' => '12',
                            'responded' => '12',
                            'response_rate' => '100'
                        ]
                    ]],

                    // Example 1b: Sort by external_id desc
                    [4883099, 157, 1, 11, null, 'external_id', 'DESC', [
                        [
                            'group_name' => 'Cross Country',
                            'org_group_id' => '370658',
                            'external_id' => 'XC',
                            'parent_group' => 'Athletics',
                            'student_id_cnt' => '1',
                            'responded' => '1',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Outdoor Track and Field',
                            'org_group_id' => '370660',
                            'external_id' => 'OTF',
                            'parent_group' => 'Athletics',
                            'student_id_cnt' => '12',
                            'responded' => '12',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Indoor Track and Field',
                            'org_group_id' => '370659',
                            'external_id' => 'ITF',
                            'parent_group' => 'Athletics',
                            'student_id_cnt' => '9',
                            'responded' => '9',
                            'response_rate' => '100'
                        ]
                    ]],
                    // Example 1c: Sorting by response rate secondarily sorts by group name so that the sorting is deterministic.
                    [4883099, 157, 1, 11, null, 'response_rate', 'ASC', [
                        [
                            'group_name' => 'Cross Country',
                            'org_group_id' => '370658',
                            'external_id' => 'XC',
                            'parent_group' => 'Athletics',
                            'student_id_cnt' => '1',
                            'responded' => '1',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Indoor Track and Field',
                            'org_group_id' => '370659',
                            'external_id' => 'ITF',
                            'parent_group' => 'Athletics',
                            'student_id_cnt' => '9',
                            'responded' => '9',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Outdoor Track and Field',
                            'org_group_id' => '370660',
                            'external_id' => 'OTF',
                            'parent_group' => 'Athletics',
                            'student_id_cnt' => '12',
                            'responded' => '12',
                            'response_rate' => '100'
                        ]
                    ]],
                    // Example 1d: Filter text
                    [4883099, 157, 1, 11, ['group_name' => 'Cr'], null, null, [
                        [
                            'group_name' => 'Cross Country',
                            'org_group_id' => '370658',
                            'external_id' => 'XC',
                            'parent_group' => 'Athletics',
                            'student_id_cnt' => '1',
                            'responded' => '1',
                            'response_rate' => '100'
                        ]
                    ]],
                    // Example 2: The faculty member is in Stark Hall.
                    // The report should include both Stark Hall and all subgroups (which the students are actually in).
                    [4883175, 157, 1, 11, null, null, null, [
                        [
                            'group_name' => 'Stark 1',
                            'org_group_id' => '370649',
                            'external_id' => 'SH1',
                            'parent_group' => 'Stark Hall',
                            'student_id_cnt' => '10',
                            'responded' => '10',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Stark 2',
                            'org_group_id' => '370650',
                            'external_id' => 'SH2',
                            'parent_group' => 'Stark Hall',
                            'student_id_cnt' => '11',
                            'responded' => '11',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Stark Hall',
                            'org_group_id' => '370636',
                            'external_id' => 'SH',
                            'parent_group' => 'West Side',
                            'student_id_cnt' => '21',
                            'responded' => '21',
                            'response_rate' => '100'
                        ]
                    ]],

                    // Example 3a: This faculty member is two levels above the students,
                    // and should be able to see all subgroups below (and including) his group.
                    [4883174, 157, 1, 11, null, null, null, [
                        [
                            'group_name' => 'Stark 1',
                            'org_group_id' => '370649',
                            'external_id' => 'SH1',
                            'parent_group' => 'Stark Hall',
                            'student_id_cnt' => '10',
                            'responded' => '10',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Stark 2',
                            'org_group_id' => '370650',
                            'external_id' => 'SH2',
                            'parent_group' => 'Stark Hall',
                            'student_id_cnt' => '11',
                            'responded' => '11',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Stark Hall',
                            'org_group_id' => '370636',
                            'external_id' => 'SH',
                            'parent_group' => 'West Side',
                            'student_id_cnt' => '21',
                            'responded' => '21',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Tully 1',
                            'org_group_id' => '370651',
                            'external_id' => 'TH1',
                            'parent_group' => 'Tully Hall',
                            'student_id_cnt' => '10',
                            'responded' => '10',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Tully 2',
                            'org_group_id' => '370652',
                            'external_id' => 'TH2',
                            'parent_group' => 'Tully Hall',
                            'student_id_cnt' => '12',
                            'responded' => '12',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Tully 3',
                            'org_group_id' => '370653',
                            'external_id' => 'TH3',
                            'parent_group' => 'Tully Hall',
                            'student_id_cnt' => '12',
                            'responded' => '12',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Tully Hall',
                            'org_group_id' => '370637',
                            'external_id' => 'TH',
                            'parent_group' => 'West Side',
                            'student_id_cnt' => '34',
                            'responded' => '34',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'West Side',
                            'org_group_id' => '370630',
                            'external_id' => 'RLWS',
                            'parent_group' => 'Residence Life',
                            'student_id_cnt' => '55',
                            'responded' => '55',
                            'response_rate' => '100'
                        ]
                    ]],

                    // Example 3b: Sorting by parent group secondarily sorts by group name (and then org_group_id) so that it's deterministic.
                    [4883174, 157, 1, 11, null, 'parent_group', 'ASC', [
                        [
                            'group_name' => 'West Side',
                            'org_group_id' => '370630',
                            'external_id' => 'RLWS',
                            'parent_group' => 'Residence Life',
                            'student_id_cnt' => '55',
                            'responded' => '55',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Stark 1',
                            'org_group_id' => '370649',
                            'external_id' => 'SH1',
                            'parent_group' => 'Stark Hall',
                            'student_id_cnt' => '10',
                            'responded' => '10',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Stark 2',
                            'org_group_id' => '370650',
                            'external_id' => 'SH2',
                            'parent_group' => 'Stark Hall',
                            'student_id_cnt' => '11',
                            'responded' => '11',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Tully 1',
                            'org_group_id' => '370651',
                            'external_id' => 'TH1',
                            'parent_group' => 'Tully Hall',
                            'student_id_cnt' => '10',
                            'responded' => '10',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Tully 2',
                            'org_group_id' => '370652',
                            'external_id' => 'TH2',
                            'parent_group' => 'Tully Hall',
                            'student_id_cnt' => '12',
                            'responded' => '12',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Tully 3',
                            'org_group_id' => '370653',
                            'external_id' => 'TH3',
                            'parent_group' => 'Tully Hall',
                            'student_id_cnt' => '12',
                            'responded' => '12',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Stark Hall',
                            'org_group_id' => '370636',
                            'external_id' => 'SH',
                            'parent_group' => 'West Side',
                            'student_id_cnt' => '21',
                            'responded' => '21',
                            'response_rate' => '100'
                        ],
                        [
                            'group_name' => 'Tully Hall',
                            'org_group_id' => '370637',
                            'external_id' => 'TH',
                            'parent_group' => 'West Side',
                            'student_id_cnt' => '34',
                            'responded' => '34',
                            'response_rate' => '100'
                        ],
                    ]],
                    // Example 4a: Response rates of less than 100% (org 57)
                    [67638, 143, 1, 11, null, null, null, [
                        [
                            'group_name' => 'All Students',
                            'org_group_id' => '346048',
                            'external_id' => 'ALLSTUDENTS',
                            'parent_group' => null,
                            'student_id_cnt' => '205',
                            'responded' => '183',
                            'response_rate' => '89'
                        ],
                        [
                            'group_name' => 'Test Group 00015159',
                            'org_group_id' => '15159',
                            'external_id' => 'EXID00015159',
                            'parent_group' => null,
                            'student_id_cnt' => '58',
                            'responded' => '51',
                            'response_rate' => '88'
                        ],
                        [
                            'group_name' => 'Test Group 00365713',
                            'org_group_id' => '365713',
                            'external_id' => 'EXID00365713',
                            'parent_group' => 'Test Group 00365709',
                            'student_id_cnt' => '20',
                            'responded' => '18',
                            'response_rate' => '90'
                        ]
                    ]],

                    // Example 4c: Subgroup filter
                    [67638, 143, 1, 11, ['group_selection' => 'sub_groups'], null, null, [
                        [
                            'group_name' => 'Test Group 00365713',
                            'org_group_id' => '365713',
                            'external_id' => 'EXID00365713',
                            'parent_group' => 'Test Group 00365709',
                            'student_id_cnt' => '20',
                            'responded' => '18',
                            'response_rate' => '90'
                        ]
                    ]],

                    // Example 4e: Sorting by total students
                    [67638, 143, 1, 11, null, 'student_id_cnt', 'ASC', [
                        [
                            'group_name' => 'Test Group 00365713',
                            'org_group_id' => '365713',
                            'external_id' => 'EXID00365713',
                            'parent_group' => 'Test Group 00365709',
                            'student_id_cnt' => '20',
                            'responded' => '18',
                            'response_rate' => '90'
                        ],
                        [
                            'group_name' => 'Test Group 00015159',
                            'org_group_id' => '15159',
                            'external_id' => 'EXID00015159',
                            'parent_group' => null,
                            'student_id_cnt' => '58',
                            'responded' => '51',
                            'response_rate' => '88'
                        ],
                        [
                            'group_name' => 'All Students',
                            'org_group_id' => '346048',
                            'external_id' => 'ALLSTUDENTS',
                            'parent_group' => null,
                            'student_id_cnt' => '205',
                            'responded' => '183',
                            'response_rate' => '89'
                        ]
                    ]],
                    // Example 4f: Sorting by count of students that responded
                    [67638, 143, 1, 11, null, 'responded', 'ASC', [
                        [
                            'group_name' => 'Test Group 00365713',
                            'org_group_id' => '365713',
                            'external_id' => 'EXID00365713',
                            'parent_group' => 'Test Group 00365709',
                            'student_id_cnt' => '20',
                            'responded' => '18',
                            'response_rate' => '90'
                        ],
                        [
                            'group_name' => 'Test Group 00015159',
                            'org_group_id' => '15159',
                            'external_id' => 'EXID00015159',
                            'parent_group' => null,
                            'student_id_cnt' => '58',
                            'responded' => '51',
                            'response_rate' => '88'
                        ],
                        [
                            'group_name' => 'All Students',
                            'org_group_id' => '346048',
                            'external_id' => 'ALLSTUDENTS',
                            'parent_group' => null,
                            'student_id_cnt' => '205',
                            'responded' => '183',
                            'response_rate' => '89'
                        ]
                    ]],
                    // Example 4g: Sorting by response rate
                    [67638, 143, 1, 11, null, 'response_rate', 'ASC', [
                        [
                            'group_name' => 'Test Group 00015159',
                            'org_group_id' => '15159',
                            'external_id' => 'EXID00015159',
                            'parent_group' => null,
                            'student_id_cnt' => '58',
                            'responded' => '51',
                            'response_rate' => '88'
                        ],
                        [
                            'group_name' => 'All Students',
                            'org_group_id' => '346048',
                            'external_id' => 'ALLSTUDENTS',
                            'parent_group' => null,
                            'student_id_cnt' => '205',
                            'responded' => '183',
                            'response_rate' => '89'
                        ],
                        [
                            'group_name' => 'Test Group 00365713',
                            'org_group_id' => '365713',
                            'external_id' => 'EXID00365713',
                            'parent_group' => 'Test Group 00365709',
                            'student_id_cnt' => '20',
                            'responded' => '18',
                            'response_rate' => '90'
                        ]
                    ]],
                    // Example 5a: Faculty is directly in both a group (271653) and its subgroup (366324).
                    // ESPRJ-10397 uncovered the issue that students were being double counted in the subgroup
                    // (and not counted at all in the parent group).
                    [111696, 103, 1, 11, null, null, null, [
                        [
                            'group_name' => 'Test Group 00271653',
                            'org_group_id' => '271653',
                            'external_id' => 'EXID00271653',
                            'parent_group' => 'Test Group 00013853',
                            'student_id_cnt' => '393',
                            'responded' => '318',
                            'response_rate' => '81'
                        ],
                        [
                            'group_name' => 'Test Group 00366320',
                            'org_group_id' => '366320',
                            'external_id' => 'EXID00366320',
                            'parent_group' => 'Test Group 00271653',
                            'student_id_cnt' => '42',
                            'responded' => '32',
                            'response_rate' => '76'
                        ],
                        [
                            'group_name' => 'Test Group 00366321',
                            'org_group_id' => '366321',
                            'external_id' => 'EXID00366321',
                            'parent_group' => 'Test Group 00271653',
                            'student_id_cnt' => '125',
                            'responded' => '103',
                            'response_rate' => '82'
                        ],
                        [
                            'group_name' => 'Test Group 00366323',
                            'org_group_id' => '366323',
                            'external_id' => 'EXID00366323',
                            'parent_group' => 'Test Group 00271653',
                            'student_id_cnt' => '10',
                            'responded' => '7',
                            'response_rate' => '70'
                        ],
                        [
                            'group_name' => 'Test Group 00366324',
                            'org_group_id' => '366324',
                            'external_id' => 'EXID00366324',
                            'parent_group' => 'Test Group 00271653',
                            'student_id_cnt' => '146',  // previous query produced 292
                            'responded' => '121',       // previous query produced 242
                            'response_rate' => '83'
                        ],
                        [
                            'group_name' => 'Test Group 00366325',
                            'org_group_id' => '366325',
                            'external_id' => 'EXID00366325',
                            'parent_group' => 'Test Group 00271653',
                            'student_id_cnt' => '70',
                            'responded' => '55',
                            'response_rate' => '79'
                        ]
                    ]],
                    // Example 5b: Filter by parent group.
                    [111696, 103, 1, 11, ['parent_group' => '13'], null, null, [
                        [
                            'group_name' => 'Test Group 00271653',
                            'org_group_id' => '271653',
                            'external_id' => 'EXID00271653',
                            'parent_group' => 'Test Group 00013853',
                            'student_id_cnt' => '393',
                            'responded' => '318',
                            'response_rate' => '81'
                        ]
                    ]
                    ]
                ]
            ]);
    }

}