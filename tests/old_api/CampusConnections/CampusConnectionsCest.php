<?php
require_once 'tests/api/SynapseTestHelper.php';

class CampusConnectionsCest extends SynapseTestHelper
{

    private $orgId = 1;

    private $personStudentId = 6;

    private $personStudentId2 = 8;

    private $token;

    private $InvalidOrgId = -200;

    private $invalidPersonStudentId = - 1;

    private $personFacultyId = 4;

    private $invalidFacultyId = 0;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testGetStudentFacultyConnections(ApiTester $I)
    {
        $I->wantTo('Get Student Faculty Campus Connections by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('campusconnections/faculty' . '?org_id=' . $this->orgId . '&studentids=' . $this->personStudentId . ',' . $this->personStudentId2);
        $facultyConn = json_decode($I->grabResponse());
        $I->seeResponseContains('staff_details');
        if (! empty($facultyConn->data->staff_details)) {
            foreach ($facultyConn->data->staff_details as $faculty) {
                $I->seeResponseContains('id');
                $I->seeResponseContains('external_id');
                $I->seeResponseContains('firstname');
                if (! empty($faculty->students)) {
                    foreach ($faculty->students as $students) {
                        $I->seeResponseContainsJson(array(
                            'student_id' => $this->personStudentId
                        ));
                        $I->seeResponseContainsJson(array(
                            'student_id' => $this->personStudentId2
                        ));
                    }
                }
            }
        }
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetStudentFacultyConnectionsInvalidOrg(ApiTester $I)
    {
        $I->wantTo('Get Student Faculty Campus Connections with invalid organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('campusconnections/faculty' . '?org_id=' . $this->InvalidOrgId . '&studentids=' . $this->personStudentId . ',' . $this->personStudentId2);
        $I->seeResponseContains('Organization Not Found.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
	/*
    public function testGetStudentCampusConnections(ApiTester $I)
    {
        $I->wantTo('Get Campus Connections for student by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('campusconnections/' . $this->personStudentId . '?orgId=' . $this->orgId);
        $connections = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'organization_id' => $this->orgId
        ));
        $I->seeResponseContains('campus_id');
        $I->seeResponseContains('campus_name');
        $I->seeResponseContains('campus_connections');
        if (! empty($connections->data->campus_connections)) {
            foreach ($connections->data->campus_connections as $faculty) {
                $I->seeResponseContains('person_id');
                $I->seeResponseContains('person_firstname');
                $I->seeResponseContains('person_lastname');
                $I->seeResponseContains('primary_connection');
            }
        }
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	*/

    public function testGetStudentCampusConnectionsInvalidOrg(ApiTester $I)
    {
        $I->wantTo('Get Campus Connections for student with invalid organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('campusconnections/' . $this->personStudentId . '?orgId=' . $this->InvalidOrgId);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetStudentCampusConnectionsInvalidStudent(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Get Campus Connections with invalid student by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('campusconnections/' . $this->invalidPersonStudentId . '?orgId=' . $this->orgId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetStudentFacultyConnectionsInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Student Faculty Campus Connections with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('campusconnections/faculty' . '?org_id=' . $this->orgId . '&studentids=' . $this->personStudentId . ',' . $this->personStudentId2);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetStudentCampusConnectionsInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Campus Connections for student with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('campusconnections/' . $this->personStudentId . '?orgId=' . $this->orgId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testAssignPrimaryConnection(ApiTester $I)
    {
        $I->wantTo('Assign a primary campus connection to student(s)');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('campusconnections/primaryconnection', [
            "organization_id" => $this->orgId,
            "student_list" => [
                [
                    "student_id" => $this->personStudentId,
                    "staff_id" => $this->personFacultyId
                ]
            ]
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(array(
            'organization_id' => $this->orgId
        ));
    }

    public function testAssignPrimaryConnectionInvalidOrganization(ApiTester $I)
    {
        $I->wantTo('Assign a primary campus connection to student(s) with invalid organization');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('campusconnections/primaryconnection', [
            "organization_id" => $this->InvalidOrgId,
            "student_list" => [
                [
                    "student_id" => $this->personStudentId,
                    "staff_id" => $this->personFacultyId
                ]
            ]
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testAssignPrimaryConnectionInvalidFaculty(ApiTester $I)
    {
        $I->wantTo('Assign a primary campus connection to student(s) with invalid Faculty');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('campusconnections/primaryconnection', [
            "organization_id" => $this->orgId,
            "student_list" => [
                [
                    "student_id" => $this->personStudentId,
                    "staff_id" => $this->invalidFacultyId
                ]
            ]
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(array(
            'student_list' => array(
                'is_primary_assigned' => false
            )
        ));
    }
	/* Need to be Fixed
    public function testRemovePrimaryConnection(ApiTester $I)
    {
        $I->wantTo('Remove primary connection by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('campusconnections/primaryconnection?orgId=' . $this->orgId . '&studentId=' . $this->personStudentId);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }
	*/
    
    public function testRemovePrimaryConnectionInvalidStudent(ApiTester $I)
    {
        $I->wantTo('Remove primary connection with invalid student by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('campusconnections/primaryconnection?orgId=' . $this->orgId . '&studentId=' . $this->invalidPersonStudentId);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testRemovePrimaryConnectionInvalidOrganization(ApiTester $I)
    {
        $I->wantTo('Remove primary connection with invalid organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('campusconnections/primaryconnection?orgId=' . $this->InvalidOrgId . '&studentId=' . $this->personStudentId);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
}
