<?php

namespace Step\Acceptance;

class ISPStep extends \AcceptanceTester {

    /**
     * @When users clicks on Add another profile item button
     */
    public function usersClicksOnAddAnotherProfileItemButton() {
        $this->AddAnotherISP();
    }

    /**
     * @When select :arg1 from dropdown
     */
    public function selectFromDropdown($DataType) {
        $this->selectDataType($DataType);
    }

    /**
     * @When fill details for :arg1 ISP with  :arg2
     */
    public function fillDetailsForCategoryISPWith($DataType, $ISPName) {
        $this->fillCategoryISP($DataType, $ISPName, "create");
    }

    /**
     * @When fill details for :arg1 type ISP with details :arg2, :arg3
     */
    public function fillDetailsForISPWith($DataType, $ISPName, $ISPDescription) {
        $this->fillDetails($DataType, $ISPName, $ISPDescription, "create");
    }

    /**
     * @When fill details for :arg1 type ISP with details :arg2, :arg3, :arg4, :arg5, :arg6
     */
    public function fillDetailsForTypeISPWithDetails($DataType, $ISPName, $ISPDescription, $Min, $Max, $decimal) {
        $this->fillNumberISP($DataType, $ISPName, $ISPDescription, $Min, $Max, $decimal, "create");
    }

    /**
     * @When select :arg1 for ISP
     */
    public function selectForTheISP($Calender) {
        $this->SelectCalender($Calender);
    }

    /**
     * @When click on Save button to save ISP
     */
    public function clickOnSaveButtonToSaveISP() {
        $this->ClickOnSave();
    }

    /**
     * @When user clicks on :arg1 for :arg2 ISP
     */
    public function userClicksOnISP($icon, $Datatype) {
        $this->clickOnIcon($icon, $Datatype);
    }

    /**
     * @When user edits  the :arg1 ISP with new details :arg2
     */
    public function userEditsTheISPWithNewDetailsForCategory($DataType, $ISPName) {
        $this->fillCategoryISP($DataType, $ISPName, "edit");
    }

    /**
     * @When user edits  the :arg1 ISP with new details :arg2, :arg3
     */
    public function userEditsTheISPWithNewDetails($DataType, $ISPName, $ISPDescription) {

        $this->fillDetails($DataType, $ISPName, $ISPDescription, "edit");
    }

    /**
     * @When user edits  the :arg1 ISP with new details :arg2, :arg3, :arg4, :arg5, :arg6
     */
    public function userEditsTheISPWithNewDetailsFornumberISP($DataType, $ISPName, $ISPDescription, $Min, $Max, $decimal) {
        $this->fillNumberISP($DataType, $ISPName, $ISPDescription, $Min, $Max, $decimal, "edit");
    }

    /**
     * @Then user is able to see :arg1 ISP in ISP list
     */
    public function userShouldSeeInISPList($DataType) {
        $this->ISPInList($DataType, "edited");
    }

    /**
     * @Then user is not able to see :arg1 ISP in ISP list
     */
    public function userShouldNotSeeOnISPPage($DataType) {
        $this->ISPInList($DataType, "delete");
    }

    /**
     * @When user clicks on Delete button on DialogBox 
     */
    public function userClicksOnDeleteButtonOnDialogBoxOnISPPage() {
        $this->ClickDeleteOnDialogWindow();
    }

/////////////////////////////////////////////////////////////////////////////////////////////
    public function AddAnotherISP() {
        $I = $this;
        $I->click($I->Element("AddISPButton", "ISPPage"));
        $I->WaitForPageToLoad($I);
    }

    public function selectDataType($DataType) {
        $I = $this;
        $I->waitForElement($I->Element("datatypedropdown", "ISPPage"),60);
        $I->click($I->Element("datatypedropdown", "ISPPage"));
        $I->waitForElement(str_replace("{{}}", $DataType, $I->Element("datetype", "ISPPage")),60);
        $I->click(str_replace("{{}}", $DataType, $I->Element("datetype", "ISPPage")));
    }

    public function fillCategoryISP($DataType, $ISPName, $typeofOperation) {
        $I = $this;
        $ISPName = $ISPName . rand(0, 9999999);
        $I->writeDataInJson($this, $DataType . "Name", $ISPName);

        if ($typeofOperation == 'create') {

            $I->fillField($I->Element("NameOfISP", "ISPPage"), $ISPName);
            $I->fillField($I->Element("ColumnHeader", "ISPPage"), $ISPName);
            $I->fillField($I->Element("answer1", "ISPPage"), 'Male');
            $I->fillField($I->Element("answer2", "ISPPage"), 'Female');
        }
        if ($typeofOperation == 'edit') {
            $I->fillField($I->Element("NameOfISP", "ISPPage"), $ISPName);
            $I->fillField($I->Element("ColumnHeader", "ISPPage"), $ISPName);
        }
    }

    public function fillDetails($DataType, $ISPName, $ISPDescription, $typeofoperation) {
        $I = $this;
        $ISPName = $ISPName . rand(0, 99999999);
        $I->writeDataInJson($this, $DataType . "Name", $ISPName);
        $I->fillField($I->Element("Description", "ISPPage"), $ISPDescription);
        $I->fillField($I->Element("NameOfISP", "ISPPage"), $ISPName);
        if ($typeofoperation == "create") {
            $I->fillField($I->Element("ColumnHeader", "ISPPage"), $ISPName);
            $I->fillField($I->Element("Description", "ISPPage"), $ISPDescription);
        }
    }

    public function fillNumberISP($DataType, $ISPName, $ISPDescription, $Min, $Max, $decimal, $typeofoperation) {
        $I = $this;

        $ISPName = $ISPName . rand(0, 9999999);
        $I->writeDataInJson($this, $DataType . "Name", $ISPName);

        if ($typeofoperation == "create") {
            $I->fillField($I->Element("MinValue", "ISPPage"), $Min);
            $I->fillField($I->Element("MaxValue", "ISPPage"), $Max);
            $I->Click($I->Element("decimaldropdown", "ISPPage"));
            $I->click(str_replace("{{}}", $decimal, $I->Element("decimalValue", "ISPPage")));
        }
        $I->fillField($I->Element("NameOfISP", "ISPPage"), $ISPName);
        $I->fillField($I->Element("ColumnHeader", "ISPPage"), $ISPName);
        $I->fillField($I->Element("Description", "ISPPage"), $ISPDescription);
    }

    public function SelectCalender($Calender) {
        $I = $this;
        $I->click(str_replace('{{}}', $Calender, '//label[contains(text(),"{{}}")]'));
    }

    public function ClickOnSave() {
        $I = $this;
        $I->click($I->Element("Savebutton", "ISPPage"));
    }

    public function ISPInList($Datatype, $typeofoperation) {
        $I = $this;
        $I->NavigateToLastPage();
        if ($typeofoperation == "delete") {
            $I->cantSeeElement(str_replace("{{}}", $I->getDataFromJson($this, $Datatype . "Name"), $I->Element("ISPInList", "ISPPage")));
        } else {
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, $Datatype . "Name"), $I->Element("ISPInList", "ISPPage")));
        }
    }

    public function clickOnIcon($Icon, $Datatype) {
        $I = $this;

        if ($Icon == 'delete') {
            $I->NavigateToLastPage();
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, $Datatype . "Name"), $I->Element("RemoveIconforISP", "ISPPage")));
        }
        if ($Icon == 'edit') {
            $I->NavigateToLastPage();
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, $Datatype . "Name"), $I->Element("EditIconforISP", "ISPPage")));
        }
    }

    public function ClickDeleteOnDialogWindow() {
        $I = $this;
        $I->UserclicksOnDeleteButtonDisplayedOnDialogBox($I);
    }

    public function NavigateToLastPage() {
        $I = $this;


        if ($I->isElementDisplayed($I, $I->Element("PaginationContainer", "ISPPage"))) {
            $I->WaitForPageToLoad($I);
            $I->click($I->Element("lastPaginationContainer", "ISPPage"));
            $I->WaitForPageToLoad($I);
        } else {
            $I->WaitForPageToLoad($I);
            $I->cantSeeElement($I->Element("PaginationContainer", "ISPPage"));
        }
    }

}
