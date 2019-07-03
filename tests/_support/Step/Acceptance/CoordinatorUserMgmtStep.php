<?php

namespace Step\Acceptance;

class CoordinatorUserMgmtStep extends \AcceptanceTester {

    /**
     * @Then user is able to view Add new button and or choose an existing user link
     */
    public function userIsAbleToViewAddNewButtonAndOrChooseAnExistingUserLink() {
        $this->UserIsAbleToSeeButtonAndLinkUnderPanel();
    }

    /**
     * @When user grabs count and clicks on Add New button
     */
    public function userGrabsCountAndClicksOnAddNewButton() {

        $this->ClickOnAddNewCoordinatorBTn();
    }

    /**
     * @When user fills :arg1 as first Name
     */
    public function userFillsAsFirstName($firstName) {
        $this->FillFirstName($firstName);
    }

    /**
     * @When user fills :arg1 as last Name
     */
    public function userFillsAsLastName($LastName) {
        $this->FilllastName($LastName);
    }

    /**
     * @When user fills :arg1 as title
     */
    public function userFillsAsTitle($Title) {
        $this->FillTitle($Title);
    }

    /**
     * @When user fills :arg1 as contactInfo
     */
    public function userFillsAsContactInfo($contact) {
        $this->FillEmail($contact);
    }

    /**
     * @When user fills :arg1 as phoneNumber
     */
    public function userFillsAsPhoneNumber($phone) {
        $this->FillPhoneNumber($phone);
    }

    /**
     * @When user fills :arg1 as ID
     */
    public function userFillsAsID($ID) {
        $this->FillID($ID);
    }

    /**
     * @When user selects :arg1 option
     */
    public function userSelectsOption($MobileOption) {
        $this->CheckMobile($MobileOption);
    }

    /**
     * @When user selects :arg1 as Coordinator Type
     */
    public function userSelectsAsCoordinatorType($Role) {
        $this->SelectCoordinatorType($Role);
    }

    /**
     * @When user clicks on Cancel button
     */
    public function userClicksOnCancelButton() {
        $this->UserClicksOnCancelBtn();
    }

    /**
     * @Then user is not able to see the added coordinator
     */
    public function userIsNotAbleToSeeTheAddedCoordinator() {
        $this->CoordinatorDetailsAreNotDisplaying();
    }

    /**
     * @When user clicks on save button to save user
     */
    public function userClicksOnSaveButton() {
        $this->UserClicksOnSaveBtn();
    }

    /**
     * @Then user is able to see the added coordinator in the list
     */
    public function userIsAbleToSeeTheAddedCoordinatorInTheList() {
        $this->CoordinatorDetailsAreDisplaying("create");
    }

    /**
     * @When user clicks on Edit icon
     */
    public function userClicksOnEditIcon() {
        $this->ClickOnEditIcon();
    }

    /**
     * @Then user is able to see the edited coordinator in the list
     */
    public function userIsAbleToSeeTheEditedCoordinatorInTheList() {
        $this->CoordinatorDetailsAreDisplaying("edited");
    }

    /**
     * @Then Count of the coordinator remains same
     */
    public function countOfTheCoordinatorRemainsSame() {
        $this->CountisNotIncreased();
    }

    /**
     * @Then count of the coordinator increases by one
     */
    public function countOfTheCoordinatorIncreasesByOne() {
        $this->CountisIncreased();
    }

    /**
     * @When user clicks on Delete icon
     */
    public function userClicksOnDeleteIcon() {
        $this->ClickOnDeleteIcon();
    }

    /**
     * @When Click on Remove button displayed on the modal window
     */
    public function clickOnRemoveButtonDisplayedOnTheModalWindow() {
        $this->clickonRemoveBtn();
    }
 
    
    //////////////////////////Implementations/////////////////////

    public function UserIsAbleToSeeButtonAndLinkUnderPanel() {
        $I = $this;
        $I->canSeeElement($I->Element("addNewCoordinatorBTn", "CoordinatorUserMgmtPage"));
        $I->canSeeElement($I->Element("ExistingUserLink", "CoordinatorUserMgmtPage"));
    }

    public function ClickOnAddNewCoordinatorBTn() {
        $I = $this;
        $I->GrabCount();
        $I->click($I->Element("addNewCoordinatorBTn", "CoordinatorUserMgmtPage"));
        $I->WaitForModalWindowToAppear($I);
    }

    public function UserClicksOnCancelBtn() {
        $I = $this;
        $I->click($I->Element("CancelBtn", "CoordinatorUserMgmtPage"));
    }

    public function UserClicksOnSaveBtn() {
        $I = $this;
        $I->click($I->Element("SaveBtn", "CoordinatorUserMgmtPage"));
    }

    public function CoordinatorDetailsAreDisplaying($type) {
        $I = $this;
        $PhoneNum = $I->getDataFromJson($this, "PhoneNum");
        $Type = $I->getDataFromJson($this, "Type");
        if ($type == "create") {
            $FirstName = $I->getDataFromJson($this, "FirstName");
            $LastName = $I->getDataFromJson($this, "LastName");
            $Title = $I->getDataFromJson($this, "Title");
            $EnclosedTitle = '(' . $Title . ')';
            $ContactInfo = $I->getDataFromJson($this, "ContactInfo");
        } else {
            $FirstName = $I->getDataFromJson($this, "Edited_FirstName");
            $LastName = $I->getDataFromJson($this, "Edited_LastName");
            $Title = $I->getDataFromJson($this, "Edited_Title");
            $EnclosedTitle = '(' . $Title . ')';
            $ContactInfo = $I->getDataFromJson($this, "Edited_ContactInfo");
        }
        $I->canSeeElement(str_replace("{{}}", $FirstName, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
        $I->canSeeElement(str_replace("{{}}", $LastName, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
        $I->canSeeElement(str_replace("{{}}", $EnclosedTitle, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
        $I->canSeeElement(str_replace("{{}}", $ContactInfo, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
        $I->canSeeElement(str_replace("{{}}", $PhoneNum, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
        $I->canSeeElement(str_replace("<<>>", $FirstName, str_replace("{{}}", $Type, $I->Element("CoordinatorType", "CoordinatorUserMgmtPage"))));
    }

    public function CoordinatorDetailsAreNotDisplaying() {
        $I = $this;
        $FirstName = $I->getDataFromJson($this, "FirstName");
        $LastName = $I->getDataFromJson($this, "LastName");
        $Title = $I->getDataFromJson($this, "Title");
        $EnclosedTitle = '(' . $Title . ')';
        $ContactInfo = $I->getDataFromJson($this, "ContactInfo");
        $PhoneNum = $I->getDataFromJson($this, "PhoneNum");
        $IDNum = $I->getDataFromJson($this, "IDNum");
        $Type = $I->getDataFromJson($this, "Type");
        $I->cantSeeElement(str_replace("{{}}", $FirstName, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
        $I->cantSeeElement(str_replace("{{}}", $LastName, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
        $I->cantSeeElement(str_replace("{{}}", $EnclosedTitle, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
        $I->cantSeeElement(str_replace("{{}}", $ContactInfo, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
        $I->cantSeeElement(str_replace("{{}}", $PhoneNum, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
        $I->cantSeeElement(str_replace("{{}}", $IDNum, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
        $I->cantSeeElement(str_replace("<<>>", $FirstName, str_replace("{{}}", $Type, $I->Element("CoordinatorType", "CoordinatorUserMgmtPage"))));
    }

    public function GrabCount() {
        $I = $this;
        $Count = $I->grabTextFrom($I->Element("CoordinatorCount", "CoordinatorUserMgmtPage"));
        $I->writeDataInJson($this, "Count", $Count);
    }

    public function CountisNotIncreased() {
        $I = $this;
        $Count = $I->grabTextFrom($I->Element("CoordinatorCount", "CoordinatorUserMgmtPage"));
        $EarlierCount = $I->getDataFromJson($this, "Count");
        $I->assertEquals($Count, $EarlierCount);
    }

    public function CountisIncreased() {
        $I = $this;
        $Count = $I->grabTextFrom($I->Element("CoordinatorCount", "CoordinatorUserMgmtPage"));
        $EarlierCount = $I->getDataFromJson($this, "Count");
        $I->assertEquals($Count, $EarlierCount + 1);
        $I->writeDataInJson($this, "Count", $EarlierCount + 1);
    }

    public function ClickOnEditIcon() {
        $I = $this;
        $FirstName = $I->getDataFromJson($this, "FirstName");
        $I->click(str_replace("{{}}", $FirstName, $I->Element("EditIcon", "CoordinatorUserMgmtPage")));
        $I->WaitForModalWindowToAppear($I);
    }

    public function ClickOnDeleteIcon() {
        $I = $this;
        $FirstName = $I->getDataFromJson($this, "Edited_FirstName");
        $I->click(str_replace("{{}}", $FirstName, $I->Element("DeleteIcon", "CoordinatorUserMgmtPage")));
        $I->WaitForModalWindowToAppear($I);
    }

    public function clickonRemoveBtn() {
        $I = $this;
        $I->click($I->Element("RemoveBtn", "CoordinatorUserMgmtPage"));
    }

    public function FillFirstName($first) {
        $I = $this;
        $firstName = $first . rand(9, 1000);
        if (strpos($first, "edited") !== false || strpos($first, "Edited") !== false) {
            $I->writeDataInJson($this, "Edited_FirstName", $firstName);
        } else {
            $I->writeDataInJson($this, "FirstName", $firstName);
        }
        $I->fillField($I->Element("FirstName", "CoordinatorUserMgmtPage"), $firstName);
    }

    public function FilllastName($last) {
        $I = $this;
        $lastName = $last . rand(9, 1000);
        if (strpos($last, "edited") !== false || strpos($last, "Edited") !== false) {
            $I->writeDataInJson($this, "Edited_LastName", $lastName);
        } else {
            $I->writeDataInJson($this, "LastName", $lastName);
        }

        $I->fillField($I->Element("LastName", "CoordinatorUserMgmtPage"), $lastName);
    }

    public function FillTitle($title) {
        $I = $this;
        $Title = $title . rand(0, 1000);
        if (strpos($title, "edited") !== false || strpos($title, "Edited") !== false) {
            $I->writeDataInJson($this, "Edited_Title", $Title);
        } else {
            $I->writeDataInJson($this, "Title", $Title);
        }
        $I->fillField($I->Element("Title", "CoordinatorUserMgmtPage"), $Title);
    }

    public function FillEmail($contact) {
        $I = $this;
        $ContactInfo = $contact . rand(0, 5000) . "@mailinator.com";
        if (strpos($contact, "edited") !== false || strpos($contact, "Edited") !== false) {
            $I->writeDataInJson($this, "Edited_ContactInfo", $ContactInfo);
        } else {
            $I->writeDataInJson($this, "ContactInfo", $ContactInfo);
        }
        $I->fillField($I->Element("contactInfo", "CoordinatorUserMgmtPage"), $ContactInfo);
    }

    public function FillPhoneNumber($phone) {
        $I = $this;
        $PhoneNum = $phone . rand(9, 1000);
        $I->writeDataInJson($this, "PhoneNum", $PhoneNum);
        $I->fillField($I->Element("phone", "CoordinatorUserMgmtPage"), $PhoneNum);
    }

    public function CheckMobile($mobile) {
        $I = $this;
        if ($mobile == "yes" || $mobile == "true") {
            $I->click($I->Element("MobileCheckBox", "CoordinatorUserMgmtPage"));
        }
    }

    public function FillID($ID) {
        $I = $this;
        $IDNum = $ID . rand(9, 2000);
        $I->writeDataInJson($this, "IDNum", $IDNum);
        $I->fillField($I->Element("Id", "CoordinatorUserMgmtPage"), $IDNum);
    }

    public function SelectCoordinatorType($Role) {
        $I = $this;
        $I->writeDataInJson($this, "Type", $Role);
        $I->click($I->Element("TypeDropdown", "CoordinatorUserMgmtPage"));
        $I->waitForElementVisible(str_replace("{{}}", $Role, $I->Element("SelectType", "CoordinatorUserMgmtPage")));
        $I->click(str_replace("{{}}", $Role, $I->Element("SelectType", "CoordinatorUserMgmtPage")));
    }

   
    
    
    
}
