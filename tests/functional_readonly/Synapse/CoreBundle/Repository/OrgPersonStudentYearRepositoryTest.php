<?php
use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\SynapseConstant;

class OrgPersonStudentYearRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
    }

    public function testgetParticipantStudentsFromStudentList()
    {
        $this->specify("TestgetParticipantStudentsFromStudentList ", function ($studentArray, $orgId, $orgAcademicYearId, $expectedResult) {
            $result = $this->orgPersonStudentYearRepository->getParticipantStudentsFromStudentList($studentArray, $orgId, $orgAcademicYearId);
            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                [[4878808, 4878809, 4878810], 203, 157, [4878808, 4878809, 4878810]],
                [[4878841, 4878905, 4878969], 203, 158, [4878841, 4878905]],
                [[4878808, 4878809, 4878810], 203, 158, [4878808, 4878809, 4878810]]
            ]
        ]);
    }

    public function testDoesStudentIdListContainNonParticipants()
    {

        $this->specify("Verify the functionality of the method doesStudentIdListContainNonParticipants", function ($studentIds, $orgAcademicYearId, $expectedResult) {

            $result = $this->orgPersonStudentYearRepository->doesStudentIdListContainNonParticipants($studentIds, $orgAcademicYearId);
            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                [[4878808, 4878809, 4897396], 157, false]
            ]
        ]);
    }

    public function testGetParticipantAndActiveStudents()
    {
        $this->specify("Test get Participant And Active Students", function ($orgId, $orgAcademicYearId, $onlyActiveStudents, $expectedResult) {
            $result = $this->orgPersonStudentYearRepository->getParticipantAndActiveStudents($orgId, $orgAcademicYearId, $onlyActiveStudents);
            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                [203, 157, false, 1050],
                [203, 158, false, 1048],
                [203, 158, true, 1046]
            ]
        ]);
    }


    public function testGetActiveStatusForStudentList()
    {
        $this->specify("Test Get Active Status For StudentList ", function ($studentIds, $organizationId, $orgAcademicYearId, $expectedResult) {
            $result = $this->orgPersonStudentYearRepository->getActiveStatusForStudentList($organizationId, $orgAcademicYearId, $studentIds);
            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                // Get all active status for student lists
                [
                    [4878808, 4878809, 4878810],
                    203,
                    157,
                    [
                        4878808 => '1',
                        4878809 => '1',
                        4878810 => '1'
                    ]
                ],
                // Get all active|inactive status for student lists
                [
                    [4878841, 4878905, 4878969],
                    203,
                    158,
                    [
                        4878841 => '1',
                        4878905 => '0'
                    ]
                ],
                // If no student ids are passed
                [
                    [],
                    203,
                    158,
                    []
                ],
                // If no organization_id is passed
                [
                    [4878841, 4878905, 4878969],
                    null,
                    158,
                    []
                ],
                // If no org_academic_year is passed
                [
                    [4878841, 4878905, 4878969],
                    203,
                    null,
                    []
                ],
            ]
        ]);
    }

}
