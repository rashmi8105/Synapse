<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\UserManagementService;
use Synapse\CoreBundle\SynapseConstant;

class UserManagementServiceTest extends Test
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
     * @var UserManagementService
     */
    private $userManagementService;

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);

        $this->userManagementService = $this->container->get(UserManagementService::SERVICE_KEY);

        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
    }


    public function testIsStudentActive()
    {
        $this->specify("Verify the functionality of the method isStudentActive when longitudinal student management is not in place", function ($studentId, $expectedResult) {

            $longitudinalStudentManagement = false;

            $ebiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Longitudinal_Student_Management']);
            if ($ebiConfigObject) {
                $longitudinalStudentManagement = $ebiConfigObject->getValue();
            }

            if ($longitudinalStudentManagement) {
                $this->assertTrue(true);
            } else {
                $result = $this->userManagementService->isStudentActive($studentId);
                verify($result)->equals($expectedResult);
            }

        }, ["examples" =>
            [
                // Example 1: This student is active (status 1 in org_person_student)
                [4621280, true],
                // Example 2: This student is inactive (status 0 in org_person_student)
                [272634, false],
                // Example 3: This student was soft-deleted from org_person_student
                [273886, false],
                // Example 4: This person is not a student (no record in org_person_student)
                [136859, false]
            ]
        ]);
    }


    // WARNING: This test will start failing when the 201617 academic year ends for org 203 (2017-07-28).
    public function testIsStudentActiveLongitudinal()
    {
        $this->specify("Verify the functionality of the method isStudentActive when longitudinal student management is in place", function ($studentId, $expectedResult) {

            $longitudinalStudentManagement = false;

            $ebiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Longitudinal_Student_Management']);
            if ($ebiConfigObject) {
                $longitudinalStudentManagement = $ebiConfigObject->getValue();
            }

            if ($longitudinalStudentManagement) {
                $result = $this->userManagementService->isStudentActive($studentId);
                verify($result)->equals($expectedResult);
            } else {
                $this->assertTrue(true);
            }


        }, ["examples" =>
            [
                // Example 1: This student is active up to 2020 (1 in org_person_student_year)
                [4878841, true],
                // Example 2: This student is inactive up to 2020  (0 in org_person_student_year)
                [4878905, false],
                // Example 3: This student is archived up to 2020 (has a deleted_at value in org_person_student_year)
                [4878969, false],
                // Example 4: This student is archived up to 2020 (not in org_person_student_year for 201617)
                [4879803, false]
            ]
        ]);
    }


    public function testIsStudentMemberOfCurrentAcademicYear()
    {
        $this->specify("Verify the functionality of the method testIsStudentMemberOfCurrentAcademicYear when longitudinal student management is not in place", function ($studentId, $expectedResult) {

            $longitudinalStudentManagement = false;

            $ebiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Longitudinal_Student_Management']);
            if ($ebiConfigObject) {
                $longitudinalStudentManagement = $ebiConfigObject->getValue();
            }

            if ($longitudinalStudentManagement) {
                $this->assertTrue(true);
            } else {
                $result = $this->userManagementService->isStudentMemberOfCurrentAcademicYear($studentId);
                verify($result)->equals($expectedResult);
            }

        }, ["examples" =>
            [
                // Example 1: This student is active (status 1 in org_person_student)
                [4621280, true],
                // Example 2: This student is inactive (status 0 in org_person_student)
                [272634, true],
                // Example 3: This student was soft-deleted from org_person_student
                [273886, false],
                // Example 4: This person is not a student (no record in org_person_student)
                [136859, false]
            ]
        ]);
    }

    // WARNING: This test will start failing when the 201617 academic year ends for org 203 (2017-07-28).
    public function testIsStudentMemberOfCurrentAcademicYearLongitudinal()
    {
        $this->specify("Verify the functionality of the method isStudentMemberOfCurrentAcademicYear when longitudinal student management is in place", function ($studentId, $expectedResult) {

            $longitudinalStudentManagement = false;

            $ebiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Longitudinal_Student_Management']);
            if ($ebiConfigObject) {
                $longitudinalStudentManagement = $ebiConfigObject->getValue();
            }

            if ($longitudinalStudentManagement) {
                $result = $this->userManagementService->isStudentMemberOfCurrentAcademicYear($studentId);
                verify($result)->equals($expectedResult);
            } else {
                $this->assertTrue(true);
            }


        }, ["examples" =>
            [
                // Example 1: This student is active for 201617 (1 in org_person_student_year)
                [4878841, true],
                // Example 2: This student is inactive for 201617 (0 in org_person_student_year)
                [4878905, true],
                // Example 3: This student is archived for 201617 (has a deleted_at value in org_person_student_year)
                [4878969, false],
                // Example 4: This student is archived for 201617 (not in org_person_student_year for 201617)
                [4879803, false]
            ]
        ]);
    }


    public function testIsUserAllowedToLogin()
    {
        $this->specify("Verify the functionality of the method isUserAllowedToLogin ", function ($studentId, $expectedResult) {

            $result = $this->userManagementService->isUserAllowedToLogin($studentId);
            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                //inactive faculty
                [105963, false],

                //inactive faculty
                [97379, false],

                //active faculty
                [118256, true],

                // dual role , with faculty not active , but student role  is participant (up to 2020)
                [141932, true],

                //inactive student
                [7, false],

                //active student
                [132752, true]
            ]
        ]);
    }

}