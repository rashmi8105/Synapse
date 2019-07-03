<?php

use Synapse\UploadBundle\Job\UpdateStudent;
use Synapse\PersonBundle\Repository\ContactInfoRepository;

class UpdateStudentTest extends \PHPUnit_Framework_TestCase{
    
    use \Codeception\Specify;
    

    private $orgId = 1;    
    
    private $person = 1;
    
    public function testRun(){
        $this->markTestSkipped("Skipped as of now. @todo -  fix this ");
        $this->specify("Run job for student upload", function(array $studentData){
            $mockOutput = $this->getMock('StreamOutput', array('writeln'));
                        
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', array('findOneById'));
            $mockEbiMetadataRepository = $this->getMock('EbiMetadataRepository', array('findOneById'));
            $mockOrgMetadataRepository = $this->getMock('OrgMetadataRepository', array('findOneById'));
            $mockPersonOrgMetadataRepository = $this->getMock('PersonOrgMetaDataRepository', array('persist'));
            $mockPersonEbiMetadataRepository = $this->getMock('PersonEbiMetaDataRepository', array('persist'));
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', array('persist', 'getOrgStudentsCohorts'));
            $mockOrgGroupStudentsRepository = $this->getMock('OrgGroupStudentsRepository', array('persist'));
            $mockOrgAcademicYearRepository = $this->getMock('orgAcademicYearRepository', array('getCurrentAcademicDetails', 'findOneBy'));
            $mockOrgAcademicTermRepository = $this->getMock('OrgAcademicTermRepository', array('findOneBy'));
            $mockRiskGroupHistoryRepository = $this->getMock('RiskGroupPersonHistoryRepository', array('persist'));
            $mockRiskGroupRepository = $this->getMock('RiskGroupRepository', array('findOneById'));
            $mockContactRepository = $this->getMock('ContactInfoRepository', array('findByPrimaryEmail'));
            $mockOrgRiskGroupModelRepo = $this->getMock('OrgRiskGroupModelRepository', array('findOneBy'));
            $mockMetaDataListValuesRepository = $this->getMock('MetadataListValues', array('findByListName'));
            $mockOrgPersonStudentCohortRepository = $this->getMock('OrgPersonStudentCohort', array('findOneBy'));
            $mockRiskCalculationRepository = $this->getMock('OrgRiskvalCalcInputs', array('findOneBy'));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $mockKernel = $this->getMockBuilder('kernel')
                ->setMethods(array(
                'getContainer'
            ))
                ->getMock();           
            $studentObj = new UpdateStudent();           
            $reflection = new \ReflectionClass(get_class($studentObj));           
            $parentReflection = $reflection->getParentClass();            
            $property = $parentReflection->getProperty('kernel');            
            $property->setAccessible(true);            
            $property->setValue($studentObj, $mockKernel);
            
            $mockKernel->method('getContainer')
                ->willReturn($mockContainer);
            $mockRepositoryResolver = $this->getMock('repositoryResolver', array(
                'getRepository'
            ));
            
            $entityService = $this->getMock('entityService', array(
                'findOneByName'
            ));
            
            $groupService = $this->getMock('groupService', array(
                'addSystemGroup'
            ));
            
            $uploadLogService = $this->getMock('uploadFileLogService', array(
                'updateValidRowCount',
                'updateCreatedRowCount',
                'updateUpdatedRowCount',
                'updateUnchangedRowCount'
            ));
            
            $mockEbiMetadataRepository = $this->getMock('EbiMetadata', array(
                'findOneByKey',
                'getScope'
            ));
            
            $mockEbiMetadataRepository->expects($this->any())
            ->method('findOneByKey')
            ->will($this->returnValue($mockEbiMetadataRepository));
            
            $mockEbiMetadataRepository->expects($this->any())
            ->method('getScope')
            ->will($this->returnValue($mockEbiMetadataRepository));
            
            $mockPersonEbiMetadataRepository = $this->getMock('PersonEbiMetadataRepository', array(
                'findOneBy',
                'setMetadataValue',
                'setEbiMetadata',
                'setPerson',
                'update'
            ));
            
            $mockPersonEbiMetadata = $this->getMock('PersonEbiMetadata', array(
                'getEbiMetadata',
                'setMetadataValue'
            ));
            
            $mockEbiMetadata = $this->getMock('EbiMetadata', array('getMetadataType'));
            
            $mockPersonEbiMetadataRepository->expects($this->any())
            ->method('findOneBy')
            ->will($this->returnValue($mockPersonEbiMetadata));
            
            $mockPersonEbiMetadata->expects($this->any())
            ->method('getEbiMetadata')
            ->will($this->returnValue($mockEbiMetadata));
            
            $mockPersonEbiMetadataRepository->expects($this->any())
            ->method('setMetadataValue')
            ->will($this->returnValue($mockPersonEbiMetadataRepository));
            
            $mockPersonEbiMetadataRepository->expects($this->any())
            ->method('setEbiMetadata')
            ->will($this->returnValue($mockPersonEbiMetadataRepository));
            
            $mockPersonEbiMetadataRepository->expects($this->any())
            ->method('setPerson')
            ->will($this->returnValue($mockPersonEbiMetadataRepository));
            
            $mockOrgPersonStudentCohortRepository = $this->getMock('OrgPersonStudentCohort', array('findOneBy'));
            
            $mockOrgPersonStudentCohortRepository->expects($this->any())
            ->method('findOneBy')
            ->will($this->returnValue($mockOrgPersonStudentCohortRepository));
            
            $personService = $this->getMock('PersonService', array(
                'findOneByExternalIdOrg',
            	'flush',
            	'clear',
            	'getContacts',
            	'setexternalid',
            	'setauthusername',
            	'setfirstname',
            	'setlastname',
            	'settitle',
            	'setUsername',
            	'setAddress1',
            	'setPrimaryEmail',
                'updatePerson',
                'getExternalId',
                'getOrganization',
                'getId'
            ));
            
            $organizationMock = $this->getMockBuilder('Synapse\CoreBundle\Entity\Organization', array(
            		'getId'
            ))
            ->disableOriginalConstructor()
            ->getMock();
            $organizationMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($this->orgId));
            
            $personService->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($this->person));
            $personService->expects($this->any())
            ->method('getOrganization')
            ->will($this->returnValue($organizationMock));
            
            $personService->expects($this->any())
            ->method('findOneByExternalIdOrg')
            ->will($this->returnValue($personService));
            
            $personService->expects($this->any())
            ->method('getContacts')
            ->will($this->returnValue(array($personService)));
            
           $mockOrganization = $this->getMock('Organization', array(
            		'getId'
            ));
           
            
            $mockOrgAcademicYear = $this->getMock('OrgAcademicYear', array(
            	'getCurrentAcademicDetails'
            ));
            
            $mockOrgAcademicYearRepository->expects($this->any())
            ->method('getCurrentAcademicDetails')
            ->will($this->returnValue([
                [
                'id'=>1
                ]
            ]));
            
            $mockCache = $this->getMock('cache', array(
                'fetch', 'save'
            ));
            
            $mockEntityValidator = $this->getMock('Validator', array(
            		'validate'
            ));
            
            $mockValidator = $this->getMock('StudentUploadValidatorService', array(
                'validate', 'getErrors'
            ));
            
            $mockValidator->expects($this->any())
            ->method('validate')
            ->will($this->returnValue($mockValidator));
            
            $mocklogger = $this->getMock('Logger', array('debug','error', 'info'));
            
            $mocklogger->expects($this->any())
            ->method('error')
            ->will($this->returnValue($mocklogger));
            
            $mockOrganization = $this->getMock('Organization', array(
            	'getTimezone'
            ));
            
            $mockOrganizationRepository->expects($this->any())
            ->method('findOneById')
            ->will($this->returnValue($mockOrganization));
            
             $mockOrganization->expects($this->any())
                ->method('getTimezone')
                ->will($this->returnValue('Asia/Calcutta'));
             $mockMetaDataListValuesRepository = $this->getMock('MetadataListValuesRepository', array(
             		'findByListName'
             ));
             
             $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', array(
                 'findOneByPerson',
                 'setOrganization',
                 'setPerson',
                 'setStatus',
                 'setReceiveSurvey',
                 'getStatus',
                 'getReceiveSurvey'
             ));
             
             $mockOrgPersonStudent = $this->getMock('OrgPersonStudent', array('setOrganization'));
             
             $mockOrgPersonStudentRepository->expects($this->any())
             ->method('findOneByPerson')
             ->will($this->returnValue($mockOrgPersonStudentRepository));
             
             $mockOrgPersonStudent->expects($this->any())
             ->method('setOrganization')
             ->will($this->returnValue($mockOrgPersonStudent));
             
             $mockOrgGroupStudentsRepository = $this->getMock('OrgGroupStudents', array('findOneBy'));
             
             $mockOrgGroupStudentsRepository->expects($this->any())
             ->method('findOneBy')
             ->will($this->returnValue($mockOrgGroupStudentsRepository));
             
             $mockRiskCalculationRepository = $this->getMock('OrgRiskvalCalcInputs', array('findOneBy'));
             
             $mockRiskCalculationRepository->expects($this->any())
             ->method('findOneBy')
             ->will($this->returnValue(false));
             
             $mockRiskCalculation = $this->getMock('RiskCalculationService', array(
                 'createRiskCalculationInput'
             ));
             
             $mockProfileValidationService = $this->getMock('profile_validation_service', array(
                 'profileItemCustomValidations'
             ));
                          
             $mockRepositoryResolver->method('getRepository')->willReturnMap([
             		['SynapseAcademicBundle:OrgAcademicYear', $mockOrgAcademicYearRepository],
             		['SynapseCoreBundle:Organization', $mockOrganizationRepository],
             		['SynapseCoreBundle:EbiMetadata', $mockEbiMetadataRepository],
             		['SynapseCoreBundle:OrgMetadata', $mockOrgMetadataRepository],
             		['SynapseCoreBundle:PersonOrgMetadata', $mockPersonOrgMetadataRepository],
             		['SynapseCoreBundle:PersonEbiMetadata', $mockPersonEbiMetadataRepository],
             		['SynapseCoreBundle:OrgPersonStudent', $mockOrgPersonStudentRepository],
             		['SynapseCoreBundle:OrgGroupStudents', $mockOrgGroupStudentsRepository],
             		['SynapseAcademicBundle:OrgAcademicTerms', $mockOrgAcademicTermRepository],
             		['SynapseRiskBundle:RiskGroupPersonHistory', $mockRiskGroupHistoryRepository],
             		['SynapseRiskBundle:RiskGroup', $mockRiskGroupRepository],
             		[ContactInfoRepository::REPOSITORY_KEY, $mockContactRepository],
             		['SynapseRiskBundle:OrgRiskGroupModel', $mockOrgRiskGroupModelRepo],
             		['SynapseCoreBundle:MetadataListValues', $mockMetaDataListValuesRepository],
                    ['SynapseCoreBundle:OrgPersonStudentCohort', $mockOrgPersonStudentCohortRepository],
                    ['SynapseRiskBundle:OrgRiskvalCalcInputs', $mockRiskCalculationRepository]
             		]);
             
             
             
             $mockMetaDataListValuesRepository->expects($this->any())
             ->method('findByListName')
             ->will($this->returnValue('Asia/Calcutta'));
            
            $mockContainer->method('get')->willReturnMap([
                ['repository_resolver', $mockRepositoryResolver],
                ['entity_service',$entityService],            
                ['StreamOutput',$mockOutput],       
                ['group_service',$groupService],
                ['upload_file_log_service', $uploadLogService],
                ['person_service', $personService],
                ['synapse_redis_cache', $mockCache],
                ['student_upload_validator_service', $mockValidator],
				['validator', $mockEntityValidator],
				['logger', $mocklogger],
                ['riskcalculation_service', $mockRiskCalculation],
                ['profile_validation_service', $mockProfileValidationService]
            ]);
            
            $expectedResults = $studentData['expectedResult'];
			$students = $studentObj->run($studentData);  
			
            $this->assertEquals($students, $expectedResults);
            
        }, [
            'examples' => [
                            [    
                                [
                                   'orgId' => 2,
                                   'uploadId' => '20162599N',
                                   'jobNumber' => '4523156',
                                   'expectedResult' => [                                                                           

                                                        ],
                                   'updates' => [
                                [
                                    '2015TestAbby11',
                                    
                                    [
                                        'externalid' => '2015TestAbby11',
                                        'authusername' => 'test',
                                        'firstname' => 'test',
                                        'lastname' => 'test',
                                        'title' => 'test',
                                        'studentphoto' => '',
                                        'isactive' => 1,
                                        'surveycohort' => 2,
                                        'receivesurvey' => 1,
                                        'yearid' => 201516,
                                        'termid' => '',
                                        'primaryconnect' => '',
                                        'riskgroupid' => '',
                                        'campusid' => '',
                                        'address1' => 'Address5',
                                        'address2' => '',
                                        'city' => '',
                                        'zip' => '',
                                        'state' => '',
                                        'country' => '',
                                        'primarymobile' => '',
                                        'alternatemobile' => '',
                                        'homephone' => '',
                                        'primaryemail' => 'tests21ya234weq5@mailinator.com',
                                        'alternateemail' => '',
                                        'primarymobileprovider' => '',
                                        'alternatemobileprovider' => '',
                                        'gender' => '',
                                        'birthyear' => '',
                                        'raceethnicity' => '',
                                        'stateinout' => '',
                                        'internationalstudent' => '',
                                        'campuscountry' => '',
                                        'campusaddress' => '',
                                        'campuscity' => '',
                                        'campusstate' => '',
                                        'campuszip' => '',
                                        'fafsasubmitted' => '',
                                        'pelleligible' => '',
                                        'efc' => '',
                                        'unmetneed' => '',
                                        'fedaidreceived' => '',
                                        'guardianedlevel' => '',
                                        'firstgenstudent' => '',
                                        'militarybenefitsus' => '',
                                        'militaryservedus' => '',
                                        'highschoolnumber' => '',
                                        'highschoolgpa' => '',
                                        'highschoolpercentile' => '',
                                        'highschoolgradyear' => '',
                                        'actcomposite' => '',
                                        'actenglish' => '',
                                        'actmath' => '',
                                        'actwriting' => '',
                                        'actscience' => '',
                                        'actreading' => '',
                                        'satmath' => '',
                                        'satcomposite' => '',
                                        'satwriting' => '',
                                        'satcriticalread' => '',
                                        'compassprealgebra' => '',
                                        'compassalgebra' => '',
                                        'compasscollegealgebra' => '',
                                        'compassgeometry' => '',
                                        'compasstrig' => '',
                                        'compasswriting' => '',
                                        'compassreading' => '',
                                        'compassesl' => '',
                                        'accuplacersentence' => '',
                                        'accuplacerarithmetic' => '',
                                        'accuplaceralgebra' => '',
                                        'accuplacerreading' => '',
                                        'applicationdate' => '',
                                        'admissiondate' => '',
                                        'acceptdate' => '',
                                        'orientationdate' => '',
                                        'enrollyear' => '',
                                        'enrollterm' => '',
                                        'enrolltype' => '',
                                        'degreeseeking' => '',
                                        'classlevel' => '',
                                        'majorcip' => '',
                                        'programonline' => '',
                                        'honorsstudent' => '',
                                        'athletestudent' => '',
                                        'athletescholarship' => '',
                                        'athletesport' => '',
                                        'ipedscohort' => '',
                                        'retentiontrack' => '',
                                        'persistmidyear' => '',
                                        'retainyear2' => '',
                                        'retainyear3' => '',
                                        'complete1yrsorless' => '',
                                        'complete2yrsorless' => '',
                                        'complete3yrsorless' => '',
                                        'complete4yrsorless' => '',
                                        'complete5yrsorless' => '',
                                        'complete6yrsorless' => '',
                                        'levelofcompletion' => '',
                                        'preyearcredtotal' => '',
                                        'preyearremcredearned' => '',
                                        'preyearcumgpa' => '',
                                        'enrolltimestatus' => '',
                                        'campusresident' => '',
                                        'starttermcredtotal' => '',
                                        'starttermcredrem' => '',
                                        'midcoursesfailing' => '',
                                        'endtermgpa' => '',
                                        'endtermcreditsearned' => '',
                                        'endtermcumgpa' => '',
                                        'endtermcumcreditsearned' => '',
                                        'endtermremcredpass' => '',
                                        'sap' => '',
                                        'passportnumber' => '',
                                        'aadharnumber' => '',
                                        'pulkitagarwal' => '',
                                        'skills' => '',
                                        'kiranprofiledemo' => '',
                                        'kiranprofiledemo01' => '',
                                        'testyear' => '',
                                        'shuchi' => '',
                                        'year' => '',
                                        'termprofile' => '',
                                        'yearprofile' => '',
                                        'searchyear' => '',
                                        'firstname604' => ''
                                    ]
                                ]
                                
                            ]
                            
                        ]
                    ]
                ]
            ]);
    }
    
    private function getOrganizationMock(){
       
    	// Mocking to organization
    	$organizationMock = $this->getMockBuilder('Synapse\CoreBundle\Entity\Organization', array(
    			'getId'
    	))
    	->disableOriginalConstructor()
    	->getMock();
    	$organizationMock->expects($this->any())
    	->method('getId')
    	->will($this->returnValue($this->orgId));
    
    	return $organizationMock;
    }
    
    private function getPersonMock(){
    	// Mocking to person
    	$this->personMock = $this->getMockBuilder('Synapse\CoreBundle\Entity\Person', array(
    			'getId',
    			'getOrganization'
    	))
    	->disableOriginalConstructor()
    	->getMock();
    	$this->personMock->expects($this->any())
    	->method('getId')
    	->will($this->returnValue($this->person));
    	$this->personMock->expects($this->any())
    	->method('getOrganization')
    	->will($this->returnValue($this->getOrganizationMock()));
    
    	return $this->personMock;
    }
}
