<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\PersonOrgMetaDataRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;


class PersonOrgMetadataRepositoryTest extends Test
{
    use Codeception\Specify;


    private $classLevelEbiMetadataId = 56;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var PersonOrgMetaDataRepository
     */
    private $personOrgMetadataRepository;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;


    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->personOrgMetadataRepository = $this->repositoryResolver->getRepository(PersonOrgMetaDataRepository::REPOSITORY_KEY);
    }

    public function testGetGpaDataForIsp()
    {

        $this->specify("Verify the functionality of the method getGpaDataForIsp", function ($organizationId, $facultyId, $gpaYearId, $orgAcademicTermId, $orgAcademicYearId, $ebiMetaKey, $orgMetaDataId, $caseQuery, $type, $studentIdsToInclude, $expectedResults, $expectedCount) {
            $results = $this->personOrgMetadataRepository->getGpaDataForIsp($orgMetaDataId, $organizationId, $facultyId, $gpaYearId, $orgAcademicTermId, $orgAcademicYearId, $caseQuery, $studentIdsToInclude);
            verify(count($results))->equals($expectedCount);
            $testArray = [];
            $results = array_slice($results, 0, 11);
            foreach ($results as $key => $resultData) {
                unset($resultData['participant_id']); // can’t test RAND()
                $testArray[$key] = $resultData;
            }
            verify($testArray)->equals($expectedResults);

        }, ["examples" =>
            [
                [ // test gpa data for category type org metadata with term and year info (having term id and year id)

                    'organizationId' => "195",
                    'facultyId' => '4605389',
                    'gpaYearId' => "201617",
                    'orgAcademicTermId' => '404',
                    'orgAcademicYearId' => '162',
                    'ebiMetaKey' => 'EndTermGPA',
                    'orgMetaDataId' => "7165",

                    [
                        'case_query' => "CASE
                            WHEN pom.metadata_value IN (:subpopulation1_category_values) THEN 1
                            WHEN pom.metadata_value IN (:subpopulation2_category_values) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_category_values' => ['0'],
                            'subpopulation2_category_values' => ['1']
                        ]
                    ],
                    "gpa",
                    [],
                    [
                        [
                            "org_academic_terms_id" => "404",
                            "term_name" => "Term",
                            "subpopulation_id" => "2",
                            "gpa_value" => "4.00"
                        ]

                    ],
                    1
                ],
                [ // test gpa data for category type org metadata

                    'organizationId' => "92",
                    'facultyId' => '4865923',
                    'gpaYearId' => "201516",
                    'orgAcademicTermId' => '',
                    'orgAcademicYearId' => '',
                    'ebiMetaKey' => 'EndTermGPA',
                    'orgMetaDataId' => "7051",

                    [
                        'case_query' => "CASE
                            WHEN pom.metadata_value IN (:subpopulation1_category_values) THEN 1
                            WHEN pom.metadata_value IN (:subpopulation2_category_values) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_category_values' => ["ACCYBS"],
                            'subpopulation2_category_values' => ["AHPTHERPRE"]
                        ]
                    ],
                    "gpa",
                    [],
                    [
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "4.00"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "2.25"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "3.25"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "3.07"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "2.69"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "3.27"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "3.06"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "4.00"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "4.00"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "1.50"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "4.00"
                        ]
                    ],
                    234
                ],
                [ // test gpa data for category type org metadata with filtered students

                    'organizationId' => "92",
                    'facultyId' => '4865923',
                    'gpaYearId' => "201516",
                    'orgAcademicTermId' => '',
                    'orgAcademicYearId' => '',
                    'ebiMetaKey' => 'EndTermGPA',
                    'orgMetaDataId' => "7051",

                    [
                        'case_query' => "CASE
                            WHEN pom.metadata_value IN (:subpopulation1_category_values) THEN 1
                            WHEN pom.metadata_value IN (:subpopulation2_category_values) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_category_values' => ["ACCYBS"],
                            'subpopulation2_category_values' => ["AHPTHERPRE"]
                        ]
                    ],
                    "gpa",
                    [
                        4651068, 4651040, 4651013, 4650929, 4650833, 4650793, 4650744, 4650740, 4650721, 4650652, 4650642, 4650619
                    ],
                    [
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "2",
                            "gpa_value" => "4.00"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "2",
                            "gpa_value" => "3.62"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "2",
                            "gpa_value" => "4.00"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "2",
                            "gpa_value" => "3.22"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "2",
                            "gpa_value" => "2.77"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "2",
                            "gpa_value" => "1.00"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "2",
                            "gpa_value" => "2.59"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "2",
                            "gpa_value" => "2.31"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "2",
                            "gpa_value" => "4.00"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "2",
                            "gpa_value" => "4.00"
                        ],
                        [
                            "org_academic_terms_id" => "12",
                            "term_name" => "Fall 2015",
                            "subpopulation_id" => "2",
                            "gpa_value" => "3.41"
                        ]
                    ],
                    12
                ]
            ]

        ]);
    }

    public function testGetFactorDataForIsp()
    {

        $this->specify("Verify the functionality of the method getFactorDataForIsp", function ($organizationId, $facultyId, $firstCohortId, $firstSurveyId, $orgMetaDataId, $surveyYearId, $orgAcademicTermId, $orgAcademicYearId, $caseQuery, $type, $studentIdsToInclude, $expectedResults, $expectedCount) {
            $results = $this->personOrgMetadataRepository->getFactorDataForIsp($orgMetaDataId, $organizationId, $facultyId, $firstCohortId, $firstSurveyId, $surveyYearId, $orgAcademicTermId, $orgAcademicYearId, $caseQuery, $studentIdsToInclude);
            $testArray = [];
            foreach ($results as $key => $resultData) {
                unset($resultData['participant_id']); // can’t test RAND()
                $testArray[$key] = $resultData;
            }
            $testArray = array_slice($testArray, 0, 11);
            verify($testArray)->equals($expectedResults);
            verify(count($results))->equals($expectedCount);

        }, ["examples" =>
            [
                [ // test factor data for category type org metadata
                    'organizationId' => "92",
                    'facultyId' => '4865923',
                    'firstCohartId' => "1",
                    'firstSurveyId' => "11",
                    'orgMetaDataId' => "7051",
                    'surveyYearId' => "201516",
                    null,
                    null,
                    [
                        'case_query' => "CASE
                            WHEN pom.metadata_value IN (:subpopulation1_category_values) THEN 1
                            WHEN pom.metadata_value IN (:subpopulation2_category_values) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_category_values' => ["ACCYBS"],
                            'subpopulation2_category_values' => ["AHPTHERPRE"]
                        ]
                    ],
                    "factor",
                    [],
                    [
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "7.00"
                        ]
                    ],
                    3309

                ],
                [ // test factor data for category type org metadata with filtered students
                    'organizationId' => "92",
                    'facultyId' => '4865923',
                    'firstCohartId' => "1",
                    'firstSurveyId' => "11",
                    'orgMetaDataId' => "7051",
                    'surveyYearId' => "201516",
                    null,
                    null,
                    [
                        'case_query' => "CASE
                            WHEN pom.metadata_value IN (:subpopulation1_category_values) THEN 1
                            WHEN pom.metadata_value IN (:subpopulation2_category_values) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_category_values' => ["ACCYBS"],
                            'subpopulation2_category_values' => ["AHPTHERPRE"]
                        ]
                    ],
                    "factor",
                    [
                        4651068, 4651040, 4650929, 4650721, 4650619, 4650609, 4650606, 4650543, 4650522, 4633537, 4633527, 4633515, 4633484
                    ],
                    [
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "6.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "6.67"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "4.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "7.00"
                        ]
                    ],
                    257

                ]
            ],


        ]);
    }

    public function testGetYearAndTermByOrgMetadataId()
    {
        $this->specify("Verify the functionality of the method getYearAndTermOrgMetadata", function ($orgMetadataId, $expectedResult) {

            $result = $this->personOrgMetadataRepository->getYearAndTermByOrgMetadataId($orgMetadataId);
            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                [7366, //Example 1 Test orgmetadata id for year information present
                    [
                        [
                            'org_academic_year_id' => 194,
                            'year_name' => '2016-17 Academic Year',
                            'org_academic_periods_id' => '',
                            'term_name' => ''

                        ],
                    ]
                ],
                [7365, //Example 2 Test orgmetadata id for term information present
                    [
                        [
                            'org_academic_year_id' => '',
                            'year_name' => '',
                            'org_academic_periods_id' => 212,
                            'term_name' => 'Fall Semester 2015'

                        ],
                    ]
                ],
                [1011, //Example 3 Test invalid orgmetadata id
                    [] // empty result
                ],
                [7366, //Example 4 Test with term name and org academic periods id null
                    [
                        [
                            "org_academic_year_id" => "194",
                            "year_name" => "2016-17 Academic Year",
                            "org_academic_periods_id" => null,
                            "term_name" => null
                        ]
                    ]
                ]
            ]
        ]);
    }


    public function testGetStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues()
    {
        $this->specify("Verify the functionality of the method getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues", function ($orgMetadataId, $studentIds, $orgAcademicYearId, $orgAcademicTermsId, $optionValue, $optionMin, $optionMax, $sortBy, $resultCountLimit, $offset, $expectedResult) {

            $result = $this->personOrgMetadataRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues($orgMetadataId, $this->classLevelEbiMetadataId, $studentIds, $orgAcademicYearId, $orgAcademicTermsId, $optionValue, $optionMin, $optionMax, $sortBy, $resultCountLimit, $offset);

            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                // Example 1:  Base example with default (name) sorting and no offset or limit.
                [6350, [1035513, 1035919, 1035953, 1038536, 1039536, 1039825, 1040424], null, null, null, null, null, null, null, null,
                    [
                        [
                            'student_id' => 1035953,
                            'firstname' => 'Anya',
                            'lastname' => 'Andersen',
                            'external_id' => 1035953,
                            'username' => 'MapworksBetaUser01035953@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Senior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1039825,
                            'firstname' => 'Selena',
                            'lastname' => 'Blackburn',
                            'external_id' => 1039825,
                            'username' => 'MapworksBetaUser01039825@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1040424,
                            'firstname' => 'Ann',
                            'lastname' => 'Blake',
                            'external_id' => 1040424,
                            'username' => 'MapworksBetaUser01040424@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Junior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1035919,
                            'firstname' => 'Jimena',
                            'lastname' => 'Cantrell',
                            'external_id' => 1035919,
                            'username' => 'MapworksBetaUser01035919@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1038536,
                            'firstname' => 'Jeremiah',
                            'lastname' => 'Carson',
                            'external_id' => 1038536,
                            'username' => 'MapworksBetaUser01038536@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1039536,
                            'firstname' => 'Layla',
                            'lastname' => 'Carson',
                            'external_id' => 1039536,
                            'username' => 'MapworksBetaUser01039536@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1035513,
                            'firstname' => 'Elizabeth',
                            'lastname' => 'Cochran',
                            'external_id' => 1035513,
                            'username' => 'MapworksBetaUser01035513@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 2a:  Using limit and offset for first page
                [6350, [1035513, 1035919, 1035953, 1038536, 1039536, 1039825, 1040424], null, null, null, null, null, null, 5, 0,
                    [
                        [
                            'student_id' => 1035953,
                            'firstname' => 'Anya',
                            'lastname' => 'Andersen',
                            'external_id' => 1035953,
                            'username' => 'MapworksBetaUser01035953@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Senior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1039825,
                            'firstname' => 'Selena',
                            'lastname' => 'Blackburn',
                            'external_id' => 1039825,
                            'username' => 'MapworksBetaUser01039825@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1040424,
                            'firstname' => 'Ann',
                            'lastname' => 'Blake',
                            'external_id' => 1040424,
                            'username' => 'MapworksBetaUser01040424@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Junior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1035919,
                            'firstname' => 'Jimena',
                            'lastname' => 'Cantrell',
                            'external_id' => 1035919,
                            'username' => 'MapworksBetaUser01035919@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1038536,
                            'firstname' => 'Jeremiah',
                            'lastname' => 'Carson',
                            'external_id' => 1038536,
                            'username' => 'MapworksBetaUser01038536@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 2b:  Using limit and offset for second page
                [6350, [1035513, 1035919, 1035953, 1038536, 1039536, 1039825, 1040424], null, null, null, null, null, null, 5, 5,
                    [
                        [
                            'student_id' => 1039536,
                            'firstname' => 'Layla',
                            'lastname' => 'Carson',
                            'external_id' => 1039536,
                            'username' => 'MapworksBetaUser01039536@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1035513,
                            'firstname' => 'Elizabeth',
                            'lastname' => 'Cochran',
                            'external_id' => 1035513,
                            'username' => 'MapworksBetaUser01035513@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 2c:  The limit and offset work even if the parameters are passed in as strings.
                [6350, [1035513, 1035919, 1035953, 1038536, 1039536, 1039825, 1040424], null, null, null, null, null, null, '5', '5',
                    [
                        [
                            'student_id' => 1039536,
                            'firstname' => 'Layla',
                            'lastname' => 'Carson',
                            'external_id' => 1039536,
                            'username' => 'MapworksBetaUser01039536@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1035513,
                            'firstname' => 'Elizabeth',
                            'lastname' => 'Cochran',
                            'external_id' => 1035513,
                            'username' => 'MapworksBetaUser01035513@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 3a:  Sorting by student name ascending
                [6350, [1035513, 1035919, 1035953, 1038536, 1039536, 1039825, 1040424], null, null, null, null, null, '+name', null, null,
                    [
                        [
                            'student_id' => 1035953,
                            'firstname' => 'Anya',
                            'lastname' => 'Andersen',
                            'external_id' => 1035953,
                            'username' => 'MapworksBetaUser01035953@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Senior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1039825,
                            'firstname' => 'Selena',
                            'lastname' => 'Blackburn',
                            'external_id' => 1039825,
                            'username' => 'MapworksBetaUser01039825@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1040424,
                            'firstname' => 'Ann',
                            'lastname' => 'Blake',
                            'external_id' => 1040424,
                            'username' => 'MapworksBetaUser01040424@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Junior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1035919,
                            'firstname' => 'Jimena',
                            'lastname' => 'Cantrell',
                            'external_id' => 1035919,
                            'username' => 'MapworksBetaUser01035919@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1038536,
                            'firstname' => 'Jeremiah',
                            'lastname' => 'Carson',
                            'external_id' => 1038536,
                            'username' => 'MapworksBetaUser01038536@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1039536,
                            'firstname' => 'Layla',
                            'lastname' => 'Carson',
                            'external_id' => 1039536,
                            'username' => 'MapworksBetaUser01039536@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1035513,
                            'firstname' => 'Elizabeth',
                            'lastname' => 'Cochran',
                            'external_id' => 1035513,
                            'username' => 'MapworksBetaUser01035513@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 3b:  Sorting by student name descending
                [6350, [1035513, 1035919, 1035953, 1038536, 1039536, 1039825, 1040424], null, null, null, null, null, '-name', null, null,
                    [
                        [
                            'student_id' => 1035513,
                            'firstname' => 'Elizabeth',
                            'lastname' => 'Cochran',
                            'external_id' => 1035513,
                            'username' => 'MapworksBetaUser01035513@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1039536,
                            'firstname' => 'Layla',
                            'lastname' => 'Carson',
                            'external_id' => 1039536,
                            'username' => 'MapworksBetaUser01039536@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1038536,
                            'firstname' => 'Jeremiah',
                            'lastname' => 'Carson',
                            'external_id' => 1038536,
                            'username' => 'MapworksBetaUser01038536@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1035919,
                            'firstname' => 'Jimena',
                            'lastname' => 'Cantrell',
                            'external_id' => 1035919,
                            'username' => 'MapworksBetaUser01035919@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1040424,
                            'firstname' => 'Ann',
                            'lastname' => 'Blake',
                            'external_id' => 1040424,
                            'username' => 'MapworksBetaUser01040424@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Junior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1039825,
                            'firstname' => 'Selena',
                            'lastname' => 'Blackburn',
                            'external_id' => 1039825,
                            'username' => 'MapworksBetaUser01039825@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1035953,
                            'firstname' => 'Anya',
                            'lastname' => 'Andersen',
                            'external_id' => 1035953,
                            'username' => 'MapworksBetaUser01035953@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Senior',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 4a:  Sorting by risk ascending
                [5692, [373851, 376419, 966159, 966299, 4613011, 4613268, 4613299], null, null, null, null, null, 'risk_color', null, null,
                    [
                        [
                            'student_id' => 966299,
                            'firstname' => 'Aubrielle',
                            'lastname' => 'Barnett',
                            'external_id' => 966299,
                            'username' => 'MapworksBetaUser00966299@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 391850
                        ],
                        [
                            'student_id' => 966159,
                            'firstname' => 'Kallie',
                            'lastname' => 'Black',
                            'external_id' => 966159,
                            'username' => 'MapworksBetaUser00966159@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 392680
                        ],
                        [
                            'student_id' => 4613011,
                            'firstname' => 'Marshall',
                            'lastname' => 'Anderson',
                            'external_id' => 4613011,
                            'username' => 'MapworksBetaUser04613011@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 391620
                        ],
                        [
                            'student_id' => 4613299,
                            'firstname' => 'Emmitt',
                            'lastname' => 'Barnett',
                            'external_id' => 4613299,
                            'username' => 'MapworksBetaUser04613299@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 395430
                        ],
                        [
                            'student_id' => 4613268,
                            'firstname' => 'Kieran',
                            'lastname' => 'Davidson',
                            'external_id' => 4613268,
                            'username' => 'MapworksBetaUser04613268@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 393100
                        ],
                        [
                            'student_id' => 373851,
                            'firstname' => 'Faith',
                            'lastname' => 'Ashley',
                            'external_id' => 373851,
                            'username' => 'MapworksBetaUser00373851@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Senior',
                            'profile_item_value' => 391660
                        ],
                        [
                            'student_id' => 376419,
                            'firstname' => 'Rosa',
                            'lastname' => 'Campos',
                            'external_id' => 376419,
                            'username' => 'MapworksBetaUser00376419@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Junior',
                            'profile_item_value' => 393062
                        ]
                    ]
                ],
                // Example 4b:  Sorting by risk descending
                [5692, [373851, 376419, 966159, 966299, 4613011, 4613268, 4613299], null, null, null, null, null, '-risk_color', null, null,
                    [
                        [
                            'student_id' => 4613268,
                            'firstname' => 'Kieran',
                            'lastname' => 'Davidson',
                            'external_id' => 4613268,
                            'username' => 'MapworksBetaUser04613268@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 393100
                        ],
                        [
                            'student_id' => 4613299,
                            'firstname' => 'Emmitt',
                            'lastname' => 'Barnett',
                            'external_id' => 4613299,
                            'username' => 'MapworksBetaUser04613299@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 395430
                        ],
                        [
                            'student_id' => 4613011,
                            'firstname' => 'Marshall',
                            'lastname' => 'Anderson',
                            'external_id' => 4613011,
                            'username' => 'MapworksBetaUser04613011@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 391620
                        ],
                        [
                            'student_id' => 966299,
                            'firstname' => 'Aubrielle',
                            'lastname' => 'Barnett',
                            'external_id' => 966299,
                            'username' => 'MapworksBetaUser00966299@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 391850
                        ],
                        [
                            'student_id' => 966159,
                            'firstname' => 'Kallie',
                            'lastname' => 'Black',
                            'external_id' => 966159,
                            'username' => 'MapworksBetaUser00966159@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 392680
                        ],
                        [
                            'student_id' => 373851,
                            'firstname' => 'Faith',
                            'lastname' => 'Ashley',
                            'external_id' => 373851,
                            'username' => 'MapworksBetaUser00373851@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Senior',
                            'profile_item_value' => 391660
                        ],
                        [
                            'student_id' => 376419,
                            'firstname' => 'Rosa',
                            'lastname' => 'Campos',
                            'external_id' => 376419,
                            'username' => 'MapworksBetaUser00376419@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Junior',
                            'profile_item_value' => 393062
                        ]
                    ]
                ],
                // Example 5a:  Sorting by class level ascending
                // Note: I wasn't able to easily find a list of students who had values for the same ISP and some had a class level and others didn't.
                // But the function being tested has the same logic as the corresponding function in PersonEbiMetadataRepository, which is tested thoroughly.
                [6350, [1035513, 1035919, 1035953, 1038536, 1039536, 1039825, 1040424], null, null, null, null, null, 'class_level', null, null,
                    [
                        [
                            'student_id' => 1038536,
                            'firstname' => 'Jeremiah',
                            'lastname' => 'Carson',
                            'external_id' => 1038536,
                            'username' => 'MapworksBetaUser01038536@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1035513,
                            'firstname' => 'Elizabeth',
                            'lastname' => 'Cochran',
                            'external_id' => 1035513,
                            'username' => 'MapworksBetaUser01035513@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1039825,
                            'firstname' => 'Selena',
                            'lastname' => 'Blackburn',
                            'external_id' => 1039825,
                            'username' => 'MapworksBetaUser01039825@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1035919,
                            'firstname' => 'Jimena',
                            'lastname' => 'Cantrell',
                            'external_id' => 1035919,
                            'username' => 'MapworksBetaUser01035919@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1039536,
                            'firstname' => 'Layla',
                            'lastname' => 'Carson',
                            'external_id' => 1039536,
                            'username' => 'MapworksBetaUser01039536@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1040424,
                            'firstname' => 'Ann',
                            'lastname' => 'Blake',
                            'external_id' => 1040424,
                            'username' => 'MapworksBetaUser01040424@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Junior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1035953,
                            'firstname' => 'Anya',
                            'lastname' => 'Andersen',
                            'external_id' => 1035953,
                            'username' => 'MapworksBetaUser01035953@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Senior',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 5b:  Sorting by class level descending
                [6350, [1035513, 1035919, 1035953, 1038536, 1039536, 1039825, 1040424], null, null, null, null, null, '-class_level', null, null,
                    [
                        [
                            'student_id' => 1035953,
                            'firstname' => 'Anya',
                            'lastname' => 'Andersen',
                            'external_id' => 1035953,
                            'username' => 'MapworksBetaUser01035953@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Senior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1040424,
                            'firstname' => 'Ann',
                            'lastname' => 'Blake',
                            'external_id' => 1040424,
                            'username' => 'MapworksBetaUser01040424@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Junior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1039825,
                            'firstname' => 'Selena',
                            'lastname' => 'Blackburn',
                            'external_id' => 1039825,
                            'username' => 'MapworksBetaUser01039825@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1035919,
                            'firstname' => 'Jimena',
                            'lastname' => 'Cantrell',
                            'external_id' => 1035919,
                            'username' => 'MapworksBetaUser01035919@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1039536,
                            'firstname' => 'Layla',
                            'lastname' => 'Carson',
                            'external_id' => 1039536,
                            'username' => 'MapworksBetaUser01039536@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1038536,
                            'firstname' => 'Jeremiah',
                            'lastname' => 'Carson',
                            'external_id' => 1038536,
                            'username' => 'MapworksBetaUser01038536@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1035513,
                            'firstname' => 'Elizabeth',
                            'lastname' => 'Cochran',
                            'external_id' => 1035513,
                            'username' => 'MapworksBetaUser01035513@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 6a:  Sorting by profile item value ascending (only 2 integer values)
                [6350, [1035513, 1035919, 1035953, 1038536, 1039536, 1039825, 1040424], null, null, null, null, null, 'profile_item_value', null, null,
                    [
                        [
                            'student_id' => 1039825,
                            'firstname' => 'Selena',
                            'lastname' => 'Blackburn',
                            'external_id' => 1039825,
                            'username' => 'MapworksBetaUser01039825@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1035919,
                            'firstname' => 'Jimena',
                            'lastname' => 'Cantrell',
                            'external_id' => 1035919,
                            'username' => 'MapworksBetaUser01035919@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1035953,
                            'firstname' => 'Anya',
                            'lastname' => 'Andersen',
                            'external_id' => 1035953,
                            'username' => 'MapworksBetaUser01035953@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Senior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1040424,
                            'firstname' => 'Ann',
                            'lastname' => 'Blake',
                            'external_id' => 1040424,
                            'username' => 'MapworksBetaUser01040424@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Junior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1038536,
                            'firstname' => 'Jeremiah',
                            'lastname' => 'Carson',
                            'external_id' => 1038536,
                            'username' => 'MapworksBetaUser01038536@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1039536,
                            'firstname' => 'Layla',
                            'lastname' => 'Carson',
                            'external_id' => 1039536,
                            'username' => 'MapworksBetaUser01039536@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1035513,
                            'firstname' => 'Elizabeth',
                            'lastname' => 'Cochran',
                            'external_id' => 1035513,
                            'username' => 'MapworksBetaUser01035513@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 6b:  Sorting by profile item value descending (only 2 integer values)
                [6350, [1035513, 1035919, 1035953, 1038536, 1039536, 1039825, 1040424], null, null, null, null, null, '-profile_item_value', null, null,
                    [
                        [
                            'student_id' => 1035953,
                            'firstname' => 'Anya',
                            'lastname' => 'Andersen',
                            'external_id' => 1035953,
                            'username' => 'MapworksBetaUser01035953@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Senior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1040424,
                            'firstname' => 'Ann',
                            'lastname' => 'Blake',
                            'external_id' => 1040424,
                            'username' => 'MapworksBetaUser01040424@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Junior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1038536,
                            'firstname' => 'Jeremiah',
                            'lastname' => 'Carson',
                            'external_id' => 1038536,
                            'username' => 'MapworksBetaUser01038536@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1039536,
                            'firstname' => 'Layla',
                            'lastname' => 'Carson',
                            'external_id' => 1039536,
                            'username' => 'MapworksBetaUser01039536@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1035513,
                            'firstname' => 'Elizabeth',
                            'lastname' => 'Cochran',
                            'external_id' => 1035513,
                            'username' => 'MapworksBetaUser01035513@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 1039825,
                            'firstname' => 'Selena',
                            'lastname' => 'Blackburn',
                            'external_id' => 1039825,
                            'username' => 'MapworksBetaUser01039825@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1035919,
                            'firstname' => 'Jimena',
                            'lastname' => 'Cantrell',
                            'external_id' => 1035919,
                            'username' => 'MapworksBetaUser01035919@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ]
                    ]
                ],
                // Example 8:  Sorting by profile item value ascending (strings)
                [7035, [775031, 972031, 4557184, 4557376], null, null, null, null, null, 'profile_item_value', null, null,
                    [
                        [
                            'student_id' => 4557376,
                            'firstname' => 'Kristian',
                            'lastname' => 'Acosta',
                            'external_id' => 4557376,
                            'username' => 'MapworksBetaUser04557376@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 'Biology'
                        ],
                        [
                            'student_id' => 775031,
                            'firstname' => 'Tobias',
                            'lastname' => 'Allen',
                            'external_id' => 775031,
                            'username' => 'MapworksBetaUser00775031@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Senior',
                            'profile_item_value' => 'BIOLOGY'
                        ],
                        [
                            'student_id' => 4557184,
                            'firstname' => 'Finnegan',
                            'lastname' => 'Dunn',
                            'external_id' => 4557184,
                            'username' => 'MapworksBetaUser04557184@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 'Business Administration'
                        ],
                        [
                            'student_id' => 972031,
                            'firstname' => 'Madalyn',
                            'lastname' => 'Allen',
                            'external_id' => 972031,
                            'username' => 'MapworksBetaUser00972031@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Senior',
                            'profile_item_value' => 'NURSING'
                        ]

                    ]
                ],
                // Example 9:  Demonstrating secondary sorting by student last name, first name, and primary email.
                // Since the email is based on the student id in the functional testing database,
                // it's impossible to show that the last level of sorting is by email rather than person_id or external_id,
                // but this test at least demonstrates that it's deterministic.
                [6031, [644560, 646920, 947920, 4617920, 4644920], null, null, null, null, null, 'profile_item_value', null, null,
                    [
                        [
                            'student_id' => 646920,
                            'firstname' => 'Beckett',
                            'lastname' => 'Daugherty',
                            'external_id' => 646920,
                            'username' => 'MapworksBetaUser00646920@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Senior',
                            'profile_item_value' => 'Good Standing'
                        ],
                        [
                            'student_id' => 4644920,
                            'firstname' => 'Beckett',
                            'lastname' => 'Daugherty',
                            'external_id' => 4644920,
                            'username' => 'MapworksBetaUser04644920@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Junior',
                            'profile_item_value' => 'Good Standing'
                        ],
                        [
                            'student_id' => 4617920,
                            'firstname' => 'Hope',
                            'lastname' => 'Daugherty',
                            'external_id' => 4617920,
                            'username' => 'MapworksBetaUser04617920@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Senior',
                            'profile_item_value' => 'Good Standing'
                        ],
                        [
                            'student_id' => 644560,
                            'firstname' => 'Avianna',
                            'lastname' => 'Davenport',
                            'external_id' => 644560,
                            'username' => 'MapworksBetaUser00644560@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Senior',
                            'profile_item_value' => 'Good Standing'
                        ],
                        [
                            'student_id' => 947920,
                            'firstname' => 'Elle',
                            'lastname' => 'Daugherty',
                            'external_id' => 947920,
                            'username' => 'MapworksBetaUser00947920@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 'Probation'
                        ]
                    ]
                ],
                // Example 10:  Filtering by a particular option value
                [6350, [1035513, 1035919, 1035953, 1038536, 1039536, 1039825, 1040424], null, null, 0, null, null, null, null, null,
                    [
                        [
                            'student_id' => 1039825,
                            'firstname' => 'Selena',
                            'lastname' => 'Blackburn',
                            'external_id' => 1039825,
                            'username' => 'MapworksBetaUser01039825@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1035919,
                            'firstname' => 'Jimena',
                            'lastname' => 'Cantrell',
                            'external_id' => 1035919,
                            'username' => 'MapworksBetaUser01035919@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 0
                        ]
                    ]
                ],
                // Example 11:  Filtering by a range of values
                [5692, [373851, 376419, 966159, 966299, 4613011, 4613268, 4613299], null, null, null, 392000, 394000, null, null, null,
                    [
                        [
                            'student_id' => 966159,
                            'firstname' => 'Kallie',
                            'lastname' => 'Black',
                            'external_id' => 966159,
                            'username' => 'MapworksBetaUser00966159@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 392680
                        ],
                        [
                            'student_id' => 376419,
                            'firstname' => 'Rosa',
                            'lastname' => 'Campos',
                            'external_id' => 376419,
                            'username' => 'MapworksBetaUser00376419@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Junior',
                            'profile_item_value' => 393062
                        ],
                        [
                            'student_id' => 4613268,
                            'firstname' => 'Kieran',
                            'lastname' => 'Davidson',
                            'external_id' => 4613268,
                            'username' => 'MapworksBetaUser04613268@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 393100
                        ]
                    ]
                ]
            ]
        ]);

        $this->specify("Verify that the method getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues throws appropriate exceptions", function ($ebiMetadataId, $studentIds, $orgAcademicYearId, $orgAcademicTermsId, $optionValue, $optionMin, $optionMax, $sortBy, $resultCountLimit, $offset, $expectedExceptionClass, $expectedExceptionMessage) {
            try {
                $this->personOrgMetadataRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues($ebiMetadataId, $this->classLevelEbiMetadataId, $studentIds, $orgAcademicYearId, $orgAcademicTermsId, $optionValue, $optionMin, $optionMax, $sortBy, $resultCountLimit, $offset);
            } catch (SynapseException $exception) {
                verify($exception)->isInstanceOf($expectedExceptionClass);
                verify($exception->getMessage())->equals($expectedExceptionMessage);
            }
        }, ['examples' =>
            [
                // Example 1:  If a range is provided, the endpoints must be numeric.
                // This is necessary to prevent SQL injection, and in current functionality, ranges are only used for numeric profile items.
                [5692, [373851], null, null, null, 'a', 'b', null, null, null, 'Synapse\CoreBundle\Exception\SynapseValidationException', 'Range must be numeric.']
            ]
        ]);
    }


    public function testGetStudentISPProfileInformation()
    {
        $this->specify('test get student ISP profile information', function ($studentId, $accessibleISPIds, $expectedResults) {
            $functionResults = $this->personOrgMetadataRepository->getStudentISPProfileInformation($studentId, $accessibleISPIds);
            verify($expectedResults)->equals($functionResults);

        }, ['examples' => [
            [
                5,
                [1, 2, 3],
                [
                    0 =>
                        [
                            'org_metadata_id' => '1',
                            'metadata_type' => 'T',
                            'meta_name' => 'PreferredName',
                            'metadata_value' => 'Christina',
                            'year_name' => null,
                            'term_name' => null,
                            'scope' => 'N'
                        ],
                    1 =>
                        [
                            'org_metadata_id' => '3',
                            'metadata_type' => 'N',
                            'meta_name' => 'CulturalEvents',
                            'metadata_value' => '6',
                            'year_name' => null,
                            'term_name' => null,
                            'scope' => 'N'
                        ],
                    2 =>
                        [
                            'org_metadata_id' => '2',
                            'metadata_type' => 'D',
                            'meta_name' => 'OnlineTrainingDate',
                            'metadata_value' => '2014-06-09 00:00:00',
                            'year_name' => null,
                            'term_name' => null,
                            'scope' => 'N'
                        ]
                ]
            ],
            [
                6,
                [1, 2, 3],
                [
                    0 =>
                        [
                            'org_metadata_id' => '1',
                            'metadata_type' => 'T',
                            'meta_name' => 'PreferredName',
                            'metadata_value' => 'Jennifer',
                            'year_name' => null,
                            'term_name' => null,
                            'scope' => 'N'
                        ],
                    1 =>
                        [
                            'org_metadata_id' => '3',
                            'metadata_type' => 'N',
                            'meta_name' => 'CulturalEvents',
                            'metadata_value' => '4',
                            'year_name' => null,
                            'term_name' => null,
                            'scope' => 'N'
                        ],
                    2 =>
                        [
                            'org_metadata_id' => '2',
                            'metadata_type' => 'D',
                            'meta_name' => 'OnlineTrainingDate',
                            'metadata_value' => '2014-06-18 00:00:00',
                            'year_name' => null,
                            'term_name' => null,
                            'scope' => 'N'
                        ]

                ]
            ],
            [
                7,
                [1, 2, 3],
                [
                    0 =>
                        [
                            'org_metadata_id' => '1',
                            'metadata_type' => 'T',
                            'meta_name' => 'PreferredName',
                            'metadata_value' => 'Ian',
                            'year_name' => null,
                            'term_name' => null,
                            'scope' => 'N'
                        ],
                    1 =>
                        [
                            'org_metadata_id' => '3',
                            'metadata_type' => 'N',
                            'meta_name' => 'CulturalEvents',
                            'metadata_value' => '3',
                            'year_name' => null,
                            'term_name' => null,
                            'scope' => 'N'
                        ],
                    2 =>

                        [
                            'org_metadata_id' => '2',
                            'metadata_type' => 'D',
                            'meta_name' => 'OnlineTrainingDate',
                            'metadata_value' => '2014-06-09 00:00:00',
                            'year_name' => null,
                            'term_name' => null,
                            'scope' => 'N'
                        ]
                ]

            ]
        ]]);
    }

    public function testGetRetentionDataForISP()
    {
        $this->specify("Verify the functionality of the method getRetentionDataForISP", function ($orgMetaDataId, $organizationId, $loggedInUserId, $retentionTrackingYearId, $orgAcademicTermId, $orgAcademicYearId, $selectCaseQuery, $studentIdsToInclude, $expectedResults) {
            $results = $this->personOrgMetadataRepository->getRetentionDataForISP($orgMetaDataId, $organizationId, $loggedInUserId, $retentionTrackingYearId, $orgAcademicTermId, $orgAcademicYearId, $selectCaseQuery, $studentIdsToInclude);
            $testArray = [];
            foreach ($results as $key => $resultData) {
                unset($resultData['participant_id']); // can’t test RAND()
                $testArray[$key] = $resultData;
            }
            $testArray = array_slice($testArray, 0, 6);
            verify($testArray)->equals($expectedResults);

        }, ["examples" =>
            [
                [ // Example 1 test with empty data
                    6031, //orgMetaDataId
                    59, //organizationId
                    53573, //facultyId
                    201516, //retentionTrackingYearId
                    null,
                    null,
                    [
                        'case_query' => "CASE
                            WHEN pom.metadata_value BETWEEN (:subpopulation1_min_digits) AND (:subpopulation1_max_digits) THEN 1
                            WHEN pom.metadata_value BETWEEN (:subpopulation2_min_digits) AND (:subpopulation2_max_digits) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_min_digits' => 0,
                            'subpopulation1_max_digits' => 50,
                            'subpopulation2_min_digits' => 51,
                            'subpopulation2_max_digits' => 100
                        ],
                        'parameterTypes' => [
                            'subpopulation1_min_digits' => \PDO::PARAM_INT,
                            'subpopulation1_max_digits' => \PDO::PARAM_INT,
                            'subpopulation2_min_digits' => \PDO::PARAM_INT,
                            'subpopulation2_max_digits' => \PDO::PARAM_INT
                        ]
                    ],
                    [],
                    [
                        [
                            "subpopulation_id" => "1",
                            "organization_id" => "59",
                            "person_id" => "4614937",
                            "retention_tracking_year" => "201516",
                            "year_id" => "201516",
                            "year_name" => "2015-16 Academic Year",
                            "retention_completion_variable_name" => "Completed Degree in 1 Year or Less",
                            "years_from_retention_track" => "0",
                            "retention_completion_value" => "0",
                            "retention_completion_variable_order" => null
                        ],
                        [
                            "subpopulation_id" => "1",
                            "organization_id" => "59",
                            "person_id" => "4614937",
                            "retention_tracking_year" => "201516",
                            "year_id" => "201617",
                            "year_name" => "2016-17 Academic Year",
                            "retention_completion_variable_name" => "Completed Degree in 2 Years or Less",
                            "years_from_retention_track" => "1",
                            "retention_completion_value" => "0",
                            "retention_completion_variable_order" => null
                        ],
                        [
                            "subpopulation_id" => "1",
                            "organization_id" => "59",
                            "person_id" => "4614937",
                            "retention_tracking_year" => "201516",
                            "year_id" => "201718",
                            "year_name" => "2017-18 Academic Year",
                            "retention_completion_variable_name" => "Completed Degree in 3 Years or Less",
                            "years_from_retention_track" => "2",
                            "retention_completion_value" => "0",
                            "retention_completion_variable_order" => null
                        ],
                        [
                            "subpopulation_id" => "1",
                            "organization_id" => "59",
                            "person_id" => "4614937",
                            "retention_tracking_year" => "201516",
                            "year_id" => "201516",
                            "year_name" => "2015-16 Academic Year",
                            "retention_completion_variable_name" => "Retained to Midyear Year 1",
                            "years_from_retention_track" => "0",
                            "retention_completion_value" => "1",
                            "retention_completion_variable_order" => "2"
                        ],
                        [
                            "subpopulation_id" => "1",
                            "organization_id" => "59",
                            "person_id" => "4614937",
                            "retention_tracking_year" => "201516",
                            "year_id" => "201617",
                            "year_name" => "2016-17 Academic Year",
                            "retention_completion_variable_name" => "Retained to Midyear Year 2",
                            "years_from_retention_track" => "1",
                            "retention_completion_value" => "1",
                            "retention_completion_variable_order" => "2"
                        ],
                        [
                            "subpopulation_id" => "1",
                            "organization_id" => "59",
                            "person_id" => "4614937",
                            "retention_tracking_year" => "201516",
                            "year_id" => "201718",
                            "year_name" => "2017-18 Academic Year",
                            "retention_completion_variable_name" => "Retained to Midyear Year 3",
                            "years_from_retention_track" => "2",
                            "retention_completion_value" => "1",
                            "retention_completion_variable_order" => "2"
                        ]
                    ]
                ],
                [ // Example 2 test with data
                    6031, //orgMetaDataId
                    59, //organizationId
                    53573, //facultyId
                    201516, //retentionTrackingYearId
                    null,
                    null,
                    [
                        'case_query' => "CASE
                            WHEN pom.metadata_value BETWEEN (:subpopulation1_min_digits) AND (:subpopulation1_max_digits) THEN 1
                            WHEN pom.metadata_value BETWEEN (:subpopulation2_min_digits) AND (:subpopulation2_max_digits) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_min_digits' => 0,
                            'subpopulation1_max_digits' => 50,
                            'subpopulation2_min_digits' => 51,
                            'subpopulation2_max_digits' => 100
                        ],
                        'parameterTypes' => [
                            'subpopulation1_min_digits' => \PDO::PARAM_INT,
                            'subpopulation1_max_digits' => \PDO::PARAM_INT,
                            'subpopulation2_min_digits' => \PDO::PARAM_INT,
                            'subpopulation2_max_digits' => \PDO::PARAM_INT
                        ]
                    ],
                    [],
                    [
                        [
                            "subpopulation_id" => "1",
                            "organization_id" => "59",
                            "person_id" => "4614937",
                            "retention_tracking_year" => "201516",
                            "year_id" => "201516",
                            "year_name" => "2015-16 Academic Year",
                            "retention_completion_variable_name" => "Completed Degree in 1 Year or Less",
                            "years_from_retention_track" => "0",
                            "retention_completion_value" => "0",
                            "retention_completion_variable_order" => null
                        ],
                        [
                            "subpopulation_id" => "1",
                            "organization_id" => "59",
                            "person_id" => "4614937",
                            "retention_tracking_year" => "201516",
                            "year_id" => "201617",
                            "year_name" => "2016-17 Academic Year",
                            "retention_completion_variable_name" => "Completed Degree in 2 Years or Less",
                            "years_from_retention_track" => "1",
                            "retention_completion_value" => "0",
                            "retention_completion_variable_order" => null
                        ],
                        [
                            "subpopulation_id" => "1",
                            "organization_id" => "59",
                            "person_id" => "4614937",
                            "retention_tracking_year" => "201516",
                            "year_id" => "201718",
                            "year_name" => "2017-18 Academic Year",
                            "retention_completion_variable_name" => "Completed Degree in 3 Years or Less",
                            "years_from_retention_track" => "2",
                            "retention_completion_value" => "0",
                            "retention_completion_variable_order" => null
                        ],
                        [
                            "subpopulation_id" => "1",
                            "organization_id" => "59",
                            "person_id" => "4614937",
                            "retention_tracking_year" => "201516",
                            "year_id" => "201516",
                            "year_name" => "2015-16 Academic Year",
                            "retention_completion_variable_name" => "Retained to Midyear Year 1",
                            "years_from_retention_track" => "0",
                            "retention_completion_value" => "1",
                            "retention_completion_variable_order" => "2"
                        ],
                        [
                            "subpopulation_id" => "1",
                            "organization_id" => "59",
                            "person_id" => "4614937",
                            "retention_tracking_year" => "201516",
                            "year_id" => "201617",
                            "year_name" => "2016-17 Academic Year",
                            "retention_completion_variable_name" => "Retained to Midyear Year 2",
                            "years_from_retention_track" => "1",
                            "retention_completion_value" => "1",
                            "retention_completion_variable_order" => "2"
                        ],
                        [
                            "subpopulation_id" => "1",
                            "organization_id" => "59",
                            "person_id" => "4614937",
                            "retention_tracking_year" => "201516",
                            "year_id" => "201718",
                            "year_name" => "2017-18 Academic Year",
                            "retention_completion_variable_name" => "Retained to Midyear Year 3",
                            "years_from_retention_track" => "2",
                            "retention_completion_value" => "1",
                            "retention_completion_variable_order" => "2"
                        ]
                    ]
                ],
                [ // Example 3 test with data with filtered students
                    6031, //orgMetaDataId
                    59, //organizationId
                    53573, //facultyId
                    201516, //retentionTrackingYearId
                    null,
                    null,
                    [
                        'case_query' => "CASE
                            WHEN pom.metadata_value BETWEEN (:subpopulation1_min_digits) AND (:subpopulation1_max_digits) THEN 1
                            WHEN pom.metadata_value BETWEEN (:subpopulation2_min_digits) AND (:subpopulation2_max_digits) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_min_digits' => 0,
                            'subpopulation1_max_digits' => 50,
                            'subpopulation2_min_digits' => 51,
                            'subpopulation2_max_digits' => 100
                        ],
                        'parameterTypes' => [
                            'subpopulation1_min_digits' => \PDO::PARAM_INT,
                            'subpopulation1_max_digits' => \PDO::PARAM_INT,
                            'subpopulation2_min_digits' => \PDO::PARAM_INT,
                            'subpopulation2_max_digits' => \PDO::PARAM_INT
                        ]
                    ],
                    [
                        4651066, 4651196, 4651284, 4633695, 4633644
                    ],
                    []
                ]
            ]
        ]);
    }

    public function testGetStudentIdsListBasedOnISPSelection()
    {
        $this->specify("Verify the functionality of the method getStudentIdsListBasedOnISPSelection", function ($organizationId, $orgMetadataId, $whereClause, $expectedResults) {
            $results = $this->personOrgMetadataRepository->getStudentIdsListBasedOnISPSelection($organizationId, $orgMetadataId, $whereClause);
            $testArray = array_slice($results, 0, 11);
            verify($testArray)->equals($expectedResults);
        }, ["examples" =>
            [
                [ // Example 1 test with empty data
                    92, //organizationId
                    7051, //orgMetaDataId
                    [
                        "where_query" => "pom.metadata_value IN (:category_values)",
                        "parameters" => [
                            "category_values" => [0, 1, 2]
                        ]
                    ],
                    [
                        0 => 4631637,
                        1 => 4631640,
                        2 => 4631641,
                        3 => 4631643,
                        4 => 4631642,
                        5 => 4631645,
                        6 => 4631644,
                        7 => 4631647,
                        8 => 4631649,
                        9 => 4631650,
                        10 => 4631651
                    ]
                ],
                [ // Example 1 test with empty data
                    92, //organizationId
                    7051, //orgMetaDataId
                    [
                        "where_query" => "((pom.metadata_value BETWEEN :subpopulation1_min_digits AND :subpopulation1_max_digits) OR pom.metadata_value IN (:subpopulation2_single_values))",
                        "parameters" => [
                            "subpopulation1_min_digits" => "0",
                            "subpopulation1_max_digits" => "1",
                            "subpopulation2_single_values" => "2"
                        ]
                    ],
                    [],
                ]
            ]
        ]);
    }

}