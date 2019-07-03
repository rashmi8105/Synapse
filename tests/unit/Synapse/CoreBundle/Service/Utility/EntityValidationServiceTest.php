<?php

namespace tests\unit\Synapse\CoreBundle\Service\Util;

use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\SynapseConstant;

class EntityValidationServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    // tests function ValidateDoctrineEntity
    public function testValidateDoctrineEntity()
    {
        $this->specify("Test Validate Doctrine Entity", function ($externalId, $errorType, $errorArray, $throwException, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);

            $mockContainer = $this->getMock('Container', [
                'get'
            ]);

            // Mock Validator
            $mockValidator = $this->getMock('Validator', ['validate']);

            $validationGroup = ($errorType == 'required') ? $errorType : null;

            // Mock DataProcessingExceptionHandler
            if (!empty($errorType)) {
                $exceptionObject = $this->getDataProcessingExceptionHandlerInstance($errorArray, $validationGroup);
            } else {
                $exceptionObject = null;
            }

            $mockContainer->method('get')->willReturnMap([
                [
                    SynapseConstant::VALIDATOR,
                    $mockValidator
                ],
            ]);

            try {
                $doctrineEntity = $this->getPersonInstance($externalId, $errorType);
                $entityValidationService = new EntityValidationService($mockRepositoryResolver, $mockLogger, $mockContainer);

                $result = $entityValidationService->validateDoctrineEntity($doctrineEntity, $exceptionObject, $validationGroup, $throwException);
                $validateResult = (!empty($result)) ? $result->getAllErrors() : [];

                if (empty($expectedResult)) {
                    $expectedResult = $this->errorArrayWithValidationGroup($errorArray, $validationGroup);
                }
                $this->assertEquals($validateResult, $expectedResult);

            } catch (SynapseException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }


        }, ['examples' => [
            // Test0: Test case for required parameter error
            [
                'X10001',
                'required',
                [
                    'firstname' => 'Firstname cannot be empty.',
                    'lastname' => 'Lastname cannot be empty.',
                    'username' => 'Username cannot be empty.'
                ],
                false,
                ''
            ],
            // Test1: Test case for optional parameter error
            [
                'X10001',
                'required',
                [
                    'externalId' => 'External ID is already in use at this organization.'
                ],
                true,
                'Data Processing Exception'
            ],
            // Test2: Test case for optional parameter error
            [
                'X10001',
                'optional',
                [
                    'title' => 'Title cannot be longer than 100 characters',
                    'authUsername' => 'Authusername cannot be longer than 100 characters',
                ],
                false,
                ''
            ],
            // Test3: Test case for throwing exception
            [
                'X10001',
                '',
                [],
                false,
                ''
            ]
        ]]);
    }


    private function getPersonInstance($externalId = '123', $errorType = '')
    {
        $person = new Person();
        $person->setExternalId($externalId);
        if ($errorType == 'required') {
            $person->setFirstname(null);
            $person->setLastname(null);
            $person->setUsername(null);
        } else {
            $person->setFirstname('testFirstname');
            $person->setLastname('testLastname');
            $person->setUsername('testUsername');
        }

        if ($errorType == 'optional') {
            $person->setTitle(str_repeat("testTitle", 15));
            $person->setAuthUsername(str_repeat("testAuthUsername", 10));
        } else {
            $person->setTitle('testTitle');
            $person->setAuthUsername('testAuthUsername');
        }

        return $person;
    }


    private function getDataProcessingExceptionHandlerInstance($errorArray, $validationGroup, $key = 'error')
    {
        $errorMessage = 'Data Processing Exception';
        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler($errorMessage, $key);
        if ($key == 'error_handle') {
            if (!empty($errorArray)) {
                $dataProcessingExceptionHandler->addErrors($errorArray, 'error', $validationGroup);
            }
            $dataProcessingExceptionHandler->doesErrorHandlerContainError($validationGroup);
        } else {
            $dataProcessingExceptionHandler->enqueueErrorsOntoExceptionObject($errorArray, $validationGroup);
        }

        return $dataProcessingExceptionHandler;
    }

    private function errorArrayWithValidationGroup($errorArray, $validationGroup)
    {
        $errorMessageArray = [];
        foreach ($errorArray as $errorKey => $errorMessage) {
            $typeError = [];
            $typeError[$errorKey] = $errorMessage;
            $typeError['type'] = $validationGroup;

            $errorMessageArray[] = $typeError;
        }
        return $errorMessageArray;
    }


    // tests function buildErrorObjectBasedOffOfValidatedErrorEntity
    public function testBuildErrorObjectBasedOffOfValidatedErrorEntity()
    {

        $this->specify("Test buildErrorObjectBasedOffOfValidatedErrorEntity", function ($externalId, $errorType, $errorArray) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);

            $mockContainer = $this->getMock('Container', [
                'get'
            ]);

            // Mock Validator
            $mockValidator = $this->getMock('Validator', ['validate']);
            $allErrorsArray = $this->arrayOfErrorObjects($errorArray);
            $mockValidator->method('validate')->willReturn($allErrorsArray);

            $validationGroup = ($errorType == 'required') ? $errorType : null;

            // Mock DataProcessingExceptionHandler
            if (!empty($errorType)) {
                $exceptionObject = $this->getDataProcessingExceptionHandlerInstance([], $validationGroup);
            } else {
                $exceptionObject = null;
            }

            $mockContainer->method('get')->willReturnMap([
                [
                    SynapseConstant::VALIDATOR,
                    $mockValidator
                ],
            ]);

            $doctrineEntity = $this->getPersonInstance($externalId, $errorType);
            $entityValidationService = new EntityValidationService($mockRepositoryResolver, $mockLogger, $mockContainer);

            $result = $entityValidationService->buildErrorObjectBasedOffOfValidatedErrorEntity($doctrineEntity, $exceptionObject, $validationGroup);
            $validateResult = (!empty($result)) ? $result->getAllErrors() : null;

            $expectedResult = (!empty($errorArray)) ? $this->errorArrayWithValidationGroup($errorArray, $validationGroup) : null;
            $this->assertEquals($validateResult, $expectedResult);

        }, ['examples' => [
            // Test0: Test case for required parameter error, Returns Exception object
            [
                'X10001',
                'required',
                [
                    'firstname' => 'Firstname cannot be empty.',
                    'lastname' => 'Lastname cannot be empty.',
                    'username' => 'Username cannot be empty.'
                ]
            ],
            // Test1: Test case for required parameter error, Returns Exception object
            [
                'X10001',
                'required',
                [
                    'externalId' => 'External ID is already in use at this organization.'
                ]
            ],
            // Test2: Test case for optional parameter error, Returns Exception object
            [
                'X10001',
                'optional',
                [
                    'title' => 'Title cannot be longer than 100 characters',
                    'authUsername' => 'Authusername cannot be longer than 100 characters',
                ]
            ],
            // Test3: Test case for null object, returns null
            [
                'X10001',
                null,
                []
            ]
        ]]);
    }


    private function arrayOfErrorObjects($errorArray = [])
    {
        $returnArray = [];
        foreach ($errorArray as $errorKey => $error) {
            $mockErrorObject = $this->getMock('ErrorObject', ['getPropertyPath', 'getMessage']);
            $mockErrorObject->method('getPropertyPath')->willReturn($errorKey);
            $mockErrorObject->method('getMessage')->willReturn($error);
            $returnArray[] = $mockErrorObject;
        }
        return $returnArray;
    }


    // tests function validateAllDoctrineEntityValidationGroups
    public function testValidateAllDoctrineEntityValidationGroups()
    {

        $this->specify("Test validateAllDoctrineEntityValidationGroups", function ($externalId, $errorType, $errorArray, $throwException) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);

            $mockContainer = $this->getMock('Container', [
                'get'
            ]);

            // Mock Validator
            $mockValidator = $this->getMock('Validator', ['validate']);
            $validationGroup = ($errorType == 'required') ? $errorType : null;

            $validationGroupArray = [$validationGroup => $throwException];

            // Mock DataProcessingExceptionHandler
            if (!empty($errorType)) {
                $exceptionObject = $this->getDataProcessingExceptionHandlerInstance($errorArray, $validationGroup);
            } else {
                $exceptionObject = null;
            }

            $mockContainer->method('get')->willReturnMap([
                [
                    SynapseConstant::VALIDATOR,
                    $mockValidator
                ],
            ]);

            $doctrineEntity = $this->getPersonInstance($externalId, $errorType);
            $entityValidationService = new EntityValidationService($mockRepositoryResolver, $mockLogger, $mockContainer);

            $result = $entityValidationService->validateAllDoctrineEntityValidationGroups($doctrineEntity, $exceptionObject, $validationGroupArray);
            $validateResult = (!empty($result)) ? $result->getAllErrors() : null;
            $expectedResult = (!empty($errorArray)) ? $this->errorArrayWithValidationGroup($errorArray, $validationGroup) : null;

            $this->assertEquals($validateResult, $expectedResult);

        }, ['examples' => [
            // Test0: Test case for required parameter error, return exception object
            [
                'X10001',
                'required',
                [
                    'firstname' => 'Firstname cannot be empty.',
                    'lastname' => 'Lastname cannot be empty.',
                    'username' => 'Username cannot be empty.'
                ],
                false,
            ],
            // Test1: Test case for required parameter error, return exception object
            [
                'X10001',
                'required',
                [
                    'externalId' => 'External ID is already in use at this organization.'
                ],
                false,
            ],
            // Test2: Test case for optional parameter error, return exception object
            [
                'X10001',
                'optional',
                [
                    'title' => 'Title cannot be longer than 100 characters',
                    'authUsername' => 'Authusername cannot be longer than 100 characters',
                ],
                false,
            ],
            // Test3: Test case for returning null object
            [
                'X10001',
                '',
                [],
                false,
            ]
        ]]);
    }


    // tests function throwErrorIfContains
    public function testThrowErrorIfContains()
    {

        $this->specify("Test throwErrorIfContains", function ($errorType, $errorArray, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);

            $mockContainer = $this->getMock('Container', [
                'get'
            ]);

            // Mock DataProcessingExceptionHandler
            $exceptionObject = $this->getDataProcessingExceptionHandlerInstance($errorArray, $errorType, 'error_handle');

            try {
                $entityValidationService = new EntityValidationService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $entityValidationService->throwErrorIfContains($exceptionObject, $errorType);

                $expectedResult = (!empty($errorArray)) ? $errorArray : [];

                $this->assertInstanceOf('Synapse\CoreBundle\Exception\DataProcessingExceptionHandler', $result);
                $this->assertEquals($result->getAllErrors(), $expectedResult);

            } catch (SynapseException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, ['examples' => [
            // Test0: Test case for throwing exception object
            [
                'required',
                [
                    'firstname' => 'Firstname cannot be empty.',
                    'lastname' => 'Lastname cannot be empty.',
                    'username' => 'Username cannot be empty.'
                ],
                'Data Processing Exception'
            ],
            // Test1: Test case for required parameter error, return exception object
            [
                'optional',
                [
                    'title' => 'Title cannot be longer than 100 characters',
                    'authUsername' => 'Authusername cannot be longer than 100 characters',
                ],
                'Data Processing Exception'
            ],
            // Test2: Test case for optional parameter error, return exception object
            [
                '',
                [],
                null,
            ],
        ]]);
    }


    // tests function nullifyFieldsToBeCleared
    public function testNullifyFieldsToBeCleared()
    {
        $this->specify("Test nullify fields to be cleared", function ($doctrineEntity, $clearFieldsArray, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);

            $mockContainer = $this->getMock('Container', [
                'get'
            ]);

            // Mock DataProcessingUtilityService
            $mockDataProcessingUtilityService = $this->getMock('DataProcessingUtilityService', ['getAllAttributesOfDoctrineEntity']);
            $mockDataProcessingUtilityService->method('getAllAttributesOfDoctrineEntity')->willReturnCallback(function($doctrineEntity) {
                $entityName = get_class($doctrineEntity);
                $entity = (array)$doctrineEntity;
                $attributeArray = array_keys($entity);
                $entityAttributesArray = [];
                foreach($attributeArray as $attribute) {
                    $entityAttributesArray[] = trim(str_replace($entityName, '', $attribute));
                }
                return $entityAttributesArray;
            });

            $mockContainer->method('get')->willReturnMap([
                [
                    DataProcessingUtilityService::SERVICE_KEY,
                    $mockDataProcessingUtilityService
                ],
            ]);

            $entityValidationService = new EntityValidationService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $entityValidationService->nullifyFieldsToBeCleared($doctrineEntity, $clearFieldsArray);

            $this->assertEquals($result, $expectedResult);

        }, ['examples' => [
            // Test0: Passing Person entity with clear fields, returns person entity with null value
            [
                $this->getPersonInstance('12321'),
                [
                    'firstname',
                    'lastname',
                    'username'
                ],
                $this->getPersonInstance('12321', 'required'),
            ],
            // Test1: Passing Person entity without clear fields, returns person entity
            [
                $this->getPersonInstance('12321'),
                [],
                $this->getPersonInstance('12321'),
            ],
        ]]);
    }


    // tests function restoreErroredProperties
    public function testRestoreErroredProperties()
    {
        $this->specify("Test restore errored properties", function ($editedDoctrineEntity, $clonedDoctrineEntity, $errorArray, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);

            $mockContainer = $this->getMock('Container', [
                'get'
            ]);

            // Mock DataProcessingExceptionHandler
            $exceptionObject = $this->getDataProcessingExceptionHandlerInstance($errorArray, 'optional');

            $entityValidationService = new EntityValidationService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $entityValidationService->restoreErroredProperties($editedDoctrineEntity, $clonedDoctrineEntity, $exceptionObject);

            $this->assertEquals($result, $expectedResult);

        }, ['examples' => [
            // Test0: Passing Edited Person entity, Cloned Person entity with errors, returns edited person entity without setting value
            [
                $this->getPersonInstance('12321', 'optional'),
                $this->getPersonInstance('12321'),
                [
                    'title' => 'Title cannot be longer than 100 characters',
                    'authUsername' => 'Authusername cannot be longer than 100 characters',
                ],
                $this->getPersonInstance('12321'),
            ],
            // Test1: Passing Edited Person entity, Cloned Person entity without errors, returns edited person entity
            [
                $this->getPersonInstance('12321'),
                $this->getPersonInstance('12321'),
                [],
                $this->getPersonInstance('12321'),
            ],
        ]]);
    }

}
