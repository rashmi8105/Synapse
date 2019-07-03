<?php

/**
 * Class StudentServiceTest
 */

use Codeception\TestCase\Test;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\RestBundle\Entity\OrgGroupDto;

class StudentServiceTest extends Test
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
     * @var \Synapse\CoreBundle\Service\Impl\StudentService
     */
    private $studentService;

    /**
     * @var \Synapse\CoreBundle\Repository\OrgGroupStudentsRepository
     */
    private $orgGroupStudentsRepository;

    public function testStudentService()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->studentService = $this->container->get('student_service');
            $this->orgGroupStudentsRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupStudents');
        });

        $this->specify("Verify the functionality of the method fro deletGroup", function ($studentId, $parentGroupId, $childGroupId) {


            $this->studentService->addGroup($studentId, $childGroupId);
            $checkStudent = $this->orgGroupStudentsRepository->findOneBy([
                'person' => $studentId,
                'orgGroup' => $childGroupId
            ]);
            verify($checkStudent)->notEmpty();
            $this->studentService->removeGroup($studentId, $parentGroupId);

            $checkStudent = $this->orgGroupStudentsRepository->findOneBy([
                'person' => $studentId,
                'orgGroup' => $childGroupId
            ]);
            verify($checkStudent)->isEmpty();

        }, ["examples" =>
            [
                [4878808, 370628, 370629],
                [4878813, 370628, 370630],
                [4878808, 370628, 370628],
            ]
        ]);

    }

}