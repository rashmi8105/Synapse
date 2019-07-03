<?php
use Synapse\ReportsBundle\Job\SurveyStudentResponseJob;
use Synapse\RestBundle\Entity\PersonDTO;

class SurveyStudentResponseJobTest extends \Codeception\Test\Unit
{
    use Codeception\specify;
    /*
     * Unit test for factor response survey response date time
     */
    public function testGetStudentFactorResponse()
    {
        $this->specify("Test to check factor response and survey response date and timezone", function ($surveyArr, $surveyStudentPerson, $orgId, $personData, $fatorData, $timezone, $responseDate, $personId, $yearId)
        {
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $mockKernel = $this->getMockBuilder('kernel')
                ->setMethods(array(
                'getContainer'
            ))
                ->getMock();
            $mockSurveyLinkRepository = $this->getMock('studentSurveyLinkRepo', array(
                'getSurveyCompletionDate'
            ));
            $mockFactorRepository = $this->getMock('factorRepository', array(
                'getStudentFactorValues'
            ));
            $orgService = $this->getMock('OrganizationService', array(
                'find'
            ));
            
            $mockOrgPersonStudentCohort = $this->getMock('OrgPersonStudentCohort', array(
                'getPersonYearCohort'
            ));
            
            $studentResponseJob = new SurveyStudentResponseJob();
            $reflection = new \ReflectionClass(get_class($studentResponseJob));
            $parentReflection = $reflection->getParentClass()
                ->getParentClass();
            $property = $parentReflection->getProperty('kernel');
            $property->setAccessible(true);
            $property->setValue($studentResponseJob, $mockKernel);
            
            $mockKernel->method('getContainer')
                ->willReturn($mockContainer);
            $mockRepositoryResolver = $this->getMock('repositoryResolver', array(
                'getRepository'
            ));
            $mockContainer->method('get')
                ->willReturnMap([
                [
                    'repository_resolver',
                    $mockRepositoryResolver
                ],
                [
                    'org_service',
                    $orgService
                ]
            ]
            );
            
            /*
             * mock find and getTimeZone from orgService
             */
            $mockOrganization = $this->getMock('orgService', array(
                'find',
                'getTimeZone'
            ));
            /*
             * Orgmock expectation
             */
            $orgService->expects($this->at(0))
                ->method('find')
                ->willReturn($mockOrganization);
            
            $mockOrganization->expects($this->at(0))
                ->method('getTimeZone')
                ->willReturn($timezone);
            
            $mockMetaList = $this->getMock('metadataListValues', array(
                'findByListName'
            ));
            /*
             * Repository expectation
             */
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                [
                    "SynapseSurveyBundle:Factor",
                    $mockFactorRepository
                ],
                [
                    "SynapseSurveyBundle:OrgPersonStudentSurveyLink",
                    $mockSurveyLinkRepository
                ],
                [
                    'SynapseCoreBundle:MetadataListValues',
                    $mockMetaList
                ],
                [
                    'SynapseCoreBundle:OrgPersonStudentCohort',
                    $mockOrgPersonStudentCohort
                ]
            ]);
            
            /*
             * mock survey completion date
             */
            $surveyDate['survey_completion_date'] = $responseDate;
            $surveyCompletionDate[] = $surveyDate;
            $mockSurveyLinkRepository->expects($this->at(0))
                ->method('getSurveyCompletionDate')
                ->willReturn($surveyCompletionDate);
            
            $mockOrgPersonStudentCohort = $this->getMock('OrgPersonStudentCohort', array(
                'findOneBy'
            ));
            
            $mockOrgPersonStudentCohort->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($mockOrgPersonStudentCohort));
            
            $studentResponseJob->getStudentFactorResponseAndCompletionDate($surveyArr, $surveyStudentPerson, $orgId, $personData, $timezone);
            $studentResponseJob->getPersonYearCohort($orgId, $personId, $yearId);
        }, [
            'examples' => [
                [
                    [
                        '1' => 'Survey1',
                        '2' => 'Survey 2'
                    ],
                    [
                        '1' => 1,
                        '2' => 2,
                        '3' => 3
                    ],
                    1,
                    $this->getPersonData(),
                    'factor_data' => [
                        [
                            'factor_id' => 1,
                            'mean_value' => 5.89
                        ],
                        [
                            'factor_id' => 2,
                            'mean_value' => 5.36
                        ],
                        [
                            'factor_id' => 3,
                            'mean_value' => 7.58
                        ]
                    ],
                    'Canada/Eastern',
                    '2015-10-26 03:55:33',
                    2,
                    201516
                ]
            ]
        ]);
    }

    public function getPersonData()
    {
        $person = new PersonDTO();
        $person->setFirstName(1);
        $person->setLastName('Red');
        $person->setTitle('Student');
        $person->setExternalId('45785');
        $person->setUsername('robert@mailinator.com');
        $person->setOrganization(1);
        $person->setAddress1('location');
        $person->setCity('Boston');
        $person->setZip('45876');
        $personData[] = $person;
        return $personData;
    }
    
    public function testGetReceiveSurvey()
    {
         $this->specify("Test to get receive survey", function ()
         {            
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $mockKernel = $this->getMockBuilder('kernel')
                ->setMethods(array(
                'getContainer'
            ))
                ->getMock();
            $mockPersonStudentSurveyRepo = $this->getMock('personStudentSurveyRepo', array(
                'findOneBy'
            ));
            $studentResponseJob = new SurveyStudentResponseJob();
            $reflection = new \ReflectionClass(get_class($studentResponseJob));
            $parentReflection = $reflection->getParentClass()
                ->getParentClass();
            $property = $parentReflection->getProperty('kernel');
            $property->setAccessible(true);
            $property->setValue($studentResponseJob, $mockKernel);            
            $mockKernel->method('getContainer')
                ->willReturn($mockContainer);
            $mockRepositoryResolver = $this->getMock('repositoryResolver', array(
                'getRepository'
            ));
            
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap(
                [
                    "SynapseSurveyBundle:OrgPersonStudentSurvey",
                    $mockPersonStudentSurveyRepo
                ]);
            
         });
    }
}