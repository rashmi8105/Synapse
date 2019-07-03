<?php

use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrgMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Service\Utility\SearchUtilityService;
use Synapse\RiskBundle\Entity\RiskLevels;
use Synapse\RiskBundle\Repository\RiskLevelsRepository;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\SearchBundle\Repository\OrgSearchRepository;
use Synapse\SearchBundle\Service\Impl\SearchService;
use Synapse\StaticListBundle\Repository\OrgStaticListStudentsRepository;
use Synapse\SurveyBundle\Repository\FactorRepository;


class SearchServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    private $personId = 1;

    private $organization = 1;

    private $isCsv = "csv";

    public function testCreateCustomSearchToListStudents()
    {

        $this->specify("List the total students based on custom search filter", function ($datablockArr = null, $ispArr = null, $orgId, $loggedInUserId, $orgProfileType, $ebiProfileType) {
            $this->markTestSkipped("Skipping until the developer can update the query strings that are cut and pasted into this test.");

            $mockOrganizationEntity = $this->getMock('Organization', [
                'getId',
                'getTimezone'
            ]);
            $mockOrganizationEntity->method('getId')
                ->willReturn(1);
            $mockOrganizationEntity->method('getTimezone')
                ->willReturn('Asia/Kolkotta');

            $mockPersonEntity = $this->getMock('Person', [
                'getId',
                'getOrganization'
            ]);
            $mockPersonEntity->method('getId')
                ->willReturn(1);

            $mockPersonEntity->method('getOrganization')
                ->willReturn($mockOrganizationEntity);

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockOrgSearchRepository = $this->getMock('OrgSearchRepository', array(
                'getOrgSearch',
                'getRiskIntentData'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockResque = $this->getMockBuilder('BCC\ResqueBundle\Resque')
                ->disableOriginalConstructor()
                ->setMethods([
                    'enqueue'
                ])
                ->getMock();

            $mockOrgService = $this->getMock('OrganizationService', array(
                'find'
            ));
            $mockOrgService->method('find')
                ->willReturn($mockOrganizationEntity);

            $mockPersonService = $this->getMock('PersonService', array(
                'findPerson'
            ));
            $mockPersonService->method('findPerson')
                ->willReturn($mockPersonEntity);

            $loggerService = $this->getMock('Logger', [
                'debug',
                'error',
                'info',
                'getLog'
            ]);

            $loggerService->method('getLog')
                ->willReturn(1);

            $mockLogger->method('info')
                ->willReturn(1);

            $utilServiceMock = $this->getMock('UtilServiceHelper', [
                'getDateByTimezone'
            ]);
            $utilServiceMock->method('getDateByTimezone')
                ->willReturn(date('Y-m-d'));

            $mockStaticListStudents = $this->getMock('OrgStaticListStudents', []);

            $mockMetaDataListValues = $this->getMock('MetadataListValues', [
                'findByListName'
            ]);
            $mockMetaDataListValues->method('findByListName')
                ->willReturn(null);

            $mockOrgMetadata = $this->getMock('OrgMetadata', [
                'getScope'
            ]);
            $mockEbiMetadata = $this->getMock('OrgMetadata', [
                'getScope'
            ]);

            $mockOrgMetadata->method('getScope')
                ->willReturn($orgProfileType);

            $mockEbiMetadata->method('getScope')
                ->willReturn($ebiProfileType);

            $mockOrgMetadataRepo = $this->getMock('OrgMetadataRepository', [
                'findOneBy'
            ]);
            $mockEbiMetadataRepo = $this->getMock('EbiMetadataRepository', [
                'findOneBy'
            ]);

            $mockOrgMetadataRepo->method('findOneBy')
                ->willReturn($mockOrgMetadata);

            $mockEbiMetadataRepo->method('findOneBy')
                ->willReturn($mockEbiMetadata);

            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['getCurrentAcademicYear']);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                ['SynapseStaticListBundle:OrgStaticListStudents', $mockStaticListStudents],
                ['SynapseSearchBundle:OrgSearch', $mockOrgSearchRepository],
                ['SynapseCoreBundle:MetadataListValues', $mockMetaDataListValues],
                ['SynapseCoreBundle:OrgMetadata', $mockOrgMetadataRepo],
                ['SynapseCoreBundle:EbiMetadata', $mockEbiMetadataRepo],
                ['SynapseAcademicBundle:OrgAcademicYear', $mockOrgAcademicYearRepository]
            ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        'loggerhelper_service',
                        $loggerService
                    ],
                    [
                        'org_service',
                        $mockOrgService
                    ],
                    [
                        'person_service',
                        $mockPersonService
                    ]
                ]);

            $searchService = new SearchService($mockRepositoryResolver, $mockLogger, $mockContainer, $mockResque, $utilServiceMock);
            $queryArr = $searchService->getStudentListBasedCriteria($this->createSearchAttributes($datablockArr, $ispArr), 1, 1);

            if (isset($datablockArr['sql'])) {
                $assertSQl = $datablockArr['sql'];
            } else if ($ispArr['sql']) {
                $assertSQl = $ispArr['sql'];
            }

            /*
             * Exploding to find out the profile query
             */
            $strArr = explode("EXISTS", $queryArr);
            $profileSql = $strArr[count($strArr) - 1];  // last elemet of the array would be the query to be checked
            $pos = strpos($profileSql, "select"); // Finding out from where the query starts
            $sql = substr($profileSql, $pos); // Removing the comments  added in the queery before asserting.
            $sql = str_replace(")", "", $sql); // Removing the end bracket if it would exist.

            // Removing all the spaces from the sql string to match.
            $assertSQl = preg_replace("/\s+/", " ", $assertSQl);
            $sql = preg_replace("/\s+/", " ", $sql);

            $this->assertEquals(trim($assertSQl), trim($sql)); // asserting if the query returned from the method is matching the query send from the createDataBlockArrForQueryCheck and createIspArrForTermForQueryCheck method below.

        }, [
            'examples' => [
                [
                    $this->createDataBlockArrForQueryCheck(),
                    [],
                    $this->organization,
                    $this->personId,
                    "T",
                    "Y"
                ],
                [
                    [],
                    $this->createIspArrForTermForQueryCheck(),
                    $this->organization,
                    $this->personId,
                    "T",
                    "Y"
                ]
            ]

        ]);
    }

    private function createSaveSearchDto($dataBlockArr = null, $ispArr = null)
    {
        $searchAttribute = [];
        $searchAttribute["risk_indicator_ids"] = "1,2,3";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "1";
        $searchAttribute["referral_status"] = "'o','c'";
        $searchAttribute["contact_types"] = "1,2";
        $searchAttribute['academic_updates'] = ['isBlankAcadUpdate' => true];

        if (!is_null($ispArr)) {
            $searchAttribute["isps"] = $ispArr;
        }
        if (!is_null($dataBlockArr)) {
            $searchAttribute["datablocks"][] = $dataBlockArr;
        }

        $saveSearchDto = new SaveSearchDto();
        $saveSearchDto->setOrganizationId($this->organization);
        $saveSearchDto->setSearchAttributes($searchAttribute);
        return $saveSearchDto;
    }


    private function createDataBlockArrForQueryCheck()
    {
        $datablockArray = [
            "profile_block_id" => 12,
            "name" => "Profile Block",
            "profile_items" => [
                [
                    "id" => 1,
                    "item_data_type" => "S",
                    "calendar_assignment" => "Y",
                    "category_type" => [
                        [
                            "answer" => "some ans",
                            "value" => "0"
                        ]
                    ],
                    "name" => "PROFILE NAME",
                    "years" => [
                        [
                            "year_id" => 1,
                            "year_name" => "some Year"
                        ]
                    ]
                ]
            ]
        ];

        // the query which needs to be checked with the retuen value.The values in the query should be same as above array.

        $queryText = 'SELECT DISTINCT
                        pem.person_id
                    FROM
                        person_ebi_metadata pem
                            INNER JOIN
                        org_person_student ops ON ops.person_id = pem.person_id
                            INNER JOIN
                        org_faculty_student_permission_map ofspm ON ofspm.student_id = ops.person_id
                            AND ofspm.org_id = ops.organization_id
                            INNER JOIN
                        datablock_metadata dm ON dm.ebi_metadata_id = pem.ebi_metadata_id
                            INNER JOIN
                        org_permissionset_datablock opd ON opd.org_permissionset_id = ofspm.permissionset_id
                            AND opd.organization_id = ofspm.org_id
                            AND dm.datablock_id = opd.datablock_id
                            INNER JOIN
                        ebi_metadata em ON em.id = pem.ebi_metadata_id
                    WHERE
                        pem.deleted_at IS NULL
                            AND ops.deleted_at IS NULL
                            AND dm.deleted_at IS NULL
                            AND opd.deleted_at IS NULL
                            AND em.deleted_at IS NULL
                            AND ops.organization_id = 1
                            AND ofspm.faculty_id = 1
                            AND pem.ebi_metadata_id = "0"
                            AND pem.org_academic_year_id = 1
                        AND pem.person_id = p.id
                    ';

        return [
            'datablock' => $datablockArray,
            'sql' => $queryText
        ];

    }


    private function createIspArrForTermForQueryCheck()
    {

        $ispArray = [
            [
                "id" => 2,
                "item_data_type" => "N",
                "calendar_assignment" => "T",
                "is_single" => true,
                "single_value" => "5",
                "name" => "ISP",
                "terms" => [
                    [
                        "term_id" => 1,
                        "term_name" => "Term1"
                    ]
                ]
            ]
        ];

        // the query which needs to be checked with the retuen value.The values in the query should be same as above array.

        $query = 'SELECT distinct
                        pom.person_id
                    FROM
                        person_org_metadata pom
                            INNER JOIN
                        org_person_student ops ON ops.person_id = pom.person_id
                            INNER JOIN
                        org_faculty_student_permission_map ofspm ON ofspm.student_id = ops.person_id
                            AND ofspm.org_id = ops.organization_id
                            INNER JOIN
                        org_permissionset_metadata opm ON ofspm.permissionset_id = opm.org_permissionset_id
                            AND opm.organization_id = ofspm.org_id
                            INNER JOIN
                        org_metadata om ON om.id = pom.org_metadata_id
                    WHERE
                        pom.deleted_at IS NULL
                            AND ops.deleted_at IS NULL
                            AND opm.deleted_at IS NULL
                            AND om.deleted_at IS NULL
                            AND ops.organization_id = 1
                            AND ofspm.faculty_id = 1
                            AND pom.org_metadata_id = 2
                            AND pom.metadata_value = 5
                            AND pom.org_academic_periods_id = 1
                        AND pom.person_id = p.id
                    ';

        return [
            'ispArr' => $ispArray,
            'sql' => $query
        ];
    }


    private function createSearchAttributes($dataBlockArr, $ispArr)
    {
        $searchAttribute = [];

        $searchAttribute["risk_indicator_ids"] = "";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "";
        $searchAttribute["referral_status"] = "";
        $searchAttribute["contact_types"] = "";
        $searchAttribute["academic_updates"] = "";

        if (!is_null($ispArr) && count($ispArr) > 0) {
            $searchAttribute["isps"] = $ispArr['ispArr'];
        }
        if (!is_null($dataBlockArr) && count($dataBlockArr) > 0) {
            $searchAttribute["datablocks"][] = $dataBlockArr['datablock'];
        }
        return $searchAttribute;
    }

    private function createIspArrForTerm()
    {
        $ispArr = [
            [
                "id" => 2,
                "item_data_type" => "N",
                "calendar_assignment" => "T",
                "is_single" => true,
                "single_value" => "5",
                "name" => "ISP",
                "terms" => [
                    [
                        "term_id" => 1,
                        "term_name" => "Term1"
                    ]
                ]
            ]
        ];
        return $ispArr;
    }

    private function createIspArrForYear()
    {
        $ispArr = [
            [
                "id" => 2,
                "item_data_type" => "N",
                "calendar_assignment" => "Y",
                "is_single" => true,
                "single_value" => "5",
                "name" => "ISP",
                "years" => [
                    [
                        "year_id" => 1,
                        "year_name" => "year1"
                    ]
                ]
            ]
        ];
        return $ispArr;
    }

    private function createIspArrForYearInvalid()
    {
        $ispArr = [
            [
                "id" => 2,
                "item_data_type" => "N",
                "calendar_assignment" => "Y",
                "is_single" => true,
                "single_value" => "5",
                "name" => "ISP"
            ]
        ];
        return $ispArr;
    }

    private function createNegativeDatablockForArrForYearProfile()
    {

        /*
         * Year specific profile item.. but no years been passed
         */
        $negativeArr = [
            "profile_block_id" => 12,
            "name" => "Profile Block",
            "profile_items" => [
                [
                    "id" => 1,
                    "item_data_type" => "S",
                    "calendar_assignment" => "Y",
                    "category_type" => [
                        [
                            "answer" => "some ans",
                            "value" => "0"
                        ]
                    ],
                    "name" => "PROFILE NAME"
                ]
            ]
        ];
        return $negativeArr;
    }

    private function createDataBlockArrWithYear()
    {
        $datablockArr = [
            "profile_block_id" => 12,
            "name" => "Profile Block",
            "profile_items" => [
                [
                    "id" => 1,
                    "item_data_type" => "S",
                    "calendar_assignment" => "Y",
                    "category_type" => [
                        [
                            "answer" => "some ans",
                            "value" => "0"
                        ]
                    ],
                    "name" => "PROFILE NAME",
                    "years" => [
                        [
                            "year_id" => 1,
                            "year_name" => "some Year"
                        ]
                    ]
                ],
                [
                    "id" => 12,
                    "item_data_type" => "S",
                    "calendar_assignment" => "Y",
                    "category_type" => [
                        [
                            "answer" => "some ans",
                            "value" => "0"
                        ]
                    ],
                    "name" => "PROFILE NAME",
                    "years" => [
                        [
                            "year_id" => 1,
                            "year_name" => "some Year"
                        ]
                    ]
                ]
            ]
        ];

        return $datablockArr;
    }

    private function createDataBlockArrWithTerm()
    {
        $datablockArr = [
            "profile_block_id" => 12,
            "name" => "Profile Block",
            "profile_items" => [
                [
                    "id" => 1,
                    "item_data_type" => "S",
                    "calendar_assignment" => "T",
                    "category_type" => [
                        [
                            "answer" => "some ans",
                            "value" => "0"
                        ]
                    ],
                    "name" => "PROFILE NAME",
                    "terms" => [
                        [
                            "term_id" => 1,
                            "term_name" => "some Year"
                        ]
                    ]
                ],
                [
                    "id" => 12,
                    "item_data_type" => "S",
                    "calendar_assignment" => "T",
                    "category_type" => [
                        [
                            "answer" => "some ans",
                            "value" => "0"
                        ]
                    ],
                    "name" => "PROFILE NAME",
                    "years" => [
                        [
                            "term_id" => 1,
                            "term_name" => "some Year"
                        ]
                    ]
                ]
            ]
        ];
        return $datablockArr;
    }

    public function testGetRiskDates()
    {

        $this->specify(
            'Test get risk dates',
            function ($searchAttributes, $mockFormattedStart, $mockFormattedEnd, $expectedResult) {
                //Create all mocks necessary for Service class creation
                $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
                $mockCache = $this->getMock('cache', array('run'));
                $mockResque = $this->getMock('resque', array('enqueue'));
                $mockLogger = $this->getMock('Logger', array('debug', 'error'));
                $mockContainer = $this->getMock('Container', array('get'));

                //Create necessary mocks for method
                $mockAcademicYearRepo = $this->getMock('academicYearRepo', array('find'));
                $mockAcademicYearObj = $this->getMock('academicYearObj', array('getStartDate', 'getEndDate'));
                $mockDateUtilityService = $this->getMock('dateUtilityService', array('convertToUtcDatetime'));
                $mockStartObject = $this->getMock('date', array('format'));
                $mockEndObject = $this->getMock('date', array('format'));
                $orgId = 1;

                //Mocking behavior that exists outside the tested function
                $mockRepositoryResolver->method('getRepository')->willReturnMap([
                    ['SynapseAcademicBundle:OrgAcademicYear', $mockAcademicYearRepo]
                ]);

                $mockContainer->method('get')->willReturnMap([
                    ['date_utility_service', $mockDateUtilityService]
                ]);

                $mockAcademicYearRepo->expects($this->at(0))->method('find')->willReturn($mockAcademicYearObj);
                $mockAcademicYearObj->expects($this->any())->method('getStartDate')->willReturn($mockStartObject);
                $mockAcademicYearObj->expects($this->any())->method('getEndDate')->willReturn($mockEndObject);
                $mockStartObject->expects($this->any())->method('format')->willReturn($mockFormattedStart);
                $mockEndObject->expects($this->any())->method('format')->willReturn($mockFormattedEnd);
                $mockDateUtilityService->expects($this->at(0))->method('convertToUtcDatetime')->willReturn($mockFormattedStart);
                $mockDateUtilityService->expects($this->at(1))->method('convertToUtcDatetime')->willReturn($mockFormattedEnd);


                //Creating class
                $searchService = new SearchService(
                    $mockRepositoryResolver,
                    $mockLogger,
                    $mockContainer
                );

                //Calling function
                $functionResults = $searchService->getRiskDates($orgId, $searchAttributes);

                $this->assertEquals($expectedResult, $functionResults);
            },
            [
                'examples' =>
                    [
                        //Scenario 1 - All necessary data is present
                        [
                            "search_attributes" =>
                                [
                                    "retention_date" =>
                                        [
                                            "academic_year_id" => 190,
                                            "start_date" => "2012-08-01",
                                            "end_date" => "2012-09-10",
                                            "academic_year_name" => "YearName",
                                        ],
                                    "risk_date" =>
                                        [
                                            "start_date" => "2012-08-09",
                                            "end_date" => "2012-09-10",
                                        ],
                                ],
                            "2012-08-09",
                            "2012-09-10",
                            array('start_date' => "2012-08-09", 'end_date' => "2012-09-10"),
                        ]
                        ,
                        //Scenario 2 - risk date data is nulled out
                        [
                            "search_attributes" =>
                                [
                                    "retention_date" =>
                                        [
                                            "academic_year_id" => 190,
                                            "start_date" => "2012-08-01",
                                            "end_date" => "2012-09-10",
                                            "academic_year_name" => "YearName",
                                        ],
                                    "risk_date" => null,
                                ],
                            "2012-08-09",
                            "2012-09-10",
                            array('start_date' => "2012-08-09", 'end_date' => "2012-09-10"),
                        ]
                        ,
                        //Scenario 3 - risk date data exists, but is null
                        [
                            "search_attributes" =>
                                [
                                    "retention_date" =>
                                        [
                                            "academic_year_id" => 190,
                                            "start_date" => "2012-08-01",
                                            "end_date" => "2012-09-10",
                                            "academic_year_name" => "YearName",
                                        ],
                                    "risk_date" =>
                                        [
                                            "start_date" => null,
                                            "end_date" => null,
                                        ],
                                ],
                            "2012-08-09",
                            "2012-09-10",
                            array('start_date' => "2012-08-09", 'end_date' => "2012-09-10"),
                        ]
                        ,
                        //Scenario 4 - risk date data is there for the start date, but not the end date.
                        [
                            "search_attributes" =>
                                [
                                    "retention_date" =>
                                        [
                                            "academic_year_id" => 190,
                                            "start_date" => "2012-08-01",
                                            "end_date" => "2012-09-10",
                                            "academic_year_name" => "YearName",
                                        ],
                                    "risk_date" =>
                                        [
                                            "start_date" => "2012-08-09",
                                            "end_date" => null,
                                        ],
                                ],
                            "2012-08-09",
                            "2012-09-10",
                            array('start_date' => "2012-08-09", 'end_date' => "2012-09-10"),
                        ]
                        ,
                        //Scenario 5 - risk date data is there for end date, but not the start date
                        [
                            "search_attributes" =>
                                [
                                    "retention_date" =>
                                        [
                                            "academic_year_id" => 190,
                                            "start_date" => "2012-08-01",
                                            "end_date" => "2012-09-10",
                                            "academic_year_name" => "YearName",
                                        ],
                                    "risk_date" =>
                                        [
                                            "start_date" => null,
                                            "end_date" => "2012-09-10",
                                        ],
                                ],
                            "2012-08-09",
                            "2012-09-10",
                            array('start_date' => "2012-08-09", 'end_date' => "2012-09-10"),
                        ],
                    ],
            ]
        );
    }


    public function testGetStudentListBasedCriteriaForRetentionAndCompletion()
    {

        $this->specify("List the total students based on custom search filter", function ($searchAttributes, $orgId, $loggedInUserId, $expectedSQl) {

            $mockOrganizationEntity = $this->getMock('Organization', [
                'getId',
                'getTimezone'
            ]);
            $mockOrganizationEntity->method('getId')
                ->willReturn(1);
            $mockOrganizationEntity->method('getTimezone')
                ->willReturn('Asia/Kolkotta');

            $mockPersonEntity = $this->getMock('Person', [
                'getId',
                'getOrganization'
            ]);
            $mockPersonEntity->method('getId')
                ->willReturn(1);

            $mockPersonEntity->method('getOrganization')
                ->willReturn($mockOrganizationEntity);

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockOrgSearchRepository = $this->getMock('OrgSearchRepository', array(
                'getOrgSearch',
                'getRiskIntentData'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockResque = $this->getMockBuilder('BCC\ResqueBundle\Resque')
                ->disableOriginalConstructor()
                ->setMethods([
                    'enqueue'
                ])
                ->getMock();

            $mockOrgService = $this->getMock('OrganizationService', array(
                'find'
            ));
            $mockOrgService->method('find')
                ->willReturn($mockOrganizationEntity);

            $mockPersonService = $this->getMock('PersonService', array(
                'findPerson'
            ));
            $mockPersonService->method('findPerson')
                ->willReturn($mockPersonEntity);

            $loggerService = $this->getMock('Logger', [
                'debug',
                'error',
                'info',
                'getLog'
            ]);

            $loggerService->method('getLog')
                ->willReturn(1);

            $mockLogger->method('info')
                ->willReturn(1);

            $utilServiceMock = $this->getMock('UtilServiceHelper', [
                'getDateByTimezone'
            ]);
            $utilServiceMock->method('getDateByTimezone')
                ->willReturn(date('Y-m-d'));

            $mockStaticListStudents = $this->getMock('OrgStaticListStudents', []);

            $mockMetaDataListValues = $this->getMock('MetadataListValues', [
                'findByListName'
            ]);
            $mockMetaDataListValues->method('findByListName')
                ->willReturn(null);

            $mockOrgMetadata = $this->getMock('OrgMetadata', [
                'getScope'
            ]);
            $mockEbiMetadata = $this->getMock('OrgMetadata', [
                'getScope'
            ]);


            $mockOrgMetadataRepo = $this->getMock('OrgMetadataRepository', [
                'findOneBy'
            ]);
            $mockEbiMetadataRepo = $this->getMock('EbiMetadataRepository', [
                'findOneBy'
            ]);

            $mockOrgMetadataRepo->method('findOneBy')
                ->willReturn($mockOrgMetadata);

            $mockEbiMetadataRepo->method('findOneBy')
                ->willReturn($mockEbiMetadata);

            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['getCurrentAcademicYear', 'getAllAcademicYearsForOrganization']);


            $mockretentionCompletionVariableNameRepository = $this->getMock('RetentionCompletionVariableNameRepository', ['getAllVariableNames']);
            $mockOrgPersonFacultyRepository = $this->getMock("OrgPersonFacultyRepository", ['findOneBy']);
            $mockFactorRepository = $this->getMock("FactorRepository", ['findOneBy']);

            $mockretentionCompletionVariableNameRepository->method('getAllVariableNames')->willReturn(

                [
                    "Completed Degree in 1 Year",
                    "Completed Degree in 2 Years",
                    "Completed Degree in 3 Years",
                    "Completed Degree in 4 Years",
                    "Completed Degree in 5 Years",
                    "Completed Degree in 6 Years",
                    "Retained to Midyear Year 1",
                    "Retained to Midyear Year 2",
                    "Retained to Midyear Year 3",
                    "Retained to Midyear Year 4",
                    "Retained to Start of Year 2",
                    "Retained to Start of Year 3",
                    "Retained to Start of Year 4"
                ]


            );

            $mockOrgAcademicYearRepository->method('getAllAcademicYearsForOrganization')->willReturn(
                [
                    201415,
                    201516,
                    201617]
            );


            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                ['SynapseStaticListBundle:OrgStaticListStudents', $mockStaticListStudents],
                ['SynapseSearchBundle:OrgSearch', $mockOrgSearchRepository],
                ['SynapseCoreBundle:MetadataListValues', $mockMetaDataListValues],
                ['SynapseCoreBundle:OrgMetadata', $mockOrgMetadataRepo],
                ['SynapseCoreBundle:EbiMetadata', $mockEbiMetadataRepo],
                ['SynapseAcademicBundle:OrgAcademicYear', $mockOrgAcademicYearRepository],
                ['SynapseReportsBundle:RetentionCompletionVariableName', $mockretentionCompletionVariableNameRepository],
                [
                    FactorRepository::REPOSITORY_KEY,
                    $mockFactorRepository
                ],
                [
                    OrgPersonFacultyRepository::REPOSITORY_KEY,
                    $mockOrgPersonFacultyRepository
                ]
            ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        'loggerhelper_service',
                        $loggerService
                    ],
                    [
                        'org_service',
                        $mockOrgService
                    ],
                    [
                        'person_service',
                        $mockPersonService
                    ]
                ]);

            $mockFactorRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue(true));
            $mockOrgPersonFacultyRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue(true));
            $searchService = new SearchService($mockRepositoryResolver, $mockLogger, $mockContainer, $mockResque, $utilServiceMock);
            $sql = $searchService->getStudentListBasedCriteria($searchAttributes, $orgId, $loggedInUserId);
            $sql = preg_replace("/\s+/", " ", trim($sql));
            $expectedSQl = preg_replace("/\s+/", " ", trim($expectedSQl));
            $this->assertEquals($sql, $expectedSQl);
        }, [
            'examples' => [
                [
                    [
                        "academic_updates" => [],
                        "retention_completion" =>
                            [
                                "retention_tracking_year" => "201415",
                                "variables" =>
                                    [
                                        [
                                            "Retained to Midyear Year 1" => ["1", "0"]
                                        ],
                                        [
                                            "Retained to Start of Year 2" => ["0"]
                                        ],
                                        [
                                            "Retained to Midyear Year 2" => ["0"]
                                        ]
                                    ]
                            ]
                    ], 1, 1,
                    "EXISTS (

            SELECT DISTINCT
                merged.student_id
            FROM
                (
                    SELECT
                        ofspm.student_id,
                        ofspm.permissionset_id
                    FROM
                        org_faculty_student_permission_map ofspm
                    WHERE
                        ofspm.org_id = 1
                        AND ofspm.faculty_id = 1
                ) AS merged
                    INNER JOIN
                org_permissionset OPS
                        ON OPS.id = merged.permissionset_id
                        AND OPS.deleted_at IS NULL
                        AND OPS.accesslevel_ind_agg = 1
                        AND OPS.retention_completion = 1

                WHERE student_id = p.id
                )
                 AND  EXISTS (
                 SELECT person_id FROM org_person_student WHERE deleted_at is NULL AND person_id in (SELECT
                    person_id
                  FROM
                    (SELECT
                        person_id,
                        retention_tracking_year AS `Retention Tracking Year`,
                        retained_to_midyear_year_1 AS `Retained to Midyear Year 1`,
                        retained_to_start_of_year_2 AS `Retained to Start of Year 2`,
                        retained_to_midyear_year_2 AS `Retained to Midyear Year 2`,
                        retained_to_start_of_year_3 AS `Retained to Start of Year 3`,
                        retained_to_midyear_year_3 AS `Retained to Midyear Year 3`,
                        retained_to_start_of_year_4 AS `Retained to Start of Year 4`,
                        retained_to_midyear_year_4 AS `Retained To Midyear Year 4`,
                        completed_degree_in_1_year_or_less AS `Completed Degree in 1 Year or Less`,
                        CASE
                            WHEN completed_degree_in_2_years_or_less IS NULL THEN NULL
                            WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                            ELSE completed_degree_in_2_years_or_less
                        END AS `Completed Degree in 2 Years or Less`,
                        CASE
                            WHEN completed_degree_in_3_years_or_less IS NULL THEN NULL
                            WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                            WHEN completed_degree_in_2_years_or_less = 1 THEN 1
                            ELSE completed_degree_in_3_years_or_less
                        END AS `Completed Degree in 3 Years or Less`,
                        CASE
                            WHEN completed_degree_in_4_years_or_less IS NULL THEN NULL
                            WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                            WHEN completed_degree_in_2_years_or_less = 1 THEN 1
                            WHEN completed_degree_in_3_years_or_less = 1 THEN 1
                            ELSE completed_degree_in_4_years_or_less
                        END AS `Completed Degree in 4 Years or Less`,
                        CASE
                            WHEN completed_degree_in_5_years_or_less IS NULL THEN NULL
                            WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                            WHEN completed_degree_in_2_years_or_less = 1 THEN 1
                            WHEN completed_degree_in_3_years_or_less = 1 THEN 1
                            WHEN completed_degree_in_4_years_or_less = 1 THEN 1
                            ELSE completed_degree_in_5_years_or_less
                        END AS `Completed Degree in 5 Years or Less`,
                        CASE
                            WHEN completed_degree_in_6_years_or_less IS NULL THEN NULL
                            WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                            WHEN completed_degree_in_2_years_or_less = 1 THEN 1
                            WHEN completed_degree_in_3_years_or_less = 1 THEN 1
                            WHEN completed_degree_in_4_years_or_less = 1 THEN 1
                            WHEN completed_degree_in_5_years_or_less = 1 THEN 1
                            ELSE completed_degree_in_6_years_or_less
                        END AS `Completed Degree in 6 Years or Less`
                    FROM
                        (SELECT
                            p.id AS person_id,
                            opsrcpv.retention_tracking_year,
                            oay.name AS retention_tracking_year_name,
                            MAX(opsrcpv.retained_to_midyear_year_1) AS retained_to_midyear_year_1,
                            MAX(opsrcpv.retained_to_start_of_year_2) AS retained_to_start_of_year_2,
                            MAX(opsrcpv.retained_to_midyear_year_2) AS retained_to_midyear_year_2,
                            MAX(opsrcpv.retained_to_start_of_year_3) AS retained_to_start_of_year_3,
                            MAX(opsrcpv.retained_to_midyear_year_3) AS retained_to_midyear_year_3,
                            MAX(opsrcpv.retained_to_start_of_year_4) AS retained_to_start_of_year_4,
                            MAX(opsrcpv.retained_to_midyear_year_4) AS retained_to_midyear_year_4,
                            MAX(opsrcpv.completed_degree_in_1_year_or_less) AS completed_degree_in_1_year_or_less,
                            MAX(opsrcpv.completed_degree_in_2_years_or_less) AS completed_degree_in_2_years_or_less,
                            MAX(opsrcpv.completed_degree_in_3_years_or_less) AS completed_degree_in_3_years_or_less,
                            MAX(opsrcpv.completed_degree_in_4_years_or_less) AS completed_degree_in_4_years_or_less,
                            MAX(opsrcpv.completed_degree_in_5_years_or_less) AS completed_degree_in_5_years_or_less,
                            MAX(opsrcpv.completed_degree_in_6_years_or_less) AS completed_degree_in_6_years_or_less
                        FROM
                            person p
                                INNER JOIN
                            org_person_student_retention_completion_pivot_view opsrcpv ON p.id = opsrcpv.person_id
                                INNER JOIN
                            org_academic_year oay ON oay.year_id = opsrcpv.retention_tracking_year
                                AND oay.organization_id = p.organization_id
                        WHERE
                            p.organization_id = 1
                            AND opsrcpv.retention_tracking_year =  '201415'
                        GROUP BY opsrcpv.organization_id, opsrcpv.person_id, opsrcpv.retention_tracking_year) AS var_query) AS var_selection
                        WHERE 1 = 1
                         AND var_selection.`Retained to Midyear Year 1` IN ( 1,0 )  AND var_selection.`Retained to Start of Year 2` IN ( 0 )  AND var_selection.`Retained to Midyear Year 2` IN ( 0 ) ) AND person_id = p.id )"
                ]
            ]

        ]);
    }

    public function testGetStudentListBasedCriteriaForCheckAndIncludeSurveyQuestions()
    {

        $this->specify("List the total students based on custom search filter", function ($searchAttributes, $expectedSQL = '') {

            $mockOrganizationEntity = $this->getMock('Organization', [
                'getId',
                'getTimezone'
            ]);
            $mockOrganizationEntity->method('getId')
                ->willReturn(1);
            $mockOrganizationEntity->method('getTimezone')
                ->willReturn('Asia/Kolkotta');

            $mockPersonEntity = $this->getMock('Person', [
                'getId',
                'getOrganization'
            ]);
            $mockPersonEntity->method('getId')
                ->willReturn(1);

            $mockPersonEntity->method('getOrganization')
                ->willReturn($mockOrganizationEntity);

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockOrgSearchRepository = $this->getMock('OrgSearchRepository', array(
                'getOrgSearch',
                'getRiskIntentData'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockResque = $this->getMockBuilder('BCC\ResqueBundle\Resque')
                ->disableOriginalConstructor()
                ->setMethods([
                    'enqueue'
                ])
                ->getMock();

            $mockOrgService = $this->getMock('OrganizationService', array(
                'find'
            ));
            $mockOrgService->method('find')
                ->willReturn($mockOrganizationEntity);

            $mockPersonService = $this->getMock('PersonService', array(
                'findPerson'
            ));
            $mockPersonService->method('findPerson')
                ->willReturn($mockPersonEntity);

            $loggerService = $this->getMock('Logger', [
                'debug',
                'error',
                'info',
                'getLog'
            ]);

            $loggerService->method('getLog')
                ->willReturn(1);

            $mockLogger->method('info')
                ->willReturn(1);

            $utilServiceMock = $this->getMock('UtilServiceHelper', [
                'getDateByTimezone'
            ]);
            $utilServiceMock->method('getDateByTimezone')
                ->willReturn(date('Y-m-d'));

            $mockStaticListStudents = $this->getMock('OrgStaticListStudents', []);

            $mockMetaDataListValues = $this->getMock('MetadataListValues', [
                'findByListName'
            ]);
            $mockMetaDataListValues->method('findByListName')
                ->willReturn(null);

            $mockOrgMetadata = $this->getMock('OrgMetadata', [
                'getScope'
            ]);
            $mockEbiMetadata = $this->getMock('OrgMetadata', [
                'getScope'
            ]);


            $mockOrgMetadataRepo = $this->getMock('OrgMetadataRepository', [
                'findOneBy'
            ]);
            $mockEbiMetadataRepo = $this->getMock('EbiMetadataRepository', [
                'findOneBy'
            ]);

            $mockOrgMetadataRepo->method('findOneBy')
                ->willReturn($mockOrgMetadata);

            $mockEbiMetadataRepo->method('findOneBy')
                ->willReturn($mockEbiMetadata);

            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['getCurrentAcademicYear', 'getAllAcademicYearsForOrganization']);


            $mockOrgAcademicYearRepository->method('getAllAcademicYearsForOrganization')->willReturn(
                [
                    201415,
                    201516,
                    201617]
            );


            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                ['SynapseStaticListBundle:OrgStaticListStudents', $mockStaticListStudents],
                ['SynapseSearchBundle:OrgSearch', $mockOrgSearchRepository],
                ['SynapseCoreBundle:MetadataListValues', $mockMetaDataListValues],
                ['SynapseCoreBundle:OrgMetadata', $mockOrgMetadataRepo],
                ['SynapseCoreBundle:EbiMetadata', $mockEbiMetadataRepo],
                ['SynapseAcademicBundle:OrgAcademicYear', $mockOrgAcademicYearRepository],
            ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        'loggerhelper_service',
                        $loggerService
                    ],
                    [
                        'org_service',
                        $mockOrgService
                    ],
                    [
                        'person_service',
                        $mockPersonService
                    ]
                ]);

            $searchService = new SearchService($mockRepositoryResolver, $mockLogger, $mockContainer, $mockResque, $utilServiceMock);
            $result = $this->executePrivateMethod($searchService, 'checkAndIncludeSurveyQuestions', array($searchAttributes));
            if (!isset($searchAttributes['survey']) || empty($searchAttributes['survey'])) {
                $this->assertEmpty($result);
            } else {
                $sql = preg_replace("/\s+/", " ", trim($result[0]));
                $expectedSQL = preg_replace("/\s+/", " ", trim($expectedSQL));
                $this->assertEquals($sql, $expectedSQL);
            }

        }, [
            'examples' => [
                // Example1: Survey questions included in query when survey filter in search attributes
                [
                    [
                        "academic_updates" => [],
                        "survey" => [
                            [
                                "survey_id" => "15",
                                "survey_questions" =>
                                    [[
                                        "id" => "4695",
                                        "type" => "category",
                                        "options" =>
                                            [[
                                                "answer" => "(7) Extremely",
                                                "value" => "7",
                                                "id" => "16886"

                                            ]]
                                    ]
                                    ]
                            ]
                        ]
                    ],
                    "SELECT DISTINCT
                          sr.person_id
                     FROM survey_response sr
                             INNER JOIN
                          (SELECT
                               *
                           FROM org_faculty_student_permission_map
                           WHERE
                               faculty_id = [FACULTY_ID] ) ofspm ON ofspm.student_id = sr.person_id
                               AND ofspm.org_id = sr.org_id
                               INNER JOIN
                          survey_questions sq ON sq.id = sr.survey_questions_id
                               INNER JOIN
                          org_permissionset_datablock opd ON ofspm.permissionset_id = opd.org_permissionset_id
                               AND opd.organization_id = sr.org_id
                               INNER JOIN
                         datablock_questions dq ON dq.datablock_id = opd.datablock_id
                               AND sq.ebi_question_id = dq.ebi_question_id
                     WHERE sr.org_id = [ORG_ID]
                     AND ( sr.survey_id = 15
                     and sr.survey_questions_id = 4695
                     and decimal_value in ( 7 ) )
                     AND sr.deleted_at IS NULL
                     AND dq.deleted_at IS NULL
                     AND sq.deleted_at IS NULL
                     AND opd.deleted_at IS NULL"

                ],
                // Example2: Survey questions will not be included in query when survey filter is not in search attributes
                [
                    [
                        "academic_updates" => []
                    ]
                ],
                // Example3: Survey questions will not be included in query when survey filter is empty in search attributes
                [
                    [
                        "academic_updates" => [],
                        "survey" => []
                    ]
                ]
            ]

        ]);
    }

    public function testGetStudentListBasedCriteriaForCheckAndIncludeStudentsWithSurveyFactors()
    {

        $this->specify("List the total students based on search attributes", function ($searchAttributes, $personId, $organizationId, $expectedSQL = '') {

            $mockOrganizationEntity = $this->getMock('Organization', [
                'getId',
                'getTimezone'
            ]);
            $mockOrganizationEntity->method('getId')
                ->willReturn(1);
            $mockOrganizationEntity->method('getTimezone')
                ->willReturn('Asia/Kolkotta');

            $mockPersonEntity = $this->getMock('Person', [
                'getId',
                'getOrganization'
            ]);
            $mockPersonEntity->method('getId')
                ->willReturn(1);

            $mockPersonEntity->method('getOrganization')
                ->willReturn($mockOrganizationEntity);

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockOrgSearchRepository = $this->getMock('OrgSearchRepository', array(
                'getOrgSearch',
                'getRiskIntentData'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockResque = $this->getMockBuilder('BCC\ResqueBundle\Resque')
                ->disableOriginalConstructor()
                ->setMethods([
                    'enqueue'
                ])
                ->getMock();

            $mockOrgService = $this->getMock('OrganizationService', array(
                'find'
            ));
            $mockOrgService->method('find')
                ->willReturn($mockOrganizationEntity);

            $mockPersonService = $this->getMock('PersonService', array(
                'findPerson'
            ));
            $mockPersonService->method('findPerson')
                ->willReturn($mockPersonEntity);

            $loggerService = $this->getMock('Logger', [
                'debug',
                'error',
                'info',
                'getLog'
            ]);

            $loggerService->method('getLog')
                ->willReturn(1);

            $mockLogger->method('info')
                ->willReturn(1);

            $utilServiceMock = $this->getMock('UtilServiceHelper', [
                'getDateByTimezone'
            ]);
            $utilServiceMock->method('getDateByTimezone')
                ->willReturn(date('Y-m-d'));

            $mockStaticListStudentsRepository = $this->getMock('OrgStaticListStudents', []);

            $mockMetaDataListValuesRepository = $this->getMock('MetadataListValues', [
                'findByListName'
            ]);
            $mockMetaDataListValuesRepository->method('findByListName')
                ->willReturn(null);

            $mockOrgMetadata = $this->getMock('OrgMetadata', [
                'getScope'
            ]);
            $mockEbiMetadata = $this->getMock('OrgMetadata', [
                'getScope'
            ]);


            $mockOrgMetadataRepository = $this->getMock('OrgMetadataRepository', [
                'findOneBy'
            ]);
            $mockEbiMetadataRepository = $this->getMock('EbiMetadataRepository', [
                'findOneBy'
            ]);

            $mockOrgMetadataRepository->method('findOneBy')
                ->willReturn($mockOrgMetadata);

            $mockEbiMetadataRepository->method('findOneBy')
                ->willReturn($mockEbiMetadata);

            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['getCurrentAcademicYear', 'getAllAcademicYearsForOrganization']);


            $mockOrgAcademicYearRepository->method('getAllAcademicYearsForOrganization')->willReturn(
                [
                    201415,
                    201516,
                    201617]
            );

            $mockOrgPersonFacultyRepository = $this->getMock("OrgPersonFacultyRepository", ['findOneBy']);
            $mockFactorRepository = $this->getMock("FactorRepository", ['find']);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [OrgStaticListStudentsRepository::REPOSITORY_KEY,
                    $mockStaticListStudentsRepository
                ],
                [OrgSearchRepository::REPOSITORY_KEY,
                    $mockOrgSearchRepository
                ],
                [MetadataListValuesRepository::REPOSITORY_KEY,
                    $mockMetaDataListValuesRepository
                ],
                [OrgMetadataRepository::REPOSITORY_KEY,
                    $mockOrgMetadataRepository
                ],
                [EbiMetadataRepository::REPOSITORY_KEY,
                    $mockEbiMetadataRepository
                ],
                [OrgAcademicYearRepository::REPOSITORY_KEY,
                    $mockOrgAcademicYearRepository
                ],
                [
                    OrgPersonFacultyRepository::REPOSITORY_KEY,
                    $mockOrgPersonFacultyRepository
                ],
                [
                    FactorRepository::REPOSITORY_KEY,
                    $mockFactorRepository
                ]
            ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        'loggerhelper_service',
                        $loggerService
                    ],
                    [
                        'org_service',
                        $mockOrgService
                    ],
                    [
                        'person_service',
                        $mockPersonService
                    ]
                ]);

            $mockOrgPersonFacultyRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue(true));
            $mockFactorRepository->expects($this->any())
                ->method('find')
                ->will($this->returnValue(true));
            $searchService = new SearchService($mockRepositoryResolver, $mockLogger, $mockContainer, $mockResque, $utilServiceMock);
            $result = $this->executePrivateMethod($searchService, 'checkAndIncludeStudentsWithSurveyFactors', array($searchAttributes, $personId, $organizationId));
            if ((!isset($searchAttributes['survey_filter']) || empty($searchAttributes['survey_filter']))
                || (!isset($searchAttributes['survey_filter']['survey_id']) || empty($searchAttributes['survey_filter']['survey_id']))
                || (!isset($searchAttributes['survey_filter']['factors']) || empty($searchAttributes['survey_filter']['factors']))
            ) {
                $this->assertEmpty($result);
            } else {
                $sql = preg_replace("/\s+/", " ", trim($result[0]));
                $expectedSQL = preg_replace("/\s+/", " ", trim($expectedSQL));
                $this->assertEquals($sql, $expectedSQL);
            }

        }, [
            'examples' => [
                // Example1: Survey factors included in query when survey filter, survey id and factors in search attributes
                [
                    [
                        "survey_filter" => [

                            "survey_id" => "11",
                            "academic_year_name" => "2016-2017",
                            "org_academic_year_id" => "192",
                            "year_id" => "201617",
                            "survey_name" => "Transition One",
                            "cohort" => 1,
                            "cohort_Name" => "Survey Cohort 1",
                            "factors" =>
                                [[
                                    "id" => "1",
                                    "factor_name" => "Commitment to the Institution",
                                    "value_min" => "1",
                                    "value_max" => "2",
                                    "survey_id" => 11,
                                    "survey_name" => "Transition One"
                                ],
                                    [
                                        "id" => "2",
                                        "factor_name" => "Self-Assessment: Communication Skills",
                                        "value_min" => "2",
                                        "value_max" => "5",
                                        "survey_id" => 11,
                                        "survey_name" => "Transition One"
                                    ],
                                    [
                                        "id" => "3",
                                        "factor_name" => "Self-Assessment: Analytical Skills",
                                        "value_min" => "5",
                                        "value_max" => "7",
                                        "survey_id" => 11,
                                        "survey_name" => "Transition One"
                                    ]
                                ]

                        ]
                    ],
                    1,
                    1,
                    "SELECT
                        person_id
                    FROM
                        (SELECT DISTINCT
                            pfc.person_id AS person_id
                        FROM
                            person_factor_calculated pfc
                                JOIN
                            org_faculty_student_permission_map ofspm ON (ofspm.student_id = pfc.person_id
                                AND ofspm.faculty_id = [FACULTY_ID]
                                AND ofspm.org_id = [ORG_ID])
                                JOIN
                            org_permissionset_datablock opd ON (ofspm.permissionset_id = opd.org_permissionset_id
                                AND opd.deleted_at IS NULL
                                AND opd.organization_id = [ORG_ID])
                                JOIN
                            datablock_questions dq ON (opd.datablock_id = dq.datablock_id
                                AND dq.deleted_at IS NULL)
                        WHERE
                            pfc.organization_id = [ORG_ID]
                                AND   (pfc.survey_id = 11
                               AND pfc.deleted_at IS NULL
                               AND dq.factor_id = 1
                               AND dq.survey_id = 11
                               AND (pfc.factor_id = 1
                               AND pfc.mean_value BETWEEN 1 AND 2)
                               AND pfc.modified_at = ( SELECT
                                                           modified_at
                                                       FROM
                                                           person_factor_calculated AS fc
                                                       WHERE
                                                           fc.organization_id = [ORG_ID]
                                                               AND fc.person_id = pfc.person_id
                                                               AND fc.factor_id = 1
                                                               AND fc.survey_id = 11
                                                       ORDER BY modified_at DESC
                                                       LIMIT 1)
                               ) OR  (pfc.survey_id = 11
                               AND pfc.deleted_at IS NULL
                               AND dq.factor_id = 2
                               AND dq.survey_id = 11
                               AND (pfc.factor_id = 2
                               AND pfc.mean_value BETWEEN 2 AND 5)
                               AND pfc.modified_at = ( SELECT
                                                           modified_at
                                                       FROM
                                                           person_factor_calculated AS fc
                                                       WHERE
                                                           fc.organization_id = [ORG_ID]
                                                               AND fc.person_id = pfc.person_id
                                                               AND fc.factor_id = 2
                                                               AND fc.survey_id = 11
                                                       ORDER BY modified_at DESC
                                                       LIMIT 1)
                               ) OR  (pfc.survey_id = 11
                               AND pfc.deleted_at IS NULL
                               AND dq.factor_id = 3
                               AND dq.survey_id = 11
                               AND (pfc.factor_id = 3
                               AND pfc.mean_value BETWEEN 5 AND 7)
                               AND pfc.modified_at = ( SELECT
                                                           modified_at
                                                       FROM
                                                           person_factor_calculated AS fc
                                                       WHERE
                                                           fc.organization_id = [ORG_ID]
                                                               AND fc.person_id = pfc.person_id
                                                               AND fc.factor_id = 3
                                                               AND fc.survey_id = 11
                                                       ORDER BY modified_at DESC
                                                       LIMIT 1)
                               )
                        ) AS pfc"

                ],
                // Example2: Survey factors included in query by eliminating non float values of value_min and value_max
                [
                    [
                        "survey_filter" => [

                            "survey_id" => "15",
                            "academic_year_name" => "2016-2017",
                            "org_academic_year_id" => "192",
                            "year_id" => "201617",
                            "survey_name" => "Transition One",
                            "cohort" => 1,
                            "cohort_Name" => "Survey Cohort 1",
                            "factors" =>
                                [[
                                    "id" => "1",
                                    "factor_name" => "Commitment to the Institution",
                                    "value_min" => "0,50",
                                    "value_max" => "0.50",
                                    "survey_id" => 15,
                                    "survey_name" => "Transition One"
                                ],
                                    [
                                        "id" => "2",
                                        "factor_name" => "Self-Assessment: Communication Skills",
                                        "value_min" => "122.00,50",
                                        "value_max" => "122.34343The",
                                        "survey_id" => 11,
                                        "survey_name" => "Transition One"
                                    ],
                                ]

                        ]
                    ],
                    1,
                    1,
                    "SELECT
                        person_id
                    FROM
                        (SELECT DISTINCT
                            pfc.person_id AS person_id
                        FROM
                            person_factor_calculated pfc
                                JOIN
                            org_faculty_student_permission_map ofspm ON (ofspm.student_id = pfc.person_id
                                AND ofspm.faculty_id = [FACULTY_ID]
                                AND ofspm.org_id = [ORG_ID])
                                JOIN
                            org_permissionset_datablock opd ON (ofspm.permissionset_id = opd.org_permissionset_id
                                AND opd.deleted_at IS NULL
                                AND opd.organization_id = [ORG_ID])
                                JOIN
                            datablock_questions dq ON (opd.datablock_id = dq.datablock_id
                                AND dq.deleted_at IS NULL)
                        WHERE
                            pfc.organization_id = [ORG_ID]
                                AND   (pfc.survey_id = 15
                               AND pfc.deleted_at IS NULL
                               AND dq.factor_id = 1
                               AND dq.survey_id = 15
                               AND (pfc.factor_id = 1
                               AND pfc.mean_value BETWEEN 0 AND 0.5)
                               AND pfc.modified_at = ( SELECT
                                                           modified_at
                                                       FROM
                                                           person_factor_calculated AS fc
                                                       WHERE
                                                           fc.organization_id = [ORG_ID]
                                                               AND fc.person_id = pfc.person_id
                                                               AND fc.factor_id = 1
                                                               AND fc.survey_id = 15
                                                       ORDER BY modified_at DESC
                                                       LIMIT 1)
                               ) OR  (pfc.survey_id = 11
                               AND pfc.deleted_at IS NULL
                               AND dq.factor_id = 2
                               AND dq.survey_id = 11
                               AND (pfc.factor_id = 2
                               AND pfc.mean_value BETWEEN 122 AND 122.34343)
                               AND pfc.modified_at = ( SELECT
                                                           modified_at
                                                       FROM
                                                           person_factor_calculated AS fc
                                                       WHERE
                                                           fc.organization_id = [ORG_ID]
                                                               AND fc.person_id = pfc.person_id
                                                               AND fc.factor_id = 2
                                                               AND fc.survey_id = 11
                                                       ORDER BY modified_at DESC
                                                       LIMIT 1)
                               )
                        ) AS pfc"
                ],
                // Example3: Survey factors will not be included in query when survey filter is not in search attributes
                [
                    [
                        "academic_updates" => []
                    ],
                    1,
                    1
                ],
                // Example4: Survey factors will not be included in query when survey filter is empty in search attributes
                [
                    [
                        "survey_filter" => []
                    ],
                    1,
                    1
                ]
            ]

        ]);
    }

    public function executePrivateMethod(&$object, $methodName, $parameters)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testCheckAndIncludeAcademicUpdatesSelection()
    {

        $this->specify("fetching student ids for academic updates based on custom search filter", function ($searchAttributes = null, $loggedInUserId, $organizationId, $updatedDate, $expectedQuery) {

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $academicYearService = $this->getMock('AcademicYearService', array(
                'getAcademicYear'
            ));
            $dateUtilityService = $this->getMock('DateUtilityService', ['convertToUtcDatetime']);

            $mockOrgAcademicYear = $this->getMock('OrgAcademicYear', [
                'getStartDate',
                'getEndDate'
            ]);
            $mockOrgAcademicYear->method('getStartDate')->willReturn(new \DateTime());
            $mockOrgAcademicYear->method('getEndDate')->willReturn(new \DateTime());

            $academicYearService->method('getAcademicYear')
                ->willReturn($mockOrgAcademicYear);
            $dateUtilityService->method('convertToUtcDatetime')->willReturn($updatedDate);


            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        \Synapse\AcademicBundle\Service\Impl\AcademicYearService::SERVICE_KEY,
                        $academicYearService
                    ],
                    [
                        \Synapse\CoreBundle\Service\Utility\DateUtilityService::SERVICE_KEY,
                        $dateUtilityService
                    ]
                ]);

            $searchService = new SearchService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $query = $searchService->checkAndIncludeAcademicUpdatesSelection($searchAttributes, $loggedInUserId, $organizationId);
            $query = str_replace('[FACULTY_ID]', $loggedInUserId, $query);
            $query = str_replace('[ORG_ID]', $organizationId, $query);

            if ($query) {
                $query = $query[0];
            }

            $this->assertEquals($query, $expectedQuery);

        }, [
            'examples' => [
                //Example 0: Creates SQL query for Academic Updates Selection for custom search except term ids
                [
                    $this->createAcademicUpdateSearchAttributes(),
                    $this->personId,
                    $this->organization,
                    '2017-11-11',
                    $this->createAcademicUpdateQuery($this->personId, $this->organization, '2017-11-11')
                ],
                //Example 1: Creates SQL query for Academic Updates multiple grades A,B
                [
                    $this->createAcademicUpdateSearchAttributes(true, null, 'A,B', '', ''),
                    $this->personId,
                    $this->organization,
                    '2017-11-11',
                    $this->createAcademicUpdateQuery($this->personId, $this->organization, '2017-11-11', '', ['A', 'B'], "", '')
                ],
                //Example 2: Creates SQL query for Academic Updates risk level high
                [
                    $this->createAcademicUpdateSearchAttributes(true, null, '', true, ''),
                    $this->personId,
                    $this->organization,
                    '2017-11-11',
                    $this->createAcademicUpdateQuery($this->personId, $this->organization, '2017-11-11', '', [], "high", '')
                ],
                //Example 3: Creates SQL query for Academic Updates risk level low
                [
                    $this->createAcademicUpdateSearchAttributes(true, null, '', false, ''),
                    $this->personId,
                    $this->organization,
                    '2017-11-11',
                    $this->createAcademicUpdateQuery($this->personId, $this->organization, '2017-11-11', '', [], "low", '')
                ],
                //Example 4: Creates SQL query for Academic Updates absence 10
                [
                    $this->createAcademicUpdateSearchAttributes(true, null, '', '', 10),
                    $this->personId,
                    $this->organization,
                    '2017-11-11',
                    $this->createAcademicUpdateQuery($this->personId, $this->organization, '2017-11-11', '', [], "", 10)
                ],
                //Example 5: Creates SQL query for Academic Updates absence range between 20 and 30
                [
                    $this->createAcademicUpdateSearchAttributes(true, null, '', '', null, 20, 30),
                    $this->personId,
                    $this->organization,
                    '2017-11-11',
                    $this->createAcademicUpdateQuery($this->personId, $this->organization, '2017-11-11', '', [], "", null, 20, 30)
                ],
                //Example 6: Creates SQL query for Academic Updates absence min range 20
                [
                    $this->createAcademicUpdateSearchAttributes(true, null, '', '', null, 20, null),
                    $this->personId,
                    $this->organization,
                    '2017-11-11',
                    $this->createAcademicUpdateQuery($this->personId, $this->organization, '2017-11-11', '', [], "", null, 20, null)
                ],
                //Example 7: Creates SQL query for Academic Updates absence max range 30
                [
                    $this->createAcademicUpdateSearchAttributes(true, null, '', '', null, null, 30),
                    $this->personId,
                    $this->organization,
                    '2017-11-11',
                    $this->createAcademicUpdateQuery($this->personId, $this->organization, '2017-11-11', '', [], "", null, null, 30)
                ],
                //Example 8: Does not creates any SQL query for no Academic Updates Selection
                [
                    $this->createAcademicUpdateSearchAttributes(false),
                    $this->personId,
                    $this->organization,
                    '2017-11-11',
                    []
                ]
            ]
        ]);
    }

    private function createAcademicUpdateSearchAttributes($filter = true, $termIds = null, $grade = null, $riskLevel = "", $absence = null, $minRange = null, $maxRange = null)
    {
        if (!isset($riskLevel) || $riskLevel === '') {
            $failureRisk = '';
        } elseif ($riskLevel) {
            $failureRisk = true;
        } else {
            $failureRisk = false;
        }
        $academicUpdateAttributes['academic_updates'] = [
            "grade" => $grade,
            "absences" => $absence,
            "absence_range" => [
                "min_range" => $minRange,
                "max_range" => $maxRange
            ],
            "is_current_academic_year" => true,
            "start_date" => "2017-11-11",
            "end_date" => "2017-12-11",
            "failure_risk" => $failureRisk,
            "ignoreThis" => "",
            "term_ids" => $termIds
        ];

        if (!$filter) {
            $academicUpdateAttributes['academic_updates'] = [];
        }

        return $academicUpdateAttributes;
    }

    private function createAcademicUpdateQuery($loggedInPersonId, $organizationId, $updateDate, $termsId = null, $grades = null, $riskLevel = null, $absence = null, $minRange = null, $maxRange = null)
    {
        $filterString = " AND  ar.update_date IS NOT NULL";
        if ($grades) {
            $grade = implode('","', $grades);
            if ($grade) {
                $filterString .= '   AND  ar.in_progress_grade in ( "' . $grade . '" )';
            }
        }
        if ($absence) {
            $filterString .= '   AND  ar.absence = ' . $absence . '';
        }
        if ($riskLevel) {
            $filterString .= '   AND  ar.failure_risk_level = "' . $riskLevel . '"';
        }
        if ($termsId) {
            $filterString .= "   AND ( oc.org_academic_terms_id IN ($termsId) ) ";
        }
        if ($minRange && $maxRange) {
            $filterString .= "   AND  ( ar.absence BETWEEN $minRange AND $maxRange )";
        } elseif ($minRange) {
            $filterString .= "   AND  ( ar.absence >= $minRange )";
        } elseif ($maxRange) {
            $filterString .= "   AND  ( ar.absence <= $maxRange )";
        }

        $query = "
            SELECT
                person_id
            FROM
              (
                SELECT
                    DISTINCT ofspm.student_id AS person_id
                FROM
                    academic_record ar
                        JOIN
                    ( SELECT 
                            DISTINCT org_id,
                            student_id,
                            faculty_id,
                            permissionset_id 
                      FROM 
                            org_faculty_student_permission_map 
                      WHERE 
                            faculty_id = $loggedInPersonId
                    ) ofspm
                            ON ofspm.org_id = ar.organization_id
                            AND ofspm.student_id = ar.person_id_student
                        JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                        JOIN
                    org_courses oc
                            ON oc.organization_id = ar.organization_id
                            AND oc.id = ar.org_courses_id
                        JOIN
                    org_academic_terms oat
                            ON oat.organization_id = oc.organization_id
                            AND oat.id = oc.org_academic_terms_id
                        JOIN
                    org_academic_year oay
                            ON oay.organization_id = oat.organization_id
                            AND oay.id = oat.org_academic_year_id
                        LEFT JOIN
                    org_course_faculty ocf
                            ON ocf.organization_id = ar.organization_id
                            AND oc.id = ocf.org_courses_id
                            AND ocf.deleted_at IS NULL
                        JOIN
                    org_course_student ocs
                            ON ocs.organization_id = ar.organization_id
                            AND oc.id = ocs.org_courses_id
                            AND ocs.person_id = ar.person_id_student
                WHERE
                    ar.deleted_at IS NULL
                    AND op.deleted_at IS NULL
                    AND oc.deleted_at IS NULL
                    AND oat.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                    AND ocs.deleted_at IS NULL
                    AND ofspm.faculty_id = $loggedInPersonId
                    AND ofspm.org_id = $organizationId
                    AND ar.update_date BETWEEN '" . $updateDate . "' AND '" . $updateDate . "'
                    AND '" . $updateDate . "' >= oat.start_date AND '" . $updateDate . "' <= oat.end_date
                    AND CURDATE() BETWEEN oay.start_date AND oay.end_date
                    AND op.accesslevel_ind_agg = 1
                    AND
                    (
                        (op.view_courses = 1 AND op.view_all_academic_update_courses = 1)
                            OR
                        (op.create_view_academic_update = 1 AND ocf.person_id = $loggedInPersonId)
                    )
                    $filterString  
              ) au  
              ";

        return $query;

    }

    public function testGetFilterCriteriaForAcademicUpdate()
    {

        $this->specify(
            'Test get filter criteria for academic update',
            function ($searchAttributes, $expectedResult) {

                $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
                $mockLogger = $this->getMock('Logger', array('debug', 'error'));
                $mockContainer = $this->getMock('Container', array('get'));

                $mockSearchUtilityService = $this->getMock('SearchUtilityService', ['makeSqlQuery']);

                $iterator = 0;
                if (isset($searchAttributes['academic_updates']['grade']) && !empty($searchAttributes['academic_updates']['grade'])) {
                    $mockSearchUtilityService->expects($this->at($iterator))->method("makeSqlQuery")->willReturn($this->makeSqlQuery($searchAttributes['academic_updates']['grade'], 'au.grade'));
                    $iterator++;
                }
                if (isset($searchAttributes['academic_updates']['final_grade']) && !empty($searchAttributes['academic_updates']['final_grade'])) {
                    $mockSearchUtilityService->expects($this->at($iterator))->method("makeSqlQuery")->willReturn($this->makeSqlQuery($searchAttributes['academic_updates']['final_grade'], 'au.final_grade'));
                }

                $mockContainer->method('get')->willReturnMap([
                    [SearchUtilityService::SERVICE_KEY, $mockSearchUtilityService]
                ]);

                $searchService = new SearchService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $searchService->getFilterCriteriaForAcademicUpdate($searchAttributes['academic_updates']);

                $this->assertEquals(trim($result), trim($expectedResult));
            },
            [
                'examples' =>
                    [
                        // Example 1 : Will return grade appended query string
                        [
                            "search_attributes" =>
                                [
                                    "academic_updates" =>
                                        [
                                            "term_ids" => "3",
                                            "is_current_academic_year" => true,
                                            "failure_risk" => "high",
                                            "grade" => "B",
                                            "absences" => "",
                                            "final_grade" => ""
                                        ],
                                ],
                            '(  ( oc.org_academic_terms_id IN (3) ) AND  ( au.failure_risk_level = "high" ) AND  (au.grade = \'B\'))'
                        ],
                        // Example 2 : Will return absences appended query string
                        [
                            "search_attributes" =>
                                [
                                    "academic_updates" =>
                                        [
                                            "term_ids" => "3",
                                            "is_current_academic_year" => true,
                                            "failure_risk" => "high",
                                            "grade" => "",
                                            "absences" => "2",
                                            "absence_range" => [
                                                "min_range" => "5",
                                                "max_range" => "20"
                                            ],
                                            "final_grade" => ""
                                        ],
                                ],
                            '(  ( oc.org_academic_terms_id IN (3) ) AND  ( au.failure_risk_level = "high" ) AND  ( au.absence = 2 ))'
                        ],
                        // Example 3 : Will return absence_range appended query string
                        [
                            "search_attributes" =>
                                [
                                    "academic_updates" =>
                                        [
                                            "term_ids" => "3",
                                            "is_current_academic_year" => true,
                                            "failure_risk" => "high",
                                            "grade" => "",
                                            "absences" => "",
                                            "absence_range" => [
                                                "min_range" => "5",
                                                "max_range" => "20"
                                            ],
                                            "final_grade" => ""
                                        ],
                                ],
                            '(  ( oc.org_academic_terms_id IN (3) ) AND  ( au.failure_risk_level = "high" ) AND  ( au.absence between 5 and 20 ))'
                        ],
                        // Example 4 : Will return final grade appended query string
                        [
                            "search_attributes" =>
                                [
                                    "academic_updates" =>
                                        [
                                            "term_ids" => "3",
                                            "is_current_academic_year" => true,
                                            "failure_risk" => "high",
                                            "grade" => "",
                                            "absences" => "",
                                            "absence_range" => [
                                                "min_range" => "5",
                                                "max_range" => "20"
                                            ],
                                            "final_grade" => "C"
                                        ],
                                ],
                            '(  ( oc.org_academic_terms_id IN (3) ) AND  ( au.failure_risk_level = "high" ) AND  ( au.absence between 5 and 20 ) AND  ( au.final_grade = \'C\' ))'
                        ],
                        // Example 5 : Will return academic update query with start and end dates string
                        [
                            "search_attributes" =>
                                [
                                    "academic_updates" =>
                                        [
                                            "term_ids" => "3",
                                            "is_current_academic_year" => true,
                                            "failure_risk" => "high",
                                            "grade" => "B",
                                            "absences" => "2",
                                            "absence_range" => [
                                                "min_range" => "5",
                                                "max_range" => "20"
                                            ],
                                            "final_grade" => "C",
                                            'start_date' => '2017-11-18 14:24:56',
                                            'end_date' => '2017-11-21 14:24:56'
                                        ],
                                ],
                            '(  ( oc.org_academic_terms_id IN (3) ) AND  ( au.failure_risk_level = "high" ) AND  (au.grade = \'B\') AND  ( au.absence = 2 ) AND  ( au.final_grade = \'C\' ))   AND ( DATE(au.update_date) BETWEEN STR_TO_DATE("2017-11-18 14:24:56", "%Y-%m-%d") and STR_TO_DATE("2017-11-21 14:24:56", "%Y-%m-%d") )'
                        ]
                    ]
            ]
        );
    }

    private function makeSqlQuery($value, $append)
    {
        $valueCount = substr_count($value, ',');
        if ($value == null || strlen($value) < 1) {
            $sqlAppend = "";
        } elseif ($valueCount == 0 && strlen($value) > 0) {
            $sqlAppend = $append . " = '" . $value . "'";
        } elseif ($valueCount > 0) {
            $valueArray = explode(',', $value);
            foreach ($valueArray as $arrayData) {
                if (trim($arrayData) != "") {
                    $resultArray[] = $arrayData;
                }
                $value = "'" . implode("','", $resultArray) . "'";
            }
            $sqlAppend = $append . " in ($value)";
        } else {
            $sqlAppend = "";
        }
        return $sqlAppend;
    }

    public function testCheckAndIncludeRiskSelection()
    {
        $this->specify("create SQL section for Risk Indicators", function ($searchAttributes, $incomingSQL, $expectedSQl) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);
            $mockRiskLevelsRepository = $this->getMock('RiskLevelsRepository', ['findOneBy']);
            $mockSearchUtilityService = $this->getMock('SearchServiceUtility', ['makeSqlQuery']);

            $mockGrayRisk = new RiskLevels();
            $mockGrayRisk->setId(6);


            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [RiskLevelsRepository::REPOSITORY_KEY, $mockRiskLevelsRepository],

            ]);

            $mockContainer->method('get')->willReturnMap([[SearchUtilityService::SERVICE_KEY, $mockSearchUtilityService]]);

            //Return Gray Risk Level
            $mockRiskLevelsRepository->expects($this->any())
                ->method('findOneBy')
                ->willReturn($mockGrayRisk);


            $mockSearchUtilityService->method('makeSqlQuery')->willReturn($incomingSQL);

            $searchService = new SearchService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $sql = $searchService->checkAndIncludeRiskSelection($searchAttributes);
            $this->assertEquals($expectedSQl, $sql);
        }, [
                'examples' => [
                    //Risk Levels of 1, 2, 3
                    [
                        [
                            'risk_indicator_ids' => "1, 2, 3"
                        ],
                        'p.risk_level IN (1, 2, 3)',
                        ' /* Filter to select student with specified risk */ p.risk_level IN (1, 2, 3)'
                    ],
                    //Risk Levels With Gray(6)
                    [
                        [
                            'risk_indicator_ids' => "1, 2, 6"
                        ],
                        'p.risk_level IN (1, 2, 6)',
                        ' /* Filter to select student with specified risk */ ( p.risk_level IN (1, 2, 6) OR p.risk_level IS NULL )'
                    ],
                    //No Risk Indicators Set
                    [
                        [
                            'risk_indicator_ids' => ""
                        ],
                        '',
                        ''
                    ]


                ]
            ]
        );


    }


    public function testGetSelectedDateValue()
    {

        $this->specify("Testing get Selected Date value", function ($metadata, $field, $notCondition, $expectedResult) {


            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));


            $searchService = new SearchService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $reflectionClass = new ReflectionClass(get_class($searchService));

            $method = $reflectionClass->getMethod('getSelectedDateValue');
            $method->setAccessible(true);

            $result = $method->invokeArgs($searchService, [$metadata, $field, $notCondition]);
            $result = str_replace(' ', '', $result);
            $expectedResult = str_replace(' ', '', $expectedResult);
            $this->assertEquals($result, $expectedResult);


        }, [
            'examples' => [

                //Query Without NOT condition
                [
                    [
                        'start_date' => "2010-10-12",
                        'end_date' => "2010-10-13"
                    ],
                    "field_to_test",
                    'not',
                    <<<TEXT
(
    ( STR_TO_DATE( field_to_test, "%m/%d/%Y")  NOT BETWEEN  '2010-10-12' AND '2010-10-13') 
    OR
    ( STR_TO_DATE( field_to_test, "%Y-%m-%d")  NOT BETWEEN '2010-10-12' AND '2010-10-13')
)
TEXT


                ],

                //Query with NOT condition
                [
                    [
                        'start_date' => "2010-10-12",
                        'end_date' => "2010-10-13"
                    ],
                    "field_to_test",
                    '',
                    <<<TEXT
(
    ( STR_TO_DATE( field_to_test, "%m/%d/%Y")   BETWEEN  '2010-10-12' AND '2010-10-13') 
    OR
    ( STR_TO_DATE( field_to_test, "%Y-%m-%d")   BETWEEN '2010-10-12' AND '2010-10-13')
)
TEXT


                ],

                //Query without start date and end date
                [
                    [
                        'start_date' => "",
                        'end_date' => ""
                    ],
                    "field_to_test",
                    '',
                    ''


                ],

                //Query with start date but no end date
                [
                    [
                        'start_date' => "2010-10-10",
                        'end_date' => ""
                    ],
                    "field_to_test",
                    '', "field_to_test >= '2010-10-10'"


                ],

                //Query with end date but no start date
                [
                    [
                        'start_date' => "",
                        'end_date' => "2010-10-10"
                    ],
                    "field_to_test",
                    '',
                    "field_to_test<='2010-10-10'"
                ],

            ]

        ]);
    }

}