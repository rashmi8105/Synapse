<?php
namespace Step\Acceptance;

class InstituteSettingsStep extends \AcceptanceTester
{
/**
     * @When user is able to expand :arg1 panel
     */
     public function userIsAbleToExpandPanel($PanelName)
     {
        $this->clickOnExpandIcon($PanelName);
     }
     
     /**
     * @Then user is able to see expanded :arg1 panel
     */
     public function userIsAbleToSeeExpandedPanel($PanelName)
     {
         $this->ExpandedPanelIsDisplayed($PanelName);
     }

     /**
     * @When click on save button on settings page
     */
     public function clickOnSaveButton()
     {
         $this->ClickSaveButton();
     }


     
     public function clickOnExpandIcon($PanelName){
         $I=$this;
         $I->click(str_replace("{{}}", $PanelName,$I->Element("ExpandIcon", "InstituteSettingsPage")));
         $I->wait(2);//due to latency 
     }
     
     public function ExpandedPanelIsDisplayed($PanelName){
         $I=$this;
         $I->canSeeElement(str_replace("{{}}", $PanelName,$I->Element("CollapseIcon", "InstituteSettingsPage")));
     }

     public function ClickSaveButton(){
         $I=$this;
         $I->click($I->Element("saveBtn", "InstituteSettingsPage"));
     }
     
     public function ClickOnCancelButton(){
         $I=$this;
         $I->click($I->Element("CancelBtn", "InstituteSettingsPage"));
     }
     
     
     }
