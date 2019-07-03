<?php
use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\StaticListBundle\Repository\OrgStaticListStudentsRepository;

class OrgStaticListStudentRepository extends Test
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
     * @var OrgStaticListStudentsRepository
     */
    private $orgStaticListStudentsRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->orgStaticListStudentsRepository = $this->repositoryResolver->getRepository(OrgStaticListStudentsRepository::REPOSITORY_KEY);

    }

    public function testGetStaticListStudents()
    {
        $this->specify('', function ($orgStaticListId, $faultyId, $orgAcademicYearId, $expectedResult) {
            $result = $this->orgStaticListStudentsRepository->getStaticListStudents($orgStaticListId, $faultyId, $orgAcademicYearId);
            $staticListStudent = array_column($result, 'student_id');
            verify($staticListStudent)->equals($expectedResult);

        }, ['examples' => [
                // both the students are participant for academic year  92
                [9, 174388, 92, [4622752, 971065]],
                // student is participant for the academic year 44
                [11, 4551171, 44, [4543345]],
                // student is not participant for the academic year 74
                [11, 4551171, 74, []],
                // student is participant for the academic year 85
                [11, 4551171, 85, []]
            ]]
        );
    }

}
