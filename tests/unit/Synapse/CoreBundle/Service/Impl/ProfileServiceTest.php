<?php

namespace Synapse\CoreBundle\Service\Impl;

use Codeception\Specify;
use Codeception\TestCase\Test;
use Consolidation\Log\Logger;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestMetadataRepository;
use Synapse\CoreBundle\Entity\DatablockMaster;
use Synapse\CoreBundle\Entity\EbiMetadata;
use Synapse\CoreBundle\Entity\EbiMetadataLang;
use Synapse\CoreBundle\Entity\LanguageMaster;
use Synapse\CoreBundle\Repository\DatablockMasterRepository;
use Synapse\CoreBundle\Repository\DatablockMetadataRepository;
use Synapse\CoreBundle\Repository\EbiMetadataLangRepository;
use Synapse\CoreBundle\Repository\EbiMetadataListValuesRepository;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\LanguageMasterRepository;
use Synapse\CoreBundle\Repository\OrgMetadataRepository;
use Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\ProfileDto;
use Synapse\RestBundle\Entity\ReOrderProfileDto;
use Synapse\RestBundle\Exception\ValidationException;


class ProfileServiceTest extends Test
{
    use Specify;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRepositoryResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContainer;

    protected function _before()
    {
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
        $this->mockLogger = $this->getMock('Logger', array('debug', 'error'));
        $this->mockContainer = $this->getMock('Container', array('get'));
    }

    public function testGetDatablocksAndBlockitemsWithYearAndTermInformation()
    {

        $this->specify("Test getting profile datablock and block items with year and term information function", function ($organizationId, $facultyId, $blockItems, $ebiMetaKeys, $expectedResult) {

            // Repository Mocks
            $mockPersonEbiMetadataRepository = $this->getMock("PersonEbiMetaDataRepository", ['getProfileBlockWithBlockitemAndYearTermInformation']);

            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonEbiMetaDataRepository::REPOSITORY_KEY,
                        $mockPersonEbiMetadataRepository
                    ]

                ]);

            $mockPersonEbiMetadataRepository->expects($this->any())->method('getProfileBlockWithBlockitemAndYearTermInformation')
                ->willReturn($blockItems);

            $profileBlockService = new ProfileService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $results = $profileBlockService->getDatablocksAndBlockitemsWithYearAndTermInformation($facultyId, $organizationId, $ebiMetaKeys);
             $this->assertEquals($expectedResult, $results);

        }, [
            'examples' => [
                // Example 1 Test with blank ebi meta key
                [
                    2,
                    2,
                    [
                        [
                            'datablock_id' => 13,
                            'datablock_name' => 'Demographic',
                            'ebi_metadata_id' => 1,
                            'display_name' => 'Gender',
                            'item_data_type' => 'S',
                            'calendar_assignment' => 'T',
                            'year_id' => '201617',
                            'org_academic_year_id' => 163,
                            'year_name' => '2016-2017',
                            'org_academic_terms_id' => 234,
                            'term_name' => 'Last Year',
                            'is_current_academic_year' => 1

                        ],
                        [
                            'datablock_id' => 13,
                            'datablock_name' => 'Demographic',
                            'ebi_metadata_id' => 2,
                            'display_name' => 'BirthYear',
                            'item_data_type' => 'N',
                            'calendar_assignment' => 'Y',
                            'year_id' => '201516',
                            'org_academic_year_id' => 162,
                            'year_name' => '2015-2016',
                            'org_academic_terms_id' => '',
                            'term_name' => '',
                            'is_current_academic_year' => 0

                        ],
                        [
                            'datablock_id' => 14,
                            'datablock_name' => 'Admissions',
                            'ebi_metadata_id' => 3,
                            'display_name' => 'StateInOut',
                            'item_data_type' => 'S',
                            'calendar_assignment' => 'N',
                            'year_id' => '',
                            'org_academic_year_id' => '',
                            'year_name' => '',
                            'org_academic_terms_id' => '',
                            'term_name' => '',
                            'is_current_academic_year' => 1

                        ],
                        [
                            'datablock_id' => 14,
                            'datablock_name' => 'Admissions',
                            'ebi_metadata_id' => 4,
                            'display_name' => 'InternationalStudent',
                            'item_data_type' => 'S',
                            'calendar_assignment' => 'N',
                            'year_id' => '',
                            'org_academic_year_id' => '',
                            'year_name' => '',
                            'org_academic_terms_id' => '',
                            'term_name' => '',
                            'is_current_academic_year' => 1

                        ]
                    ],
                    '',
                    [
                        "profile_blocks" => [
                            [
                                "id" => 13,
                                "display_name" => "Demographic",
                                "profile_items" =>
                                    [
                                        [
                                            "id" => 1,
                                            "item_data_type" => 'S',
                                            "display_name" => "Gender",
                                            "calendar_assignment" => 'T',
                                            "year_term" => [
                                                [
                                                    "year" => '201617',
                                                    "year_id" => 163,
                                                    "year_name" => '2016-2017',
                                                    "term_id" => 234,
                                                    "term_name" => 'Last Year',
                                                    "is_current_academic_year" => 1
                                                ]
                                            ]

                                        ],
                                        [
                                            "id" => 2,
                                            "item_data_type" => 'N',
                                            "display_name" => "BirthYear",
                                            "calendar_assignment" => 'Y',
                                            "year_term" => [
                                                [
                                                    "year" => '201516',
                                                    "year_id" => 162,
                                                    "year_name" => '2015-2016',
                                                    "term_id" => '',
                                                    "term_name" => '',
                                                    "is_current_academic_year" => 0
                                                ]
                                            ]

                                        ]
                                    ],

                            ],
                            [
                                "id" => 14,
                                "display_name" => "Admissions",
                                "profile_items" =>
                                    [
                                        [
                                            "id" => 3,
                                            "item_data_type" => 'S',
                                            "display_name" => "StateInOut",
                                            "calendar_assignment" => 'N'

                                        ],
                                        [
                                            "id" => 4,
                                            "item_data_type" => 'S',
                                            "display_name" => "InternationalStudent",
                                            "calendar_assignment" => 'N'

                                        ]
                                    ]
                            ]
                        ]
                    ]
                ],
                //Example 2 Test with only one ebi meta key
                [
                    220115,
                    62,
                    [
                        [
                            "ebi_metadata_id" => "83",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermGPA",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "109",
                            "term_name" => "Spring",
                            'is_current_academic_year' => 1
                        ],
                        [
                            "ebi_metadata_id" => "83",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermGPA",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            'is_current_academic_year' => 1
                        ]
                    ],
                    ['EndTermGPA'],
                    [
                        "profile_blocks" => [
                            [
                                "id" => "20",
                                "display_name" => "Academic Record-End",
                                "profile_items" => [
                                    [
                                        "id" => "83",
                                        "item_data_type" => "N",
                                        "display_name" => "EndTermGPA",
                                        "calendar_assignment" => "T",
                                        "year_term" => [
                                            [
                                                "year" => "201516",
                                                "year_id" => "48",
                                                "year_name" => "2015-2016",
                                                "term_id" => "109",
                                                "term_name" => "Spring",
                                                "is_current_academic_year" => 1
                                            ],
                                            [
                                                "year" => "201516",
                                                "year_id" => "48",
                                                "year_name" => "2015-2016",
                                                "term_id" => "108",
                                                "term_name" => "Fall",
                                                "is_current_academic_year" => 1
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                //Example 3 Test with more than one ebi meta key
                [
                    220115,
                    62,
                    [
                        [
                            "ebi_metadata_id" => "5",
                            "datablock_id" => "14",
                            "datablock_name" => "Admissions",
                            "display_name" => "StateInOut",
                            "item_data_type" => "S",
                            "calendar_assignment" => "N",
                            "org_academic_year_id" => null,
                            "year_id" => null,
                            "year_name" => null,
                            "org_academic_terms_id" => null,
                            "term_name" => null,
                            'is_current_academic_year' => 0
                        ],
                        [
                            "ebi_metadata_id" => "83",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermGPA",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "109",
                            "term_name" => "Spring",
                            'is_current_academic_year' => 0
                        ],
                        [
                            "ebi_metadata_id" => "83",
                            "datablock_id" => "20",
                            "datablock_name" => "Academic Record-End",
                            "display_name" => "EndTermGPA",
                            "item_data_type" => "N",
                            "calendar_assignment" => "T",
                            "org_academic_year_id" => "48",
                            "year_id" => "201516",
                            "year_name" => "2015-2016",
                            "org_academic_terms_id" => "108",
                            "term_name" => "Fall",
                            'is_current_academic_year' => 0
                        ]
                    ],
                    ['EndTermGPA,StateInOut'],
                    [
                        "profile_blocks" => [
                            [
                                "id" => "14",
                                "display_name" => "Admissions",
                                "profile_items" => [
                                    [
                                        "id" => "5",
                                        "item_data_type" => "S",
                                        "display_name" => "StateInOut",
                                        "calendar_assignment" => "N"
                                    ]
                                ]
                            ],
                            [
                                "id" => "20",
                                "display_name" => "Academic Record-End",
                                "profile_items" => [
                                    [
                                        "id" => "83",
                                        "item_data_type" => "N",
                                        "display_name" => "EndTermGPA",
                                        "calendar_assignment" => "T",
                                        "year_term" => [
                                            [
                                                "year" => "201516",
                                                "year_id" => "48",
                                                "year_name" => "2015-2016",
                                                "term_id" => "109",
                                                "term_name" => "Spring",
                                                "is_current_academic_year" => 0
                                            ],
                                            [
                                                "year" => "201516",
                                                "year_id" => "48",
                                                "year_name" => "2015-2016",
                                                "term_id" => "108",
                                                "term_name" => "Fall",
                                                "is_current_academic_year" => 0
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testCreateProfile()
    {
        $this->specify("Create profile Items", function ($profileInput, $expectedResult, $errorMessage) {
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

            $mockOrgProfileService = $this->getMock('OrgProfileService', ['checkExistingColumnNames']);
            $mockLoggerHelperService = $this->getMock('LoggerHelperService', ['getLog']);
            $mockLanguageMasterService = $this->getMock('LanguageMasterService', ['getLangRepository']);
            $mockValidatorService = $this->getMock('validator', ['validate']);

            // Scaffolding for Services
            $mockContainer->method('get')->willReturnMap(
                [
                    [OrgProfileService::SERVICE_KEY, $mockOrgProfileService],
                    [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService],
                    [LanguageMasterService::SERVICE_KEY, $mockLanguageMasterService],
                    [SynapseConstant::VALIDATOR, $mockValidatorService]
                ]);

            $mockLanguageMasterRepository = $this->getMock('LanguageMasterRepository', ['find']);
            $mockEbiMetadataRepository = $this->getMock('EbiMetadataRepository', ['getEbiProfileCount', 'create', 'flush']);
            $mockEbiMetadataLangRepository = $this->getMock('EbiMetadataLangRepository', ['persist']);
            $mockDataBlockMasterRepository = $this->getMock('DatablockMasterRepository', ['find']);
            $mockDataBlockMetadataRepository = $this->getMock('DatablockMetadataRepository', ['persist']);
            $mockPersonRepository = $this->getMock('PersonRepository', ['getPredefinedProfile']);
            $mockOrgMetadataRepository = $this->getMock('OrgMetadataRepository', ['IsOrgProfileExists']);

            $mockRepositoryResolver->expects($this->any())
                ->method('getRepository')
                ->willReturnMap([
                    [EbiMetadataRepository::REPOSITORY_KEY, $mockEbiMetadataRepository],
                    [EbiMetadataLangRepository::REPOSITORY_KEY, $mockEbiMetadataLangRepository],
                    [DatablockMasterRepository::REPOSITORY_KEY, $mockDataBlockMasterRepository],
                    [DatablockMetadataRepository::REPOSITORY_KEY, $mockDataBlockMetadataRepository],
                    [LanguageMasterRepository::REPOSITORY_KEY, $mockLanguageMasterRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrgMetadataRepository::REPOSITORY_KEY, $mockOrgMetadataRepository]
                ]);
            $mockLanguageMasterService->method('getLangRepository')->willReturn($mockLanguageMasterRepository);
            if ($profileInput['lang_id']) {
                $mockLanguageMasterRepository->method('find')->willReturn(new LanguageMaster());
            } else {
                $mockLanguageMasterRepository->method('find')->willThrowException(new ValidationException('', 'Language Not Found'));
            }
            if (strlen($profileInput['label']) > 50) {
                $errors = $this->arrayOfErrorObjects(['metadata_error' => 'Column header cannot be longer than 50 characters']);
                $mockValidatorService->method('validate')->willReturn($errors);
            }

            if ($profileInput['label'] == 'exists') {
                $mockPersonRepository->method('getPredefinedProfile')->willReturn([$profileInput['label']]);
            } else {
                $mockPersonRepository->method('getPredefinedProfile')->willReturn([]);
            }

            if ($profileInput['label'] == 'isp-exists') {
                $mockOrgMetadataRepository->method('IsOrgProfileExists')->willReturn(false);
            } else {
                $mockOrgMetadataRepository->method('IsOrgProfileExists')->willReturn(true);
            }

            if ($profileInput['profile_block']) {
                $mockDataBlockMasterRepository->method('find')->willReturn(new DatablockMaster());
            } else {
                $mockDataBlockMasterRepository->method('find')->willThrowException(new ValidationException('', 'Profile blocks not found .'));
            }
            $profileDto = $this->createProfileDto($profileInput);
            $mockEbiMetadataRepository->method('create')->willReturn(new EbiMetadata());
            try {
                $profileService = new ProfileService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $profileService->createProfile($profileDto);
                $this->assertEquals($result, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($e->getMessage(), $errorMessage);
            }

        }, [
            'examples' => [
                // The definition type is invalid should throw ValidationException
                [
                    [
                        'label' => 'Label',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'A',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 2,
                        'profile_block' => true
                    ],
                    NULL,
                    'Validation errors found',
                ],
                // The calendar assignment is invalid should throw ValidationException
                [
                    [
                        'label' => 'Label',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'A',
                        'profile_block_id' => 4,
                        'profile_block' => true
                    ],
                    NULL,
                    'Validation errors found',
                ],
                // Language id passed is not found should throw an exception.
                [
                    [
                        'label' => 'Label',
                        'name' => 'name',
                        'lang_id' => '',
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 2,
                        'profile_block' => true
                    ],
                    NULL,
                    'Language Not Found',
                ],
                // Invalid input is passed - label name is greater than the limit should throw an exception.
                [
                    [
                        'label' => 'Label name should not be greater than 50 characters. Please check',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 2,
                        'profile_block' => true
                    ],
                    NULL,
                    'Column header cannot be longer than 50 characters',
                ],
                // Profile item already exists should throw an exception.
                [
                    [
                        'label' => 'exists',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 7,
                        'profile_block' => true
                    ],
                    NULL,
                    'Profile item already exists as Person Record Information fields',
                ],
                // ISP is already exists should throw an exception.
                [
                    [
                        'label' => 'isp-exists',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 2,
                        'profile_block' => true
                    ],
                    NULL,
                    'ISP already exists',
                ],
                // Invalid profile block found should throw an exception.
                [
                    [
                        'label' => 'label',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 1000,
                        'profile_block' => false,
                    ],
                    NULL,
                    'Profile blocks not found .',
                ],
                // Create a valid profile item.
                [
                    [
                        'label' => 'label',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 10,
                        'profile_block' => true,
                    ],
                    $this->createProfileDto([
                        'label' => 'label',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 10,
                        'sequence_no' => 1
                    ]),
                    NULL,
                ],
            ]
        ]);
    }

    public function testUpdateProfile()
    {
        $this->specify("Update profile Items", function ($profileInput, $expectedResult, $errorMessage) {
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

            $mockLoggerHelperService = $this->getMock('LoggerHelperService', ['getLog']);
            $mockLanguageMasterService = $this->getMock('LanguageMasterService', ['getLangRepository']);
            $mockValidatorService = $this->getMock('validator', ['validate']);

            $mockContainer->method('get')->willReturnMap(
                [
                    [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService],
                    [LanguageMasterService::SERVICE_KEY, $mockLanguageMasterService],
                    [SynapseConstant::VALIDATOR, $mockValidatorService]
                ]);

            $mockLanguageMasterRepository = $this->getMock('LanguageMasterRepository', ['find']);
            $mockEbiMetadataRepository = $this->getMock('EbiMetadataRepository', ['find', 'flush']);
            $mockEbiMetadataLangRepository = $this->getMock('EbiMetadataLangRepository', ['findOneBy']);
            $mockDataBlockMasterRepository = $this->getMock('DatablockMasterRepository', ['find']);
            $mockDataBlockMetadataRepository = $this->getMock('DatablockMetadataRepository', ['findOneBy', 'persist']);
            $mockPersonRepository = $this->getMock('PersonRepository', ['getPredefinedProfile']);
            $mockOrgMetadataRepository = $this->getMock('OrgMetadataRepository', ['IsOrgProfileExists']);
            $mockPersonEbiMetadataRepository = $this->getMock('PersonEbiMetadataRepository', ['isDataAttched']);
            $mockPersonEbiMetaDataListValuesRepository = $this->getMock('EbiMetaDataListValuesRepository', ['findBy']);

            $mockRepositoryResolver->expects($this->any())
                ->method('getRepository')
                ->willReturnMap([
                    [PersonEbiMetaDataRepository::REPOSITORY_KEY, $mockPersonEbiMetadataRepository],
                    [EbiMetadataRepository::REPOSITORY_KEY, $mockEbiMetadataRepository],
                    [EbiMetadataLangRepository::REPOSITORY_KEY, $mockEbiMetadataLangRepository],
                    [DatablockMasterRepository::REPOSITORY_KEY, $mockDataBlockMasterRepository],
                    [DatablockMetadataRepository::REPOSITORY_KEY, $mockDataBlockMetadataRepository],
                    [LanguageMasterRepository::REPOSITORY_KEY, $mockLanguageMasterRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrgMetadataRepository::REPOSITORY_KEY, $mockOrgMetadataRepository],
                    [EbiMetadataListValuesRepository::REPOSITORY_KEY, $mockPersonEbiMetaDataListValuesRepository]
                ]);
            $mockLanguageMasterService->method('getLangRepository')->willReturn($mockLanguageMasterRepository);
            if ($profileInput['lang_id']) {
                $mockLanguageMasterRepository->method('find')->willReturn(new LanguageMaster());
            } else {
                $mockLanguageMasterRepository->method('find')->willThrowException(new ValidationException('', 'Language Not Found'));
            }

            if ($profileInput['id']) {
                $mockEbiMetadataRepository->method('find')->willReturn(new EbiMetadata());
                $mockPersonEbiMetadataRepository->method('isDataAttched')->willReturn($profileInput['profile_attached']);
            } else {
                $mockLanguageMasterRepository->method('find')->willThrowException(new ValidationException('', 'Profile not found .'));
            }

            if ($profileInput['profile_block']) {
                $mockDataBlockMasterRepository->method('find')->willReturn(new DatablockMaster());
            } else {
                $mockDataBlockMasterRepository->method('find')->willThrowException(new ValidationException('', 'Profile blocks not found .'));
            }

            if ($profileInput['meta_data_lang']) {
                $mockEbiMetadataLangRepository->method('findOneBy')->willReturn(new EbiMetadataLang());
            } else {
                $mockEbiMetadataLangRepository->method('findOneBy')->willThrowException(new ValidationException('', 'Profile Lang not found .'));
            }

            if ($profileInput['label'] == 'exists') {
                $mockPersonRepository->method('getPredefinedProfile')->willReturn([$profileInput['label']]);
            } else {
                $mockPersonRepository->method('getPredefinedProfile')->willReturn([]);
            }

            if ($profileInput['label'] == 'isp-exists') {
                $mockOrgMetadataRepository->method('IsOrgProfileExists')->willReturn(false);
            } else {
                $mockOrgMetadataRepository->method('IsOrgProfileExists')->willReturn(true);
            }

            $profileDto = $this->createProfileDto($profileInput);
            $mockEbiMetadataRepository->method('create')->willReturn(new EbiMetadata());
            try {
                $profileService = new ProfileService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $profileService->updateProfile($profileDto);
                $this->assertEquals($result, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($e->getMessage(), $errorMessage);
            }

        }, [
            'examples' => [
                // Language id passed is not found should throw an exception.
                [
                    [
                        'id' => 34,
                        'label' => 'Label',
                        'name' => 'name',
                        'lang_id' => '',
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 2,
                        'profile_block' => true,
                        'profile_attached' => false,
                        'meta_data_lang' => true,
                    ],
                    NULL,
                    'Language Not Found',
                ],
                // Passing empty profile id should throw an exception.
                [
                    [
                        'id' => '',
                        'label' => 'Label',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 2,
                        'profile_block' => true,
                        'profile_attached' => false,
                        'meta_data_lang' => true,
                    ],
                    NULL,
                    'Profile not found .',
                ],
                // Meta data attached with profile can not be updated, should throw an exception.
                [
                    [
                        'id' => 89,
                        'label' => 'Label',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 2,
                        'profile_block' => true,
                        'profile_attached' => true,
                        'meta_data_lang' => true,
                    ],
                    NULL,
                    'Ebi Meta data attached to this profile',
                ],
                // Invalid profile block found should throw an exception.
                [
                    [
                        'id' => 89,
                        'label' => 'Label',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 2000,
                        'profile_block' => false,
                        'profile_attached' => false,
                        'meta_data_lang' => true,
                    ],
                    NULL,
                    'Profile blocks not found .',
                ],
                // Profile Lang not found should throw an exception.
                [
                    [
                        'id' => 89,
                        'label' => 'Label',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 2,
                        'profile_block' => true,
                        'profile_attached' => false,
                        'meta_data_lang' => false,
                    ],
                    NULL,
                    'Profile Lang not found .',
                ],
                // Duplicate profile item should throw an exception.
                [
                    [
                        'id' => 89,
                        'label' => 'exists',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 2,
                        'profile_block' => true,
                        'profile_attached' => false,
                        'meta_data_lang' => true,
                    ],
                    NULL,
                    'Profile item already exists as Person Record Information fields',
                ],
                // ISP is already exists should throw an exception.
                [
                    [
                        'id' => 89,
                        'label' => 'isp-exists',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 2,
                        'profile_block' => true,
                        'profile_attached' => false,
                        'meta_data_lang' => true,
                    ],
                    NULL,
                    'ISP already exists',
                ],
                // Update profile item
                [
                    [
                        'id' => 89,
                        'label' => 'label',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 2,
                        'profile_block' => true,
                        'profile_attached' => false,
                        'meta_data_lang' => true,
                    ],
                    $this->createProfileDto([
                        'id' => 89,
                        'label' => 'label',
                        'name' => 'name',
                        'lang_id' => 1,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'N',
                        'profile_block_id' => 2,
                    ]),
                    NULL
                ],
            ]
        ]);
    }

    public function testReorderProfile()
    {
        $this->specify("Reorder profile Items", function ($profileId, $sequence, $isProfileAvailable, $expectedResult, $errorMessage) {
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

            $mockLoggerHelperService = $this->getMock('LoggerHelperService', ['getLog']);
            $mockContainer->method('get')->willReturnMap([
                [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService],
            ]);

            $mockEbiMetadataRepository = $this->getMock('EbiMetadataRepository', ['find', 'flush', 'merge', 'getEbiProfileCount', 'findOneBy']);
            $mockRepositoryResolver->expects($this->any())
                ->method('getRepository')
                ->willReturnMap([
                    [EbiMetadataRepository::REPOSITORY_KEY, $mockEbiMetadataRepository]
                ]);

            $mockEbiMetadataRepository->method('getEbiProfileCount')->willReturn(4);

            if ($isProfileAvailable) {
                $ebiMetadata = new EbiMetadata();
                $ebiMetadata->setSequence($sequence);
                $mockEbiMetadataRepository->method('find')->willReturn($ebiMetadata);
            } else {
                $mockEbiMetadataRepository->method('find')->willThrowException(new ValidationException('', 'Profile Lang not found .'));
            }
            $reorderProfileDto = $this->createReOrderProfileDto($profileId, $sequence);
            try {
                $profileService = new ProfileService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $profileService->reorderProfile($reorderProfileDto);
                $this->assertEquals($result, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($e->getMessage(), $errorMessage);
            }

        }, [
            'examples' => [
                // Passing empty profile id should throw exception
                [
                    '',
                    1,
                    false,
                    NULL,
                    'Profile Lang not found .'
                ],
                // Reorder profile
                [
                    234,
                    3,
                    true,
                    $this->createEbiMetadata(3),
                    NULL
                ],
                // Passing invalid profile id should throw exception
                [
                    'invalid-id',
                    5,
                    false,
                    NULL,
                    'Profile Lang not found .'
                ],
            ]
        ]);
    }

    public function testDeleteProfile()
    {
        $this->specify("Delete profile Items", function ($profileId, $isProfileAvailable, $academicUpdate, $isDataAttached, $expectedResult, $errorMessage) {
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

            $mockLoggerHelperService = $this->getMock('LoggerHelperService', ['getLog']);
            $mockContainer->method('get')->willReturnMap([
                [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService],
            ]);

            $mockEbiMetadataRepository = $this->getMock('EbiMetadataRepository', ['find', 'remove', 'flush', 'clear', 'findOneBy']);
            $mockEbiMetadataLangRepository = $this->getMock('EbiMetadataLangRepository', ['findBy', 'remove']);
            $mockEbiMetaDataListValuesRepository = $this->getMock('EbiMetaDataListValuesRepository', ['findBy', 'remove']);
            $mockAcademicUpdateRequestMetadataRepository = $this->getMock('AcademicUpdateRequestMetadataRepository', ['isEbiExists']);
            $mockPersonEbiMetadataRepository = $this->getMock('PersonEbiMetadataRepository', ['isDataAttched']);

            $mockRepositoryResolver->expects($this->any())
                ->method('getRepository')
                ->willReturnMap([
                    [EbiMetadataRepository::REPOSITORY_KEY, $mockEbiMetadataRepository],
                    [EbiMetadataLangRepository::REPOSITORY_KEY, $mockEbiMetadataLangRepository],
                    [EbiMetadataListValuesRepository::REPOSITORY_KEY, $mockEbiMetaDataListValuesRepository],
                    [AcademicUpdateRequestMetadataRepository::REPOSITORY_KEY, $mockAcademicUpdateRequestMetadataRepository],
                    [PersonEbiMetaDataRepository::REPOSITORY_KEY, $mockPersonEbiMetadataRepository]
                ]);
            if ($isProfileAvailable) {
                $mockEbiMetadataRepository->method('find')->willReturn(new EbiMetadata());
            } else {
                $mockEbiMetadataRepository->method('find')->willThrowException(new ValidationException('', 'Profile not found .'));
            }
            if ($academicUpdate) {
                $mockAcademicUpdateRequestMetadataRepository->method('isEbiExists')->willThrowException(new ValidationException('', 'Academic Update attached this profile'));
            }
            $mockPersonEbiMetadataRepository->method('isDataAttched')->willReturn($isDataAttached);
            try {
                $profileService = new ProfileService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $profileService->deleteProfile($profileId);
                $this->assertEquals($result, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($e->getMessage(), $errorMessage);
            }

        }, [
            'examples' => [
                // Passing empty profile id should throw exception
                [
                    '',
                    false,
                    NULL,
                    false,
                    NULL,
                    'Profile not found .'
                ],
                // Passing invalid profile id should throw an exception.
                [
                    'invalid-id',
                    false,
                    NULL,
                    false,
                    NULL,
                    'Profile not found .'
                ],
                // Remove profile item 25
                [
                    25,
                    true,
                    '',
                    false,
                    new EbiMetadata(),
                    NULL
                ],
                // Profile can not be deleted since the academic update is attached should throw an exception.
                [
                    25,
                    true,
                    [89, 76],
                    false,
                    NULL,
                    'Academic Update attached this profile'
                ],
                // Profile can not be deleted since ebi meta data attached with the profile should throw an exception.
                [
                    25,
                    true,
                    '',
                    true,
                    NULL,
                    'Ebi Meta data attached to this profile'
                ],
            ]
        ]);
    }

    public function testGetProfiles()
    {
        $this->specify("Get profile Items", function ($status, $ebiProfileItems, $expectedResult) {
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

            $mockEbiMetadataLangRepository = $this->getMock('EbiMetadataLangRepository', ['getProfiles']);

            $mockRepositoryResolver->expects($this->any())
                ->method('getRepository')
                ->willReturnMap([
                    [EbiMetadataLangRepository::REPOSITORY_KEY, $mockEbiMetadataLangRepository],
                ]);
            $mockEbiMetadataLangRepository->method('getProfiles')->willReturn($ebiProfileItems);

            $profileService = new ProfileService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $profileService->getProfiles($status);
            $this->assertEquals($result, $expectedResult);
        }, [
            'examples' => [
                // List all profile items
                [
                    'all',
                    [
                        [
                            "id" => 7764,
                            "modified_at" => "2018-01-03",
                            "display_name" => "ProfileItem",
                            "item_data_type" => "D",
                            "decimal_points" => 1,
                            'min_range' => 1,
                            'max_range' => 5,
                            'profile_block_id' => 2,
                            'profile_block_name' => 'ProfileItemBlock',
                            'pom_id' => '',
                            'au_id' => '',
                            "definition_type" => "O",
                            "item_label" => "ProfileItem",
                            "item_subtext" => "ProfileItem",
                            "sequence_no" => 1,
                            "calendar_assignment" => "Y",
                            "status" => "active",
                            "item_used" => false
                        ],
                        [
                            "id" => 7764,
                            "modified_at" => "2018-01-03",
                            "display_name" => "Archived",
                            "item_data_type" => "D",
                            "decimal_points" => 1,
                            'min_range' => 1,
                            'max_range' => 5,
                            'profile_block_id' => 2,
                            'profile_block_name' => 'ArchivedBlock',
                            'pom_id' => '',
                            'au_id' => '',
                            "definition_type" => "O",
                            "item_label" => "Archived",
                            "item_subtext" => "Archived",
                            "sequence_no" => 1,
                            "calendar_assignment" => "Y",
                            "status" => "archive",
                            "item_used" => false
                        ]
                    ],
                    [
                        "total_archive_count" => 2,
                        "profile_items" => [
                            [
                                "id" => 7764,
                                "modified_at" => '2018-01-03',
                                "item_label" => 'ProfileItem',
                                "display_name" => 'ProfileItem',
                                "item_subtext" => 'ProfileItem',
                                "item_data_type" => 'D',
                                "definition_type" => 'O',
                                "decimal_points" => '1',
                                "min_range" => 1,
                                "max_range" => 5,
                                "sequence_no" => 1,
                                "profile_block_id" => 2,
                                "profile_block_name" => 'ProfileItemBlock',
                                "status" => 'active',
                                "item_used" => '',
                            ],
                            [
                                "id" => 7764,
                                "modified_at" => '2018-01-03',
                                "item_label" => 'Archived',
                                "display_name" => 'Archived',
                                "item_subtext" => 'Archived',
                                "item_data_type" => 'D',
                                "definition_type" => 'O',
                                "decimal_points" => '1',
                                "min_range" => 1,
                                "max_range" => 5,
                                "sequence_no" => 1,
                                "profile_block_id" => 2,
                                "profile_block_name" => 'ArchivedBlock',
                                "status" => 'archive',
                                "item_used" => '',
                            ]
                        ]

                    ]
                ],
                // List archive profile items
                [
                    'archive',
                    [
                        [
                            "id" => 7764,
                            "modified_at" => "2018-01-03",
                            "display_name" => "Archived",
                            "item_data_type" => "D",
                            "decimal_points" => 1,
                            'min_range' => 1,
                            'max_range' => 5,
                            'profile_block_id' => 2,
                            'profile_block_name' => 'ArchivedBlock',
                            'pom_id' => '',
                            'au_id' => '',
                            "definition_type" => "O",
                            "item_label" => "Archived",
                            "item_subtext" => "Archived",
                            "sequence_no" => 1,
                            "calendar_assignment" => "Y",
                            "status" => "archive",
                            "item_used" => false
                        ]
                    ],
                    [
                        "total_archive_count" => 1,
                        "profile_items" => [
                            [
                                "id" => 7764,
                                "modified_at" => '2018-01-03',
                                "item_label" => 'Archived',
                                "display_name" => 'Archived',
                                "item_subtext" => 'Archived',
                                "item_data_type" => 'D',
                                "definition_type" => 'O',
                                "decimal_points" => '1',
                                "min_range" => 1,
                                "max_range" => 5,
                                "sequence_no" => 1,
                                "profile_block_id" => 2,
                                "profile_block_name" => 'ArchivedBlock',
                                "status" => 'archive',
                                "item_used" => '',
                            ]
                        ]

                    ]
                ],
                // List active profile items
                [
                    'active',
                    [
                        [
                            "id" => 7764,
                            "modified_at" => "2018-01-03",
                            "display_name" => "ProfileItem",
                            "item_data_type" => "D",
                            "decimal_points" => 1,
                            'min_range' => 1,
                            'max_range' => 5,
                            'profile_block_id' => 2,
                            'profile_block_name' => 'ProfileItemBlock',
                            'pom_id' => '',
                            'au_id' => '',
                            "definition_type" => "O",
                            "item_label" => "ProfileItem",
                            "item_subtext" => "ProfileItem",
                            "sequence_no" => 1,
                            "calendar_assignment" => "Y",
                            "status" => "active",
                            "item_used" => false
                        ],
                    ],
                    [
                        "total_archive_count" => 1,
                        "profile_items" => [
                            [
                                "id" => 7764,
                                "modified_at" => '2018-01-03',
                                "item_label" => 'ProfileItem',
                                "display_name" => 'ProfileItem',
                                "item_subtext" => 'ProfileItem',
                                "item_data_type" => 'D',
                                "definition_type" => 'O',
                                "decimal_points" => '1',
                                "min_range" => 1,
                                "max_range" => 5,
                                "sequence_no" => 1,
                                "profile_block_id" => 2,
                                "profile_block_name" => 'ProfileItemBlock',
                                "status" => 'active',
                                "item_used" => '',
                            ],
                        ]

                    ]
                ]
            ]
        ]);
    }

    public function testGetProfile()
    {
        $this->specify("Get profile details", function ($profileItemId, $isProfileAvailable, $profileData, $expectedResult, $errorMessage) {
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

            $mockEbiMetadataRepository = $this->getMock('EbiMetadataRepository', ['getProfileWithProfileBlock', 'find']);
            $mockEbiMetadataLangRepository = $this->getMock('EbiMetadataLangRepository', ['findOneBy']);
            $mockEbiMetaDataListValuesRepository = $this->getMock('EbiMetaDataListValuesRepository', ['findBy']);
            $mockRepositoryResolver->expects($this->any())
                ->method('getRepository')
                ->willReturnMap([
                    [EbiMetadataLangRepository::REPOSITORY_KEY, $mockEbiMetadataLangRepository],
                    [EbiMetadataRepository::REPOSITORY_KEY, $mockEbiMetadataRepository],
                    [EbiMetadataListValuesRepository::REPOSITORY_KEY, $mockEbiMetaDataListValuesRepository]
                ]);

            if ($isProfileAvailable) {
                $ebiMetaData = $this->createEbiMetadata($profileData['sequence'], $profileData);
                $mockEbiMetadataRepository->method('find')->willReturn($ebiMetaData);
            } else {
                $mockEbiMetadataRepository->method('find')->willThrowException(new ValidationException('', 'Profile not found .'));
            }

            if ($profileData) {
                $ebiMeatdataLang = new EbiMetadataLang();
                $ebiMeatdataLang->setMetaName($profileData['meta_name']);
                $ebiMeatdataLang->setMetaDescription($profileData['meta_description']);
                $mockEbiMetadataLangRepository->method('findOneBy')->willReturn($ebiMeatdataLang);
            }


            try {
                $profileService = new ProfileService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $profileService->getProfile($profileItemId);
                $this->assertEquals($result, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($e->getMessage(), $errorMessage);
            }

        }, [
            'examples' => [
                // Passing empty profile id should throw an exception.
                ['', false, [], [], 'Profile not found .'],
                // Passing invalid profile id should throw an exception.
                ['invalid-id', false, [], [], 'Profile not found .'],
                // Get profile details for 234
                [
                    234, true,
                    [
                        "definition_type" => 'E',
                        "metadata_type" => 'S',
                        "scope" => 'Y',
                        "status" => "active",
                        "meta_name" => "SATMath",
                        "meta_description" => "SAT Math",
                        'key' => 'RetainYear3',
                        'sequence' => 5
                    ],
                    $this->createProfileDto([
                        'id' => '',
                        'label' => 'RetainYear3',
                        'name' => 'SATMath',
                        'lang_id' => null,
                        'definition_type' => 'E',
                        'calendar_assignment' => 'Y',
                        'profile_block_id' => 0,
                        "metadata_type" => 'S',
                        "scope" => 'Y',
                        'sequence_no' => 5,
                        "status" => "active",
                        'item_subtext' => 'SAT Math'
                    ], 'details'), NULL
                ]
            ]
        ]);
    }

    /**
     * Create ProfileDto
     *
     * @param array $profileInput
     * @param null|string $type
     * @return ProfileDto
     */
    private function createProfileDto($profileInput, $type = null)
    {
        $profileDto = new ProfileDto();
        $profileDto->setItemLabel($profileInput['label']);
        $profileDto->setDisplayName($profileInput['name']);
        $profileDto->setLangId($profileInput['lang_id']);
        $profileDto->setOrganizationId(213);
        $profileDto->setDefinitionType($profileInput['definition_type']);
        $profileDto->setCalenderAssignment($profileInput['calendar_assignment']);
        $profileDto->setProfileBlockId($profileInput['profile_block_id']);
        if (isset($profileInput['sequence_no'])) {
            $profileDto->setSequenceNo($profileInput['sequence_no']);
        }
        if (isset($profileInput['id'])) {
            $profileDto->setId($profileInput['id']);
        }
        if ($type) {
            $profileDto->setItemDataType($profileInput['metadata_type']);
            $profileDto->setItemSubtext($profileInput['item_subtext']);
            $profileDto->setStatus($profileInput['status']);
            $profileDto->setOrganizationId(NULL);
        }
        return $profileDto;
    }

    /**
     * Create error objects.
     *
     * @param array $errorArray
     * @return array
     */
    private function arrayOfErrorObjects($errorArray)
    {
        $returnArray = [];
        foreach ($errorArray as $errorKey => $error) {
            $mockErrorObject = $this->getMock('ErrorObject', ['getMessage']);
            $mockErrorObject->method('getMessage')->willReturn($error);
            $returnArray[] = $mockErrorObject;
        }
        return $returnArray;
    }

    /**
     * Create ReOrderProfileDto
     *
     * @param int $profileId
     * @param int $sequence
     * @return ReOrderProfileDto
     */
    private function createReOrderProfileDto($profileId, $sequence)
    {
        $reorderProfileDto = new ReOrderProfileDto();
        $reorderProfileDto->setId($profileId);
        $reorderProfileDto->setSequenceNo($sequence);
        return $reorderProfileDto;
    }

    /**
     * create EbiMetadata
     *
     * @param int $sequence
     * @param null|string $metaData
     * @return EbiMetadata
     */
    private function createEbiMetadata($sequence, $metaData = null)
    {
        $ebiMetadata = new EbiMetadata();
        $ebiMetadata->setSequence($sequence);
        if ($metaData) {
            $ebiMetadata->setDefinitionType($metaData['definition_type']);
            $ebiMetadata->setMetadataType($metaData['metadata_type']);
            $ebiMetadata->setScope($metaData['scope']);
            $ebiMetadata->setStatus($metaData['status']);
            $ebiMetadata->setKey($metaData['key']);
        }
        return $ebiMetadata;
    }
}