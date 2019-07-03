<?php

namespace Step\Acceptance;

class SettingsFeaturesStep extends \AcceptanceTester {
   /**
     * @When user selects radiobuttons for :arg1 and :arg1 under academic update panel
     */
     public function userSelectsRadiobuttonsForAndUnderAcademicUpdatePanel($sendToStudents,$ReferForAssitance)
     { 
         
         $this->SelectsAcademicUpdateOption($sendToStudents, $ReferForAssitance);
     }

    /**
     * @When user selects radiobuttons for :arg1,:arg1 and :arg1
     */
     public function userSelectsRadiobuttonsForAnd($SendGrade,$SendAbsence,$SendComment)
     { 
         $this->SelectsSendToStudentOptions($SendGrade, $SendAbsence, $SendComment);
     }
    
    
    /**
     * @Then user is able to view :arg1,:arg2,:arg3,:arg4 and :arg5 labels
     */
    public function userIsAbleToViewandLabels($Referral, $Notes, $LogContacts, $Appointments, $Email) {
        $this->VerifyLabelsUnderFeaturePanel($Referral, $Notes, $LogContacts, $Appointments, $Email);
    }

    /**
     * @When user selects radiobuttons for :arg1,:arg2,:arg3,:arg4 and :arg5 under Features Panel
     */
    public function userONFollowingFeaturesAndUnderFeaturesPanel($Referral_option, $Notes_option, $LogContacts_option, $Appointments_option, $Email_option) {
        $this->SelectRadioButtonInFrontOnFeatures($Referral_option, $Notes_option, $LogContacts_option, $Appointments_option, $Email_option);
    }

    /**
     * @Then user is able to see the following :arg1,:arg2,:arg3,:arg4 and :arg5 radio button as selected
     */
    public function userIsAbleToSeeTheFollowingAndRadioButtonAsSelected($Referral_option, $Notes_option, $LogContacts_option, $Appointments_option, $Email_option) {
        $this->VerifyRadioButtonIsChecked($Referral_option, $Notes_option, $LogContacts_option, $Appointments_option, $Email_option);
    }    
    
    /**
     * @When user selects :arg1,:arg2 and :arg3 under Referrals
     */
     public function userSelectsAndUnderReferrals($notification,$connection,$reason)
     {
         $this->SelectRadioButtonUnderReferrals($notification,$connection,$reason);
     }

    /**
     * @Then user is able to see the following :arg1,:arg2 and :arg3 radio button as selected under Referrals
     */
     public function userIsAbleToSeeTheFollowingAndRadioButtonAsSelectedUnderReferrals($notification, $connection, $reason)
     {
         $this->VerifyRadioButtonIsCheckedUnderReferrals($notification, $connection, $reason);
     }
    
     
     

    //////////////////////////////////////implementations///////////////

   
    
    public function SelectsAcademicUpdateOption($sendToStudents,$ReferForAssitance)
    {
     $I=$this;
      $I->waitForElement(str_replace("{{}}",$sendToStudents,$I->Element("SendAcademicUpdateToStudent","Settings_FeaturesPage")));     
      $I->click(str_replace("{{}}",$sendToStudents,$I->Element("SendAcademicUpdateToStudent","Settings_FeaturesPage")));
      $I->click(str_replace("{{}}",$ReferForAssitance,$I->Element("AcademicRefer","Settings_FeaturesPage")));  
        
    }
    
    public function SelectsSendToStudentOptions($SendGrade,$SendAbsence,$SendComment)
    {    $I=$this;
         $I->waitForElement(str_replace("{{}}",$SendGrade,$I->Element("sendProgressGrade","Settings_FeaturesPage")));     
        $I->click(str_replace("{{}}",$SendGrade,$I->Element("sendProgressGrade","Settings_FeaturesPage"))); 
        $I->click(str_replace("{{}}",$SendComment,$I->Element("sendComment","Settings_FeaturesPage"))); 
        $I->click(str_replace("{{}}",$SendAbsence,$I->Element("sendAbsence","Settings_FeaturesPage"))); 
 
        
    }
    
    
     public function VerifyLabelsUnderFeaturePanel($Referral, $Notes, $LogContacts, $Appointments, $Email) {
        $I = $this;
        $I->canSeeElement(str_replace("{{}}", $Referral, $I->Element("LabelName", "Settings_FeaturesPage")));
        $I->canSeeElement(str_replace("{{}}", $Notes, $I->Element("LabelName", "Settings_FeaturesPage")));
        $I->canSeeElement(str_replace("{{}}", $LogContacts, $I->Element("LabelName", "Settings_FeaturesPage")));
        $I->canSeeElement(str_replace("{{}}", $Appointments, $I->Element("LabelName", "Settings_FeaturesPage")));
        $I->canSeeElement(str_replace("{{}}", $Email, $I->Element("LabelName", "Settings_FeaturesPage")));
    }

    public function SelectRadioButtonInFrontOnFeatures($Referral_option, $Notes_option, $LogContacts_option, $Appointments_option, $Email_option) {
        $I = $this;
        $I->click(str_replace("{{}}", $Referral_option, $I->Element("LabelOptionValue", "Settings_FeaturesPage")));
        $I->click(str_replace("{{}}", $Notes_option, $I->Element("LabelOptionValue", "Settings_FeaturesPage")));
        $I->click(str_replace("{{}}", $LogContacts_option, $I->Element("LabelOptionValue", "Settings_FeaturesPage")));
        $I->click(str_replace("{{}}", $Appointments_option, $I->Element("LabelOptionValue", "Settings_FeaturesPage")));
        $I->click(str_replace("{{}}", $Email_option, $I->Element("LabelOptionValue", "Settings_FeaturesPage")));
    }

    public function VerifyRadioButtonIsChecked($Referral_option, $Notes_option, $LogContacts_option, $Appointments_option, $Email_option) {
        $I = $this;
        $I->click(str_replace("{{}}", "Features", $I->Element("ExpandIcon", "Settings_FeaturesPage")));
        $I->wait(2);
        $I->canSeeOptionIsSelected(str_replace("{{}}", $Referral_option, $I->Element("LabelOptionValue", "Settings_FeaturesPage")), explode("-", $Referral_option)[1]);
        $I->canSeeOptionIsSelected(str_replace("{{}}", $Notes_option, $I->Element("LabelOptionValue", "Settings_FeaturesPage")), explode("-", $Notes_option)[1]);
        $I->canSeeOptionIsSelected(str_replace("{{}}", $LogContacts_option, $I->Element("LabelOptionValue", "Settings_FeaturesPage")), explode("-", $LogContacts_option)[1]);
        $I->canSeeOptionIsSelected(str_replace("{{}}", $Appointments_option, $I->Element("LabelOptionValue", "Settings_FeaturesPage")), explode("-", $Appointments_option)[1]);
        $I->canSeeOptionIsSelected(str_replace("{{}}", $Email_option, $I->Element("LabelOptionValue", "Settings_FeaturesPage")), explode("-", $Email_option)[1]);
    }
    
    public function SelectRadioButtonUnderReferrals($notification,$connection,$reason) {
        $I = $this;
        $I->click(str_replace("{{}}", $notification, $I->Element("LabelOptionValue", "Settings_FeaturesPage")));
        $I->click(str_replace("{{}}", $connection, $I->Element("LabelOptionValue", "Settings_FeaturesPage")));
        $I->click(str_replace("{{}}", $reason, $I->Element("LabelOptionValue", "Settings_FeaturesPage")));
        
    }

    public function VerifyRadioButtonIsCheckedUnderReferrals($notification,$connection,$reason) {
        $I = $this;
        $I->canSeeOptionIsSelected(str_replace("{{}}", $notification, $I->Element("LabelOptionValue", "Settings_FeaturesPage")), explode("-", $notification)[1]);
        $I->canSeeOptionIsSelected(str_replace("{{}}", $connection, $I->Element("LabelOptionValue", "Settings_FeaturesPage")), explode("-", $connection)[2]);
        $I->canSeeOptionIsSelected(str_replace("{{}}", $reason, $I->Element("LabelOptionValue", "Settings_FeaturesPage")), explode("-", $reason)[1]);
       
    }

}
