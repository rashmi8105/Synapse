<?php
require_once 'SynapseTestHelper.php';

class ProfileCest extends SynapseTestHelper
{

    private $token;

    private $orgId = 1;

    private $invalidOrg = - 1;

    private $profileId = 1;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }
    /* Create Profile Item - Completed & Tested */
    public function testCreateProfile(ApiTester $I)
    {
        $I->wantTo('Create a Profile by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "O",
            'item_data_type' => "N",
            'number_type' => [
                "min_digits" => 3,
                "max_digits" => 8,
                "decimal_points" => 2
            ],
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        $I->seeResponseContainsJson(array(
            'definition_type' => "O"
        ));
        $I->seeResponseContainsJson(array(
            'item_data_type' => "N"
        ));
        $I->seeResponseContainsJson(array(
            'item_subtext' => "some description"
        ));
        $I->seeResponseContainsJson(array(
            'number_type' => array(
                'min_digits' => 3,
                "max_digits" => 8,
                "decimal_points" => 2
            )
        ));
        $I->seeResponseContains('sequence_no');
        $I->seeResponseContains('item_label');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testCreateProfileInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Create a Profile with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "O",
            'item_data_type' => "N",
            'number_type' => [
                "min_digits" => 3,
                "max_digits" => 8,
                "decimal_points" => 2
            ],
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testCreateProfileInvalid(ApiTester $I)
    {
        $I->wantTo('Create a Profile with invalid organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->invalidOrg,
            'definition_type' => "O",
            'item_data_type' => "N",
            'number_type' => [
                "min_digits" => 3,
                "max_digits" => 8,
                "decimal_points" => 2
            ],
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        $I->seeResponseContains('Unauthorized access to organization: '.$this->invalidOrg);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
    
    /* Create Profile Item - Completed & Tested */
    public function testCreateProfileListType(ApiTester $I)
    {
        $I->wantTo('Create a Profile List type by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "O",
            'item_data_type' => "S",
            'category_type' => array(
                [
                    "answer" => "Label",
                    "value" => uniqid("Val_", true),
                    "sequence_no" => 1
                ]
            ),
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        $I->seeResponseContainsJson(array(
            'definition_type' => "O"
        ));
        $I->seeResponseContainsJson(array(
            'item_data_type' => "S"
        ));
        $I->seeResponseContainsJson(array(
            'item_subtext' => "some description"
        ));
        $I->seeResponseContainsJson(array(
            'category_type' => array(
                'answer' => "Label",
                "sequence_no" => 1
            )
        ));
        $I->seeResponseContains('sequence_no');
        $I->seeResponseContains('item_label');
        $I->seeResponseContains('id');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
    
    /* Create Profile Item - Completed & Tested */
    public function testCreateProfileListTypeUniqueError(ApiTester $I)
    {
        $I->wantTo('Create a Profile List type unique error by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "O",
            'item_data_type' => "S",
            'category_type' => array(
                [
                    "answer" => "Label",
                    "value" => "Value",
                    "sequence_no" => 1
                ],
                [
                    "answer" => "Label",
                    "value" => "Value",
                    "sequence_no" => 1
                ]
            ),
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        $I->seeResponseContains('List Value already exists');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateProfileDuplicateName(ApiTester $I)
    {
        $I->wantTo('Create a Profile with duplicate name  by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "O",
            'item_data_type' => "N",
            'number_type' => [
                "min_digits" => 3,
                "max_digits" => 8,
                "decimal_points" => 2
            ],
            'item_label' => "Duplicate",
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        
        $I->wantTo('Create a Profile by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "O",
            'item_data_type' => "N",
            'number_type' => [
                "min_digits" => 3,
                "max_digits" => 8,
                "decimal_points" => 2
            ],
            'item_label' => "Duplicate",
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        $I->seeResponseContains('Another item exists with this name. Please choose another name');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateProfileWithInvalildDefType(ApiTester $I)
    {
        $I->wantTo('Create a Profile with invalid definition type by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "PPP",
            'item_data_type' => "N",
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        
        $I->seeResponseContains('The definition type is invalid');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateProfileWithInvalildLang(ApiTester $I)
    {
        $I->wantTo('Create a Profile with invalid lang by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "E",
            'item_data_type' => "N",
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => - 1
        ]);
        
        $I->seeResponseContains('Language Not Found');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    /* Getting Particular Profile Item - Completed & Tested */
    public function testGetProfile(ApiTester $I)
    {
        $I->wantTo('Get Profile Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "O",
            'item_data_type' => "N",
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        $profile = json_decode($I->grabResponse());
        $id = $profile->data->id;
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('profile_item/'.$id.'?type=O' );
        $I->seeResponseContains('definition_type');
        $I->seeResponseContains('item_data_type');
        $I->seeResponseContains('item_subtext');
        $I->seeResponseContains('item_label');
        $I->seeResponseContains('number_type');
        $I->seeResponseContains('min_digits');
        $I->seeResponseContains('max_digits');
        $I->seeResponseContainsJson(array(
            'id' => $id
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    
    /* Updating Particular Profile Item - Completed & Tested */
    public function testUpdateProfile(ApiTester $I)
    {
        $I->wantTo('Update Profile Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "O",
            'item_data_type' => "N",
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        $profile = json_decode($I->grabResponse());
        $id = $profile->data->id;
        
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPUT('profile_item', [
            'organization_id' => $this->orgId,
            "id" => $id,
            'definition_type' => "O",
            'item_data_type' => "N",
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        
        $I->seeResponseContainsJson(array(
            'id' => $id
        ));
        $I->seeResponseContainsJson(array(
            'definition_type' => 'O'
        ));
        $I->seeResponseContainsJson(array(
            'item_data_type' => 'N'
        ));
        $I->seeResponseContainsJson(array(
            'item_subtext' => 'some description'
        ));
        $I->seeResponseContainsJson(array(
            'id' => $id
        ));
        $I->seeResponseContains('item_label');
        
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testUpdateProfileInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Update Profile Details with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('profile_item', [
            'organization_id' => $this->orgId,
            "id" => $this->profileId,
            'definition_type' => "O",
            'item_data_type' => "N",
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testUpdateProfileInvalidLang(ApiTester $I)
    {
        $I->wantTo('Update Profile Details with invalid language by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPUT('profile_item', [
            'organization_id' => $this->orgId,
            "id" => 4,
            'definition_type' => "E",
            'item_data_type' => "N",
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => - 1
        ]);
        $I->seeResponseContains('Language Not Found');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testUpdateProfileInvalidId(ApiTester $I)
    {
        $I->wantTo('Update Profile Details with invalid Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPUT('profile_item', [
            'organization_id' => $this->orgId,
            "id" => - 1,
            'definition_type' => "O",
            'item_data_type' => "N",
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        $I->seeResponseContains('Profile not found .');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    /* Deleting Profile Item - Completed & Tested */
    public function testDeleteOrgProfile(ApiTester $I)
    {
        $I->wantTo('Delete a Profile by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "O",
            'item_data_type' => "N",
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        $profile = json_decode($I->grabResponse());
        $id = $profile->data->id;
        $I->sendPOST('profile', [
            'organization_id' => $this->orgId,
            'definition_type' => "O",
            'item_data_type' => "N",
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
            ]);
        // Delete Profile
        $I->sendDELETE('profile_item/' . $id."?type=O");
        
        $I->seeResponseContainsJson(array(
            'id' => $id
        ));
        $I->seeResponseContains('deleted_at');
        $I->seeResponseContains('definition_type');
        $I->seeResponseContains('metadata_type');
        
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
    
    /* Deleting Profile Item - Completed & Tested */
 public function testDeleteEBIProfile(ApiTester $I)
    {
        $I->wantTo('Delete a Profile by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "E",
            'item_data_type' => "T",
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        $profile = json_decode($I->grabResponse());
        
        $id = $profile->data->id;
       
        // Delete Profile
        $I->sendDELETE('profile_item/' . $id."?type=E");
        
        $I->seeResponseContainsJson(array(
            'id' => $id
        ));
        $I->seeResponseContains('deleted_at');
        $I->seeResponseContains('definition_type');
        $I->seeResponseContains('metadata_type');
        
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
    public function testDeleteProfileInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Delete a Profile with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendDELETE('profile_item/' . $this->profileId);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testDeleteProfileEbi(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->wantTo('Delete a EBI Profile by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "E",
            'item_data_type' => "T",
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        $profile = json_decode($I->grabResponse());
        
        $id = $profile->data->id;
        // Create Profile
        $I->sendDELETE('profile_item/' . $id);
        
        $I->seeResponseContainsJson(array(
            'id' => $id
        ));
        $I->seeResponseContains('deleted_at');
        $I->seeResponseContains('definition_type');
        $I->seeResponseContains('metadata_type');
        $I->seeResponseContains('key');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
    
    /* Getting Organization Profile Item - Completed & Tested */
    public function testGetOrgProfile(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "O",
            'item_data_type' => "T",
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        $profile = json_decode($I->grabResponse());
        $id = $profile->data->id;
        
        $I->wantTo('Get Organization Profile Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('profile_item/org/' . $this->orgId);
        
        $I->seeResponseContains('id');
        $I->seeResponseContains('item_label');
        $I->seeResponseContains('item_subtext');
        $I->seeResponseContains('item_data_type');
        $I->seeResponseContains('sequence_no');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetOrgProfileInvalidAuthentication(ApiTester $I)
    {
        $I->haveHttpHeader('Accept', 'application/json');
        $I->wantTo('Get Organization Profile Details with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('profile_item/org/' . $this->orgId);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
    
    /* Getting EBI Profile Item - Completed & Tested */
    public function testGetEbiProfile(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('profile_item', [
            'organization_id' => $this->orgId,
            'definition_type' => "E",
            'item_data_type' => "T",
            'item_label' => uniqid("Key_", true),
            "item_subtext" => "some description",
            "lang_id" => 1
        ]);
        $profile = json_decode($I->grabResponse());
        $id = $profile->data->id;
        $I->wantTo('Get the EBI Profile Details  by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('profile_item/ebi?status=active');
        
        $I->seeResponseContains('id');
        $I->seeResponseContains('item_label');
        $I->seeResponseContains('item_subtext');
        $I->seeResponseContains('item_data_type');
        $I->seeResponseContains('sequence_no');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetEbiProfileInvalidAuthentication(ApiTester $I)
    {
        $I->haveHttpHeader('Accept', 'application/json');
        $I->wantTo('Get the EBI Profile Details with Invalid Authentication  by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('profile_item/ebi');
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testCreateProfileReorderBigNum(ApiTester $I)
    {
        $profileIds = array();
        for ($i = 0; $i < 2; $i ++) {
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->amBearerAuthenticated($this->token);
            $I->haveHttpHeader('Accept', 'application/json');
            $I->sendPOST('profile_item', [
                'organization_id' => $this->orgId,
                'definition_type' => "O",
                'item_data_type' => "N",
                'number_type' => [
                    "min_digits" => 3,
                    "max_digits" => 8,
                    "decimal_points" => 2
                ],
                'item_label' => uniqid("Key_", true),
                "item_subtext" => "some description",
                "lang_id" => 1
            ]);
            $profile = json_decode($I->grabResponse());
            
            $profileIds[] = $profile->data->id;
        }
        $I->wantTo('Profile Reorder API to Outbound Number');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('profile_item/reorder?type=O', [
            'id' => $profileIds[1],
            'sequence_no' => 999999
        ]
        );
        
        $I->seeResponseContainsJson(array(
            'id' => $profileIds[1]
        ));
        $I->seeResponseContains('key');
        $I->seeResponseContains('definition_type');
        $I->seeResponseContains('metadata_type');
        $I->seeResponseContains('sequence');
        $I->seeResponseContains('no_of_decimals');
        $I->seeResponseContains('min_range');
        $I->seeResponseContains('max_range');
        
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testCreateProfileReorderLowToHigh(ApiTester $I)
    {
        $profileIds = array();
        $seq = array();
        for ($i = 0; $i < 10; $i ++) {
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->amBearerAuthenticated($this->token);
            $I->haveHttpHeader('Accept', 'application/json');
            $I->sendPOST('profile_item', [
                'organization_id' => $this->orgId,
                'definition_type' => "O",
                'item_data_type' => "N",
                'number_type' => [
                    "min_digits" => 3,
                    "max_digits" => 8,
                    "decimal_points" => 2
                ],
                'item_label' => uniqid("Key_", true),
                "item_subtext" => "some description",
                "lang_id" => 1
            ]);
            $profile = json_decode($I->grabResponse());
            $profileIds[] = $profile->data->id;
            $seq[] = $profile->data->sequence_no;
        }
        $I->wantTo('Profile Reorder API to Outbound Number');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('profile_item/reorder?type=O', [
            'id' => $profileIds[5],
            'sequence_no' => $seq[8]
        ]
        );
        
        $I->seeResponseContainsJson(array(
            'id' => $profileIds[5]
        ));
        $I->seeResponseContains('key');
        $I->seeResponseContains('definition_type');
        $I->seeResponseContains('metadata_type');
        $I->seeResponseContains('sequence');
        $I->seeResponseContains('no_of_decimals');
        $I->seeResponseContains('min_range');
        $I->seeResponseContains('max_range');
        $I->seeResponseContains('organization');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testCreateOrgProfileReorderHighToLow(ApiTester $I)
    {
        $profileIds = array();
        $seq = array();
        for ($i = 0; $i < 10; $i ++) {
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->amBearerAuthenticated($this->token);
            $I->haveHttpHeader('Accept', 'application/json');
            $I->sendPOST('profile_item', [
                'organization_id' => $this->orgId,
                'definition_type' => "O",
                'item_data_type' => "N",
                'number_type' => [
                    "min_digits" => 3,
                    "max_digits" => 8,
                    "decimal_points" => 2
                ],
                'item_label' => uniqid("Key_", true),
                "item_subtext" => "some description",
                "lang_id" => 1
            ]);
            $profile = json_decode($I->grabResponse());
            $profileIds[] = $profile->data->id;
            $seq[] = $profile->data->sequence_no;
        }
        $I->wantTo('Profile Reorder API to High To Low');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('profile_item/reorder?type=O', [
            'id' => $profileIds[8],
            'sequence_no' => $seq[5]
        ]
        );
        
        $I->seeResponseContains('definition_type');
        $I->seeResponseContains('metadata_type');
        $I->seeResponseContains('sequence');
        $I->seeResponseContains('no_of_decimals');
        $I->seeResponseContains('min_range');
        $I->seeResponseContains('max_range');
        $I->seeResponseContains('organization');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testCreateEbiProfileReorderHighToLow(ApiTester $I)
    {
        $profileIds = array();
        $seq = array();
        for ($i = 0; $i < 10; $i ++) {
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->amBearerAuthenticated($this->token);
            $I->haveHttpHeader('Accept', 'application/json');
            $I->sendPOST('profile_item', [
                'organization_id' => $this->orgId,
                'definition_type' => "E",
                'item_data_type' => "N",
                'number_type' => [
                    "min_digits" => 3,
                    "max_digits" => 8,
                    "decimal_points" => 2
                ],
                'item_label' => uniqid("Key_", true),
                "item_subtext" => "some description",
                "lang_id" => 1
            ]);
            $profile = json_decode($I->grabResponse());
            
            $profileIds[] = $profile->data->id;
            $seq[] = $profile->data->sequence_no;
        }
        $I->wantTo('Profile Reorder API to High To Low');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('profile_item/reorder?type=E', [
            'id' => $profileIds[8],
            'sequence_no' => $seq[5]
        ]
        );
        
        $I->seeResponseContains('definition_type');
        $I->seeResponseContains('metadata_type');
        $I->seeResponseContains('sequence');
        $I->seeResponseContains('no_of_decimals');
        $I->seeResponseContains('min_range');
        $I->seeResponseContains('max_range');
       
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testCreateProfileReorderInvalidAuthentication(ApiTester $I)
    {
        $profileIds = array();
        $seq = array();
        for ($i = 0; $i < 10; $i ++) {
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->haveHttpHeader('Accept', 'application/json');
            $I->sendPOST('profile_item', [
                'organization_id' => $this->orgId,
                'definition_type' => "O",
                'item_data_type' => "N",
                'number_type' => [
                    "min_digits" => 3,
                    "max_digits" => 8,
                    "decimal_points" => 2
                ],
                'item_label' => uniqid("Key_", true),
                "item_subtext" => "some description",
                "lang_id" => 1
            ]);
            $profile = json_decode($I->grabResponse());
            $profileIds[] = $this->profileId;
            $seq[] = 21;
        }
        $I->wantTo('Profile Reorder API to High To Low with Invalid Authentication');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('profile_item/reorder?type=O', [
            'id' => $profileIds[8],
            'sequence_no' => $seq[5]
        ]
        );
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
}