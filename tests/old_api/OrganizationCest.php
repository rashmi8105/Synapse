<?php
use GuzzleHttp\json_decode;
require_once 'SynapseTestHelper.php';

class OrganizationCest extends SynapseTestHelper
{

    private $token;

    private $organization = 1;

    private $invalidOrg = -200;

    private $langId = 1;

    private $invalidLangId = - 1;

    private $groupId = 1;

    private $invalidGroupId = - 1;

    private $personId = 2;

    private $invalidPersonId = - 1;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }
	/* Need to be Fixed
    public function testCreateOrganization(ApiTester $I)
    {
        $I->wantTo('Create a Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('organization', [
            "name" => "ORG".mt_rand().date("his"),
            "nick_name" => "North University",
            "subdomain" => "subdomain".mt_rand().date("his"),
            "timezone" => "Pacific",
            "langid" => $this->langId
        ]);
        $org = json_decode($I->grabResponse());
        $I->seeResponseContains('name');
        $I->seeResponseContains('nick_name');
        $I->seeResponseContains('subdomain');
        $I->seeResponseContains('timezone');
        $I->seeResponseContains('id');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
	*/

    public function testCreateOrganizationInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Create a Organization with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('organization', [
            "name" => uniqid("Org_", true),
            "nick_name" => "North University",
            "subdomain" => uniqid("Subdomain_", true),
            "timezone" => "Pacific",
            "langid" => $this->langId
        ]);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
	/* Need to be Fixed
    public function testCreateOrganizationDuplicateOrgName(ApiTester $I)
    {
        $I->wantTo('Create a Organization with duplicate Organization Name by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('organization', [
            "name" => "NorthWest",
            "nick_name" => "North University",
            "subdomain" => "subdomain".mt_rand().date("his"),
            "timezone" => "Pacific",
            "langid" => $this->langId
        ]);
        $I->seeResponseContains('Name already exists.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateOrganizationDuplicateSubdomain(ApiTester $I)
    {
        $I->wantTo('Create a Organization with duplicate Subdomain by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('organization', [
            "name" => "ORG".mt_rand().date("his"),
            "nick_name" => "North University",
            "subdomain" => "northwest",
            "timezone" => "Pacific",
            "langid" => $this->langId
        ]);
        $I->seeResponseContains('Subdomain already exists.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetOrganization(ApiTester $I)
    {
        $I->wantTo('Get Organization Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('organization', [
            "name" => "ORG".mt_rand().date("his"),
            "nick_name" => "NickName".mt_rand().date("his"),
            "subdomain" => "Subdomain".mt_rand().date("his"),
            "timezone" => "Pacific",
            "langid" => $this->langId
        ]);
        $org = json_decode($I->grabResponse());
        $I->sendGET('organization/' . $org->data->id);
        $I->seeResponseContainsJson(array(
            'id' => $org->data->id
        ));
        $I->seeResponseContains('id');
        $I->seeResponseContains('name');
        $I->seeResponseContains('nick_name');
        $I->seeResponseContains('subdomain');
        $I->seeResponseContains('timezone');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	*/

    public function testGetOrganizationInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Organization Details with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('organization', [
            "name" => uniqid("Org_", true),
            "nick_name" => uniqid("NickName_", true),
            "subdomain" => uniqid("Subdomain_", true),
            "timezone" => "Pacific",
            "langid" => $this->langId
        ]);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetOrganizationInvalid(ApiTester $I)
    {
        $I->wantTo('Get Invalid Organization Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization/' . $this->invalidOrg);
        $I->seeResponseContains('Organization Not Found');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetOrganizations(ApiTester $I)
    {
        $I->wantTo('Get All Organization Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization');
        $org = json_decode($I->grabResponse());
        // print_r($org);exit;
        $I->seeResponseContains('institutions');
        $I->seeResponseContains('id');
        $I->seeResponseContains('name');
        $I->seeResponseContains('nick_name');
        $I->seeResponseContains('subdomain');
        $I->seeResponseContains('timezone');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetOrganizationsInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get All Organization Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization');
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
	/* Need to be Fixed
    public function testUpdateOrganization(ApiTester $I)
    {
        $I->wantTo('Update Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('organization', [
            "name" => "ORG".mt_rand().date("his"),
            "nick_name" => "NickName".mt_rand().date("his"),
            "subdomain" => "Subdomain".mt_rand().date("his"),
            "timezone" => "Pacific",
            "langid" => $this->langId
        ]);
        $organizations = json_decode($I->grabResponse());
        $I->sendPUT('organization/' . $organizations->data->id, [
            'id' => $organizations->data->id,
            'name' => "ORG".mt_rand().date("his"),
            'nick_name' => "North University",
            'subdomain' => "Subdomain".mt_rand().date("his"),
            "timezone" => "Central",
            "langid" => $this->langId,
            "is_send_link" => 1
        ]);
		
        $org = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'id' => $org->data->id
        ));
        $I->seeResponseContains('name');
        $I->seeResponseContains('nick_name');
        $I->seeResponseContains('subdomain');
        $I->seeResponseContains('timezone');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	*/

    public function testUpdateOrganizationInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Update Organization with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('organization/' . $this->organization, [
            'id' => $this->organization,
            'name' => uniqid("Org_", true),
            'nick_name' => "North University",
            'subdomain' => uniqid("Subdomain_", true),
            "timezone" => "Central",
            "langid" => $this->langId,
            "is_send_link" => 1
        ]);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
	/* Need to be Fixed
    public function testDeleteOrganization(ApiTester $I)
    {
        $I->wantTo('Delete Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('organization', [
            "name" => "ORG".mt_rand().date("his"),
            "nick_name" => "NorthwestUniv",
            "subdomain" => "Subdomain".mt_rand().date("his"),
            "timezone" => "Pacific",
            "langid" => $this->langId
        ]);
        $organizations = json_decode($I->grabResponse());
        $I->sendDELETE('organization/' . $organizations->data->id);
        $org = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	*/

    public function testDeleteOrganizationInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Delete Organization with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('organization/' . $this->organization);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testDeleteOrganizationInvalid(ApiTester $I)
    {
        $I->wantTo('Delete Invalid Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendDELETE('organization/' . $this->invalidOrg);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetOverview(ApiTester $I)
    {
        $I->wantTo('Get Overview Details of an Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization/' . $this->organization . '/overview');
        $overview = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'organization_id' => "$this->organization"
        ));
        $I->seeResponseContains('students_count');
        $I->seeResponseContains('staff_count');
        $I->seeResponseContains('permissions_count');
        $I->seeResponseContains('groups_count');
        $I->seeResponseContains('teams_count');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetOverviewInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Overview Details of an Organization with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization/' . $this->organization . '/overview');
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetOverviewInvalidOrganization(ApiTester $I)
    {
        $I->wantTo('Get Overview Details of an Invalid Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization/' . $this->invalidOrg . '/overview');
        $I->seeResponseContains('Unauthorized access to organization: '.$this->invalidOrg);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetActivePermissionset(ApiTester $I)
    {
        $I->wantTo('Get Active Permission set by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization/' . $this->organization . '/permission');
        $I->seeResponseContains('id');
        $I->seeResponseContains('permissionset_name');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetActivePermissionsetInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Active Permission set with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization/' . $this->organization . '/permission');
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetOrganizationDetails(ApiTester $I)
    {
        $I->wantTo('Get Organization Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization/logo/' . $this->organization);
        $list = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'organization_id' => $this->organization
        ));
        $I->seeResponseContains('primary_color');
        $I->seeResponseContains('secondary_color');
        $I->seeResponseContains('ebi_confidentiality_statement');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetOrganizationDetailsInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Organization Details with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization/logo/' . $this->organization);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetCustomConfidStmt(ApiTester $I)
    {
        $I->wantTo('Get Custom Confidential Statement by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization/' . $this->organization . '/custom_confid_stmt');
        $I->seeResponseContainsJson(array(
            'organization_id' => $this->organization
        ));
        $I->seeResponseContains('custom_confidentiality_statement');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetCustomConfidStmtInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Custom Confidential Statement with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization/' . $this->organization . '/custom_confid_stmt');
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetCustomConfidStmtInvalid(ApiTester $I)
    {
        $I->wantTo('Get Custom Confidential Statement for invalid organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization/' . $this->invalidOrg . '/custom_confid_stmt');
        $I->seeResponseContains('Organization Not Found');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
	/* Need to be Fixed
    public function testUpdateOrganizationDetails(ApiTester $I)
    {
        $I->wantTo('Update Organization Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('organization', [
            "name" => "ORG".mt_rand().date("his"),
            "nick_name" => "North University",
            "subdomain" => "Subdomain".mt_rand().date("his"),
            "timezone" => "Pacific",
            "langid" => $this->langId
        ]);
        
        $organization = json_decode($I->grabResponse());
        $I->sendPUT('organization/metadata/customization', [
            "organization_id" => $organization->data->id,
            "primary_color" => "red",
            "secondary_color" => "yellow"
        ]);
        $I->seeResponseContainsJson(array(
            'organization_id' => $organization->data->id
        ));
        $I->seeResponseContainsJson(array(
            'primary_color' => "red"
        ));
        $I->seeResponseContainsJson(array(
            'secondary_color' => "yellow"
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	*/

    public function testUpdateOrganizationDetailsInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Update Organization Details with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('organization/metadata/customization', [
            "organization_id" => $this->organization,
            "primary_color" => "red",
            "secondary_color" => "yellow"
        ]);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
	/* Need to be Fixed
    public function testUpdateCustomConfidStmt(ApiTester $I)
    {
        $I->wantTo('Update Custom Confidential Statement of an Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('organization', [
            "name" => "ORG".mt_rand().date("his"),
            "nick_name" => "NorthwestUniv",
            "subdomain" => "Submain".mt_rand().date("his"),
            "timezone" => "Pacific",
            "langid" => $this->langId
        ]);
        $organization = json_decode($I->grabResponse());
        $I->sendPUT('organization/' . $organization->data->id . '/custom_confid_stmt', [
            
            "organization_id" => $organization->data->id,
            "custom_confidentiality_statement" => "<b><i><u>sweeeeedfewewe,mhm</u></i></b><div><b><i></i></b><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p>wwewe</p></div>"
        ]);
        $I->seeResponseContainsJson(array(
            'organization_id' => $organization->data->id
        ));
        $I->seeResponseContains('custom_confidentiality_statement');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	*/

    public function testUpdateCustomConfidStmtInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Update Custom Confidential Statement of an Organization with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('organization/' . $this->organization . '/custom_confid_stmt', [
            
            "organization_id" => $this->organization,
            "custom_confidentiality_statement" => "<b><i><u>sweeeeedfewewe,mhm</u></i></b><div><b><i></i></b><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p>wwewe</p></div>"
        ]);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testUpdateCustomConfidStmtInvalid(ApiTester $I)
    {
        $I->wantTo('Update Custom Confidential Statement of an Invalid Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('organization/' . $this->invalidOrg . '/custom_confid_stmt', [
            
            "organization_id" => $this->invalidOrg,
            "custom_confidentiality_statement" => "<b><i><u>sweeeeedfewewe,mhm</u></i></b><div><b><i></i></b><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p>wwewe</p></div>"
        ]);
        $I->seeResponseContains('Organization Not Found');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testUserSearch(ApiTester $I)
    {
        $I->wantTo('Search User from an Organization Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization/' . $this->organization . '/users/staff');
        $I->seeResponseContainsJson(array(
            'organization_id' => "$this->organization"
        ));
        $I->seeResponseContains('users');
        $I->seeResponseContains('user_id');
        $I->seeResponseContains('user_firstname');
        $I->seeResponseContains('user_lastname');
        $I->seeResponseContains('user_email');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testUserSearchInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Search User from an Organization Details with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization/' . $this->organization . '/users/staff');
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testUserSearchInvalid(ApiTester $I)
    {
        $I->wantTo('Search User from an invalid Organization Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('organization/' . $this->invalidOrg . '/users/staff');
        $I->seeResponseContains('Organization Not Found');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
}

?>