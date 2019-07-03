<?php
namespace Step\Api;

class staticList extends \ApiTester
{
 /**
     * @Given I am on Static Page
     */
     public function iAmOnStaticPage()
     {
         $this->UserOnStaticPage();
     }

    /**
     * @When I create a Static List
     */
     public function iCreateAStaticList()
     {
        $this->UserCreateStaticList();
     }

    /**
     * @Then I see static List in the list
     */
     public function iSeeStaticListInTheList()
     {
        $this->seeinresponse();
     }

     public function UserOnStaticPage(){
         $I=$this;
         $I->sendGET('api/v1/staticlists?org_id=215');
        
     }
     
     public function UserCreateStaticList(){
         $I=$this;
         $I->sendPOST('api/v1/staticlists',["org_id"=>"215","staticlist_name"=>"Static_List","staticlist_description"=>"jggk"]);
        
     }
     
     public function seeinresponse(){
         $I=$this;
         $I->canSeeResponseContains('"staticlist_name":"Static_List"');
     }
}