<?php

use \Synapse\CoreBundle\Repository\OrgFeaturesRepository;

class OrgFeaturesRepositoryTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\CoreBundle\Repository\OrgFeaturesRepository
     */
    private $orgFeaturesRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgFeaturesRepository = $this->repositoryResolver->getRepository(OrgFeaturesRepository::REPOSITORY_KEY);
    }

    public function testIsFeatureEnabledForOrganization()
    {
        $this->specify('test is feature enabled for organization ', function ($organizationId, $featureName, $expectedResult) {
            $functionResult = $this->orgFeaturesRepository->isFeatureEnabledForOrganization($organizationId, $featureName);
            verify($expectedResult)->equals($functionResult);
        }, ['examples' => [
            [
                196,
                "Referrals",
                true
            ],
            [
                196,
                "Notes",
                true
            ],
            [
                196,
                "Log Contacts",
                true
            ],
            [
                196,
                "Booking",
                true
            ],
            [
                196,
                "Student Referrals",
                false
            ],
            [
                196,
                "Reason Routing",
                true
            ],
            [
                196,
                "Email",
                true
            ],
            [
                196,
                "Primary Campus Connection Referral Routing",
                true
            ],
            [
                99,
                "Referrals",
                true
            ],
            [
                99,
                "Notes",
                true
            ],
            [
                99,
                "Log Contacts",
                true
            ],
            [
                99,
                "Booking",
                true
            ],
            [
                99,
                "Student Referrals",
                false
            ],
            [
                99,
                "Reason Routing",
                true
            ],
            [
                99,
                "Email",
                true
            ],
            [
                99,
                "Primary Campus Connection Referral Routing",
                false
            ]
        ]]);
    }
}