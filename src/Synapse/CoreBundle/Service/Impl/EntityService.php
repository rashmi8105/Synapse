<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\EntityServiceInterface;
use Synapse\CoreBundle\Entity\Entity;

/**
 * @DI\Service("entity_service")
 */
class EntityService extends AbstractService implements EntityServiceInterface
{

    const SERVICE_KEY = 'entity_service';

    /**
     *
     * @var entityRepository
     */
    private $entityRepository;

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
        parent::__construct($repositoryResolver, $logger);
        $this->entityRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Entity");
    }

    /**
     *
     * @return EntityRepository
     */
    public function getEntityRepository()
    {
        $this->logger->info(">>>>Get Entity Repository");
        return $this->entityRepository;
    }

    /**
     * Get entity by name
     */
    public function findOneByName($name)
    {
        $this->logger->debug(">>>>Find One By Name" . $name);
        return $this->entityRepository->findOneByName($name);
    }

    public function getUserTypeById($userType)
    {
        $this->logger->debug(">>>>Get UserType by Id" . $userType);
        return $this->entityRepository->getUserTypeById($userType);
    }
}
