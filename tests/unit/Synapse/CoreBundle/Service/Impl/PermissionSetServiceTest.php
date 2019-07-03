<?php

namespace Synapse\CoreBundle\Service\Impl;

use Synapse\CoreBundle\Entity\FeatureMaster;
use Synapse\CoreBundle\Entity\PermissionSet;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\DatablockMasterLangRepository;
use Synapse\CoreBundle\Repository\EbiPermissionsetDatablockRepository;
use Synapse\CoreBundle\Repository\EbiPermissionsetFeaturesRepository;
use Synapse\CoreBundle\Repository\FeatureMasterLangRepository;
use Synapse\CoreBundle\Repository\FeatureMasterRepository;
use Synapse\CoreBundle\Repository\LanguageMasterRepository;
use Synapse\CoreBundle\Repository\PermissionSetLangRepository;
use Synapse\CoreBundle\Repository\PermissionSetRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\AccessLevelDto;
use Synapse\RestBundle\Entity\CoursesAccessDto;
use Synapse\RestBundle\Entity\FeatureBlockDto;
use Synapse\RestBundle\Entity\PermissionSetDto;
use Synapse\RestBundle\Entity\PermissionSetStatusDto;
use Synapse\RestBundle\Entity\PermissionValueDto;

class PermissionSetServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testCreate()
    {
        $this->specify("Test to create permissionset", function ($permissionSetArray, $isLanguageMasterAvailable, $isFeatureAvailable, $expectedErrorMessage, $expectedResults) {
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
            $mockLanguageMasterService = $this->getMock('LanguageMasterService', ['getLanguageById']);
            $mockValidator = $this->getMock('Validator', ['validate']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [LanguageMasterService::SERVICE_KEY, $mockLanguageMasterService],
                    [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService],
                    [SynapseConstant::VALIDATOR, $mockValidator]
                ]);

            $mockEbiPermissionSetDatablockRepository = $this->getMock('EbiPermissionsetDatablockRepository', ['flush']);
            $mockDataBlockMasterLangRepository = $this->getMock('DatablockMasterLangRepository', ['getDatablocks']);
            $mockPermissionSetRepository = $this->getMock('PermissionSetRepository', ['createPermissionSet', 'flush']);
            $mockPermissionSetLangRepository = $this->getMock('PermissionSetLangRepository', ['createPermissionSet', 'createPermissionSetLang']);
            $mockEbiPermissionSetFeaturesRepository = $this->getMock('EbiPermissionsetFeaturesRepository', ['createEbiPermissionsetFeatures']);
            $mockFeatureMasterRepository = $this->getMock('FeatureMasterRepository', ['find']);
            $mockLanguageMasterRepository = $this->getMock('LanguageMasterRepository', ['getLangReferance']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [EbiPermissionsetDatablockRepository::REPOSITORY_KEY, $mockEbiPermissionSetDatablockRepository],
                    [EbiPermissionsetFeaturesRepository::REPOSITORY_KEY, $mockEbiPermissionSetFeaturesRepository],
                    [DatablockMasterLangRepository::REPOSITORY_KEY, $mockDataBlockMasterLangRepository],
                    [LanguageMasterRepository::REPOSITORY_KEY, $mockLanguageMasterRepository],
                    [FeatureMasterRepository::REPOSITORY_KEY, $mockFeatureMasterRepository],
                    [PermissionSetRepository::REPOSITORY_KEY, $mockPermissionSetRepository],
                    [PermissionSetLangRepository::REPOSITORY_KEY, $mockPermissionSetLangRepository]

                ]);
            $mockLanguageMaster = $this->getMock('LanguageMaster', ['getId']);
            if ($isLanguageMasterAvailable) {
                $mockLanguageMasterService->method('getLanguageById')->willReturn($mockLanguageMaster);
            } else {
                $mockLanguageMasterService->method('getLanguageById')->willReturn(NULL);
            }
            if ($isFeatureAvailable) {
                $mockFeatureMasterRepository->method('find')->willReturn(new FeatureMaster());
            } else {
                $mockFeatureMasterRepository->method('find')->willReturn(false);
            }
            $permissionSetDto = $this->createPermissionSetDto($permissionSetArray, 'create');
            try {
                $permissionSetService = new PermissionSetService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $permissionSetService->create($permissionSetDto);
                $this->assertEquals($results, $expectedResults);
            } catch (\Exception $e) {
                $this->assertEquals($expectedErrorMessage, $e->getMessage());
            }
        }, [
                'examples' => [
                    // Language id not found should throw an exception.
                    [
                        [
                            'lang_id' => 'invalid',
                            'template_name' => 'Template Name',
                            'access_level' => [
                                'individual_and_aggregate' => false,
                                'aggregate_only' => true,
                            ],
                            'courses_access' => [
                                'view_courses' => false,
                                'create_view_academic_update' => true,
                                'view_all_final_grades' => false,
                                'view_all_academic_update' => false
                            ]
                        ], false, true, 'Language Not Found.', NULL
                    ],
                    // Feature not found should throw an exception.
                    [
                        [
                            'lang_id' => 1,
                            'template_name' => 'Template Name',
                            'access_level' => [
                                'individual_and_aggregate' => false,
                                'aggregate_only' => true,
                            ],
                            'courses_access' => [
                                'view_courses' => false,
                                'create_view_academic_update' => true,
                                'view_all_final_grades' => false,
                                'view_all_academic_update' => false
                            ],
                            'features' => [
                                [
                                    'id' => "invalid-id",
                                    'name' => 'Referrals',
                                    "private_share" => [
                                        'create' => true,
                                        'view' => true
                                    ],
                                    "public_share" => [
                                        'create' => false,
                                        'view' => false
                                    ],
                                    "team_share" => [
                                        'create' => false,
                                        'view' => false
                                    ],
                                ]
                            ]
                        ], true, false, 'Feature Not Found', NULL,
                    ],
                    // Create new permission set
                    [
                        [
                            'lang_id' => 1,
                            'template_name' => 'Template Name',
                            'access_level' => [
                                'individual_and_aggregate' => false,
                                'aggregate_only' => true,
                            ],
                            'courses_access' => [
                                'view_courses' => false,
                                'create_view_academic_update' => true,
                                'view_all_final_grades' => false,
                                'view_all_academic_update' => false
                            ],
                            'features' => [
                                [
                                    'id' => 12,
                                    'name' => 'Referrals',
                                    "private_share" => [
                                        'create' => true,
                                        'view' => true
                                    ],
                                    "public_share" => [
                                        'create' => false,
                                        'view' => false
                                    ],
                                    "team_share" => [
                                        'create' => false,
                                        'view' => false
                                    ],
                                ]
                            ]
                        ],
                        true, true, NULL,
                        $this->createPermissionSetDto([
                            'lang_id' => 1,
                            'template_name' => 'Template Name',
                            'access_level' => [
                                'individual_and_aggregate' => false,
                                'aggregate_only' => true,
                            ],
                            'courses_access' => [
                                'view_courses' => false,
                                'create_view_academic_update' => true,
                                'view_all_final_grades' => false,
                                'view_all_academic_update' => false
                            ],
                            'features' => [
                                [
                                    'id' => 12,
                                    'name' => 'Referrals',
                                    "private_share" => [
                                        'create' => true,
                                        'view' => true
                                    ],
                                    "public_share" => [
                                        'create' => false,
                                        'view' => false
                                    ],
                                    "team_share" => [
                                        'create' => false,
                                        'view' => false
                                    ],
                                ]
                            ]
                        ], 'create'),
                    ],
                ]
            ]
        );

    }

    public function testEdit()
    {

        $this->specify("Test to edit permissionset", function ($permissionSetArray, $isPermissionSetAvailable, $isFeatureAvailable, $expectedErrorMessage, $expectedResults) {
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
            $mockLanguageMasterService = $this->getMock('LanguageMasterService', ['getLanguageById']);
            $mockValidator = $this->getMock('Validator', ['validate']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [LanguageMasterService::SERVICE_KEY, $mockLanguageMasterService],
                    [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService],
                    [SynapseConstant::VALIDATOR, $mockValidator]
                ]);

            $mockEbiPermissionSetDatablockRepository = $this->getMock('EbiPermissionsetDatablockRepository', ['flush']);
            $mockDataBlockMasterLangRepository = $this->getMock('DatablockMasterLangRepository', ['getDatablocks']);
            $mockPermissionSetRepository = $this->getMock('PermissionSetRepository', ['createPermissionSet', 'flush', 'find']);
            $mockPermissionSetLangRepository = $this->getMock('PermissionSetLangRepository', ['createPermissionSet', 'createPermissionSetLang', 'findOneBy']);
            $mockEbiPermissionSetFeaturesRepository = $this->getMock('EbiPermissionsetFeaturesRepository', ['createEbiPermissionsetFeatures', 'findOneBy']);
            $mockFeatureMasterRepository = $this->getMock('FeatureMasterRepository', ['find']);
            $mockLanguageMasterRepository = $this->getMock('LanguageMasterRepository', ['getLangReferance']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [EbiPermissionsetDatablockRepository::REPOSITORY_KEY, $mockEbiPermissionSetDatablockRepository],
                    [EbiPermissionsetFeaturesRepository::REPOSITORY_KEY, $mockEbiPermissionSetFeaturesRepository],
                    [DatablockMasterLangRepository::REPOSITORY_KEY, $mockDataBlockMasterLangRepository],
                    [LanguageMasterRepository::REPOSITORY_KEY, $mockLanguageMasterRepository],
                    [FeatureMasterRepository::REPOSITORY_KEY, $mockFeatureMasterRepository],
                    [PermissionSetRepository::REPOSITORY_KEY, $mockPermissionSetRepository],
                    [PermissionSetLangRepository::REPOSITORY_KEY, $mockPermissionSetLangRepository]

                ]);
            $mockLanguageMaster = $this->getMock('LanguageMaster', ['getId']);

            $mockLanguageMasterService->method('getLanguageById')->willReturn($mockLanguageMaster);
            if ($isPermissionSetAvailable) {
                $mockPermissionSetRepository->method('find')->willReturn(new PermissionSet());
                $mockPermissionSetLang = $this->getMock('PermissionSetLang', ['getId', 'setPermissionsetName']);
                $mockPermissionSetLangRepository->method('findOneBy')->willReturn($mockPermissionSetLang);
            } else {
                $mockPermissionSetRepository->method('find')->willReturn(NULL);
            }
            if ($isFeatureAvailable) {
                $mockFeatureMasterRepository->method('find')->willReturn(new FeatureMaster());
            } else {
                $mockFeatureMasterRepository->method('find')->willReturn(false);
            }
            try {
                $permissionSetDto = $this->createPermissionSetDto($permissionSetArray, 'edit');
                $permissionSetService = new PermissionSetService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $permissionSetService->edit($permissionSetDto);
                $this->assertEquals($expectedResults, $results);
            } catch (\Exception $e) {
                $this->assertEquals($expectedErrorMessage, $e->getMessage());
            }
        }, [
                'examples' => [
                    // Permission set not found should throw an exception.
                    [
                        [
                            'lang_id' => 1,
                            'permission_template_id' => 2,
                            'template_name' => 'Template Name',
                            'access_level' => [
                                'individual_and_aggregate' => false,
                                'aggregate_only' => true,
                            ],
                            'courses_access' => [
                                'view_courses' => false,
                                'create_view_academic_update' => true,
                                'view_all_final_grades' => false,
                                'view_all_academic_update' => false
                            ]
                        ], false, true, 'Permissionset Not Found', NULL
                    ],
                    // Feature not found should throw an exception.
                    [
                        [
                            'lang_id' => 1,
                            'permission_template_id' => 2,
                            'template_name' => 'Template Name',
                            'access_level' => [
                                'individual_and_aggregate' => false,
                                'aggregate_only' => true,
                            ],
                            'courses_access' => [
                                'view_courses' => false,
                                'create_view_academic_update' => true,
                                'view_all_final_grades' => false,
                                'view_all_academic_update' => false
                            ],
                            'features' => [
                                [
                                    'id' => "invalid-id",
                                    'name' => 'Referrals',
                                    "private_share" => [
                                        'create' => true,
                                        'view' => true
                                    ],
                                    "public_share" => [
                                        'create' => false,
                                        'view' => false
                                    ],
                                    "team_share" => [
                                        'create' => false,
                                        'view' => false
                                    ],
                                ]
                            ]

                        ], true, false, 'Feature Not Found', NULL
                    ],
                    // edit exiting permission set.
                    [
                        [
                            'lang_id' => 1,
                            'permission_template_id' => 2,
                            'template_name' => 'Template Name',
                            'access_level' => [
                                'individual_and_aggregate' => false,
                                'aggregate_only' => true,
                            ],
                            'courses_access' => [
                                'view_courses' => false,
                                'create_view_academic_update' => true,
                                'view_all_final_grades' => false,
                                'view_all_academic_update' => false
                            ],
                            'features' => [[
                                'id' => 10,
                                'name' => 'Referrals',
                                "private_share" => [
                                    'create' => true,
                                    'view' => true
                                ],
                                "public_share" => [
                                    'create' => false,
                                    'view' => false
                                ],
                                "team_share" => [
                                    'create' => false,
                                    'view' => false
                                ],
                            ]]
                        ],
                        true, true, NULL,
                        $this->createPermissionSetDto([
                            'lang_id' => 1,
                            'template_name' => 'Template Name',
                            'permission_template_id' => 2,
                            'access_level' => [
                                'individual_and_aggregate' => false,
                                'aggregate_only' => true,
                            ],
                            'courses_access' => [
                                'view_courses' => false,
                                'create_view_academic_update' => true,
                                'view_all_final_grades' => false,
                                'view_all_academic_update' => false
                            ],
                            'features' => [[
                                'id' => 10,
                                'name' => 'Referrals',
                                "private_share" => [
                                    'create' => true,
                                    'view' => true
                                ],
                                "public_share" => [
                                    'create' => false,
                                    'view' => false
                                ],
                                "team_share" => [
                                    'create' => false,
                                    'view' => false
                                ],
                            ]]
                        ], 'edit'),
                    ],
                ]
            ]
        );
    }

    public function testUpdateStatus()
    {
        $this->specify("Test to change the status of permissionset", function ($permissionSet, $isPermissionSetAvailable, $expectedResults, $expectedErrorMessage) {
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
            $mockContainer->method('get')
                ->willReturnMap([
                    [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService],
                ]);

            $mockPermissionSetRepository = $this->getMock('PermissionSetRepository', ['find', 'flush']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [PermissionSetRepository::REPOSITORY_KEY, $mockPermissionSetRepository]
                ]);

            $mockPermissionSet = $this->getMock('PermissionSet', ['setIsActive', 'getModifiedAt', 'getId', 'setInactiveDate']);
            if ($isPermissionSetAvailable) {
                $mockPermissionSetRepository->method('find')->willReturn($mockPermissionSet);
                $mockPermissionSet->method('getId')->willReturn($permissionSet['template_id']);
            } else {
                $mockPermissionSetRepository->method('find')->willReturn(false);
            }
            try {
                $permissionSetStatusDto = $this->createPermissionSetStatusDto($permissionSet);
                $permissionSetService = new PermissionSetService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $permissionSetService->updateStatus($permissionSetStatusDto);
                $this->assertEquals($expectedResults, $results);
            } catch (\Exception $e) {
                $this->assertEquals($expectedErrorMessage, $e->getMessage());
            }
        }, [
                'examples' => [
                    // Change the permission set status as active.
                    [
                        [
                            'lang_id' => 1,
                            'template_name' => 'Template Name',
                            'template_id' => 12,
                            'status' => true
                        ],
                        true,
                        $this->createPermissionSetStatusDto([
                            'lang_id' => 1,
                            'template_name' => 'Template Name',
                            'template_id' => 12,
                            'status' => true
                        ]), NULL
                    ],
                    // Passing invalid template id should throw an exception.
                    [
                        [
                            'lang_id' => 1,
                            'template_name' => 'Template Name',
                            'template_id' => 'invalid-id',
                            'status' => true
                        ],
                        false,
                        [], 'Permissionset Not Found'
                    ],
                    // Passing empty template id should throw an exception.
                    [
                        [
                            'lang_id' => 1,
                            'template_name' => 'Template Name',
                            'template_id' => '',
                            'status' => true
                        ],
                        false,
                        [], 'Permissionset Not Found'
                    ],
                    // Changing permission set status as in-active.
                    [
                        [
                            'lang_id' => 1,
                            'template_name' => 'Template Name',
                            'template_id' => 15,
                            'status' => false
                        ],
                        true,
                        $this->createPermissionSetStatusDto([
                            'lang_id' => 1,
                            'template_name' => 'Template Name',
                            'template_id' => 15,
                            'status' => false
                        ]), NULL
                    ],

                ]
            ]
        );
    }

    public function testGetDataBlocksByType()
    {
        $this->specify("Test to get the data blocks by type", function ($languageId, $blockType, $isLanguageAvailable, $isDataBlockAvailable, $dataBlocks, $expectedResults, $expectedErrorMessage) {
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

            $mockLanguageMasterRepository = $this->getMock('LanguageMasterRepository', ['find']);
            $mockDataBlockMasterLangRepository = $this->getMock('DataBlockMasterLangRepository', ['getDatablocks']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [DatablockMasterLangRepository::REPOSITORY_KEY, $mockDataBlockMasterLangRepository],
                    [LanguageMasterRepository::REPOSITORY_KEY, $mockLanguageMasterRepository]
                ]);

            $mockLanguageMaster = $this->getMock('LanguageMaster', ['getId']);
            if ($isLanguageAvailable) {
                $mockLanguageMasterRepository->method('find')->willReturn($mockLanguageMaster);
                $mockLanguageMaster->method('getId')->willReturn($languageId);
            } else {
                $mockLanguageMasterRepository->method('find')->willReturn(false);
            }
            if ($isDataBlockAvailable) {
                $mockDataBlockMasterLangRepository->method('getDatablocks')->willReturn($dataBlocks);
            } else {
                $mockDataBlockMasterLangRepository->method('getDatablocks')->willThrowException(new SynapseValidationException($expectedErrorMessage));
            }
            try {
                $permissionSetService = new PermissionSetService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $permissionSetService->getDataBlocksByType($languageId, $blockType);
                $this->assertEquals($expectedResults, $results);
            } catch (\Exception $e) {
                $this->assertEquals($expectedErrorMessage, $e->getMessage());
            }
        }, [
                'examples' => [
                    // Passing empty language id should throw an exception.
                    ['', 'profile', false, true, NULL, [], 'Language Not Found.'],
                    // Passing in-valid language id should throw an exception
                    ['invalid-id', 'profile', false, true, NULL, [], 'Language Not Found.'],
                    // Passing invalid block type should throw an exception.
                    [1, 'invalid-type', true, false, NULL, [], 'An error has occurred with Mapworks. Please contact client services.'],
                    // Passing empty block type should throw an exception
                    [1, '', true, false, NULL, [], 'An error has occurred with Mapworks. Please contact client services.'],
                    // Get profile data blocks.
                    [
                        1, 'profile', true, true,
                        [

                            'data_blocks' => [
                                "datablock_name" => "BasicStudentInfo",
                                "datablock_id" => "12",
                                'profile_item_count' => 2,
                            ],
                            [
                                "datablock_name" => "Demographic",
                                "datablock_id" => "13",
                                'profile_item_count' => 3,
                            ]
                        ],
                        [
                            'lang_id' => 1,
                            'data_block_type' => 'profile',
                            'data_blocks' => [
                                [
                                    'block_name' => 'BasicStudentInfo',
                                    'block_id' => 12
                                ],
                                [
                                    'block_name' => 'Demographic',
                                    'block_id' => 13
                                ]
                            ]
                        ], NULL
                    ],
                    // Get survey data blocks.
                    [
                        1, 'survey', true, true,
                        [

                            'data_blocks' => [
                                "datablock_name" => "Intent to Leave",
                                "datablock_id" => "34",
                                'profile_item_count' => 5,
                            ],
                            [
                                "datablock_name" => "Academic: Content Skills",
                                "datablock_id" => "35",
                                'profile_item_count' => 0,
                            ]
                        ],
                        [
                            'lang_id' => 1,
                            'data_block_type' => 'survey',
                            'data_blocks' => [
                                [
                                    'block_name' => 'Intent to Leave',
                                    'block_id' => 34
                                ],
                                [
                                    'block_name' => 'Academic: Content Skills',
                                    'block_id' => 35
                                ]
                            ]
                        ], NULL
                    ]
                ]
            ]
        );
    }

    public function testGetPermissionSet()
    {
        $this->specify("Test to get permission set", function ($languageId, $permissionSetId, $languageAvailable, $isPermissionSet, $permissionSet, $expectedErrorMessage) {
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
            $mockContainer->method('get')
                ->willReturnMap([
                    [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService],
                ]);

            $mockDataBlockMasterLangRepository = $this->getMock('DataBlockMasterLangRepository', ['getDatablocks']);
            $mockFeatureMasterLangRepository = $this->getMock('FeatureMasterLangRepository', ['listFeaturesAll']);
            $mockLanguageMasterRepository = $this->getMock('LanguageMasterRepository', ['find']);
            $mockPermissionSetRepository = $this->getMock('PermissionSetRepository', ['find']);
            $mockPermissionSetLangRepository = $this->getMock('PermissionSetLangRepository', ['findOneBy']);
            $mockEbiPermissionSetDataBlockRepository = $this->getMock('EbiPermissionsetDatablockRepository', ['getEbiDataBlockID']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [DatablockMasterLangRepository::REPOSITORY_KEY, $mockDataBlockMasterLangRepository],
                    [EbiPermissionsetDatablockRepository::REPOSITORY_KEY, $mockEbiPermissionSetDataBlockRepository],
                    [FeatureMasterLangRepository::REPOSITORY_KEY, $mockFeatureMasterLangRepository],
                    [LanguageMasterRepository::REPOSITORY_KEY, $mockLanguageMasterRepository],
                    [PermissionSetRepository::REPOSITORY_KEY, $mockPermissionSetRepository],
                    [PermissionSetLangRepository::REPOSITORY_KEY, $mockPermissionSetLangRepository]
                ]);
            if ($languageAvailable) {
                $mockLanguageMaster = $this->getMock('LanguageMaster', ['getId']);
                $mockLanguageMasterRepository->method('find')->willReturn($mockLanguageMaster);
            } else {
                $mockLanguageMasterRepository->method('find')->willReturn(NULL);
            }
            if ($isPermissionSet) {
                $mockPermissionSet = $this->getMock('Permissionset', ['getId', 'getIsActive', 'getRiskIndicator', 'getIntentToLeave', 'getModifiedAt', 'getAccesslevelAgg', 'getAccesslevelIndAgg', 'getCreateViewAcademicUpdate', 'getViewAllAcademicUpdateCourses', 'getViewAllFinalGrades', 'getViewCourses', 'setRiskindicator', 'setIntentToleave']);
                $mockPermissionSetRepository->method('find')->willReturn($mockPermissionSet);
                $mockPermissionSet->method('getId')->willReturn($permissionSetId);
                $mockPermissionSet->method('getIsActive')->willReturn($permissionSet['status']);
                $mockPermissionSet->method('getModifiedAt')->willReturn(new \DateTime($permissionSet['modified_at']));

                $mockPermissionSet->method('getAccesslevelAgg')->willReturn($permissionSet['access_level']['aggregate_only']);
                $mockPermissionSet->method('getAccesslevelIndAgg')->willReturn($permissionSet['access_level']['individual_and_aggregate']);

                $mockPermissionSet->method('getCreateViewAcademicUpdate')->willReturn($permissionSet['courses_access']['create_view_academic_update']);
                $mockPermissionSet->method('getViewAllAcademicUpdateCourses')->willReturn($permissionSet['courses_access']['view_all_academic_update']);
                $mockPermissionSet->method('getViewAllFinalGrades')->willReturn($permissionSet['courses_access']['view_all_final_grades']);
                $mockPermissionSet->method('getViewCourses')->willReturn($permissionSet['courses_access']['view_courses']);

                $mockPermissionSet->method('setRiskindicator')->willReturn($permissionSet['risk_indicator']);
                $mockPermissionSet->method('setIntentToleave')->willReturn($permissionSet['intent_to_leave']);

                $mockPermissionSetLang = $this->getMock('PermissionSetLang', ['getLang', 'getPermissionsetName']);
                $mockPermissionSetLangRepository->method('findOneBy')->willReturn($mockPermissionSetLang);
                $mockLanguageMaster = $this->getMock('LanguageMaster', ['getId']);
                $mockPermissionSetLang->method('getLang')->willReturn($mockLanguageMaster);
                $mockLanguageMaster->method('getId')->willReturn($permissionSet['lang_id']);
                $mockPermissionSetLangRepository->method('getPermissionsetName')->willReturn($permissionSet['template_name']);
            } else {
                $mockPermissionSetRepository->method('find')->willReturn(false);
            }
            try {
                $permissionSetService = new PermissionSetService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $permissionSetService->getPermissionSet($languageId, $permissionSetId);
                $this->assertEquals($languageId, $results->getLangId());
                $this->assertEquals($permissionSetId, $results->getPermissionTemplateId());
            } catch (\Exception $e) {
                $this->assertEquals($expectedErrorMessage, $e->getMessage());
            }
        }, [
                'examples' => [
                    // passing empty language id should throw an exception
                    ['', 5, false, true, NULL, 'Language Not Found.'],
                    // Passing empty permission set id should throw an exception
                    [1, '', true, false, NULL, 'Permissionset Not Found'],
                    // Get permission set details
                    [1, 5, true, true,
                        [
                            'lang_id' => 1,
                            'status' => true,
                            'modified_at' => '2017-10-10',
                            'template_name' => 'Template Name',
                            'permission_template_id' => 2,
                            'risk_indicator' => true,
                            'intent_to_leave' => true,
                            'access_level' => [
                                'individual_and_aggregate' => false,
                                'aggregate_only' => true,
                            ],
                            'courses_access' => [
                                'view_courses' => false,
                                'create_view_academic_update' => true,
                                'view_all_academic_update' => true,
                                'view_all_final_grades' => false,
                            ],

                        ], NULL
                    ],
                    // Passing invalid permission set id should throw an exception
                    [1, 'invalid-id', true, false, NULL, 'Permissionset Not Found'],
                ]
            ]
        );
    }

    public function testListPermissionSetByStatus()
    {
        $this->specify("Test to get permission set", function ($languageId, $status, $languageAvailable, $permissionSetCount, $permissionSets, $expectedResults, $expectedErrorMessage) {
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

            $mockLanguageMasterRepository = $this->getMock('LanguageMasterRepository', ['find']);
            $mockPermissionSetRepository = $this->getMock('PermissionSetRepository', ['listPermissionsetCount', 'findBy', 'findAll']);
            $mockDataBlockMasterLangRepository = $this->getMock('DataBlockMasterLangRepository', ['getDatablocks']);
            $mockPermissionSetLangRepository = $this->getMock('PermissionSetLangRepository', ['findOneBy']);
            $mockEbiPermissionsetDatablockRepository = $this->getMock('EbiPermissionsetDatablockRepository', ['getEbiDataBlockID']);
            $mockFeatureMasterLangRepository = $this->getMock('FeatureMasterLangRepository', ['listFeaturesAll']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [DatablockMasterLangRepository::REPOSITORY_KEY, $mockDataBlockMasterLangRepository],
                    [FeatureMasterLangRepository::REPOSITORY_KEY, $mockFeatureMasterLangRepository],
                    [LanguageMasterRepository::REPOSITORY_KEY, $mockLanguageMasterRepository],
                    [PermissionSetRepository::REPOSITORY_KEY, $mockPermissionSetRepository],
                    [PermissionSetLangRepository::REPOSITORY_KEY, $mockPermissionSetLangRepository],
                    [EbiPermissionsetDatablockRepository::REPOSITORY_KEY, $mockEbiPermissionsetDatablockRepository]
                ]);
            if ($languageAvailable) {
                $mockLanguageMaster = $this->getMock('LanguageMaster', ['getId']);
                $mockLanguageMasterRepository->method('find')->willReturn($mockLanguageMaster);
            } else {
                $mockLanguageMasterRepository->method('find')->willReturn(NULL);
            }
            $mockPermissionSetRepository->method('listPermissionsetCount')->willReturn($permissionSetCount);
            $permissionSetList = [];
            if (isset($permissionSetCount) && ($permissionSetCount[0]['count_active'] || $permissionSetCount[0]['count_archive'])) {
                foreach ($permissionSets as $permissionSet) {
                    $permissionSetList[] = $this->createPermissionSet($permissionSet);
                }
            }
            $mockPermissionSetRepository->method('findBy')->willReturn($permissionSetList);
            $permissionSetLang = $this->getMock('PermissionSetLang', ['setPermissionsetName', 'getPermissionsetName']);
            $mockPermissionSetLangRepository->method('findOneBy')->willReturn($permissionSetLang);
            $permissionSetLang->method('getPermissionsetName')->willReturn($permissionSets[0]['template_name']);
            try {
                $permissionSetService = new PermissionSetService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $permissionSetService->listPermissionSetByStatus($languageId, $status);
                $this->assertEquals($results, $expectedResults);
            } catch (\Exception $e) {
                $this->assertEquals($expectedErrorMessage, $e->getMessage());
            }
        }, [
                'examples' => [
                    // Passing empty language id should throw an exception.
                    ['', 'active', false, NULL, NULL, NULL, 'Language Not Found.'],
                    // Passing empty status
                    [1, 'active', true, NULL, NULL,
                        [
                            'lang_id' => 1,
                            'permission_template_count_active' => 0,
                            'permission_template_count_archive' => 0,
                            'permission_template' => []
                        ], NULL
                    ],
                    // Passing invalid language id
                    ['invalid-id', 'active', false, NULL, NULL, NULL, 'Language Not Found.'],
                    // Get the active permission sets
                    [
                        1, 'active', true,
                        [
                            [
                                'count_active' => 1,
                                'count_archive' => 0
                            ]
                        ],
                        [
                            [
                                'lang_id' => 1,
                                'status' => 1,
                                'modified_at' => '2017-10-10',
                                'template_name' => 'Template Name',
                                'permission_template_id' => 2,
                                'access_level' => [
                                    'individual_and_aggregate' => false,
                                    'aggregate_only' => true,
                                ],
                                'courses_access' => [
                                    'view_courses' => false,
                                    'create_view_academic_update' => true,
                                    'view_all_final_grades' => false,
                                    'view_all_academic_update' => true,
                                ],
                                'risk_indicator' => true,
                                'intent_to_leave' => true,
                            ]
                        ]
                        , $this->createPermissionSetDtoObject(
                        [
                            [
                                'lang_id' => 1,
                                'status' => 'active',
                                'modified_at' => '2017-10-10',
                                'template_name' => 'Template Name',
                                'permission_template_id' => 2,
                                'access_level' => [
                                    'individual_and_aggregate' => false,
                                    'aggregate_only' => true,
                                ],
                                'courses_access' => [
                                    'view_courses' => false,
                                    'create_view_academic_update' => true,
                                    'view_all_final_grades' => false,
                                    'view_all_academic_update' => true,
                                ],
                                'risk_indicator' => true,
                                'intent_to_leave' => true,
                            ]
                        ], 1, 0), NULL
                    ],
                    // Get the archive permission sets
                    [
                        1, 'archive', true,
                        [
                            [
                                'count_active' => 0,
                                'count_archive' => 1
                            ]
                        ],
                        [
                            [
                                'lang_id' => 1,
                                'status' => 0,
                                'modified_at' => '2017-08-17',
                                'template_name' => 'Template Name - Archived',
                                'permission_template_id' => 2,
                                'access_level' => [
                                    'individual_and_aggregate' => true,
                                    'aggregate_only' => false,
                                ],
                                'courses_access' => [
                                    'view_courses' => true,
                                    'create_view_academic_update' => true,
                                    'view_all_final_grades' => false,
                                    'view_all_academic_update' => true,
                                ],
                                'risk_indicator' => false,
                                'intent_to_leave' => true,
                            ]
                        ]
                        , $this->createPermissionSetDtoObject(
                        [
                            [
                                'lang_id' => 1,
                                'status' => 'archive',
                                'modified_at' => '2017-08-17',
                                'template_name' => 'Template Name - Archived',
                                'permission_template_id' => 2,
                                'access_level' => [
                                    'individual_and_aggregate' => true,
                                    'aggregate_only' => false,
                                ],
                                'courses_access' => [
                                    'view_courses' => true,
                                    'create_view_academic_update' => true,
                                    'view_all_final_grades' => false,
                                    'view_all_academic_update' => true,
                                ],
                                'risk_indicator' => false,
                                'intent_to_leave' => true,
                            ]
                        ], 0, 1), NULL
                    ],
                ]
            ]
        );
    }

    /**
     * create permission set DTO
     *
     * @param array $permissionSet
     * @param string $type
     * @return PermissionSetDto
     */
    private function createPermissionSetDto($permissionSet, $type)
    {
        $permissionSetDto = new PermissionSetDto();
        $permissionSetDto->setPermissionTemplateName($permissionSet['template_name']);
        $accessLevelDto = new AccessLevelDto();
        $accessLevelDto->setAggregateOnly($permissionSet['access_level']['aggregate_only']);
        $accessLevelDto->setIndividualAndAggregate($permissionSet['access_level']['individual_and_aggregate']);
        $permissionSetDto->setAccessLevel($accessLevelDto);
        $coursesAccessDto = new CoursesAccessDto();
        $coursesAccessDto->setViewCourses($permissionSet['courses_access']['view_courses']);
        $coursesAccessDto->setCreateViewAcademicUpdate($permissionSet['courses_access']['create_view_academic_update']);
        $coursesAccessDto->setViewAllFinalGrades($permissionSet['courses_access']['view_all_final_grades']);
        $coursesAccessDto->setViewAllAcademicUpdateCourses($permissionSet['courses_access']['view_all_academic_update']);
        $permissionSetDto->setCoursesAccess($coursesAccessDto);
        if (isset($permissionSet['features'])) {
            $featuresArray = $permissionSet['features'];
            foreach ($featuresArray as $feature) {
                $featuresDto = new FeatureBlockDto();
                $featuresDto->setId($feature['id']);
                $featuresDto->setName($feature['name']);
                $privateShare = new PermissionValueDto();
                $privateShare->setCreate($feature['private_share']['create']);
                $privateShare->setView($feature['private_share']['view']);
                $featuresDto->setPrivateShare($privateShare);
                $publicShare = new PermissionValueDto();
                $publicShare->setCreate($feature['public_share']['create']);
                $publicShare->setView($feature['public_share']['view']);
                $featuresDto->setPublicShare($publicShare);
                $teamShare = new PermissionValueDto();
                $teamShare->setCreate($feature['team_share']['create']);
                $teamShare->setView($feature['team_share']['view']);
                $featuresDto->setTeamsShare($teamShare);
                $features[] = $featuresDto;

            }
            $permissionSetDto->setFeatures($features);
        }
        if ($type == 'get') {
            $permissionSetDto->setFeatures([]);
            $permissionSetDto->setSurveyBlocks([]);
            $permissionSetDto->setProfileBlocks([]);
            $permissionSetDto->setRiskIndicator($permissionSet['risk_indicator']);
            $permissionSetDto->setIntentToLeave($permissionSet['intent_to_leave']);
            $permissionSetDto->setPermissionTemplateStatus($permissionSet['status']);
        }
        return $permissionSetDto;
    }

    /**
     * create PermissionSetStatusDto
     *
     * @param array $permissionSet
     * @return PermissionSetStatusDto
     */
    private function createPermissionSetStatusDto($permissionSet)
    {
        $permissionSetStatusDto = new PermissionSetStatusDto();
        $permissionSetStatusDto->setLangId($permissionSet['lang_id']);
        $permissionSetStatusDto->setPermissionTemplateName($permissionSet['template_name']);
        $permissionSetStatusDto->setPermissionTemplateId($permissionSet['template_id']);
        $permissionSetStatusDto->setPermissionTemplateStatus($permissionSet['status']);
        return $permissionSetStatusDto;
    }

    /**
     * Create permission set
     *
     * @param array $permissionSetArray
     * @return PermissionSet
     */
    private function createPermissionSet($permissionSetArray)
    {
        $permissionSet = new PermissionSet();
        $permissionSet->setIsActive($permissionSetArray['status']);
        $permissionSet->setAccesslevelAgg($permissionSetArray['access_level']['aggregate_only']);
        $permissionSet->setAccesslevelIndAgg($permissionSetArray['access_level']['individual_and_aggregate']);
        $permissionSet->setCreateViewAcademicUpdate($permissionSetArray['courses_access']['create_view_academic_update']);
        $permissionSet->setViewAllAcademicUpdateCourses($permissionSetArray['courses_access']['view_all_academic_update']);
        $permissionSet->setViewAllFinalGrades($permissionSetArray['courses_access']['view_all_final_grades']);
        $permissionSet->setViewCourses($permissionSetArray['courses_access']['view_courses']);
        $permissionSet->setRiskIndicator($permissionSetArray['risk_indicator']);
        $permissionSet->setIntentToLeave($permissionSetArray['intent_to_leave']);
        return $permissionSet;
    }

    /**
     * @param array $permissionSetArray
     * @param int $activeCount
     * @param int $archiveCount
     * @return array
     */
    private function createPermissionSetDtoObject($permissionSetArray, $activeCount, $archiveCount)
    {
        $permissionSetTemplate['lang_id'] = 1;
        $permissionSetTemplate['permission_template_count_active'] = $activeCount;
        $permissionSetTemplate['permission_template_count_archive'] = $archiveCount;
        foreach ($permissionSetArray as $permissionSet) {
            $permissionSetDto[] = $this->createPermissionSetDto($permissionSet, 'get');
        }
        $permissionSetTemplate['permission_template'] = $permissionSetDto;
        return $permissionSetTemplate;
    }
}
