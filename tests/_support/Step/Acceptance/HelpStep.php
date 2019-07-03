<?php

namespace Step\Acceptance;

class HelpStep extends \AcceptanceTester {

    /**
     * @Then user is able to see knowledge base link
     */
    public function userCanSeeKnowledgeBaseLink() {
        $this->VerifyPresenceKnowledgeBaseLink();
    }

    /**
     * @When user clicks on :arg1 button on help page
     */
    public function userClicksOnAddButton($typeofSupportMaterials) {
        $this->ClickOnAddButton($typeofSupportMaterials);
    }

    /**
     * @When user fill the url field with :arg1
     */
    public function userFillTheUrlFieldWith($URL) {
        $this->FillURL($URL, "create");
    }

    /**
     * @When user fills title and description with :arg1 and :arg2 for Link
     */
    public function userFillsTitleAndDescriptionWithAnd($Title, $Description) {
        $this->FillTitleDescription($Title, $Description, "create", "Link");
    }

    /**
     * @When user clicks on save button on help page
     */
    public function userClicksOnSaveButton() {
        $this->ClickOnSaveButton();
    }

    /**
     * @Then user is able to see :arg1 link in table
     */
    public function userSeeAddedLinkInTable($operation) {

        $this->VerifyPresenceOfMaterialInList("Link", $operation);
    }

    /**
     * @When user clicks on :arg1 icon of link
     */
    public function userClicksOnIconOfLink($Icon) {
        $this->ClickOnIconForLink($Icon);
    }

    /**
     * @When user edits the url field with :arg1
     */
    public function userEditsTheUrlFieldWith($URL) {
        $this->FillURL($URL, "edit");
    }

    /**
     * @When user edits title and description with :arg1 and :arg2 for Link
     */
    public function userEditsTitleAndDescriptionWithAnd($Title, $Description) {
        $this->FillTitleDescription($Title, $Description, "edit", "Link");
    }

    /**
     * @When user clicks confirm remove button
     */
    public function userClicksConfirmRemoveButton() {
        $this->ClickOnConfirmDeleteButton();
    }

    /**
     * @Then user is not able to see :arg1 link in table
     */
    public function userShouldNotSeeEditedLinkInTable($Type) {
        $this->VerifyAbsenseOfMaterial("Link",$Type);
    }

    /**
     * @When user attach a file
     */
    public function userAttachAFile() {
        $this->AttachDocument("TextDocument.txt");
    }

    /**
     * @Then user is able to see :arg1 document in table
     */
    public function userSeeDocumentInTable($operation) {

        $this->VerifyPresenceOfMaterialInList("Document", $operation);
    }

    /**
     * @When user clicks on :arg1 icon of document
     */
    public function userClicksOnIconOfDocument($Icon) {

        $this->ClickOnIconForDocument($Icon);
    }

    /**
     * @When user fills title and description with :arg1 and :arg2 for document
     */
    public function userFillsTitleAndDescriptionWithAndForDocument($Title, $Description) {

        $this->FillTitleDescription($Title, $Description, "create", "Document");
    }

    /**
     * @When user edits title and description with :arg1 and :arg2 for document
     */
    public function userEditsTitleAndDescriptionWithAndForDocument($Title, $Description) {
        $this->FillTitleDescription($Title, $Description, "edit", "Document");
    }

    /**
     * @Then user is not able to see :arg1 document in table
     */
    public function userShouldNotSeeEditedDocumentInTable($Type) {
        $this->VerifyAbsenseOfMaterial("Document", $Type);
    }

    /**
     * @When user clicks on file ticket link
     */
    public function userClicksOnFileTicketLink() {
        $this->ClickOnFileTicketLink();
    }

    /**
     * @When user clicks on file ticket Button
     */
    public function userClicksOnFileTicketButton() {
        $this->clickOnFileCreatebutton();
    }

    /**
     * @When user fills the details for ticket with :arg1, :arg2 , :arg3 , :arg4
     */
    public function userFillsTheDetailsForTicketWith($caregory, $subject, $description, $screenshot) {
        $this->FillTicketDeatails($caregory, $subject, $description, $screenshot);
    }

    /**
     * @Then user is able to see :arg1 on Help page
     */
    public function userCanSeeOnHelpPage($Email) {

        $this->SeeEmailonHelpPage($Email);
    }

///////////////////////////////////////
    public function SeeEmailonHelpPage($Email) {
        $I = $this;
        $I->canSeeElement(str_replace("{{}}", $Email, $I->Element("EmailOfCoordniator", "HelpPage")));
    }

    public function FillTicketDeatails($category, $subject, $description, $screenshot) {
        $I = $this;
        $I->waitForElement($I->Element("selectACategory", "HelpPage"),60);
        $I->click($I->Element("selectACategory", "HelpPage"));
        $I->waitForElement(str_replace("{{}}", $category, $I->Element("itemSelected", "HelpPage")),60);
        $I->click(str_replace("{{}}", $category, $I->Element("itemSelected", "HelpPage")));
        $I->fillField($I->Element("Subject", "HelpPage"), $subject);
        $I->fillField($I->Element("Description", "HelpPage"), $description);
        $I->executeInSelenium(function(\Facebook\WebDriver\Remote\RemoteWebDriver $driver) {
            $screenshotFile = 'testImage.jpg';
            $driver->findElement(\WebDriverBy::xpath('//input[@type="file"]'))->sendKeys(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'uploadFiles' . DIRECTORY_SEPARATOR . $screenshotFile);
        });
        $I->wait(5);
    }

    public function clickOnFileCreatebutton() {
        $I = $this;
        $I->waitForElement($I->Element("fileTicketSubmitButton", "HelpPage"),60);
        $I->click($I->Element("fileTicketSubmitButton", "HelpPage"));
    }

    public function ClickOnFileTicketLink() {
        $I = $this;
        $I->click($I->Element("fileTicketLink", "HelpPage"));
        $I->WaitForModalWindowToAppear($I);
        
    }

    public function AttachDocument($FileName) {
        $I=$this;
        $I->attachFile($I->Element("attachFile","HelpPage"),$FileName) ;
        $I->Wait(5);
    }

    public function ClickOnConfirmDeleteButton() {
        $I = $this;
        $I->click($I->Element("RemoveThislinkDocumentButton", "HelpPage"));
    }

    public function VerifyAbsenseOfMaterial($MaterialType, $Type) {
        $I = $this;
        if (strcasecmp($MaterialType, "Link") == 0) {
            if (strpos($Type, "Edited") !== false) {
                $I->cantSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "EditedLinkTitle"), $I->Element("MaterialOnList", "HelpPage")));
            }
        } else {
            if (strpos($Type, "Edited") !== false) {
                $I->cantSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "EditedDocTitle"), $I->Element("MaterialOnList", "HelpPage")));
            }
        }
    }

    public function VerifyPresenceKnowledgeBaseLink() {
        $I = $this;
        $I->canSeeElement($I->Element("KnowledgeBaseLink", "HelpPage"));
    }

    public function ClickOnAddButton($typeofSupportMaterials) {
        $I = $this;
        if (strcasecmp($typeofSupportMaterials, "Link") == 0) {

            $I->click(str_replace("{{}}", "Link", $I->Element("addButton", "HelpPage")));
            $I->WaitForModalWindowToAppear($I);
        } else {
            $I->click(str_replace("{{}}", "Document", $I->Element("addButton", "HelpPage")));
            $I->WaitForModalWindowToAppear($I);
        }
    }

    public function FillURL($URL, $operation) {
        $I = $this;
        $I->waitForElement($I->Element("linkTextField", "HelpPage"),60);
        $I->fillField($I->Element("linkTextField", "HelpPage"), $URL);

        if (strcasecmp($operation, "create") == 0) {
            $I->writeDataInJson($this, "URL", $URL);
        } else {
            $I->writeDataInJson($this, "EditedURL", $URL);
        }
    }

    public function FillTitleDescription($Title, $Description, $operation, $MaterialType) {

        $I = $this;
        $Title = $Title . rand(0, 9999);
        $Description = $Description . rand(0, 9999);
        $I->fillField($I->Element("TitleTextField", "HelpPage"), $Title);
        $I->fillField($I->Element("DescriptionTextField", "HelpPage"), $Description);

        if (strcasecmp($MaterialType, "Link") == 0) {

            if (strcasecmp($operation, "create") == 0) {
                $I->writeDataInJson($this, "LinkTitle", $Title);
                $I->writeDataInJson($this, "LinkDescription", $Description);
            } else {
                $I->writeDataInJson($this, "EditedLinkTitle", $Title);
                $I->writeDataInJson($this, "EditedLinkDescription", $Description);
            }
        } else {
            if (strcasecmp($operation, "create") == 0) {
                $I->writeDataInJson($this, "DocTitle", $Title);
                $I->writeDataInJson($this, "DocDescription", $Description);
            } else {
                $I->writeDataInJson($this, "EditedDocTitle", $Title);
                $I->writeDataInJson($this, "EditedDocDescription", $Description);
            }
        }
    }

    public function ClickOnSaveButton() {

        $I = $this;
        $I->click($I->Element("saveButton", "HelpPage"));
        
    }

    public function VerifyPresenceOfMaterialInList($Material, $operation) {
        $I = $this;
        if (strcasecmp($Material, "Link") == 0) {
            if (strcasecmp($operation, "create") == 0) {
                $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "LinkTitle"), $I->Element("MaterialOnList", "HelpPage")));
            } else {
                $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "EditedLinkTitle"), $I->Element("MaterialOnList", "HelpPage")));
            }
        } else {
            if (strcasecmp($operation, "create") == 0) {
                $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "DocTitle"), $I->Element("MaterialOnList", "HelpPage")));
            } else {
                $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "EditedDocTitle"), $I->Element("MaterialOnList", "HelpPage")));
            }
        }
    }

    public function ClickOnIconForLink($Icon) {
        $I = $this;
        if (strcasecmp($Icon, "Edit") == 0) {

            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "LinkTitle"), $I->Element("editButton", "HelpPage")));
        } else {
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "EditedLinkTitle"), $I->Element("removeButton", "HelpPage")));
        }
        $I->WaitForModalWindowToAppear($I);
    }

    public function ClickOnIconForDocument($Icon) {
        $I = $this;
        if (strcasecmp($Icon, "Edit") == 0) {

            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "DocTitle"), $I->Element("editButton", "HelpPage")));
        } else {
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "EditedDocTitle"), $I->Element("removeButton", "HelpPage")));
        }
        $I->WaitForModalWindowToAppear($I);
    }

}
