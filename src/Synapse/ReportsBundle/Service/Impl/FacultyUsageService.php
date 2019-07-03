<?php
namespace Synapse\ReportsBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\OrganizationlangService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\Job\ReportJob;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\RestBundle\Exception\ValidationException;


/**
 * @DI\Service("faculty_usage_service")
 */
class FacultyUsageService extends AbstractService
{

    const SERVICE_KEY = 'faculty_usage_service';

    /**
     *
     * @var AlertNotificationsService
     */
    private $alertNotificationService;

    /**
     *
     * @var Container
     */
    private $container;

    /**
     *
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     *
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     *
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     *
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     *
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     *
     * @var OrganizationlangService
     */
    private $organizationLangService;

    /**
     *
     * @var PersonService
     */
    private $personService;

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     *
     * @var ReportsRunningStatusRepository
     */
    private $reportRunningStatusRepository;

    /**
     *
     * @var ReportsRepository
     */
    private $reportsRepository;

    /**
     *
     * @var TeamMembersRepository
     */
    private $teamMembersRepository;


    /**
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        //Services
        $this->alertNotificationService = $this->container->get('alertNotifications_service');
        $this->organizationLangService = $this->container->get('organizationlang_service');
        $this->personService = $this->container->get('person_service');
        $this->resque = $this->container->get('bcc_resque.resque');
        $this->serializer = $this->container->get('jms_serializer');

        //Repositories
        $this->reportRunningStatusRepository = $this->repositoryResolver->getRepository('SynapseReportsBundle:ReportsRunningStatus');
        $this->organizationRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Organization');
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgAcademicYear');
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupFaculty');
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPersonFaculty');
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrganizationRole');
        $this->reportsRepository = $this->repositoryResolver->getRepository("SynapseReportsBundle:Reports");
        $this->teamMembersRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:TeamMembers');
    }


    /**
     * Initializes the Report Job for Faculty Usage Report and does some initial validation
     *
     * @param integer $reportInstanceId
     * @param ReportRunningStatusDto $reportRunningDto
     */
    public function initiateReportJob($reportInstanceId, $reportRunningDto)
    {
        $personId = $reportRunningDto->getPersonId();
        $personObject = $this->personService->find($personId);
        $organizationId = $personObject->getOrganization()->getId();
        $coordinatorObject = $this->organizationRoleRepository->findOneBy(array(
            'organization' => $organizationId,
            'person' => $personId
        ));

        $searchAttributes = $reportRunningDto->getSearchAttributes();

        if (count($searchAttributes) == 0) {
            $currentDate = new \DateTime('now');
            $currentDateString = $currentDate->format('Y-m-d H:i:s');
            $currentOrPreviousAcademicYear = $this->orgAcademicYearRepository->getCurrentOrPreviousAcademicYearUsingCurrentDate($currentDateString, $organizationId);

            if (empty($currentOrPreviousAcademicYear)) {
                $message = "Academic years have not added for your institution. Academic years have to be created in order to run this report.";
                $key = "No_Academic_Year";
                throw new ValidationException([
                    $message
                ], $message, $key);
            }
        }

        // Check for team Leader
        $teamLeaderObject = $this->teamMembersRepository->findOneBy(array(
            'isTeamLeader' => 1,
            'person' => $personId,
            'organization' => $organizationId
        ));

        if (empty($coordinatorObject) && empty($teamLeaderObject)) {
            throw new ValidationException([
                "Not Authorized to generate report. You must be a coordinator or team leader"
            ], "Not Authorized to generate report. You must be a coordinator or team leader", "Not Authorized to generate report. You must be a coordinator or team leader");
        }

        $reportService = 'faculty_usage_service';

        $job = new ReportJob();
        $job->args = array(
            'reportInstanceId' => $reportInstanceId,
            'reportRunningDto' => serialize($reportRunningDto),
            'service' => $reportService
        );
        $this->resque->enqueue($job, true);
    }


    /**
     * Called by the Job, generates the Faculty Usage Report
     *
     * @param integer $reportInstanceId
     * @param ReportRunningStatusDto $reportRunningDto
     * @return mixed
     */
    public function generateReport($reportInstanceId, $reportRunningDto)
    {
        $organizationId = $reportRunningDto->getOrganizationId();

        $runningStatusObject = $this->reportRunningStatusRepository->find($reportRunningDto->getId());

        $organizationDetails = $this->organizationLangService->getOrganization($organizationId);

        $organizationObject = $this->organizationRepository->find($organizationId);
        if ($organizationObject) {
            $organizationImage = $organizationObject->getLogoFileName();
        } else {
            $organizationImage = null;
        }

        $personId = $reportRunningDto->getPersonId();
        $personObject = $this->personService->find($personId);

        // check if coordinator or team leader
        $organizationId = $personObject->getOrganization()->getId();
        $coordinatorObject = $this->organizationRoleRepository->findOneBy(array(
            'organization' => $organizationId,
            'person' => $personId
        ));

        $searchAttributes = $reportRunningDto->getSearchAttributes();

        //Initialize Search Attributes if they have not been set (Default)
        if (!is_array($searchAttributes) || count($searchAttributes) == 0) {

            $searchAttributes['academic_year']['start_date'] = '';
            $searchAttributes['academic_year']['end_date'] = '';
            $searchAttributes['group_ids'] = '';
            $searchAttributes['team_ids'] = '';
        }

        //If the search attributes are not the default of blank, set our date variables
        if ((!empty($searchAttributes['academic_year']['start_date'])) && (!empty($searchAttributes['academic_year']['end_date']))) {

            $startDate = $searchAttributes['academic_year']['start_date'];
            $endDate = $searchAttributes['academic_year']['end_date'];
            $academicYearsWithinDateRange = $this->orgAcademicYearRepository->getAcademicYearsWithinSpecificDateRange($organizationId, $startDate, $endDate);
            $academicYearIds = array_unique(array_column($academicYearsWithinDateRange, 'org_academic_year_id'));

        } else {
            //Else if we have blank default search attributes, find last academic year and supply date range
            $currentDateTime = new \DateTime('now');
            $currentDateTimeString = $currentDateTime->format('Y-m-d H:i:s');
            $currentOrPreviousAcademicYear = $this->orgAcademicYearRepository->getCurrentOrPreviousAcademicYearUsingCurrentDate($currentDateTimeString, $organizationId);

            //Beginning date should be starting date of the Academic Year by default
            //Else should not be used unless there are no academic years
            $startDate = '';
            if (isset($currentOrPreviousAcademicYear[0]['start_date'])) {
                $startDate = $currentOrPreviousAcademicYear[0]['start_date'];
            }
            $searchAttributes['academic_year']['start_date'] = $startDate;

            //Faculty Staff Usage is always ends on the current date by default
            $endDate = date('Y-m-d');
            $searchAttributes['academic_year']['end_date'] = $endDate;
            $currentAcademicYear = $this->orgAcademicYearRepository->getCurrentAcademicYear($organizationId);
            $academicYearIds[] = $currentAcademicYear['org_academic_year_id'];

            //Participating value set
            $searchAttributes['participating']['participating_value'] = [1];
            $searchAttributes['participating']['org_academic_year_id'] = [$currentAcademicYear['org_academic_year_id']];

        }

        $teamIds = [];
        if (empty($coordinatorObject) && empty($searchAttributes['team_ids']) && empty($searchAttributes['group_ids'])) {
            //check to see if person is a team Leader
            $teamMemberLeaderObjects = $this->teamMembersRepository->findBy(array(
                'isTeamLeader' => 1,
                'person' => $personId,
                'organization' => $organizationId
            ));

            //Retrieving all teams where the person is team leader
            if (count($teamMemberLeaderObjects) > 0) {
                $teamIds = [];
                foreach ($teamMemberLeaderObjects as $teamMemberLeaderObject) {
                    $teamIds[] = $teamMemberLeaderObject->getTeamId()->getId();
                }

            }

            $searchAttributes['team_ids'] = implode(",", $teamIds);
        }

        //Getting type by inference from set/unset search attributes
        if (!empty($searchAttributes['team_ids']) && !empty($searchAttributes['group_ids'])) {
            $type = "both";
        } elseif (!empty($searchAttributes['group_ids'])) {
            $type = "group";
        } elseif (!empty($searchAttributes['team_ids'])) {
            $type = "team";
        } else {
            $type = "all";
        }

        //Retrieving Faculty List Based on type determined above
        $facultyIdsArray = $this->getFacultyList($type, $searchAttributes, $organizationId);
        $resultSet = [];
        if (empty($facultyIdsArray)) {
            $reportData['status_message'] = [
                'code' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_CODE,
                'description' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_MESSAGE
            ];
        } else {

            //Retrieve Usage of Faculty Within Specified dates
            $resultSet = $this->reportsRepository->getFacultyUsage($organizationId, $facultyIdsArray, $startDate, $endDate, $academicYearIds);

            if (empty($resultSet)) {
                $reportData['status_message'] = [
                    'code' => ReportsConstants::REPORT_NO_DATA_CODE,
                    'description' => ReportsConstants::REPORT_NO_DATA_MESSAGE
                ];
            }

            $numericColumns = array(
                'student_connected',
                'contacts_student_count',
                'contacted_student_percentage',
                'interaction_contact_student_count',
                'interaction_contact_student_percentage',
                'reports_viewed_student_count',
                'reports_viewed_student_percentage',
                'notes_count',
                'referrals_count',
                'days_login'
            );

            //Checking for missing entries and replacing them with zero
            foreach ($resultSet as $rowIndex => $resultSetRow) {
                foreach ($resultSetRow as $columnHeader => $columnValue) {
                    if (!is_null($columnValue) && is_numeric($columnValue)) {
                        $resultSetRow[$columnHeader] = (float)$columnValue;
                        //If the column belongs to the numeric Columns
                        //but doesn't have a value, supply zero
                    } elseif (in_array($columnHeader, $numericColumns)) {
                        $resultSetRow[$columnHeader] = 0;
                    }

                }
                $resultSet[$rowIndex] = $resultSetRow;
            }

            //Find last login and reformat the date in UTC time
            foreach ($resultSet as $rowIndex => $resultSetRow) {

                if (isset($resultSetRow['last_login'])) {
                    $resultSetRow['last_login'] = new \DateTime($resultSetRow['last_login'], new \DateTimeZone('UTC'));
                    $resultSetRow['last_login'] = $resultSetRow['last_login']->format('Y-m-d\TH:i:sO');
                    $resultSet[$rowIndex] = $resultSetRow;
                }
            }
        }

        $campusInfo = array(
            'campus_id' => $organizationId,
            'campus_name' => $organizationDetails['name'],
            'campus_logo' => $organizationImage
        );

        $reportSections = $reportRunningDto->getReportSections();
        $reportGeneratedTime = $runningStatusObject->getCreatedAt();
        $reportGeneratedTimestamp = $reportGeneratedTime->getTimestamp();
        $reportInfo = array(
            'report_id' => $reportSections['reportId'],
            'report_name' => $reportSections['report_name'],
            'short_code' => $reportSections['short_code'],
            'report_instance_id' => $reportInstanceId,
            'report_date' => date(SynapseConstant::DATE_FORMAT_WITH_TIMEZONE, $reportGeneratedTimestamp),
            'report_start_date' => $startDate,
            'report_end_date' => $endDate,
            'report_by' => array(
                'first_name' => $personObject->getFirstname(),
                'last_name' => $personObject->getLastname()
            )
        );

        $reportData['id'] = $reportRunningDto->getId();
        $reportData['organization_id'] = $organizationId;
        $reportData['report_id'] = $reportSections['reportId'];
        $reportData['report_sections'] = $reportSections;
        $reportData['search_attributes'] = $searchAttributes;
        $reportData['campus_info'] = $campusInfo;
        $reportData['request_json'] = $reportRunningDto;
        $reportData['report_info'] = $reportInfo;
        $reportData['report_data'] = $resultSet;

        $reportJSON = $this->serializer->serialize($reportData, 'json');

        $runningStatusObject->setResponseJson($reportJSON);
        $runningStatusObject->setStatus('C');
        $this->reportRunningStatusRepository->update($runningStatusObject);
        $this->reportRunningStatusRepository->flush();

        $reportId = $runningStatusObject->getId();
        $url = "/reports/FUR/$reportId";
        $this->alertNotificationService->createNotification("FUR", $reportSections['report_name'], $personObject, null, null, null, $url, null, null, null, 1, $runningStatusObject);
        return $reportData;
    }


    /**
     * Switch for which type of faculty list will be retrieved
     *
     * @param string $type : all|group|team|both
     * @param array $searchAttributes
     * @param integer $orgId
     * @return array
     */
    public function getFacultyList($type, $searchAttributes, $orgId)
    {

        $reportedFaculty = [];
        switch ($type) {

            case "all":
                $reportedFaculty = $this->orgPersonFacultyRepository->getAllFacultiesForOrg($orgId);

                break;
            case "both":
                $groupIds = explode(",", $searchAttributes['group_ids']);
                $teamIds = explode(",", $searchAttributes['team_ids']);
                $reportedFaculty = $this->reportsRepository->getFacultyIdsInAnIntersectionBetweenTeamsAndGroups($orgId, $groupIds, $teamIds);
                break;
            case "team":
                $teamIds = explode(",", $searchAttributes['team_ids']);
                $reportedFaculty = $this->teamMembersRepository->getFacultyTeamMembers($orgId, $teamIds);
                break;
            case "group":
                $groupIds = explode(",", $searchAttributes['group_ids']);
                $reportedFaculty = $this->orgGroupFacultyRepository->getGroupFaculty($orgId, $groupIds);
                break;
        }
        $facultyIdArray = array();

        foreach ($reportedFaculty as $faculty) {
            $facultyIdArray[] = $faculty['faculty_id'];
        }

        return $facultyIdArray;
    }

}
