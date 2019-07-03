<?php

namespace Synapse\CoreBundle\Service\Impl;

use Codeception\Specify;
use Codeception\Test\Unit;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgMetadata;
use Synapse\CoreBundle\Entity\OrgMetadataListValues;
use Synapse\CoreBundle\Entity\PersonOrgMetadata;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\OrgMetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrgMetadataRepository;
use Synapse\CoreBundle\Repository\PersonOrgMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\ProfileDto;

class TestOrgMetadataListValues extends OrgMetadataListValues
{
    ///test class created to test method using setId and getId

    private $id;

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }
}

class OrgProfileServiceTest extends Unit
{
    use Specify;

    /**
     * @var Logger
     */
    private $mockLogger;

    /**
     * @var Container
     */
    private $mockContainer;

    /**
     * @var RepositoryResolver
     */
    private $mockRepositoryResolver;

    public function _before()
    {
        $this->mockContainer = $this->getMock('container', ['get']);
        $this->mockLogger = $this->getMock('logger', ['debug', 'error']);
        $this->mockRepositoryResolver = $this->getMock('repositoryResolver', ['getRepository']);
    }

    public function testGetProfiles()
    {
        $this->specify("Test activity permission from allowed profile items ", function ($expectedResult, $organizationId, $orgMetadataRepositoryGetProfileWillReturn,
                                                                                    $orgPermissionsetMetadataRepositoryGetISPsByPermisisonset,
                                                                                    $orgPermissionsetServiceGetAllowedISPISQBlocks,
                                                                                    $userId, $exclude) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockOrgPermissionsetMetadataRepository = $this->getMock('OrgPermissionsetMetadataRepository', ['getAllISPsByPersonIdWithRelationToStudentAccess']);
            $mockOrgPermissionsetMetadataRepository->method('getAllISPsByPersonIdWithRelationToStudentAccess')->willReturn($orgPermissionsetMetadataRepositoryGetISPsByPermisisonset);

            $mockOrgOrgMetadataRepository = $this->getMock('OrgMetadataRepository', ['getProfile']);
            $mockOrgOrgMetadataRepository->method('getProfile')->willReturn([$orgMetadataRepositoryGetProfileWillReturn]);

            $mockOrgPermissionsetService = $this->getMock('orgPermissionsetService', array('getAllowedIspIsqBlocks'));
            $mockOrgPermissionsetService->method('getAllowedIspIsqBlocks')->willReturn($orgPermissionsetServiceGetAllowedISPISQBlocks);

            $mockContainer->method('get')->willReturnMap(
                [
                    [
                        'orgpermissionset_service',
                        $mockOrgPermissionsetService
                    ]
                ]
            );

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        'SynapseCoreBundle:OrgPermissionsetMetadata',
                        $mockOrgPermissionsetMetadataRepository
                    ],
                    [
                        'SynapseCoreBundle:OrgMetadata',
                        $mockOrgOrgMetadataRepository
                    ]
                ]);


            $orgProfileService = new OrgProfileService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $response = $orgProfileService->getInstitutionSpecificProfileBlockItems($organizationId, $exclude, 'all', false, false, $userId);

            $this->assertEquals($response, $expectedResult);

        }, [
            'examples' => [
                [ // EXAMPLE 1: No user id, returns all ISPs
                    [
                        "display_name" => "Institution Specific Profile Items",
                        "organization_id" => 14,
                        "total_archive_count" => 1,
                        "profile_items" => [
                            0 => [
                                "id" => 7016,
                                "modified_at" => [
                                    "date" => "2017-04-05 05:35:52",
                                    "timezone_type" => 3,
                                    "timezone" => "UTC"],
                                "display_name" => "Organization: 032 Metadata ID: 007016",
                                "item_data_type" => "N",
                                "definition_type" => "O",
                                "item_label" => "ORG032META007016",
                                "item_subtext" => "Organization: 032 Metadata ID: 007016",
                                "sequence_no" => 1,
                                "calendar_assignment" => "N",
                                "number_type" => [
                                    "decimal_points" => 0,
                                    "min_digits" => null,
                                    "max_digits" => null
                                ],
                                "status" => "active",
                                "item_used" => false
                            ]
                        ]
                    ],
                    14,// organization id
                    [
                        'org_metadata_id' => 7016,
                        'modified_at' => [
                            'date' => "2017-04-05 05:35:52",
                            'timezone_type' => 3,
                            'timezone' => "UTC"],
                        'item_label' => "ORG032META007016",
                        'display_name' => "Organization: 032 Metadata ID: 007016",
                        'item_subtext' => "Organization: 032 Metadata ID: 007016",
                        'item_data_type' => "N",
                        'definition_type' => "O",
                        'sequence_no' => 1,
                        'decimal_points' => 0,
                        'min_digits' => null,
                        'max_digits' => null,
                        'status' => null,
                        'pom_id' => null,
                        'au_id' => null,
                        'calendar_assignment' => "N",
                    ],
                    [7016],
                    [],
                    null,
                    false
                ],
                [ // EXAMPLE 2: person id is being passed in and the person has access to the profile item id
                    [
                        "display_name" => "Institution Specific Profile Items",
                        "organization_id" => 14,
                        "total_archive_count" => 1,
                        "profile_items" => [
                            0 => [
                                "id" => 7016,
                                "modified_at" => [
                                    "date" => "2017-04-05 05:35:52",
                                    "timezone_type" => 3,
                                    "timezone" => "UTC"],
                                "display_name" => "Organization: 032 Metadata ID: 007016",
                                "item_data_type" => "N",
                                "definition_type" => "O",
                                "item_label" => "ORG032META007016",
                                "item_subtext" => "Organization: 032 Metadata ID: 007016",
                                "sequence_no" => 1,
                                "calendar_assignment" => "N",
                                "number_type" => [
                                    "decimal_points" => 0,
                                    "min_digits" => null,
                                    "max_digits" => null
                                ],
                                "status" => "active",
                                "item_used" => false
                            ]
                        ]
                    ],
                    14,
                    [
                        'org_metadata_id' => 7016,
                        'modified_at' => [
                            'date' => "2017-04-05 05:35:52",
                            'timezone_type' => 3,
                            'timezone' => "UTC"],
                        'item_label' => "ORG032META007016",
                        'display_name' => "Organization: 032 Metadata ID: 007016",
                        'item_subtext' => "Organization: 032 Metadata ID: 007016",
                        'item_data_type' => "N",
                        'definition_type' => "O",
                        'sequence_no' => 1,
                        'decimal_points' => 0,
                        'min_digits' => null,
                        'max_digits' => null,
                        'status' => null,
                        'pom_id' => null,
                        'au_id' => null,
                        'calendar_assignment' => "N",
                    ],
                    [7016],
                    [],
                    123,
                    false
                ],
                [ // EXAMPLE 3: Person id is passed in and person does not have access to ISPs
                    [
                        "display_name" => "Institution Specific Profile Items",
                        "organization_id" => 14,
                        "total_archive_count" => 1,
                        "profile_items" => []
                    ],
                    14,
                    [
                        'org_metadata_id' => 7016,
                        'modified_at' => [
                            'date' => "2017-04-05 05:35:52",
                            'timezone_type' => 3,
                            'timezone' => "UTC"],
                        'item_label' => "ORG032META007016",
                        'display_name' => "Organization: 032 Metadata ID: 007016",
                        'item_subtext' => "Organization: 032 Metadata ID: 007016",
                        'item_data_type' => "N",
                        'definition_type' => "O",
                        'sequence_no' => 1,
                        'decimal_points' => 0,
                        'min_digits' => null,
                        'max_digits' => null,
                        'status' => null,
                        'pom_id' => null,
                        'au_id' => null,
                        'calendar_assignment' => "N",
                    ],
                    [10],
                    [],
                    123,
                    false
                ]
            ]
        ]);
    }

    /**
     * @expectedException \Synapse\CoreBundle\Exception\SynapseValidationException
     */
    public function testGetProfile()
    {
        $this->specify("Test getProfile", function ($personOrgMetadataData, $orgMetadataListValuesObject, $orgProfile, $organizationId, $metadataId, $expectedResult) {
            $mockOrgMetadataRepository = $this->getMock('OrgMetadataRepository', ['find', 'findOneBy']);

            if (empty($orgProfile)) {
                $mockOrgMetadataRepository->method('find')->will($this->throwException(new SynapseValidationException($expectedResult)));
            } else {
                $mockOrgMetadataRepository->method('find')->will($this->returnValue($orgProfile));
            }

            $mockPersonOrgMetadataRepository = $this->getMock('personOrgMetadataRepository', ['findBy', 'findOneBy']);
            $mockPersonOrgMetadataRepository->method('findBy')->will($this->returnValue($personOrgMetadataData));
            $mockPersonOrgMetadataRepository->method('findOneBy')->will($this->returnValue(!empty($personOrgMetadataData) ? true : false));

            $mockOrgMetadataListRepository = $this->getMock('OrgMetadataListValuesRepository', ['findBy']);
            $mockOrgMetadataListRepository->method('findBy')->will($this->returnValue($orgMetadataListValuesObject));

            // Mocking repository
            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
                    [OrgMetadataRepository::REPOSITORY_KEY, $mockOrgMetadataRepository],
                    [PersonOrgMetadataRepository::REPOSITORY_KEY, $mockPersonOrgMetadataRepository],
                    [OrgMetadataListValuesRepository::REPOSITORY_KEY, $mockOrgMetadataListRepository]
                ]
            );

            $mockRbacManager = $this->getMock('RbacManager', ['checkAccessToOrganization']);
            $mockRbacManager->method('checkAccessToOrganizsation')->willReturn(true);
            $this->mockContainer->method('get')->willReturnMap(
                [
                    [
                        Manager::SERVICE_KEY,
                        $mockRbacManager
                    ]
                ]
            );

            $orgPermissionSetService = new OrgProfileService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $result = $orgPermissionSetService->getProfile($organizationId, $metadataId);
            $this->assertEquals($result, $expectedResult);
        }, [
            'examples' => [
                [ // Example 1 Test with metadata type category
                    [ // mapping condition
                        $this->createPersonOrgMetadataEntityObject('2', $this->exampleDataForOrgProfileData()['example_1']),
                        $this->createPersonOrgMetadataEntityObject('3', $this->exampleDataForOrgProfileData()['example_1']),
                    ],
                    [
                        $this->createOrgMetadataListValuesEntityObject(237348, 'test1', '1', $this->exampleDataForOrgProfileData()['example_1'], 0),
                        $this->createOrgMetadataListValuesEntityObject(237349, 'test2', '2', $this->exampleDataForOrgProfileData()['example_1'], 0),
                        $this->createOrgMetadataListValuesEntityObject(237350, 'test3', '3', $this->exampleDataForOrgProfileData()['example_1'], 0)
                    ],
                    $this->exampleDataForOrgProfileData()['example_1'],
                    null,// org id
                    7597, //metadata id
                    // Expected
                    $this->createProfileDTO('O', null, 'N', null, 1, null, null, $this->exampleDataForCategoryTypeDto()['example_1'], 'Test Category-7597', [], null, 'S', 'TestCategoryHeader', '', null, null, null, null, null, null, null, '', 'active')
                ],
                [ // Example 2 Test with metadata type Text
                    [],
                    [
                        $this->createOrgMetadataListValuesEntityObject(237350, 'test3', '3', $this->exampleDataForOrgProfileData()['example_3'], 15)
                    ],
                    $this->exampleDataForOrgProfileData()['example_3'],
                    '',// org id
                    7597, //metadata id
                    $this->createProfileDTO('O', null, 'N', null, false, null, null, null, '', [], null, 'T', '', 'q', null, null, null, null, null, null, null, 15, 'active')
                    // Expected
                ],
                [ // Example 3 Test with metadata type Number
                    [],
                    [
                        $this->createOrgMetadataListValuesEntityObject(237350, 'list_3', '3', $this->exampleDataForOrgProfileData()['example_2'], 3)
                    ],
                    $this->exampleDataForOrgProfileData()['example_2'],
                    '',// org id
                    7597, //metadata id
                    $this->createProfileDTO('O', null, 'N', null, false, null, null, null, 'Organization: 014 Metadata ID: 006087', [], null, 'N', 'ORG014META006087', 'Organization: 014 Metadata ID: 006087', null, null, null, ['min_digits' => '', 'max_digits' => '', 'decimal_points' => 0.0], null, null, null, 3, 'active')
                ],
                [ // Example 4 Test with metadata type date
                    [],
                    [
                        $this->createOrgMetadataListValuesEntityObject(237350, 'test3', '3', $this->exampleDataForOrgProfileData()['example_5'], 15)
                    ],
                    $this->exampleDataForOrgProfileData()['example_5'],
                    '',// org id
                    7597, //metadata id
                    $this->createProfileDTO('O', null, 'T', null, false, null, null, null, 'SampleTest4', [], null, 'D', 'Test4', 'This is a sample text.', null, null, null, null, null, null, null, 19, 'active')
                    // Expected
                ],
                [ // Example 5 Test with empty profile throwException
                    [],
                    [
                        $this->createOrgMetadataListValuesEntityObject(237350, 'test3', '3', $this->exampleDataForOrgProfileData()['example_5'], 15)
                    ],
                    '',
                    '',// org id
                    7597, //metadata id
                    "Profile not found"
                    // Expected
                ]
            ]
        ]);
    }

    private function exampleDataForOrgProfileData()
    {
        $data['example_1'] = $this->createOrgMetadataEntityObject('TestCategoryHeader', 'Test Category-7597', 'S', '', 'O', 0, false, '0.0000', '0.0000', '', null, 'N', 'active');
        $data['example_2'] = $this->createOrgMetadataEntityObject('ORG014META006087', 'Organization: 014 Metadata ID: 006087', 'N', 'Organization: 014 Metadata ID: 006087', 'O', 0, false, null, null, 3, null, 'N', 'active');
        $data['example_3'] = $this->createOrgMetadataEntityObject('', '', 'T', 'q', 'O', null, false, '0.0000', '0.0000', 15, null, 'N', 'active');
        $data['example_4'] = $this->createOrgMetadataEntityObject('ABCD', 'abcd', 'N', 'asdasdasd', 'O', 0, 0, '1.0000', '10.0000', 12, null, 'Y', 'active');
        $data['example_5'] = $this->createOrgMetadataEntityObject('Test4', 'SampleTest4', 'D', 'This is a sample text.', 'O', 0, false, null, null, 19, null, 'T', 'active');
        return $data;
    }

    private function exampleDataForCategoryTypeDto()
    {
        $data['example_1'] = [
            [
                "can_edit_list_row" => true,
                "answer" => "test1",
                "value" => "1",
                "sequence_no" => 0,
                "org_metadata_list_value_id" => 237348
            ],
            [
                "can_edit_list_row" => true,
                "answer" => "test2",
                "value" => "2",
                "sequence_no" => 0,
                "org_metadata_list_value_id" => 237349
            ],
            [
                "can_edit_list_row" => true,
                "answer" => "test3",
                "value" => "3",
                "sequence_no" => 0,
                "org_metadata_list_value_id" => 237350
            ]
        ];
        $data['example_2'] = [
            [
                "can_edit_list_row" => false,
                "answer" => "test7",
                "value" => "7",
                "sequence_no" => 0,
                "org_metadata_list_value_id" => 237111
            ]
        ];
        return $data;
    }


    /**
     * @param string $metaKey
     * @param string $metaName
     * @param string $metaDataType
     * @param string $metaDescription
     * @param string $definationType
     * @param integer $noOfDecimals
     * @param string $isRequired
     * @param string $minRange
     * @param string $maxRange
     * @param integer $sequence
     * @param string $metaGroup
     * @param string $scope
     * @param string $status
     * @return OrgMetadata
     */
    public function createOrgMetadataEntityObject($metaKey, $metaName, $metaDataType, $metaDescription, $definationType, $noOfDecimals, $isRequired, $minRange, $maxRange, $sequence, $metaGroup, $scope, $status)
    {
        $orgMetaData = new OrgMetadata();
        $orgMetaData->setMetaKey($metaKey);
        $orgMetaData->setMetaName($metaName);
        $orgMetaData->setMetadataType($metaDataType);
        $orgMetaData->setMetaDescription($metaDescription);
        $orgMetaData->setDefinitionType($definationType);
        $orgMetaData->setNoOfDecimals($noOfDecimals);
        $orgMetaData->setIsRequired($isRequired);
        $orgMetaData->setMinRange($minRange);
        $orgMetaData->setMaxRange($maxRange);
        $orgMetaData->setSequence($sequence);
        $orgMetaData->setMetaGroup($metaGroup);
        $orgMetaData->setOrganization($this->getOrganizationInstance());
        $orgMetaData->setScope($scope);
        $orgMetaData->setStatus($status);
        return $orgMetaData;
    }

    /**
     * @return Organization
     */
    private function getOrganizationInstance()
    {
        $organization = new Organization();
        $organization->setCampusId(2);
        return $organization;
    }

    /**
     * @param integer $metadataValue
     * @param object $orgMetadataMaster
     * @return PersonOrgMetadata
     */
    private function createPersonOrgMetadataEntityObject($metadataValue, $orgMetadataMaster)
    {
        $personOrgMetadata = new PersonOrgMetadata();
        $personOrgMetadata->setMetadataValue($metadataValue);
        $personOrgMetadata->setOrgMetadata($orgMetadataMaster);
        return $personOrgMetadata;
    }

    /**
     * @param integer $id
     * @param string $listName
     * @param integer $listValue
     * @param object $orgMetadata
     * @param integer $sequence
     * @return OrgMetadataListValues
     */
    private function createOrgMetadataListValuesEntityObject($id, $listName, $listValue, $orgMetadata, $sequence)
    {
        $orgMetadataListValues = new TestOrgMetadataListValues();
        $orgMetadataListValues->setId($id);
        $orgMetadataListValues->setListName($listName);
        $orgMetadataListValues->setListValue($listValue);
        $orgMetadataListValues->setOrgMetadata($orgMetadata);
        $orgMetadataListValues->setSequence($sequence);
        return $orgMetadataListValues;
    }

    /**
     * @param string $definitionType
     * @param string $decimalPoints
     * @param string $calenderAssignment
     * @param integer $id
     * @param boolean $isMetaDataMapped
     * @param string $listName
     * @param integer $listValue
     * @param array $categoryType
     * @param string $displayName
     * @param array $fieldNameCanBeEditedIfMetaDataMapped
     * @param string $isRequired
     * @param string $itemDataType
     * @param string $itemLabel
     * @param string $itemSubtext
     * @param integer $langId
     * @param integer $maxDigits
     * @param integer $minDigits
     * @param string $numberType
     * @param integer $organizationId
     * @param string $profileBlockId
     * @param string $profileBlockName
     * @param string $sequenceNo
     * @param string $status
     * @return ProfileDto
     */
    public function createProfileDTO($definitionType, $decimalPoints, $calenderAssignment, $id, $isMetaDataMapped, $listName, $listValue, $categoryType, $displayName, $fieldNameCanBeEditedIfMetaDataMapped, $isRequired, $itemDataType, $itemLabel, $itemSubtext, $langId, $maxDigits, $minDigits, $numberType, $organizationId, $profileBlockId, $profileBlockName, $sequenceNo, $status)
    {
        $profileDto = new ProfileDto();
        $profileDto->setDefinitionType($definitionType);
        $profileDto->setDecimalPoints($decimalPoints);
        $profileDto->setCalenderAssignment($calenderAssignment);
        $profileDto->setId($id);
        $profileDto->setIsMetaDataMapped($isMetaDataMapped);
        $profileDto->setListName($listName);
        $profileDto->setListValue($listValue);
        $profileDto->setCategoryType($categoryType);
        $profileDto->setDisplayName($displayName);
        $profileDto->setFieldNameCanBeEditedIfMetaDataMapped($fieldNameCanBeEditedIfMetaDataMapped);
        $profileDto->setIsRequired($isRequired);
        $profileDto->setItemDataType($itemDataType);
        $profileDto->setItemLabel($itemLabel);
        $profileDto->setItemSubtext($itemSubtext);
        $profileDto->setLangId($langId);
        $profileDto->setMaxDigits($maxDigits);
        $profileDto->setMinDigits($minDigits);
        $profileDto->setNumberType($numberType);
        $profileDto->setOrganizationId($organizationId);
        $profileDto->setProfileBlockId($profileBlockId);
        $profileDto->setProfileBlockName($profileBlockName);
        $profileDto->setSequenceNo($sequenceNo);
        $profileDto->setStatus($status);
        return $profileDto;
    }

    public function testBuildOrgMetadataListValuesListValueMap()
    {
        $this->specify("Test buildOrgMetadataListValuesListValueMap", function ($orgMetadataListValuesObject, $personOrgMetadataData, $expectedResult) {
            $mockOrgMetadataListRepository = $this->getMock('OrgMetadataListValuesRepository', ['findBy']);
            $mockOrgMetadataListRepository->method('findBy')->will($this->returnValue($orgMetadataListValuesObject));
            $mockPersonOrgMetadataRepository = $this->getMock('personOrgMetadataRepository', ['findOneBy']);
            $mockPersonOrgMetadataRepository->method('findOneBy')->will($this->returnValue(!empty($personOrgMetadataData) ? true : false));
            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
                    [OrgMetadataListValuesRepository::REPOSITORY_KEY, $mockOrgMetadataListRepository],
                    [PersonOrgMetadataRepository::REPOSITORY_KEY, $mockPersonOrgMetadataRepository],
                ]
            );
            $orgPermissionSetService = new OrgProfileService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $result = $orgPermissionSetService->buildOrgMetadataListValuesListValueMap($orgMetadataListValuesObject);
            $this->assertEquals($result, $expectedResult);
        }, [
            'examples' => [
                [ // Example 1 test with valid data
                    [
                        $this->createOrgMetadataListValuesEntityObject(237348, 'test1', '1', $this->exampleDataForOrgProfileData()['example_1'], 0),
                        $this->createOrgMetadataListValuesEntityObject(237349, 'test2', '2', $this->exampleDataForOrgProfileData()['example_1'], 0),
                        $this->createOrgMetadataListValuesEntityObject(237350, 'test3', '3', $this->exampleDataForOrgProfileData()['example_1'], 0)
                    ],
                    [
                        $this->createPersonOrgMetadataEntityObject('2', $this->exampleDataForOrgProfileData()['example_1']),
                        $this->createPersonOrgMetadataEntityObject('3', $this->exampleDataForOrgProfileData()['example_1']),
                    ],
                    [// expected
                        "237348" => [
                            "has_values" => true,
                            "list_value" => "1",
                            'list_answer' => 'test1'
                        ],
                        "237349" => [
                            "has_values" => true,
                            "list_value" => "2",
                            'list_answer' => 'test2'
                        ],
                        "237350" => [
                            "has_values" => true,
                            "list_value" => "3",
                            'list_answer' => 'test3'
                        ]
                    ]
                ],
                [ // Example 2 test with empty person org metadata
                    [
                        $this->createOrgMetadataListValuesEntityObject(237348, 'test1', '1', $this->exampleDataForOrgProfileData()['example_3'], 0)
                    ],
                    [],
                    [ //expected
                        "237348" => [
                            "has_values" => false,
                            "list_value" => "1",
                            'list_answer' => 'test1'
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testBuildAndPersistListValueObject()
    {
        $this->specify("Test buildAndPersistListValueObject", function ($orgMetadataListValuesObject, $orgMetadataMaster, $metadataListValueProfileDto, $expectedResult) {

            $mockOrgMetadataListRepository = $this->getMock('OrgMetadataListValuesRepository', ['persist']);
            $mockOrgMetadataListRepository->method('persist')->will($this->returnValue(true));

            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
                    [OrgMetadataListValuesRepository::REPOSITORY_KEY, $mockOrgMetadataListRepository]
                ]
            );

            $orgPermissionSetService = new OrgProfileService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $result = $orgPermissionSetService->buildAndPersistListValueObject($orgMetadataListValuesObject, $orgMetadataMaster, $metadataListValueProfileDto);
            $this->assertEquals($result, $expectedResult);
        }, [
            'examples' => [
                [ // test with mapping in which can_edit_list_row is true
                    $this->createOrgMetadataListValuesEntityObject(237348, 'test1', '1', $this->exampleDataForOrgProfileData()['example_1'], 0),
                    $this->exampleDataForOrgProfileData()['example_1'],
                    [
                        "can_edit_list_row" => true,
                        "answer" => "test3",
                        "value" => "3",
                        "sequence_no" => 0,
                        "org_metadata_list_value_id" => 237348
                    ],
                    $this->createOrgMetadataListValuesEntityObject(237348, 'test3', '3', $this->exampleDataForOrgProfileData()['example_1'], 0)
                ],
                [ // test with mapping not exist and false can_edit_list_row
                    $this->createOrgMetadataListValuesEntityObject(211148, 'test3', '3', $this->exampleDataForOrgProfileData()['example_1'], 0),
                    $this->exampleDataForOrgProfileData()['example_3'],
                    [
                        "can_edit_list_row" => false,
                        "answer" => "test",
                        "value" => "1",
                        "sequence_no" => 01,
                        "org_metadata_list_value_id" => 211148
                    ],
                    $this->createOrgMetadataListValuesEntityObject(211148, 'test', '1', $this->exampleDataForOrgProfileData()['example_3'], 1)
                ]
            ]
        ]);
    }

    public function testBuildOrgMetadataListValueObject()
    {
        $this->specify("Test buildOrgMetadataListValueObject", function ($orgMetadataListValuesObject, $orgMetadataMaster, $metadataListValueProfileDto, $expectedResult) {
            $orgPermissionSetService = new OrgProfileService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $result = $orgPermissionSetService->buildOrgMetadataListValueObject($orgMetadataListValuesObject, $orgMetadataMaster, $metadataListValueProfileDto);
            $this->assertEquals($result, $expectedResult);
        }, [
            'examples' => [
                [ // test with building org metadata list value object having valid data
                    $this->createOrgMetadataListValuesEntityObject(211111, 'test3', '3', $this->exampleDataForOrgProfileData()['example_1'], 0),
                    $this->exampleDataForOrgProfileData()['example_1'],
                    [
                        "can_edit_list_row" => true,
                        "answer" => "test3",
                        "value" => "3",
                        "sequence_no" => 0,
                        "org_metadata_list_value_id" => 211111
                    ],
                    //expected
                    $this->createOrgMetadataListValuesEntityObject(211148, 'test3', '3', $this->exampleDataForOrgProfileData()['example_1'], 0)
                ],
                [ // test with deleted_at not null
                    $this->createOrgMetadataListValuesEntityObject(211111, 'test3', '3', $this->exampleDataForOrgProfileData()['example_1'], 0, true),
                    $this->exampleDataForOrgProfileData()['example_1'],
                    [
                        "can_edit_list_row" => true,
                        "answer" => "test3",
                        "value" => "3",
                        "sequence_no" => 0,
                        "org_metadata_list_value_id" => 211111
                    ],
                    //expected
                    $this->createOrgMetadataListValuesEntityObject(211148, 'test3', '3', $this->exampleDataForOrgProfileData()['example_1'], 0, true)
                ]
            ]
        ]);
    }

    public function testEditISPListItems()
    {
        $this->specify("Test editISPListItems", function ($exceptionCondition, $personHasValue, $dtoMetadataListValues, $orgMetadataMaster, $orgMetadataListValuesObject, $orgMetadataListValuesObjectWithFindOneBy, $isMetaDataMappedOnISPLevel, $expectedResult) {

            $mockOrgMetadataListRepository = $this->getMock('OrgMetadataListValuesRepository', ['findOneBy', 'findBy', 'persist']);
            $mockOrgMetadataListRepository->method('findOneBy')->will($this->returnValue($orgMetadataListValuesObjectWithFindOneBy));
            $mockOrgMetadataListRepository->method('findBy')->will($this->returnValue($orgMetadataListValuesObject));

            $mockPersonOrgMetadataRepository = $this->getMock('personOrgMetadataRepository', ['findOneBy']);

            if (is_null($exceptionCondition)) {
                $mockPersonOrgMetadataRepository->expects($this->any())->method('findOneBy')->will($this->onConsecutiveCalls($personHasValue[0], $personHasValue[1], $personHasValue[2]));
            } else {
                $mockPersonOrgMetadataRepository->method('findOneBy')->will($this->throwException(new SynapseValidationException($expectedResult)));
            }

            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
                    [OrgMetadataListValuesRepository::REPOSITORY_KEY, $mockOrgMetadataListRepository],
                    [PersonOrgMetadataRepository::REPOSITORY_KEY, $mockPersonOrgMetadataRepository]
                ]
            );

            try {
                $orgPermissionSetService = new OrgProfileService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
                $result = $orgPermissionSetService->editISPListItems($dtoMetadataListValues, $orgMetadataMaster, $isMetaDataMappedOnISPLevel);
                $this->assertEquals($result, $expectedResult);
            } catch (SynapseValidationException $e) {
                $errorMessage = $e->getMessage();
                $this->assertEquals($errorMessage, $expectedResult);
            }
        }, [
            'examples' => [
                [ // example 1 test with insertion
                    null,//exceptionCondition
                    [0 => null, 1 => null, 2 => null], //personOrgMetadata has_value
                    [ //dtoMetadataListValues
                        [
                            "can_edit_list_row" => false,
                            "answer" => "test",
                            "value" => 9,
                            "sequence_no" => 0,
                            "org_metadata_list_value_id" => 237311
                        ]
                    ],
                    // orgMetadataMaster
                    $this->exampleDataForOrgProfileData()['example_1'],
                    [ //orgMetadataListValuesObject
                        0 => $this->createOrgMetadataListValuesEntityObject(237311, 'test3', 3, $this->exampleDataForOrgProfileData()['example_1'], 0)
                    ],
                    $this->createOrgMetadataListValuesEntityObject(237311, 'test3', 3, $this->exampleDataForOrgProfileData()['example_1'], 0),
                    true, //isMetaDataMappedOnISPLevel
                    $this->createOrgMetadataListValuesEntityObject(237311, 'test', 9, $this->exampleDataForOrgProfileData()['example_1'], 0)
                ],
                [ // example 2 test with student associated
                    null,//exceptionCondition
                    [0 => true, 1 => true, 2 => true
                    ], //personOrgMetadata has_value
                    [ //dtoMetadataListValues
                        [
                            "can_edit_list_row" => true,
                            "answer" => "test3",
                            "value" => 2,
                            "sequence_no" => 0,
                            "org_metadata_list_value_id" => 237348
                        ],
                        [
                            "can_edit_list_row" => true,
                            "answer" => "test2",
                            "value" => 3,
                            "sequence_no" => 0,
                            "org_metadata_list_value_id" => 237349
                        ],
                        [
                            "can_edit_list_row" => false,
                            "answer" => "test4",
                            "value" => 6,
                            "sequence_no" => 0,
                            "org_metadata_list_value_id" => 237350
                        ]
                    ],
                    $this->exampleDataForOrgProfileData()['example_1'],
                    [ //orgMetadataListValuesObject
                        0 => $this->createOrgMetadataListValuesEntityObject(237348, 'test3', 3, $this->exampleDataForOrgProfileData()['example_1'], 0),
                        1 => $this->createOrgMetadataListValuesEntityObject(237349, 'test2', 2, $this->exampleDataForOrgProfileData()['example_1'], 0),
                        2 => $this->createOrgMetadataListValuesEntityObject(237350, 'test4', 4, $this->exampleDataForOrgProfileData()['example_1'], 0)
                    ],
                    $this->createOrgMetadataListValuesEntityObject(237348, 'test3', 3, $this->exampleDataForOrgProfileData()['example_1'], 0),
                    true, //isMetaDataMappedOnISPLevel
                    "The requested option value cannot be edited, as there are values associated with students."
                ],
                [ // example 3 test with invalid org metadata id passed
                    true, // exceptionCondition
                    [0 => false],
                    [ //dtoMetadataListValues
                        [
                            "can_edit_list_row" => false,
                            "answer" => "test",
                            "value" => 2,
                            "sequence_no" => 0,
                            "org_metadata_list_value_id" => 232323
                        ]
                    ],
                    // orgMetadataMaster
                    $this->exampleDataForOrgProfileData()['example_1'],
                    [ //orgMetadataListValuesObject
                        0 => $this->createOrgMetadataListValuesEntityObject(232323, 'sky', 8, $this->exampleDataForOrgProfileData()['example_1'], 0)
                    ],
                    $this->createOrgMetadataListValuesEntityObject(232323, 'sky', 8, $this->exampleDataForOrgProfileData()['example_1'], 0),
                    false, //isMetaDataMappedOnISPLevel
                    "The org metadata list value ID passed was invalid" //expected
                ],
                [ // example 5 test with student associated exception
                    null,//exceptionCondition
                    [0 => true, 1 => null, 2 => true], //personOrgMetadata has_value
                    [ //dtoMetadataListValues
                        [
                            "can_edit_list_row" => false,
                            "answer" => "test exception",
                            "value" => 898193,
                            "sequence_no" => 0,
                            "org_metadata_list_value_id" => null
                        ]
                    ],
                    $this->exampleDataForOrgProfileData()['example_1'],
                    [ //orgMetadataListValuesObject
                        0 => $this->createOrgMetadataListValuesEntityObject(237647, 'Yes', 1, $this->exampleDataForOrgProfileData()['example_1'], 0),
                        1 => $this->createOrgMetadataListValuesEntityObject(237648, 'No', 2, $this->exampleDataForOrgProfileData()['example_1'], 0),
                        2 => $this->createOrgMetadataListValuesEntityObject(237649, 'MayBe', 0, $this->exampleDataForOrgProfileData()['example_1'], 0)
                    ],
                    $this->createOrgMetadataListValuesEntityObject(237348, 'test3', 3, $this->exampleDataForOrgProfileData()['example_1'], 0),
                    true, //isMetaDataMappedOnISPLevel
                    "The requested option list value cannot be edited, as there are values associated with students."
                ],
                [ // example 6 test with The requested option id is invalid
                    null,//exceptionCondition
                    [0 => null, 1 => null, 2 => null],
                    [ //dtoMetadataListValues
                        [
                            "can_edit_list_row" => false,
                            "answer" => "uni",
                            "value" => 11111,
                            "sequence_no" => 0,
                            "org_metadata_list_value_id" => ''
                        ]
                    ],
                    $this->exampleDataForOrgProfileData()['example_1'],
                    [ //orgMetadataListValuesObject
                        0 => $this->createOrgMetadataListValuesEntityObject(237348, 'test3', 3, $this->exampleDataForOrgProfileData()['example_1'], 0)
                    ],
                    $this->createOrgMetadataListValuesEntityObject(237348, 'test3', 3, $this->exampleDataForOrgProfileData()['example_1'], 0),
                    true, //isMetaDataMappedOnISPLevel
                    "The requested option ID is invalid."
                ]
            ]
        ]);
    }

    public function testEditProfile()
    {
        $this->specify("Test editProfile", function ($orgMetadataListValuesObjectWithFindOneBy, $orgMetadataListValuesObject, $profileDto, $orgProfile, $personHasValue, $valuesForListValue, $expectedResult) {
            $mockRbacManager = $this->getMock('RbacManager', ['checkAccessToOrganization']);
            $mockRbacManager->method('checkAccessToOrganizsation')->willReturn(true);
            $mockValidator = $this->getMock('Validator', ['validate']);
            $this->mockContainer->method('get')->willReturnMap(
                [
                    [Manager::SERVICE_KEY, $mockRbacManager],
                    [SynapseConstant::VALIDATOR, $mockValidator]
                ]
            );

            $mockOrgMetadataRepository = $this->getMock('OrgMetadataRepository', ['findOneBy', 'flush']);

            if (is_null($orgProfile)) {
                $mockOrgMetadataRepository->method('findOneBy')->will($this->throwException(new SynapseValidationException('Profile not found')));
            } else {
                $mockOrgMetadataRepository->method('findOneBy')->will($this->returnValue($orgProfile));
            }

            $mockPersonOrgMetadataRepository = $this->getMock('personOrgMetadataRepository', ['findOneBy']);
            $mockPersonOrgMetadataRepository->expects($this->any())->method('findOneBy')->will($this->onConsecutiveCalls($valuesForListValue, $personHasValue));
            $mockPersonRepository = $this->getMock('PersonRepository', ['getPredefinedProfile']);
            $mockPersonRepository->method('getPredefinedProfile')->will($this->returnValue([]));
            $mockEbiMetadataRepository = $this->getMock('EbiMetadataRepository', ['IsEbiProfileExists']);
            $mockEbiMetadataRepository->method('IsEbiProfileExists')->will($this->returnValue(true));
            $mockOrgMetadataListRepository = $this->getMock('OrgMetadataListValuesRepository', ['findOneBy', 'findBy', 'persist', 'remove']);
            $mockOrgMetadataListRepository->method('findBy')->will($this->returnValue($orgMetadataListValuesObject));
            $mockOrgMetadataListRepository->method('findOneBy')->will($this->returnValue($orgMetadataListValuesObjectWithFindOneBy));

            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
                    [OrgMetadataRepository::REPOSITORY_KEY, $mockOrgMetadataRepository],
                    [PersonOrgMetadataRepository::REPOSITORY_KEY, $mockPersonOrgMetadataRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [EbiMetadataRepository::REPOSITORY_KEY, $mockEbiMetadataRepository],
                    [OrgMetadataListValuesRepository::REPOSITORY_KEY, $mockOrgMetadataListRepository]
                ]
            );

            try {
                $orgPermissionSetService = new OrgProfileService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
                $result = $orgPermissionSetService->editProfile($profileDto);
                $this->assertEquals($result, $expectedResult);
            } catch (SynapseValidationException $e) {
                $errorMessage = $e->getMessage();
                $this->assertEquals($errorMessage, $expectedResult);
            }

        }, [
            'examples' => [
                [ //Example 1 : test with empty org metadata master
                    $this->createOrgMetadataListValuesEntityObject(237111, 'test3', 3, $this->exampleDataForOrgProfileData()['example_1'], 0),
                    [ //orgMetadataListValuesObject
                        0 => $this->createOrgMetadataListValuesEntityObject(237111, 'test3', 3, $this->exampleDataForOrgProfileData()['example_1'], 0)
                    ],
                    $this->createProfileDTO('O', null, 'N', null, 1, null, null, $this->exampleDataForCategoryTypeDto()['example_1'], 'Test Category-7597', [], null, 'S', 'TestCategoryHeader', '', null, null, null, null, null, null, null, '', 'active'),
                    null,
                    false, //person org has value
                    true, //mapping ISP
                    'Profile not found'
                ],
                [ //Example 2 : wrong metadata selected during mapping
                    $this->createOrgMetadataListValuesEntityObject(237111, 'test3', 3, $this->exampleDataForOrgProfileData()['example_3'], 0),
                    [
                        0 => $this->createOrgMetadataListValuesEntityObject(237111, 'test3', 3, $this->exampleDataForOrgProfileData()['example_3'], 0)
                    ],
                    $this->createProfileDTO('O', null, 'N', null, 1, null, null, $this->exampleDataForCategoryTypeDto()['example_1'], 'Test Category-7597', [], null, 'S', 'TestCategoryHeader', '', null, null, null, null, null, null, null, '', 'active'),
                    $this->exampleDataForOrgProfileData()['example_3'],
                    false, //personHasValue
                    true, //mapping ISP
                    'Wrong metadata type selected'
                ],
                [ //Example 3 : test with mapped ISP in category metadata type
                    $this->createOrgMetadataListValuesEntityObject(237111, 'test3', 3, $this->exampleDataForOrgProfileData()['example_1'], 0),
                    [ //orgMetadataListValuesObject
                        0 => $this->createOrgMetadataListValuesEntityObject(237111, 'test3', 3, $this->exampleDataForOrgProfileData()['example_1'], 0)
                    ],
                    $this->createProfileDTO('O', null, 'N', null, 0, null, null, $this->exampleDataForCategoryTypeDto()['example_2'], 'Test Category-7597', [], null, 'S', 'TestCategoryHeader', '', null, null, null, null, null, null, null, '', 'active'),
                    $this->createOrgMetadataEntityObject('TestCategoryHeader', 'Test Category-7597', 'S', '', 'O', 0, false, '0.0000', '0.0000', '', null, 'N', 'active'),
                    false,
                    $this->createPersonOrgMetadataEntityObject('2', $this->exampleDataForOrgProfileData()['example_1']),
                    $this->createProfileDTO('O', null, 'N', null, 0, null, null, $this->exampleDataForCategoryTypeDto()['example_2'], 'Test Category-7597', [], null, 'S', 'TestCategoryHeader', '', null, null, null, null, null, null, null, '', 'active'),
                ],
                [ //Example 4 : test with number metadata type
                    $this->createOrgMetadataListValuesEntityObject(null, null, null, null, null),
                    [
                        0 => $this->createOrgMetadataListValuesEntityObject(null, null, null, null, null)
                    ],
                    $this->createProfileDTO('O', null, 'Y', 7570, 1, null, null, null, 'ABCD', ["min_digits" => true, "max_digits" => true, "item_subtext" => true], null, 'N', 'ABCD', 'asdasdasd', null, 10, 1, ['min_digits' => 1.0000, 'max_digits' => 10.0000, 'decimal_points' => 0], null, null, null, 12, 'active'),
                    $this->createOrgMetadataEntityObject('ABCD', 'abcd', 'N', 'asdasdasd', 'O', 0, 0, 1.0000, 10.0000, 12, null, 'Y', 'active'),
                    true,
                    $this->createPersonOrgMetadataEntityObject('2', $this->exampleDataForOrgProfileData()['example_4']),
                    $this->createProfileDTO('O', null, 'Y', 7570, 1, null, null, null, 'ABCD', ["min_digits" => true, "max_digits" => true, "item_subtext" => true], null, 'N', 'ABCD', 'asdasdasd', null, 10, 1, ['min_digits' => 1.0000, 'max_digits' => 10.0000, 'decimal_points' => 0], null, null, null, 12, 'active'),
                ],
                [ //Example 5 : invalid calender assignment
                    $this->createOrgMetadataListValuesEntityObject(null, null, null, null, null),
                    [
                        0 => $this->createOrgMetadataListValuesEntityObject(null, null, null, null, null)
                    ],
                    $this->createProfileDTO('O', null, 'A', 7570, 1, null, null, null, 'ABCD', ["min_digits" => true, "max_digits" => true, "item_subtext" => true], null, 'N', 'ABCD', 'asdasdasd', null, 10, 1, ['min_digits' => 1.0000, 'max_digits' => 10.0000, 'decimal_points' => 0], null, null, null, 12, 'active'),
                    $this->createOrgMetadataEntityObject('ABCD', 'abcd', 'N', 'asdasdasd', 'O', 0, 0, 1.0000, 10.0000, 12, null, 'Y', 'active'),
                    true,
                    $this->createPersonOrgMetadataEntityObject('2', $this->exampleDataForOrgProfileData()['example_4']),
                    'The calender assignment is invalid.'
                ],
                [ //Example 6 : min_digits is greater than the minRange
                    $this->createOrgMetadataListValuesEntityObject(null, null, null, null, null),
                    [
                        0 => $this->createOrgMetadataListValuesEntityObject(null, null, null, null, null)
                    ],
                    $this->createProfileDTO('O', null, 'A', 7570, 1, null, null, null, 'ABCD', ["min_digits" => true, "max_digits" => true, "item_subtext" => true], null, 'N', 'ABCD', 'asdasdasd', null, 10, 1, ['min_digits' => 2.0000, 'max_digits' => 10.0000, 'decimal_points' => 0], null, null, null, 12, 'active'),
                    $this->createOrgMetadataEntityObject('ABCD', 'abcd', 'N', 'asdasdasd', 'O', 0, 0, 1.0000, 10.0000, 12, null, 'Y', 'active'),
                    true,
                    $this->createPersonOrgMetadataEntityObject('2', $this->exampleDataForOrgProfileData()['example_4']),
                    'Min Digits cannot be larger than current value.'
                ],
                [ //Example 7 : max_digits is greater than the maxRange
                    $this->createOrgMetadataListValuesEntityObject(null, null, null, null, null),
                    [
                        0 => $this->createOrgMetadataListValuesEntityObject(null, null, null, null, null)
                    ],
                    $this->createProfileDTO('O', null, 'A', 7570, 1, null, null, null, 'ABCD', ["min_digits" => true, "max_digits" => true, "item_subtext" => true], null, 'N', 'ABCD', 'asdasdasd', null, 10, 1, ['min_digits' => 1.0000, 'max_digits' => 9.0000, 'decimal_points' => 0], null, null, null, 12, 'active'),
                    $this->createOrgMetadataEntityObject('ABCD', 'abcd', 'N', 'asdasdasd', 'O', 0, 0, 1.0000, 10.0000, 12, null, 'Y', 'active'),
                    true,
                    $this->createPersonOrgMetadataEntityObject('2', $this->exampleDataForOrgProfileData()['example_4']),
                    'Max Digits cannot be smaller than current value.'
                ],
                [ //Example 8 : minRange is null
                    $this->createOrgMetadataListValuesEntityObject(null, null, null, null, null),
                    [
                        0 => $this->createOrgMetadataListValuesEntityObject(null, null, null, null, null)
                    ],
                    $this->createProfileDTO('O', null, 'Y', 7570, 1, null, null, null, 'ABCD', ["min_digits" => true, "max_digits" => true, "item_subtext" => true], null, 'N', 'ABCD', 'asdasdasd', null, 10, null, ['min_digits' => 1.0000, 'max_digits' => 10.0000, 'decimal_points' => 0], null, null, null, 12, 'active'),
                    $this->createOrgMetadataEntityObject('ABCD', 'abcd', 'N', 'asdasdasd', 'O', 0, 0, 1.0000, 10.0000, 12, null, 'Y', 'active'),
                    true,
                    $this->createPersonOrgMetadataEntityObject('2', $this->exampleDataForOrgProfileData()['example_4']),
                    $this->createProfileDTO('O', null, 'Y', 7570, 1, null, null, null, 'ABCD', ["min_digits" => true, "max_digits" => true, "item_subtext" => true], null, 'N', 'ABCD', 'asdasdasd', null, 10, null, ['min_digits' => 1.0000, 'max_digits' => 10.0000, 'decimal_points' => 0], null, null, null, 12, 'active'),
                ],
                [ //Example 9 : minRange is null
                    $this->createOrgMetadataListValuesEntityObject(null, null, null, null, null),
                    [
                        0 => $this->createOrgMetadataListValuesEntityObject(null, null, null, null, null)
                    ],
                    $this->createProfileDTO('O', null, 'Y', 7570, 1, null, null, null, 'ABCD', ["min_digits" => true, "max_digits" => true, "item_subtext" => true], null, 'N', 'ABCD', 'asdasdasd', null, null, 1, ['min_digits' => 1.0000, 'max_digits' => 10.0000, 'decimal_points' => 0], null, null, null, 12, 'active'),
                    $this->createOrgMetadataEntityObject('ABCD', 'abcd', 'N', 'asdasdasd', 'O', 0, 0, 1.0000, 10.0000, 12, null, 'Y', 'active'),
                    true,
                    $this->createPersonOrgMetadataEntityObject('2', $this->exampleDataForOrgProfileData()['example_4']),
                    $this->createProfileDTO('O', null, 'Y', 7570, 1, null, null, null, 'ABCD', ["min_digits" => true, "max_digits" => true, "item_subtext" => true], null, 'N', 'ABCD', 'asdasdasd', null, null, 1, ['min_digits' => 1.0000, 'max_digits' => 10.0000, 'decimal_points' => 0], null, null, null, 12, 'active'),
                ],
            ]
        ]);
    }

}