<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Util\Constants\PersonConstant;

/**
 * @DI\Service("loggedin_person_service")
 */
class LoggedInPersonService extends AbstractService
{

    const SERVICE_KEY = 'loggedin_person_service';

    // Scaffolding
    /**
     * @var Container
     */
    private $container;

    // Services
    /**
     * @var OrgPermissionsetService
     */
    private $orgPermissionSetService;

    // Repositories
    /**
     * @var OrgCoursesRepository
     */
    private $orgCourseRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * LoggedInPersonService constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger"),
     *
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        //Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->logger = $logger;

        //services
        $this->orgPermissionSetService = $this->container->get(OrgPermissionsetService::SERVICE_KEY);

        //Repositories
        $this->orgCourseRepository = $this->repositoryResolver->getRepository(OrgCoursesRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);

    }

    private function getMulticampusPersons($personsObj)
    {
        $multiCampusPersons = array();
        foreach ($personsObj as $personObj) {
            $orgTier = $personObj->getOrganization()->getTier();
            if ($orgTier > 0) {
                $multiCampusPersons[] = $personObj->getId();
            }
        }
        return $multiCampusPersons;
    }

    public function getIsMulticampusUser($loggedUserId)
    {
        $this->logger->info(">>>> Get Is Logged In User Id - Multi Campus User" );
        $isMulticampusUser = '';
        $this->personRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Person');
        $this->tierUserRepository = $this->repositoryResolver->getRepository('SynapseMultiCampusBundle:OrgUsers');
        $person = $this->personRepository->find($loggedUserId);
        $personsObj = $this->personRepository->findBy([
            'username' => $person->getUsername(),
            'externalId' => $person->getExternalId()
        ]);
        $multiCampusPersons = $this->getMulticampusPersons($personsObj);
        if (count($multiCampusPersons) > 1) {
            $isMulticampusUser = true;
        } else {
            $personStudentFaculty = $this->personRepository->getConflictPersonsByRole($multiCampusPersons);
            $organization = [];
            if ($personStudentFaculty) {
                $studentOrg = array_column($personStudentFaculty, 'studentOrg');
                $facultyOrg = array_column($personStudentFaculty, 'facultyOrg');
                $organizations = array_unique(array_merge($facultyOrg, $studentOrg));
                $organization = array_values(array_filter($organizations));
            }
            if (count($organization) > 1) {
                $isMulticampusUser = true;
            }
            $isTierUser = $this->tierUserRepository->findBy([
                'person' => $multiCampusPersons
            ]);
            $tierUserCount = count($isTierUser);
            $tierAndCampusCount = $tierUserCount + count($organization);
            if ($tierUserCount > 1 || $tierAndCampusCount > 1) {                
                if(count($organization) == 1 && count($isTierUser) == 1)
                {
                    $tierOrganization[] = $isTierUser[0]->getOrganization()->getId();
                    if(!empty(array_diff($tierOrganization, $organization)))
                    {
                        $isMulticampusUser = true;
                    }                    
                }else {
                    $isMulticampusUser = true;
                }
            }
        }
        $this->logger->info(">>>> Get Is Logged In User Id - Multi Campus User" );
        return $isMulticampusUser;
    }

    public function getUserTierType($loggedUserId)
    {
        $this->logger->info(">>>> Get Logged User Id Tier Type" );
        $tierUserType = array();
        $tierLevel = '';
        $orgRepository = $this->repositoryResolver->getRepository('SynapseMultiCampusBundle:OrgUsers');
        $tierUser = $orgRepository->findBy(array(
            'person' => $loggedUserId
        ));
        foreach ($tierUser as $user) {
            if ($user->getOrganization()->getTier() == 1) {
                $tierUserType[] = PersonConstant::FILTER_PRIMARY;
            } elseif ($user->getOrganization()->getTier() == 2) {
                $tierUserType[] = PersonConstant::FILTER_SECONDARY;
            }
        }
        $tiers = array_unique($tierUserType);
        if (count($tiers) > 1 && in_array(PersonConstant::FILTER_PRIMARY, $tiers)) {
            $tierLevel = PersonConstant::FILTER_PRIMARY;
        } elseif (count($tiers) == 1 && in_array(PersonConstant::FILTER_PRIMARY, $tiers)) {
            $tierLevel = PersonConstant::FILTER_PRIMARY;
        } elseif (count($tiers) == 1 && in_array(PersonConstant::FILTER_SECONDARY, $tiers)) {
            $tierLevel = PersonConstant::FILTER_SECONDARY;
        } else {
            $tierLevel = '';
        }
        $this->logger->info(">>>> Get Logged User Id Tier Type" );
        return $tierLevel;
    }


    /**
     * Get user permission data
     *
     * @param int $loggedUserId
     * @param string $type - Coordinator | Staff | null
     * @return array
     */
    public function getUserPermissionTemplates($loggedUserId, $type)
    {
        $permissionTemplates = $this->orgPermissionSetService->getPermissionSetsByUser($loggedUserId);
        $permissions = [];
        $templates = [];
        $type = explode(',', $type);

        if (in_array('Coordinator', $type)) {
            $permissions['permission'] = 'Coordinator';
        } elseif (in_array('Staff', $type)) {
            foreach ($permissionTemplates['permission_templates'] as $permission) {
                $permissionArray = array();
                $permissionArray['permission_template_id'] = $permission->getPermissionTemplateId();
                $permissionArray['permission_template_name'] = $permission->getPermissionTemplateName();
                $templates[] = $permissionArray;
            }
            $permissions['permission'] = 'Staff';
        } else {
            $permissions['permission'] = 'Student';
        }
        $permissions['templates'] = $templates;
        return $permissions;
    }

    /**
     * Check if user is associated with any courses. If so, return True.
     *
     * @param int $loggedUserId
     * @param int $orgId
     * @param string $type
     * @return bool
     */
    public function getOrgPersonCourseTabPermission($loggedUserId, $orgId, $type)
    {
        $type = explode(',', $type);

        if (in_array('Coordinator', $type) || in_array('Staff', $type)) {
            $userType = 'faculty';
        } else {
            $userType = '';
        }

        if ($userType == 'faculty') {
            $courseListCount = count($this->orgCourseRepository->getCoursesForFaculty($orgId, $loggedUserId));
        } else {
            $courseListCount = 0;
        }

        if ($courseListCount > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  Get privacy policy details of a student or faculty
     *
     * @param int $loggedUserId
     * @param int $orgId
     * @param string $userType
     * @return array
     */
    public function getPrivacyPolicy($loggedUserId, $orgId, $userType){
        $privacy['is_accepted'] = false;
        $privacy['accepted_date'] = NULL;
        $type = explode(',', $userType);
        if (in_array('Coordinator', $type) || in_array('Staff', $type)) {
            $orgPersonPrivacyPolicy = $this->orgPersonFacultyRepository->findOneBy([
                'person' => $loggedUserId,
                'organization' => $orgId,
            ]);
        } elseif (in_array('Student', $type)) {
            $orgPersonPrivacyPolicy = $this->orgPersonStudentRepository->findOneBy([
                'person' => $loggedUserId,
                'organization' => $orgId,
            ]);
        }

        if ($orgPersonPrivacyPolicy) {
            $privacyPolicy = $orgPersonPrivacyPolicy->getIsPrivacyPolicyAccepted();
            $privacy['accepted_date'] = $orgPersonPrivacyPolicy->getPrivacyPolicyAcceptedDate();
            $privacy['is_accepted'] = ($privacyPolicy == 'y') ? true : false;
        }
        return $privacy;
	}
}