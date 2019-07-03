<?php
use GuzzleHttp\json_decode;
require_once 'tests/api/SynapseTestHelper.php';

class AcademicYearCest extends SynapseTestHelper
{

    private $token;

    private $organization = 1;

    private $inValidOrganization = -200;

    private $name = "Academic Year 2015";

    private $yearId = "201516";

    private $start;

    private $end;

    private $academicYearId = 15;

    private $invalidEndDate = "2013-02-12";

    private $invalidYearId = - 1;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
        $this->start = new DateTime("now");
        $this->end = clone $this->start;
        $this->end->add(new \DateInterval('P7M'));
    }
	
    public function testcreateAcademicyearWithValidAuthentication(ApiTester $I)
    {
        $I->wantTo('Create an academic year with valid authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST('academicyears', [
            "organization_id" => $this->organization,
            "name" => $this->name,
            "year_id" => "202021",
            "start_date" => "2020-01-01",
            "end_date" => "2021-01-01"
        ]);

        $I->seeResponseContainsJson(array('organization_id' => $this->organization));
        $I->seeResponseContainsJson(array('year_id' => "202021"));
        $I->seeResponseContainsJson(array('start_date' => "2020-01-01"));
        $I->seeResponseContainsJson(array('end_date' => "2021-01-01"));
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        
        $year = json_decode($I->grabResponse());
        $I->sendDELETE('academicyears/' . $year->data->organization_id . '/' . $year->data->id);
    }
	

    public function testcreateAcademicyearWithInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Create an academic year with invalid authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('academicyears', [
            "organization_id" => $this->organization,
            "name" => $this->name,
            "year_id" => $this->yearId,
            "start_date" => $this->start->format("Y-m-d"),
            "end_date" => $this->end->format("Y-m-d")
        ]);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testcreateAcademicyearInvalidOrg(ApiTester $I)
    {
        $I->wantTo('Create an academic year with invalid organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('academicyears', [
            "organization_id" => $this->inValidOrganization,
            "name" => $this->name,
            "year_id" => $this->yearId,
            "start_date" => $this->start->format("Y-m-d"),
            "end_date" => $this->end->format("Y-m-d")
        ]);
        
        $I->seeResponseContains('Unauthorized access to organization: '.$this->inValidOrganization);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testcreateAcademicyearInvalidEndDate(ApiTester $I)
    {
        $I->wantTo('Create an academic year with invalid end date by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('academicyears', [
            "organization_id" => $this->organization,
            "name" => $this->name,
            "year_id" => $this->yearId,
            "start_date" => $this->start->format("Y-m-d"),
            "end_date" => $this->invalidEndDate
        ]);
        $I->seeResponseContains('End-Date should be grater than Start-Date');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetAcademicYearInvalidOrg(ApiTester $I)
    {
        $I->wantTo("Get Academic year with invalid Organization by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicyears/' . $this->inValidOrganization . "/" . $this->academicYearId);
        $I->seeResponseContains('Unauthorized access to organization: '.$this->inValidOrganization);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetAcademicYearInvalidYear(ApiTester $I)
    {
        $I->wantTo("Get Academic Year with invalid Academic Year by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicyears/' . $this->organization . "/" . $this->invalidYearId);
        $I->seeResponseContains('Year Id does not exist.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetAcademicYear(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo("Get Academic Year by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $this->yearId = "201719";
        $I->sendPOST('academicyears', [
            "organization_id" => $this->organization,
            "name" => $this->name,
            "year_id" => "202930",
            "start_date" => "2029-02-12",
            "end_date" => "2030-02-12"
        ]);
        $year = json_decode($I->grabResponse());
        $I->sendGET('academicyears/' . $this->organization . "/" . $year->data->id);
        $getYear = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'organization_id' => $this->organization
        ));
        $I->seeResponseContainsJson(array(
            'name' => $this->name
        ));
        $I->seeResponseContainsJson(array(
            'year_id' => $getYear->data->year_id
        ));
        $I->seeResponseContains('id');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('year_id');
        $I->seeResponseContains('start_date');
        $I->seeResponseContains('end_date');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        
        $I->sendDELETE('academicyears/' . $year->data->organization_id . '/' . $year->data->id);
    }

    public function testListAcademicYearInvalidOrg(ApiTester $I)
    {
        $I->wantTo("List Academic year with invalid Organization by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicyears/' . $this->inValidOrganization);
        $I->seeResponseContains('Unauthorized access to organization: '.$this->inValidOrganization);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testListAcademicYear(ApiTester $I)
    {
        $I->wantTo("List Academic Year by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('academicyears', [
            "organization_id" => $this->organization,
            "name" => $this->name,
            "year_id" => "201718",
            "start_date" => "2024-02-12",
            "end_date" => "2025-02-12"
        ]);
        
        $year = json_decode($I->grabResponse());
        
        $I->sendGET('academicyears/' . $this->organization);
        $getYear = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'organization_id' => $getYear->data->organization_id
        ));
        $I->seeResponseContainsJson(array(
            'name' => $getYear->data->academic_years[0]->name
        ));
        $I->seeResponseContainsJson(array(
            'year_id' => $getYear->data->academic_years[0]->year_id
        ));
        $I->seeResponseContains('id');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('year_id');
        $I->seeResponseContains('start_date');
        $I->seeResponseContains('end_date');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        
        
        $I->sendDELETE('academicyears/' . $year->data->organization_id . '/' . $year->data->id);
    } 

    public function testEditAcademicYear(ApiTester $I, $scenario)
    {
        $I->wantTo("Edit Academic Year by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('academicyears', [
            "organization_id" => $this->organization,
            "name" => $this->name,
            "year_id" => "202728",
            "start_date" => "2027-09-12",
            "end_date" => "2028-06-12"
        ]);
        $year = json_decode($I->grabResponse());
        
        $I->sendPUT('academicyears', [
            "id" => $year->data->id,
            "organization_id" => $this->organization,
            "name" => "Academic year edited",
            "year_id" => "202728",
            "start_date" => "2027-08-12",
            "end_date" => "2028-05-12"
        ]);
        $I->seeResponseCodeIs(204);
        $I->sendDELETE('academicyears/' . $year->data->organization_id . '/' . $year->data->id);
    }

     public function testDeleteAcademicYear(ApiTester $I)
    {
        $I->wantTo("Delete Academic Year by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('academicyears', [
            "organization_id" => $this->organization,
            "name" => $this->name,
            "year_id" => "202425",
            "start_date" => "2050-02-12",
            "end_date" => "2052-05-12"
        ]);
        $year = json_decode($I->grabResponse());
        $I->sendDELETE('academicyears/' . $year->data->organization_id . '/' . $year->data->id);
        $I->seeResponseCodeIs(204);
    }

    public function testEditAcademicYearInvalidOrg(ApiTester $I)
    {
        $I->wantTo("Edit Academic Year with Invalid Organization by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('academicyears', [
            "id" => $this->academicYearId,
            "organization_id" => $this->inValidOrganization,
            "name" => "Academic year 2015",
            "year_id" => "201819",
            "start_date" => "2022-02-12",
            "end_date" => "2023-02-12"
        ]);
        $I->seeResponseContains('Unauthorized access to organization: '.$this->inValidOrganization);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testEditAcademicYearInvalidYear(ApiTester $I)
    {
        $I->wantTo("Edit Academic Year with Invalid Academic Year by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('academicyears', [
            "id" => $this->invalidYearId,
            "organization_id" => $this->organization,
            "name" => "Academic year 2015 edited",
            "year_id" => "201819",
            "start_date" => "2023-02-12",
            "end_date" => "2024-02-12"
        ]);
        $I->seeResponseContains('Year Id does not exist.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testDeleteAcademicYearInvalidOrganization(ApiTester $I)
    {
        $I->wantTo("Delete Academic year with invalid organization by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('academicyears/' . $this->inValidOrganization . '/' . $this->academicYearId);
        $I->seeResponseContains('Unauthorized access to organization: '.$this->inValidOrganization);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testDeleteAcademicYearInvalidYear(ApiTester $I)
    {
        $I->wantTo("Delete Academic Year with Invalid Academic Year by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('academicyears/' . $this->organization . '/' . $this->invalidYearId);
        $I->seeResponseContains('Year Id does not exist.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetAcademicYearInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Academic Year by API with invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicyears/' . $this->organization . "/" . $this->academicYearId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testListAcademicYearInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('List Academic Year by API with invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicyears/' . $this->organization);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testEditAcademicYearInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Edit Academic Year by API with invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('academicyears', [
            "id" => $this->academicYearId,
            "organization_id" => $this->organization,
            "name" => "Academic year 2015 edited",
            "year_id" => "201819",
            "start_date" => "2023-02-12",
            "end_date" => "2024-02-12"
        ]);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testDeleteAcademicYearInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Delete Academic Year by API with invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('academicyears/' . $this->organization . '/' . $this->academicYearId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testListYear(ApiTester $I)
    {
        $I->wantTo("List Year by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicyears/year');
        $getYear = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'id' => $getYear->data->year_id[0]->id
        ));
        $I->seeResponseContains('id');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testListYearInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('List year API with invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicyears/year');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
    
    public function testcreateAcademicyearWithYearOverlap(ApiTester $I)
    {
        $I->wantTo('Create an academic year with Year Overlap by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
    
        $I->sendPOST('academicyears', [
            "organization_id" => $this->organization,
            "name" => $this->name,
            "year_id" => "202021",
            "start_date" => "2020-01-01",
            "end_date" => "2021-01-01"
            ]);
        
        $year = json_decode($I->grabResponse());
        $I->sendPOST('academicyears', [
            "organization_id" => $this->organization,
            "name" => $this->name,
            "year_id" => "202122",
            "start_date" => "2020-01-01",
            "end_date" => "2021-01-01"
            ]);

        $I->seeResponseContains('Academic year should not overlap with other years for same organization.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    
        $I->sendDELETE('academicyears/' . $year->data->organization_id . '/' . $year->data->id);
    }
    
    public function testGetCurrentAcademicYearDetails(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo("Get Get Current Academic Year Details by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('academicyears/' . $this->organization . "/current");
        $getYear = json_decode($I->grabResponse());

        $I->seeResponseContainsJson(array(
            'organization_id' => $this->organization
        ));
        $I->seeResponseContainsJson(array(
            'name' => 'Educational Year'
        ));
        $I->seeResponseContainsJson(array(
            'year_id' => '202627'
        ));
        $I->seeResponseContains('id');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('year_id');
        $I->seeResponseContains('start_date');
        $I->seeResponseContains('end_date');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testEditAcademicYearOverlap(ApiTester $I, $scenario)
    {
        $I->wantTo("Edit Academic Year Overlap by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('academicyears', [
            "organization_id" => $this->organization,
            "name" => $this->name,
            "year_id" => "202728",
            "start_date" => "2027-09-12",
            "end_date" => "2028-06-12"
            ]);
        $year1 = json_decode($I->grabResponse());
    
        $I->sendPOST('academicyears', [
            "organization_id" => $this->organization,
            "name" => $this->name,
            "year_id" => "202223",
            "start_date" => "2022-09-12",
            "end_date" => "2023-06-12"
            ]);
        $year2 = json_decode($I->grabResponse());
        
        $I->sendPUT('academicyears', [
            "id" => $year2->data->id,
            "organization_id" => $this->organization,
            "name" => "Academic year edited",
            "year_id" => "202223",
            "start_date" => "2027-08-12",
            "end_date" => "2028-05-12"
            ]);
        $I->seeResponseContains('Academic year should not overlap with other years for same organization.');
        $I->seeResponseCodeIs(400);
        $I->sendDELETE('academicyears/' . $year1->data->organization_id . '/' . $year1->data->id);
        $I->sendDELETE('academicyears/' . $year2->data->organization_id . '/' . $year2->data->id);
    }
}
