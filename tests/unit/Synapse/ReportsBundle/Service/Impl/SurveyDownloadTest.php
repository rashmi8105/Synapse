<?php
namespace Synapse\ReportsBundle\Service\Impl;

use Synapse\CampusConnectionBundle\Service\Impl\CampusConnectionService;
use Synapse\CampusResourceBundle\Service\Impl\CampusResourceService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\OrgProfileService;
use Synapse\CoreBundle\Service\Impl\ProfileService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\ProfileDto;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\CoreBundle\Repository\DatablockQuestionsRepository;
use Synapse\SurveyBundle\Entity\WessLink;
use Synapse\SurveyBundle\Service\Impl\SurveyBlockService;

class SurveyDownloadTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function _before()
    {
        \Codeception\Specify\Config::setDeepClone(false);
    }

    /*
     * Test downloadFactorKey
     * 
     */
    public function testDownloadFactorKey()
    {
        $this->specify("Test factor key download ", function ($surveyId, $expectedResults) {
            /*
             * mock Repository Resolver
             * 
             */
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $mockResque = $this->getMock('resque', array(
                'enqueue'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error'
            ));
            $orgService = $this->getMock('OrganizationService', array(
                'find'
            ));
            $mockSurveyBlockService = $this->getMock('SurveyBlockService', array(
                'find'
            ));
            $mockCampusConnectionService = $this->getMock('CampusConnectionService', array(
                'getStudentCampusConnections'
            ));
            $mockProfileService = $this->getMock('ProfileService', array(
                'getProfiles'
            ));
            $mockOrgProfileService = $this->getMock('OrgProfileService', array(
                'getProfiles'
            ));
            $mockDoctrine = $this->getMock('doctrine', array(
                'getManager'
            ));
            $mockCampusResourceService = $this->getMock('CampusResourceService', array(
                'getCampusResources'
            ));
            $mockPdfReportService = $this->getMock('PdfReportsService', array(
                'createReportRunningStatus'
            ));
            $mockActivityReportService = $this->getMock('ActivityReportService', array(
                'initiateReportJob'
            ));
            $mockSurveySnapshotService = $this->getMock('SurveySnapshotService', array(
                'initiateSnapshotJob',
                'generateReport'
            ));
            $factorReportService = $this->getMock('FactorReportService', array(
                'initiateFactorReport'
            ));

            $mockDatablockRepo = $this->getMock('dataBlockQuestionRepo', array(
                'getFactorForSurvey'
            ));

            $mockContainer->expects($this->any())
                ->method('get')
                ->willReturnMap(
                    [
                        [
                            SurveyBlockService::SERVICE_KEY,
                            $mockSurveyBlockService
                        ],
                        [
                            CampusConnectionService::SERVICE_KEY,
                            $mockCampusConnectionService
                        ],
                        [
                            CampusResourceService::SERVICE_KEY,
                            $mockCampusResourceService
                        ],
                        [
                            PdfReportsService::SERVICE_KEY,
                            $mockPdfReportService
                        ],
                        [
                            ActivityReportService::SERVICE_KEY,
                            $mockActivityReportService
                        ],
                        [
                            SurveySnapshotService::SERVICE_KEY,
                            $mockSurveySnapshotService
                        ],
                        [
                            ProfileSnapshotService::SERVICE_KEY,
                            $mockProfileService
                        ],
                        [
                            SynapseConstant::RESQUE_CLASS_KEY,
                            $mockResque
                        ],
                        [
                            ProfileService::SERVICE_KEY,
                            $mockProfileService
                        ],
                        [
                            OrgProfileService::SERVICE_KEY,
                            $mockOrgProfileService
                        ],
                        [
                            SynapseConstant::DOCTRINE_CLASS_KEY,
                            $mockDoctrine
                        ],
                        [
                            FactorReportService::SERVICE_KEY,
                            $factorReportService
                        ],
                        [
                            OrganizationService::SERVICE_KEY,
                            $orgService
                        ],

                    ]);


            $mockRepositoryResolver->method('getRepository')->willReturnMap([['SynapseCoreBundle:DatablockQuestions', $mockDatablockRepo]]);

            $reportService = new ReportsService($mockRepositoryResolver, $mockLogger, $mockContainer);


            $csv_file_path = '/roaster_uploads';
            $org_id = 1;
            $factorFileData = $reportService->downloadFactorKey($org_id, $csv_file_path, 1);
            $this->assertEquals($factorFileData, NULL);
        }, [
            'examples' => [
                [
                    '1',
                    [
                        'id' => 1,
                        'name' => 'Commitment to the Institution'
                    ]
                ],
                [
                    '2',
                    [
                        'id' => 2,
                        'name' => 'Self-Assessment: Communication Skills'
                    ]
                ]
            ]
        ]);
    }

    /*
     * Test cohortsKeyDownload
     *
     */

    public function testSurveyKeyDownload()
    {
        $this->specify("Test survey key download", function ($organization_id, $cohort_id, $timeZoneName) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $mockResque = $this->getMock('resque', array(
                'enqueue'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error'
            ));
            $orgService = $this->getMock('OrganizationService', array(
                'find'
            ));
            $mockSurveyBlockService = $this->getMock('SurveyBlockService', array(
                'find'
            ));
            $mockCampusConnectionService = $this->getMock('CampusConnectionService', array(
                'getStudentCampusConnections'
            ));
            $mockProfileService = $this->getMock('ProfileService', array(
                'getProfiles'
            ));
            $mockOrgProfileService = $this->getMock('OrgProfileService', array(
                'getProfiles'
            ));
            $mockDoctrine = $this->getMock('doctrine', array(
                'getManager'
            ));
            $mockCampusResourceService = $this->getMock('CampusResourceService', array(
                'getCampusResources'
            ));
            $mockPdfReportService = $this->getMock('PdfReportsService', array(
                'createReportRunningStatus'
            ));
            $mockActivityReportService = $this->getMock('ActivityReportService', array(
                'initiateReportJob'
            ));
            $mockSurveySnapshotService = $this->getMock('SurveySnapshotService', array(
                'initiateSnapshotJob',
                'generateReport'
            ));
            $factorReportService = $this->getMock('FactorReportService', array(
                'initiateFactorReport'
            ));

            $mockWessLink = $this->getMock('wessLinkRepo', array(
                'findBy'
            ));
            $surveyQuestionRepository = $this->getMock('surveyQuestionsRepo', array(
                'getQuestionsForSurvey',
                'getOptionsForSurveyQuestions'
            ));

            $mockContainer->expects($this->any())
                ->method('get')
                ->willReturnMap(
                    [
                        [
                            SurveyBlockService::SERVICE_KEY,
                            $mockSurveyBlockService
                        ],
                        [
                            CampusConnectionService::SERVICE_KEY,
                            $mockCampusConnectionService
                        ],
                        [
                            CampusResourceService::SERVICE_KEY,
                            $mockCampusResourceService
                        ],
                        [
                            PdfReportsService::SERVICE_KEY,
                            $mockPdfReportService
                        ],
                        [
                            ActivityReportService::SERVICE_KEY,
                            $mockActivityReportService
                        ],
                        [
                            SurveySnapshotService::SERVICE_KEY,
                            $mockSurveySnapshotService
                        ],
                        [
                            ProfileSnapshotService::SERVICE_KEY,
                            $mockProfileService
                        ],
                        [
                            SynapseConstant::RESQUE_CLASS_KEY,
                            $mockResque
                        ],
                        [
                            ProfileService::SERVICE_KEY,
                            $mockProfileService
                        ],
                        [
                            OrgProfileService::SERVICE_KEY,
                            $mockOrgProfileService
                        ],
                        [
                            SynapseConstant::DOCTRINE_CLASS_KEY,
                            $mockDoctrine
                        ],
                        [
                            FactorReportService::SERVICE_KEY,
                            $factorReportService
                        ],
                        [
                            OrganizationService::SERVICE_KEY,
                            $orgService
                        ],

                    ]);

            /*
             * mock find and getTimeZone from orgService
             */
            $mockOrganization = $this->getMock('orgService', array(
                'find',
                'getTimeZone'
            ));
            $orgService->expects($this->at(0))
                ->method('find')
                ->willReturn($mockOrganization);

            $timezone = $this->getMock('timezone', array(
                'getTimeZone'
            ));
            $mockOrganization->expects($this->at(0))
                ->method('getTimeZone')
                ->willReturn($timezone);

            $mockMetaList = $this->getMock('metadataListValues', array(
                'findByListName'
            ));
            $mockRepositoryResolver->method('getRepository')->willReturnMap([['SynapseCoreBundle:MetadataListValues', $mockMetaList], ['SynapseSurveyBundle:WessLink', $mockWessLink]]);

            $mockTimeZone = array(
                $this->getMock('timez', array(
                    'getListValue'
                ))
            );

            if ($timeZoneName) {
                $mockMetaList->expects($this->at(1))
                    ->method('findByListName')
                    ->willReturn($mockTimeZone);
                $mockTimeZone[0]->expects($this->at(0))
                    ->method('getListValue')
                    ->willReturn($timeZoneName);
            } else {
                $mockMetaList->expects($this->at(1))
                    ->method('findByListName')
                    ->willReturn($timeZoneName);
            }
            /*
             * Create mockbuilder for DateTime
             */
            $currentNow = $this->getMockBuilder('\DateTime')
                ->setMethods(array(
                    '__construct'
                ))
                ->setConstructorArgs(array(
                    'now',
                    new \DateTimeZone($timeZoneName)
                ))
                ->getMock();

            $reportService = new ReportsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $surveyData = $reportService->cohortsKeyDownload($orgId = 1, $cohortId = 1);
        }, [
            'examples' => [
                [
                    14,
                    1,
                    'Canada/Eastern'

                ]
            ]
        ]);
    }

    public function testDownloadEbiQuestionKey()
    {

        $this->specify("Test survey key download", function ($surveyId, $csvPath, $expectedResult, $optionsArray = array()) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $mockResque = $this->getMock('resque', array(
                'enqueue'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error'
            ));
            $orgService = $this->getMock('OrganizationService', array(
                'find'
            ));
            $mockSurveyBlockService = $this->getMock('SurveyBlockService', array(
                'find'
            ));
            $mockCampusConnectionService = $this->getMock('CampusConnectionService', array(
                'getStudentCampusConnections'
            ));
            $mockProfileService = $this->getMock('ProfileService', array(
                'getProfiles'
            ));
            $mockOrgProfileService = $this->getMock('OrgProfileService', array(
                'getProfiles'
            ));
            $mockDoctrine = $this->getMock('doctrine', array(
                'getManager'
            ));
            $mockCampusResourceService = $this->getMock('CampusResourceService', array(
                'getCampusResources'
            ));
            $mockPdfReportService = $this->getMock('PdfReportsService', array(
                'createReportRunningStatus'
            ));
            $mockActivityReportService = $this->getMock('ActivityReportService', array(
                'initiateReportJob'
            ));
            $mockSurveySnapshotService = $this->getMock('SurveySnapshotService', array(
                'initiateSnapshotJob',
                'generateReport'
            ));
            $factorReportService = $this->getMock('FactorReportService', array(
                'initiateFactorReport'
            ));


            $mockContainer->expects($this->any())
                ->method('get')
                ->willReturnMap(
                    [
                        [
                            SurveyBlockService::SERVICE_KEY,
                            $mockSurveyBlockService
                        ],
                        [
                            CampusConnectionService::SERVICE_KEY,
                            $mockCampusConnectionService
                        ],
                        [
                            CampusResourceService::SERVICE_KEY,
                            $mockCampusResourceService
                        ],
                        [
                            PdfReportsService::SERVICE_KEY,
                            $mockPdfReportService
                        ],
                        [
                            ActivityReportService::SERVICE_KEY,
                            $mockActivityReportService
                        ],
                        [
                            SurveySnapshotService::SERVICE_KEY,
                            $mockSurveySnapshotService
                        ],
                        [
                            ProfileSnapshotService::SERVICE_KEY,
                            $mockProfileService
                        ],
                        [
                            SynapseConstant::RESQUE_CLASS_KEY,
                            $mockResque
                        ],
                        [
                            ProfileService::SERVICE_KEY,
                            $mockProfileService
                        ],
                        [
                            OrgProfileService::SERVICE_KEY,
                            $mockOrgProfileService
                        ],
                        [
                            SynapseConstant::DOCTRINE_CLASS_KEY,
                            $mockDoctrine
                        ],
                        [
                            FactorReportService::SERVICE_KEY,
                            $factorReportService
                        ],
                        [
                            OrganizationService::SERVICE_KEY,
                            $orgService
                        ],

                    ]);
            /* 
             * Mock for Survey Question Repository
             */
            $surveyQuestionsRepo = $this->getMock('surveyQuestionsRepo', array(
                'getUniqueSurveyQuestionsForCohort', 'getOptionsForSurveyQuestions'
            ));


            $csvPath = @fopen("php://input", "w+");
            /*
             * Set up the expectation for the getRepository
             */

            $mockRepositoryResolver->method('getRepository')->willReturnMap([["SynapseSurveyBundle:SurveyQuestions", $surveyQuestionsRepo]]);
            /*
             * Set up the expectation for getUniqueSurveyQuestionsForCohort
             */
            $surveyQuestionsRepo->expects($this->at(0))
                ->method('getUniqueSurveyQuestionsForCohort')
                ->willReturn($expectedResult);

            if (!empty($expectedResult)) {
                foreach ($expectedResult as $inputData) {
                    if ($inputData['question_type'] == 'Q') {
                        /*
                         * Set up the expectation for getOptionsForSurveyQuestions
                         */
                        $surveyQuestionsRepo->expects($this->at(1))
                            ->method('getOptionsForSurveyQuestions')
                            ->willReturn($optionsArray);
                    }
                }
            }
            /*  
             * Create Instance for ReportsService
             */
            $reportService = new ReportsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            /*
             * function calling
             */
            $surveyData = $reportService->downloadEbiQuestionKey($surveyId, $csvPath);
        }, [
            'examples' => [
                [
                    11,
                    'roster_uploads/cohort-key.csv',
                    [
                        [
                            'survey_id' => 1,
                            'question_type' => 'Q',
                            'ebi_question_id' => '21',
                            'qnbr' => '23',
                            'ebi_ques_text' => 'Question name'
                        ]
                    ],
                    [
                        [
                            'ebi_option_value' => '12',
                            'ebi_option_text' => 'Not Sure'
                        ]
                    ]
                ],
                [
                    12,
                    'roster_uploads/cohort-key.csv',
                    [
                        [
                            'survey_id' => 2,
                            'question_type' => 'SA',
                            'ebi_question_id' => '152',
                            'qnbr' => '452',
                            'ebi_ques_text' => 'Short answer question'
                        ]
                    ]
                ],
                [
                    13,
                    'roster_uploads/cohort-key.csv',
                    [
                        [
                            'survey_id' => 2,
                            'question_type' => 'LA',
                            'ebi_question_id' => '458',
                            'qnbr' => '547',
                            'ebi_ques_text' => 'Long answer question'
                        ]
                    ]
                ]
            ]
        ]);
    }
}