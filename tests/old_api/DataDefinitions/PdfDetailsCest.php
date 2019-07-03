<?php
require_once 'tests/api/SynapseTestHelper.php';

class PdfDetailsCest extends SynapseTestHelper
{
    private $token;
    private $organization = 1;
    private $invalidOrg = -1;
    
    /**
     *  
     * @param ApiTester $I
     * Initialize the token variable for Authorization
     */
    public function _before(ApiTester $I) {
        $this->token = $this->authenticate ( $I );
    }
    
    public function testGetFacultyUploadPdfDetails(ApiTester $I, $scenario)
    {
        $scenario->skip("Breaks sonar reporting, causes system-out messages");
        $I->wantTo ( "Get faculty upload pdf details by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->sendGET ( 'pdf/faculty');
        $I->seeResponseCodeIs ( 204 );
    }
    
    public function testGetStudentUploadPdfDetails(ApiTester $I, $scenario)
    {
        $scenario->skip("Breaks sonar reporting, causes system-out messages");
        $I->wantTo ( "Get student upload pdf details by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->sendGET ( 'pdf/students/'.$this->organization);
        $I->seeResponseCodeIs ( 204 );
    }
    
    public function testGetCourseUploadPdfDetails(ApiTester $I, $scenario)
    {
        $scenario->skip("Breaks sonar reporting, causes system-out messages");
        $I->wantTo ( "Get Course upload pdf details by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->sendGET ( 'pdf/courses');
        $I->seeResponseCodeIs ( 204 );
    }
    
    public function testGetCourseFacultyUploadPdfDetails(ApiTester $I, $scenario)
    {
        $scenario->skip("Breaks sonar reporting, causes system-out messages");
        $I->wantTo ( "Get Course Faculty upload pdf details by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->sendGET ( 'pdf/courses/faculty');
        $I->seeResponseCodeIs ( 204 );
    }
    
    public function testGetCourseStudentsUploadPdfDetails(ApiTester $I, $scenario)
    {
        $scenario->skip("Breaks sonar reporting, causes system-out messages");
        $I->wantTo ( "Get Course Students upload pdf details by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->sendGET ( 'pdf/courses/students');
        $I->seeResponseCodeIs ( 204 );
    }
    
    public function testGetAcademicUpdateUploadPdfDetails(ApiTester $I, $scenario)
    {
        $scenario->skip("Breaks sonar reporting, causes system-out messages");
        $I->wantTo ( "Get Academic Update upload pdf details by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->sendGET ( 'pdf/academicupdates');
        $I->seeResponseCodeIs ( 204 );
    }
    
    public function testGetSubGroupsUploadPdfDetails(ApiTester $I, $scenario)
    {
        $scenario->skip("Breaks sonar reporting, causes system-out messages");
        $I->wantTo ( "Get Sub groups upload pdf details by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->sendGET ( 'pdf/subgroups');
        $I->seeResponseCodeIs ( 204 );
    }
    
    public function testGetGroupsFacultyUploadPdfDetails(ApiTester $I, $scenario)
    {
        $scenario->skip("Breaks sonar reporting, causes system-out messages");
        $I->wantTo ( "Get Groups faculty upload pdf details by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->sendGET ( 'pdf/groups/faculty?orgid='.$this->organization);
        $I->seeResponseCodeIs ( 204 );
    }
    
    public function testGetGroupStudentsUploadPdfDetails(ApiTester $I, $scenario)
    {
        $scenario->skip("Breaks sonar reporting, causes system-out messages");
        $I->wantTo ( "Get Groups Students upload pdf details by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->sendGET ( 'pdf/groups/students');
        $I->seeResponseCodeIs ( 204 );
    }
}