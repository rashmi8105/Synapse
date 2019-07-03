<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RoleLangRepository;

/**
 * @DI\Service("ebi_user_service")
 */
class EbiUserService extends AbstractService
{
    const SERVICE_KEY = 'ebi_user_service';

    // Repositories
    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var RoleLangRepository
     */
    private $roleLangRepository;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger")
     *            })
     */
    public function __construct($repositoryResolver, $logger)
    {
        // scaffolding
        parent::__construct($repositoryResolver, $logger);

        // Repositories
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->roleLangRepository = $this->repositoryResolver->getRepository(RoleLangRepository::REPOSITORY_KEY);
    }

    public function isEbIUser($userId)
    {
        $this->logger->debug(">>>>Is EBI User" . $userId);
        // $this->ebiUserRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:EbiUsers");
        $this->personRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Person");

        // We can have check organization role chek as well, As of now -1 is and Ebi User
        $ebiUser = $this->personRepository->findBy(array(
            'id' => $userId,
            'organization' => - 1
        ));
        if ($ebiUser) {
            return true;
        }
        return false;
    }
    
    public function isARTUser($userId)
    {
        $this->logger->debug(">>>>Is EBI User" . $userId);
        // $this->ebiUserRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:EbiUsers");
        $this->personRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Person");
    
        // We can have check organization role chek as well, As of now -1 is and Ebi User
        $ebiUser = $this->personRepository->findBy(array(
            'id' => $userId,
            'organization' => '-2'
        ));
        if ($ebiUser) {
            return true;
        }
        return false;
    }

    /**
     * This method determines user is Skyfactor Admin based on user id
     *
     * @param int $userId
     * @return bool
     */
    public function isSkyfactorUser($userId)
    {
        $personObject = $this->personRepository->findOneBy(
            [
                'id' => $userId,
                'organization' => -1
            ]);
        if ($personObject) {
            $organizationRole = $this->organizationRoleRepository->findOneBy(['person' => $personObject->getId()]);
            $roleLangObject = $this->roleLangRepository->findOneBy(['role' => $organizationRole->getRole()->getId()]);
            if ($roleLangObject->getRoleName() == 'Skyfactor Admin') {
                return true;
            }
        }
        return false;
    }
}