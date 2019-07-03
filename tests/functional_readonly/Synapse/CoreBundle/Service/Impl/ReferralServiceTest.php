<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Service\Impl\ReferralService;


class ReferralServiceTest extends Test
{
    use Codeception\Specify;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var ReferralService
     */
    private $referralService;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();

        $managerMock = $this->getMockBuilder("\Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager")
            ->disableOriginalConstructor()
            ->setMethods(['assertPermissionToEngageWithStudents'])
            ->getMock("\Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager");

        $managerMock->expects($this->any())
            ->method('assertPermissionToEngageWithStudents')
            ->willReturn(1);

        $this->referralService = $this->container->get(ReferralService::SERVICE_KEY);
        $this->referralService->rbacManager = $managerMock;
    }


    public function testGetReferralCampusConnections()
    {
        $this->specify("Verify the functionality of the method getReferralCampusConnections", function($organizationId, $facultyId, $studentId, $expectedResult) {

            $result = $this->referralService->getReferralCampusConnections($organizationId, $facultyId, $studentId);
            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                // Example 1:  This campus does not have Primary Campus Connection Referral Routing enabled.
                // (This student has a primary campus connection that he/she is actually connected to, so if it were not for the above fact, a primary campus connection would be listed.)
                [19, 141986, 644387,
                    [
                        'organization_id' => 19,
                        'student_id' => 644387,
                        'faculty_id' => 141986,
                        'primary_campus_connection' => [],
                        'campus_connections' => [
                            [
                                'person_id' => 58203,
                                'first_name' => 'Mckenna',
                                'last_name' => 'Armstrong',
                                'title' => '',
                                'user_key' => 'CC-58203'
                            ],
                            [
                                'person_id' => 142659,
                                'first_name' => 'Dorothy',
                                'last_name' => 'Beard',
                                'title' => 'Associate Dean for Academic Support, ARC',
                                'user_key' => 'CC-142659'
                            ],
                            [
                                'person_id' => 132730,
                                'first_name' => 'Joyce',
                                'last_name' => 'Blankenship',
                                'title' => 'Dir, Student Act-Orient & Ldrshp',
                                'user_key' => 'CC-132730'
                            ],
                            [
                                'person_id' => 183541,
                                'first_name' => 'Rohan',
                                'last_name' => 'Bryan',
                                'title' => 'Residence Life Coordinator',
                                'user_key' => 'CC-183541'
                            ],
                            [
                                'person_id' => 189536,
                                'first_name' => 'Layne',
                                'last_name' => 'Carson',
                                'title' => '',
                                'user_key' => 'CC-189536'
                            ],
                            [
                                'person_id' => 182179,
                                'first_name' => 'Lyric',
                                'last_name' => 'Daniels',
                                'title' => 'Asst Dir of Student Activities',
                                'user_key' => 'CC-182179'
                            ],
                            [
                                'person_id' => 141986,
                                'first_name' => 'Leah',
                                'last_name' => 'Ferrell',
                                'title' => 'Adjunct Faculty',
                                'user_key' => 'CC-141986'
                            ],
                            [
                                'person_id' => 231054,
                                'first_name' => 'Alan',
                                'last_name' => 'Flores',
                                'title' => 'Project and Operations Manager',
                                'user_key' => 'CC-231054'
                            ],
                            [
                                'person_id' => 4857113,
                                'first_name' => 'Cannon',
                                'last_name' => 'Griffin',
                                'title' => 'Director of Career Development',
                                'user_key' => 'CC-4857113'
                            ],
                            [
                                'person_id' => 238661,
                                'first_name' => 'Nathaly',
                                'last_name' => 'Ibarra',
                                'title' => 'Associate Director of Financial Aid',
                                'user_key' => 'CC-238661'
                            ],
                            [
                                'person_id' => 245697,
                                'first_name' => 'Jamir',
                                'last_name' => 'Kemp',
                                'title' => 'Residence Life Coordinator',
                                'user_key' => 'CC-245697'
                            ],
                            [
                                'person_id' => 132723,
                                'first_name' => 'Jolie',
                                'last_name' => 'Knox',
                                'title' => '',
                                'user_key' => 'CC-132723'
                            ],
                            [
                                'person_id' => 4622766,
                                'first_name' => 'Adam',
                                'last_name' => 'Leblanc',
                                'title' => 'Part-Time RLC',
                                'user_key' => 'CC-4622766'
                            ],
                            [
                                'person_id' => 132707,
                                'first_name' => 'Sutton',
                                'last_name' => 'Mccullough',
                                'title' => 'Testing Center Coordinator',
                                'user_key' => 'CC-132707'
                            ],
                            [
                                'person_id' => 143039,
                                'first_name' => 'Jesus',
                                'last_name' => 'Nelson',
                                'title' => 'Academic Advisor',
                                'user_key' => 'CC-143039'
                            ],
                            [
                                'person_id' => 4557399,
                                'first_name' => 'Zain',
                                'last_name' => 'Newton',
                                'title' => 'Dean of Student Success',
                                'user_key' => 'CC-4557399'
                            ],
                            [
                                'person_id' => 254512,
                                'first_name' => 'Laney',
                                'last_name' => 'Patrick',
                                'title' => 'Counseling Center Case Manager',
                                'user_key' => 'CC-254512'
                            ],
                            [
                                'person_id' => 51280,
                                'first_name' => 'Arthur',
                                'last_name' => 'Pearson',
                                'title' => 'Academic Success Advocate',
                                'user_key' => 'CC-51280'
                            ],
                            [
                                'person_id' => 132735,
                                'first_name' => 'Aviana',
                                'last_name' => 'Pruitt',
                                'title' => 'Asst. Dean of Students',
                                'user_key' => 'CC-132735'
                            ],
                            [
                                'person_id' => 132897,
                                'first_name' => 'Abrielle',
                                'last_name' => 'Schmitt',
                                'title' => 'Associate Dean of Academic Advising, ARC',
                                'user_key' => 'CC-132897'
                            ],
                            [
                                'person_id' => 182180,
                                'first_name' => 'Gemma',
                                'last_name' => 'Stephens',
                                'title' => 'Director Student Campus Program',
                                'user_key' => 'CC-182180'
                            ],
                            [
                                'person_id' => 4751161,
                                'first_name' => 'Winston',
                                'last_name' => 'Stone',
                                'title' => '',
                                'user_key' => 'CC-4751161'
                            ],
                            [
                                'person_id' => 132729,
                                'first_name' => 'Saylor',
                                'last_name' => 'Stout',
                                'title' => 'Asst Dean of Students',
                                'user_key' => 'CC-132729'
                            ],
                            [
                                'person_id' => 132717,
                                'first_name' => 'Brenna',
                                'last_name' => 'Strong',
                                'title' => 'Director of Disability Services',
                                'user_key' => 'CC-132717'
                            ],
                            [
                                'person_id' => 53165,
                                'first_name' => 'Maximus',
                                'last_name' => 'Warren',
                                'title' => '',
                                'user_key' => 'CC-53165'
                            ],
                            [
                                'person_id' => 132711,
                                'first_name' => 'Naya',
                                'last_name' => 'Winters',
                                'title' => '',
                                'user_key' => 'CC-132711'
                            ],
                            [
                                'person_id' => 4557396,
                                'first_name' => 'Julien',
                                'last_name' => 'Yang',
                                'title' => 'Assistant Director of Residence Life',
                                'user_key' => 'CC-4557396'
                            ]

                        ]
                    ]
                ],
                // Example 2:  This student has a primary campus connection that he/she is actually connected to.
                // (This campus has Primary Campus Connection Referral Routing enabled.)
                [138, 230124, 4633630,
                    [
                        'organization_id' => 138,
                        'student_id' => 4633630,
                        'faculty_id' => 230124,
                        'primary_campus_connection' => [
                            'person_id' => 230124,
                            'first_name' => 'Anastasia',
                            'last_name' => 'Marshall',
                            'title' => 'Residence Coordinator',
                            'user_key' => 'PCC-230124'
                        ],
                        'campus_connections' => [
                            [
                                'person_id' => 985300,
                                'first_name' => 'Darian',
                                'last_name' => 'Caldwell',
                                'title' => 'Resident Assistant',
                                'user_key' => 'CC-985300'
                            ],
                            [
                                'person_id' => 987468,
                                'first_name' => 'Camilo',
                                'last_name' => 'Floyd',
                                'title' => 'Learning Assistant',
                                'user_key' => 'CC-987468'
                            ],
                            [
                                'person_id' => 229660,
                                'first_name' => 'Blaze',
                                'last_name' => 'Gilmore',
                                'title' => 'Resident Assistant',
                                'user_key' => 'CC-229660'
                            ],
                            [
                                'person_id' => 255113,
                                'first_name' => 'Griffin',
                                'last_name' => 'Griffin',
                                'title' => 'Resident Assistant',
                                'user_key' => 'CC-255113'
                            ],
                            [
                                'person_id' => 4687780,
                                'first_name' => 'Quinn',
                                'last_name' => 'Hebert',
                                'title' => 'Success Coach',
                                'user_key' => 'CC-4687780'
                            ],
                            [
                                'person_id' => 229658,
                                'first_name' => 'Van',
                                'last_name' => 'Hobbs',
                                'title' => 'Resident Assistant',
                                'user_key' => 'CC-229658'
                            ],
                            [
                                'person_id' => 229661,
                                'first_name' => 'Kolten',
                                'last_name' => 'Ibarra',
                                'title' => 'Resident Assistant',
                                'user_key' => 'CC-229661'
                            ],
                            [
                                'person_id' => 987518,
                                'first_name' => 'Aria',
                                'last_name' => 'Li',
                                'title' => 'Learning Assistant',
                                'user_key' => 'CC-987518'
                            ],
                            [
                                'person_id' => 229657,
                                'first_name' => 'Mayson',
                                'last_name' => 'Suarez',
                                'title' => 'Resident Assistant',
                                'user_key' => 'CC-229657'
                            ]
                        ]
                    ]
                ],
                // Example 3:  This student has no primary campus connection.
                // (This campus has Primary Campus Connection Referral Routing enabled.)
                [138, 230124, 4633618,
                    [
                        'organization_id' => 138,
                        'student_id' => 4633618,
                        'faculty_id' => 230124,
                        'primary_campus_connection' => [],
                        'campus_connections' => [
                            [
                                'person_id' => 985300,
                                'first_name' => 'Darian',
                                'last_name' => 'Caldwell',
                                'title' => 'Resident Assistant',
                                'user_key' => 'CC-985300'
                            ],
                            [
                                'person_id' => 987468,
                                'first_name' => 'Camilo',
                                'last_name' => 'Floyd',
                                'title' => 'Learning Assistant',
                                'user_key' => 'CC-987468'
                            ],
                            [
                                'person_id' => 229660,
                                'first_name' => 'Blaze',
                                'last_name' => 'Gilmore',
                                'title' => 'Resident Assistant',
                                'user_key' => 'CC-229660'
                            ],
                            [
                                'person_id' => 255113,
                                'first_name' => 'Griffin',
                                'last_name' => 'Griffin',
                                'title' => 'Resident Assistant',
                                'user_key' => 'CC-255113'
                            ],
                            [
                                'person_id' => 4687780,
                                'first_name' => 'Quinn',
                                'last_name' => 'Hebert',
                                'title' => 'Success Coach',
                                'user_key' => 'CC-4687780'
                            ],
                            [
                                'person_id' => 229658,
                                'first_name' => 'Van',
                                'last_name' => 'Hobbs',
                                'title' => 'Resident Assistant',
                                'user_key' => 'CC-229658'
                            ],
                            [
                                'person_id' => 229661,
                                'first_name' => 'Kolten',
                                'last_name' => 'Ibarra',
                                'title' => 'Resident Assistant',
                                'user_key' => 'CC-229661'
                            ],
                            [
                                'person_id' => 987518,
                                'first_name' => 'Aria',
                                'last_name' => 'Li',
                                'title' => 'Learning Assistant',
                                'user_key' => 'CC-987518'
                            ],
                            [
                                'person_id' => 230124,
                                'first_name' => 'Anastasia',
                                'last_name' => 'Marshall',
                                'title' => 'Residence Coordinator',
                                'user_key' => 'CC-230124'
                            ],
                            [
                                'person_id' => 229657,
                                'first_name' => 'Mayson',
                                'last_name' => 'Suarez',
                                'title' => 'Resident Assistant',
                                'user_key' => 'CC-229657'
                            ]
                        ]
                    ]
                ],
                // Example 4:  This student has a primary campus connection, but is not connected to him/her.
                // (This campus has Primary Campus Connection Referral Routing enabled.)
                [138, 4687780, 681846,
                    [
                        'organization_id' => 138,
                        'student_id' => 681846,
                        'faculty_id' => 4687780,
                        'primary_campus_connection' => [],
                        'campus_connections' => [
                            [
                                'person_id' => 4687780,
                                'first_name' => 'Quinn',
                                'last_name' => 'Hebert',
                                'title' => 'Success Coach',
                                'user_key' => 'CC-4687780'
                            ]
                        ]
                    ]
                ],
                // Example 5:  This organization doesn't have referrals enabled.
                [30, 85003, 543389,
                    [
                        'organization_id' => 30,
                        'student_id' => 543389,
                        'faculty_id' => 85003
                    ]
                ],
                // Example 6:  This faculty and student are connected, but the permission set connecting them (110)
                // does not allow the user to create direct referrals;
                // it does allow the user to create reason-routed referrals.
                [20, 231859, 231683,
                    [
                        'organization_id' => 20,
                        'student_id' => 231683,
                        'faculty_id' => 231859
                    ]
                ]
            ]
        ]);

        $this->specify("Verify that the method getReferralCampusConnections throws appropriate exceptions.", function ($organizationId, $facultyId, $studentId, $expectedExceptionClass, $expectedExceptionMessage) {
            try {
                $this->referralService->getReferralCampusConnections($organizationId, $facultyId, $studentId);
            } catch (SynapseException $exception) {
                verify($exception)->isInstanceOf($expectedExceptionClass);
                verify($exception->getMessage())->equals($expectedExceptionMessage);
            }
        }, ['examples' =>
            [
                // Example 1:  This student and faculty have no connection.
                [138, 230124, 681846, 'Synapse\CoreBundle\Exception\AccessDeniedException', 'Access Denied'],
                // Example 2:  This faculty is not connected to any students.
                [19, 132936, null, 'Synapse\CoreBundle\Exception\AccessDeniedException', 'Access Denied']
            ]
        ]);
    }

}