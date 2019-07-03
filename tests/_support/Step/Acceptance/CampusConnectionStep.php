<?php
namespace Step\Acceptance;

class CampusConnectionStep extends \AcceptanceTester
{ 
  /**
 * @When user sets :arg1 as Campus Connection
 */
 public function userSetsAsCampusConnection($FacultyName)
 {
  $this->SetFacuoltyAsCampusCoonection($FacultyName);
 }
 
 /**
 * @Then user is able to see :arg1, :arg2 and :arg3 on campus connection page
 */
 public function userSeeAndOnCmapusConnectionPage($Name, $Email,$Phone)
 { 
     $this->VerifyCampusConnectionDetails($Name, $Email,$Phone);
 }
    
 /**
 * @When user removes :arg1 as Campus Connection
 */
 public function userRemovesAsCampusConnection($Faculty_Name)
 {
$this->RemovePrimaryConnection();
        
 } 
 
 /**
 * @Then user is not able to see :arg1 as primary campus connection
 */
 public function userIsNotAbleToSeeAsPrimaryCampusConnection($FacultyName)
 {
   $this->PrimaryConnectionIsNotDisplayed($FacultyName);
  }
 
    //////////////////////////////////////////
public function  PrimaryConnectionIsNotDisplayed($FacultyName)
{ 
  $I=$this;
 $I->cantSeeElement(str_replace("{{}}",$FacultyName,$I->Element("PrimaryConnectionName","AboutTheStudentPage")));
    
}
  
  
 public function RemovePrimaryConnection()
 {      $I=$this;
        $I->click($I->Element("RemovePrimaryCampusConnection","AboutTheStudentPage"));
       
 }
 public function SetFacuoltyAsCampusCoonection($FacultyName)
 {    $I=$this;
      $I->wait(3);
      if($I->isElementDisplayed($I,$I->Element("PrimaryConnectionRemoveOption","AboutTheStudentPage"))) {
           
            $I->RemovePrimaryConnection();
            $I->SuccessMsgAppears($I);
            $I->SuccessMsgDisappears($I);
        } 
        $I->click($I->Element("setPrimaryCampusConnectionBtn","AboutTheStudentPage"));
        $I->WaitForModalWindowToAppear($I);
         $I->fillField($I->Element("NameInput","AboutTheStudentPage"),$FacultyName);
         $I->click(str_replace("{{}}",$FacultyName, $I->Element("NameSelect","AboutTheStudentPage")));
          $I->click($I->Element("saveBtn","AboutTheStudentPage"));
 }
    
 public function VerifyCampusConnectionDetails($Name, $Email,$Phone)
 {
     
     $I=$this;
     $I->wait(3);
     $I->canSeeElement(str_replace("{{}}",$Name,$I->Element("PrimaryConnectionName","AboutTheStudentPage")));
     $I->canSeeElement(str_replace("{{}}",$Email,$I->Element("PrimaryConnectionEmail","AboutTheStudentPage")));
     $I->canSeeElement(str_replace("<<>>",$Name,str_replace("{{}}",$Phone,$I->Element("PrimaryConnectionPhone","AboutTheStudentPage"))));

 }
 
    
}