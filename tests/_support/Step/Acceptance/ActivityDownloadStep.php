<?php
namespace Step\Acceptance;

class ActivityDownloadStep extends \AcceptanceTester
{

    /**
     * @Then user is able to view created :arg1 on the page
     */
     public function userIsAbleToViewCreatedOnThePage($FeatureName)
     {
         $this->ViewCreatedModule($FeatureName);
     }

    /**
     * @When user clicks on view link in front of created :arg1
     */
     public function userClicksOnViewLinkInFrontOfCreated($FeatureName)
     {
         $this->UserClicksOnViewLink($FeatureName);  
     }

    /**
     * @Then user is able to view the :arg1 on the modal window
     */
     public function userIsAbleToViewTheOnTheModalWindow($FeatureName)
     {
         $this->VerifyDescOnModalWin($FeatureName);
     }
     
     ///implemantation///////////
     
     public function ViewCreatedModule($FeatureName){
         $I=$this;
         if(strpos($FeatureName, "Referral")!==false){
             $commentDesc=$I->getDataFromJson($this, "ReferralCommentDesc");
         }
         elseif(strpos($FeatureName, "Note")!==false){
             $commentDesc=$I->getDataFromJson($this, "NoteCommentDesc");
         }
         elseif(strpos($FeatureName, "Appointment")!==false){
             $commentDesc=$I->getDataFromJson($this, "AppointmentCommentDesc");
         }
         else{
             $commentDesc=$I->getDataFromJson($this, "ContactCommentDesc");
         }
        // $I->canSeeElement(str_replace("{{}}", $commentDesc, $I->Element("FeatureDesc", "ActivityDownloadPage")));
         
         $I->assertTrue($I->isElementDisplayed($I, str_replace("{{}}", $commentDesc, $I->Element("FeatureDesc", "ActivityDownloadPage"))));
     }
     
     public function UserClicksOnViewLink($FeatureName){
         $I=$this;
         if(strpos($FeatureName, "Referral")!==false){
             $commentDesc=$I->getDataFromJson($this, "ReferralCommentDesc");
         }
         elseif(strpos($FeatureName, "Note")!==false){
             $commentDesc=$I->getDataFromJson($this, "NoteCommentDesc");
         }
         elseif(strpos($FeatureName, "Appointment")!==false){
             $commentDesc=$I->getDataFromJson($this, "AppointmentCommentDesc");
         }
         else{
             $commentDesc=$I->getDataFromJson($this, "ContactCommentDesc");
         }
         $I->ClickOnElementWithJS($I,str_replace("{{}}",$commentDesc ,$I->Element("ViewLink", "ActivityDownloadPage")));
         $I->WaitForModalWindowToAppear($I);
     }
     
     public function VerifyDescOnModalWin($FeatureName){
         $I=$this;
         if(strpos($FeatureName, "Referral")!==false){
             $commentDesc=$I->getDataFromJson($this, "ReferralCommentDesc");
         }
         elseif(strpos($FeatureName, "Note")!==false){
             $commentDesc=$I->getDataFromJson($this, "NoteCommentDesc");
         }
         elseif(strpos($FeatureName, "Appointment")!==false){
             $commentDesc=$I->getDataFromJson($this, "AppointmentCommentDesc");
         }
         else{
             $commentDesc=$I->getDataFromJson($this, "ContactCommentDesc");
         }
         $I->canSeeElement(str_replace("{{}}", $commentDesc,$I->Element("DescOnWin", "ActivityDownloadPage")));
         $I->click($I->Element("ClosePopUpWindow", "ActivityDownloadPage"));
         $I->WaitForModalWindowToDisappear($I); 
     }

}