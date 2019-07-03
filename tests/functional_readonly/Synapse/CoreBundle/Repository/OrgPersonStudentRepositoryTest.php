<?php
use Codeception\TestCase\Test;

class OrgPersonStudentRepositoryTest extends Test
{

    use Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Dump\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\CoreBundle\Repository\OrgPersonStudentRepository
     */
    private $orgPersonStudentsRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgPersonStudentsRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgPersonStudent");
    }

    public function testGetCampusDetails()
    {
        $this->specify("Verify the functionality of the method getStudentsByGroups", function ($studentId, $expectedResult) {
            $results = $this->orgPersonStudentsRepository->getCampusDetails($studentId);
            verify($results)->contains($expectedResult);

        }, ["examples" =>
            [
                ['4879305', [
                    'person_id' => 4879305,
                    'organization_id' => 203,
                    'campus_id' => 1234567890,
                    'organization_name' => 'Synapse Beta Org: 0204'
                ]
                ],
                ['4878822', [
                    'person_id' => 4878822,
                    'organization_id' => 203,
                    'campus_id' => 1234567890,
                    'organization_name' => 'Synapse Beta Org: 0204'
                ]
                ],
                ['4879609', [
                    'person_id' => 4879609,
                    'organization_id' => 203,
                    'campus_id' => 1234567890,
                    'organization_name' => 'Synapse Beta Org: 0204'
                ]
                ]
            ]
        ]);
    }
}