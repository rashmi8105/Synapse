<?php

namespace Step\Acceptance;

class SystemAlertStep extends \AcceptanceTester {

    /**
     * @When user clicks on Create New Message button on System Alert page
     */
    public function userClicksOnCreateNewMessageButtonOnSystemAlertPage() {

        $this->UserClicksOnCreateNewMessageButton();
    }

    /**
     * @When Fill :arg1 and :arg2 and :arg3 in modal window
     */
    public function fillAndAndInModalWindow($message, $messageType, $StopDateOption) {

        $this->UserCreatesSystemAlert($message, $messageType, $StopDateOption);
    }

    /**
     * @Then user is able to view the system alert with message :arg1 in the list
     */
    public function userIsAbleToViewTheSystemAlertWithMessageInTheList($systemAlert) {

        $this->SystemAlertIsDisplayed($systemAlert);
    }

    /**
     * @When user edits the created system alert :arg1
     */
    public function userEditsTheCreatedSystemAlert($systemAlert) {

        $this->EditSystemAlert($systemAlert);
    }

    /**
     * @When user deletes the created system alert :arg1
     */
    public function userDeletesTheCreatedSystemAlert($systemAlert) {

        $this->DeleteSystemAlert($systemAlert);
    }

    /**
     * @Then user is not able to view the system alert :arg1 in the list
     */
    public function userIsNotAbleToViewTheSystemAlertInTheList($systemAlert) {

        $this->SystemAlertNotDisplayed($systemAlert);
    }

/////////////////////////implementations///////////////////////////     


    public function UserClicksOnCreateNewMessageButton() {
        $I = $this;
        $I->click($I->Element("createNewMessageBtn", "SystemAlertPage"));
        $I->WaitForModalWindowToAppear($I);
    }

    public function UserCreatesSystemAlert($message, $MessageType, $stopOption) {
        $I = $this;
        $finalMessage = $message.round(microtime(true) * 1000);
        $I->writeDataInJson($this, $message, $finalMessage);
        $I->fillField($I->Element("messageTextField", "SystemAlertPage"), $finalMessage);
        $I->click(str_replace("{{}}", $MessageType, $I->Element("MessageType", "SystemAlertPage")));
        $I->click($I->Element("Stop_DateTimeButton", "SystemAlertPage"));
        $I->waitForElementVisible(str_replace("{{}}", $stopOption, $I->Element("Stop_DateTimeDropDown", "SystemAlertPage")), 60);
        $I->click(str_replace("{{}}", $stopOption, $I->Element("Stop_DateTimeDropDown", "SystemAlertPage")));
        $I->click($I->Element("SaveBtn", "SystemAlertPage"));
    }

    public function SystemAlertIsDisplayed($staticList) {
        $I = $this;
        $StaticList_Text = $I->getDataFromJson($this, $staticList);
        $I->canSeeElement(str_replace("{{}}", $StaticList_Text, $I->Element("systemalert_inlist", "SystemAlertPage")));
    }

    public function EditSystemAlert($Message) {
        $I = $this;
        $StaticList_Text = $I->getDataFromJson($this, $Message);
        $I->click(str_replace("{{}}", $StaticList_Text, $I->Element("editIcon", "SystemAlertPage")));
        $I->WaitForModalWindowToAppear($I);
        $finalMessage = $Message.round(microtime(true) * 1000);
        $I->writeDataInJson($this, "SystemAlert_Edited", $finalMessage);
        $I->fillField($I->Element("messageTextField", "SystemAlertPage"), $finalMessage);
        $I->click($I->Element("SaveBtn", "SystemAlertPage"));
    }

    public function DeleteSystemAlert($Message) {
        $I = $this;
        $StaticList_Text = $I->getDataFromJson($this, $Message);
        $I->click(str_replace("{{}}", $StaticList_Text, $I->Element("deleteIcon", "SystemAlertPage")));
    }

    public function SystemAlertNotDisplayed($staticList) {
        $I = $this;
        $StaticList_Text = $I->getDataFromJson($this, $staticList);
        $I->cantSeeElement(str_replace("{{}}", $StaticList_Text, $I->Element("systemalert_inlist", "SystemAlertPage")));
    }

}
