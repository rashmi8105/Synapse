<?php

use Codeception\TestCase\Test;

class RoleServiceTest extends Test
{
    use Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\CoreBundle\Service\Impl\RoleService
     */
    private $roleService;

    /**
     * @var \Synapse\CoreBundle\Repository\EbiConfigRepository
     */
    private $ebiConfigRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');

        $this->roleService = $this->container->get('role_service');

        $this->ebiConfigRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:EbiConfig');
    }


    public function testHasCoordinatorOmniscience()
    {
        $this->specify("Verify the functionality of the method hasCoordinatorOmniscience", function ($personId, $expectedResult) {

            $result = $this->roleService->hasCoordinatorOmniscience($personId);

            $coordinatorOmniscience = false;

            $ebiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Coordinator_Omniscience']);
            if ($ebiConfigObject) {
                $coordinatorOmniscience = $ebiConfigObject->getValue();
            }

            if ($coordinatorOmniscience) {
                verify($result)->equals($expectedResult);
            } else {
                $this->assertFalse($result);
            }

        }, ["examples" =>
            [
                // Example 1:  Primary coordinator
                [202267, true],
                // Example 2:  Technical coordinator
                [123338, true],
                // Example 3:  This person was a coordinator but the record in organization_role was soft-deleted.
                [153684, false],
                // Example 4:  This person is a faculty member but not a coordinator.
                [229811, false]
            ]
        ]);
    }
}