<?php

class OrgCronofyCalendarRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\CalendarBundle\Repository\OrgCronofyCalendarRepository
     */
    private $orgCronofyCalendarRepository;

    
    public function testGetListOfCronofyCalendarSyncUsers()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->orgCronofyCalendarRepository = $this->repositoryResolver->getRepository('SynapseCalendarBundle:OrgCronofyCalendar');
        });
        $this->specify("Verify the functionality of the method getListOfCronofyCalendarSyncUsers", function ($organizationId, $expectedResultsSize) {

            $results = $this->orgCronofyCalendarRepository->getListOfCronofyCalendarSyncUsers($organizationId);
            verify(count($results))->equals($expectedResultsSize);

        }, [
            "examples" => [
                [203, 0]
            ]

        ]);
    }
}