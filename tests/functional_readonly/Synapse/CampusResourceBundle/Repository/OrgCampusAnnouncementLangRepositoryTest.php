<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CampusResourceBundle\Repository\OrgCampusAnnouncementLangRepository;
use Synapse\CoreBundle\SynapseConstant;

class OrgCampusAnnouncementLangRepositoryTest extends Test
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
     * @var OrgCampusAnnouncementLangRepository
     */
    private $orgCampusAnnouncementLangRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->orgCampusAnnouncementLangRepository = $this->repositoryResolver->getRepository(OrgCampusAnnouncementLangRepository::REPOSITORY_KEY);
    }

    public function testListBannerOrgAnnouncements()
    {
        $this->specify("test listBannerOrgAnnouncements", function ($expectedResults, $organizationId = null, $personId = null, $currentDateTime = null) {
            $functionResults = $this->orgCampusAnnouncementLangRepository->listBannerOrgAnnouncements($organizationId, $personId, $currentDateTime);
            verify($functionResults)->equals($expectedResults);
        }, [
            'examples' =>
                [
                    //Valid data
                    [
                        [
                            [
                                'start_datetime' => '2015-07-20 20:09:01',
                                'stop_datetime' => '2015-08-19 13:30:00',
                                'display_type' => 'banner',
                                'message' => 'I have an announcement!!!',
                                'message_duration' => 'custom',
                                'org_announcements_id' => '1'
                            ]
                        ],
                        116,
                        111042,
                        '2015-08-01 00:00:00'
                    ],
                    //Invalid org ID
                    [
                        [],
                        null,
                        111042,
                        '2015-08-01 00:00:00'
                    ],
                    //Invalid person ID - should return all campus announcements
                    [
                        [
                            [
                                'start_datetime' => '2015-07-20 20:09:01',
                                'stop_datetime' => '2015-08-19 13:30:00',
                                'display_type' => 'banner',
                                'message' => 'I have an announcement!!!',
                                'message_duration' => 'custom',
                                'org_announcements_id' => '1'
                            ]
                        ],
                        116,
                        null,
                        '2015-08-01 00:00:00'
                    ],
                    //Invalid datetime
                    [
                        [],
                        116,
                        111042
                    ],
                ]
            ]
        );
    }

}