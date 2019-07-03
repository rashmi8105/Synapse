<?php
namespace Synapse\ReportsBundle\Job;


use PhpOption\Tests\Repository;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Repository\SearchRepository;
use Synapse\CoreBundle\Service\Impl\OrganizationlangService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\ReportsBundle\Service\Impl\ActivityReportService;
use Synapse\ReportsBundle\Service\Impl\ReportsService;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\SearchBundle\Service\Impl\SearchService;

class ActivityReportJob extends ReportsJob
{

    //Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;


    // Class level variables

    private $activityCounts = [];

    // Services

    /**
     * @var ActivityReportService
     */
    private $activityReportService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var ReportsService
     */
    private $reportService;

    /**
     * @var SearchService
     */
    private $searchService;

    // Repositories

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var ReportsRunningStatusRepository
     */
    private $reportRunningStatusRepository;

    /**
     * @var SearchRepository
     */
    private $searchRepository;


    private function generateActivityOverviewJSON($queryResult ,$totalStudents)
    {
        $section['section_id'] = 3;
        $section['title'] = "Activity Overview";


        $finalActivityLoggedArr = array();
        foreach ($queryResult as $result) {

            if($result['element_type'] == "NIC"){
                continue;
            }

            switch ($result['element_type']) {
                case "N":
                    $type = "notes";
                    break;
                case "R":
                    $type = "referrals";
                    break;
                case "A":
                    $type = "appointments";
                    break;
                case "C":
                    $type = "contacts";
                    break;
                case "IC":
                    $type = "interaction-contacts";
                    break;
                case "E":
                    $type = "email";
                    break;
                case "AU":
                    $type = "academic-updates";
                    break;
                default:
                    continue;
            }

            $activityLoggedArr = array();
            $studentsInvolved = array();
            $studentsArr = array();
            $staffLogged = array();
            $staffReceived = array();

            $activityLoggedArr['value'] = $result['activity_count'];
            $studentsInvolved['value'] = $result['student_count'];
            $studentsArr['value'] = round(($result['student_count'] /$totalStudents) * 100, 1);
            $staffLogged['value'] = $result['faculty_count'];
            $staffReceived['value'] = $result['received_referrals'];

            $activityLoggedArr['id'] = $staffReceived['id'] = $staffLogged['id'] = $studentsArr['id'] = $studentsInvolved['id'] = $type;
            $finalActivityLoggedArr[] = $activityLoggedArr;

            $finalStaffReceived[] = $staffReceived;
            $finalStaffLogged[] = $staffLogged;
            $finalStudentsArr[] = $studentsArr;
            $finalStudentsInvolved[] = $studentsInvolved;


        }

        $finalJsonArr = array(
            "section_id" => 3,
            "title" => "Activity Overview",
            "value" => "",
            "elements" => array(
                array(
                    'element_id' => 6,
                    'title' => "# of activities logged",
                    'value' => $finalActivityLoggedArr
                ),
                array(
                    'element_id' => 7,
                    'title' => "# of students involved",
                    'value' => $finalStudentsInvolved
                ),
                array(
                    'element_id' => 8,
                    'title' => "% of students",
                    'value' => $finalStudentsArr
                ),
                array(
                    'element_id' => 9,
                    'title' => "# of Faculty/Staff logged",
                    'value' => $finalStaffLogged
                ),
                array(
                    'element_id' => 10,
                    'title' => "# of Faculty/Staff received",
                    'value' => $finalStaffReceived
                )
            )

        );

        return $finalJsonArr;
    }

    private function generateActivityByCategoryJSON($queryResult)
    {
        $finalArr = array();
        foreach ($queryResult as $result) {
            $reArr = array();
            $reArr['value'] = $result['activity_count'];

            switch ($result['activity_type']) {
                case "A":
                    $type = "appointments";
                    break;
                case "E":
                    $type = "email";
                    break;
                case "C":
                    $type = "contacts";
                    break;
                case "IC":
                    $type = "interaction-contacts";
                    break;					
                case "N":
                    $type = "notes";
                    break;
                case "R":
                    $type = "referrals";
                    break;
            }
            $reArr['id'] = $type;
            $finalArr[$result['element_type']][] = $reArr;
        }
        $finalJsonArr = array(
            "section_id" => 4,
            "title" => "Categories",
            "value" => ""
        );
        foreach ($finalArr as $key => $final) {
            $finalJsonArr['elements'][] = array(
                'element_id' => $key,
                "title" => $key,
                "value" => $final
            );
        }
        return $finalJsonArr;
    }

    private function generateTopActivitiesJSON($queryResult)
    {
        $attributeMap = array(
            'element_id' => 'activity_type',
            'value' => 'total_count'
        );

        $section = $this->convertQueryResultToJSON($queryResult, 'Top Activities', $attributeMap);
        $section['section_id'] = 101;
        
        return $section;
    }

    private function generateFacultyStaffJSON($queryResult)
    {
        $attributeMap = array(
            'element_id' => 'element_type',
            'value' => 'total_staff'
        );
        $totalFaculty =  null;
        foreach ($queryResult as $facRes) {
        	if ($facRes['element_type'] == "total") {
        		$totalFaculty = $facRes['total_staff'];
        		break;
        	}
        }
        
        $section = $this->convertQueryResultToJSON($queryResult, 'Faculty/Staff', $attributeMap, $totalFaculty);
        $section['section_id'] = 1;
        
        return $section;
    }

    private function generateStudentJSON($queryResult)
    {
        $attributeMap = array(
            'element_id' => 'element_type',
            'value' => 'total_students'
        );
        $totalStudents = null;
        foreach ($queryResult as $rec) {
        	if ($rec['element_type'] == "total") {
        		$totalStudents = $rec['total_students'];
        		break;
        	}
        }
        $section = $this->convertQueryResultToJSON($queryResult, 'Students', $attributeMap ,$totalStudents);
        $section['section_id'] = 2;
        
        return $section;
    }

    private function generateReferralsJSON($queryResult)
    {
        $transposeMap = array(
            'total_referrals' => 'element_id',
            'discussed_count' => 'element_id',
            'intent_to_leave_count' => 'element_id',
            'high_priority_concern_count' => 'element_id',
            'number_open_count' => 'element_id',
            'open_count' => 'element_id',
        );
        $totalValue = $queryResult[0]['total_referrals'];
        if (! is_numeric($totalValue)) {
            $totalValue = 0;
        }
        $section = $this->transposeQueryResultAsMapToJSON($queryResult, 'Referrals', $transposeMap, $totalValue);
        $section['section_id'] = 5;
        return $section;
    }

    private function generateAppointmentsJSON($queryResult)
    {
        $transposeMap = array(
            'total_appointments' => 'element_id',
            'completed_count' => 'element_id',
            'staff_initiated_count' => 'element_id',
            'student_initiated_count' => 'element_id'
        );
        $totalValue = $queryResult[0]['total_appointments'];
        if (! is_numeric($totalValue)) {
            $totalValue = 0;
        }
        
        $section = $this->transposeQueryResultAsMapToJSON($queryResult, 'Appointments', $transposeMap, $totalValue);
        $section['section_id'] = 6;
        
        return $section;
    }

    private function generateContactsJSON($queryResult)
    {
        $transposeMap = array(
            'total_contacts' => 'element_id',
            'interaction_contacts_count' => 'element_id',
            'non_interaction_contacts_count' => 'element_id'
        );
        
        $totalValue = $queryResult[0]['total_contacts'];
        if (! is_numeric($totalValue)) {
            $totalValue = 0;
        }
        
        $section = $this->transposeQueryResultAsMapToJSON($queryResult, 'Contacts', $transposeMap, $totalValue);
        $section['section_id'] = 7;
        
        return $section;
    }

    private function generateAcademicUpdatesJSON($queryResult)
    {
        $transposeMap = array(
            'total_au' => 'element_id',
            'failure_risk_level_count' => 'element_id',
            'grade_df_count' => 'element_id',
            'request_count' => 'element_id',
            'request_closed_count' => 'element_id'
        );
        
        $totalValue = $queryResult[0]['total_au'];
        if (! is_numeric($totalValue)) {
            $totalValue = 0;
        }
        $section = $this->transposeQueryResultAsMapToJSON($queryResult, 'Academic Updates', $transposeMap, $totalValue);
        $section['section_id'] = 11;
        
        return $section;
    }

    private function generateReferralCategoriesJSON($queryResult)
    {
        $attributeMap = array(
            'element_id' => 'element_type',
            'value' => 'activity_count'
        );
        
        $section = $this->convertQueryResultToJSON($queryResult, 'Referral Categories', $attributeMap);
        $section['section_id'] = 8;
        
        return $section;
    }

    private function generateAppointmentCategoriesJSON($queryResult)
    {
        $attributeMap = array(
            'element_id' => 'element_type',
            'value' => 'activity_count'
        );
        
        $section = $this->convertQueryResultToJSON($queryResult, 'Appointment Categories', $attributeMap);
        $section['section_id'] = 9;
        
        return $section;
    }

    private function generateContactCategoriesJSON($queryResult)
    {
        $attributeMap = array(
            'element_id' => 'element_type',
            'value' => 'activity_count'
        );
        
        $section = $this->convertQueryResultToJSON($queryResult, 'Contact Categories', $attributeMap);
        $section['section_id'] = 10;

        return $section;
    }

    private function convertQueryResultToJSON($queryResult, $title, $attributeMap,$value =  null)
    {
        
        // echo "Converting to JSON...\n";
        $section = array();
        
        $section['title'] = $title;
        if (! is_null($value) && is_numeric($value)) {
        	$section['value'] = $value;
        }
        
        $elements = array();
        
        foreach ($queryResult as $row) {
            
            $element = array();
            
            foreach ($row as $column => $value) {
                
                if (isset($attributeMap)) {
                    
                    $key = array_search($column, $attributeMap);
                    
                    if (isset($key)) {
                        
                        $column = $key;
                    }
                }
                
                $element[$column] = $value;
            }
            
            $elements[] = $element;
        }
        
        $section['elements'] = $elements;
        
        return $section;
    }

    private function transposeQueryResultAsMapToJSON($queryResult, $title, $transposeMap, $value = null)
    {
        
        // echo "Transposing result as Map to JSON...\n";
        $section = array();
        
        $section['title'] = $title;
        if (! is_null($value) && is_numeric($value)) {
            $section['value'] = $value;
        }
        
        $elements = array();
        
        foreach ($queryResult as $row) {
            
            foreach ($row as $column => $value) {
                
                $element = array();
                
                $key = $column;
                
                if (isset($transposeMap)) {
                    
                    if (isset($transposeMap[$column])) {
                        
                        $key = $transposeMap[$column];
                    }
                }
                
                $element[$key] = $column;
                $element['value'] = $value;
                
                $elements[] = $element;
            }
        }
        
        $section['elements'] = $elements;
        
        // echo "JSON=\n" . json_encode( $section, JSON_PRETTY_PRINT) . "\n";
        return $section;
    }

    /**
     * Job to create Activity Report
     *
     * @param array $args
     * @return array | bool
     * @throws SynapseValidationException
     */
    public function run($args)
    {
        $this->container = $this->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);


        $this->activityReportService = $this->container->get(ActivityReportService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationlangService::SERVICE_KEY);
        $this->reportService = $this->container->get(ReportsService::SERVICE_KEY);
        $this->searchService = $this->container->get(SearchService::SERVICE_KEY);

        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->reportRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsRunningStatusRepository::REPOSITORY_KEY);
        $this->searchRepository = $this->repositoryResolver->getRepository(SearchRepository::REPOSITORY_KEY);
        $reportData = [];
        $totalStudents = [];

        $runningStatusObject = $this->reportRunningStatusRepository->find($args['reportInstanceId']);
        if (!$runningStatusObject) {
            $error = ['error' => 'Report running status not found'];
            $runningStatusObject->setResponseJson(json_encode($error));
            $runningStatusObject->setStatus('F');
            $this->reportRunningStatusRepository->update($runningStatusObject);

            $this->reportRunningStatusRepository->flush();
            return false;
        }
        $runningStatusObject->setStatus('IP');
        $this->reportRunningStatusRepository->update($runningStatusObject);
        $this->reportRunningStatusRepository->flush();

        $organizationId = $args['orgId'];
        $loggedUserId = $args['userId'];

        $organizationDetails = $this->organizationService->getOrganization($organizationId);

        $organizationImageObject = $this->organizationRepository->find($organizationId);
        if ($organizationImageObject) {
            $organizationImage = $organizationImageObject->getLogoFileName();
        } else {
            $error = ['error' => 'Organization Not Found'];
            $runningStatusObject->setResponseJson(json_encode($error));
            $runningStatusObject->setStatus('F');
            $this->reportRunningStatusRepository->update($runningStatusObject);

            $this->reportRunningStatusRepository->flush();
            return false;
        }

        $reportFilter = $runningStatusObject->getFilterCriteria();
        $reportFilter = json_decode($reportFilter, true);

        $reportSections = $args['reportSections'];

        $studentFilter = $reportFilter;

        $sql = 'SELECT DISTINCT id AS person_id FROM person p WHERE ';
        $sql .= $this->searchService->getStudentListBasedCriteria($reportFilter, $organizationId, $loggedUserId, '', true);
        $sql .= ' -- maxscale route to server slave2';
        $sql = $this->reportService->replacePlaceHolders($sql, $organizationId);

        $records = $this->searchRepository->getQueryResult($sql);

        if (empty($records)) {
            $reportData['status_message'] = [
                'code' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_CODE,
                'description' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_MESSAGE
            ];
        } else {

            $studentArray = array();
            foreach ($records as $record) {
                $studentArray[] = $record['person_id'];
            }

            if (count($studentArray) > 0) {
                $args['reporting_on_student_ids'] = implode($studentArray, ",");
            } else {
                $args['reporting_on_student_ids'] = '-1'; // report should not generate any data, if there are no selected students
            }

            if (isset($studentFilter['team_ids']) && !empty($studentFilter['team_ids'])) {
                $sql = 'SELECT DISTINCT person_id FROM team_members t WHERE t.organization_id = ' . $organizationId . ' AND t.teams_id IN (' . $reportFilter['team_ids'] . ')';
            } else {
                $sql = 'SELECT DISTINCT person_id FROM org_person_faculty opf WHERE opf.organization_id = ' . $organizationId . ' AND deleted_at IS NULL ';
            }

            $args['reporting_on_faculty_ids'] = $sql;

            $sections = array();

            $records = [];
            $sql = $this->activityReportService->getActivityReportSectionQueries('TopActivity');
            $sql = $this->activityReportService->replacePlaceHoldersInQuery($sql, $args);

            if (trim($sql) != "") {
                $records = $this->searchRepository->getQueryResult($sql);
            }

            $sections[] = $this->generateTopActivitiesJSON($records);

            $faculty = $this->activityReportService->getActivityReportSectionQueries('faculty');
            $faculty = $this->activityReportService->replacePlaceHoldersInQuery($faculty, $args);

            $facultyResults = $this->searchRepository->getQueryResult($faculty);

            foreach ($facultyResults as $facultyResultValue) {
                if ($facultyResultValue['element_type'] == "total") {
                    $totalFaculty = $facultyResultValue['total_staff'];
                    break;
                }
            }
            foreach ($facultyResults as $facultyResultKey => $facultyResultValue) {
                if ($facultyResultValue['element_type'] != "total") {
                    $facultyResults[$facultyResultKey]['total_staff'] = round(($facultyResultValue['total_staff'] / $totalFaculty) * 100, 1);
                }
            }

            $sections[] = $this->generateFacultyStaffJSON($facultyResults);

        $records = [];
        $sql = $this->activityReportService->getActivityReportSectionQueries('student');
        $sql = $this->activityReportService->replacePlaceHoldersInQuery($sql, $args);
        $totalStudents = [];

        if (trim($sql) != "") {
            $records = $this->searchRepository->getQueryResult($sql);
        }

            foreach ($records as $rec) {
                if ($rec['element_type'] == "total") {
                    $totalStudents = $rec['total_students'];
                    break;
                }
            }
            foreach ($records as $key => $rec) {
                if ($rec['element_type'] != "total") {
                    $records[$key]['total_students'] = round(($rec['total_students'] / $totalStudents) * 100, 1);
                }
            }

            //End of adding percent

            $sections[] = $this->generateStudentJSON($records);

            //ACTIVITY OVERVIEW SECTION
            $sections[] = $this->buildActivityOverviewSection($this->activityReportService, $this->searchRepository, $args, $totalStudents);

            $records = [];
            $sql = $this->activityReportService->getActivityReportSectionQueries('ActivityByCategory');
            $sql = $this->activityReportService->replacePlaceHoldersInQuery($sql, $args);

            if (trim($sql) != "") {
                $records = $this->searchRepository->getQueryResult($sql);
            }

            foreach ($records as $key => $record) {
                $records[$key]['activity_count'] = round(($record['activity_count'] / $this->activityCounts[$record['activity_type']]) * 100, 1);
            }

            $sections[] = $this->generateActivityByCategoryJSON($records);

            $records = [];
            $sql = $this->activityReportService->getActivityReportSectionQueries('referral');
            $sql = $this->activityReportService->replacePlaceHoldersInQuery($sql, $args);

            if (trim($sql) != "") {
                $records = $this->searchRepository->getQueryResult($sql);
            }

            foreach ($records[0] as $key => $val) {

                if ($key != "total_referrals") {

                    if ($key == "number_open_count") {
                        $records[0]["open_count"] = round(($records[0]['number_open_count'] / $records[0]['total_referrals']) * 100, 1);
                        $openReferralCount = $records[0]['number_open_count'];
                    }
                    $records[0][$key] = round(($val / $records[0]['total_referrals']) * 100, 1);

                }
            }

            $records = $this->generateReferralsJSON($records);

            foreach ($records['elements'] as $key => $value) {

                if ($value['element_id'] == 'number_open_count') {
                    $records['elements'][$key]['count'] = $openReferralCount;
                }
            }

            $sections[] = $records;

            $records = [];
            $sql = $this->activityReportService->getActivityReportSectionQueries('appointments');
            $sql = $this->activityReportService->replacePlaceHoldersInQuery($sql, $args);

            if (trim($sql) != "") {
                $records = $this->searchRepository->getQueryResult($sql);
            }

            foreach ($records[0] as $key => $val) {
                $val = (int)$val;
                if ($key != "total_appointments") {
                    $records[0][$key] = round(($val / $records[0]['total_appointments']) * 100, 1);
                }
            }

            //Temp fix for student initiated count
            $records[0]['student_initiated_count'] = 100 - $records[0]['staff_initiated_count'];

            if ($records[0]['total_appointments'] == 0 && $records[0]['staff_initiated_count'] == 0) {
                $records[0]['student_initiated_count'] = 0;
            }

            $sections[] = $this->generateAppointmentsJSON($records);

        $records = [];
        $sql = $this->activityReportService->getActivityReportSectionQueries('contacts');
        $sql = $this->activityReportService->replacePlaceHoldersInQuery($sql, $args);

        if (trim($sql) != "") {
            $records = $this->searchRepository->getQueryResult($sql);
        }

            foreach ($records[0] as $key => $val) {

                if ($key != "total_contacts") {
                    $records[0][$key] = round(($val / $records[0]['total_contacts']) * 100, 1);
                }
            }

            $sections[] = $this->generateContactsJSON($records);

        $records = [];
        $sql = $this->activityReportService->getActivityReportSectionQueries('Au');
        $sql = $this->activityReportService->replacePlaceHoldersInQuery($sql, $args);

        if (trim($sql) != "") {
            $records = $this->searchRepository->getQueryResult($sql);
        }

            foreach ($records[0] as $key => $val) {
                if ($key == "total_au" || $key == "request_count") {
                    continue;
                }

                if ($key == "request_closed_count") {
                    $records[0][$key] = round(($val / $records[0]['request_count']) * 100, 1);
                } elseif ($key == "student_involved") {
                    $records[0][$key] = round(($val / $totalStudents) * 100, 1);
                } elseif ($key == "faculty_logged") {
                    $records[0][$key] = round(($val / $totalFaculty) * 100, 1);
                } else {
                    $records[0][$key] = round(($val / $records[0]['total_au']) * 100, 1);
                }
            }
            // end of percent calculation for Au

            $records = $this->generateAcademicUpdatesJSON($records);
            foreach ($records['elements'] as $key => $record) {

                if ($record['element_id'] == "request_count") {
                    $records['elements'][$key]['value'] = "0";
                    $records['elements'][$key]['count'] = $record['value'];
                }
            }

            $sections[] = $records;

            $records = [];
            $sql = $this->activityReportService->getActivityReportSectionQueries('ReferralsByCategory');
            $sql = $this->activityReportService->replacePlaceHoldersInQuery($sql, $args);

            if (trim($sql) != "") {
                $records = $this->searchRepository->getQueryResult($sql);
            }

            foreach ($records as $key => $val) {
                $records[$key]['activity_count'] = round(($val['activity_count'] / $this->activityCounts['R']) * 100, 1);
            }

            $sections[] = $this->generateReferralCategoriesJSON($records);

            $records = [];
            $sql = $this->activityReportService->getActivityReportSectionQueries('AppointmentsByCategory');
            $sql = $this->activityReportService->replacePlaceHoldersInQuery($sql, $args);

            if (trim($sql) != "") {
                $records = $this->searchRepository->getQueryResult($sql);
            }

            foreach ($records as $key => $val) {
                $records[$key]['activity_count'] = round(($val['activity_count'] / $this->activityCounts['A']) * 100, 1);
            }

            $sections[] = $this->generateAppointmentCategoriesJSON($records);

            $records = [];
            $sql = $this->activityReportService->getActivityReportSectionQueries('ContactsByCategory');
            $sql = $this->activityReportService->replacePlaceHoldersInQuery($sql, $args);

        if (trim($sql) != "") {
            $records = $this->searchRepository->getQueryResult($sql);
        }

            foreach ($records as $key => $val) {
                $records[$key]['activity_count'] = round(($val['activity_count'] / $this->activityCounts['C']) * 100, 1);
            }

            $sections[] = $this->generateContactCategoriesJSON($records);

            if (empty($sections)) {
                $reportData['status_message'] = [
                    'code' => ReportsConstants::REPORT_NO_DATA_CODE,
                    'description' => ReportsConstants::REPORT_NO_DATA_MESSAGE
                ];
            }

            $reportData['sections'] = $sections;
        }

        $currentDateObject = new \DateTime('now');

        $reportInfo = array(
            'report_id' => $args['reportId'],
            'report_name' => $reportSections['report_name'],
            'short_code' => $reportSections['short_code'],
            'report_instance_id' => $args['reportInstanceId'],
            'report_date' => $currentDateObject->format('Y-m-d\TH:i:sO'),
            'report_start_date' => $args['start_date'],
            'report_end_date' => $args['end_date'],
            'students_count' => $totalStudents,
            'report_by' => array(
                'first_name' => $args['reportRunByFirstName'],
                'last_name' => $args['reportRunByLastName']
            )
        );

        $campusInfo = array(
            'campus_id' => $organizationId,
            'campus_name' => $organizationDetails['name'],
            'campus_logo' => $organizationImage
        );
        $reportJson = $args['requestJson'];

        $reportData['report_info'] = $reportInfo;
        $reportData['campus_info'] = $campusInfo;
        $reportData['search_filter'] = $studentFilter;
        $reportData['report_sections'] = $reportSections;
        $reportData['request_json'] = $reportJson;

        $runningStatusObject->setResponseJson(json_encode($reportData));
        $runningStatusObject->setStatus('C');
        $this->reportRunningStatusRepository->update($runningStatusObject);
        $this->reportRunningStatusRepository->flush();
        $this->sendEmail($runningStatusObject);

        return $reportData;
    }

    public function sendEmail($runningStatusObj)
    {
        $alertService = $this->getContainer()->get('alertNotifications_service');
        $shortCode = $runningStatusObj->getReports()->getShortCode();
        $reportName = $runningStatusObj->getReports()->getName();
        $person = $runningStatusObj->getPerson();
        $reportId = $runningStatusObj->getId();
        $url = "/reports/our-mapworks-activity/$reportId";
        $alertService->createNotification($shortCode, $reportName, $person, null, null, null, $url, null, null, null, 1, $runningStatusObj);
    }


    /**
     * Section Builder
     *
     * @param ActivityReportService $activityReportService
     * @param string $section
     * @param SearchRepository $searchRepository
     * @param array $args
     * @return array
     */
    public function getSectionData($activityReportService, $section, $searchRepository, $args){

        $records = [];
        $sql = $activityReportService->getActivityReportSectionQueries($section);

        //replaces parameters
        $sql = $activityReportService->replacePlaceHoldersInQuery($sql, $args);

        if (trim($sql) != "") {
            $records = $searchRepository->getQueryResult($sql);
        }

        /*
        * Added for storing the count of activites in an array , for finding out the percentage for later sections
        */

        foreach ($records as $record) {
            $this->activityCounts[$record['element_type']] = $record['activity_count'];
        }

        return $records;
    }


    /**
     * build Activity Overview Section
     *
     * @param ActivityReportService $activityReportService
     * @param SearchRepository $searchRepository
     * @param array $args (ActivityOverview section will include 'start_date', 'end_date', 'orgId', faculty ids, student ids)
     * @param int $totalStudentCount
     * @return array
     */

    public function buildActivityOverviewSection($activityReportService, $searchRepository, $args, $totalStudentCount){
        $sectionData = $this->getSectionData($activityReportService, 'ActivityOverview', $searchRepository, $args);
        $sectionResult = $this->generateActivityOverviewJSON($sectionData, $totalStudentCount);
        return $sectionResult;
    }
}