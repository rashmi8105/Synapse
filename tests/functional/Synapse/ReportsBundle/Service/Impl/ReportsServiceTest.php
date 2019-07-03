<?php

/**
 * Class ReportsServiceTest
 */

use Codeception\TestCase\Test;

class ReportsServiceTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     *
     * @var Logger logger
     */
    protected $logger;

    /**
     * @var \Synapse\ReportsBundle\Service\Impl\ReportsService
     */
    private $reportsService;

    /**
     * @var int
     */
    private $personId = 4878750;

    /**
     * @var int
     */
    private $orgId = 203;

    public function testGetCampusActivity()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->reportsService = $this->container->get('reports_service');
            $rbacMan = $this->container->get('tinyrbac.manager');
            $rbacMan->initializeForUser($this->personId);
        });

        $this->specify("Verify the functionality of the method getCampusActivity", function ($yearId ,$expectedResultsSize)
        {
            $resultSet = $this->reportsService->getCampusActivity($yearId, '', '', '', '', $this->personId,'', '');
            verify($resultSet)->isInstanceOf("Synapse\ReportsBundle\EntityDto\CampusActivityDto");
            verify($resultSet->getTotalRecords())->equals($expectedResultsSize);

        }, ["examples" =>
            [
                ['201617', 1],
                ['201516', 0],
                ['', 28]
            ]
        ]);
    }
}