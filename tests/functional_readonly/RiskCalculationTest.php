<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Synapse\CoreBundle\SynapseConstant;


class RiskCalculationTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Container
     */
    private $container;

    /**
     * @var EntityManager
     */
    private $em;

    public function _before()
    {

        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->em = $this->container->get(SynapseConstant::DOCTRINE_CLASS_KEY)->getManager();
    }


    public function testRiskCalculationGap()
    {
        $this->specify("Verify there is not gap between risk indicators", function ($studentId, $riskGroupId, $riskModelName, $riskBVariable) {
            //Risk Group Exists?
            $sql = "SELECT 1 FROM risk_group WHERE id = $riskGroupId AND deleted_at is null;";
            $results = $this->em->getConnection()->executeQuery($sql)->fetchAll();
            $doesRiskGroupExist = isset($results);
            $this->assertTrue($doesRiskGroupExist);

            //Risk Model Exists?
            $sql = "SELECT 1 FROM risk_model_master WHERE `name` = '$riskModelName' AND deleted_at is null;";
            $results = $this->em->getConnection()->executeQuery($sql)->fetchAll();
            $doesRiskModelExist = isset($results);
            $this->assertTrue($doesRiskModelExist);

            //Risk Variable Exists?
            $sql = "SELECT 1 FROM risk_variable WHERE id = 240 and risk_b_variable = '$riskBVariable'";
            $results = $this->em->getConnection()->executeQuery($sql)->fetchAll();
            $doesRiskVariableExist = isset($results);
            $this->assertTrue($doesRiskVariableExist);

            //Students Assigned to Risk Group?
            $sql = "SELECT 1 FROM risk_group_person_history WHERE person_id = $studentId AND risk_group_id = $riskGroupId";
            $results = $this->em->getConnection()->executeQuery($sql)->fetchAll();
            $doesRiskVariableExist = isset($results);
            $this->assertTrue($doesRiskVariableExist);

            $sql = "SELECT risk_score, risk_level FROM person_risk_level_calc WHERE person_id = $studentId";
            $results = $this->em->getConnection()->executeQuery($sql)->fetchAll();
            $isResultSet = isset($results);
            $this->assertTrue($isResultSet);
            //Since the indicators overlap, indicator 3 max = 4.0000 and indicator 4 min = 4.0000
            //If the ranges are non-inclusive on max as changed, only one result should be
            //returned. If both min and max are inclusive, two errants results will return
            $resultCount = count($results);
            $this->assertEquals(1, $resultCount);
            if ($isResultSet) {
                $riskScore = $results[0]['risk_score'];
                $riskLevel = $results[0]['risk_level'];
                $this->assertEquals(4, $riskLevel);
                $this->assertEquals(4.0000, $riskScore);

            };

        },
            ["examples" => [
                [
                    4892330,
                    9,
                    'TestingChasm',
                    'Practice-OnCampus'
                ]
            ]
            ]);
    }

    public function testRiskVariableGap()
    {
        $this->specify("Verify the there is no gap in Risk Variables", function ($studentId, $riskGroupId, $riskModelName, $riskBVariableName) {
            //Risk Group Exists?
            $sql = "SELECT 1 FROM risk_group WHERE id = $riskGroupId AND deleted_at is null;";
            $results = $this->em->getConnection()->executeQuery($sql)->fetchAll();
            $doesRiskGroupExist = isset($results);
            $this->assertTrue($doesRiskGroupExist);

            //Risk Model Exists?
            $sql = "SELECT 1 FROM risk_model_master WHERE `name` = '$riskModelName' AND deleted_at is null;";
            $results = $this->em->getConnection()->executeQuery($sql)->fetchAll();
            $doesRiskModelExist = isset($results);
            $this->assertTrue($doesRiskModelExist);

            //Risk Variable Exists?
            $sql = "SELECT 1 FROM risk_variable WHERE risk_b_variable = '$riskBVariableName'";
            $results = $this->em->getConnection()->executeQuery($sql)->fetchAll();
            $doesRiskVariableExist = isset($results);
            $this->assertTrue($doesRiskVariableExist);

            //Students Assigned to Risk Group?
            $sql = "SELECT 1 FROM risk_group_person_history WHERE person_id = $studentId AND risk_group_id = $riskGroupId";
            $results = $this->em->getConnection()->executeQuery($sql)->fetchAll();
            $doesRiskVariableExist = isset($results);
            $this->assertTrue($doesRiskVariableExist);

            $sql = "SELECT risk_variable_id, weight, calculated_value, bucket_value  FROM org_calculated_risk_variables_view
                WHERE risk_variable_id = 241 AND person_id = $studentId";
            $results = $this->em->getConnection()->executeQuery($sql)->fetchAll();
            $isResultSet = isset($results);
            $this->assertTrue($isResultSet);
            $resultCount = count($results);

            //Since the buckets overlap, bucket 3 max = 2.9999 and bucket 4 min = 2.9999
            //If the ranges are non-inclusive on max as changed, only one result should be
            //returned. If both min and max are inclusive, 2.99994444 should
            // create two errants results when rounded by the previous org_calculated_risk_variables_view
            $this->assertEquals(1, $resultCount);
            $foundRiskBVariable = false;
            foreach ($results as $result){
                if ($result['risk_variable_id'] == 241){
                    $weight = $result['weight'];
                    $calculatedValue = $result['calculated_value'];
                    $bucketValue = $result['bucket_value'];
                    $this->assertEquals(3.11, $weight);
                    $this->assertEquals(2.999944444, $calculatedValue);
                    $this->assertEquals(4, $bucketValue);
                    $foundRiskBVariable = true;
                }
            }

            $this->assertTrue($foundRiskBVariable);




        },
            ["examples" => [
                // Verify that a response that feeds a continuous variable is included in a bucket if it's equal to the minimum.
                // Verify that a response that DOES NOT feed a continuous variable is included in a bucket if it's equal to the maximum.
                // Verify that a response that feeds a continuous variable is included in a bucket if it rounds to the minimum
                [
                    4892330,
                    9,
                    'TestingChasm',
                    'TestGPA'
                ]
            ]
            ]);
    }

    public function testRiskSoftDeletionVariablesAndMetadata()
    {
        $this->specify("Verify soft deleted variables or metadata items are not picked up in risk", function ($studentId, $expectedResult, $containsNoSoftDeletion) {

            $sql = "SELECT * FROM org_calculated_risk_variables_view
                WHERE person_id = $studentId";
            $results = $this->em->getConnection()->executeQuery($sql)->fetchAll();


            //Contains Only a single risk variable
            if ($containsNoSoftDeletion) {
                verify($results)->equals($expectedResult);
            } else {
                verify($results)->notContains($expectedResult);
            }


        },
            ["examples" => [
                //Soft Deleted Profile Item (ebi_metdata)
                [
                    357968,
                    [
                        "org_id" => 153,
                        "risk_group_id" => 2,
                        "person_id" => 357968,
                        "risk_variable_id" => 125,
                        "risk_model_id" => 37,
                        "source" => 'profile',
                        "variable_type" => 'continuous',
                        "weight" => 800.0000,
                        "calculated_value" => 2.87,
                        "bucket_value" => 4
                    ],
                    false

                ],
                //Soft Deleted Individual Profile item (person_ebi_metadata)
                [4621280,
                    [
                        "org_id" => 19,
                        "risk_group_id" => 1,
                        "person_id" => 4621280,
                        "risk_variable_id" => 69,
                        "risk_model_id" => 16,
                        "source" => 'profile',
                        "variable_type" => 'continuous',
                        "weight" => 423.3300,
                        "calculated_value" => 20,
                        "bucket_value" => 3
                    ],
                    false
                ],
                //Soft Deleted ISP (org_metadata)
                [
                    4631636,
                    [
                        "org_id" => 92,
                        "risk_group_id" => 1,
                        "person_id" => 4631636,
                        "risk_variable_id" => 138,
                        "risk_model_id" => 45,
                        "source" => 'isp',
                        "variable_type" => 'categorical',
                        "weight" => 1290.0000,
                        "calculated_value" => 5,
                        "bucket_value" => 6
                    ],
                    false
                ],
                //Soft Deleted individual ISP (person_org_metadata)
                [
                    4631636,
                    [
                        "org_id" => 92,
                        "risk_group_id" => 1,
                        "person_id" => 4631636,
                        "risk_variable_id" => 139,
                        "risk_model_id" => 45,
                        "source" => 'isp',
                        "variable_type" => 'continuous',
                        "weight" => 665.5000,
                        "calculated_value" => 15.000,
                        "bucket_value" => 3
                    ],
                    false
                ],
                //Soft Deleted Survey Question (survey_questions)
                [
                    4621280,
                    [
                        "org_id" => 19,
                        "risk_group_id" => 1,
                        "person_id" => 4621280,
                        "risk_variable_id" => 81,
                        "risk_model_id" => 16,
                        "source" => 'surveyquestion',
                        "variable_type" => 'categorical',
                        "weight" => 571.0000,
                        "calculated_value" => 1.00,
                        "bucket_value" => 5
                    ],
                    false
                ],
                //Soft Deleted Individual Survey Questions (survey_response)
                [
                    4785557,
                    [
                        "org_id" => 126,
                        "risk_group_id" => 1,
                        "person_id" => 4785557,
                        "risk_variable_id" => 84,
                        "risk_model_id" => 5,
                        "source" => 'surveyquestion',
                        "variable_type" => 'categorical',
                        "weight" => 399.0000,
                        "calculated_value" => 1.00,
                        "bucket_value" => 3
                    ],
                    false
                ],
                //Soft Deleted ISQ (org_question)
                [
                    4621280,
                    [
                        "org_id" => 19,
                        "risk_group_id" => 1,
                        "person_id" => 4621280,
                        "risk_variable_id" => 244,
                        "risk_model_id" => 16,
                        "source" => 'isq',
                        "variable_type" => 'categorical',
                        "weight" => 1294.0000,
                        "calculated_value" => 2.00,
                        "bucket_value" => 2
                    ],
                    false
                ],
                //Soft Deletion on Individual ISQ (org_question_response)
                [
                    4621280,
                    [
                        "org_id" => 19,
                        "risk_group_id" => 1,
                        "person_id" => 4621280,
                        "risk_variable_id" => 243,
                        "risk_model_id" => 16,
                        "source" => 'isq',
                        "variable_type" => 'categorical',
                        "weight" => 1294.0000,
                        "calculated_value" => 1.00,
                        "bucket_value" => 1
                    ],
                    false
                ],
                //Soft Deletion on Risk Variable Category
                [4621280,
                    [
                        "org_id" => 19,
                        "risk_group_id" => 1,
                        "person_id" => 4621280,
                        "risk_variable_id" => 73,
                        "risk_model_id" => 16,
                        "source" => 'profile',
                        "variable_type" => 'categorical',
                        "weight" => 854.3300,
                        "calculated_value" => 1,
                        "bucket_value" => 5
                    ],
                    false
                ],
                //Soft Deletion on Risk Variable
                [
                4621284,
                    [
                        "org_id" => 19,
                        "risk_group_id" => 1,
                        "person_id" => 4621284,
                        "risk_variable_id" => 242,
                        "risk_model_id" => 16,
                        "source" => 'isq',
                        "variable_type" => 'categorical',
                        "weight" => 1294.0000,
                        "calculated_value" => 1.00,
                        "bucket_value" => 1
                    ],
                    false
                ],
                //Soft Deletion on Academic Update
                [4621280,
                    [
                        "org_id" => 19,
                        "risk_group_id" => 1,
                        "person_id" => 4621280,
                        "risk_variable_id" => 88,
                        "risk_model_id" => 16,
                        "source" => 'profile',
                        "variable_type" => 'continuous',
                        "weight" => 1294.0000,
                        "calculated_value" => 1,
                        "bucket_value" => 3
                    ],
                    false
                ],
                //Soft Deletion of Risk Flag.  No Calculation Generated, matching instead of excluding
                [
                    1197510,
                    [],
                    true
                ],
                //None Soft Deletion Case isn't empty, Student Has Two Risk Variable that should be present, so result will be not empty
                [
                    4892330,
                    [
                        [
                            "org_id" => 134,
                            "risk_group_id" => 9,
                            "person_id" => 4892330,
                            "risk_variable_id" => 240,
                            "risk_model_id" => 50,
                            "source" => 'profile',
                            "variable_type" => 'categorical',
                            "weight" => 1.9999,
                            "calculated_value" => 1.00,
                            "bucket_value" => 4
                        ],
                        [
                            "org_id" => 134,
                            "risk_group_id" => 9,
                            "person_id" => 4892330,
                            "risk_variable_id" => 241,
                            "risk_model_id" => 50,
                            "source" => 'profile',
                            "variable_type" => 'continuous',
                            "weight" => 3.1100,
                            "calculated_value" => 2.999944444,
                            "bucket_value" => 4
                        ]
                    ],
                    true
                ],
                //Soft Deletion on Factors
                [
                    4865999,
                    [
                        "org_id" => 201,
                        "risk_group_id" => 1,
                        "person_id" => 4865999,
                        "risk_variable_id" => 100,
                        "risk_model_id" => 47,
                        "source" => 'surveyfactor',
                        "variable_type" => 'continuous',
                        "weight" => 360.0000,
                        "calculated_value" => 6.6667,
                        "bucket_value" => 6
                    ],
                    false
                ],
                //Soft Delete Person_Factor_Calculated

                [
                    4865999,
                    [
                        "org_id" => 201,
                        "risk_group_id" => 1,
                        "person_id" => 4865999,
                        "risk_variable_id" => 101,
                        "risk_model_id" => 47,
                        "source" => 'surveyfactor',
                        "variable_type" => 'continuous',
                        "weight" => 119.0000,
                        "calculated_value" => 6.0000,
                        "bucket_value" => 6
                    ],
                    false
                ]
            ]
            ]);
    }


    public function testRiskSoftDeletionModelsAndAssociations()
    {
        $this->specify("Verify soft deleted model and model associations are not picked up in risk", function ($studentId, $expectedResult) {

            $sql = "SELECT * FROM org_calculated_risk_variables_view
                WHERE person_id = $studentId";
            $results = $this->em->getConnection()->executeQuery($sql)->fetchAll();

            verify($results)->equals($expectedResult);

        },
            ["examples" => [
                //Risk Model Is Soft Deleted
                [
                    4743940,
                    []
                ],
                //Risk Group-Model-Org Association Is Soft Deleted
                [
                    4866412,
                    []
                ],
                //An association exists for given parameter with no soft deletion
                [
                    4631636,
                    [
                        [
                            "org_id" => 92,
                            "risk_group_id" => 1,
                            "person_id" => 4631636,
                            "risk_variable_id" => 74,
                            "risk_model_id" => 45,
                            "source" => 'profile',
                            "variable_type" => 'categorical',
                            "weight" => 400.0000,
                            "calculated_value" => 0,
                            "bucket_value" => 3
                        ],
                        [
                            "org_id" => 92,
                            "risk_group_id" => 1,
                            "person_id" => 4631636,
                            "risk_variable_id" => 187,
                            "risk_model_id" => 45,
                            "source" => 'profile',
                            "variable_type" => 'continuous',
                            "weight" => 1500.0000,
                            "calculated_value" => 4,
                            "bucket_value" => 7
                        ],
                        [
                            "org_id" => 92,
                            "risk_group_id" => 1,
                            "person_id" => 4631636,
                            "risk_variable_id" => 203,
                            "risk_model_id" => 45,
                            "source" => 'profile',
                            "variable_type" => 'continuous',
                            "weight" => 2500.0000,
                            "calculated_value" => 4,
                            "bucket_value" => 7
                        ]
                    ]
                ],
            ]
            ]);
    }


    public function testRiskSoftDeletionWithADeletedRiskLevel()
    {
        $this->specify("Verify soft deleted risk levels are not picked up in risk", function ($studentId, $expectedResult) {

            $sql = "SELECT * FROM person_risk_level_calc
                WHERE person_id = $studentId";
            $results = $this->em->getConnection()->executeQuery($sql)->fetchAll();
            $this->assertEquals($results, $expectedResult);

        },
            ["examples" => [
                //Risk Level is soft deleted
                [
                    264310,
                    [
                       [
                       'org_id' => '134',
                        'person_id' => '264310',
                        'risk_group_id' => '9',
                        'risk_model_id' => '50',
                        'weighted_value' => '29.769600000',
                        'maximum_weight_value' => '5.109900000',
                        'risk_score' => '5.8258674338050',
                       'risk_level' => '7',
                        'risk_text' => null,
                        'image_name' => null,
                        'color_hex' => null
                       ]
                    ]
                ],
                //Risk Level model association is soft deleted
                [
                    4866012,
                    [
                        [
                            'org_id' => '201',
                            'person_id' => '4866012',
                            'risk_group_id' => '1',
                            'risk_model_id' => '47',
                            'weighted_value' => '22010.000000000',
                            'maximum_weight_value' => '5476.000000000',
                            'risk_score' => '4.0193571950329',
                            'risk_level' => null,
                            'risk_text' => null,
                            'image_name' => null,
                            'color_hex' => null
                        ]
                    ]
                ],
                //Risk Levels are displayed
                [
                    4892330,
                    [
                        [
                            'org_id' => '134',
                            'person_id' => '4892330',
                            'risk_group_id' => '9',
                            'risk_model_id' => '50',
                            'weighted_value' => '20.439600000',
                            'maximum_weight_value' => '5.109900000',
                            'risk_score' => '4.0000000000',
                            'risk_level' => '4',
                            'risk_text' => 'green',
                            'image_name' => 'risk-level-icon-g.png',
                            'color_hex' => '#95cd3c'
                        ]
                    ]
                ]
            ]
            ]);
    }


    public function testValidRiskVariablesAndMetadata()
    {
        $this->specify("Verify the Risk Variable Calculation", function ($studentId, $exactMatch = false, $matchingCase = true, $expectedResult = null) {

            $matchingSQL = "SELECT * FROM org_calculated_risk_variables_view WHERE person_id = $studentId";
            $nonMatchingSQL = "SELECT * FROM cur_org_aggregationcalc_risk_variable WHERE person_id = $studentId";

            if ($matchingCase) {
                $sql = $matchingSQL;
            } else {
                $sql = $nonMatchingSQL;
            }

            $results = $this->em->getConnection()->executeQuery($sql)->fetchAll();

            if ($exactMatch){
                $this->assertEquals($expectedResult, $results);
            } else {
                verify($results)->contains($expectedResult);
            }

        },
            ["examples" => [
                //Student With No Risk Variables
                [
                    1216857,
                    true,
                    false,
                    []
                ],

                //ACADEMIC UPDATES RISK VARIABLES (academic_update table)
                // Verify that an academic update with high risk level contributes 1 to the academic update risk variable.
                [
                    4778751,
                    false,
                    true,
                    [
                        'org_id' => '109',
                        'risk_group_id' => '1',
                        'person_id' => '4778751',
                        'risk_variable_id' => '88',
                        'risk_model_id' => '5',
                        'source' => 'profile',
                        'variable_type' => 'continuous',
                        'weight' => '1812.0000',
                        'calculated_value' => '1',
                        'bucket_value' => '3'
                    ]
                ],
                // Verify that an academic update with low risk level contributes 0 to the academic update risk variable.
                [
                    4774026,
                    false,
                    false,
                    [
                        'org_id' => '109',
                        'risk_group_id' => '1',
                        'person_id' => '4774026',
                        'risk_variable_id' => '87',
                        'risk_model_id' => '5',
                        'source' => 'profile',
                        'variable_type' => 'continuous',
                        'weight' => '1000.0000',
                        'calculated_value' => '0',
                        'calc_type' => 'Academic Update'
                    ]
                ],
                // Verify that an academic update with a failing grade (D) contributes 1 to the academic update risk variable.
                [
                    4774142,
                    false,
                    true,
                    [
                        'org_id' => '109',
                        'risk_group_id' => '1',
                        'person_id' => '4774142',
                        'risk_variable_id' => '88',
                        'risk_model_id' => '5',
                        'source' => 'profile',
                        'variable_type' => 'continuous',
                        'weight' => '1812.0000',
                        'calculated_value' => '1',
                        'bucket_value' => '3'
                    ]
                ],
                // Verify that an academic update with a passing grade contributes 0 to the academic update risk variable.
                [
                    4627626,
                    false,
                    false,
                    [
                        'org_id' => '196',
                        'risk_group_id' => '1',
                        'person_id' => '4627626',
                        'risk_variable_id' => '87',
                        'risk_model_id' => '5',
                        'source' => 'profile',
                        'variable_type' => 'continuous',
                        'weight' => '1000.0000',
                        'calculated_value' => '0',
                        'calc_type' => 'Academic Update'
                    ]
                ],
                // Verify that an academic update risk variable returns 2 when a student has failing academic updates in two courses.
                [
                    4802549,
                    false,
                    true,
                    [
                        'org_id' => '140',
                        'risk_group_id' => '1',
                        'person_id' => '4802549',
                        'risk_variable_id' => '87',
                        'risk_model_id' => '5',
                        'source' => 'profile',
                        'variable_type' => 'continuous',
                        'weight' => '1000.0000',
                        'calculated_value' => '2',
                        'bucket_value' => '2'
                    ]
                ],
                // Verify that if there are two academic updates in the same course, the latest is the one that is counted.
                [
                    4802419,
                    false,
                    true,
                    [
                        'org_id' => '140',
                        'risk_group_id' => '1',
                        'person_id' => '4802419',
                        'risk_variable_id' => '88',
                        'risk_model_id' => '5',
                        'source' => 'profile',
                        'variable_type' => 'continuous',
                        'weight' => '1812.0000',
                        'calculated_value' => '1',
                        'bucket_value' => '3'
                    ]
                ],
                // Verify that an academic update made(modified_at) before the start date is not counted.
                [
                    978373,
                    false,
                    false,
                    [
                        'org_id' => '85',
                        'risk_group_id' => '1',
                        'person_id' => '978373',
                        'risk_variable_id' => '160',
                        'risk_model_id' => '5',
                        'source' => 'profile',
                        'variable_type' => 'continuous',
                        'weight' => '3000.0000',
                        'calculated_value' => '0',
                        'calc_type' => 'Academic Update'
                    ]
                ],
                // Verify that an academic update made(modified_at) after the end date is not counted
                [
                    4802297,
                    false,
                    false,
                    [
                        'org_id' => '140',
                        'risk_group_id' => '1',
                        'person_id' => '4802297',
                        'risk_variable_id' => '87',
                        'risk_model_id' => '5',
                        'source' => 'profile',
                        'variable_type' => 'continuous',
                        'weight' => '1000.0000',
                        'calculated_value' => '0',
                        'calc_type' => 'Academic Update'
                    ]
                ],
                // Verify that an academic update is counted correctly when it has values for both risk level and grade.
                [
                    4774142,
                    false,
                    true,
                    [
                        'org_id' => '109',
                        'risk_group_id' => '1',
                        'person_id' => '4774142',
                        'risk_variable_id' => '88',
                        'risk_model_id' => '5',
                        'source' => 'profile',
                        'variable_type' => 'continuous',
                        'weight' => '1812.0000',
                        'calculated_value' => '1',
                        'bucket_value' => '3'
                    ]
                ],


                //STANDARD RISK VARIABLES
                // Verify that a categorical variable works correctly when there are multiple options in a bucket.
                [
                    219375,
                    false,
                    true,
                    [
                        "org_id" => '134',
                        "risk_group_id" => '1',
                        "person_id" => '219375',
                        "risk_variable_id" => '80',
                        "risk_model_id" => '5',
                        "source" => 'surveyquestion',
                        "variable_type" => 'categorical',
                        "weight" => '1209.5000',
                        "calculated_value" => '1.00',
                        "bucket_value" => '4'
                    ]
                ],
                // Verify that a continuous variables works correctly
                [
                    4631636,
                    false,
                    true,
                    [
                        "org_id" => '92',
                        "risk_group_id" => '1',
                        "person_id" => '4631636',
                        "risk_variable_id" => '203',
                        "risk_model_id" => '45',
                        "source" => 'profile',
                        "variable_type" => 'continuous',
                        "weight" => '2500.0000',
                        "calculated_value" => '4',
                        "bucket_value" => '7'
                    ]
                ],
                // Verify that if the response doesn't fall into any bucket, then the variable is not used.
                    // Step 1: Verify that person has a valid risk variable with data.
                   [
                       4621281,
                       false,
                       false,
                       [
                           "org_id" => '19',
                           "risk_group_id" => '1',
                           "person_id" => '4621281',
                           "risk_variable_id" => '31',
                           "risk_model_id" => '16',
                           "source" => 'isq',
                           "variable_type" => 'categorical',
                           "weight" => '1294.0000',
                           "calculated_value" => null,
                           "calc_type" => null
                       ]
                   ],
                    //Step 2: Verify that this risk variable does not show up because the calculated_value falls outside of bucket range.
                   [
                       4621281,
                       true,
                       true,
                       [
                           [
                               "org_id" => '19',
                               "risk_group_id" => '1',
                               "person_id" => '4621281',
                               "risk_variable_id" => '69',
                               "risk_model_id" => '16',
                               "source" => 'profile',
                               "variable_type" => 'continuous',
                               "weight" => '423.3300',
                               "calculated_value" => '28',
                               "bucket_value" => '6'
                           ],
                           [
                               "org_id" => '19',
                               "risk_group_id" => '1',
                               "person_id" => '4621281',
                               "risk_variable_id" => '74',
                               "risk_model_id" => '16',
                               "source" => 'profile',
                               "variable_type" => 'categorical',
                               "weight" => '400.0000',
                               "calculated_value" => '0',
                               "bucket_value" => '3'
                           ]
                       ]

                   ],

                // Verify that values for a risk variable based on a survey factor are included correctly.(is_calculated=0, and source = surveyfactor)
                [
                    4759591,
                    false,
                    true,
                    [
                        "org_id" => '163',
                        "risk_group_id" => '1',
                        "person_id" => '4759591',
                        "risk_variable_id" => '97',
                        "risk_model_id" => '14',
                        "source" => 'surveyfactor',
                        "variable_type" => 'continuous',
                        "weight" => '1107.5000',
                        "calculated_value" => '5.6667',
                        "bucket_value" => '5'
                    ]
                ],
                // Verify that values for a risk variable based on an ISP are included correctly.(is_calculated=0, and source = isp)
                // NO DATA AVAILABLE, CREATED TECHNICAL DEBT TICKET ESPRJ-16903 to create some
                //[],
                // Verify that values for a risk variable based on an ISQ are included correctly.(is_calculated=0, and source = isq)
                [
                    4621284,
                    false,
                    true,
                    [
                        "org_id" => '19',
                        "risk_group_id" => '1',
                        "person_id" => '4621284',
                        "risk_variable_id" => '31',
                        "risk_model_id" => '16',
                        "source" => 'isq',
                        "variable_type" => 'categorical',
                        "weight" => '1294.0000',
                        "calculated_value" => '3.00',
                        "bucket_value" => '3'
                    ]
                ],
                // Verify that values for a risk variable based on a Profile item are included correctly.(is_calculated=0, and source = profile)
                [
                    4759591,
                    false,
                    true,
                    [
                        "org_id" => '163',
                        "risk_group_id" => '1',
                        "person_id" => '4759591',
                        "risk_variable_id" => '66',
                        "risk_model_id" => '14',
                        "source" => 'profile',
                        "variable_type" => 'continuous',
                        "weight" => '640.0000',
                        "calculated_value" => '2015',
                        "bucket_value" => '5'
                    ]
                ],
                // Verify that values for a risk variable based on a survey question are included correctly.(is_calculated=0, and source = surveyquestion)
                [
                    4759591,
                    false,
                    true,
                    [
                        "org_id" => '163',
                        "risk_group_id" => '1',
                        "person_id" => '4759591',
                        "risk_variable_id" => '77',
                        "risk_model_id" => '14',
                        "source" => 'surveyquestion',
                        "variable_type" => 'categorical',
                        "weight" => '1046.0000',
                        "calculated_value" => '6.00',
                        "bucket_value" => '6'
                    ]
                ],

                //CALCULATED RISK VARIABLES
                // Verify that if the most recent response for a 'Most Recent' calculated variable doesn't fall into any bucket, then the variable is not used.
                    // Step 1: Verify that person has a valid risk variable with data.
                    [
                        86394,
                        false,
                        false,
                        [
                            'org_id' => '155',
                            'risk_group_id' => '1',
                            'person_id' => '86394',
                            'risk_variable_id' => '71',
                            'risk_model_id' => '14',
                            'source' => 'profile',
                            'variable_type' => 'continuous',
                            'weight' => '1287.5000',
                            'calculated_value' => '86',
                            'calc_type' => 'Most Recent'
                        ]
                    ],
                    //Step 2: Verify that this risk variable does not show up because the calculated_value falls outside of bucket range.
                    [
                        86394,
                        true,
                        true,
                        [
                            [
                                "org_id" => '155',
                                "risk_group_id" => '1',
                                "person_id" => '86394',
                                "risk_variable_id" => '66',
                                "risk_model_id" => '14',
                                "source" => 'profile',
                                "variable_type" => 'continuous',
                                "weight" => '640.0000',
                                "calculated_value" => '2013',
                                "bucket_value" => '5'
                            ],
                            [
                                "org_id" => '155',
                                "risk_group_id" => '1',
                                "person_id" => '86394',
                                "risk_variable_id" => '67',
                                "risk_model_id" => '14',
                                "source" => 'profile',
                                "variable_type" => 'continuous',
                                "weight" => '355.5000',
                                "calculated_value" => '480',
                                "bucket_value" => '3'
                            ],
                            [
                                "org_id" => '155',
                                "risk_group_id" => '1',
                                "person_id" => '86394',
                                "risk_variable_id" => '68',
                                "risk_model_id" => '14',
                                "source" => 'profile',
                                "variable_type" => 'continuous',
                                "weight" => '374.0000',
                                "calculated_value" => '630',
                                "bucket_value" => '6'
                            ],
                            [
                                "org_id" => '155',
                                "risk_group_id" => '1',
                                "person_id" => '86394',
                                "risk_variable_id" => '74',
                                "risk_model_id" => '14',
                                "source" => 'profile',
                                "variable_type" => 'categorical',
                                "weight" => '400.0000',
                                "calculated_value" => '0',
                                "bucket_value" => '3'
                            ]
                        ]
                    ],
                // Verify that a response for a 'Most Recent' calculated variable is not included if it's year end is outside the calculation start or end date. (year specific)
                [
                    13,
                    false,
                    false,
                    [
                        "org_id" => '2',
                        "risk_group_id" => '1',
                        "person_id" => '13',
                        "risk_variable_id" => '72',
                        "risk_model_id" => '11',
                        "source" => 'profile',
                        "variable_type" => 'continuous',
                        "weight" => '600.0000',
                        "calculated_value" => null,
                        "calc_type" => 'Most Recent'
                    ]

                ],
                // Two Test Cases Verified in same test:
                // 1. Verify that a response for a 'Most Recent' works within given term window (term specific)
                // 2. Calculated variable is not included if it's term ends after the end date. (term specific)
                // Person has another (more recent) GPA in subsequent term for 3.23, that term end date falls AFTER calculation end date
                // only GPA from term end that falls within date range shows up with 3.76.
                [
                    784510,
                    false,
                    true,
                    [
                        "org_id" => '68',
                        "risk_group_id" => '1',
                        "person_id" => '784510',
                        "risk_variable_id" => '200',
                        "risk_model_id" => '11',
                        "source" => 'profile',
                        "variable_type" => 'continuous',
                        "weight" => '2500.0000',
                        "calculated_value" => '3.76',
                        "bucket_value" => '7'
                    ]
                ],
                // Verify that a response for a 'Most Recent' calculated variable is not included if it's term ends before the start date. (term specific)
                // Only values are from previous term, previous term end date does not fall within date range.
                [
                    4869133,
                    false,
                    false,
                    [
                        "org_id" => '2',
                        "risk_group_id" => '1',
                        "person_id" => '4869133',
                        "risk_variable_id" => '204',
                        "risk_model_id" => '11',
                        "source" => 'profile',
                        "variable_type" => 'categorical',
                        "weight" => '3500.0000',
                        "calculated_value" => null,
                        "calc_type" => 'Most Recent'
                    ]

                ],
                //Verify that a response that if a data point is not term or year specific, it is picked up if modified_at is within the calculation date range
                [
                    4759591,
                    false,
                    true,
                    [
                        "org_id" => '163',
                        "risk_group_id" => '1',
                        "person_id" => '4759591',
                        "risk_variable_id" => '245',
                        "risk_model_id" => '14',
                        "source" => 'profile',
                        "variable_type" => 'categorical',
                        "weight" => '1.0000',
                        "calculated_value" => '1.00',
                        "bucket_value" => '1'
                    ]

                ],
                //Verify that a response that if an data point is not year or term specific that it is NOT picked up if modified_at is NOT within the calculation date range
                [
                    1170824,
                    false,
                    false,
                    [
                        "org_id" => '163',
                        "risk_group_id" => '1',
                        "person_id" => '1170824',
                        "risk_variable_id" => '245',
                        "risk_model_id" => '14',
                        "source" => 'profile',
                        "variable_type" => 'categorical',
                        "weight" => '1.0000',
                        "calculated_value" => null,
                        "calc_type" => 'Most Recent'
                    ]
                ]
                //Special Calculated Risk Variables are NOT tested.  calc_type IN ('SUM', 'AVERAGE', 'COUNT').
                //These are not used in Risk currently and no data cases exist for them
                //Created Technical Debt ESPRJ-16904
            ]
            ]);
    }

}