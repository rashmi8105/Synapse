<?php
use Synapse\SurveyBundle\Service\Impl\StudentSurveyService;

class StudentSurveyServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    private $studentId = 1;

    public function testListStudentsSurveysData()
    {
        $this->specify("List Students Surveys Data for Student", function ($studentId, $listType, $status)
        {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            
            $mockPersonRepository = $this->getMock('Person', array(
                'findOneById'
            ));
            
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudent', array(
                'findBy'
            ));
            
            $mockOrgLangRepository = $this->getMock('OrganizationLang', array(
                'findOneBy'
            ));
            
            $mockWessLinkRepository = $this->getMock('WessLink', array(
                'getStudentSurveyList'
            ));
            
            $mockOrgPersonStudentSurveyLinkRepository = $this->getMock('OrgPersonStudentSurveyLink', array(
                'getStudentSurveysByOrgId',
                'findOneBy'
            ));
            
            $orgCalcFlagsStudentReportsRepository = $this->getMock('OrgCalcFlagsStudentReportsRepository', array(
                'getLastStudentReportGeneratedPdfName'
            ));
            
            $orgCalcFlagsStudentReportsRepository->method('getLastStudentReportGeneratedPdfName')
                ->willReturn("nopdf.pdf");
            
            $mockAcademicYearRepository = $this->getMock('OrgAcademicYear', array(
                'getCurrentAcademicDetails'
            ));
            
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            
            $mockUtilService = $this->getMock('UtilServiceHelper', array(
                'getCohotCodesForStudent'
            ));
            
            $mockUtilService->method('getCohotCodesForStudent')
                ->willReturn("1,2");
            
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                [
                    'SynapseCoreBundle:Person',
                    $mockPersonRepository
                ],
                [
                    'SynapseCoreBundle:OrgPersonStudent',
                    $mockOrgPersonStudentRepository
                ],
                [
                    'SynapseCoreBundle:OrganizationLang',
                    $mockOrgLangRepository
                ],
                [
                    
                    'SynapseSurveyBundle:WessLink',
                    $mockWessLinkRepository
                ],
                [
                    
                    'SynapseSurveyBundle:OrgPersonStudentSurveyLink',
                    $mockOrgPersonStudentSurveyLinkRepository
                ],
                [
                    
                    'SynapseAcademicBundle:OrgAcademicYear',
                    $mockAcademicYearRepository
                ],
                [
                    'SynapseReportsBundle:OrgCalcFlagsStudentReports',
                    $orgCalcFlagsStudentReportsRepository
                ]
            ]
            );
            
            $mockContainer->method('get')
                ->willReturnMap([
                [
                    'util_service',
                    $mockUtilService
                ]
            ]);
            
            $mockPerson = $this->getMock("Person");
            
            $mockPersonRepository->expects($this->once())
                ->method('findOneById')
                ->will($this->returnValue($mockPerson));
            
            $mockOrgPersonStudent = $this->getMock("OrgPersonStudent", array(
                'getId',
                'getOrganization'
            ));
            
            $mockOrgPersonStudentRepository->expects($this->once())
                ->method('findBy')
                ->will($this->returnValue(array(
                $mockOrgPersonStudent
            )));
            
            /**
             * Organization Mock Object
             */
            $mockOrg = $this->getMock('Organization', array(
                'getId',
                'getTimeZone'
            ));
            
            $mockOrgPersonStudent->expects($this->any())
                ->method('getOrganization')
                ->will($this->returnValue($mockOrg));
            
            $mockOrg->expects($this->any())
                ->method('getId')
                ->will($this->returnValue(1));
            
            $mockOrg->expects($this->any())
                ->method('getTimeZone')
                ->will($this->returnValue('UTC'));
            
            $mockWessLinkRepository->expects($this->any())
                ->method('getStudentSurveyList')
                ->will($this->returnValue($this->getSurveyListData($status, $listType)));
            
            $mockOrgPersonStudentSurveyLinkRepository->expects($this->any())
                ->method('getStudentSurveysByOrgId')
                ->will($this->returnValue($this->getSurveyListData($status, $listType)));
            
            $studentSurveyService = new StudentSurveyService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $studentList = $studentSurveyService->listStudentsSurveysData($studentId, $listType);
            
            $this->assertInternalType("array", $studentList);
            foreach ($studentList as $studentSurvey) {
                
                $surveyLastDate = $studentSurvey->getSurveyLastDate();
                $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\StudentSurveyDetailsDto", $studentSurvey);
                $this->assertEquals(1, $studentSurvey->getSurveyId());
                $this->assertEquals("Transition One", $studentSurvey->getSurveyName());
                $this->assertObjectHasAttribute('surveyId', $studentSurvey);
                $this->assertObjectHasAttribute('surveyName', $studentSurvey);
                $this->assertObjectHasAttribute('surveyLastDate', $studentSurvey);
                $this->assertObjectHasAttribute('status', $studentSurvey);
                $this->assertObjectHasAttribute('campusName', $studentSurvey);
                $this->assertObjectHasAttribute('surveyUrl', $studentSurvey);
                $this->assertObjectHasAttribute('cohort', $studentSurvey);
            }
        }, [
            'examples' => [
                [
                    $this->studentId,
                    'list',
                    'Assigned'
                ],
                [
                    $this->studentId,
                    'list',
                    'CompletedAll'
                ],
                [
                    $this->studentId,
                    'list',
                    'CompletedMandatory'
                ],
                [
                    $this->studentId,
                    'report',
                    'CompletedMandatory'
                ]
            ]
        ]);
    }

    private function getSurveyListData($status, $type)
    {
        if ($type == "report") {
            
            $date = new DateTime('now');
        } else {
            $date = date('Y-m-d');
        }
        return [
            [
                "survey_id" => 1,
                "survey_name" => "Transition One",
                "close_date" => $date,
                "status" => $status,
                "survey_link" => "",
                'cohort' => 1,
                'id' => 1
            ]
        ];
    }
}