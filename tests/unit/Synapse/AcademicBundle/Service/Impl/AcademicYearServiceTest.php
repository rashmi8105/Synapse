<?php
namespace Synapse\AcademicBundle\Service\Impl;


use Codeception\Specify;
use Faker\Provider\cs_CZ\DateTime;
use Symfony\Component\Validator\Constraints\Date;
use Synapse\AcademicBundle\Entity\OrgAcademicYear;
use Synapse\AcademicBundle\Entity\Year;
use Synapse\AcademicBundle\EntityDto\AcademicTermDto;
use Synapse\AcademicBundle\EntityDto\AcademicYearDto;
use Synapse\AcademicBundle\EntityDto\AcademicYearListResponseDto;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;

class AcademicYearServiceTest extends \Codeception\Test\Unit
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


    public function testListAcademicYears()
    {
        $this->specify("Test list of academic years", function ($organizationId, $excludeFutureYears, $excludePastYears, $isInternal, $expectedException, $inputData) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['error', 'debug', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock OrganizationRepository
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find']);
            $mockOrganization = null;
            if (!empty($organizationId) && $organizationId != -1) {
                $mockOrganization = $this->getMock('Organization', ['getId']);
            }
            $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
            $mockOrganization = $this->getMock('Organization', ['getId']);
            $mockOrganizationRepository->method('find')->willReturn($mockOrganization);

            // Mock orgAcademicYearRepository
            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['getAllAcademicYearsWithTerms']);
            $mockOrgAcademicYearRepository->method('getAllAcademicYearsWithTerms')->willReturn($inputData);

            // Mock OrgAcademicTermRepository
            $mockOrgAcademicTermRepository = $this->getMock('OrgAcademicTermRepository', ['findOneBy']);
            $mockOrgAcademicTerm = $this->getMock('OrgAcademicTerm', ['getId']);
            $mockOrgAcademicTermRepository->method('findOneBy')->willReturn($mockOrgAcademicTerm);

            // Mocking Manager service
            $managerService = $this->getMock('Manager', ['checkAccessToOrganization']);

            if ($isInternal) {
                $managerService->method('checkAccessToOrganization')->willReturn(true);
            }

            // Mock DateUtilityService
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['adjustOrganizationDateTimeStringToUtcDateTimeObject']);
            $mockDateUtilityService->method('adjustOrganizationDateTimeStringToUtcDateTimeObject')->willReturnCallback(function ($inputData) {
                return $inputData;
            });

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [OrgAcademicYearRepository::REPOSITORY_KEY, $mockOrgAcademicYearRepository],
                    [OrgAcademicTermRepository::REPOSITORY_KEY, $mockOrgAcademicTermRepository],
                ]
            );

            $mockContainer->method('get')->willReturnMap([
                [Manager::SERVICE_KEY, $managerService],
                [DateUtilityService::SERVICE_KEY, $mockDateUtilityService],
            ]);

            try {
                $academicYearService = new AcademicYearService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $academicYears = $academicYearService->listAcademicYears($organizationId, $excludeFutureYears, $excludePastYears, $isInternal);
                $expectedResult = $this->getAcademicYearListResponseDto($organizationId, $isInternal, $inputData);
                $this->assertEquals($expectedResult, $academicYears);
            } catch (SynapseException $e) {
                $this->assertEquals($expectedException, $e->getMessage());
            }
        }, ['examples' => [
                [
                    // Example1: Invalid organization should throw SynapseValidationException for V1 API and here -1 is passed as an invalid organization id and $isInternal flag is true
                    -1,
                    false,
                    false,
                    true,
                    'Organization Not Found',
                    ''
                ],
               [
                   // Example2: Invalid organization should throw SynapseValidationException for V2 API and here -1 is passed as an invalid organization id and $isInternal flag is false
                    -1,
                    false,
                    false,
                    false,
                    'Organization Not Found',
                    ''
                ],
                [
                    // Example3: Will return organization academic year for only V1 API and here 1 is passed as organization id and $isInternal flag is true
                    1,
                    false,
                    false,
                    true,
                    '',
                    [
                        [
                            "id" => 48,
                            "year_name" => "2015-2016",
                            "year_id" => "201516",
                            "year_start_date" => "2015-08-17",
                            "year_end_date" => "2017-05-06",
                            "year_status" => "future",
                            "term_id" => 1,
                            "term_name" => "Spring",
                            "term_start_date" => "2014-08-18",
                            "term_end_date" => " 2015-06-30",
                            "term_code" => 141508
                        ]
                    ]
                ],
                [
                    // Example4: Will return organization academic year for only V2 API and here 1 is passed as organization id and $isInternal flag is false
                    1,
                    false,
                    false,
                    false,
                    '',
                    [
                        [
                            "id" => 49,
                            "year_name" => "2016-2017",
                            "year_id" => "201617",
                            "year_start_date" => "2016-08-17",
                            "year_end_date" => "2017-05-06",
                            "year_status" => "past",
                            "term_id" => 2,
                            "term_name" => "Fall",
                            "term_start_date" => "2016-08-18",
                            "term_end_date" => " 2017-06-30",
                            "term_code" => 141509
                        ]
                    ]
                ]
            ]
            ]
        );
    }

    private function getAcademicYearListResponseDto($organizationId, $isInternal, $inputData)
    {
        $academicYearListResponseDto = new AcademicYearListResponseDto();
        if($isInternal) {
            $academicYearListResponseDto->setOrganizationId($organizationId);
        }
        $academicYearArray = [];
        $academicYearDto = new AcademicYearDto();
        foreach ($inputData as $data) {

            $academicTermsArray = [];
            if (!$isInternal) {
                $academicTermDto = new AcademicTermDto();
                $academicTermDto->setName($data['term_name']);
                $academicTermDto->setTermId($data['term_code']);
                $termStartDate = $data['term_start_date'];
                $termEndDate = $data['term_end_date'];
                $academicTermDto->setStartDate($termStartDate);
                $academicTermDto->setEndDate($termEndDate);
                $academicTermsArray[] = $academicTermDto;
                $academicYearDto->setAcademicTerms($academicTermsArray);
            }
            if ($isInternal) {
                $academicYearDto->setId($data['id']);
                $academicYearDto->setCanDelete(false);
            }
            $academicYearDto->setName($data['year_name']);
            $academicYearDto->setYearId($data['year_id']);
            $academicYearDto->setStartDate($data['year_start_date']);
            $academicYearDto->setEndDate($data['year_end_date']);
            if ($data['year_status'] == 'current') {
                $academicYearDto->setIsCurrentYear(true);
            } else {
                $academicYearDto->setIsCurrentYear(false);
            }
            $academicYearArray[] = $academicYearDto;
        }
        $academicYearListResponseDto->setAcademicYears($academicYearArray);
        return $academicYearListResponseDto;
    }

    public function testValidGetSelectedAcademicYearIdAndFollowingAcademicYearIds()
    {
        $orgId = 99;
        $academicYearID = 27;
        $limit = 3;
        $expectedResult = array();

        $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
        $mockCache = $this->getMock('cache', array('run'));
        $mockResque = $this->getMock('resque', array('enqueue'));
        $mockLogger = $this->getMock('Logger', array('debug', 'error'));
        $mockContainer = $this->getMock('Container', array('get'));

        $mockAcademicYearRepo = $this->getMock('academicYearRepo', array('findFutureYears', 'find'));
        $mockObject = $this->getMock('date', array('format'));
        $mockFutureYears = array();

        $mockContainer->expects($this->any())
            ->method('get')
            ->willReturnMap(
                [
                    [
                        SynapseConstant::REDIS_CLASS_KEY,
                        $mockCache
                    ],
                    [
                        SynapseConstant::RESQUE_CLASS_KEY,
                        $mockResque
                    ]
                ]);

        $mockRepositoryResolver->method('getRepository')
            ->willReturnMap([
                [
                    OrgAcademicYearRepository::REPOSITORY_KEY,
                    $mockAcademicYearRepo
                ]
            ]);

        if ($academicYearID == null) {
            $mockAcademicYearObj = null;

            $mockAcademicYearRepo->expects($this->at(0))->method('find')->willReturn($mockAcademicYearObj);

            $academicYearService = new AcademicYearService(
                $mockRepositoryResolver,
                $mockLogger,
                $mockContainer
            );

            $futureYears = $academicYearService->getSelectedAcademicYearIdAndFollowingAcademicYearIds($orgId, $academicYearID, $limit);

        } else {
            $mockAcademicYearObj = $this->getMock('academicYearObj', array('getStartDate'));
            $mockAcademicYearObj->expects($this->at(0))->method('getStartDate')->willReturn($mockObject);

            $mockAcademicYearRepo->expects($this->at(0))->method('find')->willReturn($mockAcademicYearObj);
            $mockObject->expects($this->at(0))->method('format')->willReturn('');
            $mockAcademicYearRepo->expects($this->at(1))->method('findFutureYears')->willReturn($mockFutureYears);

            $academicYearService = new AcademicYearService(
                $mockRepositoryResolver,
                $mockLogger,
                $mockContainer
            );

            $futureYears = $academicYearService->getSelectedAcademicYearIdAndFollowingAcademicYearIds($orgId, $academicYearID, $limit);

            $this->assertEquals($expectedResult, $futureYears);
        }
    }

    /**
     * @expectedException \Synapse\CoreBundle\Exception\SynapseValidationException
     */
    public function testInvalidGetSelectedAcademicYearIdAndFollowingAcademicYearIds()
    {
        $orgId = 99;
        $academicYearID = null;
        $limit = 3;

        $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
        $mockLogger = $this->getMock('Logger', array('debug', 'error'));
        $mockContainer = $this->getMock('Container', array('get'));

        $mockAcademicYearRepo = $this->getMock('academicYearRepo', array('findFutureYears', 'find'));
        $mockObject = $this->getMock('date', array('format'));
        $mockFutureYears = array();

        if ($academicYearID == null) {
            $mockAcademicYearObj = null;

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                ['SynapseAcademicBundle:OrgAcademicYear', $mockAcademicYearRepo]
            ]);
            $mockAcademicYearRepo->expects($this->at(0))->method('find')->willReturn($mockAcademicYearObj);

            $academicYearService = new AcademicYearService(
                $mockRepositoryResolver,
                $mockLogger,
                $mockContainer
            );

            $futureYears = $academicYearService->getSelectedAcademicYearIdAndFollowingAcademicYearIds($orgId, $academicYearID, $limit);

        } else {
            $mockAcademicYearObj = $this->getMock('academicYearObj', array('getStartDate'));
            $mockAcademicYearObj->expects($this->at(0))->method('getStartDate')->willReturn($mockObject);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                ['SynapseAcademicBundle:OrgAcademicYear', $mockAcademicYearRepo]
            ]);
            $mockAcademicYearRepo->expects($this->at(0))->method('find')->willReturn($mockAcademicYearObj);
            $mockObject->expects($this->at(0))->method('format')->willReturn('');
            $mockAcademicYearRepo->expects($this->at(1))->method('findFutureYears')->willReturn($mockFutureYears);

            $academicYearService = new AcademicYearService(
                $mockRepositoryResolver,
                $mockLogger,
                $mockContainer
            );

            $futureYears = $academicYearService->getSelectedAcademicYearIdAndFollowingAcademicYearIds($orgId, $academicYearID, $limit);
        }
    }

    public function testGetCurrentOrganizationAcademicYearYearID()
    {
        $this->specify("Test to get Current Organization Academic Year Year ID", function ($organizationId, $yearId, $throwException, $expectedResult) {

            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['getCurrentYearId']);
            $currentYearId = [
                "0" => [
                    'id' => $organizationId,
                    'yearId' => $yearId
                ]
            ];
            $mockOrgAcademicYearRepository->method('getCurrentYearId')->willReturn($currentYearId);

            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgAcademicYearRepository::REPOSITORY_KEY,
                        $mockOrgAcademicYearRepository
                    ]
                ]);
            // Mock DateUtilityService
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['getCurrentFormattedDateTimeForOrganization']);
            $currentDate = new \DateTime();
            $mockDateUtilityService->method('getCurrentFormattedDateTimeForOrganization')->willReturn($currentDate);
            $this->mockContainer->method('get')
                ->willReturnMap([
                    [
                        DateUtilityService::SERVICE_KEY,
                        $mockDateUtilityService
                    ]
                ]);
            try {
                $academicYearService = new AcademicYearService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
                $results = $academicYearService->getCurrentOrganizationAcademicYearYearID($organizationId, $throwException);

                $this->assertEquals($results, $expectedResult);

            } catch (SynapseException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // current year and and no exception condition
                [
                    99,
                    "201617",
                    false,
                    "201617"
                ],
                // No year id and no exception condition
                [
                    99,
                    "",
                    false,
                    null
                ],
                // No year id throw exception condition
                [
                    99,
                    null,
                    true,
                    "There is no currently active academic year."
                ]
            ]
        ]);
    }

}