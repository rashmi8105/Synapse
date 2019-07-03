<?php

namespace Step\Acceptance;

class GroupStep extends \AcceptanceTester {

    public Static $paginationCount;


    /**
     * @Then user clicks on expand icon of :arg1 group
     */
    public function userClicksOnExpandIconOfGroup($typeofGroup) {
        $this->userClicksOnExpandIcon($typeofGroup);
    }

    /**
     * @When user clicks on :arg1 subgroup
     */
    public function userClickOnSubgroup($type) {
        $this->ClickOnSubGroup($type);
    }

    /**
     * @Then user is not able to see :arg1 in faculty section
     */
    public function userShouldNotSeeInFacultySection($Faculty) {
        $this->VerifyFaucltyisNotOnGroupPage($Faculty);
    }

    /**
     * @Then user is able to see :arg1 subgroup on group page
     */
    public function userIsAbleToSeeSubgroupOnEditGroupPage($TypeOfSubGroup) {
        $this->VerifySubGroupOnGroupPage($TypeOfSubGroup);
    }

    /**
     * @Then user is able to see :arg1 of subgroup on group summary page
     */
    public function userIsAbleToSeeSubgroupOnGroupSummaryPage($TypeOfSubGroup) {
        $this->VerifySubGroupOnGroupPage($TypeOfSubGroup);
    }

    /**
     * @Then user is able to see :arg1 in faculty section
     */
    public function userShouldSeeInFacultySection($Faculty) {
        $this->VerifyFaucltyOnGroupPage($Faculty);
    }

    /**
     * @When user clicks on Add Subgroup button inside the group
     */
    public function userClicksOnAddSubgroupButton() {

        $this->ClickOnSubGroupButton();
    }

    /**
     * @When user adds :arg1 with permission :arg2 to group
     */
    public function userAddsAsFaculty($FacultyName, $Permissionset) {
        $this->AddFacultyToTheGroup($FacultyName, $Permissionset);
    }

    /**
     * @Then user clicks on :arg1 group on group summary page
     */
    public function userClicksOnGroupOnSummary($typeOfGroup) {
        $this->ClickOnGroup($typeOfGroup);
    }

    /**
     * @Then user is able to see :arg1 on group summary page
     */
    public function userIsAbleToSeeOnGroupSummaryPage($Group) {
        $this->verifyGroupDetails($Group);
    }

    /**
     * @Then user is able to see :arg1 against :arg1 in Group list
     */
    public function userIsAbleToSeeAgainstInGroupList($GroupName, $GroupID) {

        $this->groupIDAgainstgroupName($GroupName, $GroupID);
    }

    /**
     * @Then user is able to see :arg1 group
     */
    public function userIsAbleToSeeGroup($GroupName) {
        $this->verifyGroupName($GroupName);
    }

    /**
     * @Then user is able to see :arg1
     */
    public function userIsAbleToSeeAn($ErrorMessage) {
        $this->seeError($ErrorMessage);
    }

    /**
     * @When user clicks on save button on group page
     */
    public function userClicksOnSaveButtonOnGroupPage() {
        $this->clickOnSaveButton();
    }

    /**
     * @When user clicks on save button on subgroup page
     */
    public function userClicksOnSaveButtonOnSubGroupPage() {
        $this->clickOnSaveButton();
    }

    /**
     * @When user fills :arg1 in GroupName field
     */
    public function userFillsInGroupNameField($GroupName) {
        $this->fillGroupName($GroupName);
    }

    /**
     * @When user fills :arg1 in GroupId field
     */
    public function userFillsInGroupIdField($GroupID) {
        $this->fillGroupID($GroupID);
    }

    /**
     * @When user clicks on cancel button on group page
     */
    public function userClicksOnCancelButtonOnGroupPage() {
        $this->clickOnCancelButton();
    }

    /**
     * @Then user is not able to see :arg1 on group summary page
     */
    public function userIsNotAbleToSeeOnGroupSummaryPage($TypeOfGroup) {
        $this->VerifyGroupIsNotDisplayed($TypeOfGroup);
    }

    /**
     * @When user gives :arg1 a new Permission set :arg2
     */
    public function userGivesANewPermissionSet($FacultyName, $Permissionset) {
        $this->GivePermission($FacultyName, $Permissionset);
    }

    /**
     * @When user sets faculty :arg1 as invisble
     */
    public function facultySetAsInvisble($FacultyName) {
        $this->SetInvisible($FacultyName);
    }

    /**
     * @When user clicks on Add Another Group button
     */
    public function userClicksOnAddAnotherGroupButton() {
        $this->clickonAddAnotherGroupButton();
    }

    /**
     * @When user clicks on cancel button on group create page
     */
    public function userClicksOnButtonOnAddAnotherGroupPage() {
        $this->clickOnCancelButton();
    }

    /**
     * @Then user is not able to see :arg1 group with groupid on group summary page
     */
    public function userShouldNotSeeGroupWithOnGroupSummaryPage($type) {
        $this->VerifyGroupIsNotDisplayed($type);
    }

    /**
     * @When user deletes :arg1 from the group
     */
    public function userDeletesFromTheGroup($Faculty) {
        $this->deleteFaculty($Faculty);
    }

    /**
     * @Then user see :arg1 groupid field
     */
    public function userSeeGroupid($type) {
        $this->VerifyGroupID($type);
    }

    /**
     * @Then user see :arg1 groupName field
     */
    public function userSeeGroupName($type) {
        $this->VerifyGroupName($type);
    }


    /**
     * @When user edit group name field with :arg1
     */
    public function userEditGroupNameFieldWith($groupName) {
        $this->userEditGroupNameField($groupName);
    }

    /**
     * @When user clicks on delete group button 
     */
    public function userClicksOnDeleteButton() {

        $this->DeleteGroup();
    }

    /**
     * @When user click on confirm button on Dialog box
     */
    public function userClickOnConfirmButtonOnDialogBoxForGroup() {
        $this->ConfirmDelete();
    }

    /**
     * @Then Faculty should be :arg1
     */
    public function facultyShouldBe($FacultyInvisible) {

        $this->VerifyVisibiltyOfFaculty($FacultyInvisible);
    }

    /**
     * @When user clicks on AllStudent group on group summary page
     */
    public function userClicksOnAllStudentGroupOnGroupSummaryPage() {
        $this->ClickOnGroup("All Students");
    }

    /**
     * @When user clicks on :arg1 button on group summary page
     */
    public function userClicksOnLink($typeOfUpload) {
        $this->ClickOnUploadLink($typeOfUpload);
    }

    /**
     * @Then user is able to see :arg1 as number of students in :arg2 group
     */
    public function userIsAbleToAsNumberOfStudentsInGroup($count, $TypeOfGroup) {
        $this->VerifyNumberOfStudentsInGroup($count, $TypeOfGroup);
    }
    

    /**
     * @When user clicks on upload student button on group edit page
     */
    public function userClicksOnLinkFromGroupEditPage() {

        $this->ClickOnUploadStudentLinkInsideGroup();
    }

    /**
     * @Then user is able to see :arg1 as number of students in :arg2 Subgroup
     */
    public function userIsAbleToSeeAsNumberOfStudentsInSubgroup($count, $TypeOfSubgroup) {
        $this->CheckNumberOfStudentOnGroupPage($count);
    }

    /**
     * @Given user is able to see Faculty as invisible
     */
    public function userIsAbleToSeeFacultyAsInvisible() {
        $this->VerifyFacultyVisibilty("TRUE");
    }

    /**
     * @When user add :arg1 student with :arg2 to :arg3 group via upload from inside the group
     */
    public function userAddStudentWithToGroupViaUploadFromInsideTheGroup($NumberOfstudent, $ExternalID, $TypeOfGroup) {
        $this->UploadStudentfromInsideTheGroup($ExternalID);
    }
    
    /**
     * @When user clicks on groups link to navigate to group summary page
     */
     public function userClicksOnGroupsLinkToNavigateToGroupSummaryPage()
     { 
         $this->ClickOngroupSummaryPageLink();
     }
     
 

    //////////////////////////////////////////////////////////////////////////// 

    public function UploadStudentfromInsideTheGroup($ExternalID) {
        $I = $this;
        $Write = new \ReadAndWriteCsvFile();
        $Write->WriteFileForUploadingStudentFromInsideTheGroup("UploadStudentFromInsideTheGroup.csv", $ExternalID);
        $I->UploadFiles($I, "UploadStudentFromInsideTheGroup.csv",2);
    }

    public function ClickOngroupSummaryPageLink() {
        $I = $this;
        $I->click($I->Element("GroupLinkToNavigateToGroupSummaryPage", "GroupPage"));
        $I->WaitForPageToLoad($I);
    }

    public function ClickOnUploadStudentLinkInsideGroup() {
        $I = $this;
        $I->click($I->Element("StudentUploadButtonInsideGroup", "GroupPage"));
        $I->waitForElement($I->Element("UploadWindow", "GroupPage"),60);
        $I->wait(3);
    }

    public function ClickOnUploadLink($typeOfUpload) {
        $I = $this;
        $I->click(str_replace("{{}}", "$typeOfUpload", $I->Element("UploadLink", "GroupPage")));
        $I->WaitForPageToLoad($I);
    }

    public function CheckNumberOfStudentOnGroupPage($count) {
        $I = $this;
        if ($count == 0) {
            $I->canSeeElement($I->Element("StudentTextWithZeroText", "GroupPage"));
        } else {
            $I->canSeeElement(str_replace("{{}}", $count, $I->Element("NumberOfStudentInGroup", "GroupPage")));
        }

        $I->canSeeElement(str_replace("{{}}", $count, $I->Element("NumberOfStudentsInGroupHeader", "GroupPage")));
    }

    public function VerifyNumberOfStudentsInGroup($count, $TypeOfGroup) {
        $I = $this;
        $I->amOnPage("#/groupsummary");
         $I->ClickOnGroup($TypeOfGroup);
        $I->CheckNumberOfStudentOnGroupPage($count);
    }

    public function VerifySubGroupOnGroupPage($TypeOfSubGroup, $TypeOfParentGroup = "Created") {
        $I = $this;
        if (strpos($TypeOfSubGroup, "Created") !== false) {
            if (strpos($TypeOfSubGroup, "ID") !== false) {
                $I->canSee($I->getDataFromJson($this, "SubGroupID"));
            } else {
                $I->canSee($I->getDataFromJson($this, "SubGroupName"));
            }
        } else {
            if (strpos($TypeOfSubGroup, "ID") !== false) {
                $I->canSee($I->getDataFromJson($this, "EditedSubGroupID"));
            } else {
                $I->canSee($I->getDataFromJson($this, "EditedSubGroupName"));
            }
        }
    }

    public function verifyGroupDetails($Group) {
        $I = $this;
        $I->amOnPage("#/groupsummary");
        if (strpos($Group, "Name") !== false) {
            $I->verifyGroupName($Group);
        } else {
            $I->verifyGroupID($Group);
        }
    }

    public function SetInvisible($FacultyName) {
        $I = $this;
        if(strpos($FacultyName,"ReferralAssigneeBehaviors")!==false)
        {
            $FacultyName=$I->getDataFromJson(new CoordinatorUserMgmtStep($I->getScenario()),"FirstName");
        }
        $I->click(str_replace("{{}}", $FacultyName, $I->Element("FacultyVisiblity", "GroupPage")));
    }

    public function ConfirmDelete() {

        $I = $this;
        $I->UserclicksOnDeleteButtonDisplayedOnDialogBox($I);
    }

    public function clickonAddAnotherGroupButton() {
        $I = $this;
        $I->click($I->Element("AddAnotherGroupButton", "GroupPage"));
        $I->WaitForPageToLoad($I);
    }

    public function fillGroupName($GroupName) {
        $I = $this;
        $NewGroupName = $GroupName . rand(0, 9999999);
        if (strpos($GroupName, "Edited") !== false) {
            if (strpos($GroupName, "Sub") !== false) {
                $I->writeDataInJson($this, "EditedSubGroupName", $NewGroupName);
            } else {
                $I->writeDataInJson($this, "EditedGroupName", $NewGroupName);
            }
        } else {
            if (strpos($GroupName, "Sub") !== false) {
                $I->writeDataInJson($this, "SubGroupName", $NewGroupName);
            } else {
                $I->writeDataInJson($this, "GroupName", $NewGroupName);
            }
        }
        $I->fillField($I->Element("GroupName", "GroupPage"), $NewGroupName);
    }

    public function fillGroupID($GroupID) {
        $I = $this;
        $NewGroupID = $GroupID . rand(0, 9999999);
        if (strpos($GroupID, "Edited") !== false) {
            if (strpos($GroupID, "Sub") !== FALSE) {
                $I->writeDataInJson($this, "EditedSubGroupID", $NewGroupID);
            } else {
                $I->writeDataInJson($this, "EditedGroupID", $NewGroupID);
            }
        } else {
            if (strpos($GroupID, "Sub") !== FALSE) {
                $I->writeDataInJson($this, "SubGroupID", $NewGroupID);
            } else {
                $I->writeDataInJson($this, "GroupID", $NewGroupID);
            }
        }
        $I->fillField($I->Element("GroupID", "GroupPage"), $NewGroupID);
    }

    public function clickOnCancelButton() {
        $I = $this;
        $I->click($I->Element("Cancel", "GroupPage"));
        $I->WaitForPageToLoad($I);
    }

    public function clickOnSaveButton() {

        $I = $this;
        $I->click($I->Element("SaveGroupButton", "GroupPage"));
    }

    public function AddFacultyToTheGroup($FacultyName, $Permissionset) {
        $I = $this;
        if (strpos($FacultyName, "ReferralAssigneeBehavior") !== false) {
            $FacultyName = $I->getDataFromJson(new CampusResoucesStep($I->getScenario()), "staffNameField");
        }
        $I->fillField($I->Element("Staff_Field", "GroupPage"), $FacultyName);
        $I->waitForElement(str_replace("{{}}", $FacultyName, $I->Element("DropDownValue", "GroupPage")),60);
        $I->click(str_replace("{{}}", $FacultyName, $I->Element("DropDownValue", "GroupPage")));
        $I->click($I->Element("AddStaffButton", "GroupPage"));
       $I->GivePermission($FacultyName, $Permissionset);
    }

    public function GivePermission($FacultyName, $Permissionset) {
        $I = $this;
        if (strpos($Permissionset, "PermissionForReport") !== false) {
            $Permission = $I->getDataFromJson(new PermissionStep($I->getScenario()), "PermissionName");
        } 
        elseif(strpos($Permissionset,"PermissionForESPRJ6356")!==false)
        {
         $Permission=$I->getDataFromJson(new PermissionStep($I->getScenario()),"PermissionName");   
            
        }
        else {
            $Permission = $Permissionset;
        }
        $I->click(str_replace("{{}}", $FacultyName, $I->Element("PermissionDropDown", "GroupPage")));
        $I->wait(2);
        $I->waitForElement(str_replace("{{}}", $FacultyName, str_replace("<<>>", $Permission, $I->Element("SelectPermission", "GroupPage"))),60);
       $I->ClickOnElementWithJS($I,str_replace("{{}}", $FacultyName, str_replace("<<>>", $Permission, $I->Element("SelectPermission", "GroupPage"))));
      //$I->click(str_replace("{{}}", $FacultyName, str_replace("<<>>", $Permission, $I->Element("SelectPermission", "GroupPage"))));
    
        
        }

    public function ClickOnGroup($type) {
        $I = $this;
        if (strpos($type, "Created") !== false) {
            $I->VerifyGroup($I->getDataFromJson($this, "GroupName"));
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "GroupID"), $I->Element("GroupLink", "GroupPage")));
        }
        if (strpos($type, "Edited") !== false) {
            $I->VerifyGroup($I->getDataFromJson($this, "EditedGroupName"));
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "EditedGroupID"), $I->Element("GroupLink", "GroupPage")));
        }
        if (strpos($type, "All Students") !== false) {
            $I->verifyGroup("All Students");
            $I->click(str_replace("{{}}", "ALLSTUDENTS", $I->Element("GroupLink", "GroupPage")));
        }

        $I->WaitForPageToLoad($I);
    }

    public function VerifyFaucltyOnGroupPage($Faculty) {
        $I = $this;
        if(strpos($Faculty,"ReferralAssigneeBehaviors")!==false)
        {
            $Faculty=$I->getDataFromJson(new CoordinatorUserMgmtStep($I->getScenario()),"FirstName");
        }
        $I->canSeeElement(str_replace("{{}}", $Faculty, $I->Element("FacultyonGroupPage", "GroupPage")));
    }

    public function VerifyFaucltyisNotOnGroupPage($Faculty) {
        $I = $this;
        $I->cantSeeElement(str_replace("{{}}", $Faculty, $I->Element("FacultyonGroupPage", "GroupPage")));
    }

    public function ClickOnSubGroupButton() {
        $I = $this;
        $I->click($I->Element("AddSubgroup", "GroupPage"));
        $I->WaitForPageToLoad($I);
    }

    public function userClicksOnExpandIcon($typeofGroup) {
        $I = $this;
        if(strpos($typeofGroup,"Created")!==FALSE) {
            $I->VerifyGroup($I->getDataFromJson($this, "GroupName"));
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "GroupName"), $I->Element("GroupExpandIcon", "GroupPage")));
        } else {
            $I->VerifyGroup($I->getDataFromJson($this, "EditedGroupName"));
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "EditedGroupName"), $I->Element("GroupExpandIcon", "GroupPage")));
        }
    }

    public function seeSubGroupOnGroupList($typeofsubGroup) {
        $I = $this;
        if ($typeofsubGroup == "created") {
            $I->waitForElement(str_replace("{{}}", $I->getDataFromJson($this, "SubGroupName"), $I->Element("subgroupOnGroupSummaryPage", "GroupPage")),60);
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "SubGroupName"), $I->Element("subgroupOnGroupSummaryPage", "GroupPage")));
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "SubGroupID"), $I->Element("subgroupIDonGroupSummaryPage", "GroupPage")));
        } else {
            $I->waitForElement(str_replace("{{}}", $I->getDataFromJson($this, "EditedSubGroupName"), $I->Element("subgroupOnGroupSummaryPage", "GroupPage")),60);
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "EditedSubGroupName"), $I->Element("subgroupOnGroupSummaryPage", "GroupPage")));
            if ($typeofsubGroup == "editedViaUpload") {
                $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "EditedSubGroupID"), $I->Element("subgroupIDonGroupSummaryPage", "GroupPage")));
            }
        }
    }

    public function deleteFaculty($Faculty) {
        $I = $this;
        $I->waitForElement(str_replace("{{}}", $Faculty, $I->Element("removeStaffWithName", "GroupPage")),60);
        $I->click(str_replace("{{}}", $Faculty, $I->Element("removeStaffWithName", "GroupPage")));
    }

    public function groupIDAgainstgroupName($GroupName, $GroupID) {

        $I = $this;
        if (strpos($GroupName, "Created") !== false) {
            if (strpos($GroupID, "Created") !== false) {
                $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "GroupName"), str_replace("<<>>", $I->getDataFromJson($this, "GroupID"), $I->Element("GroupIdInList", "GroupPage"))));
            }
        }
    }

    public function seeError($error) {

        $I = $this;
        $I->seeElement(str_replace("{{}}", $error, $I->Element("Error", "GroupPage")));
    }

       public function DeleteGroup() {
        $I = $this;
        $I->waitForElement($I->Element("DeleteGroup", "GroupPage"),60);
        $I->click($I->Element("DeleteGroup", "GroupPage"));
    }

    public function ClickOnSubGroup($type) {
        $I = $this;
        if (strpos($type, "Created") !== false) {
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "SubGroupName"), $I->Element("subgroupOnGroupSummaryPage", "GroupPage")));
        } else {
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "EditedSubGroupName"), $I->Element("subgroupOnGroupSummaryPage", "GroupPage")));
        }

        $I->WaitForPageToLoad($I);
    }

    public function VerifyVisibiltyOfFaculty($FacultyInvisible) {
        $I = $this;
        if (((strcasecmp($FacultyInvisible, 'True') == 0) || (strcasecmp($FacultyInvisible, 'yes') == 0))) {
            $I->waitForElement(str_replace("{{}}", "true", $I->Element("VisbliltyCheckBox", "GroupPage")),60);
            $I->canSeeElement(str_replace("{{}}", "true", $I->Element("VisbliltyCheckBox", "GroupPage")));
        } else {
            $I->waitForElement(str_replace("{{}}", "false", $I->Element("VisbliltyCheckBox", "GroupPage")),60);
            $I->canSeeElement(str_replace("{{}}", "false", $I->Element("VisbliltyCheckBox", "GroupPage")));
        }
    }

    public function VerifyGroupIsNotDisplayed($TypeOfGroup) {
        $I = $this;
        $I->amOnPage("#/groupsummary"); 
        $I->WaitForPageToLoad($I);
        if (strpos($TypeOfGroup, "Created") !== false) {
            if(strpos($TypeOfGroup,"Name")!==false){
            $GroupName = $I->getDataFromJson($this, "GroupName");
            }
            else
            {
            $GroupName = $I->getDataFromJson($this, "GroupID");
            }
            
            }
        if (strpos($TypeOfGroup, "Edited") !== false) {
            if(strpos($TypeOfGroup,"Name")!==false){
            $GroupName = $I->getDataFromJson($this, "EditedGroupName");
            }
            else
            {
             $GroupName = $I->getDataFromJson($this, "EditedGroupID");
            }
        }

        if ($I->isElementDisplayed($I, $I->Element("paginationBar", "GroupPage"))) {
            $I->executeInSelenium(function (\Facebook\WebDriver\WebDriver $webDriver) {

                self::$paginationCount = $webDriver->findElements(\WebDriverBy::xpath('//ul[contains(@class,"pagination")]//li[contains(@ng-repeat,"pageNumber")]'));
                //$webDriver->findElement(\WebDriverBy::xpath("//input[@type='file']"))->sendKeys(realpath(__DIR__ . '/../../..') . '\uploadFiles\UploadStudentInGroup.csv');
            });

            for ($page = 1; $page <= count(self::$paginationCount); $page++) {
                if ($page > 1) {
                    $I->click(str_replace("{{}}", $page, $I->Element("Page", "GroupPage")));
                    $I->wait(3);
                }
                $I->cantSee($GroupName);
            }
        } else {
            $I->cantSee($GroupName);
        }
    }

    public function verifyGroupID($GroupID) {
        $I = $this;
        if (strpos($GroupID, 'Created') !== false) {
            $GroupName = $I->getDataFromJson($this, "GroupName");
        }
        if (strpos($GroupID, 'Edited') !== false) {
            $GroupName = $I->getDataFromJson($this, "EditedGroupName");
        }
        $I->VerifyGroup($GroupName);
        $I->canSee($GroupID);
    }

    public function verifyGroupName($GroupName) {
        $I = $this;
        if (strpos($GroupName, 'Created') !== false) {
            $GroupName = $I->getDataFromJson($this, "GroupName");
        } elseif (strpos($GroupName, 'Edited') !== false) {
            $GroupName = $I->getDataFromJson($this, "EditedGroupName");
        } else {
            $GroupName = $GroupName;
        }
        $I->VerifyGroup($GroupName);
        $I->canSee($GroupName);
    }

    public function VerifyGroup($GroupName) {
        $I = $this;
        codecept_debug($GroupName);
        $I->wait(3); //Due to latency issue                                                                   //Added due to performance issues with the Application
        if ($I->isElementDisplayed($I, $I->Element("paginationBar", "GroupPage"))) {
            $I->executeInSelenium(function (\Facebook\WebDriver\WebDriver $webDriver) {
                self::$paginationCount = $webDriver->findElements(\WebDriverBy::xpath('//ul[contains(@class,"pagination")]//li[contains(@ng-repeat,"pageNumber")]'));
                //$webDriver->findElement(\WebDriverBy::xpath("//input[@type='file']"))->sendKeys(realpath(__DIR__ . '/../../..') . '\uploadFiles\UploadStudentInGroup.csv');
            });
            for ($page = 1; $page <= count(self::$paginationCount); $page++) {
                if ($page > 1) { // To click on the paginations
                    $I->click(str_replace("{{}}", $page, $I->Element("Page", "GroupPage")));
                }
                for ($row = 1; $row <= 25; $row++) {
                    $Group = $I->grabTextFrom('//table//tbody//tr[' . $row . ']/td[2]//span[@class="group-name"]/a');
                    codecept_debug($Group);
                    if ($Group == $GroupName) {
                        return;
                    }
                }
            }
        } else {
            for ($row = 1; $row <= 25; $row++) {//To grab the text of each row containing Permission Set on the page.
                $Group = $I->grabTextFrom('//table//tbody//tr[' . $row . ']/td[2]//span[@class="group-name"]');
                if ($Group == $GroupName) {
                    return;
                }
            }
        }
    }

    public function VerifyFacultyVisibilty($Isinvisible) {
        $I = $this;
        $I->wait(3);
        if (strpos($Isinvisible, "TRUE") !== false) {

            $I->assertTrue($I->isElementDisplayed($I, $I->Element("VisibilityCheckBox", "GroupPage")));
        } else {
            $I->cantSeeElement($I->Element("VisibilityCheckBox", "GroupPage"));
        }
    }

}
