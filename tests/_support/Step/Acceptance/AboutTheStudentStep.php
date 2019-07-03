<?php

namespace Step\Acceptance;

class AboutTheStudentStep extends \AcceptanceTester {
   
  /**
     * @Then user is able to navigate to the :arg1 profile page
     */
    public function userIsAbleToNavigateToTheProfilePage($StdName) {
        $this->UserNavigatesToProfilePage($StdName);
    }    
    

    /**
     * @Then user is able to student specific data :arg1, :arg2, :arg3, :arg4 and :arg4
     */
    public function userIsAbleToStudentSpecificDataAnd($StdName, $ID, $Email, $phNum, $mobNum) {
  
        $this->VerifyStudentDetails($StdName, $ID, $Email, $phNum, $mobNum);
    }

    /**
     * @Then user is not able to see :arg1, :arg2 and :arg3 panel
     */
    public function userIsNotAbleToSeeAndPanel($referral, $appointment, $contact) {
        $this->VerifyPanelAreNotDisplaying($referral, $appointment, $contact);
    }

    /**
     * @Then user is able to see :arg1, :arg2 and :arg3 panel
     */
    public function userIsAbleToSeeAndPanel($referral, $appointment, $contact) {
        $this->VerifyPanelAreDisplaying($referral, $appointment, $contact);
    }

    /**
     * @When user clicks on :arg1 tab on student page
     */
    public function userClicksOnTabOnStudentPage($tabName) {
        $this->ClickonTab($tabName);
    }

    /**
     * @When user clicks on Add New Activity link
     */
    public function userClicksOnAddNewActivityLink() {
        $this->clickonActivityLink();
    }

    /**
     * @When user clicks on :arg1 tab on window
     */
    public function userClicksOnTabOnWindow($featureName) {
        $this->ClickonTabDisplayingonWin($featureName);
    }

    /**
     * @When user select following fields :arg1, :arg2,:arg3 and :arg4
     */
    public function userSelectFollowingFieldsAnd($reason, $contactType, $contactDate, $Desc) {
        $this->userfillsthedata($reason, $contactType, $contactDate, $Desc);
    }

    /**
     * @When user select :arg1 details checkbox
     */
    public function userSelectDetailsCheckbox($option) {
        $this->SelectDetailsCheckbox($option);
    }

    /**
     * @When user select :arg1 sharing option for :arg2
     */
    public function userSelectSharingOptionFor($option, $teamname) {
        $this->SelectSharingOptions($option, $teamname);
    }

    /**
     * @When clicks on Create a contact a button
     */
    public function clicksOnCreateAContactAButton() {
        $this->ClickOnCreateContactBtn();
    }

    /**
     * @Then user should be able to see the created :arg1 with :arg2 in the list
     */
    public function userShouldBeAbleToSeeTheCreatedWithInTheList($Contact, $Desc) {
        $this->UserisAbleToSeeTheContact($Contact, $Desc);
    }

    /**
     * @When user select and fill reason from :arg1 dropdown and :arg2 in field
     */
    public function userSelectAndFillReasonFromDropdownAndInField($reason, $Desc) {
        $this->FillNotesData($reason, $Desc);
    }

    /**
     * @When clicks on Create a Note button
     */
    public function clicksOnCreateANoteButton() {
        $this->ClickOnCreateNoteBtn();
    }

    /**
     * @When user select and fills following fields :arg1,:arg2,:arg3 and :arg4 in field
     */
    public function userSelectAndFillsFollowingFieldsAndInField($reason, $assignto, $Interested, $Desc) {
        $this->FillReferralData($reason, $assignto, $Interested, $Desc);
    }

    /**
     * @When clicks on Create a Referral button
     */
    public function clicksOnCreateAReferralButton() {
        $this->ClickOnCreateReferralBtn();
    }

    /**
     * @When user select and fills following fields :arg1,:arg2,:arg2,:arg4 and :arg5 in the field
     */
    public function userSelectAndFillsFollowingFieldsAndInTheField($reason, $start_date, $end_date, $location, $desc) {
        $this->fillDataForAppointments($reason, $start_date, $end_date, $location, $desc);
    }
    
    
    /**
 * @When user clicks on :arg1 link under student details tab
 */
 public function userClicksOnLinkUnderStudentDetailsTab($link)
 {  
     $this->clickOnLinkUnderdetailsTab($link);
           
 }
 /**
     * @Then Student see year dependent Profile :arg1 with value :arg2
     */
     public function studentSeeYearDependentProfileWithValue($Profile,$value)
     { 
         $this->VerifyPreseneOfProfile($Profile, $value);
     }
      /**
     * @Then Student see term dependent Profile :arg1 with value :arg2
     */
     public function studentSeeTermDependentProfileWithValue($Profile,$value)
     {
      $this->VerifyPreseneOfProfile($Profile, $value);
     }
     
      /**
     * @Then Student see term dependent ISP :arg1 with value :arg2
     */
     public function studentSeeTermDependentISPWithValue($Profile,$value)
     {
                  $this->VerifyPreseneOfProfile($Profile, $value);
     }
     
     /**
     * @Then Student see year dependent ISP :arg1 with value :arg2
     */
     public function studentSeeYearDependentISPWithValue($Profile,$value)
     {
                  $this->VerifyPreseneOfProfile($Profile, $value);
     }
 
    /**
     * @Then user is able to see :arg1 on student profile page
     */
    public function userIsAbleToSeeOnStudentProfilePage($TypeOfGroup) {
        $this->VerifyGroupOnStudentProfilepage($TypeOfGroup);
    }
    
    /**
     * @Then user is able to view INACTIVE status on the page
     */
    public function userIsAbleToViewINACTIVEStatusOnThePage() {
        $this->VerifyUserIsInActive();
    }
    
    /**
     * @Then user is not able to view INACTIVE status on the page
     */
     public function userIsNotAbleToViewINACTIVEStatusOnThePage()
     {
         $this->VerifyUserIsActive();
     }

    //////////////////implementation//////////////////// 
 
    public function VerifyGroupOnStudentProfilepage($TypeOfGroup) {
        $I = $this;
        if (strpos($TypeOfGroup, "Edited") !== false) {
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson(new GroupStep($this->getScenario()), "EditedGroupName"), $I->Element("GroupNameOnDeatilsPage", "AboutTheStudentPage")));
        }
        if (strpos($TypeOfGroup, "Created") !== false) {
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson(new GroupStep($this->getScenario()), "GroupName"), $I->Element("GroupNameOnDeatilsPage", "AboutTheStudentPage")));
        }
    }
    
     
public function VerifyPreseneOfProfile($Profile,$value)
{
    $I=$this;
    $I->wait(2);
    $I->canSeeElement(str_replace("<<>>",$Profile, str_replace("{{}}",$value,$I->Element("ProfileTableText","AboutTheStudentPage"))));
    
    
}
     
    public function UserNavigatesToProfilePage($StdName) {
        $I = $this;
        $I->canSeeInCurrentUrl("studentprofile"); 
        if(strpos($StdName,"Uploaded")!==false)
        {
            $StdName=$I->ReadFromJson("ISPData","FirstName");
        }
        $I->canSeeElement(str_replace("{{}}", $StdName, $I->Element("DashboardBreadcrumb", "AboutTheStudentPage")));
    }

    public function VerifyStudentDetails($StdName, $ID, $Email, $phNum, $mobNum) {
        $I = $this;
        $I->canSeeElement(str_replace("{{}}", $StdName, $I->Element("StudentInfo", "AboutTheStudentPage")));
        $I->canSeeElement(str_replace("{{}}", $ID, $I->Element("StudentInfo", "AboutTheStudentPage")));
        $I->canSeeElement(str_replace("{{}}", $Email, $I->Element("EmailInfo", "AboutTheStudentPage")));
        $I->canSeeElement(str_replace("{{}}", $phNum, $I->Element("MobileInfo", "AboutTheStudentPage")));
        $I->canSeeElement(str_replace("{{}}", $mobNum, $I->Element("MobileInfo", "AboutTheStudentPage")));
    }

    public function VerifyPanelAreDisplaying($referral, $appointment, $contact) {
        $I = $this;
        $I->canSeeElement(str_replace("{{}}", $referral, $I->Element("dashboardContainer", "AboutTheStudentPage")));
        $I->canSeeElement(str_replace("{{}}", $appointment, $I->Element("dashboardContainer", "AboutTheStudentPage")));
        $I->canSeeElement(str_replace("{{}}", $contact, $I->Element("dashboardContainer", "AboutTheStudentPage")));
    }

    public function VerifyPanelAreNotDisplaying($referral, $appointment, $contact) {
        $I = $this;
        $I->cantSeeElement(str_replace("{{}}", $referral, $I->Element("dashboardContainer", "AboutTheStudentPage")));
        $I->cantSeeElement(str_replace("{{}}", $appointment, $I->Element("dashboardContainer", "AboutTheStudentPage")));
        $I->cantSeeElement(str_replace("{{}}", $contact, $I->Element("dashboardContainer", "AboutTheStudentPage")));
    }

    public function ClickonTab($tabName) {
        $I = $this;
        $I->click(str_replace("{{}}", $tabName, $I->Element("Tab", "AboutTheStudentPage")));
        
    }

    public function clickonActivityLink() {
        $I = $this;
        $I->waitForElementVisible($I->Element("addnewActivity", "AboutTheStudentPage"), 60);
      $I->click($I->Element("addnewActivity", "AboutTheStudentPage"));
        $I->WaitForModalWindowToAppear($I);
    }

    public function ClickonTabDisplayingonWin($featureName) {
        $I = $this;
        $I->click(str_replace("{{}}", $featureName, $I->Element("WinPanel", "AboutTheStudentPage")));
        $I->WaitForPageToLoad($I);
    }

    public function userfillsthedata($reason, $contactType, $contactDate, $Desc) {
        $I = $this;
        $I->click($I->Element("ReasonButton", "AboutTheStudentPage"));
        $I->waitForElementVisible(str_replace("{{}}", $reason, $I->Element("SelectReason", "AboutTheStudentPage")), 20);
        $I->click(str_replace("{{}}", $reason, $I->Element("SelectReason", "AboutTheStudentPage")));
        $I->click($I->Element("ContactTypeButton", "AboutTheStudentPage"));
        $I->waitForElementVisible(str_replace("{{}}", $contactType, $I->Element("SelectReason", "AboutTheStudentPage")), 20);
        $I->click(str_replace("{{}}", $contactType, $I->Element("SelectReason", "AboutTheStudentPage")));
        $I->click($I->Element("dateOfContact", "AboutTheStudentPage"));
        if ($contactDate == "current") {
            $date = $I->GetCurrentDate($I);
        }
        $I->waitForElementVisible(str_replace("{{}}", $date, $I->Element("selectDate", "AboutTheStudentPage")), 10);
        $I->click(str_replace("{{}}", $date, $I->Element("selectDate", "AboutTheStudentPage")));
        $Desc1 = $Desc . round(microtime(true) * 1000); 
        $I->writeDataInJson($this, "ContactCommentDesc", $Desc1);
        $Description = $I->getDataFromJson($this, "ContactCommentDesc");
        $I->fillField($I->Element("Description", "AboutTheStudentPage"), $Description);
    }

    public function SelectDetailsCheckbox($option) {
        $I = $this;
        if($option!="None"){
        $I->click(str_replace("{{}}", $option, $I->Element("DetailsCheckbox", "AboutTheStudentPage")));
    }    
        
    }

    public function SelectSharingOptions($option, $teamname) {
        $I = $this;
        $I->click(str_replace("{{}}", $option, $I->Element("SharingOptions", "AboutTheStudentPage")));
        if ($option == "Team") {
            $I->waitForElementVisible(str_replace("{{}}", $teamname, $I->Element("TeamName", "AboutTheStudentPage")), 10);
            $I->click(str_replace("{{}}", $teamname, $I->Element("TeamName", "AboutTheStudentPage")));
        }
    }

    public function ClickOnCreateContactBtn() {
        $I = $this;
        $I->click($I->Element("CreateContactBtn", "AboutTheStudentPage"));
    }

    public function ClickOnCreateNoteBtn() {
        $I = $this;
        $I->click($I->Element("CreateNoteBtn", "AboutTheStudentPage"));
    }

    public function ClickOnCreateReferralBtn() {
        $I = $this;
        $I->click($I->Element("CreateReferralBtn", "AboutTheStudentPage"));
    }

    public function UserisAbleToSeeTheContact($Contact, $Desc) {
        $I = $this;
        $I->click($I->Element("ActivityDropDown", "AboutTheStudentPage"));
        $I->wait(2);
        $I->waitForElementVisible(str_replace("{{}}", $Contact, $I->Element("ActivityDropDown", "AboutTheStudentPage")), 20);
        $I->click(str_replace("{{}}", $Contact, $I->Element("SelectDropDownValues", "AboutTheStudentPage")));
        $I->WaitForPageToLoad($I);
        $I->click($I->Element("clickSeeAll", "AboutTheStudentPage"));
        if (strpos($Desc, "Added contact") !== false) {
            $addedDesc = $I->getDataFromJson($this, "ContactCommentDesc");
            $I->canSeeElement(str_replace("{{}}", $addedDesc, $I->Element("SeeCreatedActivity", "AboutTheStudentPage")));
        } elseif (strpos($Desc, "Added note") !== false) {
            $addedDesc = $I->getDataFromJson($this, "NoteCommentDesc");
            $I->canSeeElement(str_replace("{{}}", $addedDesc, $I->Element("SeeCreatedActivity", "AboutTheStudentPage")));
        } elseif (strpos($Desc, "Added referral") !== false) {
            if(strpos($Desc,"Notification")){
                $addedDesc=$I->getDataFromJson($this, "NotificationReferralCommentDesc"); 
                
            }else
            {
                     $addedDesc = $I->getDataFromJson($this, "ReferralCommentDesc"); 
            }
      
            $I->canSeeElement(str_replace("{{}}", $addedDesc, $I->Element("SeeCreatedActivity", "AboutTheStudentPage")));
        }
        elseif (strpos($Desc, "Added appointment") !== false) {
            $addedDesc = $I->getDataFromJson($this, "AppointmentCommentDesc");
            $I->canSeeElement(str_replace("{{}}", $addedDesc, $I->Element("SeeCreatedActivity", "AboutTheStudentPage")));
        }
    }

    public function FillNotesData($reason, $Desc) {
        $I = $this;
        $I->click($I->Element("ReasonButton", "AboutTheStudentPage"));
        $I->waitForElementVisible(str_replace("{{}}", $reason, $I->Element("SelectReason", "AboutTheStudentPage")), 20);
        $I->click(str_replace("{{}}", $reason, $I->Element("SelectReason", "AboutTheStudentPage")));
        $Desc1 = $Desc . round(microtime(true) * 1000);
        $I->writeDataInJson($this, "NoteCommentDesc", $Desc1);
        $Description = $I->getDataFromJson($this, "NoteCommentDesc");
        $I->fillField($I->Element("Description", "AboutTheStudentPage"), $Description);
    }

    public function FillReferralData($reason, $assignto, $Interested, $Desc) {
        $I = $this;
        $I->click($I->Element("ReasonButton", "AboutTheStudentPage"));
        $I->waitForElementVisible(str_replace("{{}}", $reason, $I->Element("SelectReason", "AboutTheStudentPage")), 20);
        $I->click(str_replace("{{}}", $reason, $I->Element("SelectReason", "AboutTheStudentPage")));
        $I->click($I->Element("AssignedTo", "AboutTheStudentPage"));
        if (strpos($assignto, "CampusResource") !== false) {
            $assignto = $I->getDataFromJson(new CampusResoucesStep($I->getScenario()), "CampusResourceName");
            $I->waitForElementVisible(str_replace("{{}}", $assignto, $I->Element("SelectAssignTo", "AboutTheStudentPage")), 20);
            $I->click(str_replace("{{}}", $assignto, $I->Element("SelectAssignTo", "AboutTheStudentPage")));
        } elseif (strpos($assignto, "StaffForReferralAssigneeBehaviors") !== false) {
            $assignto = $I->getDataFromJson(new CampusResoucesStep($I->getScenario()), "staffNameField");
            $I->waitForElementVisible(str_replace("{{}}", $assignto, $I->Element("SelectAssignTo", "AboutTheStudentPage")), 20);
            $I->click(str_replace("{{}}", $assignto, $I->Element("SelectAssignTo", "AboutTheStudentPage")));
        } else {
            $I->waitForElementVisible(str_replace("{{}}", $assignto, $I->Element("SelectAssignTo", "AboutTheStudentPage")), 20);
            $I->click(str_replace("{{}}", $assignto, $I->Element("SelectAssignTo", "AboutTheStudentPage")));
        }
        if ($Interested != "None") {
            $I->click($I->Element("InterestedParty", "AboutTheStudentPage"));
            $I->waitForElementVisible(str_replace("{{}}", $Interested, $I->Element("SelectInterestedParty", "AboutTheStudentPage")), 20);
            $I->click(str_replace("{{}}", $Interested, $I->Element("SelectInterestedParty", "AboutTheStudentPage")));
        }
        $Desc1 = $Desc . round(microtime(true) * 1000);
        if (strpos($Desc, "Notification") !== FALSE) {
            $I->writeDataInJson($this, "NotificationReferralCommentDesc", $Desc1);
            $Description = $I->getDataFromJson($this, "NotificationReferralCommentDesc");
        } else {
            $I->writeDataInJson($this, "ReferralCommentDesc", $Desc1);
            $Description = $I->getDataFromJson($this, "ReferralCommentDesc");
        }
        $I->fillField($I->Element("Description", "AboutTheStudentPage"), $Description);
    }

    public function fillDataForAppointments($reason, $start_date, $end_date, $location, $Desc) {
        $I = $this;
        $I->click($I->Element("ReasonButton", "AboutTheStudentPage"));
        $I->waitForElementVisible(str_replace("{{}}", $reason, $I->Element("SelectAppointmentReason", "AboutTheStudentPage")), 20);
        $I->click(str_replace("{{}}", $reason, $I->Element("SelectAppointmentReason", "AboutTheStudentPage")));
        $startDate = $I->SelectDate($start_date);
        $endDate = $I->SelectDate($end_date);
        $Time=  $this->GetStartAndEndTime();
        $I->click($I->Element("StartDateBox", "AppointmentPage"));
        $I->waitForElementVisible(str_replace("{{}}", $startDate, $I->Element("SelectStartDate", "AppointmentPage")), 60);
        $I->click(str_replace("{{}}", $startDate, $I->Element("SelectStartDate", "AppointmentPage")));
        $I->fillField($I->Element("StartTime", "AppointmentPage"), $Time[0]);
        $I->click($I->Element("EndDateBox", "AppointmentPage"));
        $I->waitForElementVisible(str_replace("{{}}", $endDate, $I->Element("SelectEndDate", "AppointmentPage")), 60);
        $I->click(str_replace("{{}}", $endDate, $I->Element("SelectEndDate", "AppointmentPage")));
        $I->fillField($I->Element("EndTime", "AppointmentPage"), $Time[1]);
        $I->fillField($I->Element("Location", "AppointmentPage"), $location);
        $Desc1 = $Desc . round(microtime(true) * 1000);
        $I->writeDataInJson($this, "AppointmentCommentDesc", $Desc1);
        $Description = $I->getDataFromJson($this, "AppointmentCommentDesc");
        $I->fillField($I->Element("Description", "AboutTheStudentPage"), $Description);
    }

    public function SelectDate($day) {
        $I = $this;
        if ($day == "current") {
            $date = $I->GetCurrentDate($I);
            return $date;
        }
    }
    
    public function GetStartAndEndTime() {
        $I=$this;
        date_default_timezone_set("Asia/Calcutta");
        $now = time();
        $ten_minutes = $now + (10 * 60);
        $twenty_minutes = $now + (40 * 60);
        $startTime = date('h:i A', $ten_minutes);
        $endTime = date('h:i A', $twenty_minutes);
        $I->writeDataInJson($this, 'startTime', $startTime);
        $I->writeDataInJson($this, 'endTime', $endTime);
        $startTimeValue = $I->getDataFromJson($this, 'startTime');
        $endTimeValue = $I->getDataFromJson($this, 'endTime');
        return array($startTimeValue, $endTimeValue);
    } 
    
    
    public function clickOnLinkUnderdetailsTab($link)
    {
        $I=$this;
         $I->waitForElement(str_replace("{{}}",$link,$I->Element("linksUnderDetailsTab","AboutTheStudentPage")));
         $I->click(str_replace("{{}}",$link,$I->Element("linksUnderDetailsTab","AboutTheStudentPage")));
        
    }
       public function VerifyUserIsInActive(){
        $I=$this;
        $I->canSeeElement($I->Element("InactiveStatus", "AboutTheStudentPage"));
    }
    
    public function VerifyUserIsActive(){
        $I=$this;
        $I->dontSeeElement($I->Element("InactiveStatus", "AboutTheStudentPage"));
    }
    

}
