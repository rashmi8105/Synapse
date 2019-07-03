<?php

namespace Step\Acceptance;

class AppointmentStep extends \AcceptanceTester {

    /**
     * @When user clicks on :arg1 link on appointment page
     */
    public function userClicksOnLink($arg1) {
        
        $this->AppointmentLinks($arg1);
    }

    /**
     * @When fill all the mandatory fields :arg1,:arg2,:arg3,:arg4,:arg5,:arg6 to :arg7 appointment
     */
    public function fillAllTheMandatoryFieldsforAppointment($reason, $Date, $LOcation, $SharingOPtion, $Student1, $Student2, $type) {
        $this->FillAllTheFieldsToBookAppointment($reason, $Date, $LOcation, $SharingOPtion, $Student1, $Student2, $type);
    }

    /**
     * @When click on cancel button
     */
    public function clickOnCancelButton() {
        $this->UserClicksOnCancelButton();
    }

    /**
     * @Then user is not able to view appointment on the page
     */
    public function userIsNotAbleToViewAppointmentOnThePage() {
        $this->AppointmentIsNotDisplaying("Nevermind");
    }

    /**
     * @When click on BookAppointment button
     */
    public function clickOnBookAppointmentButton() {
        $this->UserClicksOnBookAppointmentButton();
    }

    /**
     * @Then user is able to view appointment on the page
     */
    public function userIsAbleToViewAppointmentOnThePage() {
        $this->AppointmentIsDisplaying("Create");
    }

    /**
     * @Then user is able to view the edited appointment on the page
     */
    public function userIsAbleToViewTheEditedAppointmentOnThePage() {
        $this->AppointmentIsDisplaying("Edit");
    }

    /**
     * @When user clicks on Edit Appointment text under Menu icon
     */
    public function userClicksOnEditAppointmentTextUnderMenuIcon() {
        $this->ClickOnEditAppointment();
    }

    /**
     * @When user clicks on Cancel and Remove text under Menu icon
     */
    public function userClicksOnCancelAndRemoveTextUnderMenuIcon() {
        $this->ClickOnCancelAndRemoveAppointment();
    }

    /**
     * @Then user is not able to view the cancelled appointment on the page
     */
    public function userIsNotAbleToViewTheCancelledAppointmentOnThePage() {
        $this->AppointmentIsNotDisplaying("CancelRemove");
    }

    /**
     * @When fill all the mandatory fields to :arg1 one time office hr for :arg2 time, :arg3 location and :arg4
     */
    public function fillAllTheMandatoryFieldsToOneTimeOfficeHrForTimeLocationAnd($type, $slot_time, $location, $Date) {
        $this->FillFieldsForOneTimeOfficeHrs($type, $slot_time, $location, $Date);
    }

    /**
     * @Then user is not able to view Office hr on the page
     */
    public function userIsNotAbleToViewOfficeHrOnThePage() {
        $this->OfficeHrsNotDisplaying("Create");
    }

    /**
     * @When click on save button
     */
    public function clickOnSaveButton() {
        $this->ClickOnAddOfficeHrBtn();
    }

    /**
     * @Then user is able to view Office hr on the page
     */
    public function userIsAbleToViewOfficeHrOnThePage() {
        $this->OfficeHrsIsDisplaying("Create");
    }

    /**
     * @When user clicks BookIcon icon in front of the office hour
     */
    public function iClickBookIconIconInFrontOfTheOfficeHour() {
        $this->ClickOnBookIcon();
    }

    /**
     * @Then user is able to view Office hr appointment on the page
     */
    public function userIsAbleToViewOfficeHrAppointmentOnThePage() {
        $this->AppointmentIsDisplaying("BookAppointmentFromOfficeHr");
    }

    /**
     * @When user clicks CancelIcon icon in front of the office hour
     */
    public function iClickCancelIconIconInFrontOfTheOfficeHour() {
        $this->ClickOnCancelIcon();
    }

    /**
     * @When confirm its cancellation
     */
    public function confirmItsCancellation() {
        $this->ClickOnConfirmBtn();
    }

    /**
     * @When user clicks on Manage this slot text under Menu icon
     */
    public function userClicksOnManageThisSlotTextUnderMenuIcon() {
        $this->ClickOnManageThisSlot();
    }

    /**
     * @Then user is able to view edited Office hr on the page
     */
    public function userIsAbleToViewEditedOfficeHrOnThePage() {
        $this->OfficeHrsIsDisplaying("Edit");
    }

    /**
     * @When fill all the mandatory fields to :arg1 series office hr with values :arg2, :arg3, :arg4, :arg5,:arg6,:arg7,:arg6,:arg7,:arg6,:num1
     */
    public function fillAllTheMandatoryFieldsToSeriesOfficeHrWithValues($type, $slotDuration, $location, $RepeatFrequency, $repeatDays, $includeSatSun, $StartDate, $EndBy, $EndDate, $EndAfter, $EndAfterOccurence) {
        $this->FillFieldsForSeriesOfficeHrs($type, $slotDuration, $location, $RepeatFrequency, $repeatDays, $includeSatSun, $StartDate, $EndBy, $EndDate, $EndAfter, $EndAfterOccurence);
    }

    /**
     * @Then user is able to view Series Office hr on the page
     */
    public function userIsAbleToViewSeriesOfficeHrOnThePage() {
        $this->SeriesOfficeHrIsDisplayed();
    }

    /**
     * @When user clicks Remove Office Hour text under Menu icon for :arg1
     */
    public function userClicksRemoveOfficeHourTextUnderMenuIconFor($officeHrType) {
        $this->ClickOnRemoveOfficeHr($officeHrType);
    }

    /**
     * @Then user is not able to view the cancelled series office hr on the page
     */
    public function userIsNotAbleToViewTheCancelledSeriesOfficeHrOnThePage() {
        $this->SeriesOfficeHrNotDisplaying();
    }

    /**
     * @Then user is not able to view the cancelled one time office hr on the page
     */
    public function userIsNotAbleToViewTheCancelledOneTimeOfficeHrOnThePage() {
        $this->OfficeHrsNotDisplaying("Edited");
    }

    /**
     * @When add a faculty :arg1 as delegate
     */
    public function addAFacultyAsDelegate($facultyName) {
        $this->AddDelegateUser($facultyName);
    }

    /**
     * @Then user is able to view :arg1 on the page
     */
    public function userIsAbleToViewOnThePage($coordinatorName) {
        $this->ViewCoordinatorOnDelegatePage($coordinatorName);
    }

    /////////////////////////////////Implementations////////////////////////////////////////
    public function GetStartAndEndTime() {

        $I = $this;
        date_default_timezone_set("Asia/Calcutta");
        $LastAppointmentEndTime=$I->getDataFromJson($this,"endTime");
        if($LastAppointmentEndTime=='')
        {        
        $now=time();
        }
      else{
        if(time()>strtotime($LastAppointmentEndTime))
        { 
          $now=time(); 
        }
        else
        {
            $now=strtotime($LastAppointmentEndTime);
            
        }
      }
        $ten_minutes = $now + (10 * 60);
        $twenty_minutes = $now + (20 * 60);
        $startTime = date('h:i A', $ten_minutes);
        $endTime = date('h:i A', $twenty_minutes);
        $I->writeDataInJson($this, 'startTime', $startTime);
        $I->writeDataInJson($this, 'endTime', $endTime);
        $startTimeValue = $I->getDataFromJson($this, 'startTime');
        $endTimeValue = $I->getDataFromJson($this, 'endTime');
        return array($startTimeValue, $endTimeValue);
    }

    public function AppointmentLinks($link) {
        $I = $this;
        $I->click(str_replace("{{}}", $link, $I->Element("Links", "AppointmentPage")));
        $I->WaitForModalWindowToAppear($I);
    }

    public function FillAllTheFieldsToBookAppointment($reasonValue, $Date, $locationValue, $optionValue, $attendes1, $attendes2, $type) {
        $I = $this;
        $Time = $this->GetStartAndEndTime();
        if ($Date == "current") {
            $currentDate = $I->GetCurrentDate($I);
        }
        $I->waitForElementVisible($I->Element("ReasonDropDown", "AppointmentPage"), 60);
        $I->click($I->Element("ReasonDropDown", "AppointmentPage"));
        $I->waitForElementVisible(str_replace("{{}}", $reasonValue, $I->Element("ReasonDropDownValues", "AppointmentPage")), 60);
        $I->click(str_replace("{{}}", $reasonValue, $I->Element("ReasonDropDownValues", "AppointmentPage")));
        if ($type !== "BookAppointmentFromOfficeHr") {
            $I->click($I->Element("StartDateBox", "AppointmentPage"));
            $I->waitForElementVisible(str_replace("{{}}", $currentDate, $I->Element("SelectStartDate", "AppointmentPage")), 60);
            $I->click(str_replace("{{}}", $currentDate, $I->Element("SelectStartDate", "AppointmentPage")));
            $I->fillField($I->Element("StartTime", "AppointmentPage"), $Time[0]);
            $I->click($I->Element("EndDateBox", "AppointmentPage"));
            $I->waitForElementVisible(str_replace("{{}}", $currentDate, $I->Element("SelectEndDate", "AppointmentPage")), 60);
            $I->click(str_replace("{{}}", $currentDate, $I->Element("SelectEndDate", "AppointmentPage")));
            $I->fillField($I->Element("EndTime", "AppointmentPage"), $Time[1]);
        }
        $location = $locationValue . rand(9, 999);
        if ($type == "Create") {
            $I->writeDataInJson($this, "AppointmentLocationName", $location);
            $LocationValue = $I->getDataFromJson($this, "AppointmentLocationName");
        } elseif ($type == "BookAppointmentFromOfficeHr") {
            $I->writeDataInJson($this, "BookAppointmentFromOfficeHrLocation", $location);
            $LocationValue = $I->getDataFromJson($this, "BookAppointmentFromOfficeHrLocation");
        } else {
            $I->writeDataInJson($this, "EditedLocationName", $location);
            $LocationValue = $I->getDataFromJson($this, "EditedLocationName");
        }
        $I->fillField($I->Element("Location", "AppointmentPage"), $LocationValue);
        $I->click(str_replace("{{}}", $optionValue, $I->Element("SharingOption", "AppointmentPage")));
        if ($type == "Create" || $type == "BookAppointmentFromOfficeHr") {
            $I->fillField($I->Element("SearchAttendes", "AppointmentPage"), $attendes1);
            $I->waitForElementVisible(str_replace("{{}}", $attendes1, $I->Element("selectAttendees", "AppointmentPage")), 60);
            $I->click(str_replace("{{}}", $attendes1, $I->Element("selectAttendees", "AppointmentPage")));
            $I->click($I->Element("addAttendees", "AppointmentPage"));
            if($attendes2!="None"){
            $I->fillField($I->Element("SearchAttendes", "AppointmentPage"), $attendes2);
            $I->waitForElementVisible(str_replace("{{}}", $attendes2, $I->Element("selectAttendees", "AppointmentPage")), 60);
            $I->click(str_replace("{{}}", $attendes2, $I->Element("selectAttendees", "AppointmentPage")));
            $I->click($I->Element("addAttendees", "AppointmentPage"));
            }
        } else {
            $I->click(str_replace("{{}}", $attendes1, $I->Element("RemoveStdIcon", "AppointmentPage")));
        }
    }

    public function UserClicksOnCancelButton() {
        $I = $this;
        $I->click($I->Element("CancelBtn", "AppointmentPage"));
        $I->WaitForModalWindowToDisappear($I);
    }

    public function UserClicksOnBookAppointmentButton() {
        $I = $this;
        $I->click($I->Element("BookAppointmentBtn", "AppointmentPage"));
        $I->WaitForModalWindowToAppear($I);
        $I->click($I->Element("BookAppointmentOnDialog", "AppointmentPage"));
    }

    public function AppointmentIsNotDisplaying($type) {
        $I = $this;
        if ($type == "Nevermind") {
            $Location = $I->getDataFromJson($this, "AppointmentLocationName");
        } else {
            $Location = $I->getDataFromJson($this, "EditedLocationName");
        }

        $I->cantSeeElement(str_replace("{{}}", $Location, $I->Element("AppointmentLocation", "AppointmentPage")));
    }

    public function AppointmentIsDisplaying($type) {
        $I = $this;
        if ($type == "Create") {
            $Location = $I->getDataFromJson($this, "AppointmentLocationName");
        } elseif ($type == "BookAppointmentFromOfficeHr") {
            $Location = $I->getDataFromJson($this, "BookAppointmentFromOfficeHrLocation");
        } else {
            $Location = $I->getDataFromJson($this, "EditedLocationName");
        }
        $I->canSeeElement(str_replace("{{}}", $Location, $I->Element("AppointmentLocation", "AppointmentPage")));
    }

    public function ClickOnEditAppointment() {
        $I = $this;
        $I->MouseOverOnIcon("ToEdit");
        $I->click($I->Element("Edit", "AppointmentPage"));
        $I->WaitForModalWindowToAppear($I);
    }

    public function ClickOnCancelAndRemoveAppointment() {
        $I = $this;
        $I->MouseOverOnIcon("ToCancelRemove");
        $I->click($I->Element("CancelRemove", "AppointmentPage"));
        $I->WaitForModalWindowToAppear($I);
        $I->click($I->Element("ConfirmBtn", "AppointmentPage"));
    }

    public function MouseOverOnIcon($type) {
        $I = $this;
        if ($type == "ToEdit") {
            $Location = $I->getDataFromJson($this, "AppointmentLocationName");
        } elseif ($type == "ManageThisSlot") {
            $Location = $I->getDataFromJson($this, "OfficeHrsLocation");
        } elseif ($type == "onetime") {
            $Location = $I->getDataFromJson($this, "EditedOfficeHrsLocation");
        } elseif ($type == "ToCancelRemoveSeries") {
            $Location = $I->getDataFromJson($this, "SeriesOfficeHrsLocation");
        } else {
            $Location = $I->getDataFromJson($this, "EditedLocationName");
        }
        $I->moveMouseOver(str_replace("{{}}", $Location, $I->Element("MenuArrow", "AppointmentPage")));
          
        
        }

    public function FillFieldsForOneTimeOfficeHrs($type, $slot_time, $location, $Date) {
        $I = $this;
       if (strpos($type,"Create")!==false) {
            $I->click($I->Element("SlotButton", "AppointmentPage"));
            $I->waitForElementVisible(str_replace("{{}}", $slot_time, $I->Element("SelectSlots", "AppointmentPage")), 60);
            $I->click(str_replace("{{}}", $slot_time, $I->Element("SelectSlots", "AppointmentPage")));
        }
        if (strpos($type,"Create")!==false) {
            $Time = $this->GetStartAndEndTime();
            $I->waitForElementVisible($I->Element("OfficeStrtTime", "AppointmentPage"), 60);
            $I->fillField($I->Element("OfficeStrtTime", "AppointmentPage"), $Time[0]);
            $I->fillField($I->Element("OfficeEndTime", "AppointmentPage"), $Time[1]);
        }
        if ($Date == "current") {
            $currentDate = $I->GetCurrentDate($I);
        }
        
        $Location = $location . rand(0, 3000);
        if (strpos($type,"Create")!==false) { 
             
            if(strpos($type,"CampusConnection")!==false)
            { 
                
              $I->writeDataInJson($this, "CampusConnectionOfficeHrsLocation", $Location);  
              $LocationValue = $I->getDataFromJson($this, "CampusConnectionOfficeHrsLocation");

            }
            else
            {
                
             $I->writeDataInJson($this, "OfficeHrsLocation", $Location);  
             $LocationValue = $I->getDataFromJson($this, "OfficeHrsLocation");

            }
            
        } else {
            $I->writeDataInJson($this, "EditedOfficeHrsLocation", $Location);
            $LocationValue = $I->getDataFromJson($this, "EditedOfficeHrsLocation");
        }
        $I->fillField($I->Element("OfficeHrsLocation", "AppointmentPage"), $LocationValue);
        $I->click($I->Element("OfficeHrDate", "AppointmentPage"));
        $I->waitForElementVisible(str_replace("{{}}", $currentDate, $I->Element("SelectOfficeHrsDate", "AppointmentPage")), 60);
        $I->click(str_replace("{{}}", $currentDate, $I->Element("SelectOfficeHrsDate", "AppointmentPage")));
    }

    public function OfficeHrsNotDisplaying($type) {
        $I = $this;
        if ($type == "Create") {
            $Location = $I->getDataFromJson($this, "OfficeHrsLocation");
            $I->cantSeeElement(str_replace("{{}}", $Location, $I->Element("AppointmentLocation", "AppointmentPage")));
        } else {
            $Location = $I->getDataFromJson($this, "EditedOfficeHrsLocation");
            $I->cantSeeElement(str_replace("{{}}", $Location, $I->Element("AppointmentLocation", "AppointmentPage")));
        }
    }

    public function OfficeHrsIsDisplaying($type) {
        $I = $this;
        if ($type == "Create") {
            $Location = $I->getDataFromJson($this, "OfficeHrsLocation");
            $I->canSeeElement(str_replace("{{}}", $Location, $I->Element("AppointmentLocation", "AppointmentPage")));
        } else {
            $Location = $I->getDataFromJson($this, "EditedOfficeHrsLocation");
            $I->canSeeElement(str_replace("{{}}", $Location, $I->Element("AppointmentLocation", "AppointmentPage")));
        }
    }

    public function ClickOnAddOfficeHrBtn() {
        $I = $this;
        $I->click($I->Element("AddOfficeHoursBtn", "AppointmentPage"));
    }

    public function ClickOnBookIcon() {
        $I = $this;
        $OfficeHrLocation = $I->getDataFromJson($this, "OfficeHrsLocation");
        $I->click(str_replace("{{}}", $OfficeHrLocation, $I->Element("BookIcon", "AppointmentPage")));
        $I->WaitForModalWindowToAppear($I);
    }

    public function ClickOnCancelIcon() {
        $I = $this;
        $OfficeHrLocation = $I->getDataFromJson($this, "BookAppointmentFromOfficeHrLocation");
        $I->click(str_replace("{{}}", $OfficeHrLocation, $I->Element("CancelIcon", "AppointmentPage")));
        $I->WaitForModalWindowToAppear($I);
    }

    public function ClickOnConfirmBtn() {
        $I = $this;
        $I->click($I->Element("ConfirmBtn", "AppointmentPage"));
    }

    public function FillFieldsForSeriesOfficeHrs($type, $slotDuration, $location, $RepeatFrequency, $repeatDays, $includeSatSun, $StartDate, $EndBy, $EndDate, $EndAfter, $EndAfterOccurence) {
        $I = $this;
        $I->click($I->Element("SeriesRadio", "AppointmentPage"));
        $time = $I->GetStartAndEndTime();
        if ($StartDate == "current") {
            $currentDate = $I->GetCurrentDate($I);
        }
       
        $I->click($I->Element("SlotButton", "AppointmentPage"));
        $I->waitForElementVisible(str_replace("{{}}", $slotDuration, $I->Element("SelectSlots", "AppointmentPage")), 60);
        $I->click(str_replace("{{}}", $slotDuration, $I->Element("SelectSlots", "AppointmentPage")));
         $I->fillField($I->Element("OfficeStrtTime", "AppointmentPage"), $time[0]);
        $I->fillField($I->Element("OfficeEndTime", "AppointmentPage"), $time[1]);
        $Location = $location . rand(0, 3000);
        $I->writeDataInJson($this, "SeriesOfficeHrsLocation", $Location);
        $LocationValue = $I->getDataFromJson($this, "SeriesOfficeHrsLocation");
        $I->fillField($I->Element("OfficeHrsLocation", "AppointmentPage"), $LocationValue);
        $I->click($I->Element("RepeatButton", "AppointmentPage"));
        $I->waitForElementVisible(str_replace("{{}}", $RepeatFrequency, $I->Element("RepeatOptions", "AppointmentPage")));
        $I->click(str_replace("{{}}", $RepeatFrequency, $I->Element("RepeatOptions", "AppointmentPage")));
        $I->fillField($I->Element("RepeatDaysCount", "AppointmentPage"), $repeatDays);
        $I->SelectIncludeSatSun($includeSatSun);
        $I->click($I->Element("SeriesStartDate", "AppointmentPage"));
        $I->click(str_replace("{{}}", $currentDate, $I->Element("SelectSeriesStartDate", "AppointmentPage")));
        $I->SelectSeriesEndDate($EndBy, $EndDate);
        $I->SelectEndAfterOccurence($EndAfter, $EndAfterOccurence);
    }

    public function SelectIncludeSatSun($includeSatSun) {
        $I = $this;
        if ($includeSatSun == "true") {
            $I->click($I->Element("includeSatSunday", "AppointmentPage"));
        }
    }

    public function SelectSeriesEndDate($EndBy, $EndDate) {
        $I = $this;
        if ($EndBy == "true") {
            $I->click($I->Element("SeriesEndByRadioBTn", "AppointmentPage"));
            if ($EndDate == "current") {
                $currentDate = $I->GetCurrentDate($I);
            }
            $I->click($I->Element("SeriesEndDate", "AppointmentPage"));
            $I->click(str_replace("{{}}", $currentDate, $I->Element("SelectSeriesEndDate", "AppointmentPage")));
        }
    }

    public function SelectEndAfterOccurence($EndAfter, $EndAfterOccurence) {
        $I = $this;
        if ($EndAfter == "true") {
            $I->click($I->Element("SeriesEndAfterRadioBtn", "AppointmentPage"));
            $I->fillField($I->Element("EndAfterOccurenceInput", "AppointmentPage"), $EndAfterOccurence);
        }
    }

    public function SeriesOfficeHrIsDisplayed() {
        $I = $this;
        $Location = $I->getDataFromJson($this, "SeriesOfficeHrsLocation");
        $I->canSeeElement(str_replace("{{}}", $Location, $I->Element("AppointmentLocation", "AppointmentPage")));
    }

    public function SeriesOfficeHrNotDisplaying() {
        $I = $this;
        $Location = $I->getDataFromJson($this, "SeriesOfficeHrsLocation");
        $I->cantSeeElement(str_replace("{{}}", $Location, $I->Element("AppointmentLocation", "AppointmentPage")));
    }

    public function ClickOnRemoveOfficeHr($officeHrType) {
        $I = $this;
        if ($officeHrType == "Series") {
            $I->MouseOverOnIcon("ToCancelRemoveSeries");
            $I->wait(2);
            $I->click($I->Element("RemoveOfficeHr", "AppointmentPage"));
            $I->WaitForModalWindowToAppear($I);
            $I->click($I->Element("RemoveThisOffcHrBtn", "AppointmentPage"));
        } else {
            $I->MouseOverOnIcon("onetime");
            $I->click($I->Element("RemoveOfficeHr", "AppointmentPage"));
            $I->WaitForModalWindowToAppear($I);
            $I->click($I->Element("RemoveThisOffcHrBtn", "AppointmentPage"));
        }
    }

    public function AddDelegateUser($facultyName) {
        $I = $this;
       if($I->isElementDisplayed($I, str_replace("{{}}",$facultyName,$I->Element("DeleteDelegateAccessDeleteIcon","AppointmentPage"))))
       {  
           $I->click(str_replace("{{}}",$facultyName,$I->Element("DeleteDelegateAccessDeleteIcon","AppointmentPage")));
           $I->click($I->Element("delegateSaveChanges", "AppointmentPage"));
           $I->SuccessMsgAppears($I);
           $I->SuccessMsgDisappears($I);
           $I->click(str_replace("{{}}","Manage Delegates",$I->Element("Links", "AppointmentPage")));
           $I->WaitForModalWindowToAppear($I);
      }
        $I->fillField($I->Element("facultySearchBox", "AppointmentPage"), $facultyName);
        $I->waitForElementVisible(str_replace("{{}}", $facultyName, $I->Element("selectAttendees", "AppointmentPage")), 60);
        $I->click(str_replace("{{}}", $facultyName, $I->Element("selectAttendees", "AppointmentPage")));
        $I->click($I->Element("addAttendees", "AppointmentPage"));
        $I->click($I->Element("delegateSaveChanges", "AppointmentPage"));
    }

    public function ViewCoordinatorOnDelegatePage($coordinatorName) {
        $I = $this;
        if ($I->canSeeInCurrentUrl('/managecalendars') == '') {
            $I->waitForElementVisible((str_replace("{{}}", $coordinatorName, $I->Element("CoordinatorEmail", "AppointmentPage"))), 10);
            $I->click(str_replace("{{}}", $coordinatorName, $I->Element("CoordinatorEmail", "AppointmentPage")));
            $I->click($I->Element("OpenAllSelectedSchedules", "AppointmentPage"));
            $I->WaitForPageToLoad($I);
        } 
        if($coordinatorName=="Faculty08"){
        $I->canSeeElement(str_replace("{{}}","E Johnson", $I->Element("AgendaMessage", "AppointmentPage")));
        }
        else
        {
    $I->canSeeElement(str_replace("{{}}", $coordinatorName, $I->Element("AgendaMessage", "AppointmentPage")));

        }
        
        }

    public function ClickOnManageThisSlot() {
        $I = $this; 
        $I->wait(3);
        $I->MouseOverOnIcon("ManageThisSlot");
        $I->click($I->Element("ManageThisSlot", "AppointmentPage"));
        $I->WaitForModalWindowToAppear($I);
    }

}
