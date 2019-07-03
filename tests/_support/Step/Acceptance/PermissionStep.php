<?php

namespace Step\Acceptance;

class PermissionStep extends \AcceptanceTester {

       /**
     * @When user clicks on :arg1 link on feature permission window
     */
     public function userClicksOnLinkOnFeaturePermissionWindow($Feature)
     { 
         $this->ChooseFeatures($Feature);
     }  
      /**                                                                                                                                                                                  
     * @When user chooses :arg1 permission for Direct Referral with sharing option :arg2                                                                                                 
     */                                                                                                                                                                                  
     public function userChoosesPermissionForDirectReferralWithSharingOption($CreateOrView, $SharingOption)                                                                                               
     {   
         $this->ChosseDirectReferralPermission($SharingOption, $CreateOrView);
     } 
   
    
    
    /**
     * @When user clicks on add permission template button on the page
     */
    public function iClickedOnAddPermissionTemplateButtonOnThePage() {
        $this->clickOnAddPermissionBtn();
    }

    /**
     * @When user fills :arg1 as permission template name
     */
    public function userFillsAsPermissionTemplateName($PermissionName) {
        $this->FillPermissionName($PermissionName);
    }

    /**
     * @When user clicks on cancel button displayed on permission template modal win
     */
    public function userClicksOnCancelButtonDisplayedOnPermissionTemplateModalWin() {
        $this->UserClickNevermind();
    }

    /**
     * @When user clicks on save Permission button displayed on permission template modal win
     */
    public function userClicksOnSavePermissionButtonDisplayedOnPermissionTemplateModalWin() {
        $this->UserclicksOnSaveBtn();
    }

    /**
     * @Then user is not able to see the permission template in the application
     */
    public function userIsNotAbleToSeeThePermissionTemplateInTheApplication() {
        $this->VerifyAbsensePermission();
    }

    /**
     * @Then user is able to see permission template in the application
     */
    public function userIsAbleToSeePermissionTemplateInTheApplication() {

        $this->IsPermissionDisplayed($this->getDataFromJson($this,"PermissionName"));
    }

    /**
     * @When user edits the already created permission template :arg1 with Name :arg2
     */
    public function userEditsTheAlreadyCreatedPermissionTemplateWithName($arg1, $arg2) {

                
        $this->IsPermissionDisplayed($this->getDataFromJson($this,"PermissionName"));
        $this->ClickEditIcon($arg1);
        $this->FillPermissionName($arg2);
        
    }  
    
     /**
  * @When user clicks on edit icon of :arg1 permission template
  */
  public function userClicksOnEditIconOfPermissionTemplate($PermissionSet)
  { 
        $this->IsPermissionDisplayed($this->getDataFromJson($this,"PermissionName"));
        $this->ClickEditIcon($PermissionSet);
  }
    
    
     /**
     * @When user clicks on :arg1 link on permission modal window
     */
     public function userClicksOnLinkOnPermissionModalWindow($parameter)
     {
         $this->ClickOnPermissionParameterLinkOnPermissionModalWindow($parameter);
     }

    /**
     * @When user chooses :arg1 permission
     */
     public function userChoosesPermission($permission)
     { 
         $this->SelectPermission($permission);
     }

   

    ///////////////////////implementation/////////////////////////////// 
  
    
    
    
   public function VerifyAbsensePermission()
   {
        $I = $this;
        $I->reloadPage();
        $I->WaitForPageToLoad($I);
        $count = $I->grabTextFrom($I->Element("Count", "PermissionPage"));
        $pagesCount = ceil($count / 10);
        if($pagesCount==0)
        {
         $I->cantSee($I->getDataFromJson($this,"PermissionName"));   
        }
        else{
        for ($page = 1; $page <= $pagesCount; $page++) {
           
            if ($page > 1) { // To click on the paginations
                $I->click(str_replace("{{}}", $page, $I->Element("PaginationPage", "PermissionPage")));
            }
             $I->cantSee($I->getDataFromJson($this,"PermissionName"));
           
        }  
           
        }
        
    }
       
       
   
    public function clickOnAddPermissionBtn() {
        $I = $this;
        $I->click($I->Element("addPermissionBtn", "PermissionPage"));
    }

    public function UserClickNevermind() {
        $I = $this;
        $I->click($I->Element("CancelBtn", "PermissionPage"));
        $I->WaitForModalWindowToDisappear($I);
    }

    public function FillPermissionName($PermissionName) {
        $I = $this;
        $Name=$PermissionName.rand(0, 1008080);
        $I->writeDataInJson($this, "PermissionName", $Name);
        $I->fillField($I->Element("permissionTextbox", "PermissionPage"), $Name);
    }

    public function UserclicksOnSaveBtn() {
        $I = $this;
        $I->click($I->Element("SaveBtn", "PermissionPage"));
    }

    public function PermissionNotDisplayed() {
       // $I = $this;
        //$I->dontSee($initialCount + 1, \PermissionSetPage::$Count);
    }

    public function ClickEditIcon($PermissionSet) {
        $I = $this;
        $permissionName=$I->getDataFromJson($this, "PermissionName");
        $I->click(str_replace("{{}}", $permissionName, $I->Element("editIcon", "PermissionPage")));
        $I->WaitForModalWindowToAppear($I);
    }

  public function SelectPermission($permission)
{
        $I = $this;
        if (strpos($permission, "Select all") !== false) {
            $I->waitForElement(str_replace("{{}}", $permission, $I->Element("permissionCheckBox", "PermissionPage")),60);
            $I->click(str_replace("{{}}", $permission, $I->Element("permissionCheckBox", "PermissionPage")));
        } else {
            if ($I->isElementDisplayed($I, $I->Element("isSelectAllOptionSelected", "PermissionPage"))) {
                $I->waitForElement(str_replace("{{}}", "Select all", $I->Element("permissionCheckBox", "PermissionPage")),60);
                $I->click(str_replace("{{}}", "Select all", $I->Element("permissionCheckBox", "PermissionPage")));
            }
            $I->waitForElement(str_replace("{{}}", $permission, $I->Element("permissionCheckBox", "PermissionPage")),60);
            $I->click(str_replace("{{}}", $permission, $I->Element("permissionCheckBox", "PermissionPage")));
        }
    }
  public function ChooseFeatures($Feature)
  {
      $I=$this;
      $I->waitForElement(str_replace("{{}}",$Feature,$I->Element("featureLinkOnPermissionWindow","PermissionPage")),60);
      $I->click(str_replace("{{}}",$Feature,$I->Element("featureLinkOnPermissionWindow","PermissionPage")));
      
  }
   
public function ChosseDirectReferralPermission($SharingOption,$CreateOrView)
{
    $I=$this;
    $I->waitForElement(str_replace("<<>>",$SharingOption,str_replace("{{}}",$CreateOrView,$I->Element("DirectReferral","PermissionPage"))),60);
    $I->click(str_replace("<<>>",$SharingOption,str_replace("{{}}",$CreateOrView,$I->Element("DirectReferral","PermissionPage"))));
}
  
public function ChosseReasonReferralPermission($SharingOption,$CreateOrView)
{
    $I=$this;
    $I->waitForElement(str_replace("<<>>",$SharingOption,str_replace("{{}}",$CreateOrView,$I->Element("ReferralReasonRouting","PermissionPage"))),60);
    $I->click(str_replace("<<>>",$SharingOption,str_replace("{{}}",$CreateOrView,$I->Element("ReferralReasonRouting","PermissionPage"))));
}
  


   
    public function ClickOnPermissionParameterLinkOnPermissionModalWindow($parameter)
    {
        
        $I=$this;
        $I->waitForElement(str_replace("{{}}",$parameter,$I->Element("permissionParameter","PermissionPage")),60);
        $I->click(str_replace("{{}}",$parameter,$I->Element("permissionParameter","PermissionPage")));
        
        
    }
    
    
    
    
    public function IsPermissionDisplayed($permissionNameData) {
        $I = $this;
          
       $count = $I->grabTextFrom($I->Element("Count", "PermissionPage"));
               $pagesCount = ceil($count / 10);
        for ($page = 1; $page <= $pagesCount; $page++) {
            if ($page > 1) { // To click on the paginations
                $I->click(str_replace("{{}}", $page, $I->Element("PaginationPage", "PermissionPage")));
            }
            for ($row = 1; $row <= 10; $row++) {//To grab the text of each row containing Permission Set on the page.
                $permissionName = $I->grabTextFrom('//table//tbody//tr[' . $row . ']//td[1]//span[@class="permission-temp-name ng-binding"]');
                codecept_debug($permissionName);
                codecept_debug($permissionNameData);
                if ($permissionName == $permissionNameData) {
                    $I->canSee($permissionNameData, '//table//tbody//tr[' . $row . ']//td[1]');
                    return;
                }
            }
        }
    }

}
