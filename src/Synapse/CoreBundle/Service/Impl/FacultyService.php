<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\AcademicBundle\Service\Impl\CourseFacultyStudentValidatorService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgPersonFaculty;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\PermissionConstInterface;
use Synapse\CoreBundle\Service\FacultyServiceInterface;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\CoreBundle\Util\Constants\PersonConstant;
use Synapse\RestBundle\Entity\FacultyPolicyDto;
use Synapse\PersonBundle\DTO\PersonDTO;
use Synapse\RestBundle\Exception\ValidationException;


/**
 * @DI\Service("faculty_service")
 */
class FacultyService extends AbstractService implements FacultyServiceInterface, PermissionConstInterface
{

    const SERVICE_KEY = 'faculty_service';

    //Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    private $rbacManager;

    //Services

    /**
     * @var CourseFacultyStudentValidatorService
     */
    private $courseFacultyStudentValidatorService;

    /**
     * @var PersonService
     */
    private $personService;

    //Repository

    /**
     * @var MetadataListValuesRepository
     */
    private $metadataListValuesRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgCourseStudentRepository
     */
    private $orgCourseStudentRepository;

    /**
     * @var OrgCourseFacultyRepository
     */
    private $orgCourseFacultyRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;


    /**
     * FacultyService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        //scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);

        //services
        $this->courseFacultyStudentValidatorService = $this->container->get(CourseFacultyStudentValidatorService::SERVICE_KEY);
        $this->personService = $this->container->get(CourseConstant::PERSON_SERVICE);

        //Repositories
        $this->metadataListValuesRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgCourseStudentRepository = $this->repositoryResolver->getRepository(OrgCourseStudentRepository::REPOSITORY_KEY);
        $this->orgCourseFacultyRepository = $this->repositoryResolver->getRepository(OrgCourseFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);



    }

    /**
     * Soft deletes a faculty member. It does not actually remove any records.
     * @param int $personId
     * @return bool
     */
    public function softDeleteById($personId)
    {
        /** @var OrgPersonFacultyRepository */
        $repo = $this->repositoryResolver->getRepository(PersonConstant::PERSON_FACULTY_REPO);

        try {
            $orgFaculty = $repo->findOneBy([
                'person' => $personId
            ]);
            $repo->remove($orgFaculty);
            $status = true;
        } catch (\Exception $e) {
            $status = false;
        }

        return $status;
    }

    public function getGroupsList($orgId, $personId)
    {
        $this->rbacManager->checkAccessToOrganizationUsingPersonId($personId);

        /** @var OrgGroupFacultyRepository $orgGroupFacultyRepo */
        $orgGroupFacultyRepo = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupFaculty');
        $groupList = $orgGroupFacultyRepo->getGroupsByPerson($personId);

        return $groupList;
    }

    /**
     * @param int $personId
     * @param int $groupId
     * @param bool|false $permissionsetId
     * @param bool|false $isInvisible
     * @return bool
     */
    public function addGroup($personId, $groupId, $permissionsetId = false, $isInvisible = false)
    {
        /** @var OrgGroupFacultyRepository $repo */
        $repo = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupFaculty');
        $repo->addFacultyGroupAssoc($personId, $groupId, $permissionsetId, $isInvisible);
        return true;
    }

    /**
     * @param int $personId
     * @param int $groupId
     * @return bool
     */
    public function removeGroup($personId, $groupId)
    {
        /** @var OrgGroupFacultyRepository $repo */
        $repo = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupFaculty');
        $repo->removeFacultyGroupAssoc($personId, $groupId);
        return true;
    }

    /**
     * Add a faculty member ($personId) to a course ($courseId). Returns true if successful.
     *
     * @param int $personId
     * @param int $courseId
     * @param int|null $permissionsetId
     * @throws ValidationException
     * @return bool
     */
    public function addCourse($personId, $courseId, $permissionsetId = null)
    {
        $person = $this->personService->findPerson($personId);
        $organizationId = $person->getOrganization()->getId();
        // Added this to check Faculty in course.
        $this->courseFacultyStudentValidatorService->validateAdditionOfFacultyToCourse($personId, $organizationId, $courseId);
        $this->orgCourseFacultyRepository->addFacultyCourseAssoc($personId, $organizationId, $courseId, $permissionsetId);
        return true;
    }

    /**
     * @param int $personId
     * @param int $courseId
     * @return bool
     */
    public function removeCourse($personId, $courseId)
    {
        /** @var OrgCourseFacultyRepository $repo */
        $repo = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgCourseFaculty');
        $repo->removeFacultyCourseAssoc($personId, $courseId);
        return true;
    }

    /**
     * @param int $personId
     * @param int $teamId
     * @param string $role
     * @return bool
     */
    public function addTeam($personId, $teamId, $role = null)
    {
        /** @var TeamMembersRepository $repo */
        $repo = $this->repositoryResolver->getRepository('SynapseCoreBundle:TeamMembers');
        $personService = $this->container->get('person_service');
        $person = $personService->find($personId);
        $repo->addPersonTeamAssoc($personId, $teamId, $person->getOrganization(), $role);
        return true;
    }

    /**
     * @param int $personId
     * @param int $teamId
     * @return bool
     */
    public function removeTeam($personId, $teamId)
    {
        /** @var TeamMembersRepository $repo */
        $repo = $this->repositoryResolver->getRepository('SynapseCoreBundle:TeamMembers');
        $repo->removePersonTeamAssoc($personId, $teamId);
        return true;
    }

    public function listFacultyCourses($userid, $organization)
    {
        $repo = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgCourseFaculty');

        $orgAcademicYearRepository = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgAcademicYear');
        $curDate = new \DateTime('now');
        $curDate->setTime(0, 0, 0);
        $yearId = $orgAcademicYearRepository->getCurrentAcademicDetails($curDate, $organization);


        if ($yearId) {
            $year = $yearId[0]['yearId'];

        } else {
            $yearId = 'all';
        }


        return $repo->listFacultyCourses($userid, $organization, $curDate->format('Y-m-d H:i:s'), $year);
    }

    /**
     * @param int $personId
     * @param int $groupId
     * @param bool|false $permissionsetId
     * @param bool|false $isInvisible
     * @return bool
     */
    public function updateFacultyGroupMembership($personId, $groupId, $permissionsetId = false, $isInvisible = false)
    {
        /** @var OrgGroupFacultyRepository $repo */
        $repo = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupFaculty');
        $permissionsetService = $this->container->get('orgpermissionset_service');
        $groupFaculty = $repo->findOneBy(['person' => $personId, 'orgGroup' => $groupId]);
        if (!$groupFaculty) {
            throw new ValidationException(['Person not entrolled this group'], 'Person not entrolled this group', 'person_group_enroll');
        }

        $orgPermissionset = isset($permissionsetId) ? $permissionsetId : 0;
        $isInvisible = isset($isInvisible) ? $isInvisible : 0;
        if ($orgPermissionset > 0) {
            $orgPermissionset = $permissionsetService->find($orgPermissionset);

            $groupFaculty->setOrgPermissionset($orgPermissionset);
        } else {
            $groupFaculty->setOrgPermissionset(null);
        }

        $groupFaculty->setIsInvisible($isInvisible);
        $repo->flush();
        return true;

    }

    public function updateFacultyCourseMembership($personId, $course_id, $permissionsetId)
    {
        $repo = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgCourseFaculty');
        $permissionsetService = $this->container->get('orgpermissionset_service');
        $courseFaculty = $repo->findOneBy(['person' => $personId, 'course' => $course_id]);
        if (!$courseFaculty) {
            throw new ValidationException(['Person not entrolled this course'], 'Person not entrolled this course', 'person_course_enroll');
        }
        $orgPermissionset = isset($permissionsetId) ? $permissionsetId : 0;

        if ($orgPermissionset > 0) {
            $orgPermissionset = $permissionsetService->find($orgPermissionset);

            $courseFaculty->setOrgPermissionset($orgPermissionset);
        } else {
            $courseFaculty->setOrgPermissionset(null);
        }

        $repo->flush();
        return true;
    }

    public function updateTeamMembership($personId, $teamId, $role = null)
    {
        $repo = $this->repositoryResolver->getRepository('SynapseCoreBundle:TeamMembers');
        $teamMember = $repo->findOneBy(['teamId' => $teamId, 'person' => $personId]);
        if (!$teamMember) {
            throw new ValidationException(['Person not entrolled this team'], 'Person not entrolled this team', 'personteam_enroll');
        }
        if ($role == 1) {
            $teamMember->setIsTeamLeader(true);
        } else {
            $teamMember->setIsTeamLeader(false);
        }

        $repo->flush();
        return true;
    }

    public function manageGroupMembership($groupList, $personId)
    {

        $status = false;
        foreach ($groupList as $groupData) {

            $action = $groupData['action'];
            $groupId = $groupData['group_id'];
            $permissionsetId = $groupData['staff_permissionset_id'];
            $isInvisible = $groupData['staff_is_invisible'];

            if ($action == "add") {
                $status = $this->addGroup($personId, $groupId, $permissionsetId, $isInvisible);
            }
            if ($action == "update") {
                $status = $this->updateFacultyGroupMembership($personId, $groupId, $permissionsetId, $isInvisible);
            }
            if ($action == "delete") {
                $status = $this->removeGroup($personId, $groupId);
            }
        }
        return $status;
    }

    public function manageFacultyCourseMembership($courseIds, $personId)
    {

        $status = false;
        foreach ($courseIds as $courseData) {

            $action = $courseData['action'];
            $courseId = $courseData['course_id'];
            $permissionsetId = $courseData['staff_permissionset_id'];
            if ($action == "add") {

                $status = $this->addCourse($personId, $courseId, $permissionsetId);
            }
            if ($action == "update") {

                $status = $this->updateFacultyCourseMembership($personId, $courseId, $permissionsetId);
            }
            if ($action == "delete") {

                $status = $this->removeCourse($personId, $courseId);
            }
        }
        return $status;
    }

    public function manageFacultyTeamMembership($personId, $teamList)
    {

        $status = false;
        foreach ($teamList as $teamData) {

            $action = $teamData['action'];
            $teamId = $teamData['team_id'];
            $role = $teamData['role'];
            if ($action == "add") {

                $status = $this->addTeam($personId, $teamId, $role);
            }
            if ($action == "update") {

                $status = $this->updateTeamMembership($personId, $teamId, $role);
            }
            if ($action == "delete") {

                $status = $this->removeTeam($personId, $teamId);
            }
        }
        return $status;
    }

     /**
     * Update privacy policy accepted field for a faculty
     *
     * @param FacultyPolicyDto $facultyPolicyDto
     * @throws SynapseValidationException
     */
    public function updatePolicy(FacultyPolicyDto $facultyPolicyDto)
    {

        $this->logger->info('Update faculty Privacy Policy');
        $facultyId = $facultyPolicyDto->getFacultyId();
        $organizationId = $facultyPolicyDto->getOrganizationId();
        $privacyPolicy = $facultyPolicyDto->getIsPrivacyPolicyAccepted();

        $faculty = $this->orgPersonFacultyRepository->findBy(array(
            'organization' => $organizationId,
            'person' => $facultyId
        ));

        $organization = $this->organizationRepository->find($organizationId);
        $timezone = $organization->getTimezone();
        $timezone = $this->metadataListValuesRepository->findByListName($timezone);
        if ($timezone) {
            $timezone = $timezone[0]->getListValue();
        }
        if (empty($faculty)) {
            throw new SynapseValidationException('Person not found.');
        }

        $orgFaculty = $this->orgPersonFacultyRepository->findOneBy([
            'person' => $facultyId,
            'organization' => $organizationId
        ]);

        if (empty($orgFaculty)) {
            throw new SynapseValidationException('Faculty does not exist.');
        }

        $currentDate = new \DateTime('now',new \DateTimeZone($timezone));

        $orgFaculty->setIsPrivacyPolicyAccepted($privacyPolicy);
        $orgFaculty->setPrivacyPolicyAcceptedDate($currentDate);
        $this->orgPersonFacultyRepository->flush();

        $this->logger->info('Update Student Faculty Policy is completed - Faculty ID -'.$facultyId. 'Organization ID - '. $organizationId );
    }


    /**
     * @param Person $personObject
     * @param PersonDTO $personDTO
     * @param Organization $organization
     * @param Person $loggedInUser
     * @return boolean
     */
    public function determineFacultyUpdateType($personObject, $personDTO, $organization, $loggedInUser)
    {
        // Getting person faculty object with person object
        $orgPersonFaculty = $this->orgPersonFacultyRepository->findOneBy([
            'person' => $personObject,
            'organization' => $personObject->getOrganization()
        ]);

        // Create|Remove Faculty and no case for Update as there is no fields in DTO to update
        $actionFaculty = null;
        if ($orgPersonFaculty && !$personDTO->getIsFaculty()) {
            $actionFaculty = 'remove_faculty';
        } else if (!$orgPersonFaculty && $personDTO->getIsFaculty()) {
            $actionFaculty = 'create_faculty';
        }

        switch ($actionFaculty) {
            case 'remove_faculty':
                $this->removeFaculty($orgPersonFaculty, $loggedInUser);
                break;
            case 'create_faculty':
                $this->createFaculty($personDTO, $personObject, $organization, $loggedInUser);
                break;
        }
        return true;
    }


    /**
     * Creating new faculty and returns true
     *
     * @param PersonDTO $personDTO
     * @param Person $personObject
     * @param Organization $organization
     * @param Person $loggedInUser
     * @return boolean
     */
    public function createFaculty($personDTO, $personObject, $organization, $loggedInUser)
    {
        $facultyObject = new OrgPersonFaculty();

        $facultyAuthKey = $this->personService->generateAuthKey($personDTO->getExternalId(), 'Faculty');
        $currentDate =  new \DateTime();

        //setting valid values to the orgPersonFaculty Object
        $facultyObject->setPerson($personObject);
        $facultyObject->setOrganization($organization);
        $facultyObject->setAuthKey($facultyAuthKey);
        $facultyObject->setCreatedAt($currentDate);
        $facultyObject->setCreatedBy($loggedInUser);
        $facultyObject->setModifiedAt($currentDate);
        $facultyObject->setModifiedBy($loggedInUser);

        // Faculty's status should be active, if its new person faculty
        $facultyObject->setStatus(1);

        //persisting and unset faculty object
        $facultyObject = $this->orgPersonFacultyRepository->persist($facultyObject);
        unset($facultyObject);

        return true;
    }


    /**
     * @param OrgPersonFaculty $orgPersonFaculty
     * @param Person $loggedInUser
     * @return bool
     */
    public function removeFaculty($orgPersonFaculty, $loggedInUser)
    {
        $currentDate = new \DateTime();
        // Remove the existing faculty, If OrgPersonFaculty exists and is_faculty is false
        $orgPersonFaculty->setDeletedAt($currentDate);
        $orgPersonFaculty->setDeletedBy($loggedInUser);
        $this->orgPersonFacultyRepository->persist($orgPersonFaculty);
        unset($orgPersonFaculty);

        return true;
    }

    /**
     * Checks to see if the person is a faculty
     *
     * @param int $personId
     * @return bool
     */
    public function isPersonAFaculty($personId)
    {
        $facultyObject = $this->orgPersonFacultyRepository->findOneBy([
            'person' => $personId
        ]);

        $isFaculty = ($facultyObject)? true: false;
        unset($facultyObject);
        return $isFaculty;
    }


    /**
     * Checks to see if the faculty is Active
     *
     * @param int $personId
     * @return bool
     */
    public function isFacultyActive($personId)
    {

        $facultyObject = $this->orgPersonFacultyRepository->findOneBy(
            [
                'person' => $personId
            ],
            new SynapseValidationException("$personId is not a valid faculty")
        );
        if ($facultyObject->getStatus() == 0) {
            $isFacultyActive = false;
        } else {
            $isFacultyActive = true;
        }
        unset($facultyObject);
        return $isFacultyActive;
    }
}
