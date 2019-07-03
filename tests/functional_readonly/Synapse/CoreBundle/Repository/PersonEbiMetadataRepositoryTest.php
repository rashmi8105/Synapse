<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;

class PersonEbiMetadataRepositoryTest extends Test
{
    use Codeception\Specify;

    private $classLevelEbiMetadataId = 56;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var PersonEbiMetaDataRepository
     */
    private $personEbiMetadataRepository;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->personEbiMetadataRepository = $this->repositoryResolver->getRepository(PersonEbiMetaDataRepository::REPOSITORY_KEY);
    }

    public function testGetGpaDataForProfileblock()
    {

        $this->specify("Verify the functionality of the method getGpaDataForProfileblock", function ($organizationId, $facultyId, $gpaYearId, $orgAcademicTermId, $orgAcademicYearId, $ebiMetaKey, $ebiMetaDataId, $caseQuery, $type, $studentIdsToInclude, $expectedResults, $expectedCount) {
            $results = $this->personEbiMetadataRepository->getGpaDataForProfileblock($ebiMetaDataId, $organizationId, $facultyId, $gpaYearId, $orgAcademicTermId, $orgAcademicYearId, $caseQuery, $studentIdsToInclude);
            foreach ($results as $key => $resultData) {
                unset($resultData['participant_id']); // can’t test RAND()
                $testArray[$key] = $resultData;
            }
            $testArray = array_slice($testArray, 0, 11);
            verify($testArray)->equals($expectedResults);
            verify(count($results))->equals($expectedCount);

        }, ["examples" =>
            [
                [ // test gpa data for category type metadata with academic year information (having yearId)

                    'organizationId' => "62",
                    'facultyId' => '220115',
                    'gpaYearId' => "201516",
                    'orgAcademicTermId' => '',
                    'orgAcademicYearId' => '48',
                    'ebiMetaKey' => 'EndTermGPA',
                    'ebiMetaDataId' => "84",

                    [
                        'case_query' => "CASE
                            WHEN pemfilter.metadata_value IN (:subpopulation1_category_values) THEN 1
                            WHEN pemfilter.metadata_value IN (:subpopulation2_category_values) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_category_values' => [3],
                            'subpopulation2_category_values' => [5]
                        ]
                    ],
                    "gpa",
                    [],
                    [
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "1",
                            "gpa_value" => "3.67"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "1",
                            "gpa_value" => "2.00"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "1",
                            "gpa_value" => "3.67"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "1",
                            "gpa_value" => "3.84"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "1",
                            "gpa_value" => "4.00"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "1",
                            "gpa_value" => "0.00"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "1",
                            "gpa_value" => "2.00"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "1",
                            "gpa_value" => "3.25"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "1",
                            "gpa_value" => "2.12"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "1",
                            "gpa_value" => "1.93"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "1",
                            "gpa_value" => "2.67"
                        ]
                    ],
                    690
                ],
                [ // test gpa data for category type metadata with academic year information and filterd students

                    'organizationId' => "62",
                    'facultyId' => '220115',
                    'gpaYearId' => "201516",
                    'orgAcademicTermId' => '',
                    'orgAcademicYearId' => '48',
                    'ebiMetaKey' => 'EndTermGPA',
                    'ebiMetaDataId' => "84",

                    [
                        'case_query' => "CASE
                            WHEN pemfilter.metadata_value IN (:subpopulation1_category_values) THEN 1
                            WHEN pemfilter.metadata_value IN (:subpopulation2_category_values) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_category_values' => [3],
                            'subpopulation2_category_values' => [5]
                        ]
                    ],
                    "gpa",
                    [
                        4891180, 4891185, 4891193, 4891217, 4891243, 4887638, 4719823, 4719287, 4719355, 4718916, 4718640, 4718630, 4718080, 4717926, 4717572, 4717105, 4716567, 4716201, 4714605, 4714374, 4714313
                    ],
                    [
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "1",
                            "gpa_value" => "1.60"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "1",
                            "gpa_value" => "0.87"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "0.84"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "2.67"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "1.82"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "1.60"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "2.36"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "2.20"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "0.87"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "3.40"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "3.50"
                        ]
                    ],
                    38
                ],
                [ // test gpa data for number type metadata with academic term and academic year information (having yearId and termId)

                    'organizationId' => "62",
                    'facultyId' => '220115',
                    'gpaYearId' => "201516",
                    'orgAcademicTermId' => '108',
                    'orgAcademicYearId' => '48',
                    'ebiMetaKey' => 'EndTermGPA',
                    'ebiMetaDataId' => "84",

                    [
                        'case_query' => "CASE
                            WHEN pemfilter.metadata_value IN (:subpopulation1_category_values) THEN 1
                            WHEN pemfilter.metadata_value IN (:subpopulation2_category_values) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_category_values' => [0],
                            'subpopulation2_category_values' => [1]
                        ]
                    ],
                    "gpa",
                    [],
                    [
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "2.00"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "3.67"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "2.67"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "0.00"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "3.00"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "4.00"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "0.00"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "4.00"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "4.00"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "0.00"
                        ],
                        [
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "subpopulation_id" => "2",
                            "gpa_value" => "2.00"
                        ]
                    ],
                    34
                ],
                [ // test gpa data for category type metadata

                    'organizationId' => "59",
                    'facultyId' => '181819',
                    'gpaYearId' => "201516",
                    'orgAcademicTermId' => '',
                    'orgAcademicYearId' => '',
                    'ebiMetaKey' => 'EndTermGPA',
                    'ebiMetaDataId' => "1",

                    [
                        'case_query' => "CASE
                            WHEN pemfilter.metadata_value IN (:subpopulation1_category_values) THEN 1
                            WHEN pemfilter.metadata_value IN (:subpopulation2_category_values) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_category_values' => [0],
                            'subpopulation2_category_values' => [1]
                        ]
                    ],
                    "gpa",
                    [],
                    [
                        [
                            "org_academic_terms_id" => "212",
                            "term_name" => "Fall Semester 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "4.00"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "term_name" => "Fall Semester 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "3.73"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "term_name" => "Fall Semester 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "1.46"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "term_name" => "Fall Semester 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "3.03"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "term_name" => "Fall Semester 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "2.20"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "term_name" => "Fall Semester 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "3.91"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "term_name" => "Fall Semester 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "4.00"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "term_name" => "Fall Semester 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "0.60"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "term_name" => "Fall Semester 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "3.88"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "term_name" => "Fall Semester 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "3.21"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "term_name" => "Fall Semester 2015",
                            "subpopulation_id" => "1",
                            "gpa_value" => "3.66"
                        ]
                    ],
                    14124
                ]
            ]

        ]);
    }

    public function testGetFactorDataForProfileblock()
    {

        $this->specify("Verify the functionality of the method getFactorDataForProfileblock", function ($organizationId, $facultyId, $firstCohortId, $firstSurveyId, $ebiMetaDataId, $surveyYearId, $orgAcademicTermId, $orgAcademicYearId, $caseQuery, $type, $studentIdsToInclude, $expectedResults, $expectedCount) {
            $results = $this->personEbiMetadataRepository->getFactorDataForProfileblock($ebiMetaDataId, $organizationId, $facultyId, $firstCohortId, $firstSurveyId, $surveyYearId, $orgAcademicTermId, $orgAcademicYearId, $caseQuery, $studentIdsToInclude);
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

                [ // test factor data  for yaer/term specific profile item
                    'organizationId' => "96",
                    'facultyId' => '4558970',
                    'firstCohartId' => "1",
                    'firstSurveyId' => "11",
                    'ebiMetaDataId' => "78",
                    'surveyYearId' => "201516",
                    'orgAcademicTermId' => 163,
                    'orgAcademicYearId' => 73,
                    [
                        'case_query' => "CASE
                            WHEN pemfilter.metadata_value IN (:subpopulation1_category_values) THEN 1
                            WHEN pemfilter.metadata_value IN (:subpopulation2_category_values) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_category_values' => [0],
                            'subpopulation2_category_values' => [1]
                        ]
                    ],
                    "factor",
                    [],
                    [
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "6.67"
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
                            "factor_value" => "6.33"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "6.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "6.67"
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
                            "factor_value" => "4.00"
                        ],

                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "6.33"
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
                            "factor_value" => "4.00"
                        ],

                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "6.00"
                        ],


                    ],
                    67359
                ],

                [ // test factor data for date type metadata
                    'organizationId' => "59",
                    'facultyId' => '181819',
                    'firstCohartId' => "1",
                    'firstSurveyId' => "13",
                    'ebiMetaDataId' => "48",
                    'surveyYearId' => "201516",
                    'orgAcademicTermId' => null,
                    'orgAcademicYearId' => null,
                    [
                        'case_query' => "CASE
                            WHEN STR_TO_DATE(pemfilter.metadata_value , '%m/%d/%Y' ) 
                            BETWEEN STR_TO_DATE(:subpopulation1_start_date , '%m/%d/%Y') AND STR_TO_DATE(:subpopulation1_end_date , '%m/%d/%Y') THEN 1
                            WHEN STR_TO_DATE(pemfilter.metadata_value , '%Y-%m-%d')
                            BETWEEN STR_TO_DATE(:subpopulation1_start_date , '%m/%d/%Y') AND STR_TO_DATE(:subpopulation1_end_date , '%m/%d/%Y') THEN 1
                            WHEN STR_TO_DATE(pemfilter.metadata_value , '%m/%d/%Y')
                            BETWEEN STR_TO_DATE(:subpopulation2_start_date , '%m/%d/%Y') AND STR_TO_DATE(:subpopulation2_end_date , '%m/%d/%Y') THEN 2
                            WHEN STR_TO_DATE(pemfilter.metadata_value , '%Y-%m-%d' )
                            BETWEEN STR_TO_DATE(:subpopulation2_start_date , '%m/%d/%Y') AND STR_TO_DATE(:subpopulation2_end_date , '%m/%d/%Y') THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_start_date' => '01/01/2014',
                            'subpopulation1_end_date' => '04/31/2014',
                            'subpopulation2_start_date' => '01/01/2015',
                            'subpopulation2_end_date' => '12/31/2016'
                        ]
                    ],
                    "factor",
                    [],
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
                            "factor_value" => "6.67"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "2.33"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "6.33"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "5.33"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "6.67"
                        ],
                        [
                            "factor_id" => "2",
                            "factor_name" => "Communication Skills",
                            "subpopulation_id" => "2",
                            "factor_value" => "5.50"
                        ],
                        [
                            "factor_id" => "2",
                            "factor_name" => "Communication Skills",
                            "subpopulation_id" => "2",
                            "factor_value" => "5.50"
                        ],
                        [
                            "factor_id" => "2",
                            "factor_name" => "Communication Skills",
                            "subpopulation_id" => "2",
                            "factor_value" => "6.00"
                        ]
                    ],
                    140

                ],
                [ // test factor data for category type metadata
                    'organizationId' => "59",
                    'facultyId' => '181819',
                    'firstCohartId' => "1",
                    'firstSurveyId' => "13",
                    'ebiMetaDataId' => "1",
                    'surveyYearId' => "201516",
                    'orgAcademicTermId' => null,
                    'orgAcademicYearId' => null,
                    [
                        'case_query' => "CASE
                            WHEN pemfilter.metadata_value IN (:subpopulation1_category_values) THEN 1
                            WHEN pemfilter.metadata_value IN (:subpopulation2_category_values) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_category_values' => [0],
                            'subpopulation2_category_values' => [1]
                        ]
                    ],
                    "factor",
                    [],
                    [
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "3.67"
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
                            "factor_value" => "6.67"
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
                            "factor_value" => "5.00"
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
                            "factor_value" => "6.67"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "6.00"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "1",
                            "factor_value" => "4.67"
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
                    8741

                ],
                [ // test factor data for category type metadata with filtered students
                    'organizationId' => "59",
                    'facultyId' => '181819',
                    'firstCohartId' => "1",
                    'firstSurveyId' => "13",
                    'ebiMetaDataId' => "1",
                    'surveyYearId' => "201516",
                    'orgAcademicTermId' => null,
                    'orgAcademicYearId' => null,
                    [
                        'case_query' => "CASE
                            WHEN pemfilter.metadata_value IN (:subpopulation1_category_values) THEN 1
                            WHEN pemfilter.metadata_value IN (:subpopulation2_category_values) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_category_values' => [0],
                            'subpopulation2_category_values' => [1]
                        ]
                    ],
                    "factor",
                    [
                        4870142, 4711044, 4670053, 4670044, 4670023, 4668819, 4668818, 4664276, 4652387, 4652149, 4644918, 4618166, 4618094, 4618087, 4618085, 4618059, 4618019, 4618007, 4617993, 4617827, 4617820, 4617816
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
                            "factor_value" => "6.33"
                        ],
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
                            "factor_value" => "2.33"
                        ],
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
                            "factor_value" => "6.67"
                        ],
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "2",
                            "factor_value" => "1.00"
                        ]
                    ],
                    413

                ]
            ]

        ]);
    }

    public function testGetProfileBlockWithBlockItemAndYearTermInformation()
    {
        $this->specify("Verify the functionality of the method ", function ($facultyId, $orgId, $ebiMetaKeys, $expectedResult) {

            $result = $this->personEbiMetadataRepository->getProfileBlockWithBlockItemAndYearTermInformation($facultyId, $orgId, $ebiMetaKeys);
            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                //Example 1:  Base example with default (name) sorting and no offset or limit.
                [229929,
                    9,
                    '',
                    [
                        [
                            "ebi_metadata_id" => "1",
                            "datablock_id" => "13",
                            "datablock_name" => "Demographic",
                            "display_name" => "Gender",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => 0
                        ],
                        [
                            "ebi_metadata_id" => "2",
                            "datablock_id" => "13",
                            "datablock_name" => "Demographic",
                            "display_name" => "BirthYear",
                            "item_data_type" => "N",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => 0
                        ],
                        [
                            "ebi_metadata_id" => "4",
                            "datablock_id" => "13",
                            "datablock_name" => "Demographic",
                            "display_name" => "RaceEthnicity",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => 0
                        ],
                        [
                            "ebi_metadata_id" => "5",
                            "datablock_id" => "14",
                            "datablock_name" => "Admissions",
                            "display_name" => "StateInOut",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => 0
                        ],
                        [
                            "ebi_metadata_id" => "6",
                            "datablock_id" => "14",
                            "datablock_name" => "Admissions",
                            "display_name" => "InternationalStudent",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => 0
                        ],
                        [
                            "ebi_metadata_id" => "25",
                            "datablock_id" => "23",
                            "datablock_name" => "Admissions-Tests",
                            "display_name" => "ACTComposite",
                            "item_data_type" => "N",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => 0
                        ],
                        [
                            "ebi_metadata_id" => "26",
                            "datablock_id" => "23",
                            "datablock_name" => "Admissions-Tests",
                            "display_name" => "ACTEnglish",
                            "item_data_type" => "N",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => 0
                        ],
                        [
                            "ebi_metadata_id" => "27",
                            "datablock_id" => "23",
                            "datablock_name" => "Admissions-Tests",
                            "display_name" => "ACTMath",
                            "item_data_type" => "N",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => 0
                        ],
                        [
                            "ebi_metadata_id" => "29",
                            "datablock_id" => "23",
                            "datablock_name" => "Admissions-Tests",
                            "display_name" => "ACTScience",
                            "item_data_type" => "N",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => 0
                        ],
                        [
                            "ebi_metadata_id" => "30",
                            "datablock_id" => "23",
                            "datablock_name" => "Admissions-Tests",
                            "display_name" => "ACTReading",
                            "item_data_type" => "N",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => 0
                        ],
                        [
                            "ebi_metadata_id" => "33",
                            "datablock_id" => "23",
                            "datablock_name" => "Admissions-Tests",
                            "display_name" => "SATMath",
                            "item_data_type" => "N",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => 0
                        ],
                        [
                            "ebi_metadata_id" => "34",
                            "datablock_id" => "23",
                            "datablock_name" => "Admissions-Tests",
                            "display_name" => "SATWriting",
                            "item_data_type" => "N",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => 0
                        ]
                    ]
                ],
                //Example 2 with year and term info with sort order
                [
                    220115,
                    62,
                    '',
                    [
                        [
                            "ebi_metadata_id" => "1",
                            "datablock_id" => "13",
                            "datablock_name" => "Demographic",
                            "display_name" => "Gender",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "2",
                            "datablock_id" => "13",
                            "datablock_name" => "Demographic",
                            "display_name" => "BirthYear",
                            "item_data_type" => "N",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "4",
                            "datablock_id" => "13",
                            "datablock_name" => "Demographic",
                            "display_name" => "RaceEthnicity",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "18",
                            "datablock_id" => "13",
                            "datablock_name" => "Demographic",
                            "display_name" => "FirstGenStudent",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "20",
                            "datablock_id" => "13",
                            "datablock_name" => "Demographic",
                            "display_name" => "MilitaryServedUS",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "5",
                            "datablock_id" => "14",
                            "datablock_name" => "Admissions",
                            "display_name" => "StateInOut",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "6",
                            "datablock_id" => "14",
                            "datablock_name" => "Admissions",
                            "display_name" => "InternationalStudent",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "52",
                            "datablock_id" => "14",
                            "datablock_name" => "Admissions",
                            "display_name" => "EnrollYear",
                            "item_data_type" => "N",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "54",
                            "datablock_id" => "14",
                            "datablock_name" => "Admissions",
                            "display_name" => "EnrollType",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "56",
                            "datablock_id" => "15",
                            "datablock_name" => "Academic Record",
                            "display_name" => "ClassLevel",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "59",
                            "datablock_id" => "15",
                            "datablock_name" => "Academic Record",
                            "display_name" => "HonorsStudent",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "60",
                            "datablock_id" => "16",
                            "datablock_name" => "Demographic Record",
                            "display_name" => "AthleteStudent",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "79",
                            "datablock_id" => "16",
                            "datablock_name" => "Demographic Record",
                            "display_name" => "CampusResident",
                            "item_data_type" => "S",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "75",
                            "datablock_id" => "17",
                            "datablock_name" => "Academic Record-PreYear",
                            "display_name" => "PreYearCredTotal",
                            "item_data_type" => "N",
                            "calendar_assignment" => "Y",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "80",
                            "datablock_id" => "18",
                            "datablock_name" => "Academic Record-Start",
                            "display_name" => "StartTermCredTotal",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "83",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermGPA",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "109",
                            "term_name" => "Spring",
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "83",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermGPA",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "84",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermCreditsEarned",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "109",
                            "term_name" => "Spring",
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "84",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermCreditsEarned",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "85",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermCumGPA",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "109",
                            "term_name" => "Spring",
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "85",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermCumGPA",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "86",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermCumCreditsEarned",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "109",
                            "term_name" => "Spring",
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "86",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermCumCreditsEarned",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "23",
                            "datablock_id" => "22",
                            "datablock_name" => "Admissions-HS",
                            "display_name" => "HighSchoolPercentile",
                            "item_data_type" => "N",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ],
                        [
                            "ebi_metadata_id" => "25",
                            "datablock_id" => "23",
                            "datablock_name" => "Admissions-Tests",
                            "display_name" => "ACTComposite",
                            "item_data_type" => "N",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => "0"
                        ]
                    ]
                ],
                [
                    // Example 3 filter with only one ebi metakey
                    220115,
                    62,
                    [
                        'EndTermGPA'
                    ],
                    [
                        [
                            "ebi_metadata_id" => "83",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermGPA",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "109",
                            "term_name" => "Spring",
                            "is_current_academic_year" => 0
                        ],
                        [
                            "ebi_metadata_id" => "83",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermGPA",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "is_current_academic_year" => 0
                        ]
                    ]
                ],
                [
                    // Example 4 filter with more than one ebi metakey
                    220115,
                    62,
                    [
                        'EndTermGPA', 'StateInOut'
                    ],
                    [
                        [
                            "ebi_metadata_id" => "5",
                            "datablock_id" => "14",
                            "datablock_name" => "Admissions",
                            "display_name" => "StateInOut",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            "is_current_academic_year" => 0
                        ],
                        [
                            "ebi_metadata_id" => "83",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermGPA",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "109",
                            "term_name" => "Spring",
                            "is_current_academic_year" => 0
                        ],
                        [
                            "ebi_metadata_id" => "83",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermGPA",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            "is_current_academic_year" => 0
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testGetStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues()
    {
        $this->specify("Verify the functionality of the method getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues", function ($ebiMetadataId, $studentIds, $orgAcademicYearId, $orgAcademicTermsId, $optionValue, $optionMin, $optionMax, $sortBy, $resultCountLimit, $offset, $expectedResult) {

            $result = $this->personEbiMetadataRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues($ebiMetadataId, $this->classLevelEbiMetadataId, $studentIds, $orgAcademicYearId, $orgAcademicTermsId, $optionValue, $optionMin, $optionMax, $sortBy, $resultCountLimit, $offset);

            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                // Example 1:  Base example with default (name) sorting and no offset or limit.
                [1, [4775354, 4775483, 4775641, 4776313, 4776777, 4776996, 4777077], null, null, null, null, null, null, null, null,
                    [
                        [
                            'student_id' => 4775483,
                            'firstname' => 'Cristiano',
                            'lastname' => 'Bauer',
                            'external_id' => 4775483,
                            'username' => 'MapworksBetaUser04775483@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4777077,
                            'firstname' => 'Case',
                            'lastname' => 'Bennett',
                            'external_id' => 4777077,
                            'username' => 'MapworksBetaUser04777077@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4775641,
                            'firstname' => 'Eleanor',
                            'lastname' => 'Brennan',
                            'external_id' => 4775641,
                            'username' => 'MapworksBetaUser04775641@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4775354,
                            'firstname' => 'Aryan',
                            'lastname' => 'Dawson',
                            'external_id' => 4775354,
                            'username' => 'MapworksBetaUser04775354@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4776996,
                            'firstname' => 'Brennan',
                            'lastname' => 'Duarte',
                            'external_id' => 4776996,
                            'username' => 'MapworksBetaUser04776996@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4776313,
                            'firstname' => 'Aliana',
                            'lastname' => 'Frazier',
                            'external_id' => 4776313,
                            'username' => 'MapworksBetaUser04776313@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4776777,
                            'firstname' => 'Everett',
                            'lastname' => 'Ho',
                            'external_id' => 4776777,
                            'username' => 'MapworksBetaUser04776777@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 2a:  Using limit and offset for first page
                [1, [4775354, 4775483, 4775641, 4776313, 4776777, 4776996, 4777077], null, null, null, null, null, null, 5, 0,
                    [
                        [
                            'student_id' => 4775483,
                            'firstname' => 'Cristiano',
                            'lastname' => 'Bauer',
                            'external_id' => 4775483,
                            'username' => 'MapworksBetaUser04775483@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4777077,
                            'firstname' => 'Case',
                            'lastname' => 'Bennett',
                            'external_id' => 4777077,
                            'username' => 'MapworksBetaUser04777077@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4775641,
                            'firstname' => 'Eleanor',
                            'lastname' => 'Brennan',
                            'external_id' => 4775641,
                            'username' => 'MapworksBetaUser04775641@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4775354,
                            'firstname' => 'Aryan',
                            'lastname' => 'Dawson',
                            'external_id' => 4775354,
                            'username' => 'MapworksBetaUser04775354@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4776996,
                            'firstname' => 'Brennan',
                            'lastname' => 'Duarte',
                            'external_id' => 4776996,
                            'username' => 'MapworksBetaUser04776996@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ]
                    ]
                ],
                // Example 2b:  Using limit and offset for second page
                [1, [4775354, 4775483, 4775641, 4776313, 4776777, 4776996, 4777077], null, null, null, null, null, null, 5, 5,
                    [
                        [
                            'student_id' => 4776313,
                            'firstname' => 'Aliana',
                            'lastname' => 'Frazier',
                            'external_id' => 4776313,
                            'username' => 'MapworksBetaUser04776313@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4776777,
                            'firstname' => 'Everett',
                            'lastname' => 'Ho',
                            'external_id' => 4776777,
                            'username' => 'MapworksBetaUser04776777@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 2c:  The limit and offset work even if the parameters are passed in as strings.
                [1, [4775354, 4775483, 4775641, 4776313, 4776777, 4776996, 4777077], null, null, null, null, null, null, '5', '5',
                    [
                        [
                            'student_id' => 4776313,
                            'firstname' => 'Aliana',
                            'lastname' => 'Frazier',
                            'external_id' => 4776313,
                            'username' => 'MapworksBetaUser04776313@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4776777,
                            'firstname' => 'Everett',
                            'lastname' => 'Ho',
                            'external_id' => 4776777,
                            'username' => 'MapworksBetaUser04776777@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 3a:  Sorting by student name ascending
                [1, [4775641, 4776313, 4777077], null, null, null, null, null, '+name', null, null,
                    [
                        [
                            'student_id' => 4777077,
                            'firstname' => 'Case',
                            'lastname' => 'Bennett',
                            'external_id' => 4777077,
                            'username' => 'MapworksBetaUser04777077@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4775641,
                            'firstname' => 'Eleanor',
                            'lastname' => 'Brennan',
                            'external_id' => 4775641,
                            'username' => 'MapworksBetaUser04775641@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4776313,
                            'firstname' => 'Aliana',
                            'lastname' => 'Frazier',
                            'external_id' => 4776313,
                            'username' => 'MapworksBetaUser04776313@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 3b:  Sorting by student name descending
                [1, [4775641, 4776313, 4777077], null, null, null, null, null, '-name', null, null,
                    [
                        [
                            'student_id' => 4776313,
                            'firstname' => 'Aliana',
                            'lastname' => 'Frazier',
                            'external_id' => 4776313,
                            'username' => 'MapworksBetaUser04776313@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4775641,
                            'firstname' => 'Eleanor',
                            'lastname' => 'Brennan',
                            'external_id' => 4775641,
                            'username' => 'MapworksBetaUser04775641@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4777077,
                            'firstname' => 'Case',
                            'lastname' => 'Bennett',
                            'external_id' => 4777077,
                            'username' => 'MapworksBetaUser04777077@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ]
                    ]
                ],
                // Example 4a:  Sorting by risk ascending
                [1, [193027, 767634, 4775354, 4775483, 4775641, 4777077], null, null, null, null, null, 'risk_color', null, null,
                    [
                        [
                            'student_id' => 4775483,
                            'firstname' => 'Cristiano',
                            'lastname' => 'Bauer',
                            'external_id' => 4775483,
                            'username' => 'MapworksBetaUser04775483@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4775641,
                            'firstname' => 'Eleanor',
                            'lastname' => 'Brennan',
                            'external_id' => 4775641,
                            'username' => 'MapworksBetaUser04775641@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4775354,
                            'firstname' => 'Aryan',
                            'lastname' => 'Dawson',
                            'external_id' => 4775354,
                            'username' => 'MapworksBetaUser04775354@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4777077,
                            'firstname' => 'Case',
                            'lastname' => 'Bennett',
                            'external_id' => 4777077,
                            'username' => 'MapworksBetaUser04777077@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 767634,
                            'firstname' => 'Audrey',
                            'lastname' => 'Booth',
                            'external_id' => 767634,
                            'username' => 'MapworksBetaUser00767634@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 193027,
                            'firstname' => 'Kaiden',
                            'lastname' => 'Walker',
                            'external_id' => 193027,
                            'username' => 'MapworksBetaUser00193027@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Senior',
                            'profile_item_value' => 0
                        ]
                    ]
                ],
                // Example 4b:  Sorting by risk descending
                [1, [193027, 767634, 4775354, 4775483, 4775641, 4777077], null, null, null, null, null, '-risk_color', null, null,
                    [
                        [
                            'student_id' => 767634,
                            'firstname' => 'Audrey',
                            'lastname' => 'Booth',
                            'external_id' => 767634,
                            'username' => 'MapworksBetaUser00767634@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4777077,
                            'firstname' => 'Case',
                            'lastname' => 'Bennett',
                            'external_id' => 4777077,
                            'username' => 'MapworksBetaUser04777077@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4775641,
                            'firstname' => 'Eleanor',
                            'lastname' => 'Brennan',
                            'external_id' => 4775641,
                            'username' => 'MapworksBetaUser04775641@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4775354,
                            'firstname' => 'Aryan',
                            'lastname' => 'Dawson',
                            'external_id' => 4775354,
                            'username' => 'MapworksBetaUser04775354@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4775483,
                            'firstname' => 'Cristiano',
                            'lastname' => 'Bauer',
                            'external_id' => 4775483,
                            'username' => 'MapworksBetaUser04775483@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 193027,
                            'firstname' => 'Kaiden',
                            'lastname' => 'Walker',
                            'external_id' => 193027,
                            'username' => 'MapworksBetaUser00193027@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Senior',
                            'profile_item_value' => 0
                        ]
                    ]
                ],
                // Example 5a:  Sorting by class level ascending
                [1, [193027, 623363, 767634, 4775641, 4777077, 4873453], null, null, null, null, null, 'class_level', null, null,
                    [
                        [
                            'student_id' => 4777077,
                            'firstname' => 'Case',
                            'lastname' => 'Bennett',
                            'external_id' => 4777077,
                            'username' => 'MapworksBetaUser04777077@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4775641,
                            'firstname' => 'Eleanor',
                            'lastname' => 'Brennan',
                            'external_id' => 4775641,
                            'username' => 'MapworksBetaUser04775641@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 767634,
                            'firstname' => 'Audrey',
                            'lastname' => 'Booth',
                            'external_id' => 767634,
                            'username' => 'MapworksBetaUser00767634@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 623363,
                            'firstname' => 'Jon',
                            'lastname' => 'Robbins',
                            'external_id' => 623363,
                            'username' => 'MapworksBetaUser00623363@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Junior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 193027,
                            'firstname' => 'Kaiden',
                            'lastname' => 'Walker',
                            'external_id' => 193027,
                            'username' => 'MapworksBetaUser00193027@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Senior',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4873453,
                            'firstname' => 'Sonny',
                            'lastname' => 'Frank',
                            'external_id' => 4873453,
                            'username' => 'MapworksBetaUser04873453@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => null,
                            'profile_item_value' => 0
                        ]
                    ]
                ],
                // Example 5b:  Sorting by class level descending
                [1, [193027, 623363, 767634, 4775641, 4777077, 4873453], null, null, null, null, null, '-class_level', null, null,
                    [
                        [
                            'student_id' => 193027,
                            'firstname' => 'Kaiden',
                            'lastname' => 'Walker',
                            'external_id' => 193027,
                            'username' => 'MapworksBetaUser00193027@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Senior',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 623363,
                            'firstname' => 'Jon',
                            'lastname' => 'Robbins',
                            'external_id' => 623363,
                            'username' => 'MapworksBetaUser00623363@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Junior',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 767634,
                            'firstname' => 'Audrey',
                            'lastname' => 'Booth',
                            'external_id' => 767634,
                            'username' => 'MapworksBetaUser00767634@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => 'Sophomore',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4777077,
                            'firstname' => 'Case',
                            'lastname' => 'Bennett',
                            'external_id' => 4777077,
                            'username' => 'MapworksBetaUser04777077@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4775641,
                            'firstname' => 'Eleanor',
                            'lastname' => 'Brennan',
                            'external_id' => 4775641,
                            'username' => 'MapworksBetaUser04775641@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4873453,
                            'firstname' => 'Sonny',
                            'lastname' => 'Frank',
                            'external_id' => 4873453,
                            'username' => 'MapworksBetaUser04873453@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => null,
                            'profile_item_value' => 0
                        ]
                    ]
                ],
                // Example 6a:  Sorting by profile item value ascending (only 2 integer values)
                [1, [4775354, 4775641, 4776996, 4777077], null, null, null, null, null, 'profile_item_value', null, null,
                    [
                        [
                            'student_id' => 4777077,
                            'firstname' => 'Case',
                            'lastname' => 'Bennett',
                            'external_id' => 4777077,
                            'username' => 'MapworksBetaUser04777077@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4776996,
                            'firstname' => 'Brennan',
                            'lastname' => 'Duarte',
                            'external_id' => 4776996,
                            'username' => 'MapworksBetaUser04776996@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4775641,
                            'firstname' => 'Eleanor',
                            'lastname' => 'Brennan',
                            'external_id' => 4775641,
                            'username' => 'MapworksBetaUser04775641@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4775354,
                            'firstname' => 'Aryan',
                            'lastname' => 'Dawson',
                            'external_id' => 4775354,
                            'username' => 'MapworksBetaUser04775354@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 6b:  Sorting by profile item value descending (only 2 integer values)
                [1, [4775354, 4775641, 4776996, 4777077], null, null, null, null, null, '-profile_item_value', null, null,
                    [
                        [
                            'student_id' => 4775641,
                            'firstname' => 'Eleanor',
                            'lastname' => 'Brennan',
                            'external_id' => 4775641,
                            'username' => 'MapworksBetaUser04775641@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4775354,
                            'firstname' => 'Aryan',
                            'lastname' => 'Dawson',
                            'external_id' => 4775354,
                            'username' => 'MapworksBetaUser04775354@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4777077,
                            'firstname' => 'Case',
                            'lastname' => 'Bennett',
                            'external_id' => 4777077,
                            'username' => 'MapworksBetaUser04777077@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4776996,
                            'firstname' => 'Brennan',
                            'lastname' => 'Duarte',
                            'external_id' => 4776996,
                            'username' => 'MapworksBetaUser04776996@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 0
                        ]
                    ]
                ],
                // Example 7:  Sorting by profile item value ascending (decimals)
                // This example also demonstrates specifying the term for a term-specific profile item
                // (as more results would be returned if it weren't specified)
                [83, [4775354, 4775641, 4776996, 4777077], 40, 261, null, null, null, 'profile_item_value', null, null,
                    [
                        [
                            'student_id' => '4777077',
                            'firstname' => 'Case',
                            'lastname' => 'Bennett',
                            'external_id' => '4777077',
                            'username' => 'MapworksBetaUser04777077@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => '0.00',
                        ],
                        [
                            'student_id' => '4775354',
                            'firstname' => 'Aryan',
                            'lastname' => 'Dawson',
                            'external_id' => '4775354',
                            'username' => 'MapworksBetaUser04775354@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1.41,
                        ],
                        [
                            'student_id' => '4775641',
                            'firstname' => 'Eleanor',
                            'lastname' => 'Brennan',
                            'external_id' => '4775641',
                            'username' => 'MapworksBetaUser04775641@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1.70
                        ],
                        [
                            'student_id' => '4776996',
                            'firstname' => 'Brennan',
                            'lastname' => 'Duarte',
                            'external_id' => '4776996',
                            'username' => 'MapworksBetaUser04776996@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 2.36,
                        ]
                    ]
                ],
                // Example 8:  Sorting by profile item value ascending (strings)
                [3, [4775354, 4775641, 4776996, 4777077], null, null, null, null, null, 'profile_item_value', null, null,
                    [
                        [
                            'student_id' => 4776996,
                            'firstname' => 'Brennan',
                            'lastname' => 'Duarte',
                            'external_id' => 4776996,
                            'username' => 'MapworksBetaUser04776996@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 'A'
                        ],
                        [
                            'student_id' => 4777077,
                            'firstname' => 'Case',
                            'lastname' => 'Bennett',
                            'external_id' => 4777077,
                            'username' => 'MapworksBetaUser04777077@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 'R'
                        ],
                        [
                            'student_id' => 4775641,
                            'firstname' => 'Eleanor',
                            'lastname' => 'Brennan',
                            'external_id' => 4775641,
                            'username' => 'MapworksBetaUser04775641@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 'S'
                        ]
                    ]
                ],
                // Example 9:  Demonstrating secondary sorting by student last name, first name, and primary email.
                // Since the email is based on the student id in the functional testing database,
                // it's impossible to show that the last level of sorting is by email rather than person_id or external_id,
                // but this test at least demonstrates that it's deterministic.
                [1, [895800, 1089800, 4729800, 4731130, 4893800], null, null, null, null, null, 'profile_item_value', null, null,
                    [
                        [
                            'student_id' => 4729800,
                            'firstname' => 'Sara',
                            'lastname' => 'Walls',
                            'external_id' => 4729800,
                            'username' => 'MapworksBetaUser04729800@mailinator.com',
                            'risk_color' => 'green',
                            'risk_image_name' => 'risk-level-icon-g.png',
                            'class_level' => null,
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 895800,
                            'firstname' => 'Vivienne',
                            'lastname' => 'Walls',
                            'external_id' => 895800,
                            'username' => 'MapworksBetaUser00895800@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => null,
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4893800,
                            'firstname' => 'Vivienne',
                            'lastname' => 'Walls',
                            'external_id' => 4893800,
                            'username' => 'MapworksBetaUser04893800@mailinator.com',
                            'risk_color' => 'red2',
                            'risk_image_name' => 'risk-level-icon-r2.png',
                            'class_level' => null,
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 4731130,
                            'firstname' => 'Frederick',
                            'lastname' => 'Wells',
                            'external_id' => 4731130,
                            'username' => 'MapworksBetaUser04731130@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => null,
                            'profile_item_value' => 0
                        ],
                        [
                            'student_id' => 1089800,
                            'firstname' => 'Selena',
                            'lastname' => 'Walls',
                            'external_id' => 1089800,
                            'username' => 'MapworksBetaUser01089800@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => null,
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 10:  Filtering by a particular option value
                [1, [4775354, 4775483, 4775641, 4776313, 4776777, 4776996, 4777077], null, null, 1, null, null, null, null, null,
                    [
                        [
                            'student_id' => 4775641,
                            'firstname' => 'Eleanor',
                            'lastname' => 'Brennan',
                            'external_id' => 4775641,
                            'username' => 'MapworksBetaUser04775641@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4775354,
                            'firstname' => 'Aryan',
                            'lastname' => 'Dawson',
                            'external_id' => 4775354,
                            'username' => 'MapworksBetaUser04775354@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4776313,
                            'firstname' => 'Aliana',
                            'lastname' => 'Frazier',
                            'external_id' => 4776313,
                            'username' => 'MapworksBetaUser04776313@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ],
                        [
                            'student_id' => 4776777,
                            'firstname' => 'Everett',
                            'lastname' => 'Ho',
                            'external_id' => 4776777,
                            'username' => 'MapworksBetaUser04776777@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1
                        ]
                    ]
                ],
                // Example 11:  Filtering by a range of values
                [83, [4775354, 4775483, 4775641, 4776313, 4776777, 4776996, 4777077], 40, 261, null, 1.5, 2.5, null, null, null,
                    [
                        [
                            'student_id' => '4775641',
                            'firstname' => 'Eleanor',
                            'lastname' => 'Brennan',
                            'external_id' => '4775641',
                            'username' => 'MapworksBetaUser04775641@mailinator.com',
                            'risk_color' => 'yellow',
                            'risk_image_name' => 'risk-level-icon-y.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1.70
                        ],
                        [
                            'student_id' => '4776996',
                            'firstname' => 'Brennan',
                            'lastname' => 'Duarte',
                            'external_id' => '4776996',
                            'username' => 'MapworksBetaUser04776996@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 2.36
                        ],
                        [
                            'student_id' => '4776313',
                            'firstname' => 'Aliana',
                            'lastname' => 'Frazier',
                            'external_id' => '4776313',
                            'username' => 'MapworksBetaUser04776313@mailinator.com',
                            'risk_color' => 'red',
                            'risk_image_name' => 'risk-level-icon-r1.png',
                            'class_level' => '1st Year/Freshman',
                            'profile_item_value' => 1.94
                        ]
                    ]
                ],
                // Example 12a:  Year-specific profile item
                [14, [255075], 27, null, null, null, null, null, null, null,
                    [
                        [
                            'student_id' => '255075',
                            'firstname' => 'Corbin',
                            'lastname' => 'Watson',
                            'external_id' => '255075',
                            'username' => 'MapworksBetaUser00255075@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Senior',
                            'profile_item_value' => '026133'
                        ]
                    ]
                ],
                // Example 12b:  Filtering by a range of values that contains the student's value.
                [14, [255075], 27, null, null, 20000, 30000, null, null, null,
                    [
                        [
                            'student_id' => '255075',
                            'firstname' => 'Corbin',
                            'lastname' => 'Watson',
                            'external_id' => '255075',
                            'username' => 'MapworksBetaUser00255075@mailinator.com',
                            'risk_color' => null,
                            'risk_image_name' => null,
                            'class_level' => 'Senior',
                            'profile_item_value' => '026133'
                        ]
                    ]
                ],
                // Example 12c:  Filtering by a range of values that doesn't contain the student's value.
                // This example is significant because it shows:
                // (1) That only data from the given year is being considered -- this student has a value within this range for another academic year.
                // (2) That the values are being compared as numbers and not as strings -- the database field has type varchar and this student's value is actually stored as "026133".
                [14, [255075], 27, null, null, 0, 20000, null, null, null, []]
            ]
        ]);

        $this->specify("Verify that the method getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues throws appropriate exceptions", function ($ebiMetadataId, $studentIds, $orgAcademicYearId, $orgAcademicTermsId, $optionValue, $optionMin, $optionMax, $sortBy, $resultCountLimit, $offset, $expectedExceptionClass, $expectedExceptionMessage) {
            try {
                $this->personEbiMetadataRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues($ebiMetadataId, $this->classLevelEbiMetadataId, $studentIds, $orgAcademicYearId, $orgAcademicTermsId, $optionValue, $optionMin, $optionMax, $sortBy, $resultCountLimit, $offset);
            } catch (SynapseException $exception) {
                verify($exception)->isInstanceOf($expectedExceptionClass);
                verify($exception->getMessage())->equals($expectedExceptionMessage);
            }
        }, ['examples' =>
            [
                // Example 1:  If a range is provided, the endpoints must be numeric.
                // This is necessary to prevent SQL injection, and in current functionality, ranges are only used for numeric profile items.
                [14, [255075], 27, null, null, 'a', 'b', null, null, null, 'Synapse\CoreBundle\Exception\SynapseValidationException', 'Range must be numeric.']
            ]
        ]);
    }


    public function testGetStudentProfileInformation()
    {
        $this->specify('test get student profile information', function ($studentId, $datablockPermissions, $expectedResults) {
            $functionResults = $this->personEbiMetadataRepository->getStudentProfileInformation($studentId, $datablockPermissions);
            verify($expectedResults)->equals($functionResults);

        }, ['examples' => [
            [
                645740,
                [16],
                [
                    0 =>
                        [
                            'ebi_metadata_id' => '60',
                            'metadata_type' => 'S',
                            'datablock_desc' => 'Demographic Record',
                            'meta_name' => 'AthleteStudent',
                            'metadata_value' => '0',
                            'year_name' => null,
                            'term_name' => null,
                            'scope' => 'N'
                        ],
                    1 =>
                        [
                            'ebi_metadata_id' => '79',
                            'metadata_type' => 'S',
                            'datablock_desc' => 'Demographic Record',
                            'meta_name' => 'CampusResident',
                            'metadata_value' => '0',
                            'year_name' => '2015-16 Academic Year',
                            'term_name' => 'Spring Semester 2016',
                            'scope' => 'T'
                        ],
                    2 =>
                        [
                            'ebi_metadata_id' => '79',
                            'metadata_type' => 'S',
                            'datablock_desc' => 'Demographic Record',
                            'meta_name' => 'CampusResident',
                            'metadata_value' => '0',
                            'year_name' => '2015-16 Academic Year',
                            'term_name' => 'Fall Semester 2015',
                            'scope' => 'T'
                        ]
                ]
            ],
            [
                678392,
                [16],
                [
                    0 =>
                        [
                            'ebi_metadata_id' => '60',
                            'metadata_type' => 'S',
                            'datablock_desc' => 'Demographic Record',
                            'meta_name' => 'AthleteStudent',
                            'metadata_value' => '0',
                            'year_name' => null,
                            'term_name' => null,
                            'scope' => 'N'
                        ],
                    1 =>
                        [
                            'ebi_metadata_id' => '79',
                            'metadata_type' => 'S',
                            'datablock_desc' => 'Demographic Record',
                            'meta_name' => 'CampusResident',
                            'metadata_value' => '0',
                            'year_name' => '2015-2016 Academic Year',
                            'term_name' => 'Summer Semester',
                            'scope' => 'T'
                        ],
                    2 =>
                        [
                            'ebi_metadata_id' => '79',
                            'metadata_type' => 'S',
                            'datablock_desc' => 'Demographic Record',
                            'meta_name' => 'CampusResident',
                            'metadata_value' => '0',
                            'year_name' => '2015-2016 Academic Year',
                            'term_name' => 'Spring Semester',
                            'scope' => 'T'
                        ],
                    3 =>
                        [
                            'ebi_metadata_id' => '79',
                            'metadata_type' => 'S',
                            'datablock_desc' => 'Demographic Record',
                            'meta_name' => 'CampusResident',
                            'metadata_value' => '0',
                            'year_name' => '2015-2016 Academic Year',
                            'term_name' => 'Fall Semester',
                            'scope' => 'T'
                        ]
                ]
            ],
            [
                4618153,
                [16],
                [
                    0 =>
                        [
                            'ebi_metadata_id' => '60',
                            'metadata_type' => 'S',
                            'datablock_desc' => 'Demographic Record',
                            'meta_name' => 'AthleteStudent',
                            'metadata_value' => '0',
                            'year_name' => null,
                            'term_name' => null,
                            'scope' => 'N'
                        ],
                    1 =>
                        [
                            'ebi_metadata_id' => '79',
                            'metadata_type' => 'S',
                            'datablock_desc' => 'Demographic Record',
                            'meta_name' => 'CampusResident',
                            'metadata_value' => '1',
                            'year_name' => '2015-16 Academic Year',
                            'term_name' => 'Spring Semester 2016',
                            'scope' => 'T'
                        ],
                    2 =>
                        [
                            'ebi_metadata_id' => '79',
                            'metadata_type' => 'S',
                            'datablock_desc' => 'Demographic Record',
                            'meta_name' => 'CampusResident',
                            'metadata_value' => '1',
                            'year_name' => '2015-16 Academic Year',
                            'term_name' => 'Fall Semester 2015',
                            'scope' => 'T'
                        ]
                ]

            ]
        ]]);
    }

    public function testGetRetentionDataForProfileBlock()
    {
        $this->specify("Verify the functionality of the method getRetentionDataForProfileBlock", function ($count, $profileDataId, $organizationId, $loggedInUserId, $retentionTrackingYearId, $orgAcademicTermId, $orgAcademicYearId, $selectCaseQuery, $studentIdsToInclude, $expectedResults) {
            $results = $this->personEbiMetadataRepository->getRetentionDataForProfileBlock($profileDataId, $organizationId, $loggedInUserId, $retentionTrackingYearId, $orgAcademicTermId, $orgAcademicYearId, $selectCaseQuery, $studentIdsToInclude);
            $testArray = [];
            foreach ($results as $key => $resultData) {
                unset($resultData['participant_id']); // can’t test RAND()
                $testArray[$key] = $resultData;
            }
            $testArray = array_slice($testArray, 0, 6);
            verify($testArray)->equals($expectedResults);
            verify(count($results))->equals($count);

        }, ["examples" =>
            [
                [ // Example 1 test without filter student id (having term and year id)
                    32, //count
                    83, // profile data id
                    59, // organization id
                    53573, // logged in user id
                    201516, // retention tracking year id
                    213, // org academic term id
                    94, // org academic year id
                    [
                        'case_query' => "CASE
                            WHEN pemfilter.metadata_value BETWEEN (:subpopulation1_min_digits) AND (:subpopulation1_max_digits) THEN 1
                            WHEN pemfilter.metadata_value BETWEEN (:subpopulation2_min_digits) AND (:subpopulation2_max_digits) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_min_digits' => 0,
                            'subpopulation1_max_digits' => 2,
                            'subpopulation2_min_digits' => 2.1,
                            'subpopulation2_max_digits' => 3
                        ]
                    ],
                    [ // student ids to include

                    ],
                    [ // expected results
                        [
                            "subpopulation_id" => "1",
                            "organization_id" => "59",
                            "person_id" => "4617739",
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
                            "person_id" => "4617739",
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
                            "person_id" => "4617739",
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
                            "person_id" => "4617739",
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
                            "person_id" => "4617739",
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
                            "person_id" => "4617739",
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
                [ // Example 2 test with empty data
                    0,
                    3,
                    6,
                    678,
                    201516,
                    null,
                    null,
                    [
                        'case_query' => "CASE
                            WHEN pemfilter.metadata_value BETWEEN (:subpopulation1_min_digits) AND (:subpopulation1_max_digits) THEN 1
                            WHEN pemfilter.metadata_value BETWEEN (:subpopulation2_min_digits) AND (:subpopulation2_max_digits) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_min_digits' => 0,
                            'subpopulation1_max_digits' => 2,
                            'subpopulation2_min_digits' => 2.1,
                            'subpopulation2_max_digits' => 3
                        ]
                    ],
                    [

                    ],
                    []
                ],
                [ // Example 3 with filtered students
                    16,
                    83,
                    59,
                    53573,
                    201516,
                    213,
                    94,
                    [
                        'case_query' => "CASE
                            WHEN pemfilter.metadata_value BETWEEN (:subpopulation1_min_digits) AND (:subpopulation1_max_digits) THEN 1
                            WHEN pemfilter.metadata_value BETWEEN (:subpopulation2_min_digits) AND (:subpopulation2_max_digits) THEN 2
                            END",
                        'parameters' => [
                            'subpopulation1_min_digits' => 0,
                            'subpopulation1_max_digits' => 2,
                            'subpopulation2_min_digits' => 2.1,
                            'subpopulation2_max_digits' => 3
                        ]
                    ],
                    [
                        4618245, 201718, 4617739
                    ],
                    [
                        [
                            "subpopulation_id" => "1",
                            "organization_id" => "59",
                            "person_id" => "4617739",
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
                            "person_id" => "4617739",
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
                            "person_id" => "4617739",
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
                            "person_id" => "4617739",
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
                            "person_id" => "4617739",
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
                            "person_id" => "4617739",
                            "retention_tracking_year" => "201516",
                            "year_id" => "201718",
                            "year_name" => "2017-18 Academic Year",
                            "retention_completion_variable_name" => "Retained to Midyear Year 3",
                            "years_from_retention_track" => "2",
                            "retention_completion_value" => "1",
                            "retention_completion_variable_order" => "2"
                        ]
                    ]
                ]
            ]

        ]);
    }

    public function testGetStudentIdsListBasedOnProfileItemSelection()
    {
        $this->specify("Verify the functionality of the method getStudentIdsListBasedOnProfileItemSelection", function ($organizationId, $ebiMetadataId, $whereClause, $expectedCount, $expectedResults) {
            $results = $this->personEbiMetadataRepository->getStudentIdsListBasedOnProfileItemSelection($organizationId, $ebiMetadataId, $whereClause);
            verify($expectedCount)->equals(count($results));
            $testArray = array_slice($results, 0, 11);
            verify($testArray)->equals($expectedResults);
        }, ["examples" =>
            [
                [ // Example 1 with category type profile data
                    189,
                    78,
                    [
                        "where_query" => "pem.metadata_value IN (:category_values)",
                        "parameters" => [
                            "category_values" => [
                                "0",
                                "1",
                                "2"
                            ]
                        ]
                    ],
                    424,
                    [
                        "4672299",
                        "4672300",
                        "4672373",
                        "4672374",
                        "4672375",
                        "4672376",
                        "4672377",
                        "4672378",
                        "4672379",
                        "4672380",
                        "4672381"
                    ]
                ],
                [ // Example 2 with numeric type profile data
                    189,
                    78,
                    [
                        "where_query" => "((pem.metadata_value BETWEEN :subpopulation1_min_digits AND :subpopulation1_max_digits) OR pem.metadata_value IN (:subpopulation2_single_values))",
                        "parameters" => [
                            "subpopulation1_min_digits" => "0",
                            "subpopulation1_max_digits" => "1",
                            "subpopulation2_single_values" => "2"
                        ]
                    ],
                    424,
                    [
                        "4672299",
                        "4672300",
                        "4672373",
                        "4672374",
                        "4672375",
                        "4672376",
                        "4672377",
                        "4672378",
                        "4672379",
                        "4672380",
                        "4672381"
                    ]

                ],
                [ // Example 3 with date type profile data
                    189,
                    78,
                    [
                        "where_query" => "(((pem.metadata_value BETWEEN STR_TO_DATE(:subpopulation1_start_date , ' %m/%d/%Y') AND STR_TO_DATE(:subpopulation1_end_date , ' %m/%d/%Y')) OR (pem.metadata_value BETWEEN STR_TO_DATE(:subpopulation1_start_date , ' %m/%d/%Y') AND STR_TO_DATE(:subpopulation1_end_date , ' %m/%d/%Y'))) OR ((pem.metadata_value BETWEEN STR_TO_DATE(:subpopulation2_start_date , '%Y-%m-%d') AND STR_TO_DATE(:subpopulation2_end_date , '%Y-%m-%d')) OR (pem.metadata_value BETWEEN STR_TO_DATE(:subpopulation2_start_date , '%Y-%m-%d') AND STR_TO_DATE(:subpopulation2_end_date , '%Y-%m-%d'))))",
                        "parameters" => [
                            "subpopulation1_start_date" => "01/01/2016",
                            "subpopulation1_end_date" => "31/12/2016",
                            "subpopulation2_start_date" => "01/01/2017",
                            "subpopulation2_end_date" => "31/12/2017"
                        ]
                    ],
                    0,
                    [

                    ]
                ],
                [ // Example 4 with where clause empty
                    189,
                    78,
                    [],
                    424,
                    [
                        "4672299",
                        "4672300",
                        "4672373",
                        "4672374",
                        "4672375",
                        "4672376",
                        "4672377",
                        "4672378",
                        "4672379",
                        "4672380",
                        "4672381"
                    ]
                ]

            ]

        ]);
    }

}