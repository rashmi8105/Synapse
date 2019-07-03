<?php
namespace Synapse\CampusConnectionBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CampusConnectionBundle\EntityDto\AssignPrimaryRequestDto;
use Synapse\CampusConnectionBundle\EntityDto\CampusConnectionsArrayDto;
use Synapse\CampusConnectionBundle\EntityDto\StudentCampusConnectionsDto;
use Synapse\CampusConnectionBundle\EntityDto\StudentFacultyConnectionsListDto;
use Synapse\CampusConnectionBundle\EntityDto\StudentListDto;
use Synapse\CampusConnectionBundle\Util\Constants\CampusConnectionErrorConstants;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\AcademicUpdateConstant;
use Synapse\PersonBundle\Repository\ContactInfoRepository;

/**
 * @DI\Service("campusconnection_service")
 */
class CampusConnectionService extends CampusConnectionServiceHelper
{

    const SERVICE_KEY = 'campusconnection_service';

    //Scaffolding
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    private $rbacManager;


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
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    // Repositories
    /**
     * @var ContactInfoRepository
     */
    private $contactInfoRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationlangRepository
     */
    private $organizationLangRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $orgRoleRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * CampusConnectionService constructor.
     *
     *      @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *      })
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Logger $logger
     * @param Container $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        //Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);

        // Repositories
        $this->contactInfoRepository = $this->repositoryResolver->getRepository(ContactInfoRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationLangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

    }

    /**
     * Assigning primary connection
     *
     * @param AssignPrimaryRequestDto $assignPrimaryRequestDto
     * @param int $loggedInUserId
     * @return AssignPrimaryRequestDto
     * @throws AccessDeniedException
     */
    public function assignPrimaryConnection(AssignPrimaryRequestDto $assignPrimaryRequestDto, $loggedInUserId)
    {
        $organizationId = $assignPrimaryRequestDto->getOrganizationId();
        $loggedInUser = $this->personRepository->find($loggedInUserId);

        // If an OrganizationRole object is not returned, throws a SynapseValidationError
        $this->orgRoleRepository->findOneBy([
            'organization' => $organizationId,
            'person' => $loggedInUserId
        ], new SynapseValidationException(AcademicUpdateConstant::COORDINATOR_ACCESS_DENIED));

        // If an Organization object is not returned, throws a SynapseValidationError
        $organization = $this->organizationRepository->find($organizationId, new SynapseValidationException("Organization Not Found"));

        $currentDate = new \DateTime();
        /**
         * Assuming that all student and staff are organization specific.
         * Since multiple student staff listing array, it will be more db connections and performance affect
         * within loop to identify the student and staff validation is not done
         */
        $staffList = $assignPrimaryRequestDto->getStudentList();
        $studentArray = $this->prepareStudentArray($staffList);
        $assignPrimaryResponseDto = new AssignPrimaryRequestDto();
        if (isset($studentArray) && count($studentArray) > 0) {

            $orgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
            $listContainsNonParticipants = $this->orgPersonStudentYearRepository->doesStudentIdListContainNonParticipants($studentArray, $orgAcademicYearId);
            if ($listContainsNonParticipants) {
                throw new AccessDeniedException('Student list contains non-participating students');
            }
            $campusConnections = $this->orgPersonFacultyRepository->getCourseCampusConnection(implode(",", $studentArray), $assignPrimaryRequestDto->getOrganizationId(), $currentDate);
            $campusGroupConnections = $this->orgPersonFacultyRepository->getGroupCampusConnection(implode(",", $studentArray), $assignPrimaryRequestDto->getOrganizationId(), $currentDate);
            $connections = array_merge($campusConnections, $campusGroupConnections);
            $studentFaculty = $this->mapStudentFacultyArray($connections);
            $studentListArray = [];
            $studentCount = 0;
            foreach ($staffList as $staff) {
                $studentId = $staff->getStudentId();
                $staffId = $staff->getStaffId();

                $studentListDto = new StudentListDto();
                $studentListDto->setStudentId($studentId);
                $studentListDto->setStaffId($staffId);

                if (array_key_exists($studentId, $studentFaculty) && in_array($staffId, $studentFaculty[$studentId])) {
                    $isUpdated = $this->orgPersonStudentRepository->updatePrimaryConnection($organizationId, $studentId, $staffId);
                    if ($isUpdated > 0) {
                        $studentListDto->setIsPrimaryAssigned(true);
                    } else {
                        $studentListDto->setIsPrimaryAssigned(false);
                    }
                    $studentCount++;
                } else {
                    $studentListDto->setIsPrimaryAssigned(false);
                }
                $studentListArray[] = $studentListDto;
            }
            $assignPrimaryResponseDto->setOrganizationId($organizationId);
            $assignPrimaryResponseDto->setStudentList($studentListArray);

            if (count($staffList) > 1 && ($studentCount > 0)) {
                $this->alertNotificationsService->createNotification('bulk-action-completed', $studentCount . ' students have been assigned primary campus connections.', $loggedInUser, null, null, null, null, null, null, $organization);
            }

        }
        return $assignPrimaryResponseDto;
    }

    /**
     * Fetches campus connections for students during bulk actions
     *
     * @param int $organizationId
     * @param string $studentIds
     * @return array
     * @throws AccessDeniedException
     */
    public function getStudentFacultyConnections($organizationId, $studentIds)
    {

        $studentFacultyList = [];
        $this->organizationRepository->findOneBy(['id' => $organizationId], new SynapseValidationException("Organization Not Found"));

        $currentDate = new \DateTime();
        $date = $currentDate->format(SynapseConstant::DEFAULT_DATE_FORMAT);

        $organizationAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
        $studentIdsArray = explode(",",$studentIds);
        $listContainsNonParticipants = $this->orgPersonStudentYearRepository->doesStudentIdListContainNonParticipants($studentIdsArray, $organizationAcademicYearId);
        if ($listContainsNonParticipants) {
            throw new AccessDeniedException('Student list contains non-participating students');
        }
        $facultyList = $this->orgPersonFacultyRepository->getStudentCampusConnection($studentIds, $organizationId, $date);
        $groupStudents = $this->orgPersonFacultyRepository->getGroupCampusConnection($studentIds, $organizationId, $date);
        $courseStudents = $this->orgPersonFacultyRepository->getCourseCampusConnection($studentIds, $organizationId, $date);
        $studentArray = array_merge($groupStudents, $courseStudents);
        $facultyStudents = $this->mapStudentFacultyArray($studentArray);

        foreach ($facultyList as $faculty) {
            $studentFacultyConnectionsListDto = new StudentFacultyConnectionsListDto();
            $studentFacultyConnectionsListDto->setId($faculty['person_id']);
            $studentFacultyConnectionsListDto->setFirstname($faculty['fname']);
            $studentFacultyConnectionsListDto->setLastname($faculty['lname']);

            // Set title
            if (!empty($faculty['title'])) {
                $title = $faculty['title'];
            } else {
                $title = '';
            }
            $studentFacultyConnectionsListDto->setTitle($title);

            // Set External Id
            if (!empty($faculty['external_id'])) {
                $externalId = $faculty['external_id'];
            } else {
                $externalId = '';
            }
            $studentFacultyConnectionsListDto->setExternalId($externalId);

            // Set Email
            if (!empty($faculty['email'])) {
                $email =$faculty['email'];
            } else {
                $email = '';
            }
            $studentFacultyConnectionsListDto->setEmail($email);

            $students = $this->getFacultyStudentsArray($facultyStudents, $faculty['person_id']);
            $studentFacultyConnectionsListDto->setStudents($students);
            $studentFacultyList['staff_details'][] = $studentFacultyConnectionsListDto;
        }
        return $studentFacultyList;
    }

    /**
     * Get campus connections for the specified student
     *
     * @param int $organizationId
     * @param int $studentId
     * @return StudentCampusConnectionsDto
     * @throws AccessDeniedException | SynapseValidationException
     */
    public function getStudentCampusConnections($organizationId, $studentId)
    {
        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        $organization = $this->organizationLangRepository->findOneBy(['organization' => $organizationId]);
        if (!$organization) {
            throw new SynapseValidationException('Organization Not Found.');
        }

        $orgPersonStudent = $this->orgPersonStudentRepository->findOneBy(['person' => $studentId, 'organization' => $organizationId]);
        if (!$orgPersonStudent) {
            throw new SynapseValidationException('Student Not Found.');
        }

        // Get Primary Connection Id
        if ($orgPersonStudent->getPersonIdPrimaryConnect()) {
            $personPrimary = $orgPersonStudent->getPersonIdPrimaryConnect()->getId();
        } else {
            $personPrimary = 0;
        }

        $currentDate = new \DateTime();
        $date = $currentDate->format(SynapseConstant::DATE_YMD_FORMAT);
        $campusConnections = $this->orgPersonFacultyRepository->getStudentCampusConnection($studentId, $organizationId, $date);
        $facultyCampusConnections = $this->getFacultyDetailsArray($campusConnections);
        $orderedCampusConnections = $this->getPrimaryConnectionFirstArray($facultyCampusConnections, $personPrimary);
        $studentCampusConnectionsDto = new StudentCampusConnectionsDto();
        $studentCampusConnectionsDto->setOrganizationId($organizationId);
        $studentCampusConnectionsDto->setCampusId($organization->getOrganization()->getCampusId());
        $studentCampusConnectionsDto->setCampusName($organization->getOrganizationName());

        //getting faculty contact info
        $facultyIds = array_column($orderedCampusConnections, 'id');
        $facultyContactInfoArray = $this->contactInfoRepository->getPersonMobileAndHomePhoneNumbers($facultyIds);
        $facultyPhoneArray = [];
        foreach ($facultyContactInfoArray as $facultyContactInfo) {
            // Set phone number
            if (!empty($facultyContactInfo['primary_mobile'])) {
                $phoneNumber = $facultyContactInfo['primary_mobile'];
            } else {
                $phoneNumber = $facultyContactInfo['home_phone'];
            }
            $facultyPhoneArray[$facultyContactInfo['id']] = $phoneNumber;
        }
        $connectionsArray = [];
        foreach ($orderedCampusConnections as $facultyDetail) {
            $campusConnectionArrayDto = new CampusConnectionsArrayDto();
            $campusConnectionArrayDto->setPersonId($facultyDetail['id']);
            $campusConnectionArrayDto->setPersonFirstname($facultyDetail['fname']);
            $campusConnectionArrayDto->setPersonLastname($facultyDetail['lname']);

            // Set person title
            if (!empty($facultyDetail['title'])) {
                $title = $facultyDetail['title'];
            } else {
                $title = '';
            }
            $campusConnectionArrayDto->setPersonTitle($title);

            // Set phone
            if (array_key_exists($facultyDetail['id'], $facultyPhoneArray)) {
                $phone = $facultyPhoneArray[$facultyDetail['id']];
            } else {
                $phone = null;
            }
            $campusConnectionArrayDto->setPhone($phone);

            // Set email
            if (!empty($facultyDetail['email'])) {
                $email = $facultyDetail['email'];
            } else {
                $email = '';
            }
            $campusConnectionArrayDto->setEmail($email);

            // Set Primary Connection
            if ($facultyDetail['id'] == $personPrimary) {
                $isPrimaryCampusConnection = true;
            } else {
                $isPrimaryCampusConnection = false;
            }
            $campusConnectionArrayDto->setPrimaryConnection($isPrimaryCampusConnection);

            // Set is invisible
            if ($facultyDetail['is_invisible']) {
                $isInvisible = true;
            } else {
                $isInvisible = false;
            }
            $campusConnectionArrayDto->setIsInvisible($isInvisible);

            $groups = [];
            $courses = [];
            foreach ($facultyDetail['details'] as $associatedWith) {
                if ($associatedWith['flag'] == 'group') {
                    $group = array();
                    $group['group_id'] = $associatedWith['course_or_group_id'];
                    $group['group_name'] = $associatedWith['course_or_group_name'];
                    $groups[] = $group;
                } else {
                    $course = array();
                    $course['course_id'] = $associatedWith['course_or_group_id'];
                    $course['course_name'] = $associatedWith['course_or_group_name'];
                    $courses[] = $course;
                }
            }
            $campusConnectionArrayDto->setGroups($groups);
            $campusConnectionArrayDto->setCourses($courses);
            $connectionsArray[] = $campusConnectionArrayDto;
        }
        $studentCampusConnectionsDto->setCampusConnections($connectionsArray);
        return $studentCampusConnectionsDto;
    }

    /**
     * Remove primary campus connection for student
     *
     * @param integer $organizationId
     * @param integer $studentId
     * @param integer $loggedInUserId
     * @throws SynapseValidationException
     */
    public function removePrimaryCampusConnection($organizationId, $studentId, $loggedInUserId)
    {
        $organization = $this->organizationRepository->find($organizationId);
        if (!$organization) {
            throw new SynapseValidationException('Organization Not Found.');
        }

        $coordinator = $this->orgRoleRepository->findOneBy([
            'organization' => $organizationId,
            'person' => $loggedInUserId
        ]);
        if (!$coordinator) {
            throw new SynapseValidationException('You do not have coordinator access.');
        }

        $personStudent = $this->orgPersonStudentRepository->findOneBy([
            'person' => $studentId,
            'organization' => $organizationId
        ]);
        if (!$personStudent) {
            throw new SynapseValidationException('Student Not Found.');
        }

        $personStudent->setPersonIdPrimaryConnect(null);
        $this->orgPersonStudentRepository->flush();
    }


    /**
     * Validates user as a primary campus connection
     *
     * @param string $primaryCampusConnectionId
     * @param Organization $organization
     * @param int $personId
     * @return boolean|string
     */
    public function validatePrimaryCampusConnectionId($primaryCampusConnectionId, $organization, $personId)
    {
        $errorMessage = '';
        $validPerson = $this->personRepository->findOneBy(['externalId' => $primaryCampusConnectionId, 'organization' => $organization]);
        if ($validPerson) {
            $hasStudentAccess = $this->rbacManager->checkAccessToStudent($personId, $validPerson->getId());
            if (!$hasStudentAccess) {
                $errorMessage = "Faculty does not have access to this student: " . $personId;
            }
        } else {
            $errorMessage = "Invalid Primary Campus Connection Id";
        }
        if (!empty($errorMessage)) {
            return $errorMessage;
        }
        return true;
    }
}