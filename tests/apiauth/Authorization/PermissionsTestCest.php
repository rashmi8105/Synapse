<?php

require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


/*
 * This Test is for Checking Data Integrity of Permission Sets
 * Does the faculty match the permissions set we expect?
 *
 * Albus => Valid Coordinator for Hogwarts University
 * bad guy => Invalid Coordinator from Competing University
 * Argus => Minimum Access Staff Member
 *
 */


class PermissionsTestCest extends SynapseRestfulTestBase
{

    //ORG ID
    const ORG_NUM = 542;

    private $users = [

        "Albus" => [
            'auth' => ["email" => "albus.dumbledore@mailinator.com",
                "password" => "password1!"],
            'data' => [
                //Hogwarts_FullAccess
                'access_level' => [
                    ['individual_and_aggregate' => true],
                    ['aggregate_only' => false],
                ],
                'courses_access' => [
                    ['create_view_academic_update' => true],
                    ['view_all_academic_update_courses' => true],
                    ['view_all_final_grades' => true],
                    ['view_courses' => true],
                ],
                'features' => [
                    [
                        'id' => 1,
                        'name' => "Referrals",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'receive_referrals' => true,
                        'teams_share' => [
                            'create' => true, 'view' => true
                        ],
                    ],

                    [
                        'id' => 2,
                        'name' => "Notes",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],

                    [
                        'id' => 3,
                        'name' => "Log Contacts",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ]
                    ],


                    [
                        'id' => 4,
                        'name' => "Booking",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                     ],
                ],

                ['intent_to_leave' => true],

                'isp' => [
                    '0' => [
                        'block_selection' => true,
                        'id' => 392,
                        'item_label' => "Hogwarts_Personal",
                    ],

                    '1' => [
                        'block_selection' => true,
                        'id' => 393,
                        'item_label' => "Hogwarts_Academic",
                    ],
                    '2' => [
                        'block_selection' => true,
                        'id' => 394,
                        'item_label' => "Hogwarts_Demographic",
                    ],
                ],

                'isq' => [],

                ['organization_id' => 542],

                ['permission_template_id' => 36301],

                ['permission_template_name' => "Hogwarts_CampusCoordinator"],

                'profile_blocks' => [
                    'block_id' => 1,
                    'block_name' => "Demographic",
                    'block_selection' => true,
                ],

                ['risk_indicator' => true],

                'survey_blocks' => [],

                'errors' => [],

                'sideLoaded' => [],
            ],
        ],

        "Argus" => [
            'auth' => ["email" => "argus.filch@mailinator.com",
                "password" => "password1!"],
            'data' => [
                //Hogwarts_AggregateOnlyAccess
                'access_level' => [
                    ['individual_and_aggregate' => false],
                    ['aggregate_only' => true],
                ],

                'courses_access' => [
                    ['create_view_academic_update' => false],
                    ['view_all_academic_update_courses' => false],
                    ['view_all_final_grades' => false],
                    ['view_courses' => false],
                ],

                'features' => [],

                ['intent_to_leave' => false],

                'isp' => [
                    '0' => [
                        'block_selection' => false,
                        'id' => 392,
                        'item_label' => "Hogwarts_Personal",
                    ],

                    '1' => [
                        'block_selection' => false,
                        'id' => 393,
                        'item_label' => "Hogwarts_Academic",
                    ],
                    '2' => [
                        'block_selection' => false,
                        'id' => 394,
                        'item_label' => "Hogwarts_Demographic",
                    ],
                ],

                'isq' => [],

                ['organization_id' => 542],

                ['permission_template_id' => 36302],

                ['permission_template_name' => "Hogwarts_AggregateOnlyAccess"],

                'profile_blocks' => [],

                ['risk_indicator' => false],

                'survey_blocks' => [],

                'errors' => [],

                'sideLoaded' => [],
            ],
        ],

        //INVALID COORDINATOR
        "Bad Guy" => [
            'auth' => ["email" => "bad.guy@mailinator.com",
                "password" => "password1!"],
            'data' => [
                //AllAccess
                'access_level' => [
                    ['individual_and_aggregate' => true],
                    ['aggregate_only' => false],
                ],
                'courses_access' => [
                    ['create_view_academic_update' => true],
                    ['view_all_academic_update_courses' => true],
                    ['view_all_final_grades' => true],
                    ['view_courses' => true],
                ],
                'features' => [
                    [
                        'id' => 1,
                        'name' => "Referrals",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'receive_referrals' => true,
                        'teams_share' => [
                            'create' => true, 'view' => true
                        ],
                    ],

                    [
                        'id' => 2,
                        'name' => "Notes",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],

                    [
                        'id' => 3,
                        'name' => "Log Contacts",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ]
                    ],


                    [
                        'id' => 4,
                        'name' => "Booking",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],
                ],

                ['intent_to_leave' => true],

                'isp' => [],

                'isq' => [],

                ['organization_id' => 543],

                ['permission_template_id' => 43144],

                ['permission_template_name' => "AllAccess"],

                'profile_blocks' => [
                    'block_id' => 1,
                    'block_name' => "Demographic",
                    'block_selection' => true,
                ],

                ['risk_indicator' => true],

                'survey_blocks' => [],

                'errors' => [],

                'sideLoaded' => [],
            ],
        ],

        "Minerva" => [
            'auth' => ["email" => "minerva.mcgonagall@mailinator.com",
                "password" => "password1!"],
            'data' => [
                //Hogwarts_FullAccess
                'access_level' => [
                    ['individual_and_aggregate' => true],
                    ['aggregate_only' => false],
                ],
                'courses_access' => [
                    ['create_view_academic_update' => true],
                    ['view_all_academic_update_courses' => true],
                    ['view_all_final_grades' => true],
                    ['view_courses' => true],
                ],
                'features' => [
                    [
                        'id' => 1,
                        'name' => "Referrals",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'receive_referrals' => true,
                        'teams_share' => [
                            'create' => true, 'view' => true
                        ],
                    ],

                    [
                        'id' => 2,
                        'name' => "Notes",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],

                    [
                        'id' => 3,
                        'name' => "Log Contacts",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ]
                    ],


                    [
                        'id' => 4,
                        'name' => "Booking",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],
                ],

                ['intent_to_leave' => true],

                'isp' => [
                    '0' => [
                        'block_selection' => true,
                        'id' => 392,
                        'item_label' => "Hogwarts_Personal",
                    ],

                    '1' => [
                        'block_selection' => true,
                        'id' => 393,
                        'item_label' => "Hogwarts_Academic",
                    ],
                    '2' => [
                        'block_selection' => true,
                        'id' => 394,
                        'item_label' => "Hogwarts_Demographic",
                    ],
                ],

                'isq' => [],

                ['organization_id' => 542],

                ['permission_template_id' => 36303],

                ['permission_template_name' => "Hogwarts_FullAccess"],

                'profile_blocks' => [
                    'block_id' => 1,
                    'block_name' => "Demographic",
                    'block_selection' => true,
                ],

                ['risk_indicator' => true],

                'survey_blocks' => [],

                'errors' => [],

                'sideLoaded' => [],
            ],
        ],

        "Severus" => [
            'auth' => ["email" => "severus.snape@mailinator.com",
                "password" => "password1!"],
            'data' => [
                //Hogwarts_FullAccess
                'access_level' => [
                    ['individual_and_aggregate' => true],
                    ['aggregate_only' => false],
                ],
                'courses_access' => [
                    ['create_view_academic_update' => true],
                    ['view_all_academic_update_courses' => true],
                    ['view_all_final_grades' => true],
                    ['view_courses' => true],
                ],
                'features' => [
                    [
                        'id' => 1,
                        'name' => "Referrals",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'receive_referrals' => true,
                        'teams_share' => [
                            'create' => true, 'view' => true
                        ],
                    ],

                    [
                        'id' => 2,
                        'name' => "Notes",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],

                    [
                        'id' => 3,
                        'name' => "Log Contacts",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ]
                    ],


                    [
                        'id' => 4,
                        'name' => "Booking",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],
                ],

                ['intent_to_leave' => true],

                'isp' => [
                    '0' => [
                        'block_selection' => true,
                        'id' => 392,
                        'item_label' => "Hogwarts_Personal",
                    ],

                    '1' => [
                        'block_selection' => true,
                        'id' => 393,
                        'item_label' => "Hogwarts_Academic",
                    ],
                    '2' => [
                        'block_selection' => true,
                        'id' => 394,
                        'item_label' => "Hogwarts_Demographic",
                    ],
                ],

                'isq' => [],

                ['organization_id' => 542],

                ['permission_template_id' => 36303],

                ['permission_template_name' => "Hogwarts_FullAccess"],

                'profile_blocks' => [
                    'block_id' => 1,
                    'block_name' => "Demographic",
                    'block_selection' => true,
                ],

                ['risk_indicator' => true],

                'survey_blocks' => [],

                'errors' => [],

                'sideLoaded' => [],
            ],
        ],

        "Percy" => [
            'auth' => ["email" => "percy.weasley@mailinator.com",
                "password" => "password1!"],
            'data' => [
                //Hogwarts_FullAccess
                'access_level' => [
                    ['individual_and_aggregate' => true],
                    ['aggregate_only' => false],
                ],
                'courses_access' => [
                    ['create_view_academic_update' => true],
                    ['view_all_academic_update_courses' => true],
                    ['view_all_final_grades' => true],
                    ['view_courses' => true],
                ],
                'features' => [
                    [
                        'id' => 1,
                        'name' => "Referrals",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'receive_referrals' => true,
                        'teams_share' => [
                            'create' => true, 'view' => true
                        ],
                    ],

                    [
                        'id' => 2,
                        'name' => "Notes",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],

                    [
                        'id' => 3,
                        'name' => "Log Contacts",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ]
                    ],


                    [
                        'id' => 4,
                        'name' => "Booking",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],
                ],

                ['intent_to_leave' => true],

                'isp' => [
                    '0' => [
                        'block_selection' => true,
                        'id' => 392,
                        'item_label' => "Hogwarts_Personal",
                    ],

                    '1' => [
                        'block_selection' => true,
                        'id' => 393,
                        'item_label' => "Hogwarts_Academic",
                    ],
                    '2' => [
                        'block_selection' => true,
                        'id' => 394,
                        'item_label' => "Hogwarts_Demographic",
                    ],
                ],

                'isq' => [],

                ['organization_id' => 542],

                ['permission_template_id' => 36303],

                ['permission_template_name' => "Hogwarts_FullAccess"],

                'profile_blocks' => [],

                ['risk_indicator' => true],

                'survey_blocks' => [],

                'errors' => [],

                'sideLoaded' => [],
            ],
        ],

        "Oliver" => [
            'auth' => ["email" => "oliver.wood@mailinator.com",
                "password" => "password1!"],
            'data' => [
                //Hogwarts Full Access

                'access_level' => [
                    ['individual_and_aggregate' => true],
                    ['aggregate_only' => false],
                ],
                'courses_access' => [
                    ['create_view_academic_update' => true],
                    ['view_all_academic_update_courses' => true],
                    ['view_all_final_grades' => true],
                    ['view_courses' => true],
                ],
                'features' => [
                    [
                        'id' => 1,
                        'name' => "Referrals",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'receive_referrals' => true,
                        'teams_share' => [
                            'create' => true, 'view' => true
                        ],
                    ],

                    [
                        'id' => 2,
                        'name' => "Notes",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],

                    [
                        'id' => 3,
                        'name' => "Log Contacts",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ]
                    ],


                    [
                        'id' => 4,
                        'name' => "Booking",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],
                ],

                ['intent_to_leave' => true],

                'isp' => [
                    '0' => [
                        'block_selection' => true,
                        'id' => 392,
                        'item_label' => "Hogwarts_Personal",
                    ],

                    '1' => [
                        'block_selection' => true,
                        'id' => 393,
                        'item_label' => "Hogwarts_Academic",
                    ],
                    '2' => [
                        'block_selection' => true,
                        'id' => 394,
                        'item_label' => "Hogwarts_Demographic",
                    ],
                ],

                'isq' => [],

                ['organization_id' => 542],

                ['permission_template_id' => 36303],

                ['permission_template_name' => "Hogwarts_FullAccess"],

                'profile_blocks' => [],

                ['risk_indicator' => true],

                'survey_blocks' => [],

                'errors' => [],

                'sideLoaded' => [],
            ],

                //Hogwarts_MinimumAccess
                'access_level' => [
                    ['individual_and_aggregate' => true],
                    ['aggregate_only' => false],
                ],
                'courses_access' => [
                    ['create_view_academic_update' => false],
                    ['view_all_academic_update_courses' => false],
                    ['view_all_final_grades' => false],
                    ['view_courses' => false],
                ],
                'features' => [
                    [
                        'id' => 1,
                        'name' => "Referrals",
                        'private_share' => [
                            'create' => false,
                        ],
                        'public_share' => [
                            'create' => false, 'view' => false,
                        ],
                        'receive_referrals' => false,
                        'teams_share' => [
                            'create' => false, 'view' => false
                        ],
                    ],

                    [
                        'id' => 2,
                        'name' => "Notes",
                        'private_share' => [
                            'create' => false,
                        ],
                        'public_share' => [
                            'create' => false, 'view' => false,
                        ],
                        'teams_share' => [
                            'create' => false, 'view' => false,
                        ],
                    ],

                    [
                        'id' => 3,
                        'name' => "Log Contacts",
                        'private_share' => [
                            'create' => false,
                        ],
                        'public_share' => [
                            'create' => false, 'view' => false,
                        ],
                        'teams_share' => [
                            'create' => false, 'view' => false,
                        ]
                    ],


                    [
                        'id' => 4,
                        'name' => "Booking",
                        'private_share' => [
                            'create' => false,
                        ],
                        'public_share' => [
                            'create' => false, 'view' => false,
                        ],
                        'teams_share' => [
                            'create' => false, 'view' => false,
                        ],
                    ],
                ],

                ['intent_to_leave' => false],

                'isp' => [
                    '0' => [
                        'block_selection' => false,
                        'id' => 392,
                        'item_label' => "Hogwarts_Personal",
                    ],

                    '1' => [
                        'block_selection' => false,
                        'id' => 393,
                        'item_label' => "Hogwarts_Academic",
                    ],
                    '2' => [
                        'block_selection' => false,
                        'id' => 394,
                        'item_label' => "Hogwarts_Demographic",
                    ],
                ],

                'isq' => [],

                ['organization_id' => 542],

                ['permission_template_id' => 43151],

                ['permission_template_name' => "Hogwarts_MinimumAccess"],

                'profile_blocks' => [],

                ['risk_indicator' => false],

                'survey_blocks' => [],

                'errors' => [],

                'sideLoaded' => [],
            ],


        //STAFF NOT IMPLEMENTED YET

        "Cuthbert" => [
            'auth' => ["email" => "cuthbert.binns@mailinator.com",
                "password" => "password1!"],
            'data' => [
                //Professor Course Access (1 course)
                //Hogwarts Professor Access

                'access_level' => [
                    ['individual_and_aggregate' => true],
                    ['aggregate_only' => false],
                ],
                'courses_access' => [
                    ['create_view_academic_update' => true],
                    ['view_all_academic_update_courses' => true],
                    ['view_all_final_grades' => true],
                    ['view_courses' => true],
                ],
                'features' => [
                    [
                        'id' => 1,
                        'name' => "Referrals",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'receive_referrals' => true,
                        'teams_share' => [
                            'create' => true, 'view' => true
                        ],
                    ],

                    [
                        'id' => 2,
                        'name' => "Notes",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],

                    [
                        'id' => 3,
                        'name' => "Log Contacts",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ]
                    ],


                    [
                        'id' => 4,
                        'name' => "Booking",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],
                ],

                ['intent_to_leave' => true],

                'isp' => [
                    '0' => [
                        'block_selection' => true,
                        'id' => 392,
                        'item_label' => "Hogwarts_Personal",
                    ],

                    '1' => [
                        'block_selection' => true,
                        'id' => 393,
                        'item_label' => "Hogwarts_Academic",
                    ],
                    '2' => [
                        'block_selection' => true,
                        'id' => 394,
                        'item_label' => "Hogwarts_Demographic",
                    ],
                ],

                'isq' => [],

                ['organization_id' => 542],

                ['permission_template_id' => 36299],

                ['permission_template_name' => "Hogwarts_Professor"],

                'profile_blocks' => [],

                ['risk_indicator' => true],

                'survey_blocks' => [],

                'errors' => [],

                'sideLoaded' => [],
            ],
                //Course Director Access (4 courses)
                'courses_access' => [
                    ['create_view_academic_update' => false],
                    ['view_all_academic_update_courses' => false],
                    ['view_all_final_grades' => false],
                    ['view_courses' => false],
                ],
                'isp' => [
                    '0' => [
                        'block_selection' => false,
                        'id' => 392,
                        'item_label' => "Hogwarts_Personal",
                    ],

                    '1' => [
                        'block_selection' => true,
                        'id' => 393,
                        'item_label' => "Hogwarts_Academic",
                    ],
                    '2' => [
                        'block_selection' => false,
                        'id' => 394,
                        'item_label' => "Hogwarts_Demographic",
                    ],
                ],
                'profile_blocks' => [
                    'block_id' => 1,
                    'block_name' => "Demographic",
                    'block_selection' => true,
                ]


        ],

        "Filius" => [
            'auth' => ["email" => "filius.flitwick@mailinator.com",
                "password" => "password1!"],
            'data' => []
        ],

        "Marcus" => [
            'auth' => ["email" => "marcus.flint@mailinator.com",
                "password" => "password1!"],
            'data' => []
        ],

        "Pomona" => [
            'auth' => ["email" => "pomona.sprout@mailinator.com",
                "password" => "password1!"],
            'data' => []
        ],

        "Poppy" => [
            'auth' => ["email" => "poppy.pomfrey@mailinator.com",
                "password" => "password1!"],
            'data' => []
        ],

        "Rolanda" => [
            'auth' => ["email" => "rolanda.hooch@mailinator.com",
                "password" => "password1!"],
            'data' => []
        ],

        "Rubeus" => [
            'auth' => ["email" => "rubeus.hagrid@mailinator.com",
                "password" => "password1!"],
            'data' => [//Hogwarts Professor Access

                'access_level' => [
                    ['individual_and_aggregate' => true],
                    ['aggregate_only' => false],
                ],
                'courses_access' => [
                    ['create_view_academic_update' => true],
                    ['view_all_academic_update_courses' => true],
                    ['view_all_final_grades' => true],
                    ['view_courses' => true],
                ],
                'features' => [
                    [
                        'id' => 1,
                        'name' => "Referrals",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'receive_referrals' => true,
                        'teams_share' => [
                            'create' => true, 'view' => true
                        ],
                    ],

                    [
                        'id' => 2,
                        'name' => "Notes",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],

                    [
                        'id' => 3,
                        'name' => "Log Contacts",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ]
                    ],


                    [
                        'id' => 4,
                        'name' => "Booking",
                        'private_share' => [
                            'create' => true,
                        ],
                        'public_share' => [
                            'create' => true, 'view' => true,
                        ],
                        'teams_share' => [
                            'create' => true, 'view' => true,
                        ],
                    ],
                ],

                ['intent_to_leave' => true],

                'isp' => [
                    '0' => [
                        'block_selection' => true,
                        'id' => 392,
                        'item_label' => "Hogwarts_Personal",
                    ],

                    '1' => [
                        'block_selection' => true,
                        'id' => 393,
                        'item_label' => "Hogwarts_Academic",
                    ],
                    '2' => [
                        'block_selection' => true,
                        'id' => 394,
                        'item_label' => "Hogwarts_Demographic",
                    ],
                ],

                'isq' => [],

                ['organization_id' => 542],

                ['permission_template_id' => 36299],

                ['permission_template_name' => "Hogwarts_Professor"],

                'profile_blocks' => [],

                ['risk_indicator' => true],

                'survey_blocks' => [],

                'errors' => [],

                'sideLoaded' => [],
            ],
        ]

    ];


    //Loop for testing all Faculty passed
    public function testAllPermissionSets(ApiAuthTester $I, $scenario)
    {
        // Reload the database before this test so changes made by other tests don't make it fail.
		//Commenting out Database drop as it is impacting other test cases auth-test-single-file.sql needs to be merged with testdata.sql file - Devadoss
        //shell_exec('mysql -u root -psynapse -e "drop database synapse"');
        //shell_exec('mysql -u root -psynapse -e "create database synapse"');
        $dataLoadOutput = shell_exec('mysql -u root -psynapse synapse < tests/_data/auth-test-single-file.sql');
        if ($dataLoadOutput != null)
            codecept_debug($dataLoadOutput);    // is printed when -d option is used in codecept command

        $I->wantTo('Check the permission sets for all relevant test users at Institution A and Institution B');
        foreach($this->users as $user){

            $this->_getAPITestRunner($I, $user['auth'],  'orgpermissionset/permissions', [], 200, $user['data']);

        }
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }


}
