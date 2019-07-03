<?php

namespace tests\unit\Synapse\CoreBundle\Service\Util;

use Doctrine\ORM\Mapping\Cache;
use Symfony\Component\Config\Definition\Exception\Exception;
use Synapse\AcademicUpdateBundle\EntityDto\IndividualAcademicUpdateDTO;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\RoleLangRepository;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\SynapseConstant;


class APIValidationServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    use \Codeception\Specify;

    public function testUpdateOrganizationAPIValidationErrorCount()
    {
        $this->specify("Test organization API validation error counts.", function ($organizationId, $maxErrorCount, $validationErrors, $expireAt) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockCache = $this->getMock('cache', array('fetch', 'save'));
            if ($expireAt) {
                $previousErrors['expires_at'] = $expireAt;
                $previousErrors['count'] = count($validationErrors);
                $mockCache->method('fetch')->willReturn($previousErrors);
            }
            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy', 'getValue']);
            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [EbiConfigRepository::REPOSITORY_KEY, $mockEbiConfigRepository]
            ]);

            $ebiConfigEntity = $this->getMock('EbiConfig', array('getValue'));
            $ebiConfigEntity->method('getValue')->willReturn($maxErrorCount);


            $ebiConfigEntity = $this->getMock('EbiConfig', array('getValue'));
            $ebiConfigEntity->method('getValue')->willReturn($maxErrorCount);

            $ebiConfigEntityApiInterval = $this->getMock('EbiConfig', array('getValue'));
            $ebiConfigEntityApiInterval->method('getValue')->willReturn(1); // Error interval in minutes set to 1 minute

            $mockContainer->method('get')
                ->willReturnMap([
                    [SynapseConstant::REDIS_CLASS_KEY, $mockCache]
                ]);

            $mockEbiConfigRepository->method('findOneBy')->willReturnMap([
                [
                    ['key' => SynapseConstant::API_MAX_ERROR_COUNT_KEY],
                    $ebiConfigEntity
                ],
                [
                    ['key' => SynapseConstant::API_ERROR_INTERVAL_KEY],
                    $ebiConfigEntityApiInterval
                ]
            ]);
            $apiValidationService = new APIValidationService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $apiValidationService->updateOrganizationAPIValidationErrorCount($organizationId, $validationErrors);
            } catch (\Exception $e) {

                $currentDate = new \DateTime();
                $expiresAt = New \DateTime($expireAt);

                if ($expiresAt < $currentDate) {
                    $currentDate->modify("+1 minutes"); // Error interval in minutes set to 1 minute
                    $dateTime = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
                } else {
                    $dateTime = $expiresAt->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
                }
                $errorMessage = "You have exceeded the number of allowed validation errors in the allowed time frame. Please try your API call again after $dateTime UTC";
                $this->assertEquals($e->getMessage(), $errorMessage);
            }

        }, ['examples' => [
            [
                // API error count is greater than maximum error count
                // There is no previous error in cache
                203,
                1,
                [
                    'Course ID 1234 is not valid at this institution',
                    'Person ID 89677 could not be located at the organization'
                ],
                date(SynapseConstant::DEFAULT_DATETIME_FORMAT, strtotime('+1 minute'))
            ],
            [
                // API error count is less than maximum error count
                203,
                10,
                [
                    'Course ID 452168 is not valid at this institution',
                    'Course ID 8745 is not valid at this institution',
                    'Person ID 745861 is not valid at this institution'
                ],
                ''
            ],
            [
                // Expire time is passed
                203,
                2,
                [
                    'Course ID 452168 is not valid at this institution',
                    'Course ID 8745 is not valid at this institution',
                    'Person ID 745861 is not valid at this institution'

                ],
                date(SynapseConstant::DEFAULT_DATETIME_FORMAT, strtotime('yesterday'))
            ],
            [
                // Expire time is not passed and API error count is greater than maximum error count
                203,
                2,
                [
                    'Course ID 452168 is not valid at this institution',
                    'Person ID 13547 is not valid at this institution',
                    'Person ID 6589 is not valid at this institution',
                    'Course ID 4512 is not valid at this institution',
                ],
                date(SynapseConstant::DEFAULT_DATETIME_FORMAT, strtotime('tomorrow'))
            ],
            [
                // Expire time is not passed and API error count is less than maximum error count
                203,
                10,
                [
                    'Course ID 452168 is not valid at this institution',
                    'Person ID 13547 is not valid at this institution',
                    'Person ID 6589 is not valid at this institution',
                    'Course ID 4512 is not valid at this institution',
                ],
                date(SynapseConstant::DEFAULT_DATETIME_FORMAT, strtotime('tomorrow'))
            ]
        ]]);
    }

    /**
     * @expectedException \Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testIsAPIIntegrationEnabled()
    {
        $this->specify("Validate API Integration is enabled or not", function ($isEnabled, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy', 'getValue']);
            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [EbiConfigRepository::REPOSITORY_KEY, $mockEbiConfigRepository]
            ]);
            $ebiConfigEntity = $this->getMock('EbiConfig', array('getValue'));
            $mockEbiConfigRepository->expects($this->at(0))->method('findOneBy')->willReturn($ebiConfigEntity);
            $ebiConfigEntity->expects($this->at(0))->method('getValue')->willReturn($isEnabled);

            $apiValidationService = new APIValidationService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $apiValidationService->isAPIIntegrationEnabled();
            $this->assertEquals($result, $expectedResult);
        }, ['examples' => [

            [   // API Integration is enabled
                0,
                true
            ],
            [   // API Integration is disabled, this will throw AccessDeniedException
                1,
                false
            ]

        ]]);
    }

    /**
     * @expectedException \Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testIsOrganizationAPICoordinator()
    {
        $this->specify("Validate is the logged in person has API Coordinator role", function ($organizationId, $personId, $roleId, $validAPICoordinator, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockRoleLangRepository = $this->getMock('RoleLangRepository', array('findOneBy', 'getRole'));
            $organizationRoleRepository = $this->getMock('organizationRoleRepository', array('findOneBy'));


            $roleObject = $this->getMock('RoleLang', array('getRole', 'getId'));
            $serviceAccountObject = $this->getMock('serviceAccountRoleObject', array('getRole', 'getId'));

            $mockRoleLangRepository->method('findOneBy')->willReturn($serviceAccountObject);

            $serviceAccountObject->expects($this->at(0))->method('getRole')->willReturn($roleObject);
            $roleObject->expects($this->at(0))->method('getId')->willReturn($roleId);
            $organizationRoleRepository->method('findOneBy')->willReturn($validAPICoordinator);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [RoleLangRepository::REPOSITORY_KEY, $mockRoleLangRepository],
                [OrganizationRoleRepository::REPOSITORY_KEY, $organizationRoleRepository]
            ]);


            $apiValidationService = new APIValidationService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $apiValidationService->isOrganizationAPICoordinator($organizationId, $personId);
            $this->assertEquals($result, $expectedResult);
        }, ['examples' => [

            [
                // API Coordinator
                203,
                4878751,
                '6',
                1,
                true
            ],
            [
                // Not an api coordinator which throws AccessDeniedException
                203,
                4878750,
                '4',
                0,
                false
            ],
            [
                // API Coordinator
                196,
                4622478,
                '6',
                1,
                true
            ],
            [
                // Technical coordinator which throws AccessDeniedException
                196,
                4614716,
                '2',
                0,
                false
            ],
            [
                //Primary coordinator which throws AccessDeniedException
                196,
                4614025,
                '1',
                0,
                false
            ]


        ]]);
    }

    public function createIndividualAcademicUpdateDTO($riskLevel, $grade, $absence, $comments = '', $sendToStudent = false)
    {
        $academicUpdateDTO = new IndividualAcademicUpdateDTO();
        $academicUpdateDTO->setFailureRiskLevel($riskLevel);
        $academicUpdateDTO->setInProgressGrade($grade);
        $academicUpdateDTO->setAbsences($absence);
        $academicUpdateDTO->setComment($comments);
        $academicUpdateDTO->setSendToStudent($sendToStudent);
        return $academicUpdateDTO;
    }

    public function testIsDuplicateAcademicUpdate()
    {
        $this->specify("Is Duplicate Academic Update", function ($individualAcademicUpdateDTO, $previousAcademicUpdate, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $apiValidationService = new APIValidationService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $apiValidationService->isDuplicateAcademicUpdate($individualAcademicUpdateDTO, $previousAcademicUpdate);
            $this->assertEquals($result, $expectedResult);
        }, ['examples' => [
            [
                // IndividualAcademicUpdateDTO and expectedResult being same it returns true for duplicate
                $this->createIndividualAcademicUpdateDTO('Low', 'A', 1, 'comment1', true),
                [
                    'failure_risk_level' => 'Low',
                    'in_progress_grade' => 'A',
                    'absences' => 1,
                    'comment' => 'comment1',
                    'send_to_student' => true,
                    'final_grade' => null
                ],
                true
            ],
            [
                // IndividualAcademicUpdateDTO and expectedResult not being same it returns false for duplicate
                $this->createIndividualAcademicUpdateDTO('High', 'B', 2, 'comment2', false),
                [
                    'failure_risk_level' => 'Low',
                    'in_progress_grade' => 'B',
                    'absences' => 2,
                    'comment' => 'comment2',
                    'send_to_student' => false,
                    'final_grade' => 'C'
                ],
                false
            ]
        ]]);
    }

    public function testIsRequestSizeAllowed()
    {
        $this->specify("Test Is Request Size Allowed", function ($requestJSON, $key, $limitForPost, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            //Mocking EbiConfigService
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['get']);
            $mockEbiConfigService->method('get')->willReturn($limitForPost);

            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                ]);

            try {
                $apiValidationService = new APIValidationService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $apiValidationService->isRequestSizeAllowed($requestJSON, $key);
                $this->assertEquals($expectedResult, $result);
            } catch (SynapseException $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }
        },
            [
                'examples' =>
                    [
                        //Valid POST body size
                        [
                            '{
                                "courses_with_academic_updates": [{
                                    "course_id": "qa",
                                    "students_with_academic_updates": [{
                                        "student_id": "AU5",
                                        "academic_updates": [{
                                            "faculty_id_submitted": "43211",
                                            "in_progress_grade": "A",
                                            "comment": "NON PARTICIPATING",
                                            "send_to_student": "false"
                                        }]
                                        }]
                                }]
                            }',
                            'academic_updates',
                            10,
                            true
                        ],
                        // Will throw AccessDeniedException
                        [
                            '{
                                "courses_with_academic_updates": [{
                                    "course_id": "qa",
                                    "students_with_academic_updates": [{
                                        "student_id": "AU5",
                                        "academic_updates": [{
                                            "faculty_id_submitted": "43211",
                                            "in_progress_grade": "A",
                                            "comment": "PARTICIPATING",
                                            "send_to_student": "true"
                                        },{
                                            "faculty_id_submitted": "43212",
                                            "in_progress_grade": "B",
                                            "comment": "NON PARTICIPATING",
                                            "send_to_student": "true"
                                        },{
                                            "faculty_id_submitted": "43213",
                                            "in_progress_grade": "C",
                                            "comment": "NON PARTICIPATING",
                                            "send_to_student": "true"
                                        },{
                                            "faculty_id_submitted": "43214",
                                            "in_progress_grade": "A",
                                            "comment": "PARTICIPATING",
                                            "send_to_student": "false"
                                        }]
                                        }]
                                }]
                            }',
                            'academic_updates',
                            1,
                            'The body of your POST / PUT request has exceeded the maximum number of create / update records. Please make sure your request contains less than 1 records at the base level of the JSON body.'
                        ]
                    ]
            ]
        );
    }

    public function testAddErrorsToOrganizationAPIErrorCount()
    {
        $this->specify("Test Add Errors To Organization API Error Count", function ($organizationId, $validationErrors, $isInternalIds, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockCache = $this->getMock('cache', ['fetch', 'save']);
            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);

            //Repositories
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap(
                    [
                        [
                            EbiConfigRepository::REPOSITORY_KEY,
                            $mockEbiConfigRepository
                        ]
                    ]
                );
            $mockContainer->method('get')
                ->willReturnMap(
                    [
                        [
                            'synapse_redis_cache',
                            $mockCache
                        ],
                    ]);
            $mockCache->method('fetch')->willReturn(0);
            $mockEbiConfig = $this->getMock('EbiConfig', ['getValue']);
            $mockEbiConfig->method('getValue')->willReturn(500);
            $mockEbiConfigRepository->method('findOneBy')->willReturn($mockEbiConfig);

            try {
                $apiValidationService = new APIValidationService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $apiValidationService->addErrorsToOrganizationAPIErrorCount($organizationId, $validationErrors, $isInternalIds);
                $this->assertEquals($expectedResult, $result);
            } catch (SynapseValidationException $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }
        },
            [
                'examples' =>
                    [
                        //Case1 : Invalid Group Id Exception Error thrown for Is Internal true
                        [
                            2,
                            [
                                0 =>
                                    [
                                        'Athletics11' => "Group ID Athletics11 is not valid at the organization."
                                    ]
                            ],
                            true,
                            "Group ID Athletics11 is not valid at the organization."
                        ],
                        //Case 2 : No Error thrown for empty or no validation error , returned true
                        [
                            2,
                            [],
                            true,
                            true
                        ],
                        //Case 3 : Invalid Group Id Exception Error thrown for multiple groups for Is Internal false
                        [
                            2,
                            [
                                0 =>
                                    [
                                        'Athletics11' => "Group ID Athletics11 is not valid at the organization."
                                    ],
                                1 =>
                                    [
                                        'Advising' => "Group ID Advising is not valid at the organization."
                                    ]
                            ],
                            false,
                            "Group ID Athletics11 is not valid at the organization.,Group ID Advising is not valid at the organization."
                        ]
                    ]
            ]
        );
    }
}
