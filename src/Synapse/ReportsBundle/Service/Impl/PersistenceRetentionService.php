<?php
namespace Synapse\ReportsBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use Doctrine\ORM\Cache;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Constraints\Date;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\OrganizationlangService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\Job\ReportJob;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\ReportsBundle\Repository\RetentionCompletionVariableNameRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\SearchBundle\Service\Impl\SearchService;


/**
 * @DI\Service("persistence_retention_service")
 */
class PersistenceRetentionService extends AbstractService
{

    const SERVICE_KEY = 'persistence_retention_service';

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
    private $alertNotificationsService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

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
     * @var EbiMetadataRepository
     */
    private $ebiMetadataRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

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
     * @var ReportsRepository
     */
    private $reportsRepository;

    /**
     * @var ReportsRunningStatusRepository
     */
    private $reportRunningStatusRepository;

    /**
     * @var RetentionCompletionVariableNameRepository
     */
    private $retentionCompletionVariableNameRepository;


    /**
     * PersistenceRetentionService constructor.
     * 
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Logger $logger
     * @param Container $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->serializer = $this->container->get(SynapseConstant::JMS_SERIALIZER_CLASS_KEY);

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->organizationLangService = $this->container->get(OrganizationlangService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->searchService = $this->container->get(SearchService::SERVICE_KEY);

        // Repositories
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(EbiMetadataRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRetentionTrackingGroupRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY);
        $this->reportsRepository = $this->repositoryResolver->getRepository(ReportsRepository::REPOSITORY_KEY);
        $this->reportRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsRunningStatusRepository::REPOSITORY_KEY);
        $this->retentionCompletionVariableNameRepository = $this->repositoryResolver->getRepository(RetentionCompletionVariableNameRepository::REPOSITORY_KEY);
    }

    /**
     * Initiate report building resque job
     *
     * @param ReportRunningStatusDto $reportRunningDto
     */
    public function initiateReportJob($reportRunningDto)
    {
        $personId = $reportRunningDto->getPersonId();
        $organizationId = $reportRunningDto->getOrganizationId();
        $isCoordinator = $this->organizationRoleRepository->getUserCoordinatorRole($organizationId, $personId);
        $reportInstanceId = $reportRunningDto->getId();

        if (!$isCoordinator) {
            throw new SynapseValidationException("Not Authorized to generate report. You must be a coordinator");
        }

        $job = new ReportJob();
        $job->args = array(
            'reportInstanceId' => $reportInstanceId,
            'reportRunningDto' => serialize($reportRunningDto),
            'service' => 'persistence_retention_service'
        );
        $this->resque->enqueue($job, true);
    }

    /**
     * Generate persistence and retention report
     *
     * @param int $reportInstanceId
     * @param ReportRunningStatusDto $reportRunningDto
     * @return array
     */
    public function generateReport($reportInstanceId, $reportRunningDto)
    {
        $organizationId = $reportRunningDto->getOrganizationId();
        $reportsRunningStatusInstance = $this->reportRunningStatusRepository->find($reportInstanceId);

        $studentIds = $reportsRunningStatusInstance->getFilteredStudentIds();
        $studentIdsArray = explode(",", $studentIds);

        if (trim($studentIds) == "") {
            $studentIdsArray = [-1];
            $totalStudentCount = 0;
        } else {
            $totalStudentCount = count($studentIdsArray);
        }

        $reportsRunningStatusInstance->setStatus('IP');
        $this->reportRunningStatusRepository->update($reportsRunningStatusInstance);
        $this->reportRunningStatusRepository->flush();

        $organizationDetails = $this->organizationLangService->getOrganization($organizationId);

        $organizationInstance = $this->organizationRepository->find($organizationId);
        if ($organizationInstance) {
            $organizationLogoImageName = $organizationInstance->getLogoFileName();
        } else {
            $organizationLogoImageName = null;
        }

        $personInstance = $this->personService->find($reportRunningDto->getPersonId());
        $searchAttributes = $reportRunningDto->getSearchAttributes();

        $retentionTrackOrgAcademicYearId = $searchAttributes['retention_date']['academic_year_id'];

        $areStudentsInRetentionTrack = $this->orgPersonStudentRetentionTrackingGroupRepository->areStudentsAssignedToThisRetentionTrackingYear($organizationId, $retentionTrackOrgAcademicYearId);
        if (!$areStudentsInRetentionTrack) {
            $responseData['error'] = ReportsConstants::NO_STUDENTS_AVAILABLE_FOR_RETENTION_TRACKING_YEAR ;
            $reportSections = $reportRunningDto->getReportSections();
            $reportsRunningStatusInstance->setStatus('F');
            $reportDataJSON = $this->serializer->serialize($responseData, 'json');
            $reportsRunningStatusInstance->setResponseJson($reportDataJSON);
        } else {
            $riskDates = $this->searchService->getRiskDates($organizationId, $searchAttributes);
            $resultSet = $this->formatRetentionDataset($studentIdsArray, $organizationId, $riskDates['start_date'], $riskDates['end_date'], $retentionTrackOrgAcademicYearId);

            $resultSet['total_students'] = $totalStudentCount;
            $campusDetailsArray = [
                'campus_id' => $organizationId,
                'campus_name' => $organizationDetails['name'],
                'campus_logo' => $organizationLogoImageName
            ];

            $reportSections = $reportRunningDto->getReportSections();
            $reportCreatedDateTime = $reportsRunningStatusInstance->getCreatedAt();
            $reportCreatedDateTimeFormatted = $reportCreatedDateTime->format('Y-m-d\TH:i:sO');

            $reportDetails = [
                'report_id' => $reportSections['reportId'],
                'report_name' => $reportSections['report_name'],
                'short_code' => $reportSections['short_code'],
                'report_instance_id' => $reportRunningDto->getId(),
                'report_date' => $reportCreatedDateTimeFormatted,
                'report_by' => [
                    'first_name' => $personInstance->getFirstname(),
                    'last_name' => $personInstance->getLastname()
                ]
            ];

            $reportData['id'] = $reportRunningDto->getId();
            $reportData['organization_id'] = $organizationId;
            $reportData['report_id'] = $reportSections['reportId'];
            $reportData['report_sections'] = $reportSections;
            $reportData['search_attributes'] = $searchAttributes;
            $reportData['campus_info'] = $campusDetailsArray;
            $reportData['request_json'] = $reportRunningDto;
            $reportData['report_info'] = $reportDetails;
            $reportData['report_data'] = $resultSet;

            $reportDataJSON = $this->serializer->serialize($reportData, 'json');
            $reportsRunningStatusInstance->setResponseJson($reportDataJSON);
            $reportsRunningStatusInstance->setStatus('C');
        }
        $this->reportRunningStatusRepository->update($reportsRunningStatusInstance);
        $this->reportRunningStatusRepository->flush();

        $reportId = $reportsRunningStatusInstance->getId();
        $url = "/reports/PRR/$reportId";
        $this->alertNotificationsService->createNotification("PRR", $reportSections['report_name'], $personInstance, null, null, null, $url, null, null, null, 1, $reportsRunningStatusInstance);
        return $reportDataJSON;

    }


    /**
     * Gets retention variable data  with risk and formats it by risk group
     *
     * @param array $studentIdsArray
     * @param integer $organizationId
     * @param Date $riskStartDate
     * @param Date $riskEndDate
     * @param integer $retentionTrackingOrgAcademicYearId
     * @return array
     */
    public function formatRetentionDataset($studentIdsArray, $organizationId, $riskStartDate, $riskEndDate, $retentionTrackingOrgAcademicYearId)
    {
        $sectionData = [];
        $retentionTrackingAcademicYearObject = $this->orgAcademicYearRepository->find($retentionTrackingOrgAcademicYearId);
        $retentionTrackingYearId = $retentionTrackingAcademicYearObject->getYearId()->getId();
        $currentAcademicYearDetails = $this->orgAcademicYearRepository->getCurrentAcademicYear($organizationId);
        $currentYearId = $currentAcademicYearDetails['year_id'];

        $mappedRetentionVariables = $this->getMeaningfulRetentionVariables($retentionTrackingYearId, $currentYearId);

        //Get retention data by risk group
        $retentionDataWithRisk = $this->reportsRepository->getAggregatedRetentionVariablesWithRisk($riskStartDate, $riskEndDate, $organizationId, $currentYearId, $retentionTrackingYearId, $studentIdsArray);



        if (!empty($retentionDataWithRisk)) {

            $mappedRetentionData = $this->mapDataToYearsFromRetentionTrack($retentionDataWithRisk);

            //Reformat each risk group's numbers, add them to the JSON section for retention data by risk group

            foreach ($mappedRetentionData as $yearsFromRetentionTrack => $retentionDataPoint) {

                $beginningYearNumeratorTotal = 0;
                $midYearNumeratorTotal = 0;
                $denominatorCountTotal = 0;
                $retentionBeginningYearTitle = "";
                $retentionMidYearTitle = "";
                if (isset($mappedRetentionVariables[$yearsFromRetentionTrack][0])) {
                    $retentionBeginningYearTitle = $mappedRetentionVariables[$yearsFromRetentionTrack][0];
                }
                if (isset($mappedRetentionVariables[$yearsFromRetentionTrack][1])) {
                    $retentionMidYearTitle = $mappedRetentionVariables[$yearsFromRetentionTrack][1];
                }

                $formattedBeginningYearRetentionDataJSONSection =
                    [
                        "column_title" => $retentionBeginningYearTitle,
                        "total_students_retained" => "",
                        "total_student_count" => "",
                        "percent" => "-",
                        "persistence_retention_by_risk" => []
                    ];

                $formattedMidYearRetentionDataJSONSection =
                    [
                        "column_title" => $retentionMidYearTitle,
                        "total_students_retained" => "",
                        "total_student_count" => "",
                        "percent" => "-",
                        "persistence_retention_by_risk" => []
                    ];

                //Initializing Total Counters


                foreach ($retentionDataPoint as $riskColorDataPoint) {

                    $riskColorText = $riskColorDataPoint['risk_level_text'];
                    $midyearCount = $riskColorDataPoint['midyear_numerator_count'];
                    $beginningYearCount = $riskColorDataPoint['beginning_year_numerator_count'];
                    $denominatorCount = $riskColorDataPoint['denominator_count'];


                    //Build Beginning Year Variable
                    $beginningYearVariable = $this->buildRetentionVariablesByRiskColor($riskColorText, $midyearCount, $beginningYearCount, $denominatorCount, false);

                    $formattedBeginningYearRetentionDataJSONSection['persistence_retention_by_risk'][] = $beginningYearVariable;

                    //Build MidYear Variable
                    $midyearVariable = $this->buildRetentionVariablesByRiskColor($riskColorText, $midyearCount, $beginningYearCount, $denominatorCount, true);

                    $formattedMidYearRetentionDataJSONSection['persistence_retention_by_risk'][] = $midyearVariable;

                    $beginningYearNumeratorTotal += $beginningYearCount;
                    $midYearNumeratorTotal += $midyearCount;
                    $denominatorCountTotal += $denominatorCount;
                }
                if ($denominatorCountTotal > 0) {
                    $beginningYearPercentageFromTotal = round((($beginningYearNumeratorTotal / $denominatorCountTotal) * 100), 1);
                    $midYearPercentageFromTotal = round((($midYearNumeratorTotal / $denominatorCountTotal) * 100), 1);
                } else {
                    $beginningYearPercentageFromTotal = "-";
                    $midYearPercentageFromTotal = "-";
                }

                if ($retentionBeginningYearTitle != "") {
                    $formattedBeginningYearRetentionDataJSONSection['percent'] = $beginningYearPercentageFromTotal;
                    $formattedBeginningYearRetentionDataJSONSection['total_students_retained'] = $beginningYearNumeratorTotal;
                    $formattedBeginningYearRetentionDataJSONSection['total_student_count'] = $denominatorCountTotal;
                }

                if($retentionMidYearTitle != "") {
                    $formattedMidYearRetentionDataJSONSection['percent'] = $midYearPercentageFromTotal;
                    $formattedMidYearRetentionDataJSONSection['total_students_retained'] = $midYearNumeratorTotal;
                    $formattedMidYearRetentionDataJSONSection['total_student_count'] = $denominatorCountTotal;
                }


                if ($formattedBeginningYearRetentionDataJSONSection['total_student_count'] != 0) {
                    $sectionData[] = $formattedBeginningYearRetentionDataJSONSection;
                }

                if ($formattedMidYearRetentionDataJSONSection['total_student_count'] != 0) {
                    $sectionData[] = $formattedMidYearRetentionDataJSONSection;
                }
            }
        }

        return $sectionData;
    }

    /**
     * Creating the retention variable array grouped by risk
     *
     * @param string $riskColorText
     * @param integer $midyearCount
     * @param integer $beginningYearCount
     * @param integer $denominatorCount
     * @param integer  $isMidYear
     * @return array
     */

    public function buildRetentionVariablesByRiskColor($riskColorText, $midyearCount, $beginningYearCount, $denominatorCount, $isMidYear) {

        $persistenceRetentionByRiskSubsection =
            [
                "risk_color" => "",
                "students_retained" => 0,
                "student_count" => 0,
                "percent" => 0
            ];

        if ($isMidYear) {
            $persistenceRetentionByRiskSubsection['students_retained'] = $midyearCount;
        } else {
            $persistenceRetentionByRiskSubsection['students_retained'] = $beginningYearCount;
        }

        $persistenceRetentionByRiskSubsection['student_count'] = $denominatorCount;
        $persistenceRetentionByRiskSubsection['risk_color'] = $riskColorText;

        if ($persistenceRetentionByRiskSubsection['student_count'] > 0) {
            $persistenceRetentionByRiskSubsection['percent'] = round((($persistenceRetentionByRiskSubsection['students_retained'] / $persistenceRetentionByRiskSubsection['student_count']) * 100), 1);
        } else {
            $persistenceRetentionByRiskSubsection['percent'] = "-";
        }

        return $persistenceRetentionByRiskSubsection;
    }



    /**
     * Returns an array containing mapped retention variables to the years from retention track
     * Excludes future year variables
     *
     * @param int $retentionTrackingYear - retention tracking year eg '201516'
     * @param int $yearLimit - Should almost always be the current year to limit so we don't look at the future
     * @return array
     */
    public function getMeaningfulRetentionVariables($retentionTrackingYear, $yearLimit)
    {

        $retentionVariables = $this->retentionCompletionVariableNameRepository->getRetentionVariablesOrderedByYearType($retentionTrackingYear, $yearLimit);
        $mappedRetentionVariables = [];
        foreach($retentionVariables as $retentionVariable) {
            $mappedRetentionVariables[$retentionVariable['years_from_retention_track']][$retentionVariable['is_midyear_variable']] = $retentionVariable['retention_variable'];
        }
        return $mappedRetentionVariables;
    }


    /**
     * Maps the data with years from the retention tracking year
     *
     * @param $retentionDataWithRisk
     * @return array
     */
    public function mapDataToYearsFromRetentionTrack($retentionDataWithRisk)
    {

        $mappedToYearRetentionDataWithRisk = [];
        foreach ($retentionDataWithRisk as $dataPoint) {
            $mappedToYearRetentionDataWithRisk[$dataPoint['years_from_retention_track']][] = $dataPoint;
        }

        return $mappedToYearRetentionDataWithRisk;
    }

}
