<?php
namespace Synapse\PersonBumdle\Service;

use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\PersonBundle\DTO\ContactInfoDTO;
use Synapse\PersonBundle\DTO\ContactInfoListDTO;
use Synapse\PersonBundle\Repository\ContactInfoRepository;
use Synapse\PersonBundle\Service\ContactInfoService;

class ContactInfoServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    private $errorArray;

    private $userIds = [
        1, 2, 3
    ];

    private $personFilterUserIds = [
        1,
        3
    ];

    private $contactFilterUserIds = [
        2,
        3
    ];


    private $contactInfoUserData = [
        1 => [
            "external_id" => "1",
            "firstname" => "first",
            "lastname" => "1",
            "primary_email" => "fssp1@mailinator.com",
            "home_phone" => "1234"
        ],
        2 => [
            "external_id" => "2",
            "firstname" => "second",
            "lastname" => "1",
            "primary_email" => "fssp1@mailinator.com",
            "home_phone" => "56789"
        ],
        3 => [
            "external_id" => "3",
            "firstname" => "first1",
            "lastname" => "1",
            "primary_email" => "fssp1@mailinator.com",
            "home_phone" => "5678910"
        ]
    ];


    private $createContactInfoArray = [
        // Invalid external id
        "0" => [
            "external_id" => "-3",
            "address_one" => "Bangalore",
            "address_two" => "Electronic City",
            "city" => "Bangalore",
            "state" => "Karanataka",
            "zip" => "123456781",
            "country" => "Test country",
            "primary_mobile" => "123451234512",
            "alternate_mobile" => "54321",
            "home_phone" => "987656789",
            "alternate_email" => "BJS123@mailinator.com",
            "primary_mobile_provider" => "Primary mobile provider",
            "alternate_mobile_provider" => "Alternate mobile provider"
        ],
        // contact info already exists for user
        "1" => [
            "external_id" => "5",
            "address_one" => "Test Address1",
            "address_two" => "Test Address2_3",
            "city" => "Test city",
            "state" => "Test state",
            "zip" => "123456781",
            "country" => "Test country",
            "primary_mobile" => "123451234512",
            "alternate_mobile" => "54321",
            "home_phone" => "987656789",
            "alternate_email" => "BJS123@mailinator.com",
            "primary_mobile_provider" => "Primary mobile provider",
            "alternate_mobile_provider" => "Alternate mobile provider"
        ],
        // Valid data
        "2" => [
            "external_id" => "3",
            "address_one" => "Test Address1",
            "address_two" => "Test Address2_3",
            "city" => "Test city",
            "state" => "Test state",
            "zip" => "123456781",
            "country" => "Test country",
            "primary_mobile" => "123451234512",
            "alternate_mobile" => "54321",
            "home_phone" => "987656789",
            "alternate_email" => "BJS123@mailinator.com",
            "primary_mobile_provider" => "Primary mobile provider",
            "alternate_mobile_provider" => "Alternate mobile provider"
        ],
        // Invalid optional fields
        "3" => [
            "external_id" => "3",
            "address_one" => "Test Address1",
            "address_two" => "Test Address2_3",
            "city" => "Test city",
            "state" => "Test state",
            "zip" => "12345678165555555555555555555555555555555555555554",
            "country" => "Test country",
            "primary_mobile" => "1234512345156876867868688568567856855454344342",
            "alternate_mobile" => "54321",
            "home_phone" => "987656789",
            "alternate_email" => "BJS123@mailinator.com",
            "primary_mobile_provider" => "Primary mobile provider",
            "alternate_mobile_provider" => "Alternate mobile provider"
        ]
    ];

    private $personContactInfoArray = [
        "1" => [
            "external_id" => "4611595ABCDEFG",
            "address_one" => "123 W. 135th St.555",
            "address_two" => null,
            "city" => "Detriot",
            "state" => "MI88",
            "zip" => "482016",
            "country" => "USA",
            "primary_mobile" => "88888888",
            "alternate_mobile" => "7899979117",
            "home_phone" => "567575675",
            "alternate_email" => "eminem@rap.com",
            "primary_mobile_provider" => "AT&T",
            "alternate_mobile_provider" => "Verizon",
            "fields_to_clear" => []
        ],
        "2" => [
            "external_id" => "40011ABCDEFG",
            "address_one" => "Address sample1",
            "address_two" => null,
            "city" => "Detriot88888888888888888888888888888888888888888888888888",
            "state" => "MI7777777777777777777777777777777777777777777777777777777777777777777777777777",
            "zip" => "48201677777777777777777778888888888888888888888888888888888888888888888888888888888888",
            "country" => "USA",
            "primary_mobile" => "88888888",
            "alternate_mobile" => "789997979",
            "home_phone" => "56757566",
            "alternate_email" => "eminem@rap.com",
            "primary_mobile_provider" => "AT&T",
            "alternate_mobile_provider" => "Verizon",
            "fields_to_clear" => []
        ],
        "3" => [
            "external_id" => "40011ABCDEFG",
            "address_one" => "Address sample1",
            "address_two" => null,
            "city" => "Detriot",
            "state" => "MI7",
            "zip" => "4820167",
            "country" => "USA",
            "primary_mobile" => "88888888",
            "alternate_mobile" => "789997979",
            "home_phone" => "56757566",
            "alternate_email" => "eminem@rap.com",
            "primary_mobile_provider" => "AT&T",
            "alternate_mobile_provider" => "Verizon",
            "fields_to_clear" => []
        ]
    ];

    private $contactInfoErrorMessage = [
        "1" =>
            [
                "external_id" => "Not a Valid Person"
            ],
        "2" =>
            [
                "external_id" => "Contact Information does not exist for this user"
            ],
        "3" =>
            [
                "city" => "City cannot be longer than 100 characters",
                "state" => "State cannot be longer than 100 characters",
                "zip" => "Zip cannot be longer than 20 characters"
            ]
    ];

    public function testCreateUsersContactInfo()
    {
        $this->specify("Test create users contact information", function ($organizationId, $errorType, $errorArray, $contactInfoArray, $expectedResult) {
            $this->errorArray = $errorArray;

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            // Initializing exception handler
            $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
            foreach ($this->errorArray as $errorKey => $errorValue) {
                $dataProcessingExceptionHandler->addErrors($errorValue, $errorKey);
            }
            //Mocking repositories
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy', 'persist']);
            $mockPerson = $this->getMock('Person', ['getId', 'getContacts', 'getUsername', 'addContact']);
            if ($errorType == 'required') {
                $mockPersonRepository->method('findOneBy')->willThrowException($dataProcessingExceptionHandler);
            } else {
                $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);
            }
            $mockContactInfo = $this->getMock('ContactInfo', ['getId']);
            $mockContactInfo->method('getId')->willReturn(1);
            $mockArrayCollection = $this->getMock('ArrayCollection', ['getValues']);
            if ($errorType == 'contact_info_exists') {
                $mockArrayCollection->method('getValues')->willReturn([$mockContactInfo]);
            } else {
                $mockArrayCollection->method('getValues')->willReturn([]);
            }
            $mockPerson->method('getContacts')->willReturn($mockArrayCollection);
            $mockPerson->method('getUsername')->willReturn('BS@mailinator.com');
            //Mocking services
            $mockEntityValidationService = $this->getMock('EntityValidationService', ['validateDoctrineEntity', 'replaceUnderScoreToCamelCase']);
            $mockDataProcessingUtilityService = $this->getMock('DataProcessingUtilityService', ['setErrorMessageOrValueInArray']);
            $mockApiValidationService = $this->getMock('ApiValidationService', ['updateOrganizationAPIValidationErrorCount']);
            if ($errorType == "optional") {
                $mockEntityValidationService->method('validateDoctrineEntity')->willReturn($dataProcessingExceptionHandler);
            }
            $mockEntityValidationService->method('replaceUnderScoreToCamelCase')->willReturn('city');
            $mockDataProcessingUtilityService->method('setErrorMessageOrValueInArray')->willReturnCallback(function ($records, $errorArray) {
                $responseArray = [];
                foreach ($records as $key => $value) {
                    if (array_key_exists($key, $errorArray)) {
                        $responseArray[$key]['value'] = $value;
                        $responseArray[$key]['message'] = $errorArray[$key];
                    } else {
                        $responseArray[$key] = $value;
                    }
                }
                return $responseArray;
            });
            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    PersonRepository::REPOSITORY_KEY,
                    $mockPersonRepository
                ]
            ]);
            $mockContainer->method('get')->willReturnMap([
                [
                    DataProcessingUtilityService::SERVICE_KEY,
                    $mockDataProcessingUtilityService
                ],
                [
                    APIValidationService::SERVICE_KEY,
                    $mockApiValidationService
                ],
                [
                    EntityValidationService::SERVICE_KEY,
                    $mockEntityValidationService
                ],
            ]);
            try {
                $contactInfoServiceService = new ContactInfoService($mockRepositoryResolver, $mockContainer, $mockLogger);
                $contactInfoListDTO = $this->setContactInfoListDTO($this->setContactInfoDTOOrEntity($contactInfoArray));
                $result = $contactInfoServiceService->createUsersContactInfo($organizationId, $contactInfoListDTO);
                $this->assertEquals($result, $expectedResult);
            } catch (DataProcessingExceptionHandler $dpeh) {
            }
        }, [
                'examples' => [
                    // Test0 - invalid external_id will throw exception
                    [
                        1,
                        'required',
                        [
                            "external_id" => "External Id -3 is not valid at the organization."
                        ],
                        $this->createContactInfoArray["0"],
                        [
                            'data' => [
                                'created_count' => 0,
                                'created_records' => []
                            ],
                            'error' => [
                                'error_count' => 1,
                                'error_data' => [
                                    "0" => $this->setErrorMessageInArray($this->createContactInfoArray['0'], [
                                        "external_id" => "External Id -3 is not valid at the organization."
                                    ])
                                ]
                            ]
                        ]
                    ],
                    // Test1 - If contact information exists for the use, add this error in DataProcessingExceptionHandler
                    [
                        1,
                        'contact_info_exists',
                        [
                            'external_id' => 'Contact Information already exists for this user.'
                        ],
                        $this->createContactInfoArray["1"],
                        [
                            'data' => [
                                'created_count' => 0,
                                'created_records' => []
                            ],
                            'error' => [
                                'error_count' => 1,
                                'error_data' => [
                                    "0" => $this->setErrorMessageInArray($this->createContactInfoArray["1"], [
                                        'external_id' => 'Contact Information already exists for this user.'
                                    ])
                                ]
                            ]
                        ]
                    ],
                    // Test2 - data with no error
                    [
                        1,
                        '',
                        [],
                        $this->createContactInfoArray["2"],
                        [
                            'data' => [
                                'created_count' => 1,
                                'created_records' => [
                                    0 => $this->setContactInfoDTOOrEntity($this->createContactInfoArray["2"])
                                ]
                            ],
                            'error' => [
                                'error_count' => 0,
                                'error_data' => []
                            ]
                        ]
                    ],
                    // Test3 - optional field error
                    [
                        1,
                        'optional',
                        [
                            'zip' => 'Zip cannot be longer than 20 characters',
                            'primary_mobile' => 'Primary mobile cannot be longer than 32 characters',
                        ],
                        $this->createContactInfoArray["3"],
                        [
                            'data' => [
                                'created_count' => 1,
                                'created_records' => [
                                    0 => $this->setContactInfoDTOOrEntity($this->createContactInfoArray["3"])
                                ]
                            ],
                            'error' => [
                                'error_count' => 1,
                                'error_data' => [
                                    "0" => $this->setErrorMessageInArray($this->createContactInfoArray["3"],
                                        [
                                            'zip' => 'Zip cannot be longer than 20 characters',
                                            'primary_mobile' => 'Primary mobile cannot be longer than 32 characters',
                                        ])
                                ]
                            ]
                        ]
                    ],
                ]

            ]
        );
    }

    private function setContactInfoDTOOrEntity($contactInfo, $isUpdate = false, $setEntity = false)
    {
        $dtoOrEntity = null;
        if ($setEntity) {
            $newContactInfoEntity = new ContactInfo();
            $dtoOrEntity = $newContactInfoEntity;
            $dtoOrEntity->setAddress1($contactInfo['address_one']);
            $dtoOrEntity->setAddress2($contactInfo['address_two']);
        } else {
            $contactInfoDTO = new ContactInfoDTO();
            $dtoOrEntity = $contactInfoDTO;
            $dtoOrEntity->setExternalId($contactInfo['external_id']);
            $dtoOrEntity->setAddressOne($contactInfo['address_one']);
            $dtoOrEntity->setAddressTwo($contactInfo['address_two']);
        }


        $dtoOrEntity->setCity($contactInfo['city']);
        $dtoOrEntity->setState($contactInfo['state']);
        $dtoOrEntity->setZip($contactInfo['zip']);
        $dtoOrEntity->setCountry($contactInfo['country']);
        $dtoOrEntity->setPrimaryMobile($contactInfo['primary_mobile']);
        $dtoOrEntity->setAlternateMobile($contactInfo['alternate_mobile']);
        $dtoOrEntity->setHomePhone($contactInfo['home_phone']);
        $dtoOrEntity->setAlternateEmail($contactInfo['alternate_email']);
        $dtoOrEntity->setPrimaryMobileProvider($contactInfo['primary_mobile_provider']);
        $dtoOrEntity->setAlternateMobileProvider($contactInfo['alternate_mobile_provider']);
        if ($isUpdate && !$setEntity) {
            $dtoOrEntity->setFieldsToClear($contactInfo['fields_to_clear']);
        }
        return $dtoOrEntity;
    }

    private function setContactInfoListDTO($contactInfoDto)
    {
        $contactInfoListDTO = new ContactInfoListDTO();

        $contactInfoListDTO->setContactInformation([$contactInfoDto]);
        return $contactInfoListDTO;
    }

    private function setErrorMessageInArray($records, $errorArray)
    {
        $responseArray = [];
        foreach ($records as $key => $value) {
            if (array_key_exists($key, $errorArray)) {
                $responseArray[$key]['value'] = $value;
                $responseArray[$key]['message'] = $errorArray[$key];
            } else {
                $responseArray[$key] = $value;
            }
        }
        return $responseArray;
    }

    public function testUpdateUsersContactInfo()
    {
        $this->specify("Test Update Users Contact Info", function ($organizationId, $contactInfoArray, $errorType, $errorArray, $expectedResult, $originalRecordDuplicated = null) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));


            //mocking repositories
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy']);


            $mockContactInfoRepository = $this->getMock('ContactInfoRepository', ['persist']);

            //mocking services
            $mockApiValidationService = $this->getMock('ApiValidationService', ['setErrorMessageOrValueInArray', 'updateOrganizationAPIValidationErrorCount']);
            $mockDataProcessingUtilityService = $this->getMock('DataProcessingUtilityService', ['setErrorMessageOrValueInArray']);
            $mockEntityValidationService = $this->getMock('EntityValidationService', ['nullifyFieldsToBeCleared', 'validateDoctrineEntity', 'restoreErroredProperties']);


            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    PersonRepository::REPOSITORY_KEY,
                    $mockPersonRepository
                ],
                [
                    ContactInfoRepository::REPOSITORY_KEY,
                    $mockContactInfoRepository
                ]
            ]);

            $mockContainer->method('get')->willReturnMap([
                [
                    APIValidationService::SERVICE_KEY,
                    $mockApiValidationService
                ],
                [
                    DataProcessingUtilityService::SERVICE_KEY,
                    $mockDataProcessingUtilityService
                ],
                [
                    EntityValidationService::SERVICE_KEY,
                    $mockEntityValidationService
                ],

            ]);

            $dataProcessingException = new DataProcessingExceptionHandler();
            foreach ($errorArray as $errorKey => $errorValue) {
                $dataProcessingException->addErrors($errorValue, $errorKey);
            }

            $mockPersonObject = $this->getMock('Synapse\CoreBundle\Entity\Person', ['getId', 'getContacts']);
            $mockPersonObject->method('getId')->willReturn(1);


            if ($errorType == 'duplicate' || isset($originalRecordDuplicated)) {
                $contactInfoObject = $this->setContactInfoDTOOrEntity($contactInfoArray, true, true);
            } else {
                $contactInfoObject = $this->setContactInfoDTOOrEntity($contactInfoArray, true, true);
                $contactInfoObject->setState('Mordr');
            }

            $mockArrayCollection = $this->getMock('ArrayCollection', ['getValues']);

            if ($errorType == 'contact_not_existing') {
                $mockArrayCollection->method('getValues')->willReturn([]);
            } else if (isset($originalRecordDuplicatedObject)) {
                $mockArrayCollection->method('getValues')->willReturn([$originalRecordDuplicatedObject]);
            } else {
                $mockArrayCollection->method('getValues')->willReturn([$contactInfoObject]);
            }

            $mockPersonObject->method('getContacts')->willReturn($mockArrayCollection);

            if ($errorType == 'invalid_person') {
                $mockPersonRepository->method('findOneBy')->willThrowException($dataProcessingException);
            } else {
                $mockPersonRepository->method('findOneBy')->willReturn($mockPersonObject);
            }

            $contactInfoDTO = $this->setContactInfoDTOOrEntity($contactInfoArray, true);

            $contactInfoListDTO = $this->setContactInfoListDTO($contactInfoDTO);

            if ($errorType == 'optional') {
                $mockEntityValidationService->method('validateDoctrineEntity')->willReturn($dataProcessingException);
            }

            if (isset($originalRecordDuplicatedObject)) {
                $mockEntityValidationService->method('restoreErroredProperties')->willReturn($originalRecordDuplicatedObject);
            } else {
                $mockEntityValidationService->method('restoreErroredProperties')->willReturn($contactInfoObject);

            }

            $error = $this->setErrorMessageInArray($contactInfoArray, $errorArray);
            $mockDataProcessingUtilityService->method('setErrorMessageOrValueInArray')->willReturn($error);



            $contactInfoService = new ContactInfoService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $result = $contactInfoService->updateUsersContactInfo($organizationId, $contactInfoListDTO);

            $this->assertEquals($expectedResult, $result);
        },
            [
                'examples' =>
                    [
                        //Case1: Invalid Person External Id will throw exception
                        [
                            2,
                            $this->personContactInfoArray[1],
                            'invalid_person',
                            $this->contactInfoErrorMessage[1],
                            [
                                "data" =>
                                    [
                                        "updated_count" => 0,
                                        "updated_records" => [],
                                        "skipped_count" => 0,
                                        "skipped_records" => []
                                    ],
                                "errors" =>
                                    [
                                        "error_count" => 1,
                                        "error_records" =>
                                            [
                                                0 => $this->setErrorMessageInArray($this->personContactInfoArray[1], $this->contactInfoErrorMessage[1])
                                            ]
                                    ]

                            ]
                        ],
                        //Case2: Contact does not exist for user
                        [
                            2,
                            $this->personContactInfoArray[1],
                            'contact_not_existing',
                            $this->contactInfoErrorMessage[2],
                            [
                                "data" =>
                                    [
                                        "updated_count" => 0,
                                        "updated_records" => [],
                                        "skipped_count" => 0,
                                        "skipped_records" => []
                                    ],
                                "errors" =>
                                    [
                                        "error_count" => 1,
                                        "error_records" =>
                                            [
                                                0 => $this->setErrorMessageInArray($this->personContactInfoArray[1], $this->contactInfoErrorMessage[2])
                                            ]
                                    ]

                            ]
                        ],
                        //Case3: Contact info updated successfully with no errors
                        [
                            2,
                            $this->personContactInfoArray[1],
                            '',
                            [],
                            [
                                "data" =>
                                    [
                                        "updated_count" => 1,
                                        "updated_records" => [$this->setContactInfoDTOOrEntity($this->personContactInfoArray[1], true)],
                                        "skipped_count" => 0,
                                        "skipped_records" => []
                                    ],
                                "errors" =>
                                    [
                                        "error_count" => 0,
                                        "error_records" => []
                                    ]
                            ]
                        ],
                        //Case4: Contact info updated but with optional error
                        [
                            2,
                            $this->personContactInfoArray[2],
                            'optional',
                            $this->contactInfoErrorMessage[3],
                            [
                                "data" =>
                                    [
                                        "updated_count" => 1,
                                        "updated_records" => [$this->setContactInfoDTOOrEntity($this->personContactInfoArray[2], true)],
                                        "skipped_count" => 0,
                                        "skipped_records" => []
                                    ],
                                "errors" =>
                                    [
                                        "error_count" => 1,
                                        "error_records" => [
                                            0 => $this->setErrorMessageInArray($this->personContactInfoArray[2], $this->contactInfoErrorMessage[3])
                                        ]
                                    ]
                            ]
                        ],
                        //Case5: For duplicate contact info skipped array gets populated
                        [
                            2,
                            $this->personContactInfoArray[1],
                            'duplicate',
                            [],
                            [
                                "data" =>
                                    [
                                        "updated_count" => 0,
                                        "updated_records" => [],
                                        "skipped_count" => 1,
                                        "skipped_records" => [$this->setContactInfoDTOOrEntity($this->personContactInfoArray[1], true)]
                                    ],
                                "errors" =>
                                    [
                                        "error_count" => 0,
                                        "error_records" => []
                                    ]
                            ]
                        ],
                        //Case6: Error, remaining information is duplicate contact, skipped array gets populated
                        [
                            2,
                            $this->personContactInfoArray[2],
                            'optional',
                            $this->contactInfoErrorMessage[3],
                            [
                                "data" =>
                                    [
                                        "updated_count" => 0,
                                        "updated_records" => [],
                                        "skipped_count" => 1,
                                        "skipped_records" => [$this->setContactInfoDTOOrEntity($this->personContactInfoArray[2], true)]
                                    ],
                                "errors" =>
                                    [
                                        "error_count" => 1,
                                        "error_records" => [
                                            0 => $this->setErrorMessageInArray($this->personContactInfoArray[2], $this->contactInfoErrorMessage[3])
                                        ]
                                    ]
                            ],
                            $this->personContactInfoArray[3]

                        ],
                    ]
            ]
        );
    }

    public function testGetUsersContactInfo()
    {
        $this->specify("Test get mapworks person", function ($organizationId, $personFilter, $contactFilter, $contactFilterType, $pageNumber, $recordsPerPage, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $mockContactInfoRepository = $this->getMock('ContactInfoRepository', ['getUsersContactInfo', 'getPersonIdsBasedOnContactInfoFilters']);
            $mockContactInfoRepository->method('getPersonIdsBasedOnContactInfoFilters')->willReturnCallback(function ($organizationId, $personFilter, $contactFilter, $contactFilterType) {
                if (!is_null($personFilter) && !is_null($contactFilter)) {
                    return $this->userIds;
                }
                if (!is_null($personFilter)) {
                    if ($personFilter == "invalid") {
                        return [];
                    } else {
                        return $this->personFilterUserIds;
                    }
                }
                if (!is_null($contactFilter)) {
                    if ($contactFilter == "invalid") {
                        return [];
                    } else {
                        return $this->contactFilterUserIds;
                    }
                }
                return $this->userIds;
            });
            $mockContactInfoRepository->method('getUsersContactInfo')->willReturnCallback(function ($organizationId, $personIds, $offset, $recordsPerPage) {
                $returnArray = [];
                foreach ($personIds as $personId) {
                    $returnArray[] = $this->contactInfoUserData[$personId];
                }
                return $returnArray;
            });
            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    ContactInfoRepository::REPOSITORY_KEY,
                    $mockContactInfoRepository
                ]
            ]);
            $contactService = new ContactInfoService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $resultDto = $contactService->getUsersContactInfo($organizationId, $personFilter, $contactFilter, $contactFilterType, $pageNumber, $recordsPerPage);
            if (count($expectedResult) == 0) {
                $totalPages = 0;
            } else {
                $totalPages = 1;
            }
            $this->assertEquals($resultDto->getPersonList(), $expectedResult);
            $this->assertEquals($resultDto->getCurrentPage(), 1);
            $this->assertEquals($resultDto->getTotalPages(), $totalPages);
            $this->assertEquals($resultDto->getTotalRecords(), count($expectedResult));
        }, [
                'examples' => [
                    // testing student filter
                    [
                        1, "first", null, null, 1, 2, $this->getUserDetails([1, 3])
                    ],
                    // testing invalid student filter
                    [
                        1, "invalid", null, null, 1, 2, []
                    ],
                    // testing contact filter
                    [
                        1, null, "56789", "phone", 1, 2, $this->getUserDetails([2, 3])
                    ],
                    // testing for invalid phone
                    [
                        1, null, "invalid", "phone", 1, 2, []
                    ],
                    // testing for invalid address
                    [
                        1, null, "invalid", "address", 1, 2, []
                    ],
                    // testing student and contact filter
                    [
                        1, "first", "56789", "phone", 1, 3, $this->getUserDetails([1, 2, 3])
                    ],
                    // testing student and contact filter
                    [
                        1, null, null, null, 1, 3, $this->getUserDetails([1, 2, 3])
                    ],
                ]
            ]
        );
    }

    private function getUserDetails($personIds)
    {
        $returnArray = [];
        foreach ($personIds as $personId) {
            $returnArray[] = $this->contactInfoUserData[$personId];
        }
        return $returnArray;
    }
}