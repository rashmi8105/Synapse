<?php
namespace Step\Acceptance;

class DashboardStep extends \AcceptanceTester
{
    
  /**
     * @Then user is able to see :arg1 module
     */
     public function userIsAbleToSeeModule($modulename)
     { 
         $this->VerifyPresenceOfModule($modulename);
     }

    /**
     * @Then user is not able to see :arg1 module
     */
     public function userIsNotAbleToSeeModule($modulename)
     { 
      $this->VerifyAbsenceOfModule($modulename);
     }


///////////////////////////////////////////////////////
 public function VerifyPresenceOfModule($modulename)
 {   $I=$this;
     $I->canSeeElement(str_replace("{{}}",$modulename, $I->Element("module","DashboardPage")));
     
 }
 public function VerifyAbsenceOfModule($modulename)
 {
     $I=$this;
 $I->cantSeeElement(str_replace("{{}}",$modulename, $I->Element("module","DashboardPage")));
     
 }
 
 
}