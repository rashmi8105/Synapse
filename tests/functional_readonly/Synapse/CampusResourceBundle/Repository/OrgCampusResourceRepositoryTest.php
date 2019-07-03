<?php
use Codeception\TestCase\Test;

/**
 * Class OrgCampusResourceRepositoryTest
 */
class OrgCampusResourceRepositoryTest extends Test
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
     * @var \Synapse\CampusResourceBundle\Repository\OrgCampusResourceRepository
     */
    private $orgCampusResourceRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgCampusResourceRepository = $this->repositoryResolver->getRepository('SynapseCampusResourceBundle:OrgCampusResource');
    }

    public function testGetCampusResourcesForReferralCreation()
    {
        $this->specify("Verify the functionality of the method getCampusResourcesForReferralCreation", function ($organizationId, $studentIds, $expectedResultsSize, $expectedIds) {

            $results = $this->orgCampusResourceRepository->getCampusResourcesForReferralCreation($organizationId, $studentIds);
            verify(count($results))->equals($expectedResultsSize);
            for ($i = 0; $i < count($expectedIds); $i++) {
                verify($results[$i]['faculty_id'])->notEmpty();
                verify($results[$i]['faculty_id'])->equals($expectedIds[$i]);
            }
        }, ["examples" =>
            [
                [203, [4879886, 4879863, 4879854],1,[4878750]],
                [190, [4708233,4708234], 3,[4708973,4708796,4708715]]
            ]
        ]);
    }

    public function testGetCampusResources()
    {
        $this->specify("Verify the functionality of the method getCampusResources", function ($organizationId, $expectedResourceIds, $expectedCount) {

            $results = $this->orgCampusResourceRepository->getCampusResources($organizationId, 2);

            verify(count($results))->equals($expectedCount);
            for ($i = 0; $i < count($results); $i++) {
                verify($results[$i]['staff_id'])->notEmpty();
                verify($results[$i]['staff_id'])->equals($expectedResourceIds[$i]);
            }
        }, ["examples" =>
            [
                //Test case 0: testing campus resources for organization 203
                [203, [4878750], 1],
                //Test case 1: testing campus resources for organization 190
                [190, [4709481, 4709234, 4708962, 4708993, 4708796, 4709220, 4709459, 4708887, 4709167, 4708973, 4709088, 4709014, 4708715, 4708893, 4708843, 4708783, 4708717], 17],
                //Test case 2: testing campus resources for organization 189
                [189, [4725756, 4725779, 4725683, 4725738, 256048, 4725690, 4725711], 7],
                //Test case 3: All values are given as null will return empty result array and expected count 0
                [null, [], 0]
            ]
        ]);
    }

    public function testGetSingleCampusVisibleResource()
    {
        $this->specify("Verify the functionality of the method getSingleCampusVisibleResource", function ($organizationId, $expectedResourceIds, $expectedCount) {

            $results = $this->orgCampusResourceRepository->getSingleCampusVisibleResource($organizationId);

            verify(count($results))->equals($expectedCount);
            for ($i = 0; $i < count($results); $i++) {
                verify($results[$i]['staff_id'])->notEmpty();
                verify($results[$i]['staff_id'])->equals($expectedResourceIds[$i]);
                verify($results[$i]['resource_email'])->notEmpty();
            }
        }, ["examples" =>
            [
                //Test case 0: testing campus visible resources for organization 203
                [203, [4878750], 1],
                //Test case 1: testing campus visible resources for organization 190
                [190, [4709481, 4709234, 4708962, 4708993, 4708796, 4709220, 4709459, 4708887, 4709167, 4708973, 4709088, 4709014, 4708715, 4708893, 4708843, 4708783, 4708717], 17],
                //Test case 2: testing campus visible resources for organization 189
                [189, [4725738, 256048], 2],
                //Test case 3: Organization id as null will return empty result array and expected count 0
                [null, [], 0]

            ]
        ]);
    }



}