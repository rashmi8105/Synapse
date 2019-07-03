<?php
use Synapse\CampusResourceBundle\Util\Constants\CampusResourceConstants;
require_once 'tests/api/SynapseTestHelper.php';

class CampusResourceCest extends SynapseTestHelper
{

    private $organizationId = 1;

    private $resourceId;

    private $resourceName;

    private $staffId = 1;

    private $staffName = 'Alon Solly';

    private $resourcePhoneNumber = '9894865500';

    private $resourceEmail = 'dally.bab@gmail.com';

    private $resourceLocation = 'Chennai';

    private $resourceUrl = 'http://facebook.com/dallybab';

    private $resourceDesc = 'Lorem ipsum dolor sit amet, consectetur adipising elit, sed do eiusmod tempor Incident';

    private $receiveReferals = 1;

    private $visibleToStudents = 1;

    private $studentId = 6;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testCreateResourceWithInValidOrgId(ApiTester $I)
    {
        $I->wantTo('Create an Campus Resource With Invalid Org Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        /*
         * $email = uniqid('test_', true) . '@gmail.com'; $ext = uniqid('EXT_', true); $this->emailId = $email; $this->externalId = $ext;
         */
        $this->resourceName = uniqid("Academic Advising_", true);
        
        $I->sendPOST(CampusResourceConstants::RESOURCE_CREATE_URL, [
            'organization_id' => mt_rand(),
            'resource_name' => $this->resourceName,
            'staff_id' => $this->staffId,
            'staff_name' => $this->staffName,
            'resource_phone_number' => $this->resourcePhoneNumber,
            'resource_email' => $this->resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => CampusResourceConstants::ORGANIZATION_NOT_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testCreateResourceWithInValidPersonId(ApiTester $I)
    {
        $I->wantTo('Create an Campus Resource With Invalid Staff Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST(CampusResourceConstants::RESOURCE_CREATE_URL, [
            'organization_id' => $this->organizationId,
            'resource_name' => $this->resourceName,
            'staff_id' => mt_rand(),
            'staff_name' => $this->staffName,
            'resource_phone_number' => $this->resourcePhoneNumber,
            'resource_email' => $this->resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => CampusResourceConstants::PERSON_NOT_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testCreateCampusResource(ApiTester $I)
    {
        $I->wantTo('Create an Campus Resource by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST(CampusResourceConstants::RESOURCE_CREATE_URL, [
            'organization_id' => $this->organizationId,
            'resource_name' => $this->resourceName,
            'staff_id' => $this->staffId,
            'staff_name' => $this->staffName,
            'resource_phone_number' => $this->resourcePhoneNumber,
            'resource_email' => $this->resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'organization_id' => $this->organizationId,
            'resource_name' => $this->resourceName,
            'staff_id' => $this->staffId,
            'staff_name' => $this->staffName,
            'resource_phone_number' => $this->resourcePhoneNumber,
            'resource_email' => $this->resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ));
        $this->resourceId = $resp->data->id;
        $I->seeResponseContains('id');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testCreateResourceWithExistingName(ApiTester $I)
    {
        $I->wantTo('Create an Campus Resource With Existing Name by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST(CampusResourceConstants::RESOURCE_CREATE_URL, [
            'organization_id' => $this->organizationId,
            'resource_name' => $this->resourceName,
            'staff_id' => mt_rand(),
            'staff_name' => $this->staffName,
            'resource_phone_number' => $this->resourcePhoneNumber,
            'resource_email' => $this->resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => CampusResourceConstants::RESOURCE_NAME_ALREADY_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testCreateCampusResourceWithNameContstraintVoilation(ApiTester $I)
    {
        $I->wantTo('Create an Campus Resource with Name Constraint by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $resourceName = '';
        $I->sendPOST(CampusResourceConstants::RESOURCE_CREATE_URL, [
            'organization_id' => $this->organizationId,
            'resource_name' => $resourceName,
            'staff_id' => mt_rand(),
            'staff_name' => $this->staffName,
            'resource_phone_number' => $this->resourcePhoneNumber,
            'resource_email' => $this->resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'validation_error'
        ));
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateCampusResourceWithPhoneNoContstraintVoilation(ApiTester $I)
    {
        $I->wantTo('Create an Campus Resource with Phone Number Constraint by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $resourcePhoneNumber = '';
        $I->sendPOST(CampusResourceConstants::RESOURCE_CREATE_URL, [
            'organization_id' => $this->organizationId,
            'resource_name' => $this->resourceName,
            'staff_id' => mt_rand(),
            'staff_name' => $this->staffName,
            'resource_phone_number' => $resourcePhoneNumber,
            'resource_email' => $this->resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'validation_error'
        ));
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateCampusResourceWithEmailContstraintVoilation(ApiTester $I)
    {
        $I->wantTo('Create an Campus Resource with Email Constraint by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $resourceEmail = '';
        $I->sendPOST(CampusResourceConstants::RESOURCE_CREATE_URL, [
            'organization_id' => $this->organizationId,
            'resource_name' => $this->resourceName,
            'staff_id' => mt_rand(),
            'staff_name' => $this->staffName,
            'resource_phone_number' => $this->resourcePhoneNumber,
            'resource_email' => $resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'validation_error'
        ));
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testUpdateResourceWithInValidOrgId(ApiTester $I)
    {
        $I->wantTo('Update an Campus Resource With Invalid Org Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $this->resourceName = uniqid("Academic Advising_", true);
        
        $I->sendPUT(CampusResourceConstants::RESOURCE_CREATE_URL . '/' . $this->resourceId, [
            'organization_id' => mt_rand(),
            'resource_name' => $this->resourceName,
            'staff_id' => $this->staffId,
            'staff_name' => $this->staffName,
            'resource_phone_number' => $this->resourcePhoneNumber,
            'resource_email' => $this->resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => CampusResourceConstants::ORGANIZATION_NOT_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testUpdateResourceWithInValidStaffId(ApiTester $I)
    {
        $I->wantTo('Update an Campus Resource With Invalid Staff Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPUT(CampusResourceConstants::RESOURCE_CREATE_URL . '/' . $this->resourceId, [
            'organization_id' => $this->organizationId,
            'resource_name' => $this->resourceName,
            'staff_id' => mt_rand(),
            'staff_name' => $this->staffName,
            'resource_phone_number' => $this->resourcePhoneNumber,
            'resource_email' => $this->resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => CampusResourceConstants::PERSON_NOT_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testUpdateCampusResource(ApiTester $I)
    {
        $I->wantTo('Update an Campus Resource by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPUT(CampusResourceConstants::RESOURCE_CREATE_URL . '/' . $this->resourceId, [
            'organization_id' => $this->organizationId,
            'resource_name' => $this->resourceName,
            'staff_id' => $this->staffId,
            'staff_name' => $this->staffName,
            'resource_phone_number' => $this->resourcePhoneNumber,
            'resource_email' => $this->resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testUpdateResourceWithExistingName(ApiTester $I)
    {
        $I->wantTo('Update an Campus Resource With Existing Name by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $resourceName = uniqid("Academic Advising_", true);
        
        // Create New Resource POST and pass that Resource Name to Update and check is already exits or not
        $I->sendPOST(CampusResourceConstants::RESOURCE_CREATE_URL, [
            'organization_id' => $this->organizationId,
            'resource_name' => $resourceName,
            'staff_id' => $this->staffId,
            'staff_name' => $this->staffName,
            'resource_phone_number' => $this->resourcePhoneNumber,
            'resource_email' => $this->resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        
        $I->sendPUT(CampusResourceConstants::RESOURCE_CREATE_URL . '/' . $this->resourceId, [
            'organization_id' => $this->organizationId,
            'resource_name' => $resourceName,
            'staff_id' => $this->staffId,
            'staff_name' => $this->staffName,
            'resource_phone_number' => $this->resourcePhoneNumber,
            'resource_email' => $this->resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => CampusResourceConstants::RESOURCE_NAME_ALREADY_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testUpdateCampusResourceWithNameContstraintVoilation(ApiTester $I)
    {
        $I->wantTo('Update an Campus Resource with Name Constraint by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $resourceName = '';
        $I->sendPUT(CampusResourceConstants::RESOURCE_CREATE_URL . '/' . $this->resourceId, [
            'organization_id' => $this->organizationId,
            'resource_name' => $resourceName,
            'staff_id' => mt_rand(),
            'staff_name' => $this->staffName,
            'resource_phone_number' => $this->resourcePhoneNumber,
            'resource_email' => $this->resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'validation_error'
        ));
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testUpdateCampusResourceWithPhoneNoContstraintVoilation(ApiTester $I)
    {
        $I->wantTo('Update an Campus Resource with Phone Number Constraint by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $resourcePhoneNumber = '';
        $I->sendPUT(CampusResourceConstants::RESOURCE_CREATE_URL . '/' . $this->resourceId, [
            'organization_id' => $this->organizationId,
            'resource_name' => $this->resourceName,
            'staff_id' => mt_rand(),
            'staff_name' => $this->staffName,
            'resource_phone_number' => $resourcePhoneNumber,
            'resource_email' => $this->resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'validation_error'
        ));
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testUpdateCampusResourceWithEmailContstraintVoilation(ApiTester $I)
    {
        $I->wantTo('Update an Campus Resource with Email Constraint by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $resourceEmail = '';
        $I->sendPUT(CampusResourceConstants::RESOURCE_CREATE_URL . '/' . $this->resourceId, [
            'organization_id' => $this->organizationId,
            'resource_name' => $this->resourceName,
            'staff_id' => mt_rand(),
            'staff_name' => $this->staffName,
            'resource_phone_number' => $this->resourcePhoneNumber,
            'resource_email' => $resourceEmail,
            'resource_location' => $this->resourceLocation,
            'resource_url' => $this->resourceUrl,
            'resource_description' => $this->resourceDesc,
            'receive_referals' => $this->receiveReferals,
            'visible_to_students' => $this->visibleToStudents
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'validation_error'
        ));
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetSingleCampusResourceDetails(ApiTester $I)
    {
        $I->wantTo('Get an Single Campus Resource by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET(CampusResourceConstants::RESOURCE_CREATE_URL . '/' . $this->resourceId);
        
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('id');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('resource_name');
        $I->seeResponseContains('staff_id');
        $I->seeResponseContains('staff_name');
        $I->seeResponseContains('resource_phone_number');
        $I->seeResponseContains('resource_email');
        $I->seeResponseContains('resource_location');
        $I->seeResponseContains('resource_url');
        $I->seeResponseContains('resource_description');
        $I->seeResponseContains('receive_referals');
        $I->seeResponseContains('visible_to_students');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetSingleCampusResourceDetailsWithInvalidId(ApiTester $I)
    {
        $I->wantTo('Get an Single Campus Resource With Invalid Resource Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET(CampusResourceConstants::RESOURCE_CREATE_URL . '/' . mt_rand());
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => CampusResourceConstants::CAMPUS_RESC_NOT_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testGetCampusResourceList(ApiTester $I)
    {
        $I->wantTo('Get an Campus Resource List Data by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET(CampusResourceConstants::RESOURCE_CREATE_URL . '?orgId=' . $this->organizationId);
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('id');
        // $I->seeResponseContains('organization_id');
        $I->seeResponseContains('resource_name');
        $I->seeResponseContains('staff_id');
        $I->seeResponseContains('staff_name');
        $I->seeResponseContains('resource_phone_number');
        $I->seeResponseContains('resource_email');
        $I->seeResponseContains('resource_location');
        $I->seeResponseContains('resource_url');
        $I->seeResponseContains('resource_description');
        $I->seeResponseContains('receive_referals');
        $I->seeResponseContains('visible_to_students');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetCampusResourceListWithInvalidOrgId(ApiTester $I)
    {
        $I->wantTo('Get an Campus Resource List Data with Invalid OrgnaizationId by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET(CampusResourceConstants::RESOURCE_CREATE_URL . '?orgId=' . mt_rand());
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => CampusResourceConstants::ORGANIZATION_NOT_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testDeleteCampusResourceWithInvalidResourceId(ApiTester $I)
    {
        $I->wantTo('Delete Campus Resource Data with Invalid Resource ID by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE(CampusResourceConstants::RESOURCE_CREATE_URL . '/' . mt_rand());
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => CampusResourceConstants::CAMPUS_RESC_NOT_FOUND
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testDeleteCampusResource(ApiTester $I)
    {
        $I->wantTo('Delete Campus Resource Data by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE(CampusResourceConstants::RESOURCE_CREATE_URL . '/' . $this->resourceId);
        $I->seeResponseCodeIs(204);
    }
	/* Need to be Fixed
    public function testGetCampusResourceForStudent(ApiTester $I)
    {
        $I->wantTo('Get an Campus Resource For Student Data by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET(CampusResourceConstants::STUDENT_RESOURCE_URL .'/'.$this->studentId.'/campusresources');
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('campus_resource_list');
        $I->seeResponseContains('campus_name');
        $I->seeResponseContains('campus_id');
        $I->seeResponseContains('staff_name');
        $I->seeResponseContains('resource_phone_number');
        $I->seeResponseContains('resource_email');
        $I->seeResponseContains('resource_location');
        $I->seeResponseContains('resource_url');
        $I->seeResponseContains('resource_description');
        $I->seeResponseContains('receive_referals');
        $I->seeResponseContains('visible_to_students');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }
	*/

    public function testGetCampusResourceForStudentWithInvalidStudentId(ApiTester $I)
    {
        $I->wantTo('Get an Campus Resource For Student Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET(CampusResourceConstants::STUDENT_RESOURCE_URL .'/'.mt_rand().'/campusresources');
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => CampusResourceConstants::CAMPUS_RESC_NOT_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }
}