<?php

namespace Step\Acceptance;

class ManageStudentStep extends \AcceptanceTester {

    /**
     * @When user grabs student,participant and active participant count
     */
    public function userGrabsStudentparticipantAndActiveParticipantCount() {
        $this->GrabCounts();
    }

    /**
     * @When user clicks on AddStudent button
     */
    public function userClicksOnAddStudentButton() {
        $this->AddStudentBtn();
    }

    /**
     * @When user fills :arg1 as LDAPUserName
     */
    public function userFillsAsLDAPUserName($LdapName) {
        $this->FillLDAPData($LdapName);
    }

    /**
     * @Then count of the student,participant and active participant increases by one
     */
    public function countOfTheStudentParticipantAndActiveParticipantIncreasesByOne() {
        $this->CountIncreases();
    }

    /**
     * @When user selects :arg1 as inactive option
     */
    public function userSelectsAsInactiveOption($option) {
        $this->CheckInActive($option);
    }

    /**
     * @When user selects :arg1 as NonParticipating option
     */
    public function userSelectsAsNonParticipatingOption($option) {
        $this->CheckNotParticipating($option);
    }

    /**
     * @When user clicks on Search button
     */
    public function userClicksOnSearchButton() {
        $this->ClickSearchBtn();
    }

    /**
     * @When user searches the :arg1 user
     */
    public function userSearchesTheUser($user) {
        $this->SearchUser($user);
    }

    /**
     * @Then user is able to view the user's created data
     */
    public function userIsAbleToViewTheUsersCreatedData() {
        $this->UserDetailsAreDisplaying("create");
    }

    /**
     * @Then user is able to view the user's edited data
     */
    public function userIsAbleToViewTheUsersEditedData() {
        $this->UserDetailsAreDisplaying("edit");
    }


    /////////////////////Implementation///////////////

    public function GrabCounts() {
        $I = $this;
        $TotalCount = $I->grabTextFrom($I->Element("mainStdCount", "ManageStudentPage"));
        $I->writeDataInJson($this, "TotalCount", $TotalCount);
        $ParticipantCount = $I->grabTextFrom($I->Element("participantCount", "ManageStudentPage"));
        $I->writeDataInJson($this, "ParticipantCount", $ParticipantCount);
        $ActiveParticipantCount = $I->grabTextFrom($I->Element("ActiveParticipantsCount", "ManageStudentPage"));
        $I->writeDataInJson($this, "ActiveParticipantCount", $ActiveParticipantCount);
    }

    public function AddStudentBtn() {
        $I = $this;
        $I->click($I->Element("AddStd", "ManageStudentPage"));
        $I->WaitForModalWindowToAppear($I);
    }

    public function ClickSearchBtn() {
        $I = $this;
        $I->click($I->Element("searchBtn", "ManageStudentPage"));
        $I->WaitForModalWindowToAppear($I);
    }

    public function FillLDAPData($Ldap) {
        $I = $this;
        $LdapUsername = $Ldap . rand(9, 10000);
        if (strpos($Ldap, "edited") !== false || strpos($Ldap, "Edited") !== false) {
            $I->writeDataInJson($this, "Edited_LdapUsername", $LdapUsername);
        } else {
            $I->writeDataInJson($this, "LdapUsername", $LdapUsername);
        }
        $I->fillField($I->Element("LdapUser", "ManageStudentPage"), $LdapUsername);
    }

    public function CheckInActive($option) {
        $I = $this;
        if ($option == "yes" || $option == "true") {
            $I->click($I->Element("MarkInActive", "ManageStudentPage"));
        }
    }

    public function CheckNotParticipating($option) {
        $I = $this;
        if ($option == "yes" || $option == "true") {
            $I->click($I->Element("NotParticipating", "ManageStudentPage"));
        }
    }

    public function CountIncreases() {
        $I = $this;
        $TotalCount = $I->grabTextFrom($I->Element("mainStdCount", "ManageStudentPage"));
        $earlierTotalCount = $I->getDataFromJson($this, "TotalCount");
        $I->assertEquals($TotalCount, $earlierTotalCount + 1);
        $ParticipantCount = $I->grabTextFrom($I->Element("participantCount", "ManageStudentPage"));
        $earlierParticipantCount = $I->getDataFromJson($this, "ParticipantCount");
        $I->assertEquals($ParticipantCount, $earlierParticipantCount + 1);
        $ActiveParticipantCount = $I->grabTextFrom($I->Element("ActiveParticipantsCount", "ManageStudentPage"));
        $earlierActiveParticipantCount = $I->getDataFromJson($this, "ActiveParticipantCount");
        $I->assertEquals($ActiveParticipantCount, $earlierActiveParticipantCount + 1);
    }

    public function SearchUser($user) {
        $I = $this;
        if (strpos($user, "edited") !== false || strpos($user, "Edited") !== false) {
            $user = $I->getDataFromJson($this, "Edited_FirstName");
        } else {
            $user = $I->getDataFromJson($this, "FirstName");
        }
        $I->fillField($I->Element("SearchStd", "ManageStudentPage"), $user);
        $I->click($I->Element("SearchIcon", "ManageStudentPage"));
    }

    public function UserDetailsAreDisplaying($type) {
        $I = $this;
        $IDNum = $I->getDataFromJson($this, "IDNum");
        if ($type == "create") {
            $FirstName = $I->getDataFromJson($this, "FirstName");
            $LastName = $I->getDataFromJson($this, "LastName");
            $Title = $I->getDataFromJson($this, "Title");
            $EnclosedTitle = '(' . $Title . ')';
            $ContactInfo = $I->getDataFromJson($this, "ContactInfo");
            $LDAPInfo=$I->getDataFromJson($this, "LdapUsername");
        } else {
            $FirstName = $I->getDataFromJson($this, "Edited_FirstName");
            $LastName = $I->getDataFromJson($this, "Edited_LastName");
            $Title = $I->getDataFromJson($this, "Edited_Title");
            $EnclosedTitle = '(' . $Title . ')';
            $ContactInfo = $I->getDataFromJson($this, "Edited_ContactInfo");
            $LDAPInfo=$I->getDataFromJson($this, "Edited_LdapUsername");
        }
        $I->canSeeElement(str_replace("{{}}", $FirstName, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
        $I->canSeeElement(str_replace("{{}}", $LastName, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
        $I->canSeeElement(str_replace("{{}}", $ContactInfo, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
        $I->canSeeElement(str_replace("{{}}", $IDNum, $I->Element("coordinatorDetails", "CoordinatorUserMgmtPage")));
      
    }


}
