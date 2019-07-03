<?php
namespace Synapse\ReportsBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\DAO\GroupResponseReportDAO;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\RestBundle\Utility\RestUtilityService;


/**
 * @DI\Service("group_response_report_service")
 */
class GroupResponseReportService extends AbstractService
{
    const SERVICE_KEY = 'group_response_report_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;


    // Services

    /**
     * @var CSVUtilityService
     */
    private $csvUtilityService;

    /**
     * @var RestUtilityService
     */
    private $restUtilityService;


    // DAO

    /**
     * @var GroupResponseReportDAO
     */
    private $groupResponseReportDAO;


    // Repositories

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var ReportsRepository
     */
    private $reportsRepository;


    /**
     * @DI\InjectParams({
     *     "repositoryResolver" = @DI\Inject("repository_resolver"),
     *     "logger" = @DI\Inject("logger"),
     *     "container" = @DI\Inject("service_container"),
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

        // Services
        $this->csvUtilityService = $this->container->get(CSVUtilityService::SERVICE_KEY);
        $this->restUtilityService = $this->container->get(RestUtilityService::SERVICE_KEY);

        // DAO
        $this->groupResponseReportDAO = $this->container->get(GroupResponseReportDAO::DAO_KEY);

        // Repositories
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->reportsRepository = $this->repositoryResolver->getRepository(ReportsRepository::REPOSITORY_KEY);
    }


    /**
     * Generate Group Response Report
     *
     * @param ReportRunningStatusDto $reportRunningStatusDto
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string|null $outputFormat - "csv" or null
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $sortBy - "external_id" or "group_name" or "parent_group" or "student_id_cnt" or "responded" or "response_rate",
     * @throws AccessDeniedException | SynapseValidationException
     * @return array
     */
    public function generateGroupResponseReport($reportRunningStatusDto, $loggedInUserId, $organizationId, $outputFormat, $pageNumber, $recordsPerPage, $sortBy)
    {
        $personObject = $this->personRepository->find($loggedInUserId);

        if (empty($personObject)) {
            throw new AccessDeniedException('The user does not exist within Mapworks');
        }

        $reportId = $reportRunningStatusDto->getReportId();
        $reportsObject = $this->reportsRepository->find($reportId);
        if (empty($reportsObject)) {
            throw new SynapseValidationException('Report not found');
        }

        // Get and prepare information from the request.
        $searchAttributes = $reportRunningStatusDto->getSearchAttributes();
        $surveyFilterArray = $searchAttributes['survey_filter'];
        $surveySortFilter = $searchAttributes['filter_sort'];

        $orgAcademicYearId = $surveyFilterArray['org_academic_year_id'];

        $cohort = $surveyFilterArray['cohort'];
        $surveyId = $surveyFilterArray['survey_id'];

        list($sortBy, $sortDirection) = $this->restUtilityService->getSortColumnAndDirection($sortBy);

        // Get response data for each of the user's groups.
        $groupResponseData = $this->groupResponseReportDAO->getGroupStudentCountAndResponseRateByFaculty($loggedInUserId, $orgAcademicYearId, $cohort, $surveyId, $surveySortFilter, $sortBy, $sortDirection);

        // For any top-level groups, replace null parent group with '-'.
        foreach ($groupResponseData as $groupResponseRecord) {
            if (empty($groupResponseRecord['parent_group'])) {
                $groupResponseRecord['parent_group'] = '-';
            }
        }

        if (!empty($groupResponseData)) {
            if ($outputFormat == 'csv') {
                $columnHeaders = [
                    'external_id' => 'Group ID',
                    'group_name' => 'Group Name',
                    'parent_group' => 'Parent Group',
                    'student_id_cnt' => 'Students',
                    'responded' => 'Responded',
                    'response_rate' => 'Response Rate'
                ];


                $filePath = SynapseConstant::S3_ROOT . ReportsConstants::S3_REPORT_CSV_EXPORT_DIRECTORY . '/';
                $fileName = $organizationId . "-group-response-" . time() . ".csv";

                $this->csvUtilityService->generateCSV($filePath, $fileName, $groupResponseData, $columnHeaders);

                return ['file_name' => $fileName];


            } else {
                $groupResponseDataForPage = null;

                if (empty($pageNumber)) {
                    $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
                }

                if (empty($recordsPerPage)) {
                    $recordsPerPage = SynapseConstant::DEFAULT_RECORD_COUNT;
                }

                // If there are more groups than will fit on a page, restrict the list already retrieved to the single page requested.
                $groupResponseDataForPage = array_slice($groupResponseData, $recordsPerPage * ($pageNumber - 1), $recordsPerPage);

                // Get the response data for the top of the report (which includes all students in any of the user's groups).
                $groupStudentCount = $this->groupResponseReportDAO->getOverallCountGroupStudentCountAndResponseRateByFaculty($loggedInUserId, $orgAcademicYearId, $cohort, $surveyId, $surveySortFilter);

                // Set all the necessary metadata to be returned with the report.
                $GroupResponseReport['total_students'] = (int)$groupStudentCount[0]['student_id_cnt'];
                $GroupResponseReport['responded'] = (int)$groupStudentCount[0]['responded'];
                $GroupResponseReport['responded_percentage'] = round(100 * $GroupResponseReport['responded'] / $GroupResponseReport['total_students']);


                $GroupResponseReport['total_records'] = count($groupResponseData);
                $GroupResponseReport['records_per_page'] = $recordsPerPage;
                $GroupResponseReport['current_page'] = $pageNumber;
                $GroupResponseReport['total_pages'] = ceil(count($groupResponseData) / $recordsPerPage);

                $GroupResponseReport['search_attributes'] = $searchAttributes;
                $GroupResponseReport['search_attributes']['survey_filter'] = $surveyFilterArray;
                $GroupResponseReport['report_data'] = $groupResponseDataForPage;
                $GroupResponseReport['report_sections'] = $reportRunningStatusDto->getReportSections();

                $currentDateObject = new \DateTime('now');

                $GroupResponseReport['report_info'] = [
                    'report_id' => $reportId,
                    'report_name' => $reportsObject->getName(),
                    'short_code' => $reportsObject->getShortCode(),
                    'report_instance_id' => '',
                    'report_date' => $currentDateObject,
                    'report_by' => [
                        'first_name' => $personObject->getFirstname(),
                        'last_name' => $personObject->getLastname()
                    ]
                ];

                return $GroupResponseReport;
            }
        } else {
            $GroupResponseReport['status_message'] = [
                'code' => ReportsConstants::REPORT_NO_DATA_CODE,
                'description' => ReportsConstants::REPORT_NO_DATA_MESSAGE
            ];
            return $GroupResponseReport;
        }
    }

}