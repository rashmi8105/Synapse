<?php
use Synapse\AcademicBundle\Service\Impl\CourseFacultyStudentValidatorService;

use Symfony\Bridge\Monolog\Logger;
use Synapse\RestBundle\Exception\ValidationException;

class CourseFacultyStudentValidatorServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testValidateAdditionOfFacultyToCourse()
    {
        $this->specify("Test to given person ID is not already in a course as a faculty member or student", function ($personId, $orgId, $courseId) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'error'
            ));

            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockOrgCourseStudentRepository = $this->getMock('OrgCourseStudentRepository', array(
                'findOneBy'
            ));

            $mockOrgCourseFacultyRepository = $this->getMock('OrgCourseFacultyRepository', array(
                'findOneBy'
            ));

            $rbacManager = $this->getMock('Synapse\CoreBundle\Security\Authorization\TinyRbac\Rbac');


            $mockRepositoryResolver->expects($this->any())
                ->method('getRepository')
                ->willReturnMap([
                    [
                        "SynapseAcademicBundle:OrgCourseStudent",
                        $mockOrgCourseStudentRepository
                    ],
                    [
                        "SynapseAcademicBundle:OrgCourseFaculty",
                        $mockOrgCourseFacultyRepository
                    ]
                ]);


            $mockOrgCourseStudent = $this->getMock("OrgCourseStudent", array('getPerson', 'getCourse'));
            $mockPersonObject = $this->getMock('person', array('getFirstname', 'getLastname'));
            $mockCourseObject = $this->getMock('course', array('getCourseName'));

            $mockOrgCourseStudent->expects($this->any())->method('getPerson')->willReturn($mockPersonObject);
            $mockOrgCourseStudent->expects($this->any())->method('getCourse')->willReturn($mockCourseObject);
            $mockPersonObject->expects($this->any())->method('getFirstname')->willReturn('');
            $mockPersonObject->expects($this->any())->method('getLastname')->willReturn('');
            $mockCourseObject->expects($this->any())->method('getCourseName')->willReturn('');


            $mockOrgCourseFaculty = $this->getMock("OrgCourseFaculty");

            $mockOrgCourseStudentRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($mockOrgCourseStudent));

            $mockOrgCourseFacultyRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($mockOrgCourseFaculty));

            $courseFacultyStudentValidatorService = new CourseFacultyStudentValidatorService($mockRepositoryResolver, $mockLogger, $mockContainer, $rbacManager);
            $courseFacultyStudentValidator = $courseFacultyStudentValidatorService->validateAdditionOfFacultyToCourse($personId, $orgId, $courseId);
        }, [
            'examples' => [
                [
                    4891613,
                    9,
                    248216
                ],
                [
                    4891613,
                    9,
                    248216,
                ]
            ]
        ]);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testValidateAdditionOfStudentToCourse()
    {
        $this->specify("Test to given person ID is not already in a course as a faculty member or student", function ($personId, $orgId, $courseId) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'error'
            ));

            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockOrgCourseStudentRepository = $this->getMock('OrgCourseStudentRepository', array(
                'findOneBy'
            ));

            $mockOrgCourseFacultyRepository = $this->getMock('OrgCourseFacultyRepository', array(
                'findOneBy'
            ));

            $rbacManager = $this->getMock('Synapse\CoreBundle\Security\Authorization\TinyRbac\Rbac');


            $mockRepositoryResolver->expects($this->any())
                ->method('getRepository')
                ->willReturnMap([
                    [
                        "SynapseAcademicBundle:OrgCourseStudent",
                        $mockOrgCourseStudentRepository
                    ],
                    [
                        "SynapseAcademicBundle:OrgCourseFaculty",
                        $mockOrgCourseFacultyRepository
                    ]
                ]);


            $mockOrgCourseFaculty = $this->getMock("OrgCourseFaculty", array('getPerson', 'getCourse'));
            $mockPersonObject = $this->getMock('person', array('getFirstname', 'getLastname'));
            $mockCourseObject = $this->getMock('course', array('getCourseName'));

            $mockOrgCourseFaculty->expects($this->any())->method('getPerson')->willReturn($mockPersonObject);
            $mockOrgCourseFaculty->expects($this->any())->method('getCourse')->willReturn($mockCourseObject);
            $mockPersonObject->expects($this->any())->method('getFirstname')->willReturn('');
            $mockPersonObject->expects($this->any())->method('getLastname')->willReturn('');
            $mockCourseObject->expects($this->any())->method('getCourseName')->willReturn('');


            $mockOrgCourseStudent = $this->getMock("OrgCourseStudent");

            $mockOrgCourseStudentRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($mockOrgCourseStudent));

            $mockOrgCourseFacultyRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($mockOrgCourseFaculty));

            $courseFacultyStudentValidatorService = new CourseFacultyStudentValidatorService($mockRepositoryResolver, $mockLogger, $mockContainer, $rbacManager);
            $courseFacultyStudentValidator = $courseFacultyStudentValidatorService->validateAdditionOfStudentToCourse($personId, $orgId, $courseId);
        }, [
            'examples' => [
                [
                    4891613,
                    9,
                    248216
                ],
                [
                    4891613,
                    9,
                    248216,
                ]
            ]
        ]);
    }

}