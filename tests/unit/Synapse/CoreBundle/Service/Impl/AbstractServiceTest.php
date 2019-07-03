<?php


use Synapse\PersonBundle\Service\PersonService;

class AbstractServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testBuildEntityValidationErrorArray()
    {
        $this->specify("Get My Team Activities Validation", function ($errors, $expectedResponse) {
            //Core Mocks
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info',]);
            $mockContainer = $this->getMock('Container', ['get']);

            $personService = new PersonService($mockRepositoryResolver, $mockContainer,$mockLogger);
            $result = $personService->buildEntityValidationErrorArray($errors);

            $this->assertEquals($expectedResponse, $result);

        }, ['examples' =>
            [
                //testcase for Firstname invalid length and Username invalid type
                [
                    $this->getConstraintError([
                            'username' => "Invalid Username",
                            'firstname' => "Firstname length cannot be more than 45"

                        ]),
                    [
                        'username' => "Invalid Username",
                        'firstname' => "Firstname length cannot be more than 45"
                    ]
                ],
                //testcase for existing ExternalId and Username
                [
                    $this->getConstraintError([
                        'username' => "Username already in use",
                        'externalId' => "ExternalId already in use"

                    ]),
                    [
                        'username' => "Username already in use",
                        'externalId' => "ExternalId already in use"
                    ]
                ],
                //testcase for empty error array
                [
                    $this->getConstraintError([]),[]
                ]
            ]
        ]);
    }

    private function getConstraintError($errorArray)
    {
        $returnArray = [];
        foreach ($errorArray as $key => $error) {

            $mockConstraintValidator = $this->getMock('ConstraintValidatorList', ['getPropertyPath', 'getMessage']);
            $mockConstraintValidator->method('getPropertyPath')->willReturn($key);
            $mockConstraintValidator->method('getMessage')->willReturn($error);

            $returnArray[] = $mockConstraintValidator;
        }
        return $returnArray;
    }
}