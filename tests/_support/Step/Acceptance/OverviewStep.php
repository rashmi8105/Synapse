<?php

namespace Step\Acceptance;

class OverviewStep extends \AcceptanceTester {

   /**
 * @Given user is on :arg1 page
 */
    public function iAmOnPage($arg1) {
        $this->VeriFyUserIsOnDesiredPage($arg1);
    }
    
    /**
     * @When user clicks on :arg1 tab
     */
    public function iClickOnTab($tabName) {
        $this->clickOnTabName($tabName);
    }    
    
    /**
     * @When user clicks on :arg1 button
     */
    public function iClickedOnButton($arg1) {
        $this->ClickOnButton($arg1);
    }

    /**
     * @When user clicks on account dropdown
     */
    public function iClickOnDropdown() {
        $this->ClickOnAccount();
    }

    /**
     * @When Click on :arg1 link
     */
    public function clickOnLink($arg1) {
        $this->ClickOnDropdownLink($arg1);
    } 
    
/**
 * @When user clicks on :arg1 link under additional setup
 */
 public function userClicksOnLinkUnderAdditionalSetup($arg1)
 { 
     $this->ClickOnLinkUnderAdditionalSetup($arg1);
  }
    
/**
 * @Then user should see comment for :arg1 activity
 */
 public function userShouldSeeCommentFor($ActivityType)
 { 
     $this->VerifyCommentForActivity($ActivityType);
 }
 /**
 * @When users click on help icon on overview page
 */
 public function usersClickOnHelpIconOnOverviewPage()
 {

     $this->ClickOnHelpIcon();
     
 }
 
  /**
     * @When user clicks on :arg1 subtab
     */
     public function userClicksOnSubtab($Subtab)
     {
         $this->clickOnSubTabName($Subtab);
     }

  
    ///////////////////////////// Implementations ////////////////////////////////////////////

      public function VeriFyUserIsOnDesiredPage($arg1) {
        $I = $this;
        $I->amOnPage($arg1);
        $I->WaitForPageToLoad($I);
    }

    public function clickOnTabName($tabname) {
        $I = $this;
        $I->wait(3);
        $I->waitForElement(str_replace("{{}}", $tabname,  $I->Element("TabName", "OverviewPage")));
         $I->ClickOnElementWithJS($I, str_replace("{{}}", $tabname,  $I->Element("TabName", "OverviewPage")));
        //$I->click(str_replace("{{}}", $tabname,  $I->Element("TabName", "OverviewPage")));
           $I->WaitForPageToLoad($I);
    }
      
     
    
    public function ClickOnButton($ButtonName) {
        $I = $this;
        
       $I->click(str_replace("{{}}", $ButtonName, $I->Element("WidgetsButtons", "OverviewPage")));
        $I->WaitForPageToLoad($I);
    }

    public function ClickOnAccount() {
        $I = $this;
        $I->click($I->Element("AccountDropdown", "OverviewPage"));
    }

    public function ClickOnDropdownLink($linkName) {
        $I = $this;
        $I->click(str_replace("{{}}", $linkName, $I->Element("Account", "OverviewPage")));
        $I->WaitForPageToLoad($I);
    }
     
    public function ClickOnLinkUnderAdditionalSetup($arg1)
    {
         $I=$this;
         $I->click(str_replace('{{}}',$arg1,$I->Element("AdditionalSetup","OverviewPage")));
         $I->WaitForPageToLoad($I);
    }
    
    public function VerifyCommentForActivity($ActivityType)
    {    $I=$this;
        if(strpos($ActivityType,"Referral")!==false)
        {
            if(strpos($ActivityType,"Notification")!==false)
            {
                $I->canSeeElement(str_replace("{{}}",$I->getDataFromJson(new NotificationStep($this->scenario),"NotificationReferralCommentDesc"),$I->Element("ActivityComment","OverviewPage")));
            }
            else 
            {
           $I->canSeeElement(str_replace("{{}}",$I->getDataFromJson(new AboutTheStudentStep($this->scenario),"ReferralCommentDesc"),$I->Element("ActivityComment","OverviewPage")));

            }
        }
      
       
        
    }
    
    public function ClickOnHelpIcon()
    {
        
        $I=  $this;
        $I->Click($I->Element("HelpIcon","OverviewPage"));
        $I->WaitForPageToLoad($I);
        
    }
    
      public function clickOnSubTabName($Subtab) {
        $I = $this;
        $I->waitForElement(str_replace("{{}}", $Subtab, $I->Element("SubTabName", "OverviewPage")),60); 
        $I->ClickOnElementWithJS($I,str_replace("{{}}", $Subtab, $I->Element("SubTabName", "OverviewPage")));
        $I->WaitForPageToLoad($I);
    }
    
    
    
}
