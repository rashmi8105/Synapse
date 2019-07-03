<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';

class delegateCest extends SynapseRestfulTestBase
{
    private $orgID = 542;


    /* TEST PEOPLE */

    //Delegators
    private $invalidDelegator = [
        'email' => 'argus.filch@mailinator.com',
        'password' => 'password1!',
        'id' => 99436,
        'orgId' => 542,
        'langId' => 1
    ];

    private $delegator = [
        'email' => 'albus.dumbledore@mailinator.com',
        'password' => 'password1!',
        'id' => 99704,
        'orgId' => 542,
        'langId' => 1
    ];

    private $secondDelegator = [
        'email' => 'marcus.flint@mailinator.com',
        'password' => 'password1!',
        'id' => 99439,
        'orgId' => 542,
        'langId' => 1
    ];


    //Delegates
    private $delegate = [
        'email' => 'marcus.flint@mailinator.com',
        'password' => 'password1!',
        'id' => 99439,
        'orgId' => 542,
        'langId' => 1
    ];

    private $secondDelegate = [
        'email' => 'minerva.mcgonagall@mailinator.com',
        'password' => 'password1!',
        'id' => 99440,
        'orgId' => 542,
        'langId' => 1
    ];


    /* TEST PARAMETERS */

    private $createInvalidUniversityDelegateParameters = [
        'delegated_users' => [
            [
                'calendar_sharing_id' => 1,
                'delegated_to_person_id' => 99705, //Bad Guy
                'is_deleted' => false,
                'is_selected' => true,
            ]
        ],
        'organization_id' => 542,
        'person_id' => 99704
    ];

    private $createInvalidNoPermissionsDelegateParameters = [
        'delegated_users' => [
            [
                'calendar_sharing_id' => 1,
                'delegated_to_person_id' => 99443, //Pomona Sprout
                'is_deleted' => false,
                'is_selected' => true,
            ]
        ],
        'organization_id' => 542,
        'person_id' => 99436
    ];

    private $createDelegateParameters = [
        'delegated_users' => [
            [
                'calendar_sharing_id' => 1,
                'delegated_to_person_id' => 99443, //Pomona Sprout
                'is_deleted' => false,
                'is_selected' => true,
            ]
        ],
        'organization_id' => 542,
        'person_id' => 99704
    ];

    private $singleManagedUser = [
            0 => [
                'managed_person_email' => "albus.dumbledore@mailinator.com",
                'managed_person_first_name' => "Albus",
                'managed_person_id' => "99704",
                'managed_person_last_name' => "Dumbledore"
            ],
    ];

    private $appointmentToCreateForDelegator = [
        'attendees' => [
            '0' => [
                'is_selected' => true,
                'student_first_name' => "Ron ",
                'student_id' => 99432,
                'student_last_name' => "Weasley"
            ],
        ],
        'description' => "",
        'detail' => "Class attendance concern ",
        'detail_id' => 19,
        'is_free_standing' => true,
        'location' => "place",
        'organization_id' => 542,
        'person_id' => 99704,
        'person_id_proxy' => 99439, //Marcus
        'slot_start' => "2015-07-17 21:00:00",
        'slot_end' => "2015-07-17 22:00:00",
        'type' => "F",
        'share_options' => [['private_share' => false, 'public_share' => true, 'teams_share' => false, 'team_ids' => []]]
    ];

    private $officeHourToCreateForDelegator = [
        'location' => "office",
        'office_hours_id' => 1,
        'organization_id' => 542,
        'person_id' => 99704,
        'person_id_proxy' => 99439, //Marcus
        'slot_end' => "2015-07-14 14:00:00",
        'slot_start' => "2015-07-14 13:00:00",
        'slot_type' => "I"
    ];

    private $multiManagedUsers = [
            0 => [
                'managed_person_email' => "albus.dumbledore@mailinator.com",
                'managed_person_first_name' => "Albus",
                'managed_person_id' => "99704",
                'managed_person_last_name' => "Dumbledore"
            ],
            1 => [

                'managed_person_email' => "marcus.flint@mailinator.com",
                'managed_person_first_name' => "Marcus",
                'managed_person_id' => "99439",
                'managed_person_last_name' => "Flint"
            ],
    ];

    private $delegateRecievedPermissions = [
        //FIX: NEEDS UPDATED WITH THE NEW DATA
        'user_feature_permissions' => [
            0 =>[
                'booking' => true,
                'booking_share' => [
                    'private_share' => [
                        'create' => true,
                        'view' => true
                    ],
                    'public_share' => [
                        'create' => true,
                        'view' => true
                    ],
                    'teams_share' => [
                        'create' => true,
                        'view' => true
                    ],
                ],
                'log_contacts' => true,
                'log_contacts_share' => [
                    'private_share' => [
                        'create' => true,
                        'view' => true
                    ],
                    'public_share' => [
                        'create' => true,
                        'view' => true
                    ],
                    'teams_share' => [
                        'create' => true,
                        'view' => true
                    ]
                ],
                'notes' => true,
                'notes_share' => [
                    'private_share' => [
                        'create' => true,
                        'view' => true
                    ],
                    'public_share' => [
                        'create' => true,
                        'view' => true
                    ],
                    'teams_share' => [
                        'create' => true,
                        'view' => true
                    ]
                ],
                'referrals' => true,
                'referrals_share' => [
                    'private_share' => [
                        'create' => true,
                        'view' => true
                    ],
                    'public_share' => [
                        'create' => true,
                        'view' => true
                    ],
                    'teams_share' => [
                        'create' => true,
                        'view' => true
                    ]
                ]
            ]
        ]
    ];

    private $deleteDelegateParameters = [
        'delegated_users' => [
            [
                'calendar_sharing_id' => 1,
                'delegated_to_person_id' => 99439, //Marcus
                'is_deleted' => true,
            ]
        ],
        'organization_id' => 542,
        'person_id' => 99704
    ];

    private $deleteDelegateReturnedParameters = [
        "managed_users" => []
    ];

    private $deselectDelegateParameters = [
        'delegated_users' => [
            [ 
                'calendar_sharing_id' => 1,
                'delegated_to_person_id' => 99440, //Minerva
                'is_deleted' => false,
                'is_selected' => false,
            ]
        ],
        'organization_id' => 542,
        'person_id' => 99704
    ];

    private $deselectDelegateReturnedParameters = [
        "managed_users" => []
    ];


    /* ADDING DELEGATORS */

    //Try to add a delegate from a different university
    //appointments/{orgID}/proxy
    public function testAddInvalidDelegateDifferentCollege(ApiAuthTester $I)
    {
        $I->wantTo('Give delegate permissions to someone outside the university');
        $this->_postAPITestRunner($I, $this->delegator, 'appointments/'.$this->orgID.'/proxy', $this->createInvalidUniversityDelegateParameters, 403, []);
    }

    //Try to add a delegate as a delegator without own calendar permissions
    //appointments/{orgID}/proxy
    public function testAddInvalidDelegateNoBookingPermissions(ApiAuthTester $I)
    {
        $I->wantTo('Try to assign delegate permissions without having a booking permisison set');
        $this->_postAPITestRunner($I, $this->invalidDelegator, 'appointments/'.$this->orgID.'/proxy', $this->createInvalidNoPermissionsDelegateParameters, 403, []);
    }

    //Add Delegate
    //appointments/{orgID}/proxy
    public function testAddDelegate(ApiAuthTester $I)
    {
        $I->wantTo('Give delegate permissions to staff');
        $this->_postAPITestRunner($I, $this->delegator, 'appointments/'.$this->orgID.'/proxy', $this->createDelegateParameters, 201, []);
    }


    /* SINGLE DELEGATOR */

    //View the delegator I am a delegate for
    //appointments/{orgID}/managedUsers?person_id_proxy={proxyPersonID}
    public function testViewManagedUserAsDelegate(ApiAuthTester $I)
    {
        $I->wantTo('View whom I am a delegate for');
        $this->_getAPITestRunner($I, $this->delegate, 'appointments/'.$this->orgID.'/managedUsers?person_id_proxy='.$this->delegate['id'], [], 200, $this->singleManagedUser);
    }

    //View agenda as Delegate
    //appointments/{orgID}/proxy/{proxyPersonID}?frequency=current&managed_person_id={managedPersonID}
    public function testViewAgendaAsDelegate(ApiAuthTester $I)
    {
        $I->wantTo('Verify I can view the agenda for whom I am a delegate for');
        $this->_getAPITestRunner($I, $this->delegate, 'appointments/'.$this->orgID.'/proxy/'.$this->delegate['id'].'?frequency=current&managed_person_id='.$this->delegator['id'], [], 200, []);
    }

    //Create Appointment as Delegate
    //appointments/{orgID}/{delegateID}
    public function testCreateAppointmentAsDelegate(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Create an appointment as a delegate for the delegator');
        $this->_postAPITestRunner($I, $this->delegate, 'appointments/'.$this->orgID.'/'.$this->delegate['id'], $this->appointmentToCreateForDelegator, 201, []);
    }

    //Create Office Hours as Delegate
    //booking
    public function testCreateOfficeHourAsDelegate (ApiAuthTester $I)
    {
        $I->wantTo('Create a one time office hour as a delegate for the delegator');
        $this->_postAPITestRunner($I, $this->delegate, 'booking', $this->officeHourToCreateForDelegator, 201, []);
    }


    /* MULTIPLE DELEGATORS */

    //View the delegators I am delegate for
    //appointments/{orgID}/managedUsers?person_id_proxy={proxyPersonID}
    public function testViewTheMultiManagedUsersAsDelegate(ApiAuthTester $I)
    {
        $I->wantTo('View the delegators I am a delegate for');
        $this->_getAPITestRunner($I, $this->secondDelegate, 'appointments/'.$this->orgID.'/managedUsers?person_id_proxy='.$this->secondDelegate['id'], [], 200, $this->multiManagedUsers);
    }

    //View Person 1 Agenda
    //appointments/{orgID}/proxy/{proxyPersonID}?frequency=current&managed_person_id={managedPersonID}
    public function testViewFirstDelegatorAgenda(ApiAuthTester $I)
    {
        $I->wantTo('View the agenda for one of the delegators I am delegating for');
        $this->_getAPITestRunner($I, $this->secondDelegate, 'appointments/'.$this->orgID.'/proxy/'.$this->delegate['id'].'?frequency=current&managed_person_id='.$this->delegator['id'], [], 200, []);
    }

    //View Person 2 Agenda
    //appointments/{orgID}/proxy/{proxyPersonID}?frequency=current&managed_person_id={managedPersonID}
    public function testViewSecondDelegatorAgenda(ApiAuthTester $I)
    {
        $I->wantTo('View the agenda for the other delegator I am delegating for');
        $this->_getAPITestRunner($I, $this->secondDelegate, 'appointments/'.$this->orgID.'/proxy/'.$this->delegate['id'].'?frequency=current&managed_person_id='.$this->secondDelegator['id'], [], 200, []);
    }

    //View Both People's Agenda
    //appointments/{orgID}/proxy/{proxyPersonID}?frequency=current&managed_person_id={managedPersonID, managedPersonID}
    public function testViewBothDelegatorsAgendas(ApiAuthTester $I)
    {
        $I->wantTo('View the agenda for both of the delegators I am delegating for');
        $this->_getAPITestRunner($I, $this->secondDelegate, 'appointments/'.$this->orgID.'/proxy/'.$this->delegate['id'].'?frequency=current&managed_person_id='.$this->delegator['id'].','.$this->secondDelegator['id'], [], 200, []);
    }

/* Private, Public, Team permissions not valid on creating appointments
   Test is invalid until appointment permissions are updated
    //Verify Permission Sets
    //orgpermissionset/usersFeature?userId={userID, userID}
    public function testVerifyDelegatorsPermissionSets(ApiAuthTester $I)
    {
        $I->wantTo('Verify the permission sets for the multiple delegators I am a delegate for');
        $this->_getAPITestRunner($I, $this->secondDelegate, 'orgpermissionset/usersFeature?userId='.$this->delegator['id'].','.$this->secondDelegator['id'], [], 200, $this->delegateRecievedPermissions);
        //FIX: NOT SURE ON WHAT PERMISSION SETS SHOULD BE (How should they act when veiwing multiple delegators at the same time?)
    }
*/

    /* DELEGATE REMOVAL */

    //Delete Delegate
    //appointments/{orgID}/proxy
    public function testDeleteDelegate(ApiAuthTester $I)
    {
        $I->wantTo('Revoke delegate permissions from a staff through deletion');
        //Delegator Delete Api Call
        $this->_postAPITestRunner($I, $this->delegator, 'appointments/'.$this->orgID.'/proxy', $this->deleteDelegateParameters, 201, []);
        //Verify as Delegate
        $this->_getAPITestRunner($I, $this->delegate, 'appointments/'.$this->orgID.'/managedUsers?person_id_proxy='.$this->delegate['id'], [], 200, $this->deleteDelegateReturnedParameters);
    }

    //Deselect Delegate & Verify as Delegate that Viewing is Revoked
    //appointments/{orgID}/proxy
    public function testDeselectDelegate(ApiAuthTester $I)
    {
        $I->wantTo('Revoke delegate permissions from a staff through deselection');
        //Delegator Deselect Api Call
        $this->_postAPITestRunner($I, $this->delegator, 'appointments/'.$this->orgID.'/proxy', $this->deselectDelegateParameters, 201, []);
        //Verify as Delegate
        $this->_getAPITestRunner($I, $this->secondDelegate, 'appointments/'.$this->orgID.'/managedUsers?person_id_proxy='.$this->secondDelegate['id'], [], 200, $this->deselectDelegateReturnedParameters);
    }


    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
