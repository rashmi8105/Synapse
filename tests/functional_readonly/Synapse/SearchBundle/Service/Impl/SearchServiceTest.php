<?php

use Codeception\TestCase\Test;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;

class SearchServiceTest extends Test
{
    use Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\SearchBundle\Service\Impl\SearchService
     */
    private $searchService;


    public function _before()
    {
        $this->markTestSkipped("AbstractService's repository resolver is not getting instantiated, causing this test to fail. Refreshing cache locally does not fix the issue.");
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->searchService = $this->container->get('search_service');
    }


    public function testGetCustomSearchResults()
    {
        $this->specify("Verify the functionality of the method getCustomSearchResults", function($searchAttributes, $loggedInUserId, $organizationId, $studentStatus, $sortBy, $pageNumber, $recordsPerPage, $expectedResult) {

            $customSearchDTO = $this->createCustomSearchDTOForTest($searchAttributes);
            $result = $this->searchService->getCustomSearchResults($customSearchDTO, $loggedInUserId, $organizationId, $studentStatus, $sortBy, $pageNumber, $recordsPerPage);

            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                // Example 1: Students with red or red2 risk
                [
                    [
                        'risk_indicator_ids' => '1,2',
                        'academic_updates' => ['isBlankAcadUpdate' => true]
                    ],
                    138650, 109, 'both', 'student_last_name', 1, 5,
                    [
                        'person_id' => 138650,
                        'search_result' => [
                            [
                                'student_id' => 4778376,
                                'student_first_name' => 'Lina',
                                'student_last_name' => 'Acosta',
                                'external_id' => 4778376,
                                'student_primary_email' => 'MapworksBetaUser04778376@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'red',
                                'student_risk_image_name' => 'risk-level-icon-r1.png',
                                'student_intent_to_leave' => 'red',
                                'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 9,
                                'last_activity_date' => '03/30/2016',
                                'last_activity' => 'Email'
                            ],
                            [
                                'student_id' => 4778407,
                                'student_first_name' => 'Milena',
                                'student_last_name' => 'Adkins',
                                'external_id' => 4778407,
                                'student_primary_email' => 'MapworksBetaUser04778407@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'red2',
                                'student_risk_image_name' => 'risk-level-icon-r2.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => 'Sophomore',
                                'student_logins' => 5,
                                'last_activity_date' => '04/26/2016',
                                'last_activity' => 'Email'
                            ],
                            [
                                'student_id' => 4778110,
                                'student_first_name' => 'Evie',
                                'student_last_name' => 'Alexander',
                                'external_id' => 4778110,
                                'student_primary_email' => 'MapworksBetaUser04778110@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'red2',
                                'student_risk_image_name' => 'risk-level-icon-r2.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 5,
                                'last_activity_date' => '04/26/2016',
                                'last_activity' => 'Email'
                            ],
                            [
                                'student_id' => 4877589,
                                'student_first_name' => 'Claire',
                                'student_last_name' => 'Allison',
                                'external_id' => 4877589,
                                'student_primary_email' => 'MapworksBetaUser04877589@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'red2',
                                'student_risk_image_name' => 'risk-level-icon-r2.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 1,
                                'last_activity_date' => '02/29/2016',
                                'last_activity' => 'Email'
                            ],
                            [
                                'student_id' => 4780205,
                                'student_first_name' => 'Maryam',
                                'student_last_name' => 'Andrews',
                                'external_id' => 4780205,
                                'student_primary_email' => 'MapworksBetaUser04780205@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'red2',
                                'student_risk_image_name' => 'risk-level-icon-r2.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 7,
                                'last_activity_date' => '04/26/2016',
                                'last_activity' => 'Email'
                            ]
                        ],
                        'total_records' => 725,
                        'records_per_page' => 5,
                        'total_pages' => 145,
                        'current_page' => 1,
                        'search_attributes' => [
                            'risk_indicator_ids' => '1,2',
                            'academic_updates' => ['isBlankAcadUpdate' => true]
                        ]
                    ]
                ],
                // Example 2a: All Students group
                [
                    [
                        'group_ids' => 369206,
                        'academic_updates' => ['isBlankAcadUpdate' => true]
                    ],
                    4878750, 203, 'both', 'student_last_name', 1, 5,
                    [
                        'person_id' => 4878750,
                        'search_result' => [
                            [
                                'student_id' => 4879587,
                                'student_first_name' => 'Chloe',
                                'student_last_name' => 'Abbott',
                                'external_id' => 4879587,
                                'student_primary_email' => 'MapworksBetaUser04879587@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'yellow',
                                'student_risk_image_name' => 'risk-level-icon-y.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 0,
                                'last_activity_date' => null,
                                'last_activity' => null
                            ],
                            [
                                'student_id' => 4879823,
                                'student_first_name' => 'Haley',
                                'student_last_name' => 'Acevedo',
                                'external_id' => 4879823,
                                'student_primary_email' => 'MapworksBetaUser04879823@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'gray',
                                'student_risk_image_name' => 'risk-level-icon-gray.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 0,
                                'last_activity_date' => null,
                                'last_activity' => null
                            ],
                            [
                                'student_id' => 4878823,
                                'student_first_name' => 'Raymond',
                                'student_last_name' => 'Acevedo',
                                'external_id' => 4878823,
                                'student_primary_email' => 'MapworksBetaUser04878823@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'gray',
                                'student_risk_image_name' => 'risk-level-icon-gray.png',
                                'student_intent_to_leave' => 'green',
                                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 0,
                                'last_activity_date' => null,
                                'last_activity' => null
                            ],
                            [
                                'student_id' => 4879376,
                                'student_first_name' => 'Franco',
                                'student_last_name' => 'Acosta',
                                'external_id' => 4879376,
                                'student_primary_email' => 'MapworksBetaUser04879376@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'yellow',
                                'student_risk_image_name' => 'risk-level-icon-y.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 0,
                                'last_activity_date' => null,
                                'last_activity' => null
                            ],
                            [
                                'student_id' => 4879407,
                                'student_first_name' => 'Reuben',
                                'student_last_name' => 'Adkins',
                                'external_id' => 4879407,
                                'student_primary_email' => 'MapworksBetaUser04879407@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'gray',
                                'student_risk_image_name' => 'risk-level-icon-gray.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 0,
                                'last_activity_date' => null,
                                'last_activity' => null
                            ]
                        ],
                        'total_records' => 1000,
                        'records_per_page' => 5,
                        'total_pages' => 200,
                        'current_page' => 1,
                        'search_attributes' => [
                            'group_ids' => 369206,
                            'academic_updates' => ['isBlankAcadUpdate' => true]
                        ]
                    ]
                ],
                // Example 2b: Cross Country group.  This faculty member has access to these students via the All Students group, but is not connected in any way to the Cross Country group.
                [
                    [
                        'group_ids' => 370658,
                        'academic_updates' => ['isBlankAcadUpdate' => true]
                    ],
                    4878750, 203, 'both', 'student_last_name', 1, 5,
                    [
                        'person_id' => 4878750,
                        'search_result' => [],
                        'total_records' => 0,
                        'records_per_page' => 5,
                        'total_pages' => 0,
                        'current_page' => 1,
                        'search_attributes' => [
                            'group_ids' => 370658,
                            'academic_updates' => ['isBlankAcadUpdate' => true]
                        ]
                    ]
                ],
                // Example 2c: Stark Hall group.  There are no students directly in Stark Hall; they're in the subgroups Stark 1 and Stark 2.  This example exposes the issue in ESPRJ-10372.
                [
                    [
                        'group_ids' => 370636,
                        'academic_updates' => ['isBlankAcadUpdate' => true]
                    ],
                    4883175, 203, 'both', 'student_last_name', 1, 5,
                    [
                        'person_id' => 4883175,
                        'search_result' => [
                            [
                                'student_id' => 4879823,
                                'student_first_name' => 'Haley',
                                'student_last_name' => 'Acevedo',
                                'external_id' => 4879823,
                                'student_primary_email' => 'MapworksBetaUser04879823@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'gray',
                                'student_risk_image_name' => 'risk-level-icon-gray.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 0,
                                'last_activity_date' => null,
                                'last_activity' => null
                            ],
                            [
                                'student_id' => 4878957,
                                'student_first_name' => 'Dillon',
                                'student_last_name' => 'Archer',
                                'external_id' => 4878957,
                                'student_primary_email' => 'MapworksBetaUser04878957@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'gray',
                                'student_risk_image_name' => 'risk-level-icon-gray.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 0,
                                'last_activity_date' => null,
                                'last_activity' => null
                            ],
                            [
                                'student_id' => 4879832,
                                'student_first_name' => 'Chelsea',
                                'student_last_name' => 'Arroyo',
                                'external_id' => 4879832,
                                'student_primary_email' => 'MapworksBetaUser04879832@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'gray',
                                'student_risk_image_name' => 'risk-level-icon-gray.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 0,
                                'last_activity_date' => null,
                                'last_activity' => null
                            ],
                            [
                                'student_id' => 4878934,
                                'student_first_name' => 'Matteo',
                                'student_last_name' => 'Baird',
                                'external_id' => 4878934,
                                'student_primary_email' => 'MapworksBetaUser04878934@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'gray',
                                'student_risk_image_name' => 'risk-level-icon-gray.png',
                                'student_intent_to_leave' => 'green',
                                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 0,
                                'last_activity_date' => null,
                                'last_activity' => null
                            ],
                            [
                                'student_id' => 4879446,
                                'student_first_name' => 'Santana',
                                'student_last_name' => 'Barton',
                                'external_id' => 4879446,
                                'student_primary_email' => 'MapworksBetaUser04879446@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'gray',
                                'student_risk_image_name' => 'risk-level-icon-gray.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 0,
                                'last_activity_date' => null,
                                'last_activity' => null
                            ],
                        ],
                        'total_records' => 100,
                        'records_per_page' => 5,
                        'total_pages' => 20,
                        'current_page' => 1,
                        'search_attributes' => [
                            'group_ids' => 370636,
                            'academic_updates' => ['isBlankAcadUpdate' => true]
                        ]
                    ]
                ],
                // Example 2d: Stark 1 group.  This faculty member is only directly in Stark Hall; Stark 1 is a subgroup.  This example exposes the issue in ESPRJ-10367.
                [
                    [
                        'group_ids' => 370649,
                        'academic_updates' => ['isBlankAcadUpdate' => true]
                    ],
                    4883175, 203, 'both', 'student_last_name', 1, 5,
                    [
                        'person_id' => 4883175,
                        'search_result' => [
                            [
                                'student_id' => 4879832,
                                'student_first_name' => 'Chelsea',
                                'student_last_name' => 'Arroyo',
                                'external_id' => 4879832,
                                'student_primary_email' => 'MapworksBetaUser04879832@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'gray',
                                'student_risk_image_name' => 'risk-level-icon-gray.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 0,
                                'last_activity_date' => null,
                                'last_activity' => null
                            ],
                            [
                                'student_id' => 4878934,
                                'student_first_name' => 'Matteo',
                                'student_last_name' => 'Baird',
                                'external_id' => 4878934,
                                'student_primary_email' => 'MapworksBetaUser04878934@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'gray',
                                'student_risk_image_name' => 'risk-level-icon-gray.png',
                                'student_intent_to_leave' => 'green',
                                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 0,
                                'last_activity_date' => null,
                                'last_activity' => null
                            ],
                            [
                                'student_id' => 4879672,
                                'student_first_name' => 'Jocelyn',
                                'student_last_name' => 'Blackwell',
                                'external_id' => 4879672,
                                'student_primary_email' => 'MapworksBetaUser04879672@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'gray',
                                'student_risk_image_name' => 'risk-level-icon-gray.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 1,
                                'last_activity_date' => '02/04/2016',
                                'last_activity' => 'Referral'
                            ],
                            [
                                'student_id' => 4879768,
                                'student_first_name' => 'Genevieve',
                                'student_last_name' => 'Bradshaw',
                                'external_id' => 4879768,
                                'student_primary_email' => 'MapworksBetaUser04879768@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'green',
                                'student_risk_image_name' => 'risk-level-icon-g.png',
                                'student_intent_to_leave' => 'dark gray',
                                'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 0,
                                'last_activity_date' => null,
                                'last_activity' => null
                            ],
                            [
                                'student_id' => 4878966,
                                'student_first_name' => 'Damon',
                                'student_last_name' => 'Branch',
                                'external_id' => 4878966,
                                'student_primary_email' => 'MapworksBetaUser04878966@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'gray',
                                'student_risk_image_name' => 'risk-level-icon-gray.png',
                                'student_intent_to_leave' => 'green',
                                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 0,
                                'last_activity_date' => null,
                                'last_activity' => null
                            ],
                        ],
                        'total_records' => 50,
                        'records_per_page' => 5,
                        'total_pages' => 10,
                        'current_page' => 1,
                        'search_attributes' => [
                            'group_ids' => 370649,
                            'academic_updates' => ['isBlankAcadUpdate' => true]
                        ]
                    ]
                ],
                // Example 3a: Students who answered a particular survey question
                [
                    [
                        'survey' => [
                            [
                                'survey_id' => 11,
                                'survey_questions' => [
                                    [
                                        'id' => 233,
                                        'type' => 'category',
                                        'options' => [
                                            [
                                                'answer' => '(1) Not at all',
                                                'value' => 1,
                                                'id' => 15696
                                            ],
                                            [
                                                'answer' => '(2)',
                                                'value' => 1,
                                                'id' => 15697
                                            ],
                                            [
                                                'answer' => '(3)',
                                                'value' => 1,
                                                'id' => 15698
                                            ],
                                            [
                                                'answer' => '(4) Moderately',
                                                'value' => 1,
                                                'id' => 15699
                                            ],
                                            [
                                                'answer' => '(5)',
                                                'value' => 1,
                                                'id' => 15700
                                            ],
                                            [
                                                'answer' => '(6)',
                                                'value' => 1,
                                                'id' => 15701
                                            ],
                                            [
                                                'answer' => '(7) Extremely',
                                                'value' => 1,
                                                'id' => 15702
                                            ],
                                            [
                                                'answer' => 'Not Applicable',
                                                'value' => 99,
                                                'id' => 15703
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'academic_updates' => ['isBlankAcadUpdate' => true]
                    ],
                    138650, 109, 'both', 'student_last_name', 1, 5,
                    [
                        'person_id' => 138650,
                        'search_result' => [
                            [
                                'student_id' => 4778587,
                                'student_first_name' => 'Ryann',
                                'student_last_name' => 'Abbott',
                                'external_id' => 4778587,
                                'student_primary_email' => 'MapworksBetaUser04778587@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'yellow',
                                'student_risk_image_name' => 'risk-level-icon-y.png',
                                'student_intent_to_leave' => 'yellow',
                                'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 11,
                                'last_activity_date' => '03/30/2016',
                                'last_activity' => 'Email'
                            ],
                            [
                                'student_id' => 4779110,
                                'student_first_name' => 'Zayne',
                                'student_last_name' => 'Alexander',
                                'external_id' => 4779110,
                                'student_primary_email' => 'MapworksBetaUser04779110@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'green',
                                'student_risk_image_name' => 'risk-level-icon-g.png',
                                'student_intent_to_leave' => 'green',
                                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 10,
                                'last_activity_date' => '03/30/2016',
                                'last_activity' => 'Email'
                            ],
                            [
                                'student_id' => 4778875,
                                'student_first_name' => 'Jeffrey',
                                'student_last_name' => 'Ali',
                                'external_id' => 4778875,
                                'student_primary_email' => 'MapworksBetaUser04778875@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'green',
                                'student_risk_image_name' => 'risk-level-icon-g.png',
                                'student_intent_to_leave' => 'green',
                                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                'student_classlevel' => 'Sophomore',
                                'student_logins' => 3,
                                'last_activity_date' => '02/29/2016',
                                'last_activity' => 'Email'
                            ],
                            [
                                'student_id' => 863579,
                                'student_first_name' => 'Annabelle',
                                'student_last_name' => 'Anthony',
                                'external_id' => 863579,
                                'student_primary_email' => 'MapworksBetaUser00863579@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'gray',
                                'student_risk_image_name' => 'risk-level-icon-gray.png',
                                'student_intent_to_leave' => 'green',
                                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                'student_classlevel' => 'Junior',
                                'student_logins' => 4,
                                'last_activity_date' => '02/12/2015',
                                'last_activity' => 'Contact'
                            ],
                            [
                                'student_id' => 4778591,
                                'student_first_name' => 'Aya',
                                'student_last_name' => 'Atkinson',
                                'external_id' => 4778591,
                                'student_primary_email' => 'MapworksBetaUser04778591@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'green',
                                'student_risk_image_name' => 'risk-level-icon-g.png',
                                'student_intent_to_leave' => 'green',
                                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 7,
                                'last_activity_date' => '03/04/2016',
                                'last_activity' => 'Email'
                            ]
                        ],
                        'total_records' => 295,
                        'records_per_page' => 5,
                        'total_pages' => 59,
                        'current_page' => 1,
                        'search_attributes' => [
                            'survey' => [
                                [
                                    'survey_id' => 11,
                                    'survey_questions' => [
                                        [
                                            'id' => 233,
                                            'type' => 'category',
                                            'options' => [
                                                [
                                                    'answer' => '(1) Not at all',
                                                    'value' => 1,
                                                    'id' => 15696
                                                ],
                                                [
                                                    'answer' => '(2)',
                                                    'value' => 1,
                                                    'id' => 15697
                                                ],
                                                [
                                                    'answer' => '(3)',
                                                    'value' => 1,
                                                    'id' => 15698
                                                ],
                                                [
                                                    'answer' => '(4) Moderately',
                                                    'value' => 1,
                                                    'id' => 15699
                                                ],
                                                [
                                                    'answer' => '(5)',
                                                    'value' => 1,
                                                    'id' => 15700
                                                ],
                                                [
                                                    'answer' => '(6)',
                                                    'value' => 1,
                                                    'id' => 15701
                                                ],
                                                [
                                                    'answer' => '(7) Extremely',
                                                    'value' => 1,
                                                    'id' => 15702
                                                ],
                                                [
                                                    'answer' => 'Not Applicable',
                                                    'value' => 99,
                                                    'id' => 15703
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'academic_updates' => ['isBlankAcadUpdate' => true]
                        ]
                    ]
                ],
                // Example 3b: Students who answered a particular survey question a particular way
                [
                    [
                        'survey' => [
                            [
                                'survey_id' => 11,
                                'survey_questions' => [
                                    [
                                        'id' => 233,
                                        'type' => 'category',
                                        'options' => [
                                            [
                                                'answer' => '(3)',
                                                'value' => 3,
                                                'id' => 15698
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'academic_updates' => ['isBlankAcadUpdate' => true]
                    ],
                    138650, 109, 'both', 'student_last_name', 1, 5,
                    [
                        'person_id' => 138650,
                        'search_result' => [
                            [
                                'student_id' => 4777875,
                                'student_first_name' => 'Jenna',
                                'student_last_name' => 'Ali',
                                'external_id' => 4777875,
                                'student_primary_email' => 'MapworksBetaUser04777875@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'green',
                                'student_risk_image_name' => 'risk-level-icon-g.png',
                                'student_intent_to_leave' => 'green',
                                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 7,
                                'last_activity_date' => '03/30/2016',
                                'last_activity' => 'Email'
                            ],
                            [
                                'student_id' => 1058875,
                                'student_first_name' => 'Kade',
                                'student_last_name' => 'Ali',
                                'external_id' => 1058875,
                                'student_primary_email' => 'MapworksBetaUser01058875@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'green',
                                'student_risk_image_name' => 'risk-level-icon-g.png',
                                'student_intent_to_leave' => 'green',
                                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 5,
                                'last_activity_date' => '03/30/2016',
                                'last_activity' => 'Email'
                            ],


                            [
                                'student_id' => 4778953,
                                'student_first_name' => 'Maximiliano',
                                'student_last_name' => 'Andersen',
                                'external_id' => 4778953,
                                'student_primary_email' => 'MapworksBetaUser04778953@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'green',
                                'student_risk_image_name' => 'risk-level-icon-g.png',
                                'student_intent_to_leave' => 'green',
                                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                'student_classlevel' => '1st Year/Freshman',
                                'student_logins' => 5,
                                'last_activity_date' => '03/30/2016',
                                'last_activity' => 'Email'
                            ],
                            [
                                'student_id' => 1058665,
                                'student_first_name' => 'Brady',
                                'student_last_name' => 'Andrade',
                                'external_id' => 1058665,
                                'student_primary_email' => 'MapworksBetaUser01058665@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'green',
                                'student_risk_image_name' => 'risk-level-icon-g.png',
                                'student_intent_to_leave' => 'green',
                                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                'student_classlevel' => 'Sophomore',
                                'student_logins' => 25,
                                'last_activity_date' => '03/30/2016',
                                'last_activity' => 'Email'
                            ],
                            [
                                'student_id' => 561186,
                                'student_first_name' => 'Case',
                                'student_last_name' => 'Arnold',
                                'external_id' => 561186,
                                'student_primary_email' => 'MapworksBetaUser00561186@mailinator.com',
                                'student_status' => 1,
                                'student_risk_status' => 'gray',
                                'student_risk_image_name' => 'risk-level-icon-gray.png',
                                'student_intent_to_leave' => 'green',
                                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                'student_classlevel' => 'Junior',
                                'student_logins' => 20,
                                'last_activity_date' => '03/04/2016',
                                'last_activity' => 'Email'
                            ]
                        ],
                        'total_records' => 93,
                        'records_per_page' => 5,
                        'total_pages' => 19,
                        'current_page' => 1,
                        'search_attributes' => [
                            'survey' => [
                                [
                                    'survey_id' => 11,
                                    'survey_questions' => [
                                        [
                                            'id' => 233,
                                            'type' => 'category',
                                            'options' => [
                                                [
                                                    'answer' => '(3)',
                                                    'value' => 3,
                                                    'id' => 15698
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'academic_updates' => ['isBlankAcadUpdate' => true]
                        ]
                    ]
                ]
            ]
        ]);
    }


    private function createCustomSearchDTOForTest($searchAttributes)
    {
        $customSearchDTO = new SaveSearchDto();
        $customSearchDTO->setSearchAttributes($searchAttributes);
        return $customSearchDTO;
    }

}