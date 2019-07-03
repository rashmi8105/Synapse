<?php
namespace Synapse\AcademicBundle\Service\Impl;

use Codeception\Specify;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\AcademicBundle\Entity\OrgAcademicYear;
use Synapse\AcademicBundle\EntityDto\AcademicTermDto;
use Synapse\AcademicBundle\EntityDto\AcademicTermListResponseDto;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\SynapseConstant;

class AcademicTermServiceTest extends \PHPUnit_Framework_TestCase
{
    use Specify;

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
        $this->mockLogger = $this->getMock('Logger', ['debug', 'error']);
        $this->mockContainer = $this->getMock('Container', ['get']);
    }


    public function testListAcademicTerm()
    {
        $this->specify("List academic terms based on current year", function ($organizationId, $organizationAcademicYearId, $loggedInUser, $userType, $isInternal, $expectedException, $academicYearId, $organizationAcademicTerms) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);

            $mockLogger = $this->getMock('Logger', ['error', 'debug', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock OrganizationRepository
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find']);

            if ($organizationId != -1) {
                $mockOrganization = $this->getMock('Organization', ['getId']);
                $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
            } else{
                $mockOrganizationRepository->method('find')->will($this->throwException(new SynapseValidationException('Organization Not Found')));
            }

            // Mock OrganizationRoleRepository
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['findOneBy']);

            // Mocking manager service will be used in constructor
            $managerService = $this->getMock('Manager', ['checkAccessToOrganization']);
            if ($isInternal) {
                $mockOrganizationRoleRepository->method('findOneBy')->will($this->throwException(new SynapseValidationException('The logged in person is not a coordinator.')));
                $managerService->method('checkAccessToOrganization')->willReturn(true);
            }

            // Mock ApiValidationService
            $mockApiValidationService = $this->getMock('ApiValidationService', ['updateOrganizationAPIValidationErrorCount']);

            // Mocking OrgAcademicYearRepository
            $mockOrgAcademicYear = $this->getMock('OrgAcademicYear', ['getId']);
            $mockOrgAcademicYear->method('getId')->willReturn($organizationAcademicYearId);
            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['findOneBy']);
            if ($academicYearId === false) {
                $mockOrgAcademicYearRepository->method('findOneBy')->will($this->throwException(new SynapseValidationException('Academic Year Not Found')));
            }
            else {
                $mockOrgAcademicYearRepository->method('findOneBy')->willReturn($mockOrgAcademicYear);
            }


            // Mocking OrgAcademicTermRepository
            $mockOrgAcademicTermRepository = $this->getMock('OrgAcademicTermRepository', ['getAcademicTermsForYear']);
            $mockOrgAcademicTermRepository->method('getAcademicTermsForYear')->willReturn($organizationAcademicTerms);

            // Mocking OrgCoursesRepository
            $mockOrgCourses = $this->getMock('OrgCourses', ['getId']);
            $mockOrgCoursesRepository = $this->getMock('OrgCoursesRepository', ['findOneBy']);
            $mockOrgCoursesRepository->method('findOneBy')->willReturn($mockOrgCourses);

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrganizationRoleRepository],
                    [OrgAcademicYearRepository::REPOSITORY_KEY, $mockOrgAcademicYearRepository],
                    [OrgAcademicTermRepository::REPOSITORY_KEY, $mockOrgAcademicTermRepository],
                    [OrgCoursesRepository::REPOSITORY_KEY, $mockOrgCoursesRepository],
                ]
            );

            $mockContainer->method('get')->willReturnMap([
                [Manager::SERVICE_KEY, $managerService],
                [APIValidationService::SERVICE_KEY, $mockApiValidationService],
            ]);
            try {
                $academicTermService = new AcademicTermService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $yearId = null;
                if (!$isInternal) {
                    $yearId = $organizationAcademicYearId;
                }
                $academicTermList = $academicTermService->listAcademicTerms($organizationId, $organizationAcademicYearId, $loggedInUser, $userType, $isInternal, $yearId);
                $expectedResult = $this->getAcademicTermListResponseDto($organizationId, $organizationAcademicYearId, $organizationAcademicTerms, $isInternal);
                $this->assertEquals($academicTermList, $expectedResult);

            } catch (SynapseException $e) {
                $this->assertEquals($expectedException, $e->getMessage());
            }

        }, [
            'examples' => [
                // Example1: Invalid organization should throw SynapseValidationException for both V1 and V2 API
                [
                    -1,
                    1,
                    201,
                    'staff',
                    true,
                    'Organization Not Found',
                    true,
                    []
                ],
                // Example2: User type not equals to staff, should throw SynapseValidationException only for V1 API
                [
                    1,
                    1,
                    201,
                    'student',
                    true,
                    'The logged in person is not a coordinator.',
                    true,
                    []
                ],
                // Example3: Academic Year Not Found, should throw SynapseValidationException for both V1 and V2 API
                [
                    1,
                    1,
                    201,
                    'staff',
                    false,
                    'Academic Year Not Found',
                    false,
                    []
                ],
                // Example4: Will return organization academic terms for only V1 API and here 1 is passed as $organizationAcademicYearId
                [
                    1,
                    1,
                    201,
                    'staff',
                    true,
                    NULL,
                    true,
                    [
                        [
                            'org_academic_term_id' =>3,
                            'term_code' =>125,
                            'name' =>'Third year',
                            'start_date' => '2017-07-10 14:24:56',
                            'end_date' => '2017-07-15 14:24:56',
                            'is_current_academic_term' => false
                        ],
                        [
                            'org_academic_term_id' =>4,
                            'term_code' =>126,
                            'name' =>'Fourth year',
                            'start_date' => '2017-07-10 14:24:56',
                            'end_date' => '2017-07-15 14:24:56',
                            'is_current_academic_term' => false
                        ],
                    ]
                ],
                // Example5: Will return organization academic terms for V2 API and here 201718 is passed as $yearId
                [
                    1,
                    201718,
                    201,
                    'staff',
                    false,
                    NULL,
                    true,
                    [
                        [
                            'org_academic_term_id' =>null,
                            'term_code' =>null,
                            'name' =>'Third year',
                            'start_date' => '2017-07-10 14:24:56',
                            'end_date' => '2017-07-15 14:24:56',
                            'is_current_academic_term' => false
                        ],
                        [
                            'org_academic_term_id' =>null,
                            'term_code' =>null,
                            'name' =>'Fourth year',
                            'start_date' => '2017-07-10 14:24:56',
                            'end_date' => '2017-07-15 14:24:56',
                            'is_current_academic_term' => false
                        ],
                    ]
                ],
             ]
        ]);
    }

    public function testCreateAcademicTerm()
    {
        $this->specify("Create academic term", function ($organizationId, $organizationAcademicYearId, $termCode, $loggedInUser, $termDuplicate, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);

            $mockLogger = $this->getMock('Logger', ['error', 'debug', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock OrganizationRepository
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find']);
            $mockOrganization = $this->getOrganizationInstance($organizationId);

            if ($organizationId != -1) {
                $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
            } else {
                $mockOrganizationRepository->method('find')->will($this->throwException(new SynapseValidationException('Organization Not Found.')));
            }

            // Mock OrganizationRoleRepository
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['findOneBy']);

            // Mocking manager service will be used in constructor
            $managerService = $this->getMock('Manager', ['checkAccessToOrganization']);

            if ($loggedInUser != -1) {
                $mockOrganizationRoleRepository->method('findOneBy')->willReturn(false);
            } else {
                $mockOrganizationRoleRepository->method('findOneBy')->will($this->throwException(new SynapseValidationException('The logged in person is not a coordinator.')));
            }
            $managerService->method('checkAccessToOrganization')->willReturn(true);


            // Mocking OrgAcademicYearRepository
            $mockOrgAcademicYear = $this->getOrgAcademicYearInstance($organizationId);
            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['find']);

            if ($organizationAcademicYearId === NULL) {
                $mockOrgAcademicYearRepository->method('find')->will($this->throwException(new SynapseValidationException('Academic Year Not Found.')));
            } else {
                $mockOrgAcademicYearRepository->method('find')->willReturn($mockOrgAcademicYear);
            }

            // Mocking OrgAcademicTermRepository
            $mockOrgAcademicTermRepository = $this->getMock('OrgAcademicTermRepository', array('persist'));

            $mockAcademicTerm = $this->getMock('OrgAcademicTerms', ['getId']);
            $mockAcademicTerm->method('getId')->willReturn(1);
            $mockOrgAcademicTermRepository->method('persist')->willReturn($mockAcademicTerm);

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrganizationRoleRepository],
                    [OrgAcademicYearRepository::REPOSITORY_KEY, $mockOrgAcademicYearRepository],
                    [OrgAcademicTermRepository::REPOSITORY_KEY, $mockOrgAcademicTermRepository]
                ]
            );

            // Mock Validator
            $mockValidator = $this->getMock('Validator', ['validate']);
            if ($termDuplicate) {
                $errors = $this->arrayOfErrorObjects(['Term Id already exists.']);
                $mockValidator->method('validate')->willReturn($errors);
            }

            $mockContainer->method('get')->willReturnMap([
                [
                    Manager::SERVICE_KEY,
                    $managerService
                ],
                [
                    SynapseConstant::VALIDATOR,
                    $mockValidator
                ]
            ]);

            try {
                $academicTermService = new AcademicTermService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $academicTermDto = $this->createAcademicTermDto($organizationId, $organizationAcademicYearId, $termCode);
                $academicTermDto = $academicTermService->createAcademicTerm($academicTermDto, $loggedInUser);
                $this->assertEquals($academicTermDto, $expectedResult);
            } catch (SynapseException $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }

        }, [
            'examples' => [
               // Example1: Invalid organization should throw SynapseValidationException
                [
                    -1,
                    48,
                    'ESPRJ15582',
                    5048809,
                    false,
                    'Organization Not Found.'
                ],
                // Example2: Invalid coordinator should throw SynapseValidationException
                [
                    1,
                    48,
                    'ESPRJ15582',
                    -1,
                    false,
                    'The logged in person is not a coordinator.'
                ],
                // Example3: Invalid academic year should throw SynapseValidationException
                [
                    1,
                    null,
                    'ESPRJ15582',
                    5048809,
                    false,
                    'Academic Year Not Found.'
                ],
                // Example4: Term Id already exist should throw SynapseValidationException
                [
                    62,
                    48,
                    'ESPRJ15582',
                    5048809,
                    true,
                    'Term Id already exists.'
                ],
                // Example5: Creates academic term
                [
                    62,
                    48,
                    'Term1-2017',
                    5048809,
                    false,
                    $this->createAcademicTermDto(62, 48, 'Term1-2017', 1)
                ]
            ]
        ]);
    }

    private function getAcademicTermListResponseDto($organizationId, $organizationAcademicYearId, $organizationAcademicTerms, $isInternal)
    {
        $academicTermListResponseDto = new AcademicTermListResponseDto();
        if ($isInternal) {
            $academicTermListResponseDto->setOrganizationId($organizationId);
        }
        $academicTermListResponseDto->setAcademicYearId($organizationAcademicYearId);
        $academicTermsArray = [];
        foreach ($organizationAcademicTerms as $academicTerms) {
            $academicTermsDto = $this->getAcademicTermDto($academicTerms);
            $academicTermsArray[] = $academicTermsDto;
        }
        $academicTermListResponseDto->setAcademicTerms($academicTermsArray);
        return $academicTermListResponseDto;
    }

    private function getAcademicTermDto($academicTerms)
    {
        $academicTermDto = new AcademicTermDto();
        $academicTermDto->setCanDelete(false);
        $academicTermDto->setTermId($academicTerms['org_academic_term_id']);
        $academicTermDto->setTermCode($academicTerms['term_code']);
        $academicTermDto->setName($academicTerms['name']);
        $academicTermDto->setStartDate(new \DateTime($academicTerms['start_date']));
        $academicTermDto->setEndDate(new \DateTime($academicTerms['end_date']));
        $academicTermDto->setCurrentAcademicTermFlag($academicTerms['is_current_academic_term']);
        return $academicTermDto;
    }

    private function createAcademicTermDto($organizationId, $academicYearId, $termCode, $termId = null)
    {
        $academicTermDto = new AcademicTermDto();
        $academicTermDto->setOrganizationId($organizationId);
        $academicTermDto->setAcademicYearId($academicYearId);
        $academicTermDto->setName('Term1');
        $academicTermDto->setTermCode($termCode);
        if (!empty($termId)) {
            $academicTermDto->setTermId($termId);
        }
        $academicTermDto->setStartDate(new \DateTime('2015-12-11'));
        $academicTermDto->setEndDate(new \DateTime('2017-05-06'));
        return $academicTermDto;

    }

    private function getOrganizationInstance($organizationId)
    {
        $organization = new Organization();
        $organization->setCampusId($organizationId);
        return $organization;
    }

    private function getOrgAcademicYearInstance($organizationId)
    {
        $orgAcademicYear = new OrgAcademicYear();
        $orgAcademicYear->setName('201718');
        $orgAcademicYear->setStartDate(new \DateTime('2015-08-17 00:00:00'));
        $orgAcademicYear->setEndDate(new \DateTime('2017-05-06 00:00:00'));
        $orgAcademicYear->setOrganization($this->getOrganizationInstance($organizationId));
        return $orgAcademicYear;
    }

    private function arrayOfErrorObjects($errorArray)
    {
        $returnArray = [];
        foreach ($errorArray as $errorKey => $error) {
            $mockErrorObject = $this->getMock('ErrorObject', ['getMessage']);
            $mockErrorObject->method('getMessage')->willReturn($error);
            $returnArray[] = $mockErrorObject;
        }
        return $returnArray;
    }
}