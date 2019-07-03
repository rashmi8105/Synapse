<?php

namespace Synapse\SearchBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\RiskBundle\Repository\RiskLevelsRepository;
use Synapse\SearchBundle\DAO\StudentListDAO;


/**
 * @DI\Service("student_list_service")
 */
class StudentListService extends AbstractService
{

    const SERVICE_KEY = 'student_list_service';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var StudentListDAO
     */
    private $studentListDAO;

    /**
     * @var EbiMetadataRepository
     */
    private $ebiMetadataRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

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
        $this->container = $container;

        // Services
        $this->dateUtilityService = $this->container->get('date_utility_service');

        // DAO
        $this->studentListDAO = $this->container->get('student_list_dao');

        // Repositories
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:EbiMetadata');
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPermissionset');
        $this->personRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Person');
        $this->riskLevelsRepository = $this->repositoryResolver->getRepository('SynapseRiskBundle:RiskLevels');
    }


    /**
     * Given a list of student ids, returns the requested page of the list, with additional data about each student,
     * as well as metadata about the list.
     *
     * @param array $studentIds
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $sortBy
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @return array
     */
    public function getStudentListWithMetadata($studentIds, $loggedInUserId, $organizationId, $sortBy, $pageNumber, $recordsPerPage)
    {
        if (!empty($studentIds)) {
            $records = $this->getStudentListWithAdditionalData($studentIds, $loggedInUserId, $organizationId, $sortBy, $pageNumber, $recordsPerPage);
        } else {
            $records = [];
        }

        $totalRecordCount = count($studentIds);

        $dataToReturn = [];
        $dataToReturn['person_id'] = $loggedInUserId;
        $dataToReturn['total_records'] = $totalRecordCount;
        $dataToReturn['records_per_page'] = $recordsPerPage;
        $dataToReturn['total_pages'] = ceil($totalRecordCount/$recordsPerPage);
        $dataToReturn['current_page'] = $pageNumber;
        $dataToReturn['search_result'] = $records;

        return $dataToReturn;
    }


    /**
     * Given a list of student ids, returns the requested page of the list, with additional data,
     * including name, external id, email, status, risk, intent to leave, class level, activity count, and date and type of last activity.
     * Applies risk and intent to leave permissions to ensure this data is not included about students if not permitted;
     * these students will be at the end of the list if sorting is done by risk or intent to leave.
     *
     * @param array $studentIds
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $sortBy
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @return array
     */
    public function getStudentListWithAdditionalData($studentIds, $loggedInUserId, $organizationId, $sortBy, $pageNumber, $recordsPerPage)
    {
        $classLevelEbiMetadataId = $this->ebiMetadataRepository->findOneBy(['key' => 'ClassLevel'])->getId();

        $timeZoneString = $this->dateUtilityService->getOrganizationISOTimeZone($organizationId);

        $riskPermission = $this->orgPermissionsetRepository->getRiskPermissionForFacultyAndStudents($loggedInUserId, $studentIds);
        $intentToLeavePermission = $this->orgPermissionsetRepository->getIntentToLeavePermissionForFacultyAndStudents($loggedInUserId, $studentIds);

        // These variables should always be set if this function is called, but let's set a default just in case.
        if (isset($pageNumber) && isset($recordsPerPage)) {
            $offset = $recordsPerPage * ($pageNumber - 1);
        } else {
            $offset = 0;
        }

        // If we're sorting by risk, we need to create separate lists of students with and without the risk permission;
        // otherwise, the risk level of those without the risk permission would be obvious by where they appear in the list.
        if (strpos($sortBy, 'risk') !== false) {
            $studentsWithRiskPermission = array_keys($riskPermission, 1);
            $studentsWithoutRiskPermission = array_keys($riskPermission, 0);

            // If the current page includes students with risk permission, first get the data for those students.
            if ($offset < count($studentsWithRiskPermission)) {
                $recordsWithRiskPermission = $this->studentListDAO->getAdditionalStudentData($studentsWithRiskPermission, $classLevelEbiMetadataId, $timeZoneString, $sortBy, $offset, $recordsPerPage);
                $records = $recordsWithRiskPermission;

                // If the students with risk permission haven't filled the current page and there are students without risk permission, get the data for enough of those students to fill the page.
                if ((count($recordsWithRiskPermission) < $recordsPerPage) && (count($studentsWithoutRiskPermission) > 0)) {
                    $noRiskRecordLimit = $recordsPerPage - count($recordsWithRiskPermission);
                    $recordsWithoutRiskPermission = $this->studentListDAO->getAdditionalStudentData($studentsWithoutRiskPermission, $classLevelEbiMetadataId, $timeZoneString, 'student_last_name', 0, $noRiskRecordLimit);
                    $records = array_merge($recordsWithRiskPermission, $recordsWithoutRiskPermission);
                }

            // If the current page is after all the students with risk permission, get data for students without risk permission.
            } else {
                $noRiskOffset = $offset - count($studentsWithRiskPermission);
                $records = $this->studentListDAO->getAdditionalStudentData($studentsWithoutRiskPermission, $classLevelEbiMetadataId, $timeZoneString, 'student_last_name', $noRiskOffset, $recordsPerPage);
            }

        // If we're sorting by intent to leave, we need to create separate lists of students with and without the intent to leave permission;
        // otherwise, the intent to leave of those without the intent to leave permission would be obvious by where they appear in the list.
        } elseif (strpos($sortBy, 'intent') !== false) {
            $studentsWithIntentToLeavePermission = array_keys($intentToLeavePermission, 1);
            $studentsWithoutIntentToLeavePermission = array_keys($intentToLeavePermission, 0);

            // If the current page includes students with intent to leave permission, first get the data for those students.
            if ($offset < count($studentsWithIntentToLeavePermission)) {
                $recordsWithIntentToLeavePermission = $this->studentListDAO->getAdditionalStudentData($studentsWithIntentToLeavePermission, $classLevelEbiMetadataId, $timeZoneString, $sortBy, $offset, $recordsPerPage);
                $records = $recordsWithIntentToLeavePermission;

                // If the students with intent to leave permission haven't filled the current page and there are students without intent to leave permission, get the data for enough of those students to fill the page.
                if ((count($recordsWithIntentToLeavePermission) < $recordsPerPage) && (count($studentsWithoutIntentToLeavePermission) > 0)) {
                    $noIntentToLeaveRecordLimit = $recordsPerPage - count($recordsWithIntentToLeavePermission);
                    $recordsWithoutIntentToLeavePermission = $this->studentListDAO->getAdditionalStudentData($studentsWithoutIntentToLeavePermission, $classLevelEbiMetadataId, $timeZoneString, 'student_last_name', 0, $noIntentToLeaveRecordLimit);
                    $records = array_merge($recordsWithIntentToLeavePermission, $recordsWithoutIntentToLeavePermission);
                }

            // If the current page is after all the students with intent to leave permission, get data for students without intent to leave permission.
            } else {
                $noIntentToLeaveOffset = $offset - count($studentsWithIntentToLeavePermission);
                $records = $this->studentListDAO->getAdditionalStudentData($studentsWithoutIntentToLeavePermission, $classLevelEbiMetadataId, $timeZoneString, 'student_last_name', $noIntentToLeaveOffset, $recordsPerPage);
            }

        // If we're sorting by anything other than risk or intent to leave, just get the results for the requested page.
        } else {
            $records = $this->studentListDAO->getAdditionalStudentData($studentIds, $classLevelEbiMetadataId, $timeZoneString, $sortBy, $offset, $recordsPerPage);
        }

        $records = $this->removeRiskAndIntentToLeaveFromRecordsWithoutPermission($records, $riskPermission, $intentToLeavePermission);

        return $records;
    }


    /**
     * Given a list of student ids, returns the list with additional data,
     * including name, external id, email, status, risk, intent to leave, class level, activity count, and date and type of last activity.
     *
     * Applies risk and intent to leave permissions to ensure this data is not included about students if not permitted;
     * these students will be at the end of the list if sorting is done by risk or intent to leave.
     *
     * This function is intended to be used in creating a CSV, so it includes all the students,
     * and the last activity date is concatenated with the activity type.
     *
     * @param array $studentIds
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $sortBy
     * @return array
     */
    public function getStudentListWithAdditionalDataForCSV($studentIds, $loggedInUserId, $organizationId, $sortBy)
    {
        $classLevelEbiMetadataId = $this->ebiMetadataRepository->findOneBy(['key' => 'ClassLevel'])->getId();

        $timeZoneString = $this->dateUtilityService->getOrganizationISOTimeZone($organizationId);

        $riskPermission = $this->orgPermissionsetRepository->getRiskPermissionForFacultyAndStudents($loggedInUserId, $studentIds);
        $intentToLeavePermission = $this->orgPermissionsetRepository->getIntentToLeavePermissionForFacultyAndStudents($loggedInUserId, $studentIds);

        // If we're sorting by risk, we need to create separate lists of students with and without the risk permission;
        // otherwise, the risk level of those without the risk permission would be obvious by where they appear in the list.
        if (strpos($sortBy, 'risk') !== false) {
            $studentsWithRiskPermission = array_keys($riskPermission, 1);
            $studentsWithoutRiskPermission = array_keys($riskPermission, 0);

            $recordsWithRiskPermission = $this->studentListDAO->getAdditionalStudentData($studentsWithRiskPermission, $classLevelEbiMetadataId, $timeZoneString, $sortBy);
            $recordsWithoutRiskPermission = $this->studentListDAO->getAdditionalStudentData($studentsWithoutRiskPermission, $classLevelEbiMetadataId, $timeZoneString, 'student_last_name');
            $records = array_merge($recordsWithRiskPermission, $recordsWithoutRiskPermission);

        // If we're sorting by intent to leave, we need to create separate lists of students with and without the intent to leave permission;
        // otherwise, the intent to leave of those without the intent to leave permission would be obvious by where they appear in the list.
        } elseif (strpos($sortBy, 'intent') !== false) {
            $studentsWithIntentToLeavePermission = array_keys($intentToLeavePermission, 1);
            $studentsWithoutIntentToLeavePermission = array_keys($intentToLeavePermission, 0);

            $recordsWithIntentToLeavePermission = $this->studentListDAO->getAdditionalStudentData($studentsWithIntentToLeavePermission, $classLevelEbiMetadataId, $timeZoneString, $sortBy);
            $recordsWithoutIntentToLeavePermission = $this->studentListDAO->getAdditionalStudentData($studentsWithoutIntentToLeavePermission, $classLevelEbiMetadataId, $timeZoneString, 'student_last_name');
            $records = array_merge($recordsWithIntentToLeavePermission, $recordsWithoutIntentToLeavePermission);

        } else {
            $records = $this->studentListDAO->getAdditionalStudentData($studentIds, $classLevelEbiMetadataId, $timeZoneString, $sortBy);
        }

        $records = $this->removeRiskAndIntentToLeaveFromRecordsWithoutPermission($records, $riskPermission, $intentToLeavePermission);

        return $records;
    }


    /**
     * Given a list of person_ids for students, gets their names and formats them as expected.
     * This list is used to set up a bulk action.
     *
     * @param array $studentIds
     * @param int $loggedInUserId
     * @return array
     */
    public function getStudentIdsAndNames($studentIds, $loggedInUserId)
    {
        $studentList = $this->personRepository->getPersonNames($studentIds, 'name');

        $studentListToReturn = [];

        foreach ($studentList as $student) {
            $studentRecordToReturn = [];
            $studentRecordToReturn['student_id'] = $student['person_id'];
            $studentRecordToReturn['student_first_name'] = $student['firstname'];
            $studentRecordToReturn['student_last_name'] = $student['lastname'];
            $studentListToReturn[] = $studentRecordToReturn;
        }

        $dataToReturn = [];
        $dataToReturn['person_id'] = $loggedInUserId;
        $dataToReturn['search_result'] = $studentListToReturn;

        return $dataToReturn;
    }


    /**
     * Null out risk and intent to leave for records without those permissions.
     *
     * @param array $records
     * @param array $riskPermission
     * @param array $intentToLeavePermission
     * @return array
     */
    private function removeRiskAndIntentToLeaveFromRecordsWithoutPermission($records, $riskPermission, $intentToLeavePermission) {
        $newRecords = [];
        foreach ($records as $record) {
            if (!$riskPermission[$record['student_id']]) {
                $record['student_risk_status'] = null;
                $record['student_risk_image_name'] = null;
            }

            if (!$intentToLeavePermission[$record['student_id']]) {
                $record['student_intent_to_leave'] = null;
                $record['student_intent_to_leave_image_name'] = null;
            }

            $newRecords[] = $record;
        }

        return $newRecords;
    }




}