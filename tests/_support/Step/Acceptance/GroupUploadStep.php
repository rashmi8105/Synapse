<?php

namespace Step\Acceptance;

class GroupUploadStep extends \AcceptanceTester {

    /**
     * @When user uploads a subgroup with :arg1 and :arg2 to :arg1 group
     */
    public function userUploadsASubgroupWithAndToGroup($subgroupName, $SubgroupID, $ParentGroupType) {
        $this->UploadSubgroup($subgroupName, $SubgroupID, $ParentGroupType);
    }

    /**
     * @Given user uploads a subgroup with :arg1 and :arg2 to :arg3 group
     */
    public function userUploadsASubgroupToGroup($SubgroupID, $SubgroupName, $ParentGroupType) {
        $this->UploadSubgroup($SubgroupID, $SubgroupName, $ParentGroupType, 0);
    }

    /**
     * @When user uploads subgroup with invalid parentID
     */
    public function userUploadsSubgroupWithInvalidParentID() {
        $this->UploadInvalidSubgroup("InvalidSubgroupID", "InvalidSubgroupGroup");
    }

    /**
     * @When user adds :arg1 student to :arg2 group via upload
     */
    public function userAddStudentToGroupViaUpload($count, $GroupType) {

        $this->UploadStudentToGroup($count,$GroupType);
    } 
    

    /**
     * @When user removes :arg1 student from :arg2 group via upload
     */
    public function userRemovesStudentFromGroup($count, $GroupType) {

        $this->DeleteStudentFromGroup($count, $GroupType);
    }

    /**
     * @When user adds :arg1 student to :arg2 group via upload from inside the group
     */
    public function userAddStudentToGroupViaUploadFromGroupEditPage($count, $Grouptype) {
        $this->uploadStudentFromGroupPage($count, $Grouptype);
    }

    /**
     * @When user adds :arg1 student to :arg2 subgroup via upload
     */
    public function userAddStudentToSubgroupViaUpload($count, $GroupType) {
        $this->UploadStudentToGroup($count, $GroupType, 1);
    }

    /**
     * @When user removes :arg1 student from :arg2 subgroup via upload
     */
    public function userRemovesStudentFromSubgroupViaUpload($count, $GroupType) {

        $this->DeleteStudentFromGroup($count, $GroupType, 1);
    }

    /**
     * @When user uploads an invalid format file
     */
    public function userUplodsAnInvalidFormatFile() {
        $this->UploadInvaliddFile();
    }

    /**
     * @Then user is able to see an :arg1
     */
    public function userSeesAn($ErrorMessage) {

        $this->seeInvalidFileTypeError($ErrorMessage);
    }

    /**
     * @When user uploads a file without externalID
     */
    public function userUploadsAFileWithoutExternalID() {
        $this->UploadFileWithOutExternalID();
    }

    /**
     * @When user uploads a subgroup with :arg1 and :arg1 to group with invalid Parent group ID
     */
    public function userUploadsASubgroupWithAndToGroupWithInvalidParentGroupID($subgroupName, $SubgroupID) {

        $this->UploadInvalidSubgroup($subgroupName, $SubgroupID);
    }

    /**
     * @When user uploads a duplicate subgroup 
     */
    public function userUploadsASubgroupWithAndExistingSubgroupID() {
        $I = $this;
        $I->UploadDuplicateSugroup();
    }

    /**
     * @Given user adds Faculty with details :arg1, :arg2 , :arg3, :arg4 , :arg5 , :arg6 , :arg6 to :arg8 group via upload
     */
    public function userAddsFacultyWithDetailsToGroupViaUpload($ExternalID, $FirstName, $LastName, $Email, $Permission, $invisible, $Remove, $TypeOfGroup) {
        $this->uploadFaculty($ExternalID, $FirstName, $LastName, $Email, $Permission, $invisible, $Remove, $TypeOfGroup);
    }

    /**
     * @Given user removes Faculty with details :arg1, :arg2 , :arg3, :arg4 , :arg5 , :arg6 , :arg7 from :arg8 group via upload
     */
    public function userRemovesFacultyWithDetailsFromGroupViaUpload($ExternalID, $FirstName, $LastName, $Email, $Permission, $invisible, $Remove, $TypeOfGroup) {
        $this->uploadFaculty($ExternalID, $FirstName, $LastName, $Email, $Permission, $invisible, $Remove, $TypeOfGroup);
    }

////////////////////////////////////////////////////////////////////////////




    public function UploadStudentToGroup($count, $GroupType, $isSubgroup = 0) {
        $I = $this;
        $WriteCsvFile = new \ReadAndWriteCsvFile();
        if (strpos($GroupType, "Created") !== false) {
            if (strpos($GroupType, "GroupHierarchy") !== false) {
                if ($isSubgroup == 0) {
                    $WriteCsvFile->WriteDataForUploadingStudentToGroup($count, "AddStudentToGroupUploadESPRJ9742.csv", $I->getDataFromJson($this, "GroupID"), $I->getDataFromJson($this, "GroupID"));
                    $I->UploadFiles($I, "AddStudentToGroupUploadESPRJ9742.csv");
                } else {
                    $WriteCsvFile->WriteDataForUploadingStudentToGroup($count, "AddStudentToSubGroupUploadESPRJ9742.csv", $I->getDataFromJson($this, "SubGroupID"), $I->getDataFromJson($this, "GroupID"), 3);
                    $I->UploadFiles($I, "AddStudentToSubGroupUploadESPRJ9742.csv");
                }
            } else {
                if ($isSubgroup == 0) {
                    $WriteCsvFile->WriteDataForUploadingStudentToGroup($count, "AddStudentToGroupUpload.csv", $I->getDataFromJson($this, "GroupID"), $I->getDataFromJson($this, "GroupID"));
                    $I->UploadFiles($I, "AddStudentToGroupUpload.csv");
                } else {
                    $WriteCsvFile->WriteDataForUploadingStudentToGroup($count, "AddStudentToSubGroupUpload.csv", $I->getDataFromJson($this, "SubGroupID"), $I->getDataFromJson($this, "GroupID"), 1);
                    $I->UploadFiles($I, "AddStudentToSubGroupUpload.csv");
                }
            }
        }
    }

    public function DeleteStudentFromGroup($count, $Grouptype, $isSubgroup = 0) {
        $I = $this;
        $Read = new \ReadAndWriteCsvFile();
        if ($Grouptype == "Created") {
            if ($isSubgroup == 0) {
                $Read->WriteDataForUploadingStudentToGroup($count, "DeleteStudentGroupUpload.csv", "#clear", $I->getDataFromJson($this, "GroupID"));
                $I->UploadFiles($I, "DeleteStudentGroupUpload.csv");
            } else {
                $Read->WriteDataForUploadingStudentToGroup($count, "DeleteStudentSubGroupUpload.csv", "#clear", $I->getDataFromJson($this, "SubGroupID"), 1);
                $I->UploadFiles($I, "DeleteStudentSubGroupUpload.csv");
            }
        }
    }

    public function uploadStudentFromGroupPage($count) {
        $I = $this;
        $read = new \ReadAndWriteCsvFile();
        $read->WriteStudentsForUploadingStudentFromGroupEditPage($count, "UploadStudentToGroup.csv");
        $I->DirectUploadFileInGroup($I, "UploadStudentToGroup.csv");
    }

    public function UploadInvaliddFile() {
        $I = $this;
        $I->UploadInvalidFile();
    }

    public function seeInvalidFileTypeError($ErrorMessage) {
        $I = $this; 
        $I->wait(3);
       $I->isElementDisplayed($I,$I->Element("InvalidFielTypeError","GroupPage"));
        $I->reloadPage();
        $I->WaitForPageToLoad($I);
    }

    public function UploadFileWithOutExternalID() {
        $I = $this;
        $Write = new \ReadAndWriteCsvFile();
        $Write->WriteStudentFileWithoutExternalID("WithoutExternalID.csv", $I->getDataFromJson($this, "GroupName"), $I->getDataFromJson($this, "GroupName"));
        $I->UploadFiles($I, "WithoutExternalID.csv");
    }

    public function UploadSubgroup($subgroupName, $SubgroupID, $ParentGroupType) {
        $I = $this;
        $Write = new \ReadAndWriteCsvFile();
        if (strpos($subgroupName, "Created") !== false) {
            if (strpos($SubgroupID, "Created") !== false) {
                $subgroupName = $subgroupName . rand(0, 99999);
                $SubgroupID = $SubgroupID . rand(0, 99999);
                $I->writeDataInJson($this, "SubGroupName", $subgroupName);
                $I->writeDataInJson($this, "SubGroupID", $SubgroupID);
            } else {
                $subgroupName = $I->getDataFromJson($this, "SubGroupName");
                $SubgroupID = $SubgroupID . rand(0, 99999);
            }
        } else {
            $SubgroupID = $I->getDataFromJson($this, "SubGroupID");
            $subgroupName = $subgroupName . rand(0, 99999);
            $I->writeDataInJson($this, "EditedSubGroupName", $subgroupName);
        }
        if (strpos($ParentGroupType, "Created") !== false) {
            $Write->WriteDataForSubGroup("SubGroupUploadToGroup.csv", $subgroupName, $SubgroupID, $I->getDataFromJson($this, "GroupID"));
        }

        $I->UploadFiles($I, "SubGroupUploadToGroup.csv");
    }

    public function UploadDuplicateSugroup() {
        $I = $this;
        $Write = new \ReadAndWriteCsvFile();
        $Write->WriteDataForSubGroup("DuplicateSubGroup.csv", $I->getDataFromJson($this, "SubGroupName"), $I->getDataFromJson($this, "SubGroupID"), $I->getDataFromJson($this, "GroupID"));
        $I->UploadFiles($I, "DuplicateSubGroup.csv");
    }

    public function UpdateSubgroupIDViaUpload() {
        $I = $this;
        $Write = new \ReadAndWriteCsvFile();
        $Write->WriteDataForSubGroup("UpdateSubGroupID.csv", $I->getDataFromJson($this, "SubGroupName"), "SubGroupID" . rand(0, 999999), $I->getDataFromJson($this, "GroupID"));
        $I->UploadFiles($I, "UpdateSubGroupID.csv");
    }

    public function seeSubGroupID($typeofsubGroup) {
        $I = $this;
        if ($typeofsubGroup == "created") {
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "SubGroupID"), $I->Element("subgroupIDonGroupSummaryPage", "GroupPage")));
        } else {
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "EditedSubGroupID"), $I->Element("subgroupIDonGroupSummaryPage", "GroupPage")));
        }
    }

    public function seeSubGroupNameOnList($typeofsubGroup) {
        $I = $this;
        if ($typeofsubGroup == "created") {
            $I->waitForElement(str_replace("{{}}", $I->getDataFromJson($this, "SubGroupName"), $I->Element("subgroupOnGroupSummaryPage", "GroupPage")),60);
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "SubGroupName"), $I->Element("subgroupOnGroupSummaryPage", "GroupPage")));
        } else {
            $I->waitForElement(str_replace("{{}}", $I->getDataFromJson($this, "EditedSubGroupName"), $I->Element("subgroupOnGroupSummaryPage", "GroupPage")),60);
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "EditedSubGroupName"), $I->Element("subgroupOnGroupSummaryPage", "GroupPage")));
        }
    }

    public function UploadInvalidSubgroup($subgroupName, $SubgroupID) {

        $I = $this;
        $Write = new \ReadAndWriteCsvFile();
        $Write->WriteDataForSubGroup("InvalidSubGroup.csv", $subgroupName, $SubgroupID, "InValidGroupID");
        $I->UploadFiles($I, "InvalidSubGroup.csv");
    }

    public function uploadFaculty($ExternalID, $FirstName, $LastName, $Email, $Permission, $invisible, $Remove, $GroupType, $IsSubgroup = 0) {
        $I = $this;
        $Write = new \ReadAndWriteCsvFile();
        if ($Remove == 0) {
            $remove = '';
            if (strpos($GroupType, "Created") !== false) {
                if ($IsSubgroup == 1) {
                    $Write->WriteDataForFacultyToGroup("AddFacultyToSubGroup.csv", $ExternalID, $FirstName, $LastName, $Email, $I->getDataFromJson($this, "SubGroupID"), $Permission, $invisible, $remove);
                    $I->UploadFiles($I, "AddFacultyToSubGroup.csv");
                } else {
                    $Write->WriteDataForFacultyToGroup("AddFacultyToGroup.csv", $ExternalID, $FirstName, $LastName, $Email, $I->getDataFromJson($this, "GroupID"), $Permission, $invisible, $remove);
                    $I->UploadFiles($I, "AddFacultyToGroup.csv");
                }
            }
        } else {
            $remove = "Remove";
            if (strpos($GroupType, "Created") !== false) {
                if ($IsSubgroup == 1) {
                    $Write->WriteDataForFacultyToGroup("DeleteFacultyFromSubGroup.csv", $ExternalID, $FirstName, $LastName, $Email, $I->getDataFromJson($this, "SubGroupID"), $Permission, $invisible, $remove);
                    $I->UploadFiles($I, "DeleteFacultyFromSubGroup.csv");
                } else {
                    $Write->WriteDataForFacultyToGroup("DeleteFacultyFromGroup.csv", $ExternalID, $FirstName, $LastName, $Email, $I->getDataFromJson($this, "GroupID"), $Permission, $invisible, $remove);
                    $I->UploadFiles($I, "DeleteFacultyFromGroup.csv");
                }
            }
        }
    }

}
