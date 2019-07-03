<?php
namespace Synapse\ReportsBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\OrganizationlangService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\Job\CompletionReportJob;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\SearchBundle\Service\Impl\SearchService;

/**
 * @DI\Service("completion_report_service")
 */
class CompletionReportService extends AbstractService
{

    const SERVICE_KEY = 'completion_report_service';

    // Scaffolding
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var Serializer
     */
    private $serializer;


    // Services
    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationService;

    /**
     * @var OrganizationlangService
     */
    private $organizationLangService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var SearchService
     */
    private $searchService;

    // Repositories


    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;
    /**
     * @var EbiMetadataRepository
     */
    private $ebiMetadataRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var OrgPersonStudentRetentionTrackingGroupRepository
     */
    private $orgPersonStudentRetentionTrackingGroupRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;


    /**
     * @var ReportsRepository
     */
    private $reportsRepository;

    /**
     * @var ReportsRunningStatusRepository
     */
    private $reportsRunningStatusRepository;

    /**
     * CompletionReportService Constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        // Scaffolding
        $this->container = $container;
        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->serializer = $this->container->get(SynapseConstant::JMS_SERIALIZER_CLASS_KEY);

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->alertNotificationService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->organizationLangService = $this->container->get(OrganizationlangService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->searchService = $this->container->get(SearchService::SERVICE_KEY);

        // Repositories
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(EbiMetadataRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRetentionTrackingGroupRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->reportsRepository = $this->repositoryResolver->getRepository(ReportsRepository::REPOSITORY_KEY);
        $this->reportsRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsRunningStatusRepository::REPOSITORY_KEY);
    }


    /**
     * Initiates Completion report Job
     *
     * @param integer $loggedInUserId
     * @param ReportRunningStatusDto $reportRunningDto
     * @param array $requestJSONString
     * @return void
     * @throws AccessDeniedException
     */
    public function initiateReportJob($loggedInUserId, $reportRunningDto, $requestJSONString)
    {

        $personObject = $this->personRepository->find($loggedInUserId);
        $organizationId = $personObject->getOrganization()->getId();
        $isCoordinatorOrganizationRoleObject = $this->organizationRoleRepository->getUserCoordinatorRole($organizationId, $loggedInUserId);
        if (!$isCoordinatorOrganizationRoleObject) {
            throw new AccessDeniedException("The logged in user is not authorized to generate this report.");
        }
        $completionReportJob = new CompletionReportJob();
        $completionReportJob->args = array(
            'userId' => $loggedInUserId,
            'reportRunningDto' => serialize($reportRunningDto),
            'requestjson' => $requestJSONString
        );
        $this->resque->enqueue($completionReportJob, true);
    }

    /**
     * Generates Completion Report
     *
     * @param int $personId
     * @param ReportRunningStatusDto $reportRunningDto
     * @param array $requestJSONString
     * @return mixed
     */
    public function generateReport($personId, $reportRunningDto, $requestJSONString)
    {

        $organizationId = $reportRunningDto->getOrganizationId();
        $reportRunningStatusObject  = $this->reportsRunningStatusRepository->findOneBy([
            "id" => $reportRunningDto->getId()
        ]);


        $reportRunningStatusObject->setStatus('IP');
        $this->reportsRunningStatusRepository->update($reportRunningStatusObject);
        $this->reportsRunningStatusRepository->flush();


        $studentIds = $reportRunningStatusObject->getFilteredStudentIds();
        $studentIdsArray = explode(",", $studentIds);


        if (trim($studentIds) == "") {
            $totalStudentsCount = 0;
        } else {
            $totalStudentsCount = count($studentIdsArray);
        }

        $organizationDetails = $this->organizationLangService->getOrganization($organizationId);
        $organizationInstance = $this->organizationRepository->findOneBy(["id" => $organizationId]);
        $organizationLogoImageName = "";
        if ($organizationInstance) {
            $organizationLogoImageName = $organizationInstance->getLogoFileName();
        }

        $personObject = $this->personRepository->find($personId);

        // Report data generation starts here
        $searchAttributes = $reportRunningDto->getSearchAttributes();


        $currentOrgAcademicYear = $this->orgAcademicYearRepository->getCurrentAcademicYear($organizationId);
        $currentYearId = $currentOrgAcademicYear['year_id'];

        $retentionTrackOrgAcademicYearId = $searchAttributes['retention_date']['academic_year_id'];

        $retentionTrackOrgAcademicYearObject = $this->orgAcademicYearRepository->find($retentionTrackOrgAcademicYearId);
        $retentionTrackYearId = $retentionTrackOrgAcademicYearObject->getYearId()->getId();

        $areStudentsInRetentionTrack = $this->orgPersonStudentRetentionTrackingGroupRepository->areStudentsAssignedToThisRetentionTrackingYear($organizationId, $retentionTrackOrgAcademicYearId);
        if (!$areStudentsInRetentionTrack) {
            $responseData['error'] = ReportsConstants::NO_STUDENTS_AVAILABLE_FOR_RETENTION_TRACKING_YEAR;
            $reportSections = $reportRunningDto->getReportSections();
            $reportRunningStatusObject->setStatus('F');
            $reportDataJSON = $this->serializer->serialize($responseData, 'json');
            $reportRunningStatusObject->setResponseJson($reportDataJSON);
        } else {

            $yearsFromRetentionTrackMap = [
                0 => "year1",
                1 => "year2",
                2 => "year3",
                3 => "year4",
                4 => "year5",
                5 => "year6"];

            $riskDates = $this->searchService->getRiskDates($organizationId, $searchAttributes);

            $riskStartDate = $riskDates['start_date'];
            $riskEndDate = $riskDates['end_date'];

            $aggregateCompletionVariablesWithRisk = $this->reportsRepository->getAggregatedCompletionVariablesWithRisk($riskStartDate, $riskEndDate, $organizationId, $currentYearId, $retentionTrackYearId, $studentIdsArray);


            $riskColorCompletionArray = [];
            $overAllCompletionArray = [];
            $overAllCompletionArray['count'] = 0;

            $totalNumerator = [];
            $totalDenominator = [];
            foreach ($aggregateCompletionVariablesWithRisk as $aggregateCompletionVariableWithRisk ) {

                $riskColorCompletionArray[$aggregateCompletionVariableWithRisk['risk_level_text']][$yearsFromRetentionTrackMap[$aggregateCompletionVariableWithRisk['years_from_retention_track']]] = array(
                    'percent' => round(((int)$aggregateCompletionVariableWithRisk['numerator_count'] / (int)$aggregateCompletionVariableWithRisk['denominator_count']) * 100),
                    'number' => (int)$aggregateCompletionVariableWithRisk['numerator_count']
                );

                $riskColorCompletionArray[$aggregateCompletionVariableWithRisk['risk_level_text']]['count'] = (int)$aggregateCompletionVariableWithRisk['denominator_count'];

                if (isset($totalDenominator[$aggregateCompletionVariableWithRisk['years_from_retention_track']])) {
                    $totalDenominator[$aggregateCompletionVariableWithRisk['years_from_retention_track']] += (int)$aggregateCompletionVariableWithRisk['denominator_count'];
                } else {
                    $totalDenominator[$aggregateCompletionVariableWithRisk['years_from_retention_track']] = (int)$aggregateCompletionVariableWithRisk['denominator_count'];
                }

                if (isset($totalNumerator[$aggregateCompletionVariableWithRisk['years_from_retention_track']])) {
                    $totalNumerator[$aggregateCompletionVariableWithRisk['years_from_retention_track']] += (int)$aggregateCompletionVariableWithRisk['numerator_count'];
                } else {
                    $totalNumerator[$aggregateCompletionVariableWithRisk['years_from_retention_track']] = (int)$aggregateCompletionVariableWithRisk['numerator_count'];
                }

            }


            foreach ($yearsFromRetentionTrackMap as $yearFromRetentionTrack => $yearNumber) {

                if (isset($totalNumerator[$yearFromRetentionTrack])) {
                    $overAllCompletionArray[$yearNumber] = [
                        'percent' => round(($totalNumerator[$yearFromRetentionTrack] / $totalDenominator[$yearFromRetentionTrack]) * 100),
                        'number' => $totalNumerator[$yearFromRetentionTrack]
                    ];

                }
            }
            $overAllCompletionArray['count'] = (int)$totalStudentsCount;
            $riskDataArray = $riskColorCompletionArray;
            $riskDataArray['overall'] = $overAllCompletionArray;
            $riskDataArray['total_students'] = (int)$totalStudentsCount;

            $resultSet = $riskDataArray;

            $campusDetails = [
                'campus_id' => $organizationId,
                'campus_name' => $organizationDetails['name'],
                'campus_logo' => $organizationLogoImageName
            ];

            $reportSections = $reportRunningDto->getReportSections();

            $reportDetails = [
                'report_id' => $reportSections['reportId'],
                'report_name' => $reportSections['report_name'],
                'short_code' => $reportSections['short_code'],
                'report_instance_id' => $reportRunningDto->getId(),
                'report_date' => date('Y-m-d\TH:i:sO'),
                'report_by' => [
                    'first_name' => $personObject->getFirstname(),
                    'last_name' => $personObject->getLastname()
                ]
            ];

            $reportData['id'] = $reportRunningDto->getId();
            $reportData['organization_id'] = $organizationId;
            $reportData['report_id'] = $reportSections['reportId'];
            $reportData['report_sections'] = $reportSections;
            $reportData['search_attributes'] = $searchAttributes;
            $reportData['campus_info'] = $campusDetails;
            $reportData['request_json'] = $requestJSONString;
            $reportData['report_info'] = $reportDetails;
            $reportData['report_data'] = $resultSet;

            $reportRunningStatusObject->setResponseJson(json_encode($reportData));
            $reportRunningStatusObject->setStatus('C');
        }
        $this->reportsRunningStatusRepository->update($reportRunningStatusObject);
        $this->reportsRunningStatusRepository->flush();

        $reportId = $reportRunningStatusObject->getId();
        $url = "/reports/CR/$reportId";
        $this->alertNotificationService->createNotification("CR", $reportSections['report_name'], $personObject, null, null, null, $url, null, null, null, 1, $reportRunningStatusObject);
        return $reportData;

    }

}
