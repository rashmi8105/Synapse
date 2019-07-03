<?php
namespace Synapse\ReportsBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\DatablockMasterLangRepository;
use Synapse\CoreBundle\Repository\DatablockMetadataRepository;
use Synapse\CoreBundle\Repository\EbiMetadataLangRepository;
use Synapse\CoreBundle\Repository\EbiMetadataListValuesRepository;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Repository\OrgMetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrgMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetDatablockRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonOrgMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\EntityDto\ProfileDrilldownDto;
use Synapse\ReportsBundle\EntityDto\ProfileDrilldownRecordDto;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\Job\ReportJob;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\RiskBundle\Repository\RiskLevelsRepository;
use Synapse\SearchBundle\Service\Impl\StudentListService;


/**
 * @DI\Service("profile_snapshot_service")
 */
class ProfileSnapshotService extends AbstractService
{

    const SERVICE_KEY = 'profile_snapshot_service';

    const AGGREGATION_MESSAGE = 'Insufficient data to display.';

    const TEXT_TYPE_ITEM_MESSAGE = 'Please export to CSV or use the “N” above to drill down and view all individual responses.';

    private $globallyDrillable = false;     // Will be set to true if the user has individual access to all students in the report.

    private $globallyNotDrillable = false;  // Will be set to true if the user only has aggregate access to all students in the report.


    // Scaffolding

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
     * @var CSVUtilityService
     */
    private $csvUtilityService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var ReportDrilldownService
     */
    private $reportDrilldownService;

    /**
     * @var StudentListService
     */
    private $studentListService;


    // Repositories

    /**
     * @var DatablockMasterLangRepository
     */
    private $datablockMasterLangRepository;

    /**
     * @var DatablockMetadataRepository
     */
    private $datablockMetadataRepository;

    /**
     * @var EbiMetadataRepository
     */
    private $ebiMetadataRepository;

    /**
     * @var EbiMetadataLangRepository
     */
    private $ebiMetadataLangRepository;

    /**
     * @var EbiMetadataListValuesRepository
     */
    private $ebiMetadataListValuesRepository;

    /**
     * @var OrgAcademicTermRepository
     */
    private $orgAcademicTermsRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrgCourseStudentRepository
     */
    private $orgCourseStudentRepository;

    /**
     * @var OrgGroupStudentsRepository
     */
    private $orgGroupStudentsRepository;

    /**
     * @var OrgMetadataRepository
     */
    private $orgMetadataRepository;

    /**
     * @var OrgMetadataListValuesRepository
     */
    private $orgMetadataListValuesRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgPermissionsetDatablockRepository
     */
    private $orgPermissionsetDatablockRepository;

    /**
     * @var OrgPermissionsetMetadataRepository
     */
    private $orgPermissionsetMetadataRepository;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var PersonEbiMetaDataRepository
     */
    private $personEbiMetadataRepository;

    /**
     * @var PersonOrgMetaDataRepository
     */
    private $personOrgMetadataRepository;

    /**
     * @var ReportsRepository
     */
    private $reportsRepository;

    /**
     * @var ReportsRunningStatusRepository
     */
    private $reportsRunningStatusRepository;

    /**
     * @var RiskLevelsRepository
     */
    private $riskLevelsRepository;


    /**
     * @DI\InjectParams({
     *      "repositoryResolver" = @DI\Inject("repository_resolver"),
     *      "logger" = @DI\Inject("logger"),
     *      "container" = @DI\Inject("service_container")
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
        $this->resque = $this->container->get('bcc_resque.resque');
        $this->serializer = $this->container->get('jms_serializer');

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->csvUtilityService = $this->container->get(CSVUtilityService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->reportDrilldownService = $this->container->get(ReportDrilldownService::SERVICE_KEY);
        $this->studentListService = $this->container->get(StudentListService::SERVICE_KEY);

        // Repositories
        $this->datablockMasterLangRepository = $this->repositoryResolver->getRepository(DatablockMasterLangRepository::REPOSITORY_KEY);
        $this->datablockMetadataRepository = $this->repositoryResolver->getRepository(DatablockMetadataRepository::REPOSITORY_KEY);
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(EbiMetadataRepository::REPOSITORY_KEY);
        $this->ebiMetadataLangRepository = $this->repositoryResolver->getRepository(EbiMetadataLangRepository::REPOSITORY_KEY);
        $this->ebiMetadataListValuesRepository = $this->repositoryResolver->getRepository(EbiMetadataListValuesRepository::REPOSITORY_KEY);
        $this->orgAcademicTermsRepository = $this->repositoryResolver->getRepository(OrgAcademicTermRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->orgCourseStudentRepository = $this->repositoryResolver->getRepository(OrgCourseStudentRepository::REPOSITORY_KEY);
        $this->orgGroupStudentsRepository = $this->repositoryResolver->getRepository(OrgGroupStudentsRepository::REPOSITORY_KEY);
        $this->orgMetadataRepository = $this->repositoryResolver->getRepository(OrgMetadataRepository::REPOSITORY_KEY);
        $this->orgMetadataListValuesRepository = $this->repositoryResolver->getRepository(OrgMetadataListValuesRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPermissionsetDatablockRepository = $this->repositoryResolver->getRepository(OrgPermissionsetDatablockRepository::REPOSITORY_KEY);
        $this->orgPermissionsetMetadataRepository = $this->repositoryResolver->getRepository(OrgPermissionsetMetadataRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->personEbiMetadataRepository = $this->repositoryResolver->getRepository(PersonEbiMetaDataRepository::REPOSITORY_KEY);
        $this->personOrgMetadataRepository = $this->repositoryResolver->getRepository(PersonOrgMetaDataRepository::REPOSITORY_KEY);
        $this->reportsRepository = $this->repositoryResolver->getRepository(ReportsRepository::REPOSITORY_KEY);
        $this->reportsRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsRunningStatusRepository::REPOSITORY_KEY);
        $this->riskLevelsRepository = $this->repositoryResolver->getRepository(RiskLevelsRepository::REPOSITORY_KEY);
    }


    /**
     * When the report is being generated as a job, creates and enqueues this job.
     *
     * @param int $reportsRunningStatusId
     * @param ReportRunningStatusDto $reportRunningStatusDto
     */
    public function initiateProfileSnapshotJob($reportsRunningStatusId, $reportRunningStatusDto)
    {
        $job = new ReportJob();
        $job->args = [
            'reportInstanceId' => $reportsRunningStatusId,
            'reportRunningDto' => serialize($reportRunningStatusDto),
            'service' => 'profile_snapshot_service'
        ];
        $this->resque->enqueue($job, true);
    }


    /**
     * Retrieves and organizes all data needed for the Profile Snapshot Report.
     * Inserts the resulting JSON into the reports_running_status table in the database.
     *
     * @param int $reportsRunningStatusId
     * @param ReportRunningStatusDto $reportRunningStatusDto
     */
    public function generateReport($reportsRunningStatusId, $reportRunningStatusDto)
    {
        $loggedInUserId = $reportRunningStatusDto->getPersonId();
        $organizationId = $reportRunningStatusDto->getOrganizationId();
        $reportId = $reportRunningStatusDto->getReportId();

        $personObject = $this->personRepository->find($loggedInUserId);
        $reportsObject = $this->reportsRepository->find($reportId);
        $reportsRunningStatusObject = $this->reportsRunningStatusRepository->find($reportsRunningStatusId);

        // Get the year selected in the mandatory filter.  Make sure it is set and belongs to the correct organization.
        $searchAttributes = $reportRunningStatusDto->getSearchAttributes();
        $orgAcademicYearId = $searchAttributes['org_academic_year_id'];
        if (empty($orgAcademicYearId)) {
            throw new SynapseValidationException('An academic year must be selected in the mandatory filter.');
        } else {
            $this->academicYearService->validateAcademicYear($orgAcademicYearId, $organizationId);
        }

        // Get an array of all the students selected for this report via the preliminary filters.
        $filteredStudentIdString = $reportsRunningStatusObject->getFilteredStudentIds();
        $filteredStudentIds = explode(',', $filteredStudentIdString);

        // Set the attributes we want at the beginning of the report JSON.
        if (!empty($filteredStudentIdString)) {
            $studentCount = count($filteredStudentIds);
        } else {
            $studentCount = 0;
        }

        $reportGeneratedTime = $reportsRunningStatusObject->getCreatedAt();
        $reportGeneratedTimestamp = $reportGeneratedTime->getTimestamp();

        $reportData['request_json'] = $reportRunningStatusDto;
        $reportData['report_info'] = [
            'report_id' => $reportId,
            'report_name' => $reportsObject->getName(),
            'short_code' => $reportsObject->getShortCode(),
            'report_instance_id' => $reportsRunningStatusId,
            'total_students' => $studentCount,
            'report_date' => date('Y-m-d\TH:i:sO', $reportGeneratedTimestamp),
            'report_by' => [
                'first_name' => $personObject->getFirstname(),
                'last_name' =>  $personObject->getLastname()
            ]
        ];

        if (empty($filteredStudentIdString)) {
            $reportData['status_message'] = [
                'code' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_CODE,
                'description' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_MESSAGE
            ];
            $this->logger->addWarning(json_encode($reportData['status_message']));

        } else {

            $accessLevels = $this->orgPermissionsetRepository->getAccessLevelForFacultyAndStudents($loggedInUserId, $filteredStudentIds);
            $accessLevelCounts = array_count_values($accessLevels);
            if (empty($accessLevelCounts[0])) {
                $this->globallyDrillable = true;
            } elseif (empty($accessLevelCounts[1])) {
                $this->globallyNotDrillable = true;
            }

            // Get all the profile item data for the report.
            $reportItems = $this->getReportItems($loggedInUserId, $filteredStudentIds, $orgAcademicYearId);

            // Finish assembling the JSON for the report.
            if (empty($reportItems)) {
                $reportData['status_message'] = [
                    'code' => ReportsConstants::REPORT_NO_DATA_CODE,
                    'description' => ReportsConstants::REPORT_NO_DATA_MESSAGE
                ];
                $this->logger->addWarning(json_encode($reportData['status_message']));

            } else {
                $finalReportItems = [];

                foreach ($reportItems as $reportItem) {
                    $finalReportItems[] = $this->assembleReportItemForJSON($reportItem);
                }

                $reportData['report_items'] = $finalReportItems;
            }
        }

        $responseJSON = $this->serializer->serialize($reportData, 'json');
        $reportsRunningStatusObject->setStatus('C');
        $reportsRunningStatusObject->setResponseJson($responseJSON);
        $this->reportsRunningStatusRepository->flush();

        $this->alertNotificationsService->createReportNotification($reportsRunningStatusObject);
    }


    /**
     * Returns an array of all the report items.  Before retrieving the data for each profile item,
     * uses permissions to narrow the list of $filteredStudentIds as necessary.
     *
     * @param int $loggedInUserId
     * @param array $filteredStudentIds
     * @param int $orgAcademicYearId
     * @return array
     */
    private function getReportItems($loggedInUserId, $filteredStudentIds, $orgAcademicYearId)
    {
        // Arrays used for both ISPs and ebi profile items
        $reportItems = [];
        $studentsInGroups = [];
        $studentsInCourses = [];

        // Get and organize a list of ISPs the user has access to, along with the groups and courses which give them access.
        $ispsAndGroups = $this->orgPermissionsetMetadataRepository->getISPsAndGroupsForPerson($loggedInUserId);
        $ispsAndCourses = $this->orgPermissionsetMetadataRepository->getISPsAndCoursesForPerson($loggedInUserId);

        $ispPermissionData = [];
        foreach ($ispsAndGroups as $ispAndGroup) {
            $ispPermissionData[$ispAndGroup['org_metadata_id']]['groups'][] = $ispAndGroup['org_group_id'];
        }
        foreach ($ispsAndCourses as $ispAndCourse) {
            $ispPermissionData[$ispAndCourse['org_metadata_id']]['courses'][] = $ispAndCourse['org_courses_id'];
        }

        // For each ISP, use the lists of groups and courses to get accessible students.  Combine these with the list of
        // students selected for the report to get the list of students whose data we will try to find for this ISP.
        // Then retrieve this data.
        foreach ($ispPermissionData as $orgMetadataId => $groupsAndCourses) {
            $accessibleStudents = $this->getAccessibleStudents($groupsAndCourses, $studentsInGroups, $studentsInCourses);

            $studentsIncludedInReportItem = array_intersect($filteredStudentIds, $accessibleStudents);

            $isp = [];
            if (!empty($studentsIncludedInReportItem)) {
                $isp = $this->getProfileItems('isp', null, $orgMetadataId, $loggedInUserId, $studentsIncludedInReportItem, $orgAcademicYearId);
            }

            $reportItems = array_merge($reportItems, $isp);
        }

        // Get and organize all the profile datablocks for the logged-in user, along with the groups and courses
        // which give them access to each datablock.
        $datablocksAndGroups = $this->orgPermissionsetDatablockRepository->getDatablocksAndGroupsForPerson($loggedInUserId, 'profile');
        $datablocksAndCourses = $this->orgPermissionsetDatablockRepository->getDatablocksAndCoursesForPerson($loggedInUserId, 'profile');

        $datablockData = [];
        foreach ($datablocksAndGroups as $datablockAndGroup) {
            $datablockData[$datablockAndGroup['datablock_id']]['groups'][] = $datablockAndGroup['org_group_id'];
        }
        foreach ($datablocksAndCourses as $datablockAndCourse) {
            $datablockData[$datablockAndCourse['datablock_id']]['courses'][] = $datablockAndCourse['org_courses_id'];
        }

        // For each datablock, use the lists of groups and courses to get accessible students.  Combine these with the list of
        // students selected for the report to get the list of students whose data will be returned for the items in each datablock.
        // Then retrieve this data.
        foreach ($datablockData as $datablock => $groupsAndCourses) {
            $accessibleStudents = $this->getAccessibleStudents($groupsAndCourses, $studentsInGroups, $studentsInCourses);

            $studentsIncludedInReportItems = array_intersect($filteredStudentIds, $accessibleStudents);

            $profileItems = [];
            if (!empty($studentsIncludedInReportItems)) {
                $profileItems = $this->getProfileItems('ebi', $datablock, null, $loggedInUserId, $studentsIncludedInReportItems, $orgAcademicYearId);
            }

            $reportItems = array_merge($reportItems, $profileItems);
        }

        return $reportItems;
    }


    /**
     * Uses the raw data in $reportItem to create an array which has all the data for one report item,
     * formatted correctly to be converted to JSON for the final report.
     *
     * @param array $reportItem
     * @return array
     */
    private function assembleReportItemForJSON($reportItem)
    {
        $newItem = [];

        if (isset($reportItem['ebi_metadata_id'])) {
            $newItem['metadata_id'] = (int) $reportItem['ebi_metadata_id'];
            $newItem['metadata_source'] = 'ebi';
            $newItem['item_text'] = $this->formatItemText('ebi', $reportItem['datablock_desc'], $reportItem['meta_description'],
                null, $reportItem['meta_key'], $reportItem['scope'], $reportItem['org_academic_year'], $reportItem['org_academic_term']);

        } elseif (isset($reportItem['org_metadata_id'])) {
            $newItem['metadata_id'] = (int) $reportItem['org_metadata_id'];
            $newItem['metadata_source'] = 'isp';
            $newItem['item_text'] = $this->formatItemText('isp', null, $reportItem['meta_description'], $reportItem['meta_name'],
                $reportItem['meta_key'], $reportItem['scope'], $reportItem['org_academic_year'], $reportItem['org_academic_term']);
        }

        if (!empty($reportItem['org_academic_year'])) {
            $newItem['org_academic_year'] = (int) $reportItem['org_academic_year'];
        }
        if (!empty($reportItem['org_academic_term'])) {
            $newItem['org_academic_term'] = (int) $reportItem['org_academic_term'];
        }

        $newItem['metadata_type'] = $reportItem['metadata_type'];
        $newItem['count'] = $reportItem['count'];

        if ($this->globallyDrillable) {
            $newItem['drillable'] = true;
        } elseif ($this->globallyNotDrillable) {
            $newItem['drillable'] = false;
        } else {
            $newItem['drillable'] = $reportItem['drillable'];
        }

        if ($newItem['count'] < ReportsConstants::AGGREGATION_THRESHOLD) {
            $newItem['message'] = self::AGGREGATION_MESSAGE;
        } else {
            if ($newItem['metadata_type'] == 'N') {
                $newItem['statistical_summary'] = $reportItem['statistical_summary'];
                $newItem['histogram'] = $reportItem['histogram'];
            } elseif ($newItem['metadata_type'] == 'S') {
                $newItem['values'] = $reportItem['grouped_values'];
            } else {
                $newItem['message'] = self::TEXT_TYPE_ITEM_MESSAGE;
            }
        }

        return $newItem;
    }


    /**
     * Gets a list of students the user has access to via the groups and courses in $groupsAndCourses.
     * The $studentsInGroups and $studentsInCourses arrays are used to store these lists of students
     * so we don't have to perform the same query multiple times.  The keys in these arrays are strings containing
     * comma-separated lists of group/course ids.  This efficiency measure will work best if the lists of groups and courses
     * in $groupsAndCourses are sorted.
     *
     * @param array $groupsAndCourses - an associative array with keys 'groups' and 'courses', and values being arrays of group and course ids
     * @param array $studentsInGroups
     * @param array $studentsInCourses
     * @return array
     */
    private function getAccessibleStudents($groupsAndCourses, &$studentsInGroups = [], &$studentsInCourses = [])
    {
        $accessibleStudents = [];

        if (!empty($groupsAndCourses['groups'])) {
            $groupArrayKey = implode(',', $groupsAndCourses['groups']);
            if (! isset($studentsInGroups[$groupArrayKey])) {
                $studentsInGroups[$groupArrayKey] = $this->orgGroupStudentsRepository->getStudentsByGroups($groupsAndCourses['groups']);
            }
            $accessibleStudents = $studentsInGroups[$groupArrayKey];
        }

        if (!empty($groupsAndCourses['courses'])) {
            $courseArrayKey = implode(',', $groupsAndCourses['courses']);
            if (! isset($studentsInCourses[$courseArrayKey])) {
                $studentsInCourses[$courseArrayKey] = $this->orgCourseStudentRepository->getStudentsByCourses($groupsAndCourses['courses']);
            }
            $accessibleStudents = array_unique(array_merge($accessibleStudents, $studentsInCourses[$courseArrayKey]));    // union
        }

        return $accessibleStudents;
    }


    /**
     * Retrieves all data, including student data, for one ISP or all the ebi profile items in one datablock.
     * For year- and term-specific profile items, only returns data for the given academic year.
     * For each profile item of type 'T' (text), the data includes metadata values for all the given students.
     * For each profile item of type 'S' (categorical), the data includes all the possible options and the count and percentage
     *   of students from the given list who chose each option.
     * For each profile item of type 'N' (numeric), the data includes statistical data and a histogram
     *   using the metadata values of the given students.
     *
     * @param string $metadataSource - 'ebi' or 'isp'
     * @param int|null $datablockId - only used for ebi_metadata items
     * @param int|null $orgMetadataId
     * @param int $loggedInUserId
     * @param array $studentIds
     * @param int $orgAcademicYearId
     * @return array
     */
    private function getProfileItems($metadataSource, $datablockId, $orgMetadataId, $loggedInUserId, $studentIds, $orgAcademicYearId)
    {
        // Get all the needed non-student-related information about the profile item(s).
        if ($metadataSource == 'isp') {
            $profileItems = $this->getIspMetadata($orgMetadataId);
        } else {
            $profileItems = $this->ebiMetadataRepository->getEbiMetadataByDatablock($datablockId);
        }

        // Get the values for each of these profile items for each of the given students.
        // Sort by term where necessary.
        $profileItemsToReturn = [];

        foreach ($profileItems as &$profileItem) {
            if ($profileItem['metadata_type'] == 'T') {
                if ($this->globallyDrillable || $this->globallyNotDrillable) {
                    $reportItemsForCurrentMetadataId = $this->getGroupedValuesAndSortByTerm($profileItem, $studentIds, $orgAcademicYearId);
                } else {
                    $reportItemsForCurrentMetadataId = $this->getStudentValuesAndSortByTerm($profileItem, $loggedInUserId, $studentIds, $orgAcademicYearId);
                }

                $profileItemsToReturn = array_merge($profileItemsToReturn, $reportItemsForCurrentMetadataId);

            } elseif ($profileItem['metadata_type'] == 'S') {
                // For each possible option, find the number of students who chose that option.
                // Add the text for each option and the percentage of students who chose each option.

                if ($this->globallyDrillable || $this->globallyNotDrillable) {
                    $reportItemsForCurrentMetadataId = $this->getGroupedValuesAndSortByTerm($profileItem, $studentIds, $orgAcademicYearId);
                } else {
                    $reportItemsForCurrentMetadataId = $this->getStudentValuesAndSortByTerm($profileItem, $loggedInUserId, $studentIds, $orgAcademicYearId);
                }

                foreach ($reportItemsForCurrentMetadataId as $reportItem) {
                    if ($this->globallyDrillable || $this->globallyNotDrillable) {
                        $valuesAndCounts = $reportItem['values_and_counts'];
                    } else {
                        $valuesAndCounts = array_count_values($reportItem['values']);
                    }

                    if ($metadataSource == 'isp') {
                        $metadataListValues = $this->orgMetadataListValuesRepository->getListValuesAndNamesForOrgMetadata($reportItem['org_metadata_id']);
                    } else {
                        $metadataListValues = $this->ebiMetadataListValuesRepository->getListValuesAndNamesForEbiMetadata($reportItem['ebi_metadata_id']);
                    }

                    foreach ($metadataListValues as $listValue => $listName) {
                        $count = isset($valuesAndCounts[$listValue]) ? $valuesAndCounts[$listValue] : 0;
                        $reportItem['grouped_values'][] = [
                            'option_value' => $listValue,
                            'option_text' => $listName,
                            'count' => $count,
                            'percentage' => number_format(100 * $count / $reportItem['count'], 1)     // round and keep one digit after the decimal point
                        ];
                    }

                    $profileItemsToReturn[] = $reportItem;
                }

            } elseif ($profileItem['metadata_type'] == 'N') {
                // First do some pre-processing to sort, group, and make sure the data is formatted correctly.
                // Then find the statistical summary and build a histogram.

                if ($metadataSource == 'isp') {
                    $numberOfDecimals = $this->orgMetadataRepository->find($profileItem['org_metadata_id'])->getNoOfDecimals();
                } else {
                    $numberOfDecimals = $this->ebiMetadataRepository->find($profileItem['ebi_metadata_id'])->getNoOfDecimals();
                }

                if ($this->globallyDrillable || $this->globallyNotDrillable) {
                    $reportItemsForCurrentMetadataId = $this->getGroupedValuesAndSortByTerm($profileItem, $studentIds, $orgAcademicYearId, $numberOfDecimals);
                } else {
                    $reportItemsForCurrentMetadataId = $this->getStudentValuesAndSortByTerm($profileItem, $loggedInUserId, $studentIds, $orgAcademicYearId);
                }

                foreach ($reportItemsForCurrentMetadataId as $reportItem) {
                    if (!($this->globallyDrillable || $this->globallyNotDrillable)) {
                        sort($reportItem['values']);
                        $reportItem['values_and_counts'] = $this->getValuesAndCounts(($reportItem['values']), $numberOfDecimals);
                    }

                    if ($numberOfDecimals > 0) {
                        list($correctedNumberOfDecimals, $reportItem['values_and_counts']) = $this->correctNumberOfDecimals($numberOfDecimals, $reportItem['values_and_counts']);
                    } else {
                        $correctedNumberOfDecimals = $numberOfDecimals;
                    }

                    $reportItem['statistical_summary'] = $this->getStatisticalSummary($reportItem, $studentIds);
                    $reportItem['histogram'] = $this->createHistogram($reportItem, $correctedNumberOfDecimals);

                    $profileItemsToReturn[] = $reportItem;
                }
            }
        }

        return $profileItemsToReturn;
    }


    /**
     * Returns a singleton array containing at index 0 an associative array of all the needed data about one ISP
     * from the table org_metadata.  (The strange format is so it can be used in parallel with similar data from
     * ebi_metadata, where we retrieve all the profile items in a datablock at once.)
     *
     * @param int $orgMetadataId
     * @return array
     */
    private function getIspMetadata($orgMetadataId)
    {
        $orgMetadata = $this->orgMetadataRepository->find($orgMetadataId);
        $status = $orgMetadata->getStatus();
        $deletedAt = $orgMetadata->getDeletedAt();
        if (!empty($deletedAt) || $status == 'archived') {
            return [];
        }

        $isp['org_metadata_id'] = $orgMetadataId;
        $isp['meta_description'] = $orgMetadata->getMetaDescription();
        $isp['meta_name'] = $orgMetadata->getMetaName();
        $isp['meta_key'] = $orgMetadata->getMetaKey();
        $isp['metadata_type'] = $orgMetadata->getMetadataType();
        $isp['scope'] = $orgMetadata->getScope();

        return [$isp];
    }


    /**
     * Returns metadata values for the given students for one profile item (whose id is contained in $profileItemData).
     * For year- and term-specific profile items, only returns data for the given academic year.
     * For term-specific profile items, sorts the data into respective terms before returning it.
     * Adds the "drillable" property: whether to allow drilldown on the profile item.
     *
     * @param array $profileItemData - This associative array must at least contain the key 'ebi_metadata_id' or 'org_metadata_id'.
     * @param int $loggedInUserId
     * @param array $studentIds
     * @param int $orgAcademicYearId
     * @return array
     */
    private function getStudentValuesAndSortByTerm($profileItemData, $loggedInUserId, $studentIds, $orgAcademicYearId)
    {
        $itemsToReturn = [];

        if (isset($profileItemData['ebi_metadata_id'])) {
            $values = $this->personEbiMetadataRepository
                ->getMetadataValuesByEbiMetadataAndStudentIds($profileItemData['ebi_metadata_id'], $studentIds, [$orgAcademicYearId]);
        } elseif (isset($profileItemData['org_metadata_id'])) {
            $values = $this->personOrgMetadataRepository
                ->getMetadataValuesByOrgMetadataAndStudentIds($profileItemData['org_metadata_id'], $studentIds, [$orgAcademicYearId]);
        }

        if (!empty($values)) {
            $terms = array_unique(array_column($values, 'org_academic_terms_id'));
            if (count($terms) > 1) {
                // Create a new item for each term.
                foreach ($terms as $term) {
                    $termSpecificItem[$term] = $profileItemData;
                    $termSpecificItem[$term]['values'] = [];
                    $termSpecificItem[$term]['org_academic_term'] = $term;
                }

                // Classify each value into the appropriate term.
                foreach ($values as $value) {
                    $term = $value['org_academic_terms_id'];
                    $termSpecificItem[$term]['values'][] = $value;
                }

                foreach ($terms as $term) {
                    $termSpecificItem[$term]['org_academic_year'] = $orgAcademicYearId;
                    $termSpecificItem[$term]['count'] = count($termSpecificItem[$term]['values']);
                    $itemsToReturn[] = $termSpecificItem[$term];
                }

            } else {
                $profileItemData['org_academic_term'] = $values[0]['org_academic_terms_id'];
                $profileItemData['org_academic_year'] = $values[0]['org_academic_year_id'];     // year selected in filter (if scope Y or T), or null (if scope N)
                $profileItemData['count'] = count($values);
                $profileItemData['values'] = $values;
                $itemsToReturn[] = $profileItemData;
            }
        }

        // Determine whether the user has individual or aggregate access to each of the students included.
        // Then use this information to set the "drillable" property.
        foreach ($itemsToReturn as &$item) {
            $studentsWhoHaveProfileItemValues = array_column($item['values'], 'person_id');
            $accessLevels = $this->orgPermissionsetRepository->getAccessLevelForFacultyAndStudents($loggedInUserId, $studentsWhoHaveProfileItemValues);
            $accessLevelCounts = array_count_values($accessLevels);
            $individualAccessCount = isset($accessLevelCounts[1]) ? $accessLevelCounts[1] : 0;

            if (($individualAccessCount == 0)) {
                $item['drillable'] = false;
            } else {
                $item['drillable'] = true;
            }

            $item['values'] = array_column($item['values'], 'metadata_value');
        }

        return $itemsToReturn;
    }


    /**
     * Returns a list of distinct metadata values and their counts for the given students for one profile item (whose id is contained in $profileItemData).
     * For year- and term-specific profile items, only returns data for the given academic year.
     * For term-specific profile items, sorts the data into respective terms before returning it.
     *
     * This function does the grouping in the database, providing a "fast-track" for users who have the same access level to all students in the report,
     * and thus don't require the "drillable" property to be set on an item-by-item basis.
     *
     * @param array $profileItemData - This associative array must at least contain the key 'ebi_metadata_id' or 'org_metadata_id'.
     * @param array $studentIds
     * @param int $orgAcademicYearId
     * @param int|null $numberOfDecimals - For numeric items, the number of digits after the decimal each value has.
     * @return array
     */
    private function getGroupedValuesAndSortByTerm($profileItemData, $studentIds, $orgAcademicYearId, $numberOfDecimals = null)
    {
        $itemsToReturn = [];

        if ($profileItemData['scope'] == 'N') {
            $yearForCurrentItem = null;
        } else {
            $yearForCurrentItem = $orgAcademicYearId;
        }

        if (isset($profileItemData['ebi_metadata_id'])) {
            $groupedMetadataValueRecords = $this->personEbiMetadataRepository
                ->getGroupedMetadataValuesByEbiMetadataAndStudentIds($profileItemData['ebi_metadata_id'], $studentIds, $yearForCurrentItem, null, $numberOfDecimals);
        } elseif (isset($profileItemData['org_metadata_id'])) {
            $groupedMetadataValueRecords = $this->personOrgMetadataRepository
                ->getGroupedMetadataValuesByOrgMetadataAndStudentIds($profileItemData['org_metadata_id'], $studentIds, $yearForCurrentItem, null, $numberOfDecimals);
        }

        if (!empty($groupedMetadataValueRecords)) {
            $terms = array_unique(array_column($groupedMetadataValueRecords, 'org_academic_terms_id'));
            if (count($terms) > 1) {
                // Create a new item for each term.
                foreach ($terms as $term) {
                    $termSpecificItem[$term] = $profileItemData;
                    $termSpecificItem[$term]['values_and_counts'] = [];
                    $termSpecificItem[$term]['org_academic_term'] = $term;
                }

                // Classify each value into the appropriate term.
                foreach ($groupedMetadataValueRecords as $record) {
                    $term = $record['org_academic_terms_id'];
                    $termSpecificItem[$term]['values_and_counts'][] = $record;
                }

                foreach ($terms as $term) {
                    $termSpecificItem[$term]['org_academic_year'] = $orgAcademicYearId;
                    $termSpecificItem[$term]['count'] = array_sum(array_column($termSpecificItem[$term]['values_and_counts'], 'count'));
                    $itemsToReturn[] = $termSpecificItem[$term];
                }

            } else {
                $profileItemData['org_academic_term'] = $groupedMetadataValueRecords[0]['org_academic_terms_id'];
                $profileItemData['org_academic_year'] = $groupedMetadataValueRecords[0]['org_academic_year_id'];     // year selected in filter (if scope Y or T), or null (if scope N)
                $profileItemData['count'] = array_sum(array_column($groupedMetadataValueRecords, 'count'));
                $profileItemData['values_and_counts'] = $groupedMetadataValueRecords;
                $itemsToReturn[] = $profileItemData;
            }
        }

        // Transform the 'values_and_counts' array from raw database format to a lookup table.
        foreach ($itemsToReturn as &$itemToReturn) {
            $valuesAndCounts = [];
            foreach ($itemToReturn['values_and_counts'] as $record) {
                $value = $record['metadata_value'];
                $count = (int) $record['count'];
                $valuesAndCounts[$value] = $count;
            }
            $itemToReturn['values_and_counts'] = $valuesAndCounts;
        }

        return $itemsToReturn;
    }


    /**
     * Returns an array where the keys are the distinct values in $values, and the values are the number of times each appears.
     *
     * @param array $values
     * @param int $numberOfDecimals
     * @return array
     */
    private function getValuesAndCounts($values, $numberOfDecimals)
    {
        // First need to convert or format the numbers so they can be grouped correctly.
        if ($numberOfDecimals == 0) {
            $values = array_map('intval', $values);
        } else {
            foreach ($values as &$value) {
                $value = number_format($value, $numberOfDecimals, '.', '');
            }
        }

        return array_count_values($values);
    }


    /**
     * Returns an array containing:
     * At index 0: the corrected number of decimals
     * At index 1: the $valuesAndCounts array, updated so that the values in it have this correct number of decimal places.
     * It assumes that the $valuesAndCounts array passed to it has the values all formatted to have $numberOfDecimals decimal places.
     *
     * This function is needed because the no_of_decimals in the database is an upper bound and often does not reflect the actual data.
     * Without doing this correction, some of the numbers displayed would have too many decimal places and some of the histograms
     * would not be divided as naturally.
     *
     * @param int $numberOfDecimals
     * @param array $valuesAndCounts
     * @return array
     */
    private function correctNumberOfDecimals($numberOfDecimals, $valuesAndCounts)
    {
        // Determine how many extra zeros the formatted values have.
        // If any doesn't have extra zeros, then we won't make a correction.
        $minZeroCount = $numberOfDecimals;
        foreach ($valuesAndCounts as $value => $count) {
            $currentZeroCount = 0;
            for ($i = 1; $i <= $numberOfDecimals; $i++) {
                if (substr($value, -1 * $i, 1) == '0') {
                    $currentZeroCount ++;
                }
            }
            if ($currentZeroCount < $minZeroCount) {
                $minZeroCount = $currentZeroCount;
            }
        }

        // Find the corrected number of decimal places and use it to reformat the values.
        $newNumberOfDecimals = $numberOfDecimals - $minZeroCount;
        if ($newNumberOfDecimals != $numberOfDecimals) {
            $newValuesAndCounts = [];
            foreach ($valuesAndCounts as $value => $count) {
                $revisedValue = number_format($value, $newNumberOfDecimals, '.', '');
                $newValuesAndCounts[$revisedValue] = $count;
            }
        } else {
            $newValuesAndCounts = $valuesAndCounts;
        }

        return [$newNumberOfDecimals, $newValuesAndCounts];
    }


    /**
     * Finds the statistical summary for a numeric profile item, including mean, standard deviation, min, max, median, and mode.
     *
     * @param array $numericItem - all the data we have so far about the profile item, including an ordered list of values
     *                              and a grouped list of values and their counts.
     * @param array $studentIds
     * @return array
     */
    private function getStatisticalSummary($numericItem, $studentIds)
    {
        $statisticalSummary = [];

        $valuesAndCounts = $numericItem['values_and_counts'];

        if (isset($numericItem['org_metadata_id'])) {
            $meanAndStdDev = $this->personOrgMetadataRepository
                ->getMeanAndStdDevByOrgMetadataAndStudentIds($numericItem['org_metadata_id'], $studentIds, $numericItem['org_academic_year'], $numericItem['org_academic_term']);
        } else {
            $meanAndStdDev = $this->personEbiMetadataRepository
                ->getMeanAndStdDevByEbiMetadataAndStudentIds($numericItem['ebi_metadata_id'], $studentIds, $numericItem['org_academic_year'], $numericItem['org_academic_term']);
        }

        $statisticalSummary['mean'] = $meanAndStdDev['mean'];
        $statisticalSummary['std_dev'] = $meanAndStdDev['std_dev'];

        // If we already have all the individual values (i.e., we're not on the fast track), get the median the easy way.
        // Otherwise, we have to do a bit more work using the $valuesAndCounts array.
        if (isset($numericItem['values'])) {
            $values = $numericItem['values'];

            $statisticalSummary['min'] = $values[0];
            $statisticalSummary['max'] = $values[count($values)-1];

            // Find the median.
            $valueCount = count($values);

            if ($valueCount % 2 == 1) {
                $middleKey = (int) ($valueCount / 2);
                $statisticalSummary['median'] = $values[$middleKey];
            } else {
                $upperMiddleKey = $valueCount / 2;
                $lowerMiddleKey = $upperMiddleKey - 1;
                $statisticalSummary['median'] = ($values[$lowerMiddleKey] + $values[$upperMiddleKey]) / 2;
            }

        } else {
            // Get the min and max by sorting by key and then using the first and last key.
            ksort($valuesAndCounts);
            reset($valuesAndCounts);
            $statisticalSummary['min'] = key($valuesAndCounts);
            end($valuesAndCounts);
            $statisticalSummary['max'] = key($valuesAndCounts);

            // Find the median.
            $valueCount = array_sum($valuesAndCounts);

            if ($valueCount % 2 == 1) {
                $middleKey = (int) ($valueCount / 2);

                // In order to get the value associated with $middleKey, we move $currentKey through the $valuesAndCounts array
                // by incrementing it by successive counts until it reaches or passes $middleKey.
                // It needs to start at -1 because, for example, if the first value has count 1, that one value would be
                // at index 0 in a sorted value array, which is what $currentKey will be at after adding $count to it.
                $currentKey = -1;
                foreach ($valuesAndCounts as $value => $count) {
                    $currentKey += $count;
                    if ($middleKey <= $currentKey) {
                        $statisticalSummary['median'] = $value;
                        break;
                    }
                }
            } else {
                $upperMiddleKey = $valueCount / 2;
                $lowerMiddleKey = $upperMiddleKey - 1;

                $currentKey = -1;
                $lowerMiddleValue = null;
                $upperMiddleValue = null;
                foreach ($valuesAndCounts as $value => $count) {
                    $currentKey += $count;
                    if ($lowerMiddleKey <= $currentKey && is_null($lowerMiddleValue)) {     // This check for null is needed in case $lowerMiddleKey and $upperMiddleKey are reached in successive iterations.
                        $lowerMiddleValue = $value;
                    }
                    if ($upperMiddleKey <= $currentKey) {
                        $upperMiddleValue = $value;
                        break;
                    }
                }

                $statisticalSummary['median'] = ($lowerMiddleValue + $upperMiddleValue) / 2;
            }
        }

        // Find the mode.
        // Use the array of grouped values already assembled, and put the largest counts at the beginning of the array.
        arsort($valuesAndCounts);

        // Make sure the mode includes all the values which have the same largest count.
        $maxCount = reset($valuesAndCounts);
        $mode = [];
        foreach ($valuesAndCounts as $value => $count) {
            if ($count < $maxCount) {
                break;
            } else {
                $mode[] = $value;
            }
        }
        sort($mode);
        $statisticalSummary['mode'] = implode(', ', $mode);

        return $statisticalSummary;
    }


    /**
     * Creates a histogram for a numeric profile item.  Tries to create a "good" histogram by doing the following:
     *
     * 1. If there are a small number of different values in the data and they are not too spread out
     *    (if the difference between the min and max would result in no more than $maxBinCount bins),
     *    then each value will have its own bin.  This will avoid unnatural gaps in the histogram.
     *
     * 2. Otherwise, we try to choose a number of bins that will avoid having one bin with "too much" of the data.
     *    We try to get the largest percent in a bin to be less than $maxBinPercentThreshold by trying the numbers
     *    in the list $possibleBinCounts.  We use the first bin count that achieves this result.  If none achieves this
     *    result, we use the one which minimizes the largest percent in a bin.
     *
     * $maxBinCount, $possibleBinCounts, and $maxBinPercentThreshold are initialized at the beginning of this function,
     * and can easily be changed to try to produce better histograms.
     *
     * @param array $numericItem - all the data we have so far about the profile item
     * @param int $numberOfDecimals
     * @return array
     */
    private function createHistogram($numericItem, $numberOfDecimals)
    {
        $maxBinCount = 20;
        $possibleBinCounts = [10, 12, 8, 14, 6, 16, 4, 18, 2, 20];
        $maxBinPercentThreshold = 50;
        $bestMaxBinPercent = 100;
        $bestBinCount = 10; // Just initializing it to start.  It will get adjusted below as necessary.

        // Add a "unit" to the max so that each bin can have an inclusive minimum and an exclusive maximum.
        $unitToAdd = pow(10, -1 * $numberOfDecimals);
        $min = $numericItem['statistical_summary']['min'];
        $max = $numericItem['statistical_summary']['max'] + $unitToAdd;

        $normalizedWidth = ($max - $min) * pow(10, $numberOfDecimals);
        if ($normalizedWidth <= $maxBinCount) {
            $binCount = $normalizedWidth;
            $bins = $this->createHistogramWithSpecifiedNumberOfBins($numericItem, $numberOfDecimals, $min, $max, $binCount);
        } else {
            foreach ($possibleBinCounts as $binCount) {
                $bins = $this->createHistogramWithSpecifiedNumberOfBins($numericItem, $numberOfDecimals, $min, $max, $binCount);
                $maxBinPercent = 0;
                foreach ($bins as $bin) {
                    if ($bin['percentage'] > $maxBinPercent) {
                        $maxBinPercent = $bin['percentage'];
                    }
                }

                if ($maxBinPercent < $maxBinPercentThreshold) {
                    $bestBinCount = $binCount;
                    break;
                } elseif ($maxBinPercent < $bestMaxBinPercent) {
                    $bestMaxBinPercent = $maxBinPercent;
                    $bestBinCount = $binCount;
                }
            }

            $bins = $this->createHistogramWithSpecifiedNumberOfBins($numericItem, $numberOfDecimals, $min, $max, $bestBinCount);
        }

        return $bins;
    }


    /**
     * Creates a histogram with the specified number of bins and sorts the values contained in $numericItem into those bins.
     *
     * @param array $numericItem - all the data we have so far about the profile item, including a grouped list of values and their counts
     * @param int $numberOfDecimals
     * @param float|int $min
     * @param float|int $max
     * @param int $binCount
     * @return array
     */
    private function createHistogramWithSpecifiedNumberOfBins($numericItem, $numberOfDecimals, $min, $max, $binCount)
    {
        $binWidth = ($max - $min) / $binCount;

        $bins = [];
        for ($i = 0; $i < $binCount; $i++) {
            $bins[$i]['bin_min'] = $min + $i * $binWidth;
            $bins[$i]['bin_max'] = $min + ($i + 1) * $binWidth;
            $bins[$i]['count'] = 0;
        }

        $valuesAndCounts = $numericItem['values_and_counts'];
        $currentBin = 0;
        foreach ($valuesAndCounts as $value => $count) {
            while ($value >= $bins[$currentBin]['bin_max']) {
                $currentBin++;
            }
            $bins[$currentBin]['count'] += $count;
        }

        for ($i = 0; $i < $binCount; $i++) {
            $bins[$i]['bin_min'] = $this->roundUp($bins[$i]['bin_min'], $numberOfDecimals);
            $bins[$i]['bin_max'] = $this->roundUp($bins[$i]['bin_max'], $numberOfDecimals);
            $bins[$i]['percentage'] = number_format(100 * $bins[$i]['count'] / $numericItem['count'], 1);
        }

        return $bins;
    }


    /**
     * Rounds up a float to a specified number of decimal places.
     *
     * @param float $value
     * @param int $places
     * @return float
     */
    private function roundUp ($value, $places = 0)
    {
        $multiplier = pow(10, $places);
        return ceil($value * $multiplier) / $multiplier;
    }


    /**
     * Uses all the data passed in to format the item text as desired.
     *
     * @param string $metadataSource - 'ebi' or 'isp'
     * @param string $datablockDesc - datablock_master_lang.datablock_desc (only for ebi items)
     * @param string $metaDescription - ebi_metadata_lang.metadata_description or org_metadata.metadata_description
     * @param string $metaName - org_metadata.meta_name (only for ISPs)
     * @param string $metaKey - ebi_metadata.meta_key or org_metadata.meta_key
     * @param string $scope - "T" (term) or "Y" (year) or "N" (neither)
     * @param int $yearId - org_academic_year_id
     * @param int $termId - org_academic_terms_id
     * @return string
     */
    private function formatItemText($metadataSource, $datablockDesc, $metaDescription, $metaName, $metaKey, $scope, $yearId, $termId)
    {
        if ($metadataSource == 'isp') {
            $itemText = 'ISP: ';
        } else {
            $itemText = "Profile Item: $datablockDesc > ";
        }

        if (!empty($metaDescription)) {
            $itemText .= $metaDescription;
        } elseif (!empty($metaName)) {
            $itemText .= $metaName;
        } else {
            $itemText .= $metaKey;
        }

        if ($scope == 'Y' or $scope == 'T') {
            $orgAcademicYear = $this->orgAcademicYearRepository->find($yearId);
            $yearName = $orgAcademicYear->getName();
            $itemText .= " ($yearName";

            if ($scope == 'T') {
                $orgAcademicTerm = $this->orgAcademicTermsRepository->find($termId);
                $termName = $orgAcademicTerm->getName();
                $itemText .= ", $termName";
            }

            $itemText .= ')';
        }

        return $itemText;
    }


    /**
     * Throws a SynapseValidationException if the given metadata has 'Y' or 'T' scope and the $yearId or $termId (respectively) is null.
     *
     * @param int $metadataId
     * @param string $metadataSource - 'ebi' or 'isp'
     * @param int|null $yearId
     * @param int|null $termId
     * @throws SynapseValidationException
     */
    public function validateScope($metadataId, $metadataSource, $yearId, $termId)
    {
        if ($metadataSource == 'isp') {
            $scope = $this->orgMetadataRepository->find($metadataId)->getScope();
        } else {
            $scope = $this->ebiMetadataRepository->find($metadataId)->getScope();
        }

        if ($scope == 'Y' && empty($yearId)) {
            throw new SynapseValidationException('This profile item requires a year_id query parameter.');
        }

        if ($scope == 'T' && empty($termId)) {
            throw new SynapseValidationException('This profile item requires a term_id query parameter.');
        }
    }


    /**
     * Returns a page of student data for a particular profile item on the Profile Snapshot Report.
     *
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param int $reportInstanceId -- id from the reports_running_status table
     * @param int $metadataId -- ebi_metadata_id or org_metadata_id
     * @param string $metadataSource -- "ebi" or "isp", depending on the source of the profile item
     * @param int $pageNumber -- which page of results to display
     * @param int $recordsPerPage -- number of records per page
     * @param string $sortBy -- column to sort by: "risk_color" or "name" or "class_level" or "profile_item_value", possibly preceded by "-" or "+" to indicate sort direction
     * @param int $orgAcademicYearId -- for year-specific profile items
     * @param int $orgAcademicTermId -- for term-specific profile items
     * @param int $optionValue -- for Categorical items, drilldown into a particular option
     * @param int|float $optionMin -- for Numeric items, minimum of the histogram bin to drill into
     * @param int|float $optionMax -- for Numeric items, maximum of the histogram bin to drill into
     * @return ProfileDrilldownDto
     */
    public function getDrilldownJSONResponse($loggedInUserId, $organizationId, $reportInstanceId, $metadataId, $metadataSource, $pageNumber, $recordsPerPage, $sortBy, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax)
    {
        $allAccessFlag = false;     // This will be set to true if the user has individual access and risk access for all students included in the report.

        $yearIdArray = $orgAcademicYearId ? [$orgAcademicYearId] : [];
        $termIdArray = $orgAcademicTermId ? [$orgAcademicTermId] : [];

        // For term-specific items, a year may not have been passed in but is needed in the item text.
        if (!is_null($orgAcademicTermId) && is_null($orgAcademicYearId)) {
            $orgAcademicYearId = $this->orgAcademicTermsRepository->find($orgAcademicTermId)->getOrgAcademicYear()->getId();
        }

        // Get the current org_academic_year_id, in order to limit the drilldown to participant students.
        $currentOrgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);

        // Get an array of all the students selected for this report via the preliminary filters.
        $reportInstance = $this->reportsRunningStatusRepository->find($reportInstanceId);
        $filteredStudentIds = explode(',', $reportInstance->getFilteredStudentIds());

        // Get a list of students whose data should be accessible for the given profile item.
        // Then intersect with the filtered students to get the students potentially available in the drilldown.
        // Note: These students may not actually be individually accessible because of aggregate permission sets.
        // The access level for these students needs to be handled later, after we know how many actually have profile item values.
        $accessibleStudentIds = $this->getAccessibleStudentsForProfileItem($loggedInUserId, $metadataSource, $metadataId);
        $filteredAccessibleStudentIds = array_intersect($filteredStudentIds, $accessibleStudentIds);

        // In order to provide a fast track, check whether the user has individual access and access to risk for all students included in the report,
        // and all the students are participants for the current academic year.
        $accessLevelCounts = $this->orgPermissionsetRepository->getGroupedAccessLevelsForFacultyAndStudents($loggedInUserId, $filteredAccessibleStudentIds);
        if (is_null($accessLevelCounts[0])) {
            $riskPermission = $this->orgPermissionsetRepository->getGroupedRiskPermissionsForFacultyAndStudents($loggedInUserId, $filteredAccessibleStudentIds);
            if (is_null($riskPermission[0])) {
                $studentListContainsNonParticipants = $this->orgPersonStudentYearRepository->doesStudentIdListContainNonParticipants($filteredAccessibleStudentIds, $currentOrgAcademicYearId);
                if (!$studentListContainsNonParticipants) {
                    $allAccessFlag = true;
                }
            }
        }

        // If the user has individual and risk access to all the students included in the report, and all the students are current participants, then we don't have to be as careful;
        // we can just get the records for the requested page and separately get a count of all students that have a value for this profile item.
        if ($allAccessFlag) {
            $profileDrilldownRecords = $this->getProfileDrilldownRecords($loggedInUserId, $filteredAccessibleStudentIds, $metadataSource, $metadataId, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax, $sortBy, $recordsPerPage, $pageNumber, $allAccessFlag);

            if ($metadataSource == 'isp') {
                $recordCount = $this->personOrgMetadataRepository->getMetadataValuesByOrgMetadataAndStudentIds($metadataId, $filteredAccessibleStudentIds, $yearIdArray, $termIdArray, $optionValue, $optionMin, $optionMax, true);
            } else {
                $recordCount = $this->personEbiMetadataRepository->getMetadataValuesByEbiMetadataAndStudentIds($metadataId, $filteredAccessibleStudentIds, $yearIdArray, $termIdArray, $optionValue, $optionMin, $optionMax, true);
            }

            $individualNonParticipantCount = 0;
            $aggregateOnlyParticipantCount = 0;
            $aggregateOnlyNonParticipantCount = 0;

        } else {

            // Get values for the given profile item for the list of students we just found.
            // This is a preliminary query to narrow down the list of students to only those who have a value for this profile item.
            // It's needed to get an accurate count for the aggregation restriction.
            if ($metadataSource == 'isp') {
                $profileItemValues = $this->personOrgMetadataRepository->getMetadataValuesByOrgMetadataAndStudentIds($metadataId, $filteredAccessibleStudentIds, $yearIdArray, $termIdArray, $optionValue, $optionMin, $optionMax);
            } else {
                $profileItemValues = $this->personEbiMetadataRepository->getMetadataValuesByEbiMetadataAndStudentIds($metadataId, $filteredAccessibleStudentIds, $yearIdArray, $termIdArray, $optionValue, $optionMin, $optionMax);
            }

            $studentsWhoHaveProfileItemValues = array_column($profileItemValues, 'person_id');

            // Determine whether the user has individual or aggregate permission to each of the students who have a value for this profile item.
            $accessLevels = $this->orgPermissionsetRepository->getAccessLevelForFacultyAndStudents($loggedInUserId, $studentsWhoHaveProfileItemValues);

            $individuallyAccessibleStudents = array_keys($accessLevels, 1);
            $aggregateOnlyStudents = array_keys($accessLevels, 0);

            // For each of these groupings, determine which students are participants in the current year.
            $individuallyAccessibleParticipants = $this->orgPersonStudentYearRepository->getParticipantStudentsFromStudentList($individuallyAccessibleStudents, $organizationId, $currentOrgAcademicYearId);
            $aggregateOnlyParticipants = $this->orgPersonStudentYearRepository->getParticipantStudentsFromStudentList($aggregateOnlyStudents, $organizationId, $currentOrgAcademicYearId);

            $individualParticipantCount = count($individuallyAccessibleParticipants);
            $individualNonParticipantCount = count($individuallyAccessibleStudents) - $individualParticipantCount;

            $aggregateOnlyParticipantCount = count($aggregateOnlyParticipants);
            $aggregateOnlyNonParticipantCount = count($aggregateOnlyStudents) - $aggregateOnlyParticipantCount;

            // Throw an exception if there are no students which should be included in the drilldown,
            if ($individualParticipantCount == 0) {
                throw new AccessDeniedException('No individual student data for this profile item.');
            } else {
                $recordCount = $individualParticipantCount;
            }

            // Get all the data needed for the current page of results, including student names, risk, class levels, and profile item values.
            $profileDrilldownRecords = $this->getProfileDrilldownRecords($loggedInUserId, $individuallyAccessibleParticipants, $metadataSource, $metadataId, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax, $sortBy, $recordsPerPage, $pageNumber);
        }

        // Gets the status of the participant students
        $activeStatusStudentList = $this->orgPersonStudentYearRepository->getActiveStatusForStudentList($organizationId, $currentOrgAcademicYearId, $filteredAccessibleStudentIds);

        // Assign values to the DTO properties for each student.
        $profileDrilldownRecordDtos = [];
        foreach ($profileDrilldownRecords as $record) {
            $profileDrilldownRecordDto = new ProfileDrilldownRecordDto();

            $profileDrilldownRecordDto->setStudentId($record['student_id']);
            $profileDrilldownRecordDto->setFirstName($record['firstname']);
            $profileDrilldownRecordDto->setLastName($record['lastname']);
            $profileDrilldownRecordDto->setRiskColor($record['risk_color']);
            $profileDrilldownRecordDto->setRiskImageName($record['risk_image_name']);
            $profileDrilldownRecordDto->setClassLevel($record['class_level']);
            $profileDrilldownRecordDto->setProfileItemValue($record['profile_item_value']);

            // Set student status
            $studentStatus = $activeStatusStudentList[$record['student_id']];
            $profileDrilldownRecordDto->setStudentIsActive($studentStatus);

            $profileDrilldownRecordDtos[] = $profileDrilldownRecordDto;
        }

        // Assign appropriate values to the top-level DTO properties.
        $profileDrilldownDto = new ProfileDrilldownDto();
        $profileDrilldownDto->setListTitle('Profile Snapshot Report');
        $profileDrilldownDto->setPersonId($loggedInUserId);
        $profileDrilldownDto->setCurrentPage($pageNumber);
        $profileDrilldownDto->setRecordsPerPage($recordsPerPage);
        $profileDrilldownDto->setTotalRecords($recordCount);
        $profileDrilldownDto->setIndividualNonParticipantCount($individualNonParticipantCount);
        $profileDrilldownDto->setAggregateOnlyNonParticipantCount($aggregateOnlyNonParticipantCount);
        $profileDrilldownDto->setAggregateOnlyParticipantCount($aggregateOnlyParticipantCount);

        $pageCount = ceil($recordCount / $recordsPerPage);
        $profileDrilldownDto->setTotalPages($pageCount);

        $profileItemText = $this->getItemText($metadataId, $metadataSource, $orgAcademicYearId, $orgAcademicTermId);
        $profileDrilldownDto->setProfileItemText($profileItemText);

        $searchAttributes = $this->serializer->deserialize($reportInstance->getFilterCriteria(), 'array', 'json');
        $profileDrilldownDto->setSearchAttributes($searchAttributes);

        $profileDrilldownDto->setSearchResult($profileDrilldownRecordDtos);

        return $profileDrilldownDto;
    }


    /**
     * Returns a list of ids for all students the user is connected to via a group or course, and for which the permission set
     * gives the user access to the particular profile item (either through a datablock, for ebi_metadata, or directly, for ISPs).
     * Note that the user may or may not have individual access to these students.
     *
     * @param int $loggedInUserId
     * @param string $metadataSource - "ebi" or "isp", depending on the source of the profile item
     * @param int $metadataId - ebi_metadata_id or org_metadata_id
     * @return array
     */
    private function getAccessibleStudentsForProfileItem($loggedInUserId, $metadataSource, $metadataId)
    {
        if ($metadataSource == 'isp') {
            $groups = $this->orgPermissionsetMetadataRepository->getGroupsWithPermissionToGivenIspForPerson($loggedInUserId, $metadataId);
            $courses = $this->orgPermissionsetMetadataRepository->getCoursesWithPermissionToGivenIspForPerson($loggedInUserId, $metadataId);
        } else {
            $datablock = $this->datablockMetadataRepository->findOneBy(['ebiMetadata' => $metadataId])->getDatablock()->getId();
            $groups = $this->orgPermissionsetDatablockRepository->getGroupsWithGivenDatablockForPerson($loggedInUserId, $datablock);
            $courses = $this->orgPermissionsetDatablockRepository->getCoursesWithGivenDatablockForPerson($loggedInUserId, $datablock);
        }

        $studentsInGroups = [];
        $studentsInCourses = [];
        if (!empty($groups)) {
            $studentsInGroups = $this->orgGroupStudentsRepository->getStudentsByGroups($groups);
        }
        if (!empty($courses)) {
            $studentsInCourses = $this->orgCourseStudentRepository->getStudentsByCourses($courses);
        }

        $studentsInGroupsAndCourses = array_unique(array_merge($studentsInGroups, $studentsInCourses));

        return $studentsInGroupsAndCourses;
    }


    /**
     * Returns an array of records for the current page of drilldown results, or for all results if $recordsPerPage is null,
     * where each record includes the name, internal id, external id, email, risk, class level, and profile item value of one student.
     *
     * This function carefully applies permissions to ensure we are not showing or allowing the user to infer students' risk if not permitted.
     * For categorical profile items, this function also replaces the numeric value with the option text.
     *
     * @param int $loggedInUserId
     * @param array $studentIds
     * @param string $metadataSource - "ebi" or "isp"
     * @param int $metadataId - ebi_metadata_id or org_metadata_id
     * @param int $orgAcademicYearId -- for year-specific profile items
     * @param int $orgAcademicTermId -- for term-specific profile items
     * @param int $optionValue -- for Categorical items, drilldown into a particular option
     * @param int|float $optionMin -- for Numeric items, minimum of the histogram bin to drill into
     * @param int|float $optionMax -- for Numeric items, maximum of the histogram bin to drill into
     * @param string $sortBy - column to sort by: "risk_color" or "name" or "class_level" or "profile_item_value", possibly preceded by "-" or "+" to indicate sort direction
     * @param int|null $recordsPerPage - number of records per page, or null for getting the whole list
     * @param int $pageNumber
     * @param boolean $allAccessFlag - true if the user has individual and risk access to all the included students
     * @return array
     */
    private function getProfileDrilldownRecords($loggedInUserId, $studentIds, $metadataSource, $metadataId, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax, $sortBy, $recordsPerPage = null, $pageNumber = null, $allAccessFlag = false)
    {
        $classLevelMetadataId = $this->ebiMetadataRepository->findOneBy(['key' => 'ClassLevel'])->getId();

        if (isset($recordsPerPage)) {
            $offset = $recordsPerPage * ($pageNumber - 1);      // The index of the first record to include in the list.
        } else {
            $offset = 0;
        }

        if ($metadataSource == 'isp') {
            $personMetadataRepository = $this->personOrgMetadataRepository;
        } else {
            $personMetadataRepository = $this->personEbiMetadataRepository;
        }

        if (strpos($sortBy, 'risk') === false) {

            // For sorting by anything besides risk, we simply get the appropriate number of results at the appropriate offset,
            // then determine the risk permission for them to be applied later.

            $drilldownRecords = $personMetadataRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues($metadataId, $classLevelMetadataId, $studentIds, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax, $sortBy, $recordsPerPage, $offset);

            $studentsOnCurrentPage = array_column($drilldownRecords, 'student_id');

            $riskPermission = $this->orgPermissionsetRepository->getRiskPermissionForFacultyAndStudents($loggedInUserId, $studentsOnCurrentPage);

        } else {

            // For sorting by risk, we need to create separate lists of students with and without the risk permission;
            // otherwise, the risk level of those without the risk permission would be obvious by where they appear in the list.
            // The $offset and $recordsPerPage are used to determine which student list to use (or possibly both).
            // The records for students without the risk permission will appear alphabetically at the end of the list.

            if ($allAccessFlag) {
                $studentsWithRiskPermission = $studentIds;
                $studentsWithoutRiskPermission = [];
            } else {
                $riskPermission = $this->orgPermissionsetRepository->getRiskPermissionForFacultyAndStudents($loggedInUserId, $studentIds);
                $studentsWithRiskPermission = array_keys($riskPermission, 1);
                $studentsWithoutRiskPermission = array_keys($riskPermission, 0);
            }

            if (is_null($recordsPerPage)) {
                $drilldownRecordsWithRisk = $personMetadataRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues($metadataId, $classLevelMetadataId, $studentsWithRiskPermission, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax, $sortBy);
                $drilldownRecordsWithoutRisk = $personMetadataRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues($metadataId, $classLevelMetadataId, $studentsWithoutRiskPermission, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax, 'name');
                $drilldownRecords = array_merge($drilldownRecordsWithRisk, $drilldownRecordsWithoutRisk);

            } else {

                if ($offset < count($studentsWithRiskPermission)) {
                    $drilldownRecords = $personMetadataRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues($metadataId, $classLevelMetadataId, $studentsWithRiskPermission, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax, $sortBy, $recordsPerPage, $offset);

                    if ((count($drilldownRecords) < $recordsPerPage) && (count($studentsWithoutRiskPermission) > 0)) {
                        $noRiskRecordLimit = $recordsPerPage - count($drilldownRecords);
                        $extraDrilldownRecords = $personMetadataRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues($metadataId, $classLevelMetadataId, $studentsWithoutRiskPermission, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax, 'name', $noRiskRecordLimit, 0);
                        $drilldownRecords = array_merge($drilldownRecords, $extraDrilldownRecords);
                    }
                } else {
                    $noRiskOffset = $offset - count($studentsWithRiskPermission);
                    $drilldownRecords = $personMetadataRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues($metadataId, $classLevelMetadataId, $studentsWithoutRiskPermission, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax, 'name', $recordsPerPage, $noRiskOffset);
                }
            }
        }

        $grayRiskImageName = $this->riskLevelsRepository->findOneBy(['riskText' => 'gray'])->getImageName();

        // Find the metadata type of the profile item.
        // If it's a categorical item ('S'), get a lookup table of options for the given (categorical) profile item,
        // where the keys are the numeric values and the values are the corresponding text names.
        // Then we'll later be able to replace these values for each student.
        if ($metadataSource == 'isp') {
            $metadataType = $this->orgMetadataRepository->find($metadataId)->getMetadataType();
        } else {
            $metadataType = $this->ebiMetadataRepository->find($metadataId)->getMetadataType();
        }

        if ($metadataType == 'S') {
            if ($metadataSource == 'isp') {
                $optionNames = $this->orgMetadataListValuesRepository->getListValuesAndNamesForOrgMetadata($metadataId);
            } else {
                $optionNames = $this->ebiMetadataListValuesRepository->getListValuesAndNamesForEbiMetadata($metadataId);
            }
        }

        // Replace or null out risk and/or profile item values as needed.
        foreach ($drilldownRecords as &$record) {

            // For records with risk permission but without a risk value, set the risk color to gray.
            // Null out risk for records without those permissions.
            if ($allAccessFlag || $riskPermission[$record['student_id']]) {
                if (empty($record['risk_color'])) {
                    $record['risk_color'] = 'gray';
                    $record['risk_image_name'] = $grayRiskImageName;
                }
            } else {
                $record['risk_color'] = null;
                $record['risk_image_name'] = null;
            }

            // Use the text option for categorical items; for the other types, use the actual recorded value.
            if ($metadataType == 'S') {
                $record['profile_item_value']  = $optionNames[$record['profile_item_value']];
            }
        }

        return $drilldownRecords;
    }


    /**
     * Gets the text describing a particular profile item, to be displayed at the top of a drilldown or CSV.
     *
     * @param int $metadataId -- ebi_metadata_id or org_metadata_id
     * @param string $metadataSource -- "ebi" or "isp", depending on the source of the profile item
     * @param int $orgAcademicYearId -- for year-specific profile items
     * @param int $orgAcademicTermId -- for term-specific profile items
     * @return string
     */
    private function getItemText($metadataId, $metadataSource, $orgAcademicYearId, $orgAcademicTermId)
    {
        if ($metadataSource == 'isp') {
            $orgMetadata = $this->orgMetadataRepository->find($metadataId);
            $metaDescription = $orgMetadata->getMetaDescription();
            $metaName = $orgMetadata->getMetaName();
            $metaKey = $orgMetadata->getMetaKey();
            $scope = $orgMetadata->getScope();
            $profileItemText = $this->formatItemText($metadataSource, null, $metaDescription, $metaName, $metaKey, $scope, $orgAcademicYearId, $orgAcademicTermId);
        } else {
            $ebiMetadata = $this->ebiMetadataRepository->find($metadataId);
            $scope = $ebiMetadata->getScope();
            $metaKey = $ebiMetadata->getKey();
            $metaDescription = $this->ebiMetadataLangRepository->findOneBy(['ebiMetadata' => $metadataId])->getMetaDescription();
            $datablock = $this->datablockMetadataRepository->findOneBy(['ebiMetadata' => $metadataId])->getDatablock()->getId();
            $datablockDesc = $this->datablockMasterLangRepository->findOneBy(['datablock' => $datablock])->getDatablockDesc();
            $profileItemText = $this->formatItemText($metadataSource, $datablockDesc, $metaDescription, null, $metaKey, $scope, $orgAcademicYearId, $orgAcademicTermId);
        }

        return $profileItemText;
    }


    /**
     * Returns an array of person_ids and the corresponding profile item values for the given profile item
     * for students who are included in the report and for whom the given profile item is accessible.
     * Note: The user may not have individual access to these students.
     *
     * @param int $loggedInUserId
     * @param int $reportInstanceId -- id from the reports_running_status table
     * @param int $metadataId -- ebi_metadata_id or org_metadata_id
     * @param string $metadataSource -- "ebi" or "isp", depending on the source of the profile item
     * @param int $orgAcademicYearId -- for year-specific profile items
     * @param int $orgAcademicTermId -- for term-specific profile items
     * @param int $optionValue -- for Categorical items, drilldown into a particular option
     * @param int|float $optionMin -- for Numeric items, minimum of the histogram bin to drill into
     * @param int|float $optionMax -- for Numeric items, maximum of the histogram bin to drill into
     * @return array
     */
    private function getProfileItemValuesForAccessibleStudents($loggedInUserId, $reportInstanceId, $metadataId, $metadataSource, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax)
    {
        $yearIdArray = $orgAcademicYearId ? [$orgAcademicYearId] : [];
        $termIdArray = $orgAcademicTermId ? [$orgAcademicTermId] : [];

        // Get an array of all the students selected for this report via the preliminary filters.
        $reportInstance = $this->reportsRunningStatusRepository->find($reportInstanceId);
        $filteredStudentIds = explode(',', $reportInstance->getFilteredStudentIds());

        // Get a list of students whose data should be accessible for the given profile item.
        // Then intersect with the filtered students to get the students potentially available in the drilldown.
        // Note: These students may not actually be individually accessible because of aggregate permission sets.
        // The access level for these students needs to be handled later, after we know how many actually have profile item values.
        $accessibleStudents = $this->getAccessibleStudentsForProfileItem($loggedInUserId, $metadataSource, $metadataId);
        $filteredStudentsInGroupsAndCourses = array_intersect($filteredStudentIds, $accessibleStudents);

        // Get values for the given profile item for the list of students we just found.
        // This is a preliminary query to narrow down the list of students to only those who have a value for this profile item.
        // It's needed to get an accurate count for the aggregation restriction.
        if ($metadataSource == 'isp') {
            $profileItemValues = $this->personOrgMetadataRepository->getMetadataValuesByOrgMetadataAndStudentIds($metadataId, $filteredStudentsInGroupsAndCourses, $yearIdArray, $termIdArray, $optionValue, $optionMin, $optionMax);
        } else {
            $profileItemValues = $this->personEbiMetadataRepository->getMetadataValuesByEbiMetadataAndStudentIds($metadataId, $filteredStudentsInGroupsAndCourses, $yearIdArray, $termIdArray, $optionValue, $optionMin, $optionMax);
        }

        return $profileItemValues;
    }


    /**
     * Returns a list of ids and first and last names for the students who have accessible profile item values for the given profile item,
     * formatted as expected by the front end so it can use this list to populate the bulk action modal.
     *
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param int $reportInstanceId -- id from the reports_running_status table
     * @param int $metadataId -- ebi_metadata_id or org_metadata_id
     * @param string $metadataSource -- "ebi" or "isp", depending on the source of the profile item
     * @param int $orgAcademicYearId -- for year-specific profile items
     * @param int $orgAcademicTermId -- for term-specific profile items
     * @param int $optionValue -- for Categorical items, drilldown into a particular option
     * @param int|float $optionMin -- for Numeric items, minimum of the histogram bin to drill into
     * @param int|float $optionMax -- for Numeric items, maximum of the histogram bin to drill into
     * @return array
     */
    public function getStudentIdsAndNames($loggedInUserId, $organizationId, $reportInstanceId, $metadataId, $metadataSource, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax)
    {
        // Find the ids of all students who should be included.
        $profileItemValues = $this->getProfileItemValuesForAccessibleStudents($loggedInUserId, $reportInstanceId, $metadataId, $metadataSource, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax);
        $studentsWhoHaveProfileItemValues = array_column($profileItemValues, 'person_id');
        $individuallyAccessibleParticipants = $this->reportDrilldownService->getIndividuallyAccessibleParticipants($loggedInUserId, $organizationId, $studentsWhoHaveProfileItemValues);

        // Add in the names of these students.
        $dataToReturn = $this->studentListService->getStudentIdsAndNames($individuallyAccessibleParticipants, $loggedInUserId);

        return $dataToReturn;
    }


    /**
     * Creates a CSV with a single column, filled with profile item values for accessible students included in the report.
     * This CSV is downloaded from the main report, not from the drilldown, and can include values for students that the user only has aggregate access to.
     *
     * Returns an array containing the file name, with key "file_name".
     * ToDo: Determine whether we can simplify this to just return the file name.
     *
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param int $reportInstanceId -- id from the reports_running_status table
     * @param int $metadataId -- ebi_metadata_id or org_metadata_id
     * @param string $metadataSource -- "ebi" or "isp", depending on the source of the profile item
     * @param int $orgAcademicYearId -- for year-specific profile items
     * @param int $orgAcademicTermId -- for term-specific profile items
     * @param int $optionValue -- for Categorical items, drilldown into a particular option
     * @param int|float $optionMin -- for Numeric items, minimum of the histogram bin to drill into
     * @param int|float $optionMax -- for Numeric items, maximum of the histogram bin to drill into
     * @return array
     */
    public function getProfileItemValuesInCSV($loggedInUserId, $organizationId, $reportInstanceId, $metadataId, $metadataSource, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax)
    {
        $profileItemValues = $this->getProfileItemValuesForAccessibleStudents($loggedInUserId, $reportInstanceId, $metadataId, $metadataSource, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax);

        $studentsWhoHaveProfileItemValues = array_column($profileItemValues, 'person_id');

        // Throw an exception if there are no students with the profile item.
        if (count($studentsWhoHaveProfileItemValues) == 0) {
            throw new AccessDeniedException('No data for this profile item and these students.');
        }

        // Format the file name and path.
        $currentDateTime = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, 'Ymd_His');
        $filePath = SynapseConstant::S3_ROOT . ReportsConstants::S3_REPORT_CSV_EXPORT_DIRECTORY . '/';
        $fileName = "$organizationId-profile_snapshot_report_$currentDateTime.csv";

        $profileItemText = $this->getItemText($metadataId, $metadataSource, $orgAcademicYearId, $orgAcademicTermId);

        $preliminaryRows = [
            ['Profile Snapshot Report'],
            [$profileItemText],
            ['']
        ];

        $columnHeaders = [
            'metadata_value' => 'Profile Item Value'
        ];

        $this->csvUtilityService->generateCSV($filePath, $fileName, $profileItemValues, $columnHeaders, $preliminaryRows);

        $response = [];
        $response['file_name'] = $fileName;
        return $response;
    }


    /**
     * Creates a CSV with data from the drilldown into a profile item,
     * including students' names, external ids, emails, risk levels, class levels, and profile item values.
     *
     * Returns an array containing the file name, with key "file_name".
     * ToDo: Determine whether we can simplify this to just return the file name.
     * ToDo: Determine if there should be a job to create this CSV.
     *
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param int $reportInstanceId -- id from the reports_running_status table
     * @param int $metadataId -- ebi_metadata_id or org_metadata_id
     * @param string $metadataSource -- "ebi" or "isp", depending on the source of the profile item
     * @param string $sortBy -- column to sort by: "risk_color" or "name" or "class_level" or "profile_item_value", possibly preceded by "-" or "+" to indicate sort direction
     * @param int $orgAcademicYearId -- for year-specific profile items
     * @param int $orgAcademicTermId -- for term-specific profile items
     * @param int $optionValue -- for Categorical items, drilldown into a particular option
     * @param int|float $optionMin -- for Numeric items, minimum of the histogram bin to drill into
     * @param int|float $optionMax -- for Numeric items, maximum of the histogram bin to drill into
     * @return array
     */
    public function getDrilldownCSV($loggedInUserId, $organizationId, $reportInstanceId, $metadataId, $metadataSource, $sortBy, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax)
    {
        // For term-specific items, a year may not have been passed in but is needed in the item text.
        if (!is_null($orgAcademicTermId) && is_null($orgAcademicYearId)) {
            $orgAcademicYearId = $this->orgAcademicTermsRepository->find($orgAcademicTermId)->getOrgAcademicYear()->getId();
        }

        // Find all students that should be included in the CSV.
        $profileItemValues = $this->getProfileItemValuesForAccessibleStudents($loggedInUserId, $reportInstanceId, $metadataId, $metadataSource, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax);
        $studentsWhoHaveProfileItemValues = array_column($profileItemValues, 'person_id');
        $individuallyAccessibleParticipants = $this->reportDrilldownService->getIndividuallyAccessibleParticipants($loggedInUserId, $organizationId, $studentsWhoHaveProfileItemValues);

        // Get all the data needed, including student names, risk, class levels, and profile item values.
        $profileDrilldownRecords = $this->getProfileDrilldownRecords($loggedInUserId, $individuallyAccessibleParticipants, $metadataSource, $metadataId, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax, $sortBy);

        // Format the file name and path.
        $currentDateTime = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, 'Ymd_His');
        $filePath = SynapseConstant::S3_ROOT . ReportsConstants::S3_REPORT_CSV_EXPORT_DIRECTORY . '/';
        $fileName = "$organizationId-profile_snapshot_report_$currentDateTime.csv";

        $profileItemText = $this->getItemText($metadataId, $metadataSource, $orgAcademicYearId, $orgAcademicTermId);

        $preliminaryRows = [
            ['Profile Snapshot Report'],
            [$profileItemText],
            ['']
        ];

        $columnHeaders = [
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'external_id' => 'External Id',
            'username' => 'Email',
            'risk_color' => 'Risk',
            'class_level' => 'Class Level',
            'profile_item_value' => 'Profile Item Value'
        ];

        $this->csvUtilityService->generateCSV($filePath, $fileName, $profileDrilldownRecords, $columnHeaders, $preliminaryRows);

        $response = [];
        $response['file_name'] = $fileName;
        return $response;
    }

}
