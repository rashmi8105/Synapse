<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\job\ReferralsManagementDashboardCSVJob;
use Synapse\CoreBundle\Repository\ReferralRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\ReferralConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\CoreBundle\Util\UtilServiceHelper;
use Synapse\RestBundle\Entity\ReferralDetailsResponseDto;
use Synapse\RestBundle\Entity\ReferralsArrayResponseDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SearchBundle\EntityDto\SearchDto;
use Synapse\SearchBundle\EntityDto\SearchResultListDto;

/**
 * @DI\Service("dashboardreferral_service")
 */
class DashboardReferralService extends ReferralHelperService
{

    const SERVICE_KEY = 'dashboardreferral_service';

    const PAGE_NO = 1;
    
    const OFFSET = 25;

    // Scaffolding

    /**
     * @var container
     */
    private $container;

    /**
     * @var Manager
     */
    public $rbacManager;

    // Services

    /**
     *  @var UtilServiceHelper
     */
    private $utilServiceHelper;

    /**
     * @var PersonService
     */
    private $personService;


    // Repositories

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var ReferralRepository
     */
    private $referralRepository;

    /**
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        // Scaffolding
        $this->container = $container;
        $this->rbacManager = $this->container->get('tinyrbac.manager');
        $this->resque = $this->container->get('bcc_resque.resque');

        // Services
        $this->utilServiceHelper = $this->container->get(UtilServiceHelper::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);

        // Repositories
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->referralRepository = $this->repositoryResolver->getRepository(ReferralRepository::REPOSITORY_KEY);

    }

    /**
     * Get referral details based for logged in user with status and filter
     *
     * @param Person $person
     * @param string $status
     * @param string $filter
     * @param int $offset
     * @param int $pageNo
     * @param string $data
     * @param string $sortBy
     * @param bool $isCSV
     * @param bool $isJob
     * @return ReferralDetailsResponseDto
     * @throws SynapseValidationException
     */
    public function getReferralDetailsBasedFilters(Person $person, $status, $filter, $offset, $pageNo, $data = '', $sortBy = '', $isCSV = false, $isJob = false)
    {
        $userId = $person->getId();
        $organizationId = $person->getOrganization()->getId();
        $primaryCoordinatorName = $this->personService->getPrimryCoordinatorSortedByName($organizationId, ReferralConstant::PRIMARYCOORDINATOR);
        $pageNo = (int) $pageNo;
        if(! $pageNo){
            $pageNo = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }
        $offset = (int) $offset;
        if(! $offset){
            $offset = SynapseConstant::DEFAULT_RECORD_COUNT;
        }
        $startPoint = ($pageNo * $offset) - $offset;
        
        $orgTimeZone = $person->getOrganization()->getTimezone();
        $currentDateTime = $this->utilServiceHelper->getDateByTimezone($orgTimeZone);

        // Get Student List check
        $studentListFlag = false;
        if($data === 'student-list'){
            $studentListFlag = true;
        }

        // Added this to find teh currevt academic year start and end  for restricting the view to  current academic year
        $date = new \DateTime('now');
        $currentDate = $date->setTime(0, 0, 0);
        $orgAcademicYear = $this->orgAcademicYearRepository->getCurrentAcademicDetails($currentDate, $organizationId);
        $academicStartDate = $orgAcademicYear[0]['startDate'];
        $academicEndDate = $orgAcademicYear[0]['endDate'];

        if($isCSV){
            $jobObj = 'Synapse\CoreBundle\job\ReferralsManagementDashboardCSVJob';
            $jobNumber = uniqid();
            $job = new $jobObj();
            $job->args = array(
                'jobNumber' => $jobNumber,
                'loggedInUser' => $userId,
                'currentDateTime' => $currentDateTime,
                'person' => $person,
                'status' => $status,
                'filter' => $filter,
                'offset' => $offset,                
                'pageNo' => $pageNo,
                'data' => '', 
                'sortBy' => $sortBy,
                'orgId' => $organizationId
            );
            $this->resque->enqueue($job, true);
            return [SynapseConstant::DOWNLOAD_IN_PROGRESS_MESSAGE];
        }
        // End of finding the academic year

        switch(strtolower($filter)){
            case "sent":
                $referralDetails = $this->referralRepository->getSentReferralDetails($userId, $organizationId, $status, $startPoint, $offset, $studentListFlag, $sortBy,$academicStartDate,$academicEndDate, $primaryCoordinatorName, $isJob);
                break;
            case "received":
                $referralDetails = $this->referralRepository->getRecievedReferralDetails($userId, $organizationId, $status, $startPoint, $offset, $studentListFlag, $sortBy,$academicStartDate,$academicEndDate, $primaryCoordinatorName, $isJob);
                break;
            case "isinterestedparty":
                $referralDetails = $this->referralRepository->getReferralDetailsAsInterestedParty($userId, $organizationId, $status, $startPoint, $offset, $studentListFlag, $sortBy,$academicStartDate,$academicEndDate, $primaryCoordinatorName, $isJob);
                break;
            case "all":
                $referralDetails = $this->referralRepository->getAllReferralDetails($userId, $organizationId, $status, $startPoint, $offset, $studentListFlag, $sortBy,$academicStartDate,$academicEndDate, $primaryCoordinatorName, $isJob);
                break;
            default:
                throw new SynapseValidationException('Not a valid Filter');
                break;
        }
        
        if($isJob) {
            return $referralDetails;
        }
        
        // Get the student list based on filters used
        if($studentListFlag){
            $students = $this->getStudentList($referralDetails, $userId);
            return $students;
        }else{
            
            $countQuery = "SELECT FOUND_ROWS() cnt";
            $count = $this->referralRepository->getCountOfReferrals($countQuery);
            
            $refCount = $count[0]['cnt'];
            
            $totalPageCount = ceil($refCount / $offset);
            $referralDetailsDto = new ReferralDetailsResponseDto();
            $referralDetailsDto->setPersonId($userId);
            $referralDetailsDto->setOrganizationId($organizationId);
            $referralDetailsDto->setTotalPages($totalPageCount);
            $referralDetailsDto->setTotalRecords($refCount);
            $referralDetailsDto->setRecordsPerPage($offset);
            $referralDetailsDto->setCurrentPage($pageNo);
            $referralArray = [];
            
            $timeZone = $this->utilServiceHelper->getDateByTimezone($orgTimeZone,'', true);
            
            foreach($referralDetails as $referral){
                $referralDto = new ReferralsArrayResponseDto();
                $referralDto->setReferralId($referral['referral_id']);
                $referralDto->setStudentId($referral['student_id']);
                $referralDto->setStudentFirstName($referral['student_first_name']);
                $referralDto->setStudentLastName($referral['student_last_name']);
                $referralDto->setReasonId($referral['reason_id']);
                $referralDto->setReasonText($referral['reason_text']);
                
                $referralDate = new \DateTime($referral['referral_date']);

                $referralDate->setTimezone(new \DateTimeZone($timeZone));
                $referralDto->setReferralDate($referralDate);
                
                if(!empty($referral['assigned_to_name'])){
                    $assignedTo = explode(',', $referral['assigned_to_name']);
                    $referralDto->setAssignedToFirstName($assignedTo[1]);
                    $referralDto->setAssignedToLastName($assignedTo[0]);
                }else{
                    $referralDto->setAssignedToFirstName('');
                    $referralDto->setAssignedToLastName('');
                }
                // Set CreatedBy Person Faculty name
                $referralDto->setCreatedByFirstName($referral['created_by_first_name']);
                $referralDto->setCreatedByLastName($referral['created_by_last_name']);
                $referralDto->setStatus($referral['status']);
                $referralArray[] = $referralDto;
            }

            $referralDetailsDto->setReferrals($referralArray);
            return $referralDetailsDto;
        }
    }
    
    private function getAssignToDetails($referral, $orgId, $activityCatId)
    {
        $assignTo = $referral->getPersonAssignedTo();
        $assignTodetails = [];
        if($assignTo){
            $assignTodetails['first_name'] = $assignTo->getFirstname();
            $assignTodetails['last_name'] = $assignTo->getLastname();
        }else{
            $referralRoutingRulesRepo = $this->repositoryResolver->getRepository('SynapseCoreBundle:ReferralRoutingRules');
            $referralRouting = $referralRoutingRulesRepo->findOneBy(array(
                'organization' => $orgId,
                'activityCategory' => $activityCatId
            ));
            if(isset($referralRouting)){
                if($referralRouting->getIsPrimaryCoordinator()){
                    $assignTodetails['first_name'] = 'Central';
                    $assignTodetails['last_name'] = 'Coordinator';
                }else{
                    $personAssignedTo = $this->container->get('person_service')->findPerson($referralRouting->getPerson()->getId());
                    $assignTodetails['first_name'] = $personAssignedTo->getFirstname();
                    $assignTodetails['last_name'] = $personAssignedTo->getLastname();
                }
            }
        }
        return $assignTodetails;
    }
    
    private function getStudentList($referralDetails, $userId){
        
        $studentListDto = new SearchDto();
        $studentListDto->setPersonId($userId);
        
        $studentArr = [];
        foreach ($referralDetails as $referral){
            
            $studentDto = new SearchResultListDto();
            $studentDto->setStudentId($referral['student_id']);
            $studentDto->setStudentFirstName($referral['student_first_name']);
            $studentDto->setStudentLastName($referral['student_last_name']);
            
            $studentArr[] = $studentDto;
        }
        
        $studentListDto->setSearchResult($studentArr);
        return $studentListDto;
    }
    
}