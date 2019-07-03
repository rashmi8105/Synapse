<?php
require_once(dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';

class permissionTemplatesCest extends SynapseRestfulTestBase
{
    private $admin = [
        'email' => 'david.warner@gmail.com',
        'password' => 'ramesh@1974',
        'id' => 99706,
        'orgId' => -1,
        'langId' => 1,
        'type' => 'admin'
    ];

    private $addPermissionTemplate = [
            'access_level' => [
                'aggregate_only' => true,
                'individual_and_aggregate' => false
            ],
            'features' => [
                '0' => [
                    'id' => 1,
                    'name' => "Referrals",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'receive_referrals' => false,
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '1' => [
                    'id' => 2,
                    'name' => "Notes",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '2' => [
                    'id' => 3,
                    'name' => "Log Contacts",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '3' => [
                    'id' => 4,
                    'name' => "Booking",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '4' => [
                    'id' => 5,
                    'name' => "Student Referrals",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '5' => [
                    'id' => 6,
                    'name' => "Reason Routing",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
            ],
            'intent_to_leave' => false,
            'lang_id' => 1,
            'permission_template_name' => "blah",
            'profile_blocks' => [],
            'risk_indicator' => false,
            'survey_blocks' => []
    ];

    private $addPermissionTemplateResponse = [
        'data' => [
            'permission_template_id' => 10,
            'lang_id' => 1,
            'permission_template_name' => "blah",
            'access_level' => [
                'aggregate_only' => true,
                'individual_and_aggregate' => false
            ],
            'risk_indicator' => false,
            'intent_to_leave' => false,
            'profile_blocks' => [],
            'survey_blocks' => [],
            'features' => [
                '0' => [
                    'id' => 1,
                    'name' => "Referrals",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'receive_referrals' => false,
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '1' => [
                    'id' => 2,
                    'name' => "Notes",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '2' => [
                    'id' => 3,
                    'name' => "Log Contacts",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '3' => [
                    'id' => 4,
                    'name' => "Booking",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '4' => [
                    'id' => 5,
                    'name' => "Student Referrals",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '5' => [
                    'id' => 6,
                    'name' => "Reason Routing",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ]
            ]
        ]
    ];

    private $editPermissionTemplate = [
            'access_level' => [
                'aggregate_only' => true,
                'individual_and_aggregate' => false
            ],
            'features' => [
                '0' => [
                    'id' => 1,
                    'name' => "Referrals",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'receive_referrals' => false,
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '1' => [
                    'id' => 2,
                    'name' => "Notes",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '2' => [
                    'id' => 3,
                    'name' => "Log Contacts",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '3' => [
                    'id' => 4,
                    'name' => "Booking",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '4' => [
                    'id' => 5,
                    'name' => "Student Referrals",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
                '5' => [
                    'id' => 6,
                    'name' => "Reason Routing",
                    'private_share' => [
                        'create' => false,
                    ],
                    'public_share' => [
                        'create' => false,
                        'view' => false,
                    ],
                    'teams_share' => [
                        'create' => false,
                        'view' => false
                    ]
                ],
            ],
            'intent_to_leave' => false,
            'permission_template_id' => 7,
            'permission_template_name' => "Admin Permission Template Name Edit",
            'permission_template_status' => "active",
            'profile_blocks' => [],
            'risk_indicator' => false,
            'survey_blocks' => []
    ];

    private $editPermissionTemplateResponse = [
        'access_level' => [
            'aggregate_only' => true,
            'individual_and_aggregate' => false
        ],
        'features' => [
            '0' => [
                'id' => 1,
                'name' => "Referrals",
                'private_share' => [
                    'create' => false,
                ],
                'public_share' => [
                    'create' => false,
                    'view' => false,
                ],
                'receive_referrals' => false,
                'teams_share' => [
                    'create' => false,
                    'view' => false
                ]
            ],
            '1' => [
                'id' => 2,
                'name' => "Notes",
                'private_share' => [
                    'create' => false,
                ],
                'public_share' => [
                    'create' => false,
                    'view' => false,
                ],
                'teams_share' => [
                    'create' => false,
                    'view' => false
                ]
            ],
            '2' => [
                'id' => 3,
                'name' => "Log Contacts",
                'private_share' => [
                    'create' => false,
                ],
                'public_share' => [
                    'create' => false,
                    'view' => false,
                ],
                'teams_share' => [
                    'create' => false,
                    'view' => false
                ]
            ],
            '3' => [
                'id' => 4,
                'name' => "Booking",
                'private_share' => [
                    'create' => false,
                ],
                'public_share' => [
                    'create' => false,
                    'view' => false,
                ],
                'teams_share' => [
                    'create' => false,
                    'view' => false
                ]
            ],
            '4' => [
                'id' => 5,
                'name' => "Student Referrals",
                'private_share' => [
                    'create' => false,
                ],
                'public_share' => [
                    'create' => false,
                    'view' => false,
                ],
                'teams_share' => [
                    'create' => false,
                    'view' => false
                ]
            ],
            '5' => [
                'id' => 6,
                'name' => "Reason Routing",
                'private_share' => [
                    'create' => false,
                ],
                'public_share' => [
                    'create' => false,
                    'view' => false,
                ],
                'teams_share' => [
                    'create' => false,
                    'view' => false
                ]
            ],
        ],
        'intent_to_leave' => false,
        'permission_template_id' => 7,
        'permission_template_name' => "Admin Permission Template Name Edit",
        'permission_template_status' => "active",
        'profile_blocks' => [],
        'risk_indicator' => false,
        'survey_blocks' => []
    ];

    private $archivePermissionTemplate = [
            'lang_id' => 1,
            'permission_template_id' => 7,
            'permission_template_name' => "Admin Permission Template Name Edit",
            'permission_template_status' => "archive"
    ];

    private $archivePermissionTemplateResponse = [
            'lang_id' => 1,
            'permission_template_id' => 7,
            'permission_template_name' => "Admin Permission Template Name Edit",
            'permission_template_status' => "archive"
    ];

    private $viewArchivedPermissionTemplate = [
        [
            'data' => [
                'lang_id' => "1",
                'permission_template_count_active' => "9",
                'permission_template_count_archive' => "1",
                'permission_template' => [
                    '0' => [
                        'permission_template_id' => 8,
                        'permission_template_name' => "Admin Archive Test",
                        'permission_template_status' => "archive",
                        'access_level' => [
                            'aggregate_only' => true,
                            'individual_and_aggregate' => false,
                        ],
                        'risk_indicator' => false,
                        'intent_to_leave' => false,
                        'profile_blocks' => [],
                        'survey_blocks' => [],
                        'features' => [
                            '0' => [
                                'id' => 1,
                                'name' => "Referrals",
                                'private_share' => [
                                    'create' => false
                                ],
                                'public_share' => [
                                    'create' => false,
                                    'view' => false,
                                ],
                                'receive_referrals' => false,
                                'teams_share' => [
                                    'create' => false,
                                    'view' => false,
                                ],
                            ],
                            '1' => [
                                'id' => 2,
                                'name' => "Notes",
                                'private_share' => [
                                    'create' => false
                                ],
                                'public_share' => [
                                    'create' => false,
                                    'view' => false,
                                ],
                                'teams_share' => [
                                    'create' => false,
                                    'view' => false,
                                ],
                            ],
                            '2' => [
                                'id' => 3,
                                'name' => "Log Contacts",
                                'private_share' => [
                                    'create' => false
                                ],
                                'public_share' => [
                                    'create' => false,
                                    'view' => false,
                                ],
                                'teams_share' => [
                                    'create' => false,
                                    'view' => false,
                                ],
                            ],
                            '3' => [
                                'id' => 4,
                                'name' => "Booking",
                                'private_share' => [
                                    'create' => false,
                                ],
                                'public_share' => [
                                    'create' => false,
                                    'view' => false,
                                ],
                                'teams_share' => [
                                    'create' => false,
                                    'view' => false,
                                ],
                            ],
                            '4' => [
                                'id' => 5,
                                'name' => "Student Referrals",
                                'private_share' => [
                                    'create' => false,
                                ],
                                'public_share' => [
                                    'create' => false,
                                    'view' => false,
                                ],
                                'teams_share' => [
                                    'create' => false,
                                    'view' => false,
                                ],
                            ],
                            '5' => [
                                'id' => 6,
                                'name' => "Reason Routing",
                                'private_share' => [
                                    'create' => false,
                                ],
                                'public_share' => [
                                    'create' => false,
                                    'view' => false,
                                ],
                                'teams_share' => [
                                    'create' => false,
                                    'view' => false
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    private $makePermissionTemplateActive = [
            'lang_id' => 1,
            'permission_template_id' => 7,
            'permission_template_name' => "Admin Archive Test",
            'permission_template_status' => "active"
    ];

    private $makePermissionTemplateActiveResponse = [
            'lang_id' => 1,
            'permission_template_id' => 7,
            'permission_template_name' => "Admin Archive Test",
            'permission_template_status' => "active"
    ];


    //Add Another Permission Template
    public function testAddPermissionTemplate(ApiAuthTester $I, $scenario)
    {
       $scenario->skip("Failed");
        $I->wantTo('Add another permission template');
        $this->_postAPITestRunner($I, $this->admin, 'permissionset', $this->addPermissionTemplate, 201, [$this->addPermissionTemplateResponse]);
    }

    //Edit Existing Permission Template
    public function testEditPermissionTemplate(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('Edit an existing permission template');
        $this->_putAPITestRunner($I, $this->admin, 'permissionset', $this->editPermissionTemplate, 200, [$this->editPermissionTemplateResponse]);
    }

    //View Archived Permission Template
    public function testViewArchivedPermissionTemplates(ApiAuthTester $I)
    {
        $I->wantTo('View all archived permission templates');
        $this->_getAPITestRunner($I, $this->admin, 'permissionset/1/list?status=archive', [], 200, $this->viewArchivedPermissionTemplate);
    }

    //Archive Permission Template
    public function testArchivePermissionTemplate(ApiAuthTester $I)
    {
        $I->wantTo('Archive a permission template');
        $this->_putAPITestRunner($I, $this->admin, 'permissionset/updatestatus', $this->archivePermissionTemplate, 200, [$this->archivePermissionTemplateResponse]);
    }

    //Make Active Permission Template
    public function testMakePermissionTemplateActive(ApiAuthTester $I)
    {
        $I->wantTo('Make an archived permission template active');
        $this->_putAPITestRunner($I, $this->admin, 'permissionset/updatestatus', $this->makePermissionTemplateActive, 200, [$this->makePermissionTemplateActiveResponse]);
    }
}