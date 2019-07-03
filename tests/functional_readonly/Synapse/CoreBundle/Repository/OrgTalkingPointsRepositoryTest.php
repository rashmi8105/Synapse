<?php


class OrgTalkingPointsRepositoryTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\CoreBundle\Repository\OrgTalkingPointsRepository
     */
    private $orgTalkingPointsRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgTalkingPointsRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgTalkingPoints');
    }


    public function testGetLastOrgTalkingPointIdBasedOnStudentAndProfileItem()
    {

        $this->specify("Verify the repository retrieves known example", function ($orgId, $personId, $metadataId, $orgAcademicYearId, $orgAcademicTermId, $expectedIds) {

            $results = $this->orgTalkingPointsRepository->getLastOrgTalkingPointIdBasedOnStudentAndProfileItem($orgId, $personId, $metadataId, $orgAcademicYearId, $orgAcademicTermId);

            verify($results)->equals($expectedIds);
        }, ["examples" =>
            [
                [203, 4878828, 35, null, null, 7260639],
                [203, 4878828, null, null, null, null],
                [2, 4591688, 83, 1, 365, 7000039]
            ]
        ]);
    }


}