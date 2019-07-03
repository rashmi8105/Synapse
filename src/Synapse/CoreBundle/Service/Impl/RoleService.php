<?php

namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;

/**
 * @DI\Service("role_service")
 */
class RoleService extends AbstractService
{

    const SERVICE_KEY = 'role_service';


    // Scaffolding

    /**
     * @var Container
     */
    private $container;


    // Repositories

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;


    /**
     * RoleService constructor.
     *
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

        // Repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
    }


    /**
     * Returns an associative array representing the roles a given user has in the format
     * [
     *      'coordinator' => false,
     *      'faculty' => true,
     *      'student' => false
     * ]
     *
     * @param int $personId
     * @return array
     */
    public function getRolesForUser($personId)
    {
        $roleArray = [
            'coordinator' => false,
            'faculty' => false,
            'student' => false
        ];

        $organizationRoleObject = $this->organizationRoleRepository->findOneBy(['person' => $personId]);

        if ($organizationRoleObject) {
            $roleArray['coordinator'] = true;
        }

        $orgPersonFacultyObject = $this->orgPersonFacultyRepository->findOneBy(['person' => $personId]);

        if ($orgPersonFacultyObject) {
            $roleArray['faculty'] = true;
        }

        $orgPersonStudentObject = $this->orgPersonStudentRepository->findOneBy(['person' => $personId]);

        if ($orgPersonStudentObject) {
            $roleArray['student'] = true;
        }

        return $roleArray;
    }


    /**
     * Returns true if coordinator omniscience is in place system-wide and the given person is a coordinator.
     * Otherwise returns false.
     *
     * @param int $personId
     * @return bool
     */
    public function hasCoordinatorOmniscience($personId)
    {
        $coordinatorOmniscience = false;
        $personHasCoordinatorOmniscience = false;

        $ebiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Coordinator_Omniscience']);
        if ($ebiConfigObject) {
            $coordinatorOmniscience = $ebiConfigObject->getValue();
        }

        if ($coordinatorOmniscience) {
            $organizationRoleObject = $this->organizationRoleRepository->findOneBy(['person' => $personId]);
            if ($organizationRoleObject) {
                $personHasCoordinatorOmniscience = true;
            }
        }

        return $personHasCoordinatorOmniscience;
    }

}