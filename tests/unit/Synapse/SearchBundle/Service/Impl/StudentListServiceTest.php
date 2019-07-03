<?php
namespace Synapse\SearchBundle\Service\Impl;

use Codeception\Specify;
use Codeception\Test\Unit;
use JMS\DiExtraBundle\Annotation as DI;

class StudentListServiceTest extends Unit
{
    use Specify;

    
    private $loggedInUserId = 108040;

    private $organizationId = 46;

    private $classLevelEbiMetadataId = 56;

    private $timeZoneString = 'US/Central';

    private $grayRiskImageName = 'risk-level-icon-gray.png';

    private $studentList1Ids = [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077];

    private $studentList2Ids = [397502,767634,4775483,4775641,4776313,4777077];

    private $studentList3Ids = [1021208,4776996];

    private $studentList4Ids = [767634,4775483,4776313,4777077];

    private $studentList5Ids = [397502,1021208,4775641,4776996];

    private $studentList6Ids = [767634,1021208,4775483,4776313,4777077];

    private $studentList7Ids = [397502,4775641,4776996];


    private $studentList1SortedByName = [
        [
            'student_id' => 397502,
            'student_first_name' => 'Lee',
            'student_last_name' => 'Ballard',
            'student_risk_status' => 'gray',
            'student_risk_image_name' => 'risk-level-icon-gray.png',
            'student_intent_to_leave' => 'gray',
            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
        ],
        [
            'student_id' => 4775483,
            'student_first_name' => 'Cristiano',
            'student_last_name' => 'Bauer',
            'student_risk_status' => 'green',
            'student_risk_image_name' => 'risk-level-icon-g.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4777077,
            'student_first_name' => 'Case',
            'student_last_name' => 'Bennett',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 767634,
            'student_first_name' => 'Audrey',
            'student_last_name' => 'Booth',
            'student_risk_status' => 'red2',
            'student_risk_image_name' => 'risk-level-icon-r2.png',
            'student_intent_to_leave' => 'dark gray',
            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
        ],
        [
            'student_id' => 4775641,
            'student_first_name' => 'Eleanor',
            'student_last_name' => 'Brennan',
            'student_risk_status' => 'yellow',
            'student_risk_image_name' => 'risk-level-icon-y.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4776996,
            'student_first_name' => 'Brennan',
            'student_last_name' => 'Duarte',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'red',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
        ],
        [
            'student_id' => 4776313,
            'student_first_name' => 'Aliana',
            'student_last_name' => 'Frazier',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'red',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
        ],
        [
            'student_id' => 1021208,
            'student_first_name' => 'Forrest',
            'student_last_name' => 'Lane',
            'student_risk_status' => 'green',
            'student_risk_image_name' => 'risk-level-icon-g.png',
            'student_intent_to_leave' => 'yellow',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png'
        ]
    ];

    private $studentList1SortedByRisk = [
        [
            'student_id' => 4775483,
            'student_first_name' => 'Cristiano',
            'student_last_name' => 'Bauer',
            'student_risk_status' => 'green',
            'student_risk_image_name' => 'risk-level-icon-g.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4775641,
            'student_first_name' => 'Eleanor',
            'student_last_name' => 'Brennan',
            'student_risk_status' => 'yellow',
            'student_risk_image_name' => 'risk-level-icon-y.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 1021208,
            'student_first_name' => 'Forrest',
            'student_last_name' => 'Lane',
            'student_risk_status' => 'green',
            'student_risk_image_name' => 'risk-level-icon-g.png',
            'student_intent_to_leave' => 'yellow',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png'
        ],
        [
            'student_id' => 4777077,
            'student_first_name' => 'Case',
            'student_last_name' => 'Bennett',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4776996,
            'student_first_name' => 'Brennan',
            'student_last_name' => 'Duarte',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'red',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
        ],
        [
            'student_id' => 4776313,
            'student_first_name' => 'Aliana',
            'student_last_name' => 'Frazier',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'red',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
        ],
        [
            'student_id' => 767634,
            'student_first_name' => 'Audrey',
            'student_last_name' => 'Booth',
            'student_risk_status' => 'red2',
            'student_risk_image_name' => 'risk-level-icon-r2.png',
            'student_intent_to_leave' => 'dark gray',
            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
        ],
        [
            'student_id' => 397502,
            'student_first_name' => 'Lee',
            'student_last_name' => 'Ballard',
            'student_risk_status' => 'gray',
            'student_risk_image_name' => 'risk-level-icon-gray.png',
            'student_intent_to_leave' => 'gray',
            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
        ]
    ];

    private $studentList2SortedByRisk = [
        [
            'student_id' => 4775483,
            'student_first_name' => 'Cristiano',
            'student_last_name' => 'Bauer',
            'student_risk_status' => 'green',
            'student_risk_image_name' => 'risk-level-icon-g.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4775641,
            'student_first_name' => 'Eleanor',
            'student_last_name' => 'Brennan',
            'student_risk_status' => 'yellow',
            'student_risk_image_name' => 'risk-level-icon-y.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4777077,
            'student_first_name' => 'Case',
            'student_last_name' => 'Bennett',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4776313,
            'student_first_name' => 'Aliana',
            'student_last_name' => 'Frazier',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'red',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
        ],
        [
            'student_id' => 767634,
            'student_first_name' => 'Audrey',
            'student_last_name' => 'Booth',
            'student_risk_status' => 'red2',
            'student_risk_image_name' => 'risk-level-icon-r2.png',
            'student_intent_to_leave' => 'dark gray',
            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
        ],
        [
            'student_id' => 397502,
            'student_first_name' => 'Lee',
            'student_last_name' => 'Ballard',
            'student_risk_status' => 'gray',
            'student_risk_image_name' => 'risk-level-icon-gray.png',
            'student_intent_to_leave' => 'gray',
            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
        ]
    ];

    private $studentList2SortedByIntentToLeave = [
        [
            'student_id' => 4775483,
            'student_first_name' => 'Cristiano',
            'student_last_name' => 'Bauer',
            'student_risk_status' => 'green',
            'student_risk_image_name' => 'risk-level-icon-g.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4775641,
            'student_first_name' => 'Eleanor',
            'student_last_name' => 'Brennan',
            'student_risk_status' => 'yellow',
            'student_risk_image_name' => 'risk-level-icon-y.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4777077,
            'student_first_name' => 'Case',
            'student_last_name' => 'Bennett',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4776313,
            'student_first_name' => 'Aliana',
            'student_last_name' => 'Frazier',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'red',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
        ],
        [
            'student_id' => 397502,
            'student_first_name' => 'Lee',
            'student_last_name' => 'Ballard',
            'student_risk_status' => 'gray',
            'student_risk_image_name' => 'risk-level-icon-gray.png',
            'student_intent_to_leave' => 'gray',
            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
        ],
        [
            'student_id' => 767634,
            'student_first_name' => 'Audrey',
            'student_last_name' => 'Booth',
            'student_risk_status' => 'red2',
            'student_risk_image_name' => 'risk-level-icon-r2.png',
            'student_intent_to_leave' => 'dark gray',
            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
        ]
    ];

    private $studentList3SortedByName = [
        [
            'student_id' => 4776996,
            'student_first_name' => 'Brennan',
            'student_last_name' => 'Duarte',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'red',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
        ],
        [
            'student_id' => 1021208,
            'student_first_name' => 'Forrest',
            'student_last_name' => 'Lane',
            'student_risk_status' => 'green',
            'student_risk_image_name' => 'risk-level-icon-g.png',
            'student_intent_to_leave' => 'yellow',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png'
        ]
    ];

    private $studentList4SortedByRisk = [
        [
            'student_id' => 4775483,
            'student_first_name' => 'Cristiano',
            'student_last_name' => 'Bauer',
            'student_risk_status' => 'green',
            'student_risk_image_name' => 'risk-level-icon-g.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4777077,
            'student_first_name' => 'Case',
            'student_last_name' => 'Bennett',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4776313,
            'student_first_name' => 'Aliana',
            'student_last_name' => 'Frazier',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'red',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
        ],
        [
            'student_id' => 767634,
            'student_first_name' => 'Audrey',
            'student_last_name' => 'Booth',
            'student_risk_status' => 'red2',
            'student_risk_image_name' => 'risk-level-icon-r2.png',
            'student_intent_to_leave' => 'dark gray',
            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
        ]
    ];

    private $studentList4SortedByIntentToLeave = [
        [
            'student_id' => 4775483,
            'student_first_name' => 'Cristiano',
            'student_last_name' => 'Bauer',
            'student_risk_status' => 'green',
            'student_risk_image_name' => 'risk-level-icon-g.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4777077,
            'student_first_name' => 'Case',
            'student_last_name' => 'Bennett',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4776313,
            'student_first_name' => 'Aliana',
            'student_last_name' => 'Frazier',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'red',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
        ],
        [
            'student_id' => 767634,
            'student_first_name' => 'Audrey',
            'student_last_name' => 'Booth',
            'student_risk_status' => 'red2',
            'student_risk_image_name' => 'risk-level-icon-r2.png',
            'student_intent_to_leave' => 'dark gray',
            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
        ]
    ];

    private $studentList5SortedByName = [
        [
            'student_id' => 397502,
            'student_first_name' => 'Lee',
            'student_last_name' => 'Ballard',
            'student_risk_status' => 'gray',
            'student_risk_image_name' => 'risk-level-icon-gray.png',
            'student_intent_to_leave' => 'gray',
            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
        ],
        [
            'student_id' => 4775641,
            'student_first_name' => 'Eleanor',
            'student_last_name' => 'Brennan',
            'student_risk_status' => 'yellow',
            'student_risk_image_name' => 'risk-level-icon-y.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4776996,
            'student_first_name' => 'Brennan',
            'student_last_name' => 'Duarte',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'red',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
        ],
        [
            'student_id' => 1021208,
            'student_first_name' => 'Forrest',
            'student_last_name' => 'Lane',
            'student_risk_status' => 'green',
            'student_risk_image_name' => 'risk-level-icon-g.png',
            'student_intent_to_leave' => 'yellow',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png'
        ]
    ];

    private $studentList6SortedByRisk = [
        [
            'student_id' => 4775483,
            'student_first_name' => 'Cristiano',
            'student_last_name' => 'Bauer',
            'student_risk_status' => 'green',
            'student_risk_image_name' => 'risk-level-icon-g.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 1021208,
            'student_first_name' => 'Forrest',
            'student_last_name' => 'Lane',
            'student_risk_status' => 'green',
            'student_risk_image_name' => 'risk-level-icon-g.png',
            'student_intent_to_leave' => 'yellow',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png'
        ],
        [
            'student_id' => 4777077,
            'student_first_name' => 'Case',
            'student_last_name' => 'Bennett',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4776313,
            'student_first_name' => 'Aliana',
            'student_last_name' => 'Frazier',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'red',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
        ],
        [
            'student_id' => 767634,
            'student_first_name' => 'Audrey',
            'student_last_name' => 'Booth',
            'student_risk_status' => 'red2',
            'student_risk_image_name' => 'risk-level-icon-r2.png',
            'student_intent_to_leave' => 'dark gray',
            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
        ]
    ];

    private $studentList7SortedByName = [
        [
            'student_id' => 397502,
            'student_first_name' => 'Lee',
            'student_last_name' => 'Ballard',
            'student_risk_status' => 'gray',
            'student_risk_image_name' => 'risk-level-icon-gray.png',
            'student_intent_to_leave' => 'gray',
            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
        ],
        [
            'student_id' => 4775641,
            'student_first_name' => 'Eleanor',
            'student_last_name' => 'Brennan',
            'student_risk_status' => 'yellow',
            'student_risk_image_name' => 'risk-level-icon-y.png',
            'student_intent_to_leave' => 'green',
            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
        ],
        [
            'student_id' => 4776996,
            'student_first_name' => 'Brennan',
            'student_last_name' => 'Duarte',
            'student_risk_status' => 'red',
            'student_risk_image_name' => 'risk-level-icon-r1.png',
            'student_intent_to_leave' => 'red',
            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
        ]
    ];



    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRepositoryResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContainer;


    protected function _before()
    {
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
        $this->mockLogger = $this->getMock('Logger', ['debug','error']);
        $this->mockContainer = $this->getMock('Container', ['get']);
    }


    public function testGetStudentListWithAdditionalData()
    {
        $this->specify("Verify the functionality of the method getStudentListWithAdditionalData", function($studentIds, $sortBy, $pageNumber, $recordsPerPage, $riskPermission, $intentToLeavePermission, $expectedResult) {

            // Declaring mock services, DAOs, and repositories
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['getOrganizationISOTimeZone']);
            $mockStudentListDAO = $this->getMock('StudentListDAO', ['getAdditionalStudentData']);
            $mockEbiMetadataRepository = $this->getMock('EbiMetadataRepository', ['findOneBy']);
            $mockOrgPermissionsetRepository = $this->getMock('OrgPermissionsetRepository', ['getRiskPermissionForFacultyAndStudents', 'getIntentToLeavePermissionForFacultyAndStudents']);
            $mockRiskLevelsRepository = $this->getMock('RiskLevelsRepository', ['findOneBy']);

            // Declaring mock objects
            $mockEbiMetadataObject = $this->getMock('EbiMetadata', ['getId']);
            $mockRiskLevelsObject = $this->getMock('RiskLevels', ['getImageName']);

            // Mocking method calls
            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
                ['SynapseCoreBundle:EbiMetadata', $mockEbiMetadataRepository],
                ['SynapseCoreBundle:OrgPermissionset', $mockOrgPermissionsetRepository],
                ['SynapseRiskBundle:RiskLevels', $mockRiskLevelsRepository]
            ]);

            $this->mockContainer->method('get')->willReturnMap([
                ['date_utility_service', $mockDateUtilityService],
                ['student_list_dao', $mockStudentListDAO]
            ]);

            $mockEbiMetadataRepository->method('findOneBy')->willReturn($mockEbiMetadataObject);
            $mockEbiMetadataObject->method('getId')->willReturn($this->classLevelEbiMetadataId);

            $mockDateUtilityService->method('getOrganizationISOTimeZone')->willReturn($this->timeZoneString);

            $mockRiskLevelsRepository->method('findOneBy')->willReturn($mockRiskLevelsObject);
            $mockRiskLevelsObject->method('getImageName')->willReturn($this->grayRiskImageName);

            $mockOrgPermissionsetRepository->method('getRiskPermissionForFacultyAndStudents')->willReturn($riskPermission);
            $mockOrgPermissionsetRepository->method('getIntentToLeavePermissionForFacultyAndStudents')->willReturn($intentToLeavePermission);

            $mockStudentListDAO->method('getAdditionalStudentData')->will($this->returnValueMap([
                [$this->studentList1Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, '+student_last_name', 0, 5, array_slice($this->studentList1SortedByName, 0, 5)],
                [$this->studentList1Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, 'student_last_name', 0, 5, array_slice($this->studentList1SortedByName, 0, 5)],
                [$this->studentList1Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, '+student_last_name', 5, 5, array_slice($this->studentList1SortedByName, 5, 5)],
                [$this->studentList1Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, '+student_risk_status', 0, 5, array_slice($this->studentList1SortedByRisk, 0, 5)],
                [$this->studentList1Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, '+student_risk_status', 5, 5, array_slice($this->studentList1SortedByRisk, 5, 5)],
                [$this->studentList2Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, '+student_risk_status', 0, 5, array_slice($this->studentList2SortedByRisk, 0, 5)],
                [$this->studentList2Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, '+student_risk_status', 5, 5, array_slice($this->studentList2SortedByRisk, 5, 5)],
                [$this->studentList3Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, 'student_last_name', 0, 4, array_slice($this->studentList3SortedByName, 0, 4)],
                [$this->studentList4Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, '+student_risk_status', 0, 5, array_slice($this->studentList4SortedByRisk, 0, 5)],
                [$this->studentList5Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, 'student_last_name', 0, 1, array_slice($this->studentList5SortedByName, 0, 1)],
                [$this->studentList5Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, 'student_last_name', 1, 5, array_slice($this->studentList5SortedByName, 1, 5)],
                [$this->studentList6Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, '+student_risk_status', 0, 5, array_slice($this->studentList6SortedByRisk, 0, 5)],
                [$this->studentList7Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, 'student_last_name', 0, 5, array_slice($this->studentList7SortedByName, 0, 5)],
                [$this->studentList4Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, '+student_intent_to_leave', 0, 5, array_slice($this->studentList4SortedByIntentToLeave, 0, 5)],
            ]));

            // Call the function to be tested and verify results.
            $studentListService = new StudentListService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $output = $studentListService->getStudentListWithAdditionalData($studentIds, $this->loggedInUserId, $this->organizationId, $sortBy, $pageNumber, $recordsPerPage);

            $this->assertEquals($expectedResult, $output);

        }, ['examples' =>
            [
                // Example 1a:  Sorting by name, first page.
                // Note that the null risk in the output of the DAO has been replaced by gray.
                // Also note that the risk or intent to leave has been replaced with null for particular students
                // when the user doesn't have these permissions for these students.
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_last_name',
                    1,
                    5,
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 0,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 0
                    ],
                    [
                        [
                            'student_id' => 397502,
                            'student_first_name' => 'Lee',
                            'student_last_name' => 'Ballard',
                            'student_risk_status' => 'gray',
                            'student_risk_image_name' => 'risk-level-icon-gray.png',
                            'student_intent_to_leave' => 'gray',
                            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
                        ],
                        [
                            'student_id' => 4775483,
                            'student_first_name' => 'Cristiano',
                            'student_last_name' => 'Bauer',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4777077,
                            'student_first_name' => 'Case',
                            'student_last_name' => 'Bennett',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => null,
                            'student_intent_to_leave_image_name' => null
                        ],
                        [
                            'student_id' => 767634,
                            'student_first_name' => 'Audrey',
                            'student_last_name' => 'Booth',
                            'student_risk_status' => 'red2',
                            'student_risk_image_name' => 'risk-level-icon-r2.png',
                            'student_intent_to_leave' => 'dark gray',
                            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
                        ],
                        [
                            'student_id' => 4775641,
                            'student_first_name' => 'Eleanor',
                            'student_last_name' => 'Brennan',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ]
                    ]
                ],
                // Example 1b:  Sorting by name, second page.
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_last_name',
                    2,
                    5,
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 0,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 0
                    ],
                    [
                        [
                            'student_id' => 4776996,
                            'student_first_name' => 'Brennan',
                            'student_last_name' => 'Duarte',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 4776313,
                            'student_first_name' => 'Aliana',
                            'student_last_name' => 'Frazier',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 1021208,
                            'student_first_name' => 'Forrest',
                            'student_last_name' => 'Lane',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'yellow',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png'
                        ]
                    ]
                ],
                // Example 2a:  Sorting by risk, first page, with permission to view risk for all the students.
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_risk_status',
                    1,
                    5,
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 0
                    ],
                    [
                        [
                            'student_id' => 4775483,
                            'student_first_name' => 'Cristiano',
                            'student_last_name' => 'Bauer',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4775641,
                            'student_first_name' => 'Eleanor',
                            'student_last_name' => 'Brennan',
                            'student_risk_status' => 'yellow',
                            'student_risk_image_name' => 'risk-level-icon-y.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 1021208,
                            'student_first_name' => 'Forrest',
                            'student_last_name' => 'Lane',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'yellow',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png'
                        ],
                        [
                            'student_id' => 4777077,
                            'student_first_name' => 'Case',
                            'student_last_name' => 'Bennett',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => null,
                            'student_intent_to_leave_image_name' => null
                        ],
                        [
                            'student_id' => 4776996,
                            'student_first_name' => 'Brennan',
                            'student_last_name' => 'Duarte',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ]
                    ]
                ],
                // Example 2b:  Sorting by risk, second page, with permission to view risk for all the students.
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_risk_status',
                    2,
                    5,
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 0
                    ],
                    [
                        [
                            'student_id' => 4776313,
                            'student_first_name' => 'Aliana',
                            'student_last_name' => 'Frazier',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 767634,
                            'student_first_name' => 'Audrey',
                            'student_last_name' => 'Booth',
                            'student_risk_status' => 'red2',
                            'student_risk_image_name' => 'risk-level-icon-r2.png',
                            'student_intent_to_leave' => 'dark gray',
                            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
                        ],
                        [
                            'student_id' => 397502,
                            'student_first_name' => 'Lee',
                            'student_last_name' => 'Ballard',
                            'student_risk_status' => 'gray',
                            'student_risk_image_name' => 'risk-level-icon-gray.png',
                            'student_intent_to_leave' => 'gray',
                            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
                        ]
                    ]
                ],
                // Example 3a:  Sorting by risk, first page, without permission to view risk for a couple students (but able to see risk for more than the first page).
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_risk_status',
                    1,
                    5,
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 0,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 0,
                        4777077 => 1
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        [
                            'student_id' => 4775483,
                            'student_first_name' => 'Cristiano',
                            'student_last_name' => 'Bauer',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4775641,
                            'student_first_name' => 'Eleanor',
                            'student_last_name' => 'Brennan',
                            'student_risk_status' => 'yellow',
                            'student_risk_image_name' => 'risk-level-icon-y.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4777077,
                            'student_first_name' => 'Case',
                            'student_last_name' => 'Bennett',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4776313,
                            'student_first_name' => 'Aliana',
                            'student_last_name' => 'Frazier',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 767634,
                            'student_first_name' => 'Audrey',
                            'student_last_name' => 'Booth',
                            'student_risk_status' => 'red2',
                            'student_risk_image_name' => 'risk-level-icon-r2.png',
                            'student_intent_to_leave' => 'dark gray',
                            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
                        ]
                    ]
                ],
                // Example 3b:  Sorting by risk, second page, without permission to view risk for a couple students (but able to see risk for more than the first page).
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_risk_status',
                    2,
                    5,
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 0,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 0,
                        4777077 => 1
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        [
                            'student_id' => 397502,
                            'student_first_name' => 'Lee',
                            'student_last_name' => 'Ballard',
                            'student_risk_status' => 'gray',
                            'student_risk_image_name' => 'risk-level-icon-gray.png',
                            'student_intent_to_leave' => 'gray',
                            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
                        ],
                        [
                            'student_id' => 4776996,
                            'student_first_name' => 'Brennan',
                            'student_last_name' => 'Duarte',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 1021208,
                            'student_first_name' => 'Forrest',
                            'student_last_name' => 'Lane',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'yellow',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png'
                        ]
                    ]
                ],
                // Example 4a:  Sorting by risk, first page, with permission to view risk for only a few students (less than fits on the first page).
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_risk_status',
                    1,
                    5,
                    [
                        397502 => 0,
                        767634 => 1,
                        1021208 => 0,
                        4775483 => 1,
                        4775641 => 0,
                        4776313 => 1,
                        4776996 => 0,
                        4777077 => 1
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        [
                            'student_id' => 4775483,
                            'student_first_name' => 'Cristiano',
                            'student_last_name' => 'Bauer',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4777077,
                            'student_first_name' => 'Case',
                            'student_last_name' => 'Bennett',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4776313,
                            'student_first_name' => 'Aliana',
                            'student_last_name' => 'Frazier',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 767634,
                            'student_first_name' => 'Audrey',
                            'student_last_name' => 'Booth',
                            'student_risk_status' => 'red2',
                            'student_risk_image_name' => 'risk-level-icon-r2.png',
                            'student_intent_to_leave' => 'dark gray',
                            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
                        ],
                        [
                            'student_id' => 397502,
                            'student_first_name' => 'Lee',
                            'student_last_name' => 'Ballard',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'gray',
                            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
                        ]
                    ]
                ],
                // Example 4b:  Sorting by risk, second page, with permission to view risk for only a few students (less than fits on the first page).
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_risk_status',
                    2,
                    5,
                    [
                        397502 => 0,
                        767634 => 1,
                        1021208 => 0,
                        4775483 => 1,
                        4775641 => 0,
                        4776313 => 1,
                        4776996 => 0,
                        4777077 => 1
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        [
                            'student_id' => 4775641,
                            'student_first_name' => 'Eleanor',
                            'student_last_name' => 'Brennan',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4776996,
                            'student_first_name' => 'Brennan',
                            'student_last_name' => 'Duarte',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 1021208,
                            'student_first_name' => 'Forrest',
                            'student_last_name' => 'Lane',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'yellow',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png'
                        ]
                    ]
                ],
                // Example 5a:  Sorting by risk, first page, with permission to view risk for exactly a page of students.
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_risk_status',
                    1,
                    5,
                    [
                        397502 => 0,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 0,
                        4776313 => 1,
                        4776996 => 0,
                        4777077 => 1
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        [
                            'student_id' => 4775483,
                            'student_first_name' => 'Cristiano',
                            'student_last_name' => 'Bauer',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 1021208,
                            'student_first_name' => 'Forrest',
                            'student_last_name' => 'Lane',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'yellow',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png'
                        ],
                        [
                            'student_id' => 4777077,
                            'student_first_name' => 'Case',
                            'student_last_name' => 'Bennett',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4776313,
                            'student_first_name' => 'Aliana',
                            'student_last_name' => 'Frazier',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 767634,
                            'student_first_name' => 'Audrey',
                            'student_last_name' => 'Booth',
                            'student_risk_status' => 'red2',
                            'student_risk_image_name' => 'risk-level-icon-r2.png',
                            'student_intent_to_leave' => 'dark gray',
                            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
                        ]
                    ]
                ],
                // Example 5b:  Sorting by risk, second page, with permission to view risk for exactly a page of students.
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_risk_status',
                    2,
                    5,
                    [
                        397502 => 0,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 0,
                        4776313 => 1,
                        4776996 => 0,
                        4777077 => 1
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        [
                            'student_id' => 397502,
                            'student_first_name' => 'Lee',
                            'student_last_name' => 'Ballard',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'gray',
                            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
                        ],
                        [
                            'student_id' => 4775641,
                            'student_first_name' => 'Eleanor',
                            'student_last_name' => 'Brennan',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4776996,
                            'student_first_name' => 'Brennan',
                            'student_last_name' => 'Duarte',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ]
                    ]
                ],
                // Example 6:  Sorting by risk, first page, without permission to view risk for any students.
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_risk_status',
                    1,
                    5,
                    [
                        397502 => 0,
                        767634 => 0,
                        1021208 => 0,
                        4775483 => 0,
                        4775641 => 0,
                        4776313 => 0,
                        4776996 => 0,
                        4777077 => 0
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        [
                            'student_id' => 397502,
                            'student_first_name' => 'Lee',
                            'student_last_name' => 'Ballard',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'gray',
                            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
                        ],
                        [
                            'student_id' => 4775483,
                            'student_first_name' => 'Cristiano',
                            'student_last_name' => 'Bauer',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4777077,
                            'student_first_name' => 'Case',
                            'student_last_name' => 'Bennett',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 767634,
                            'student_first_name' => 'Audrey',
                            'student_last_name' => 'Booth',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'dark gray',
                            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
                        ],
                        [
                            'student_id' => 4775641,
                            'student_first_name' => 'Eleanor',
                            'student_last_name' => 'Brennan',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ]
                    ]
                ],
                // Example 7a:  Sorting by intent to leave, first page, with permission to view intent to leave for only a few students (less than fits on the first page).
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_intent_to_leave',
                    1,
                    5,
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        397502 => 0,
                        767634 => 1,
                        1021208 => 0,
                        4775483 => 1,
                        4775641 => 0,
                        4776313 => 1,
                        4776996 => 0,
                        4777077 => 1
                    ],
                    [
                        [
                            'student_id' => 4775483,
                            'student_first_name' => 'Cristiano',
                            'student_last_name' => 'Bauer',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4777077,
                            'student_first_name' => 'Case',
                            'student_last_name' => 'Bennett',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4776313,
                            'student_first_name' => 'Aliana',
                            'student_last_name' => 'Frazier',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 767634,
                            'student_first_name' => 'Audrey',
                            'student_last_name' => 'Booth',
                            'student_risk_status' => 'red2',
                            'student_risk_image_name' => 'risk-level-icon-r2.png',
                            'student_intent_to_leave' => 'dark gray',
                            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
                        ],
                        [
                            'student_id' => 397502,
                            'student_first_name' => 'Lee',
                            'student_last_name' => 'Ballard',
                            'student_risk_status' => 'gray',
                            'student_risk_image_name' => 'risk-level-icon-gray.png',
                            'student_intent_to_leave' => null,
                            'student_intent_to_leave_image_name' => null
                        ]
                    ]
                ],
                // Example 7b:  Sorting by intent to leave, second page, with permission to view intent to leave for only a few students (less than fits on the first page).
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_intent_to_leave',
                    2,
                    5,
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        397502 => 0,
                        767634 => 1,
                        1021208 => 0,
                        4775483 => 1,
                        4775641 => 0,
                        4776313 => 1,
                        4776996 => 0,
                        4777077 => 1
                    ],
                    [
                        [
                            'student_id' => 4775641,
                            'student_first_name' => 'Eleanor',
                            'student_last_name' => 'Brennan',
                            'student_risk_status' => 'yellow',
                            'student_risk_image_name' => 'risk-level-icon-y.png',
                            'student_intent_to_leave' => null,
                            'student_intent_to_leave_image_name' => null
                        ],
                        [
                            'student_id' => 4776996,
                            'student_first_name' => 'Brennan',
                            'student_last_name' => 'Duarte',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => null,
                            'student_intent_to_leave_image_name' => null
                        ],
                        [
                            'student_id' => 1021208,
                            'student_first_name' => 'Forrest',
                            'student_last_name' => 'Lane',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => null,
                            'student_intent_to_leave_image_name' => null
                        ]
                    ]
                ]
            ]
        ]);
    }


    public function testGetStudentListWithAdditionalDataForCSV()
    {
        $this->specify("Verify the functionality of the method getStudentListWithAdditionalDataForCSV", function($studentIds, $sortBy, $riskPermission, $intentToLeavePermission, $expectedResult) {

            // Declaring mock services, DAOs, and repositories
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['getOrganizationISOTimeZone']);
            $mockStudentListDAO = $this->getMock('StudentListDAO', ['getAdditionalStudentData']);
            $mockEbiMetadataRepository = $this->getMock('EbiMetadataRepository', ['findOneBy']);
            $mockOrgPermissionsetRepository = $this->getMock('OrgPermissionsetRepository', ['getRiskPermissionForFacultyAndStudents', 'getIntentToLeavePermissionForFacultyAndStudents']);
            $mockRiskLevelsRepository = $this->getMock('RiskLevelsRepository', ['findOneBy']);

            // Declaring mock objects
            $mockEbiMetadataObject = $this->getMock('EbiMetadata', ['getId']);
            $mockRiskLevelsObject = $this->getMock('RiskLevels', ['getImageName']);

            // Mocking method calls
            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
                ['SynapseCoreBundle:EbiMetadata', $mockEbiMetadataRepository],
                ['SynapseCoreBundle:OrgPermissionset', $mockOrgPermissionsetRepository],
                ['SynapseRiskBundle:RiskLevels', $mockRiskLevelsRepository]
            ]);

            $this->mockContainer->method('get')->willReturnMap([
                ['date_utility_service', $mockDateUtilityService],
                ['student_list_dao', $mockStudentListDAO]
            ]);

            $mockEbiMetadataRepository->method('findOneBy')->willReturn($mockEbiMetadataObject);
            $mockEbiMetadataObject->method('getId')->willReturn($this->classLevelEbiMetadataId);

            $mockDateUtilityService->method('getOrganizationISOTimeZone')->willReturn($this->timeZoneString);

            $mockRiskLevelsRepository->method('findOneBy')->willReturn($mockRiskLevelsObject);
            $mockRiskLevelsObject->method('getImageName')->willReturn($this->grayRiskImageName);

            $mockOrgPermissionsetRepository->method('getRiskPermissionForFacultyAndStudents')->willReturn($riskPermission);
            $mockOrgPermissionsetRepository->method('getIntentToLeavePermissionForFacultyAndStudents')->willReturn($intentToLeavePermission);

            $mockStudentListDAO->method('getAdditionalStudentData')->will($this->returnValueMap([
                [$this->studentList1Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, '+student_last_name', $this->studentList1SortedByName],
                [$this->studentList1Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, '+student_risk_status', $this->studentList1SortedByRisk],
                [[], $this->classLevelEbiMetadataId, $this->timeZoneString, 'student_last_name', []],
                [$this->studentList2Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, '+student_risk_status', $this->studentList2SortedByRisk],
                [$this->studentList3Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, 'student_last_name', $this->studentList3SortedByName],
                [$this->studentList2Ids, $this->classLevelEbiMetadataId, $this->timeZoneString, '+student_intent_to_leave', $this->studentList2SortedByIntentToLeave]
            ]));

            // Call the function to be tested and verify results.
            $studentListService = new StudentListService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $output = $studentListService->getStudentListWithAdditionalDataForCSV($studentIds, $this->loggedInUserId, $this->organizationId, $sortBy);

            verify($output)->equals($expectedResult);

        }, ['examples' =>
            [
                // Example 1:  Sorting by name.
                // Note that the null risk in the output of the DAO has been replaced by gray.
                // Also note that the risk or intent to leave has been replaced with null for particular students
                // when the user doesn't have these permissions for these students.
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_last_name',
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 0,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 0
                    ],
                    [
                        [
                            'student_id' => 397502,
                            'student_first_name' => 'Lee',
                            'student_last_name' => 'Ballard',
                            'student_risk_status' => 'gray',
                            'student_risk_image_name' => 'risk-level-icon-gray.png',
                            'student_intent_to_leave' => 'gray',
                            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
                        ],
                        [
                            'student_id' => 4775483,
                            'student_first_name' => 'Cristiano',
                            'student_last_name' => 'Bauer',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4777077,
                            'student_first_name' => 'Case',
                            'student_last_name' => 'Bennett',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => null,
                            'student_intent_to_leave_image_name' => null
                        ],
                        [
                            'student_id' => 767634,
                            'student_first_name' => 'Audrey',
                            'student_last_name' => 'Booth',
                            'student_risk_status' => 'red2',
                            'student_risk_image_name' => 'risk-level-icon-r2.png',
                            'student_intent_to_leave' => 'dark gray',
                            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
                        ],
                        [
                            'student_id' => 4775641,
                            'student_first_name' => 'Eleanor',
                            'student_last_name' => 'Brennan',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4776996,
                            'student_first_name' => 'Brennan',
                            'student_last_name' => 'Duarte',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 4776313,
                            'student_first_name' => 'Aliana',
                            'student_last_name' => 'Frazier',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 1021208,
                            'student_first_name' => 'Forrest',
                            'student_last_name' => 'Lane',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'yellow',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png'
                        ]
                    ]
                ],
                // Example 2:  Sorting by risk, with permission to view risk for all the students.
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_risk_status',
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        [
                            'student_id' => 4775483,
                            'student_first_name' => 'Cristiano',
                            'student_last_name' => 'Bauer',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4775641,
                            'student_first_name' => 'Eleanor',
                            'student_last_name' => 'Brennan',
                            'student_risk_status' => 'yellow',
                            'student_risk_image_name' => 'risk-level-icon-y.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 1021208,
                            'student_first_name' => 'Forrest',
                            'student_last_name' => 'Lane',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'yellow',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png'
                        ],
                        [
                            'student_id' => 4777077,
                            'student_first_name' => 'Case',
                            'student_last_name' => 'Bennett',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4776996,
                            'student_first_name' => 'Brennan',
                            'student_last_name' => 'Duarte',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 4776313,
                            'student_first_name' => 'Aliana',
                            'student_last_name' => 'Frazier',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 767634,
                            'student_first_name' => 'Audrey',
                            'student_last_name' => 'Booth',
                            'student_risk_status' => 'red2',
                            'student_risk_image_name' => 'risk-level-icon-r2.png',
                            'student_intent_to_leave' => 'dark gray',
                            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
                        ],
                        [
                            'student_id' => 397502,
                            'student_first_name' => 'Lee',
                            'student_last_name' => 'Ballard',
                            'student_risk_status' => 'gray',
                            'student_risk_image_name' => 'risk-level-icon-gray.png',
                            'student_intent_to_leave' => 'gray',
                            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
                        ]
                    ]
                ],
                // Example 3:  Sorting by risk, without permission to view risk for a couple students.
                // With the old approach, these students whose risk the user shouldn't be able to see
                // would have had null risk but still would have been in the risk sorting,
                // so it would have been obvious what their risk was.
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_risk_status',
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 0,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 0,
                        4777077 => 1
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        [
                            'student_id' => 4775483,
                            'student_first_name' => 'Cristiano',
                            'student_last_name' => 'Bauer',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4775641,
                            'student_first_name' => 'Eleanor',
                            'student_last_name' => 'Brennan',
                            'student_risk_status' => 'yellow',
                            'student_risk_image_name' => 'risk-level-icon-y.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4777077,
                            'student_first_name' => 'Case',
                            'student_last_name' => 'Bennett',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4776313,
                            'student_first_name' => 'Aliana',
                            'student_last_name' => 'Frazier',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 767634,
                            'student_first_name' => 'Audrey',
                            'student_last_name' => 'Booth',
                            'student_risk_status' => 'red2',
                            'student_risk_image_name' => 'risk-level-icon-r2.png',
                            'student_intent_to_leave' => 'dark gray',
                            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
                        ],
                        [
                            'student_id' => 397502,
                            'student_first_name' => 'Lee',
                            'student_last_name' => 'Ballard',
                            'student_risk_status' => 'gray',
                            'student_risk_image_name' => 'risk-level-icon-gray.png',
                            'student_intent_to_leave' => 'gray',
                            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
                        ],
                        [
                            'student_id' => 4776996,
                            'student_first_name' => 'Brennan',
                            'student_last_name' => 'Duarte',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 1021208,
                            'student_first_name' => 'Forrest',
                            'student_last_name' => 'Lane',
                            'student_risk_status' => null,
                            'student_risk_image_name' => null,
                            'student_intent_to_leave' => 'yellow',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png'
                        ]
                    ],

                ],
                // Example 4:  Sorting by intent to leave, without permission to view intent to leave for a couple students.
                [
                    [397502,767634,1021208,4775483,4775641,4776313,4776996,4777077],
                    '+student_intent_to_leave',
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 1,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 1,
                        4777077 => 1
                    ],
                    [
                        397502 => 1,
                        767634 => 1,
                        1021208 => 0,
                        4775483 => 1,
                        4775641 => 1,
                        4776313 => 1,
                        4776996 => 0,
                        4777077 => 1
                    ],
                    [
                        [
                            'student_id' => 4775483,
                            'student_first_name' => 'Cristiano',
                            'student_last_name' => 'Bauer',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4775641,
                            'student_first_name' => 'Eleanor',
                            'student_last_name' => 'Brennan',
                            'student_risk_status' => 'yellow',
                            'student_risk_image_name' => 'risk-level-icon-y.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4777077,
                            'student_first_name' => 'Case',
                            'student_last_name' => 'Bennett',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'green',
                            'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png'
                        ],
                        [
                            'student_id' => 4776313,
                            'student_first_name' => 'Aliana',
                            'student_last_name' => 'Frazier',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => 'red',
                            'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png'
                        ],
                        [
                            'student_id' => 397502,
                            'student_first_name' => 'Lee',
                            'student_last_name' => 'Ballard',
                            'student_risk_status' => 'gray',
                            'student_risk_image_name' => 'risk-level-icon-gray.png',
                            'student_intent_to_leave' => 'gray',
                            'student_intent_to_leave_image_name' => 'leave-intent-not-stated.png'
                        ],
                        [
                            'student_id' => 767634,
                            'student_first_name' => 'Audrey',
                            'student_last_name' => 'Booth',
                            'student_risk_status' => 'red2',
                            'student_risk_image_name' => 'risk-level-icon-r2.png',
                            'student_intent_to_leave' => 'dark gray',
                            'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png'
                        ],
                        [
                            'student_id' => 4776996,
                            'student_first_name' => 'Brennan',
                            'student_last_name' => 'Duarte',
                            'student_risk_status' => 'red',
                            'student_risk_image_name' => 'risk-level-icon-r1.png',
                            'student_intent_to_leave' => null,
                            'student_intent_to_leave_image_name' => null
                        ],
                        [
                            'student_id' => 1021208,
                            'student_first_name' => 'Forrest',
                            'student_last_name' => 'Lane',
                            'student_risk_status' => 'green',
                            'student_risk_image_name' => 'risk-level-icon-g.png',
                            'student_intent_to_leave' => null,
                            'student_intent_to_leave_image_name' => null
                        ]
                    ],
                ]
            ]
        ]);
    }

}