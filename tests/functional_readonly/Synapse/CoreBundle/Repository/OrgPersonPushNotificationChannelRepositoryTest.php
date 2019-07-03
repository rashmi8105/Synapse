<?php
use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Repository\OrgPersonPushNotificationChannelRepository;


class OrgPersonPushNotificationChannelRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     *
     * @var OrgPersonPushNotificationChannelRepository
     */
    private $orgPersonPushNotificationChannelRepository;

    public function testGetChannelNameForUser()
    {
        $this->beforeSpecify(
            function () {
                $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
                $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
                $this->orgPersonPushNotificationChannelRepository = $this->repositoryResolver->getRepository(OrgPersonPushNotificationChannelRepository::REPOSITORY_KEY);
            }
        );

        $this->specify(
            "Verify the functionality of the method GetChannelNameForUser, Gets all the notification channels for the person",
            function ($personId, $organizationId, $expectedResult) {
                $channelList = $this->orgPersonPushNotificationChannelRepository->getChannelNameForUser($personId, $organizationId);
                verify($channelList)->equals($expectedResult);
            },
            [
                "examples" => [
                    // Example 1 : Gets recent five channels based on person_id and organization_id
                    [
                        113802,
                        99,
                        [
                            '99-113802-57F5D566-4499-4903-C1E9-17539066E9D2',
                            '99-113802-57F2D544-4519-4873-B1F9-17539066F8D1',
                            '99-113802-BFA82F18-8C21-4971-858A-FB2F47FA79FF',
                            '99-113802-63BE004C-5584-4ADF-A7D0-670CCB512304',
                            '99-113802-069EDAE5-E348-41AF-A616-A29F3576FBAD'
                        ]
                    ],
                    // Example 2 : If records not found, returns blank array
                    [
                        220,
                        96,
                        []
                    ],
                    // Example 3 : If person_id is null and organization_id, returns blank array
                    [
                        null,
                        99,
                        []
                    ],
                    // Example 4 : If organization_id is null and person_id, returns blank array
                    [
                        113802,
                        null,
                        []
                    ],
                    // Example 5 : Test with null data
                    [
                        null,
                        null,
                        []
                    ]
                ],
            ]
        );
    }
}
