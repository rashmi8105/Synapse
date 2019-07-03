<?php

namespace Step\Acceptance;

class StudentAppointmentStep extends \AcceptanceTester {

    /**
     * @When student schedule appointment with reason :arg1 and description :arg2
     */
    public function studentScheduleAppointmentWithReasonAnd($Reason,$Description) {
        $this->Sceduleappointment($Reason,$Description);
    }

    /**
     * @Then student is able to see :arg1 appointment on student appointment page with :arg2 from office hours
     */
    public function studentSeeAppointmentOnStudentAppointmentPageWithFromOfficeHours($TypeOfAppointment,$FacultyName) {
        $this->verifyAppointmentOnStudentAppointmentPageFromOfficeHours($TypeOfAppointment,$FacultyName);
    }

    /**
     * @When student clicks on schedule appointment button on student appointment page
     */
    public function userClicksOnScheduleAppointmentButtonOnStudentPage() {
        $this->ClickOnScheduleButton();
    }

    /**
     * @When student clicks on cancel appointment button
     */
    public function studentClicksOnCancelAppointmentButton() {

        $this->clickOnCancelButtonOnStudentAppointmentPage();
    }

    /**
     * @When student clicks on cancel appointment confirm button
     */
    public function studentClicksOnCancelConfirmButton() {
        $this->ClickOnCancelAppointmentConfirmButton();
    }

    /**
     * @Then student is not able to see appointment on student appointment page with :arg1
     */
    public function studentShouldNotSeeAppointmentOnStudentAppointmentPageWith($FacultyName) {
        $this->VerifyAbsenceofAppointment($FacultyName);
    }

    /**
     * @Then student is not able to see appointment on student appointment page with :arg1 from office hours
     */
    public function studentShouldNotSeeAppointmentOnStudentAppointmentPageWithOfficeHour($FacultyName) {
        $this->verifyAbsenceAppointmentOnStudentAppointmentPageFromOfficeHours($FacultyName);
    }

    /**
     * @Then student is able to see scheduled appointment with :arg1
     */
    public function studentSeeScheduledAppointmentWith($FacultyName) {
        $this->VerifyPresenceofAppointment($FacultyName);
    }

    /**
     * @Then student is able to see select a person window
     */
    public function studentSeesSelectAPersonWindow() {
        $this->VerifySelectApersonWindow();
    }

    /**
     * @Then student chooses :arg1 faculty from select a person window
     */
    public function studentChoosesFacultyFromSelectAPersonWindow($FacultyName) {

        $this->SelectFacultyOnSelectAPersonWindow($FacultyName);
    }

   /**
     * @Then student is able to see created office hour
     */
    public function studentSeesCreatedOfficeHour() {

        $this->VerifyPresenceofOfficeHours();
    }

   /**
     * @Then student is not able to see created office hour
     */
    public function studentDoesNotSeeCreatedOfficeHour() {

        $this->VerifyAbsenceofOfficeHours();
    }

    /**
     * @When student clicks on cancel appointment created from office hour
     */
    public function studentClicksOnCancelAppointmentCreatedFromOfficeHour() {
        $this->clickOnCancelButtonOnStudentAppointmentPageforOfficeHour();
    }

/////////////////////////////////////////////////////////////////////////////
    public function clickOnCancelButtonOnStudentAppointmentPageforOfficeHour() {
        $I = $this;
        $I->click(str_replace("{{}}", $I->getDataFromJson(new AppointmentStep($I->getScenario()), "OfficeHrsLocation"), $I->Element("cancelAppointment", "StudentAppointmentPage")));
        $I->WaitForModalWindowToAppear($I);
    }

    public function VerifyPresenceofOfficeHours() {


        $I = $this;
        $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson(new AppointmentStep($this->getScenario()), "OfficeHrsLocation"), $I->Element("OfficeHoursOnAvailbilityWindow", "StudentAppointmentPage")));
    }

    public function VerifyAbsenceofOfficeHours() {


        $I = $this;
        $I->cantSeeElement(str_replace("{{}}", $I->getDataFromJson(new AppointmentStep($this->getScenario()), "OfficeHrsLocation"), $I->Element("OfficeHoursOnAvailbilityWindow", "StudentAppointmentPage")));
    }

    public function OfficeHoursOnAvailbilityWindow() {


        $I = $this;
        $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson(new AppointmentStep($this->getScenario()), "OfficeHrsLocation"), $I->Element("StudentAppointmentPage", "OfficeHoursOnAvailbilityWindow")));
    }

     public function SelectFacultyOnSelectAPersonWindow($FacultyName) {
       $I = $this; 
       $I->waitForElement(str_replace("{{}}", $FacultyName, $I->Element("PersonName", "StudentAppointmentPage")));
        $I->click(str_replace("{{}}", $FacultyName, $I->Element("PersonName", "StudentAppointmentPage")));
    }

    public function VerifySelectApersonWindow() {

        $I = $this;
        $I->waitForElement($I->Element("SelectAWindow", "StudentAppointmentPage"));
        $I->canSeeElement($I->Element("SelectAWindow", "StudentAppointmentPage"));
    }

    public function VerifyAbsenceofAppointment($FacultyName) {

        $I = $this;
        $I->cantSeeElement(str_replace("{{}}", $I->getDataFromJson(new AppointmentStep($this->getScenario()), "AppointmentLocationName"), str_replace("<<>>", $FacultyName, $I->Element("Who", "StudentAppointmentPage"))));
    }

    public function VerifyPresenceofAppointment($FacultyName) {
        $I = $this;
        $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson(new AppointmentStep($this->getScenario()), "AppointmentLocationName"), str_replace("<<>>", $FacultyName, $I->Element("Who", "StudentAppointmentPage"))));
    }

    public function clickOnCancelButtonOnStudentAppointmentPage() {
        $I = $this;
        $I->click(str_replace("{{}}", $I->getDataFromJson(new AppointmentStep($I->getScenario()), "AppointmentLocationName"), $I->Element("cancelAppointment", "StudentAppointmentPage")));
        $I->WaitForModalWindowToAppear($I);
    }

    public function ClickOnScheduleButton() {
        $I = $this;
        $I->waitForElementVisible($I->Element("ScheduleButton", "StudentAppointmentPage"));
        $I->click($I->Element("ScheduleButton", "StudentAppointmentPage"));
        $I->WaitForModalWindowToAppear($I);
    }

    public function Sceduleappointment($Reason,$Description) {
        $I = $this;
        if(strpos($Description,"CampusConnection")!==false)
        {
        $I->waitForElement(str_replace("{{}}", $I->getDataFromJson(new AppointmentStep($this->getScenario()), "CampusConnectionOfficeHrsLocation"), $I->Element("ActionLink", "StudentAppointmentPage")));
        $I->click(str_replace("{{}}", $I->getDataFromJson(new AppointmentStep($this->getScenario()), "CampusConnectionOfficeHrsLocation"), $I->Element("ActionLink", "StudentAppointmentPage")));
       
        }
        else
        {
        $I->waitForElement(str_replace("{{}}", $I->getDataFromJson(new AppointmentStep($this->getScenario()), "OfficeHrsLocation"), $I->Element("ActionLink", "StudentAppointmentPage")));
        $I->click(str_replace("{{}}", $I->getDataFromJson(new AppointmentStep($this->getScenario()), "OfficeHrsLocation"), $I->Element("ActionLink", "StudentAppointmentPage")));
       
        }
        $I->waitForElement($I->Element("ReasonDropDown", "StudentAppointmentPage"));
        $I->click($I->Element("ReasonDropDown", "StudentAppointmentPage"));
        $I->click(str_replace("{{}}", $Reason, $I->Element("SelectReason", "StudentAppointmentPage")));
        $I->fillField($I->Element("DescriptionTextField","StudentAppointmentPage"),$Description);
        $I->wait(3);
        $I->click($I->Element("FinalScheduleBtn", "StudentAppointmentPage"));
    }

    public function verifyAppointmentOnStudentAppointmentPageFromOfficeHours($TypeOfAppointment,$FacultyName) {
        $I = $this;
        if(strpos($TypeOfAppointment,"CampusConnection")!==false){
           $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson(new AppointmentStep($this->getScenario()),"CampusConnectionOfficeHrsLocation"), str_replace("<<>>", $FacultyName, $I->Element("Who", "StudentAppointmentPage"))));
        }else{
        $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson(new AppointmentStep($this->getScenario()), "OfficeHrsLocation"), str_replace("<<>>", $FacultyName, $I->Element("Who", "StudentAppointmentPage"))));
        }
      }

    public function verifyAbsenceAppointmentOnStudentAppointmentPageFromOfficeHours($FacultyName) {
        $I = $this;
        $I->cantSeeElement(str_replace("{{}}", $I->getDataFromJson(new AppointmentStep($this->getScenario()), "OfficeHrsLocation"), str_replace("<<>>", $FacultyName, $I->Element("Who", "StudentAppointmentPage"))));
    }

    public function ClickOnCancelAppointmentConfirmButton() {
        $I = $this;
        $I->waitForElement($I->Element("CancelBtn", "StudentAppointmentPage"));
        $I->click($I->Element("CancelBtn", "StudentAppointmentPage"));
    }

}
