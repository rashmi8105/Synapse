<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\MapworksToolBundle\Repository\MapworksToolLastRunRepository;

class MapworksToolLastRunRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Container
     */
    private $container;

    /**
     *
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var MapworksToolLastRunRepository
     */
    private $mapworksToolLastRunRepository;

    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->mapworksToolLastRunRepository = $this->repositoryResolver->getRepository(MapworksToolLastRunRepository::REPOSITORY_KEY);
    }

}
