<?php
use Synapse\CoreBundle\Util\Constants\UsersConstant;
require_once 'tests/api/SynapseTestHelper.php';

class UserCest extends SynapseTestHelper
{

    private $person = 1;

    private $campusId = 1;

    private $firstName = 'myfirstname';

    private $lastName = 'mylastname';

    private $title = "Mr";

    private $emailId;

    private $externalId;

    private $phone = 9894862200;

    private $roleId = 1;

    private $isMobile = TRUE;

    private $userId;

    private $facultyId;

    private $fEmailId;

    private $fExternalId;

    private $studentId;

    private $sEmailId;

    private $sExternalId;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testCreateWithInValidCampusId(ApiTester $I)
    {
        $I->wantTo('Create an Coordinator With Invalid Campus Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $email = uniqid('test_', true) . '@gmail.com';
        $ext = uniqid('EXT_', true);
        
        $this->emailId = $email;
        $this->externalId = $ext;
        
        $I->sendPOST('users', [
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $email,
            'externalid' => $ext,
            'user_type' => UsersConstant::FILTER_COORDINATOR,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => mt_rand(),
            'roleid' => $this->roleId
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => UsersConstant::ORGANIZATION_NOT_FOUND
        ));
        $I->canSeeResponseCodeIs(403);
        $I->canSeeResponseIsJson();
    }

    public function testCreateCoordWithInValidRoleId(ApiTester $I)
    {
        $I->wantTo('Create an Coordinator with Invalid Role Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST('users', [
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $this->emailId,
            'externalid' => $this->externalId,
            'user_type' => UsersConstant::FILTER_COORDINATOR,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => $this->campusId,
            'roleid' => mt_rand()
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => UsersConstant::ERROR_ROLE_NOT_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testCreateCoordinator(ApiTester $I)
    {
        $I->wantTo('Create an Coordinator by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST('users', [
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $this->emailId,
            'externalid' => $this->externalId,
            'user_type' => UsersConstant::FILTER_COORDINATOR,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => $this->campusId,
            'roleid' => $this->roleId
        ]);
        $resp = json_decode($I->grabResponse());
        $this->userId = $resp->data->id;
        
        $I->seeResponseIsJson(array(
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $this->emailId,
            'externalid' => $this->externalId,
            'user_type' => UsersConstant::FILTER_COORDINATOR,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => $this->campusId,
            'roleid' => $this->roleId
        ));
        $I->seeResponseContains('id');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testCreateFaculty(ApiTester $I)
    {
        $I->wantTo('Create an Faculty by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $this->fEmailId = uniqid('test_', true) . '@gmail.com';
        $this->fExternalId = uniqid('EXT_', true);
        
        $I->sendPOST('users', [
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $this->fEmailId,
            'externalid' => $this->fExternalId,
            'user_type' => UsersConstant::FILTER_FACULTY,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => $this->campusId
        ]);
        $resp = json_decode($I->grabResponse());
        $this->facultyId = $resp->data->id;
        
        $I->seeResponseIsJson(array(
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $this->fEmailId,
            'externalid' => $this->fExternalId,
            'user_type' => UsersConstant::FILTER_FACULTY,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => $this->campusId
        ));
        $I->seeResponseContains('id');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testCreateStudent(ApiTester $I)
    {
        $I->wantTo('Create an Faculty by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $this->sEmailId = uniqid('test_', true) . '@gmail.com';
        $this->sExternalId = uniqid('EXT_', true);
        
        $I->sendPOST('users', [
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $this->sEmailId,
            'externalid' => $this->sExternalId,
            'user_type' => UsersConstant::FILTER_STUDENT,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => $this->campusId
        ]);
        $resp = json_decode($I->grabResponse());
        $this->studentId = $resp->data->id;
        
        $I->seeResponseIsJson(array(
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $this->sEmailId,
            'externalid' => $this->sExternalId,
            'user_type' => UsersConstant::FILTER_STUDENT,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => $this->campusId
        ));
        $I->seeResponseContains('id');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testCreateCoordWithExistingExtId(ApiTester $I)
    {
        $I->wantTo('Create an Coordinator with Existing External Id');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST('users', [
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $this->emailId,
            'externalid' => $this->externalId,
            'user_type' => UsersConstant::FILTER_COORDINATOR,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => $this->campusId,
            'roleid' => $this->roleId
        ]);
        $resp = json_decode($I->grabResponse());
        
        $I->seeResponseIsJson(array(
            'error' => UsersConstant::EXTERNALID_ALREADY_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testUpdateCoordinatorUser(ApiTester $I)
    {
        $I->wantTo('Update an User by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPUT('users/' . $this->userId, [
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $this->emailId,
            'externalid' => $this->externalId,
            'user_type' => UsersConstant::FILTER_COORDINATOR,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => $this->campusId,
            'roleid' => $this->roleId
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testUpdateFacultyUser(ApiTester $I)
    {
        $I->wantTo('Update an User by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPUT('users/' . $this->facultyId, [
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $this->fEmailId,
            'externalid' => $this->fExternalId,
            'user_type' => UsersConstant::FILTER_FACULTY,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => $this->campusId
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testUpdateStudentUser(ApiTester $I)
    {
        $I->wantTo('Update an User by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPUT('users/' . $this->studentId, [
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $this->sEmailId,
            'externalid' => $this->sExternalId,
            'user_type' => UsersConstant::FILTER_STUDENT,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => $this->campusId
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testUpdateCoordWithInValidCampusId(ApiTester $I)
    {
        $I->wantTo('Update an Coordinator User with Invalid Campus ID by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
		$inCamId = mt_rand();
        $I->sendPUT('users/' . $this->userId, [
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $this->emailId,
            'externalid' => $this->externalId,
            'user_type' => UsersConstant::FILTER_COORDINATOR,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => $inCamId,
            'roleid' => $this->roleId
        ]);
        $I->seeResponseContains("Unauthorized access to organization: ".$inCamId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testUpdateCoordWithInValidRoleId(ApiTester $I)
    {
        $I->wantTo('Update an Coordinator User with Invalid Role ID by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPUT('users/' . $this->userId, [
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $this->emailId,
            'externalid' => $this->externalId,
            'user_type' => UsersConstant::FILTER_COORDINATOR,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => $this->campusId,
            'roleid' => mt_rand()
        ]);
        $I->seeResponseIsJson(array(
            'error' => UsersConstant::ERROR_ROLE_NOT_FOUND
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testUpdateCoordWithInValidUserId(ApiTester $I)
    {
        $I->wantTo('Update an Coordinator User with Invalid User ID by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPUT('users/' . mt_rand(), [
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'title' => $this->title,
            'email' => $this->emailId,
            'externalid' => $this->externalId,
            'user_type' => UsersConstant::FILTER_COORDINATOR,
            'phone' => $this->phone,
            'ismobile' => $this->isMobile,
            'campusid' => $this->campusId,
            'roleid' => $this->roleId
        ]);
        $I->seeResponseIsJson(array(
            'error' => UsersConstant::ERROR_PERSON_NOT_FOUND
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testPromoteUser(ApiTester $I)
    {
        $I->wantTo('Promote User to Coordinator by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPUT('users/' . $this->userId, [
            'roleid' => $this->roleId,
            'campusid' => $this->campusId
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseContains('id');
        $I->seeResponseContains('roleid');
        $I->seeResponseContains('campusid');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testPromtUserToCoordinatorwithInValidRole(ApiTester $I)
    {
        $I->wantTo('Promote User to Coordinator with InValid RoleId by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPUT('users/' . $this->userId, [
            'roleid' => mt_rand(),
            'campusid' => $this->campusId
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => UsersConstant::ERROR_ROLE_NOT_FOUND
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testPromtUserToCoordinatorwithInValidId(ApiTester $I)
    {
        $I->wantTo('Promote User to Coordinator with InValid Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPUT('users/' . mt_rand(), [
            'roleid' => $this->roleId,
            'campusid' => $this->campusId
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => UsersConstant::ERROR_PERSON_NOT_FOUND
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetCoordinatorUsers(ApiTester $I)
    {
        $I->wantTo('Get an Users Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('users?campusId=' . $this->campusId . '&type=' . UsersConstant::FILTER_COORDINATOR);
        
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('id');
        $I->seeResponseContains('firstname');
        $I->seeResponseContains('lastname');
        $I->seeResponseContains('email');
        $I->seeResponseContains('externalid');
        $I->seeResponseContains('phone');
        $I->seeResponseContains('ismobile');
        $I->seeResponseContains('role');
        $I->seeResponseContains('roleid');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetFacultyUsers(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo('Get an Faculty Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('users?campusId=' . $this->campusId . '&type=' . UsersConstant::FILTER_FACULTY);
        
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains(UsersConstant::FILTER_FACULTY);
        /*$I->seeResponseContains('id');
        $I->seeResponseContains('firstname');
        $I->seeResponseContains('lastname');
        $I->seeResponseContains('email');
        $I->seeResponseContains('externalid');
        $I->seeResponseContains('phone');
        $I->seeResponseContains('ismobile');*/
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetStudentUsers(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo('Get an Student Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('users?campusId=' . $this->campusId . '&type=' . UsersConstant::FILTER_STUDENT);
        
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains(UsersConstant::FILTER_STUDENT);
        /*$I->seeResponseContains('id');
        $I->seeResponseContains('firstname');
        $I->seeResponseContains('lastname');
        $I->seeResponseContains('email');
        $I->seeResponseContains('externalid');
        $I->seeResponseContains('phone');
        $I->seeResponseContains('ismobile');*/
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetUsersWithInvalidType(ApiTester $I)
    {
        $I->wantTo('Get an User Details with Invalid User Type Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('users?campusId=' . $this->campusId . '?type=invalid');
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => UsersConstant::ERROR_INVALID_TYPE_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testGetUsersWithInvalidCampId(ApiTester $I)
    {
        $I->wantTo('Get an User Details with Invalid Campus Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('users?campusId=' . mt_rand() . '?type=' . UsersConstant::FILTER_STUDENT);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => UsersConstant::ORGANIZATION_NOT_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testGetSingleUser(ApiTester $I)
    {
        $I->wantTo('Get an single User Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('users/' . $this->userId . '?campus-id=' . $this->campusId);
        
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('id');
        $I->seeResponseContains('firstname');
        $I->seeResponseContains('lastname');
        $I->seeResponseContains('email');
        $I->seeResponseContains('externalid');
        $I->seeResponseContains('user_type');
        $I->seeResponseContains('phone');
        $I->seeResponseContains('ismobile');
        
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetSingleFacultyUser(ApiTester $I)
    {
        $I->wantTo('Get an single Faculty User Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('users/' . $this->facultyId . '?campus-id=' . $this->campusId);
        
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('id');
        $I->seeResponseContains('firstname');
        $I->seeResponseContains('lastname');
        $I->seeResponseContains('email');
        $I->seeResponseContains('externalid');
        $I->seeResponseContains('user_type');
        $I->seeResponseContains('phone');
        $I->seeResponseContains('ismobile');
        
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetSingleStudentUser(ApiTester $I)
    {
        $I->wantTo('Get an single Student User Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('users/' . $this->studentId . '?campus-id=' . $this->campusId);
        
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('id');
        $I->seeResponseContains('firstname');
        $I->seeResponseContains('lastname');
        $I->seeResponseContains('email');
        $I->seeResponseContains('externalid');
        $I->seeResponseContains('user_type');
        $I->seeResponseContains('phone');
        $I->seeResponseContains('ismobile');
        
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetSingleUserWithInvalidId(ApiTester $I)
    {
        $I->wantTo('Get an single User Details With Invalid UserId by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('users/' . mt_rand() . '?campus-id=' . $this->campusId);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => UsersConstant::ERROR_PERSON_NOT_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testGetSendInviteToInvalidUserId(ApiTester $I)
    {
        $I->wantTo('Get an Sent a Invitation Link to Invalid userId by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('users/' . $this->campusId . '/user/' . mt_rand() . '/sendlink');
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => UsersConstant::ERROR_PERSON_NOT_FOUND
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testGetSendInviteToInvalidCampusId(ApiTester $I)
    {
        $I->wantTo('Get an Sent a Invitation Link to Invalid userId by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $inCamId = mt_rand();
        $I->sendGET('users/' . $inCamId . '/user/' . $this->userId . '/sendlink');
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseContains("Unauthorized access to organization: ".$inCamId);
        $I->canSeeResponseCodeIs(403);
        $I->canSeeResponseIsJson();
    }

    public function testGetSendInviteToUser(ApiTester $I)
    {
        $I->wantTo('Get an Sent a Invitation Link to user by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('users/' . $this->campusId . '/user/' . $this->userId . '/sendlink');
        
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('email_detail');
        $I->seeResponseContains('message');
        $I->seeResponseContains('email_sent_status');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetSendInviteToFactly(ApiTester $I)
    {
        $I->wantTo('Get an Sent a Invitation Link to Faculty by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('users/' . $this->campusId . '/user/' . $this->facultyId . '/sendlink');
        
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('email_detail');
        $I->seeResponseContains('message');
        $I->seeResponseContains('email_sent_status');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetSendInviteToStudent(ApiTester $I)
    {
        $I->wantTo('Get an Sent a Invitation Link to Student by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('users/' . $this->campusId . '/user/' . $this->studentId . '/sendlink');
        
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('email_detail');
        $I->seeResponseContains('message');
        $I->seeResponseContains('email_sent_status');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testDeleteUserWithInvalidUserId(ApiTester $I)
    {
        $I->wantTo('Delete User Data with Invalid User ID by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendDELETE('users/' .mt_rand() .'?campus-id='. $this->campusId );
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => UsersConstant::ERROR_PERSON_NOT_FOUND
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testDeleteUserWithInvalidCampusId(ApiTester $I)
    {
        $invCamId = mt_rand();
		$I->wantTo('Delete User Data with Invalid Campus Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendDELETE('users/' .$this->userId .'?campus-id='. $invCamId );
        $resp = json_decode($I->grabResponse());
        $I->seeResponseContains("Unauthorized access to organization: ".$invCamId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testDeleteCoordinator(ApiTester $I)
    {
        $I->wantTo('Delete Coordinator Data by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('users/' . $this->userId . '?campus-id=' . $this->campusId.'&type=coordinator');
        $I->seeResponseCodeIs(204);
    }

    /* Need to be Fixed
	public function testDeleteStudent(ApiTester $I)
    {
        $I->wantTo('Delete student Data by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('users/' . $this->studentId. '?campus-id=' . $this->campusId.'&type=student');
        $I->seeResponseCodeIs(204);
    }*/

    public function testDeleteFaculty(ApiTester $I)
    {
        $I->wantTo('Delete Facutly Data by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('users/' . $this->facultyId. '?campus-id=' . $this->campusId.'&type=faculty');
        $I->seeResponseCodeIs(204);
    }
}
