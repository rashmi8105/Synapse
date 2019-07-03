<?php
use Synapse\UploadBundle\Job\CreateStudent;

class StudentBaseTest extends \PHPUnit_Framework_TestCase
{

    use\Codeception\Specify;

    public function testprocessReceiveSurvey()
    {
        $this->specify("Run job for student upload", function ($data, $expectedErrorsCount) {
            $mockPersonStudentSurveyEntity = $this->getMock('PersonStudentSurvey', array());

            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockRepositoryResolver = $this->getMock('repositoryResolver', array(
                'getRepository'
            ));

            $mockStudentUploadValidatorService = $this->getMock('StudentUploadValidatorService', array(
                'validateReceiveSurveys', 'getErrors'
            ));
            $mockStudentUploadValidatorService->expects($this->any())
                ->method('validateReceiveSurveys')
                ->will($this->returnValue(array()));

            $mockStudentUploadValidatorService->expects($this->any())
                ->method('getErrors')
                ->will($this->returnValue(array()));

            $mockorgPersonStudentSurvey = $this->getMock('OrgPersonStudentSurvey', array(
                'getSurveyExternalIds',
                'flush',
                'findOneBy',
                'persist'
            ));

            $mockorgPersonStudentSurvey->expects($this->any())
                ->method('getSurveyExternalIds')
                ->will($this->returnValue([
                    'Transition One' => 1647,
                    'Check-Up One' => 1648,
                    'Transition Two' => 1649,
                    'Check-Up Two' => 1650
                ]));

            $mockorgPersonStudentSurvey->expects($this->any())
                ->method('flush')
                ->will($this->returnValue(1));
            $mockorgPersonStudentSurvey->expects($this->any())
                ->method('persist')
                ->will($this->returnValue(1));

            $mockorgPersonStudentSurvey->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue(1));

            $mockSurvey = $this->getMock('Survey', array(
                'findOneBy'
            ));

            $surveyEntity = $this->getMock("Synapse\CoreBundle\Entity\Survey");

            $mockSurvey->method('findOneBy')
                ->willReturn($surveyEntity);


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        'SynapseSurveyBundle:OrgPersonStudentSurvey',
                        $mockorgPersonStudentSurvey
                    ],
                    [
                        'SynapseCoreBundle:Survey',
                        $mockSurvey
                    ]
                ]);

            $mockKernel = $this->getMockBuilder('kernel')
                ->setMethods(array(
                    'getContainer'
                ))
                ->getMock();

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        'repository_resolver',
                        $mockRepositoryResolver
                    ], ['student_upload_validator_service', $mockStudentUploadValidatorService]
                ]);
            $mockKernel->method('getContainer')
                ->willReturn($mockContainer);

            $createStudentObject = new CreateStudent();
            $reflection = new \ReflectionClass(get_class($createStudentObject));
            $parentReflection = $reflection->getParentClass();
            $grandReflection = new \ReflectionClass($parentReflection->name);
            $grandparentReflection = $grandReflection->getParentClass();
            $property = $grandparentReflection->getProperty('kernel');
            $property->setAccessible(true);
            $property->setValue($createStudentObject, $mockKernel);

            if ($data['transitiononereceivesurvey'] !== 0 && $data['transitiononereceivesurvey'] !== 1) {
                $errors[]= 'Invalid value for transitiononereceivesurvey.';
            }
            if ($data['checkuponereceivesurvey'] !== 0 && $data['checkuponereceivesurvey'] !== 1) {
                $errors[]= 'Invalid value for checkuponereceivesurvey.';
            }
            $id = 1;
            $person = $this->getMock("Synapse\CoreBundle\Entity\Person");
            $organization = $this->getMock("Synapse\CoreBundle\Entity\Organization");
            $createStudentObject->processReceiveSurvey($data, $errors, $id, $organization, $person, $type = "null");

            if (!empty($errors)) {
                $errorsCount = count($errors);
            } else {
                $errorsCount = 0;
            }

            $this->assertInternalType('integer', $errorsCount);
            $this->assertEquals($errorsCount, $expectedErrorsCount);
        }, [
            'examples' => [

                // Example 1: Expected errors count should be two, Transitiononereceivesurvey and checkuponereceivesurvey values are 2 and 5 respectively.
                [
                    [
                        'yearid' => 1,
                        'transitiononereceivesurvey' => 5,
                        'checkuponereceivesurvey' => 2,
                        'transitiontworeceivesurvey' => 1,
                        'checkuptworeceivesurvey' => 1
                    ],
                    2
                ],
                // Example 2: Expected errors count should be one, checkuponereceivesurvey value is 2.
                [
                    [
                        'yearid' => 1,
                        'transitiononereceivesurvey' => 0,
                        'checkuponereceivesurvey' => 2,
                        'transitiontworeceivesurvey' => 0,
                        'checkuptworeceivesurvey' => 0
                    ],
                    1
                ],
                // Example 3: Expected errors count should be zero, Transitiononereceivesurvey and Checkuponereceivesurvey values are acceptable (either 0 or 1)
                [
                    [
                        'yearid' => 1,
                        'transitiononereceivesurvey' => 1,
                        'checkuponereceivesurvey' => 1,
                        'transitiontworeceivesurvey' => 1,
                        'checkuptworeceivesurvey' => 1
                    ],
                    0
                ]
            ]
        ]);
    }
}