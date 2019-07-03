<?php

use Codeception\TestCase\Test;
use Synapse\CampusResourceBundle\Repository\OrgCampusAnnouncementRepository;

class OrgAnnouncementsRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     *
     * @var OrgCampusAnnouncementRepository
     */
    private $orgAnnouncementsRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgAnnouncementsRepository = $this->repositoryResolver->getRepository(OrgCampusAnnouncementRepository::REPOSITORY_KEY);
    }

    public function testCreateOrgCampusAnnouncementsAndValidateOrganization()
    {
        $this->specify("Verify the functionality to create org announcements and validate organization id", function ($organizationId) {

            $orgAnnouncementsArray = $this->orgAnnouncementsRepository->findBy(array("orgId" => $organizationId));

            foreach ($orgAnnouncementsArray as $orgAnnouncements)
            {
                verify($orgAnnouncements)->notNull();
                verify($orgAnnouncements->getOrganization()->getId())->equals($organizationId);
            }

        }, ["examples" =>
            [
                [199],
                [181],
                [195],
                [163]
            ]
        ]);
    }
}