<?php
namespace Synapse\ReportsBundle\Service\Impl;

use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\MapworksActionService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\ReportsBundle\Entity\OrgCalcFlagsStudentReports;
use Synapse\ReportsBundle\Repository\OrgCalcFlagsStudentReportsRepository;

class StudentReportEmailServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    private $studentDetail = [
        '$$student_first_name$$' => 'Brianna',
        '$$student_last_name$$' => 'Robinson',
        '$$student_email_address$$' => 'Brianna.Robinson@mailinator.com'
    ];

    private $coordinatorDetail = [
        '$$coordinator_first_name$$' => 'ankur',
        '$$coordinator_last_name$$' => 'aditya',
        '$$coordinator_email_address$$' => 'ankuraditya@mailinator.com',
        '$$coordinator_title$$' => 'coordinator'
    ];

    private $currentAcademicYear = [
        'org_academic_year_id' => 21,
        'year_id' => '201718'
    ];

    public function testMapStudentReportToTokenVariables()
    {
        $this->specify("Test map student report to token variables function", function ($studentId, $organizationId, $orgCalcFlagsStudentReportsId, $errorType, $expectedResult) {
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

            //mocking services
            $mockAcademicYearService = $this->getMock('AcademicYearService', ['findCurrentAcademicYearForOrganization']);
            $mockMapworksActionService = $this->getMock('MapworksActionService', ['getTokenVariablesFromPerson']);
            $mockPersonService = $this->getMock('PersonService', ['getFirstPrimaryCoordinatorPerson']);
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['getSystemUrl']);

            //mocking repositories
            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);

            $mockContainer->method('get')
                ->willReturnMap(
                    [
                        [
                            AcademicYearService::SERVICE_KEY,
                            $mockAcademicYearService
                        ],
                        [
                            MapworksActionService::SERVICE_KEY,
                            $mockMapworksActionService
                        ],
                        [
                            PersonService::SERVICE_KEY,
                            $mockPersonService
                        ],
                        [
                            EbiConfigService::SERVICE_KEY,
                            $mockEbiConfigService
                        ]
                    ]
                );

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap(
                    [
                        [
                            EbiConfigRepository::REPOSITORY_KEY,
                            $mockEbiConfigRepository
                        ]
                    ]
                );
            $student = new Person();
            $student->setId($studentId);

            $orgCalcFlagsStudentReportsEntity = new OrgCalcFlagsStudentReports();
            $orgCalcFlagsStudentReportsEntity->setId($orgCalcFlagsStudentReportsId);
            $orgCalcFlagsStudentReportsEntity->setFileName('test.csv');

            $mockMapworksActionService->expects($this->at(0))->method('getTokenVariablesFromPerson')->willReturn($this->studentDetail);
            $mockMapworksActionService->expects($this->at(1))->method('getTokenVariablesFromPerson')->willReturn($this->coordinatorDetail);

            $systemUrl = 'https://mapworks-publishing-api.skyfactor.com';
            $mockAcademicYearService->method('findCurrentAcademicYearForOrganization')->willReturn($this->currentAcademicYear);


            $mockEbiConfig = $this->getMock('EbiConfig', ['getValue']);
            $mockEbiConfig->method('getValue')->willReturn($systemUrl);
            if ($errorType == 'missing_system_api_url') {
                $mockEbiConfigRepository->method('findOneBy')->willThrowException(new SynapseValidationException('Unable to locate ebi_config entry for System_API_URL.'));
            } else {
                $mockEbiConfigRepository->method('findOneBy')->willReturn($mockEbiConfig);
            }

            if ($errorType == 'missing_system_url') {
                $mockEbiConfigService->method('getSystemUrl')->willReturn(null);
            } else {
                $mockEbiConfigService->method('getSystemUrl')->willReturn($systemUrl);
            }


            $studentReportEmailService = new StudentReportEmailService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $result = $studentReportEmailService->mapStudentReportToTokenVariables($student, $organizationId, $orgCalcFlagsStudentReportsEntity);

                $this->assertEquals($result, $expectedResult);
            } catch (SynapseValidationException $e) {

                $this->assertEquals($e->getUserMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                //Case 1 : Case for no error , returns token array with all value
                [
                    255,
                    2,
                    501,
                    '',
                    [
                        '$$student_first_name$$' => 'Brianna',
                        '$$student_last_name$$' => 'Robinson',
                        '$$student_email_address$$' => 'Brianna.Robinson@mailinator.com',
                        '$$coordinator_first_name$$' => 'ankur',
                        '$$coordinator_last_name$$' => 'aditya',
                        '$$coordinator_email_address$$' => 'ankuraditya@mailinator.com',
                        '$$coordinator_title$$' => 'coordinator',
                        '$$pdf_report$$' => 'https://mapworks-publishing-api.skyfactor.com/api/v1/storage/student_reports_uploads/test.csv',
                        '$$academicyear$$' => '201718',
                        '$$Skyfactor_Mapworks_logo$$' => 'https://mapworks-publishing-api.skyfactor.comimages/Skyfactor-Mapworks-login.png'
                    ]
                ],
                //Case 2 : Case for missing system api url, throws exception with error message
                [
                    211,
                    63,
                    425,
                    'missing_system_api_url',
                    'Unable to locate ebi_config entry for System_API_URL.'
                ],
                //case 3 : Case for missing system url , returns token array with no logo link
                [
                    282,
                    77,
                    514,
                    'missing_system_url',
                    [
                        '$$student_first_name$$' => 'Brianna',
                        '$$student_last_name$$' => 'Robinson',
                        '$$student_email_address$$' => 'Brianna.Robinson@mailinator.com',
                        '$$coordinator_first_name$$' => 'ankur',
                        '$$coordinator_last_name$$' => 'aditya',
                        '$$coordinator_email_address$$' => 'ankuraditya@mailinator.com',
                        '$$coordinator_title$$' => 'coordinator',
                        '$$pdf_report$$' => 'https://mapworks-publishing-api.skyfactor.com/api/v1/storage/student_reports_uploads/test.csv',
                        '$$academicyear$$' => '201718',
                        '$$Skyfactor_Mapworks_logo$$' => null
                    ]
                ],
            ]
        ]);
    }
    
    public function testSendStudentReportEmails () {
        $this->specify("Test Send Student Report Emails function", function ($orgCalcFlagsStudentReportsId, $studentId, $isCompletionEmail, $errorType, $expectedResult) {
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

            //mocking services
            $mockAcademicYearService = $this->getMock('AcademicYearService', ['findCurrentAcademicYearForOrganization']);
            $mockMapworksActionService = $this->getMock('MapworksActionService', ['sendCommunicationBasedOnMapworksAction','getTokenVariablesFromPerson']);
            $mockPersonService = $this->getMock('PersonService', ['getFirstPrimaryCoordinatorPerson']);
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['getSystemUrl']);

            //mocking repositories
            $mockOrgCalcFlagsStudentReportsRepository = $this->getMock('OrgCalcFlagsStudentReportsRepository', ['find', 'flush', 'clear']);
            $mockPersonRepository = $this->getMock('personRepository', ['find']);
            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);

            $mockContainer->method('get')
                ->willReturnMap(
                    [
                        [
                            AcademicYearService::SERVICE_KEY,
                            $mockAcademicYearService
                        ],
                        [
                            MapworksActionService::SERVICE_KEY,
                            $mockMapworksActionService
                        ],
                        [
                            PersonService::SERVICE_KEY,
                            $mockPersonService
                        ],
                        [
                            EbiConfigService::SERVICE_KEY,
                            $mockEbiConfigService
                        ]
                    ]
                );

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap(
                    [
                        [
                            OrgCalcFlagsStudentReportsRepository::REPOSITORY_KEY,
                            $mockOrgCalcFlagsStudentReportsRepository
                        ],
                        [
                            PersonRepository::REPOSITORY_KEY,
                            $mockPersonRepository
                        ],
                        [
                            EbiConfigRepository::REPOSITORY_KEY,
                            $mockEbiConfigRepository
                        ]
                    ]
                );

            if($errorType == 'invalid_report_id' || $errorType == 'null_report_id')  {
                $mockOrgCalcFlagsStudentReportsRepository->method('find')->willThrowException(new SynapseValidationException('Could not find Report for orgCalcFlagsStudentReportsId:'.$orgCalcFlagsStudentReportsId));
            } else {
                $mockOrgCalcFlagsStudentReport = $this->getMock('OrgCalcFlagsStudentReport', ['getId','getOrganization','getFileName','setCompletionEmailSent','setInProgressEmailSent']);
                if($errorType == 'invalid_organization'){
                    $mockOrgCalcFlagsStudentReport->method('getOrganization')->willReturn(null);
                } else {
                    $mockOrganization = $this->getMock('Organization', ['getId']);
                    $mockOrganization->method('getId')->willReturn(1);
                    $mockOrgCalcFlagsStudentReport->method('getOrganization')->willReturn($mockOrganization);
                }
                $mockOrgCalcFlagsStudentReport->method('getId')->willReturn($orgCalcFlagsStudentReportsId);
                $mockOrgCalcFlagsStudentReportsRepository->method('find')->willReturn($mockOrgCalcFlagsStudentReport);
            }
            if($errorType == 'invalid_student' || $errorType == 'null_student_id') {
                $mockPersonRepository->method('find')->willThrowException(new SynapseValidationException('Could not find Person for studentId: '.$studentId));
            } else {
                $mockPerson = $this->getMock('Person', ['getId']);
                $mockPerson->method('getId')->willReturn($studentId);
                $mockPersonRepository->method('find')->willReturn($mockPerson);
            }
            $systemUrl = 'https://mapworks-publishing-api.skyfactor.com';
            $mockEbiConfig = $this->getMock('EbiConfig', ['getValue']);
            $mockEbiConfig->method('getValue')->willReturn($systemUrl);
            $mockEbiConfigRepository->method('findOneBy')->willReturn($mockEbiConfig);
            $mockEbiConfigService->method('getSystemUrl')->willReturn(null);

            $mockMapworksActionService->method('getTokenVariablesFromPerson')->willReturn($this->studentDetail);
            if($errorType == 'send_communication_failed') {
                $mockMapworksActionService->method('sendCommunicationBasedOnMapworksAction')->willReturn(false);
            } else {
                $mockMapworksActionService->method('sendCommunicationBasedOnMapworksAction')->willReturn(true);
            }
            $studentReportEmailService = new StudentReportEmailService($mockRepositoryResolver, $mockLogger, $mockContainer);

            $result = $studentReportEmailService->sendStudentReportEmails($orgCalcFlagsStudentReportsId, $studentId, $isCompletionEmail);

            $this->assertEquals($result, $expectedResult);

        },
        [
            'examples' =>
                [
                    //case 1 : invalid report id , returns error message
                    [
                        4000,
                        5,
                        true,
                        'invalid_report_id',
                        [
                            'results' => 'Could not find Report for orgCalcFlagsStudentReportsId:4000',
                            'success' => false
                        ]
                    ],
                    //case 2: invalid student id , returns error message
                    [
                        4,
                        5000,
                        true,
                        'invalid_student',
                        [
                            'results' => 'Could not find Person for studentId: 5000',
                            'success' => false
                        ]
                    ],
                    //case 3: invalid organization id , returns error message
                    [
                        4444,
                        1212,
                        true,
                        'invalid_organization',
                        [
                            'results' => 'Could not find Organization',
                            'success' => false
                        ]
                    ],
                    //case 4: communication send unsuccessful, returns error message
                    [
                        221,
                        3453,
                        true,
                        'send_communication_failed',
                        [
                            'results' => 'Did not successfully send email. Will retry later.',
                            'success' => false
                        ]
                    ],
                    //case 5: communication successfully send , no error
                    [
                        221,
                        3453,
                        true,
                        '',
                        [
                            'results' => 'Email successfully sent.',
                            'success' => 1
                        ]
                    ],
                    //case 6: null report id , returns error message
                    [
                        null,
                        8855,
                        false,
                        'null_report_id',
                        [
                            'results' => 'Could not find Report for orgCalcFlagsStudentReportsId:',
                            'success' => false
                        ]
                    ],
                    //case 7: null student id , returns error message
                    [
                        211,
                        null,
                        false,
                        'null_student_id',
                        [
                            'results' => 'Could not find Person for studentId: ',
                            'success' => false
                        ]
                    ]
                ]
        ]);
    }
}