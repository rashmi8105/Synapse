<?php

namespace Step\Acceptance;

class StaticListStep extends \AcceptanceTester {

    /**
     * @When user fills data for :arg1 and :arg2
     */
    public function userFillsDataForAnd($staticListName, $staticListDesc) {

        $this->FillData($staticListName, $staticListDesc);
    }

    /**
     * @When user clicks on Create New List button on static list page
     */
    public function UserClickOnCreateStaticListBtn() {

        
        $this->clickOnCreateStaticList();
    }

    /**
     * @Then user is able to view created static List in list
     */
    public function userIsAbleToViewCreatedStaticListInList() {
        $this->StaticListDisplaysOnThePage("Created");
    }

    /**
     * @When user edits Static List with :arg1 and :arg2
     */
    public function userEditsStaticListWithAnd($arg1, $arg2) {

        $this->ClickOnEditIcon();
        $this->EditData($arg1, $arg2);
    }

    /**
     * @Then user is able to view the edited static List on the page
     */
    public function userIsAbleToViewTheEditedStaticListOnThePage() {
        $this->StaticListDisplaysOnThePage("Edited");
    }

    /**
     * @When user shares Static List with :arg1
     */
    public function userSharesStaticListWith($PersonName) {

        $this->ShareList($PersonName);
    }

    /**
     * @When user clicks on :arg1
     */
    public function userClicksOn($staticList) {

        $this->ClicksOnStaticListName($staticList);
    }

    /**
     * @When user add student :arg1 to the list
     */
    public function userAddStudentToTheList($StdName) {
        $this->AddStudentInStaticList($StdName);
    }

    /**
     * @Then user is able to view student :arg1 in the list
     */
    public function userIsAbleToViewStudentInTheList($std_Name) {
        $this->VerifyStdIsVisibleInList($std_Name);
    }
    
    /**
     * @Then user is able to see :arg1 for the :arg2 in the table
     */
     public function userIsAbleToSeeForTheInTheTable($count,$staticListName)
     {
        $this->ViewStdCountInList($count,$staticListName);
     }

    /**
     * @When user deletes the static List with :arg1
     */
    public function userDeletesTheStaticListWith($type) {
        $this->ClickOnDeleteIcon($type);
    }

    /**
     * @Then user is not able to view the deleted static List on page
     */
    public function userIsNotAbleToViewTheDeletedStaticListOnPage() {
        $get = new \WebAppTestData();
        $staticList = $get->getTestData($this);
        $this->StaticListNotDisplayed($staticList['EditedStaticList']);
    }

////////////////////////////// Implemenation ////////////////////////////////////////


    public function clickOnCreateStaticList() {
        $I=$this;
        $I->click($I->Element("CreateStaticListBtn", "StaticListPage"));
        $I->WaitForModalWindowToAppear($I);
    }

    public function FillData($staticName, $staticListDesc) {
        $I = $this;
        $staticListName = $staticName . rand(800, 1800);
        $staticListDescription = $staticListDesc . rand(800, 1800);
        $I->fillField($I->Element("ListName", "StaticListPage"), $staticListName);
        $I->writeDataInJson($this, 'StaticListName', $staticListName);
        $I->fillField($I->Element("DescriptionName", "StaticListPage"), $staticListDescription);
        $I->click($I->Element("CreateListBtn", "StaticListPage"));
    }

    public function EditData($arg1, $arg2) {
        $I = $this;
        $staticListName = $arg1 . rand(800, 1800);
        $I->fillField($I->Element("ListName", "StaticListPage"), $staticListName);
        $I->writeDataInJson($this, 'EditedStaticList', $staticListName);
        $I->fillField($I->Element("DescriptionName", "StaticListPage"), $arg2);
        $I->click($I->Element("EditList", "StaticListPage"));
    }

    public function StaticListDisplaysOnThePage($type) {
        $I = $this;
        if (strpos($type,"Create")!==false) {
            $StaticListName = $I->getDataFromJson($this, "StaticListName");
        } else {
            $StaticListName = $I->getDataFromJson($this, "EditedStaticList");
        }
        $I->canSeeElement(str_replace("{{}}", $StaticListName, $I->Element('StaticListInList', 'StaticListPage')));
    }

    public function ClickOnEditIcon() {
        $I = $this;
        $staticList = $I->getDataFromJson($this, "StaticListName");
        $I->click(str_replace("{{}}", $staticList, $I->Element('editicon', 'StaticListPage')));
        $I->WaitForModalWindowToAppear($I);
    }

    public function ClickOnDeleteIcon($type) {
        $I = $this;
        if (strpos($type, "Edited") !== false) {
            $StaticList = $I->getDataFromJson($this, "EditedStaticList");
        } else {
            $StaticList = $I->getDataFromJson($this, "StaticListName");
        }
        $I->click(str_replace("{{}}", $StaticList, $I->Element('Deleteicon', 'StaticListPage')));
        $I->WaitForModalWindowToAppear($I);
        $I->click($I->Element("DeleteListBtn", "StaticListPage"));
    }

    public function StaticListNotDisplayed($StaticList) {
        $I = $this;
        $I->dontSeeElement(str_replace("{{}}", $StaticList, $I->Element('StaticListInList', 'StaticListPage')));
    }

    public function ShareList($PersonName) {
        $I = $this;
        $StaticList = $I->getDataFromJson($this, "EditedStaticList");
        $I->click(str_replace("{{}}", $StaticList,$I->Element("ShareIcon", "StaticListPage")));
        $I->WaitForModalWindowToAppear($I);
        $I->fillField($I->Element("whoItisFor", "StaticListPage"), $PersonName);
        $I->click(str_replace("{{}}", $PersonName, $I->Element("SelectSearchUser", "StaticListPage")));
        $I->click($I->Element("shareList", "StaticListPage"));
    }

    public function ClicksOnStaticListName($staticListName) {
        $I = $this;
        if (strpos($staticListName, "Edited")!==false) {
            $StaticList = $I->getDataFromJson($this, "EditedStaticList");
        } else {
            $StaticList = $I->getDataFromJson($this, "StaticListName");
        }

        $I->click(str_replace("{{}}", $StaticList, $I->Element("StaticListInList", "StaticListPage")));
        $I->WaitForPageToLoad($I);
    }

    public function AddStudentInStaticList($StdName) {
        $I = $this;
        $I->fillField($I->Element("searchStdToAdd", "StaticListPage"), $StdName);
        $I->click(str_replace("{{}}", $StdName, $I->Element("SelectStd", "StaticListPage")));
        $I->click($I->Element("AddToList", "StaticListPage"));
    }

    public function VerifyStdIsVisibleInList($std_Name) {
        $I = $this;
        $I->canSeeElement(str_replace("{{}}", $std_Name, $I->Element("StdInList", "StaticListPage")));
    }

    public function NavigateBackToStaticPage() {
        $I = $this;
        $I->click($I->Element("StaticListLink", "StaticListPage"));
        $I->WaitForPageToLoad($I);
    }

    public function ViewStdCountInList($count,$staticListName) {
        $I = $this;
        if (strpos($staticListName, "Edited")!==false) {
            $StaticList = $I->getDataFromJson($this, "EditedStaticList");
        } else {
            $StaticList = $I->getDataFromJson($this, "StaticListName");
        }
        $I->NavigateBackToStaticPage();
        $I->canSeeElement(str_replace("{{}}", $StaticList, str_replace("<<>>", $count, $I->Element("StdCountInList", "StaticListPage"))));
    }

}
