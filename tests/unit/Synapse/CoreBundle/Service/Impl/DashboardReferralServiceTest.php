<?php
namespace Synapse\CoreBundle\Service\Impl;

use Symfony\Bridge\Monolog\Logger;
use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\CoreBundle\Entity\Person;
use \Synapse\CoreBundle\Entity\Organization;

class DashboardReferralServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    private $orgId = 1;

    private $startPoint = '';

    private $endPoint = '';

    private $sortByField = '';

    private $order = '';

    private $isCsv = false;

    private $uploadType = '';

    private $isJob = false;

    private $person = 1;

    /**
     * @var Person
     */
    private $personMock;
 
    public function testListHistory()
    {
        $this->specify("Test dashboard referral service data for CSV", function ($status, $filter, $offset, $pageNo, $data = '', $sortBy = '', $isCSV = false, $isJob = false)
        {
            
            // Inititializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockReferralsRepository = $this->getMock('ReferralRepository', array(
                'getSentReferralDetails',
                'getRecievedReferralDetails',
                'getReferralDetailsAsInterestedParty',
                'getAllReferralDetails'
            ));
            $mockMetadataListValuesRepository = $this->getMock('MetadataListValuesRepository');
            $mockPersonRepository = $this->getMock('PersonRepository', array(
                'find'
            ));
            $mockOrgAcademicYear = $this->getMock('OrgAcademicYearRepository', array(
                'getCurrentAcademicDetails'
            ));
            
            // Inititializing service to be mocked
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockResque = $this->getMock('resque', array(
                'enqueue'
            ));
            $mockCache = $this->getMock('cache');
            $orgService = $this->getMock('OrganizationService', array(
                'find',
                'getOrgTimeZone'
            ));
            $personService = $this->getMock('PersonService', array(
                'find',
                'getPrimryCoordinatorSortedByName'
            ));
            $utilServiceHelper = $this->getMock('UtilServiceHelper', array(
                'getDateByTimezone'
            ));
            $mockOrg = $this->getMock('Organization', array(
                'getId'
            ));
            
            // Mocking to organization
            $organizationMock = $this->getMockBuilder('Synapse\CoreBundle\Entity\Organization', array(
                'getId'
            ))
                ->disableOriginalConstructor()
                ->getMock();
            $organizationMock->expects($this->any())
                ->method('getId')
                ->will($this->returnValue($this->orgId));
            
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
                ->will($this->returnValue($organizationMock));
            
            // Mocking manager service will be used in constroctor
            $managerService = $this->getMock('Manager');
            
            // Scaffolding for Repository Resolver is using mockBuilder to allow more direct correlation to the Repository Resolver
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                [
                    'SynapseCoreBundle:Referrals',
                    $mockReferralsRepository
                ],
                [
                    'SynapseCoreBundle:MetadataListValues',
                    $mockMetadataListValuesRepository
                ],
                [
                    'SynapseCoreBundle:Person',
                    $mockPersonRepository
                ],
                [
                    'SynapseAcademicBundle:OrgAcademicYear',
                    $mockOrgAcademicYear
                ]
            ]);
            
            // Scaffolding for service is using mockBuilder
            $mockContainer->method('get')
                ->willReturnMap([
                [
                    'org_service',
                    $orgService
                ],
                [
                    'person_service',
                    $personService
                ],
                [
                    'SynapseCoreBundle:Person',
                    $mockPersonRepository
                ],
                [
                    'bcc_resque.resque',
                    $mockResque
                ],
                [
                    'util_service',
                    $utilServiceHelper
                ]
            ]);
            
            $uploadFileLogService = new DashboardReferralService($mockRepositoryResolver, $mockLogger, $mockContainer, $managerService, $mockResque, $utilServiceHelper);
            // Fetching data for csv
            $uploadFileLogServiceData = $uploadFileLogService->getReferralDetailsBasedFilters($this->personMock, $status, $filter, $offset, $pageNo, $data = '', $sortBy = '', true, false);
            
            // Asserting values
            $this->assertEquals('You may continue to use Mapworks while your download completes. We will notify you when it is available.', $uploadFileLogServiceData[0]);
        }, [
            'examples' => [
                [
                    'O',
                    'all',
                    '',
                    '',
                    '',
                    '',
                    true,
                    false
                ]
            ]
        ]);
    }
}