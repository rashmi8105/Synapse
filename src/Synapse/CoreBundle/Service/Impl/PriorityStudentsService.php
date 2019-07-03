<?php
namespace Synapse\CoreBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\job\PriorityStudentsCSVJob;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\PersonDTO;
use Synapse\RestBundle\Entity\RiskLevelsDto;
use Synapse\RiskBundle\Repository\RiskLevelsRepository;
use Synapse\SearchBundle\DAO\PredefinedSearchDAO;
use Synapse\SearchBundle\Service\Impl\StudentListService;


/**
 * @DI\Service("priority_students_service")
 */
class PriorityStudentsService extends AbstractService
{

    const SERVICE_KEY = 'priority_students_service';

    const PERSON_ID = 'personId';

    const RISK_LEVEL = 'risklevel';

    const TOTAL_STUDENTS_BY_RISK = 'totalStudentsByRisk';

    const RISK_TEXT = 'risk_text';

    const ORG_ID = 'orgId';


    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Resque
     */
    private $resque;

    // Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var StudentListService
     */
    private $studentListService;

    // DAO

    /**
     * @var PredefinedSearchDAO
     */
    private $predefinedSearchDAO;


    // Repositories

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var RiskLevelsRepository
     */
    private $riskLevelsRepository;


    /**
     * @DI\InjectParams({
     *      "repositoryResolver" = @DI\Inject("repository_resolver"),
     *      "logger" = @DI\Inject("logger"),
     *      "container" = @DI\Inject("service_container"),
     * })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->studentListService = $this->container->get(StudentListService::SERVICE_KEY);


        // DAO
        $this->predefinedSearchDAO = $this->container->get(PredefinedSearchDAO::DAO_KEY);

        // Repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->riskLevelsRepository = $this->repositoryResolver->getRepository(RiskLevelsRepository::REPOSITORY_KEY);
    }

    /**
     * Builds a PersonDTO containing information about counts of students for each risk level as well as high priority students
     *
     * @param int $personId
     * @return PersonDTO
     */
    public function getMyStudentsDashboard($personId)
    {
        $personObject = $this->personRepository->find($personId, new SynapseValidationException('Person not found'));
        $organizationId = $personObject->getOrganization()->getId();

        $personDto = new PersonDTO();
        $currentAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
        if (isset($currentAcademicYear['org_academic_year_id'])) {
            $orgAcademicYearId = $currentAcademicYear['org_academic_year_id'];
        } else {
            $orgAcademicYearId = null;
        }
        $highPriorityStudentsCount = $this->personRepository->getMyHighPriorityStudentsCount($personId, $organizationId, $orgAcademicYearId);
        $studentCountByRiskLevel = $this->personRepository->getStudentCountByRiskLevel($personId, $organizationId, $orgAcademicYearId);
        $totalStudents = 0;
        if (!empty($studentCountByRiskLevel)) {
            $totalStudents = array_sum(array_column($studentCountByRiskLevel, 'count'));
        }
        $personDto->setPersonId($personId);
        $riskLevelsDtoData = [];
        $riskLevels = $this->riskLevelsRepository->findAll();

        if ($totalStudents && !empty($studentCountByRiskLevel)) {
            foreach ($studentCountByRiskLevel as $studentCountForThisRiskLevel) {
                $riskText = $studentCountForThisRiskLevel['risk_text'];
                $riskPercentage = round(($studentCountForThisRiskLevel['count'] / $totalStudents) * 100);
                $riskLevelsDtoData[] = $this->setRiskLevelsDtoResponse($riskText, $studentCountForThisRiskLevel['count'], $riskPercentage, $studentCountForThisRiskLevel['color_hex']);
            }
        } else {
            foreach ($riskLevels as $risk) {
                $riskLevelsDtoData[] = $this->setRiskLevelsDtoResponse($risk->getRiskText(), 0, 0, $risk->getColorHex());
            }
        }
        $personDto->setTotalHighPriorityStudents($highPriorityStudentsCount);
        $riskLevelsDtoResponse = $riskLevelsDtoData;
        $personDto->setRiskLevels($riskLevelsDtoResponse);
        $personDto->setTotalStudents($totalStudents);
        return $personDto;
    }

    /**
     * Set risk level response
     *
     * @param string $riskText
     * @param int $totalStudentsCount
     * @param int|float $riskPercentage
     * @param string $color
     * @return RiskLevelsDto $riskLevelsDto
     */
    private function setRiskLevelsDtoResponse($riskText, $totalStudentsCount, $riskPercentage, $color)
    {
        $riskLevelsDto = new RiskLevelsDto();
        $riskLevelsDto->setRiskLevel($riskText);
        $riskLevelsDto->setTotalStudents($totalStudentsCount);
        $riskLevelsDto->setRiskPercentage($riskPercentage);
        $riskLevelsDto->setColorValue($color);
        return $riskLevelsDto;
    }


    /**
     * Returns the requested page of results for the requested drilldown from the My Students module, sorted as requested.
     *
     * @param string $searchKey
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $sortBy
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param boolean $onlyIncludeActiveStudents
     * @return array
     */
    public function getMyStudents($searchKey, $loggedInUserId, $organizationId, $sortBy, $pageNumber, $recordsPerPage, $onlyIncludeActiveStudents=true)
    {
        $studentIds = $this->getStudentsForMyStudentsModule($searchKey, $loggedInUserId, $organizationId, $onlyIncludeActiveStudents);

        $dataToReturn = $this->studentListService->getStudentListWithMetadata($studentIds, $loggedInUserId, $organizationId, $sortBy, $pageNumber, $recordsPerPage);

        return $dataToReturn;
    }


    /**
     * Returns an array of the person_ids of all students included in the given drilldown from the My Students module.
     *
     * @param string $searchKey
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param boolean $onlyIncludeActiveStudents
     * @return array
     */
    private function getStudentsForMyStudentsModule($searchKey, $loggedInUserId, $organizationId, $onlyIncludeActiveStudents = true)
    {
        $currentOrgAcademicYearArray = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
        if ($currentOrgAcademicYearArray) {
            $currentOrgAcademicYear = $currentOrgAcademicYearArray['org_academic_year_id'];
        } else {
            return [];
        }

        switch ($searchKey) {
            case 'my_students':
                $studentIds = $this->predefinedSearchDAO->getAllMyStudents($loggedInUserId, $organizationId, $currentOrgAcademicYear, $onlyIncludeActiveStudents, true);
                break;
            case 'high_priority_students':
                $studentIds = $this->predefinedSearchDAO->getHighPriorityStudents($loggedInUserId, $currentOrgAcademicYear, $onlyIncludeActiveStudents);
                break;
            case 'gray':
                $studentIds = $this->predefinedSearchDAO->getStudentsWithGrayRisk($loggedInUserId, $organizationId, $currentOrgAcademicYear, $onlyIncludeActiveStudents);
                break;
            case 'red2':
            case 'red':
            case 'yellow':
            case 'green':
                $studentIds = $this->predefinedSearchDAO->getStudentsWithSpecifiedRiskColor($loggedInUserId, $organizationId, $searchKey, $currentOrgAcademicYear, $onlyIncludeActiveStudents);
                break;
            default:
                $studentIds = [];
        }
        return $studentIds;
    }


    /**
     * Returns a list of all students, including their ids and names, for the given drilldown from the My Students module.
     * This list is used to set up a bulk action.
     *
     * @param string $searchKey
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param boolean $onlyIncludeActiveStudents
     * @return array
     */
    public function getIdsAndNamesOfMyStudents($searchKey, $loggedInUserId, $organizationId, $onlyIncludeActiveStudents = false)
    {
        $studentIds = $this->getStudentsForMyStudentsModule($searchKey, $loggedInUserId, $organizationId, $onlyIncludeActiveStudents);
        $dataToReturn = $this->studentListService->getStudentIdsAndNames($studentIds, $loggedInUserId);
        return $dataToReturn;
    }


    /**
     * Creates a job which will create a CSV of all students in the given drilldown from the My Students module.
     *
     * @param string $searchKey
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $sortBy
     * @param boolean $onlyIncludeActiveStudents
     * @return string
     */
    public function createMyStudentsJob($searchKey, $loggedInUserId, $organizationId, $sortBy, $onlyIncludeActiveStudents = true)
    {
        $job = new PriorityStudentsCSVJob();
        $job->args = [
            'search_key' => $searchKey,
            'faculty_id' => $loggedInUserId,
            'organization_id' => $organizationId,
            'sort_by' => $sortBy,
            'only_include_active_students' => $onlyIncludeActiveStudents
        ];
        $this->resque->enqueue($job, true);
        return SynapseConstant::DOWNLOAD_IN_PROGRESS_MESSAGE;
    }


    /**
     * Returns the results of the given drilldown from the My Students module for use in creating a CSV.
     *
     * @param string $searchKey
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $sortBy
     * @param boolean $onlyIncludeActiveStudents
     * @return array
     */
    public function getMyStudentsForCSV($searchKey, $loggedInUserId, $organizationId, $sortBy, $onlyIncludeActiveStudents = true)
    {
        $studentIds = $this->getStudentsForMyStudentsModule($searchKey, $loggedInUserId, $organizationId, $onlyIncludeActiveStudents);
        $dataToReturn = $this->studentListService->getStudentListWithAdditionalDataForCSV($studentIds, $loggedInUserId, $organizationId, $sortBy);
        return $dataToReturn;
    }

}