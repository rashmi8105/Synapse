<?php

namespace Synapse\MapworksToolBundle\Service\Impl;

use Faker\Provider\DateTime;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\CoreBundle\Service\Utility\URLUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\PersonConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\MapworksToolBundle\Entity\MapworksToolLastRun;
use Synapse\MapworksToolBundle\EntityDto\IssuePaginationDTO;
use Synapse\MapworksToolBundle\EntityDto\TopIssuesDTO;
use Synapse\MapworksToolBundle\EntityDto\IssuesInputDTO;
use Synapse\MapworksToolBundle\EntityDto\ToolAnalysisDTO;
use Synapse\MapworksToolBundle\Repository\MapworksToolLastRunRepository;
use Synapse\MapworksToolBundle\Repository\MapworksToolRepository;
use Synapse\MapworksToolBundle\Repository\OrgPermissionsetToolRepository;
use Synapse\PdfBundle\Service\Impl\PdfDetailsService;
use Synapse\SurveyBundle\Job\IssueCSVJob;
use Synapse\SurveyBundle\Repository\WessLinkRepository;
use Synapse\SurveyBundle\Service\Impl\IssueService;

/**
 * @DI\Service("mapworks_tool_service")
 */
class MapworksToolService extends AbstractService
{
    const SERVICE_KEY = 'mapworks_tool_service';

    const TOP_ISSUES_SHORT_CODE = 'T-I';

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
     * @var IssueService
     */
    private $issueService;

    /**
     * @var OrgPermissionsetService
     */
    private $orgPermissionsetService;

    /**
     * @var PdfDetailsService
     */
    private $pdfDetailsService;

    /**
     * @var URLUtilityService
     */
    private $urlUtilityService;

    //Repositories

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var MapworksToolLastRunRepository
     */
    private $mapworksToolLastRunRepository;

    /**
     * @var MapworksToolRepository
     */
    private $mapworksToolRepository;

    /**
     * @var orgPermissionsetToolRepository
     */
    private $orgPermissionsetToolRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var WessLinkRepository
     */
    private $wessLinkRepository;

    /**
     * @DI\InjectParams({
     *     "repositoryResolver" = @DI\Inject("repository_resolver"),
     *     "logger" = @DI\Inject("logger"),
     *     "container" = @DI\Inject("service_container")
     * })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        //Scaffolding
        $this->container = $container;
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        //Services
        $this->issueService = $this->container->get(IssueService::SERVICE_KEY);
        $this->orgPermissionsetService = $this->container->get(OrgPermissionsetService::SERVICE_KEY);
        $this->pdfDetailsService = $this->container->get(PdfDetailsService::SERVICE_KEY);
        $this->urlUtilityService = $this->container->get(URLUtilityService::SERVICE_KEY);

        //Repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->mapworksToolRepository = $this->repositoryResolver->getRepository(MapworksToolRepository::REPOSITORY_KEY);
        $this->mapworksToolLastRunRepository = $this->repositoryResolver->getRepository(MapworksToolLastRunRepository::REPOSITORY_KEY);
        $this->orgPermissionsetToolRepository = $this->repositoryResolver->getRepository(OrgPermissionsetToolRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->wessLinkRepository = $this->repositoryResolver->getRepository(wessLinkRepository::REPOSITORY_KEY);
    }

    /**
     * Gets all available tools for the faculty dashboard tool module
     *
     * @param int $facultyId
     * @param int $organizationId
     * @return array
     */
    public function getToolAnalysisData($facultyId, $organizationId)
    {
        $toolAnalysisResponse = [];
        $permissionIds = $this->orgPermissionsetService->getPermissionSetIdsOfUser($facultyId);

        // determine the closed date of the survey
        $surveyClosedDate = $this->wessLinkRepository->getSurveyClosedDateForFaculty($facultyId, $organizationId);
        $mapworksToolEntityDetails = $this->mapworksToolRepository->findAll();

        // iterate over all tools and fetch tool last run using toolId
        foreach ($mapworksToolEntityDetails as $mapworksToolObject) {

            $mapworksToolId = $mapworksToolObject->getId();
            $mapworksToolName = $mapworksToolObject->getToolName();
            $mapworksToolShortCode = $mapworksToolObject->getShortCode();
            $mapworksToolOrder = $mapworksToolObject->getToolOrder();

            $hasCurrentToolAccess = false;
            foreach ($permissionIds as $id) { // check tool id for each permission set id
                $permissionSetTool = $this->orgPermissionsetToolRepository->findOneBy([
                    'orgPermissionset' => $id,
                    'mapworksToolId' => $mapworksToolId
                ]);

                if ($permissionSetTool) {
                    $hasCurrentToolAccess = true;
                    break;
                }
            }

            if ($hasCurrentToolAccess) {
                $mapworksToolLastRunData = $this->mapworksToolLastRunRepository->findOneBy([
                    'toolId' => $mapworksToolId,
                    'personId' => $facultyId
                ]);

                if (empty($mapworksToolLastRunData)) {
                    $mapworksToolLastRunDate = null;
                } else {
                    $mapworksToolLastRunDate = $mapworksToolLastRunData->getLastRun()->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
                }

                $hasNewDataSinceLastRunDate = false;
                // survey closed date compared with the last run
                if (empty($mapworksToolLastRunData) || ($mapworksToolLastRunDate < $surveyClosedDate['close_date'])) {
                    $hasNewDataSinceLastRunDate = true;
                }

                $mapworksToolLastRunDate = !empty($mapworksToolLastRunData) ? new \DateTime($mapworksToolLastRunDate) : null;
                $toolAnalysisDtoObject = $this->generateToolAnalysisDTO($mapworksToolName, $mapworksToolShortCode, $mapworksToolOrder, $hasNewDataSinceLastRunDate, $mapworksToolLastRunDate);
                $toolAnalysisResponse[] = $toolAnalysisDtoObject;
            }

        }
        return $toolAnalysisResponse;
    }

    /**
     * Generates a toolAnalysisDTO
     *
     * @param string $toolName
     * @param string $shortCode
     * @param int $toolOrder
     * @param boolean $hasNewDataSinceLastRunDate
     * @param \DateTime $mapworksToolLastRunDate
     * @return ToolAnalysisDTO
     */
    public function generateToolAnalysisDTO($toolName, $shortCode, $toolOrder, $hasNewDataSinceLastRunDate, $mapworksToolLastRunDate)
    {
        $toolAnalysisDTO = new ToolAnalysisDTO();
        $toolAnalysisDTO->setToolName($toolName);
        $toolAnalysisDTO->setShortCode($shortCode);
        $toolAnalysisDTO->setHasNewDataSinceLastRunDate($hasNewDataSinceLastRunDate);
        $toolAnalysisDTO->setLastRunDate($mapworksToolLastRunDate);
        $toolAnalysisDTO->setToolOrder($toolOrder);
        return $toolAnalysisDTO;
    }

    /**
     * This method would set the last run date for the passed-in tool from the passed-in dateTime object
     *
     * @param int $toolId
     * @param int $personId
     * @param \DateTime $dateTime
     * @return object
     */
    public function saveToolLastRunDate($toolId, $personId, $dateTime)
    {
        $mapworksToolLastRunObject = $this->saveMapworksToolLastRunObject($toolId, $personId, $dateTime);
        $this->mapworksToolLastRunRepository->persist($mapworksToolLastRunObject);
        return $mapworksToolLastRunObject;
    }


    /**
     * Gets the top issues along with percentages and a student list
     *
     * @param integer $organizationId
     * @param integer $facultyId
     * @param IssuesInputDTO $issuesInputDTO
     * @return array
     */
    public function getMapworksToolTopIssues($organizationId, $facultyId, IssuesInputDTO $issuesInputDTO, $date = 'now')
    {
        $toolObject = $this->mapworksToolRepository->findOneBy(['shortCode' => self::TOP_ISSUES_SHORT_CODE], new SynapseValidationException('Mapworks Tool ' . self::TOP_ISSUES_SHORT_CODE . ' does not exist.'));
        $toolId = $toolObject->getId();
        $currentDate = new \DateTime($date);
        $topIssuesDTO = $this->issueService->getTopIssuesWithStudentList($organizationId, $facultyId, $issuesInputDTO);
        $this->saveToolLastRunDate($toolId, $facultyId, $currentDate);
        return $topIssuesDTO;
    }

    /**
     * Gets the top issues along with percentages and a student list
     *
     * @param int $organizationId
     * @param int $facultyId
     * @param IssuesInputDTO $issuesInputDTO
     * @return array
     */
    public function createTopIssuesCSVJob($organizationId, $facultyId, $issuesInputDTO)
    {
        $job = new IssueCSVJob();
        $job->args = [
            'organization_id' => $organizationId,
            'faculty_id' => $facultyId,
            'top_issues_pagination' => serialize($issuesInputDTO)
        ];


        $this->resque->enqueue($job, true);
        return [SynapseConstant::DOWNLOAD_IN_PROGRESS_MESSAGE];
    }

    /**
     * Generate PDF for tool (Only One tool supported right now, Top Issues)
     *
     * @param int $organizationId
     * @param int $personId
     * @param int $toolId
     * @param int $zoom
     * @return string $response
     */
    public function generateToolPDF($organizationId, $personId, $toolId, $zoom)
    {
        $mapworksTool = $this->mapworksToolRepository->find($toolId, new SynapseValidationException('Mapworks Tool Id: ' . $toolId . ' does not exist.'));
        $toolName = $mapworksTool->getToolName();

        $fileName = $organizationId . '-' . $personId . '-' . $toolName . time();
        $fileName = md5($fileName) . '.pdf';

        $temporaryFileOnServer = '/tmp/' . $fileName;

        $URLpath = $this->ebiConfigRepository->findOneBy(['key' => 'TOP_ISSUES_URL_PATH'], new SynapseValidationException('Top Issue URL path not found.'));

        $mapworksToolURLtoConvertToPDF = $this->urlUtilityService->generateURLforMapworks($URLpath, [], $personId);
        $phantomResponse = $this->pdfDetailsService->generatePDFusingPhantomJS($mapworksToolURLtoConvertToPDF, $temporaryFileOnServer, $zoom);
        $response = 'File Not Created.';
        if ($phantomResponse === true) {
            header(SynapseConstant::PDF_CONTENT_TYPE);
            header("Content-Disposition: attachment; filename=" . $temporaryFileOnServer);
            readfile($temporaryFileOnServer);
            unlink($temporaryFileOnServer);
            $response = "File Successfully Created";
        }
        return $response;
    }

    /**
     * Load $toolId, $personId, $dateTime in MapworksToolLastRunObject
     *
     * @param MapworksToolLastRun $mapworksToolLastRunObject
     * @param int $toolId
     * @param int $personId
     * @param \DateTime $dateTime
     * @return MapworksToolLastRun $mapworksToolLastRunObject
     */
    private function loadMapworksToolLastRunObject($mapworksToolLastRunObject, $toolId, $personId, $dateTime)
    {
        $toolObj = $this->mapworksToolRepository->find($toolId, new SynapseValidationException('Mapworks tool not found.'));
        $personObj = $this->personRepository->find($personId, new SynapseValidationException('Person not found.'));
        $mapworksToolLastRunObject->setToolId($toolObj);
        $mapworksToolLastRunObject->setPersonId($personObj);
        $mapworksToolLastRunObject->setLastRun($dateTime);
        return $mapworksToolLastRunObject;
    }

    /**
     *
     * Create MapworksToolLastRunObject
     *
     * @param int $toolId
     * @param int $personId
     * @param \DateTime $dateTime
     * @return MapworksToolLastRun $newMapworksToolLastRunObject
     */
    public function createMapworksToolLastRunObject($toolId, $personId, $dateTime)
    {
        $newMapworksToolLastRunObject = new MapworksToolLastRun();
        $newMapworksToolLastRunObject = $this->loadMapworksToolLastRunObject($newMapworksToolLastRunObject, $toolId, $personId, $dateTime);
        return $newMapworksToolLastRunObject;
    }

    /**
     * Save MapworksToolLastRunObject
     *
     * @param int $toolId
     * @param int $personId
     * @param \DateTime $dateTime
     * @return MapworksToolLastRun
     */
    public function saveMapworksToolLastRunObject($toolId, $personId, $dateTime)
    {
        $updateMapworksToolLastRunObject = $this->mapworksToolLastRunRepository->findOneBy(['personId' => $personId, 'toolId' => $toolId]);
        if ($updateMapworksToolLastRunObject) {
            $updateMapworksToolLastRunObject = $this->loadMapworksToolLastRunObject($updateMapworksToolLastRunObject, $toolId, $personId, $dateTime);
            return $updateMapworksToolLastRunObject;
        } else {
            return $this->createMapworksToolLastRunObject($toolId, $personId, $dateTime);
        }
    }

}
