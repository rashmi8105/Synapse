<?php

namespace Step\Acceptance;

class SearchStep extends \AcceptanceTester {
  
  
   
    /**
     * @When user clicks :arg1 panel
     */
    public function userClicksPanel($panelName) {
        $this->UserClicksonRightPanel($panelName);
    }

    /**
     * @When user selects :arg1 risk level
     */
    public function userSelectsRiskLevel($riskLevel) {
        $this->selectsRiskLevel($riskLevel);
    }

    /**
     * @When user fills :arg1 in search field
     */
    public function userFillsInSearchField($Name) {
        $this->FillSearchName($Name);
    }

    /**
     * @When Click on save Button displayed in small window
     */
    public function clickOnSaveButtonDisplayedInSmallWindow() {
        $this->ClickSaveBtnOnSmallWindow();
    }

    /**
     * @Then user navigated to :arg1 page
     */
    public function userNavigatedToPage($url) {
        $this->verifyUserNavigation($url);
    }

   

    /**
     * @When user selects :arg1 :arg2 option
     */
    public function userSelectsOption($type, $option) {
        $this->UserSelectsActivityOption($type, $option);
    }

    /**
     * @Then user is able to view edited search :arg1
     */
    public function userIsAbleToViewEditedSearch($Search) {
        $this->SearchDisplayingInList($Search);
    }

    /**
     * @When user clicks on share search icon in front of :arg1
     */
    public function userClicksOnShareSearchIconInFrontOf($SearchName) {
        $this->clickonShareIcon($SearchName);
    }

    /**
     * @When user fills and selects :arg1 faculty to share with
     */
    public function userFillsAndSelectsFacultyToShareWith($facultyName) {
        $this->SelectFaculty($facultyName);
    }

    /**
     * @When user clicks on Share button
     */
    public function userClicksOnShareButton() {
        $this->ClickShareBtn();
    }

    /**
     * @Then user is able to view shared search :arg1
     */
    public function userIsAbleToViewSharedSearch($Search) {
        $this->SearchDisplayingInList($Search);
    }


    /**
     * @Then user is able to view the list of the students
     */
    public function userIsAbleToViewTheListOfTheStudents() {
        
    }


    /**
     * @When user delete search icon in front of :arg1
     */
    public function userDeleteSearchIconInFrontOf($SearchName) {
        $this->clickonDeleteIcon($SearchName);
    }

    /**
     * @When clicks on delete button displayed on modal window
     */
    public function clicksOnDeleteButtonDisplayedOnModalWindow() {
        $this->ClickOnDeleteBtnOnModalWin();
    }

    /**
     * @Then user is not able to view the search :arg1
     */
    public function userIsNotAbleToViewTheSearch($Search) {
        $this->SearchNotDisplaying($Search);
    }
/**
 * @Then user is not able to navigate to the :arg1 profile page
 */
 public function userIsNotAbleToNavigateToTheProfilePage($studentName)
 {
     $this->VerifyUserDoesNotSeeStudent($studentName);
 }
    
 /**
 * @When user clicks on search button
 */
 public function userClicksOnSearchButton()
 {
 $this->ClickOnSearchButton();
 }  
     
/**
 * @When user selects :arg1 option from Group on custom search window
 */
 public function userSelectsOptionFromGroupActivity($GroupName)
 { 
     $this->selectGroupNameInCustomSearch($GroupName);
     
 } 
     /**
     * @When user fills and clicks the :arg1 in the search field
     */
    public function userFillsAndClicksTheInTheSearchField($StdName) {
        $this->VerifyUserSelectsUser($StdName);
    }

    /**
     * @When user clicks on SaveSearchBtn button
     */
    public function userClicksOnSaveSearchBtnButton() {
        $this->ClickOnSaveSearchBtn();
    }

    

    /**
     * @Then user is able to view created search :arg1
     */
    public function userIsAbleToViewCreatedSearch($Search) {
        $this->SearchDisplayingInList($Search);
    }

    /**
     * @When user clicks on edit search icon in front of :arg1
     */
    public function userClicksOnEditSearchIconInFrontOf($SearchName) {
        $this->clickonEditIcon($SearchName);
    }

  

    /**
     * @When clicks on :arg1 search name
     */
    public function clicksOnSearchName($Search) {
        $this->ClickOnSearchName($Search);
    }

   

    /**
     * @When user clicks on the student name :arg1
     */
    public function userClicksOnTheStudentName($StudentName) {
         $this->ClickOnstudentName($StudentName);
    }

    
   /**
     * @When user selects :arg1 from bulk option
     */
     public function userSelectsFromBulkOption($ActivityType)
     {
        $this->SelectBulkOption($ActivityType);
     }

     /**
     * @When user selects selectall option
     */
     public function userSelectsSelectallOption()
     {
$this->SelectAllStudentOnSearchResultPage();
      }

    /**
     * @When user clicks on :arg1 link displayed on the page
     */
    public function userClicksOnLinkDisplayedOnThePage($type)
    {
$this->ClickOnLinkDisplayedInCenter($type);
    }

    /**
     * @When user selects :arg1 checkbox for :arg2 profile block
     */
    public function userSelectsCheckboxForProfileBlock($Option,$Name)
    {
$this->SelectCheckBoxForProfileType($Option,$Name);
    }

    /**
     * @When user click on SearchButton displayed on the modal window
     */
    public function userClickOnSearchButtonDisplayedOnTheModalWindow()
    {
$this->ClickOnSearchBtnDisplayedOnModalWin();
    }

    /**
     * @Then user should be able to see the uploaded student :arg1 on the page
     */
    public function userShouldBeAbleToSeeTheUploadedStudentOnThePage($std)
    {
    $this->StudentIsVisibleOnPage($std);
    }

    /**
     * @When user clicks on :arg1 displayed on the page
     */
    public function userClicksOnDisplayedOnThePage($type)
    {
        $this->ClickOnstudentName($type);
    }

    /**
     * @When user clicks on Start Date box
     */
    public function userClicksOnStartDateBox()
    {
$this->ClickOnStartDateBox();
    }

    /**
     * @When user selects start date :arg1 from the calender
     */
    public function userSelectsStartDateFromTheCalender($date)
    {
        $this->SelectStartDate($date);
    }

    /**
     * @When user selects end date :arg1 from the calender
     */
    public function userSelectsEndDateFromTheCalender($date)
    {
$this->SelectEndDate($date);
    }

    /**
     * @When user clicks on End Date box
     */
    public function userClicksOnEndDateBox()
    {
$this->ClickOnEndDateBox();
    }

    /**
     * @When user fills data :arg1 in single text box
     */
    public function userFillsDataInSingleTextBox($value)
    {
$this->FillSingleTextBoxForNumberISP($value);
    }

/**
     * @When user fills :arg1 in the search field
     */
     public function userFillsInTheSearchField($StdName)
     { 
         $this->UserFillsSearchFieldWithStudentName($StdName);
     }


 

    //////////////////////implementation///////////////////////////////////
    
    
  
 
public function SelectAllStudentOnSearchResultPage()
{
    $I=$this;
    $I->click($I->Element("SelectAll","SearchPage"));
   
}
 
 public function VerifyUserDoesNotSeeStudent($StdName) {
        $I = $this;
        $I->fillField($I->Element("SearchBox", "SearchPage"), $StdName);
        $I->cantSeeElement(str_replace("{{}}",$StdName,$I->Element("StudentNameInSearchSuggestionList","SearchPage")));
    }

    public function VerifyUserSelectsUser($StdName) {
        $I = $this;
        $I->UserFillsSearchFieldWithStudentName($StdName);
        $I->waitForElementVisible(str_replace("{{}}", $StdName, $I->Element("SelectStd", "SearchPage")), 60);
        $I->click(str_replace("{{}}", $StdName, $I->Element("SelectStd", "SearchPage")));
    }
 public function UserFillsSearchFieldWithStudentName($StdName)
 {
     $I = $this;
  $I->fillField($I->Element("SearchBox", "SearchPage"), $StdName);
 }

    public function verifyUserNavigation($url) {
        $I = $this;
        $I->canSeeInCurrentUrl($url);
    }

    public function SearchDisplayingInList($Search) {
        $I = $this;
        if (strpos($Search, "Automation") !== false) {
            $SearchName = $I->getDataFromJson($this, "SearchName");
        } elseif (strpos($Search, "edited") !== false || strpos($Search, "Edited") !== false) {
            $SearchName = $I->getDataFromJson($this, "EditedSearchName");
        } else {
            $SearchName = $I->getDataFromJson($this, "SharedSearchName");
        }
        $I->canSeeElement(str_replace("{{}}", $SearchName, $I->Element("SearchInList", "SearchPage")));
    }

   

    public function clickonDeleteIcon($SearchName) {
        $I = $this;
        if (strpos($SearchName, "edited") !== false || strpos($SearchName, "Edited") !== false) {
            $Name = $I->getDataFromJson($this, "EditedSearchName");
        }
        $I->click(str_replace("{{}}", $Name, $I->Element("DeleteIcon", "SearchPage")));
    }

    public function clickonShareIcon($SearchName) {
        $I = $this;
        if (strpos($SearchName, "edited") !== false || strpos($SearchName, "Edited") !== false) {
            $Name = $I->getDataFromJson($this, "EditedSearchName");
        }
        $I->click(str_replace("{{}}", $Name, $I->Element("ShareIcon", "SearchPage")));
    }

    public function UserSelectsActivityOption($type, $option) {
        $I = $this;
        $I->click(str_replace("{{}}", $type, str_replace("<<>>", $option, $I->Element("ActivityOption", "SearchPage"))));
    }

    public function SelectFaculty($facultyName) {
        $I = $this;
        $I->fillField($I->Element("SelectFaculty", "SearchPage"), $facultyName);
        $I->waitForElementVisible(str_replace("{{}}", $facultyName, $I->Element("SelectStd", "SearchPage")), 60);
        $I->click(str_replace("{{}}", $facultyName, $I->Element("SelectStd", "SearchPage")));
    }

    public function StudentIsVisibleOnPage($StudentName){
        $I=$this;
        if (strpos($StudentName,"Uploaded")!==false){
            $StudentName=$I->ReadFromJson("ISPData","FirstName");
            $I->canSeeElement(str_replace("{{}}",$StudentName,$I->Element("StudentLink","SearchPage")));
        }
    }

    
    public function ClickOnstudentName($StudentName)
    {
        $I=$this;
        if (strpos($StudentName,"Uploaded")!==false){
            $StudentName=$I->ReadFromJson("ISPData","FirstName");
            $I->click(str_replace("{{}}",$StudentName,$I->Element("StudentLink","SearchPage")));
            $I->WaitForPageToLoad($I);
        }
        else{
            $I->click(str_replace("{{}}",$StudentName,$I->Element("StudentLink","SearchPage")));
            $I->WaitForPageToLoad($I);
        }
      
    }

    public function ClickOnLinkDisplayedInCenter($Type){
        $I=$this;
        $I->click(str_replace("{{}}",$Type,$I->Element("ProfilePanelLinks","SearchPage")));
        $I->wait(3);
    }

    public function SelectCheckBoxForProfileType($option,$Name){
        $I=$this;
        $I->click(str_replace("{{}}",$Name,str_replace("<<>>",$option,$I->Element("ProfileBlockCheckBoxSelection","SearchPage"))));
    }

    public function ClickOnSearchBtnDisplayedOnModalWin(){
        $I=$this;
        $I->click($I->Element("SaveSearchBtnModalWin","SearchPage"));
        $I->WaitForPageToLoad($I);
    }

    public function ClickOnStartDateBox(){
        $I=$this;
        $I->click($I->Element("StartDateTextBoxForDateISP","SearchPage"));
        $I->waitForElementVisible($I->Element("StartDateCalender","SearchPage"));
    }

    public function ClickOnEndDateBox(){
        $I=$this;
        $I->click($I->Element("EndDateTextBoxForDateIsp","SearchPage"));
        $I->waitForElementVisible($I->Element("EndDateCalender","SearchPage"));
    }

    public function SelectStartDate($Date){
        $I=$this;
        if (strpos($Date,"current")!==false){
            $date=$I->GetCurrentDate($I);
        }
        $I->click(str_replace("{{}}",$date,$I->Element("SelectStartDate","SearchPage")));

    }

    public function SelectEndDate($Date){
        $I=$this;
        if (strpos($Date,"current")!==false){
            $date=$I->GetCurrentDate($I);
        }
        $I->click(str_replace("{{}}",$date,$I->Element("SelectEndDate","SearchPage")));

    }

    public function FillSingleTextBoxForNumberISP($value){
        $I=$this;
        $I->fillField($I->Element("NumberISPSingleTextBox","SearchPage"),$value);
    }


 
 public function selectGroupNameInCustomSearch($GroupName)
 {
     
     $I=$this;
     $I->click(str_replace("{{}}",$GroupName,$I->Element("SelectOption","SearchPage")));
 }
 
 public function  SelectBulkOption($ActivityType)
 {
     $I=$this;
     $I->click($I->Element("BulkButton","SearchPage"));
     $I->waitForElement(str_replace("{{}}",$ActivityType,$I->Element("ActivityUnderBulk","SearchPage")));
     $I->click(str_replace("{{}}",$ActivityType,$I->Element("ActivityUnderBulk","SearchPage")));
     $I->WaitForModalWindowToAppear($I);
 }
 

 
 
 public function ClickOnSearchButton()
 {
     $I=$this;
     $I->Click($I->Element("SearchBtn","SearchPage"));
     $I->WaitForPageToLoad($I);       
    
 }
 
 

 
    public function UserClicksonRightPanel($panelName) {
        $I = $this;
        $I->click(str_replace("{{}}", $panelName, $I->Element("SearchRightPanel", "SearchPage")));
        $I->wait(2); //due to latency
    }

    public function selectsRiskLevel($riskLevel) {
        $I = $this;
        $I->click(str_replace("{{}}", $riskLevel, $I->Element("SelectCheckBox", "SearchPage")));
    }

    public function ClickOnSaveSearchBtn() {
        $I = $this;
        $I->click($I->Element("SaveSearchBtn", "SearchPage"));
        $I->waitForElementVisible($I->Element("SaveSearchWin", "SearchPage"), 10);
    }

    public function FillSearchName($Name) {
        $I = $this;
        $SearchName = $Name . rand(100, 10000);
        if (strpos($Name, "edited") !== FALSE || strpos($Name, "Edited") !== FALSE) {
            $I->writeDataInJson($this, "EditedSearchName", $SearchName);
        } elseif (strpos($Name, "share") !== FALSE || strpos($Name, "Share") !== FALSE) {
            $I->writeDataInJson($this, "SharedSearchName", $SearchName);
        } else {
            $I->writeDataInJson($this, "SearchName", $SearchName);
        }
        $I->fillField($I->Element("FillSearch", "SearchPage"), $SearchName);
    }

    public function ClickSaveBtnOnSmallWindow() {
        $I = $this;
        $I->click($I->Element("saveBtnSmallWin", "SearchPage"));
    }

   

    public function clickonEditIcon($SearchName) {
        $I = $this;
        if (strpos($SearchName, "Automation") !== false) {
            $Name = $I->getDataFromJson($this, "SearchName");
        }
        $I->click(str_replace("{{}}", $Name, $I->Element("EditIcon", "SearchPage")));
    }

   

    public function ClickShareBtn() {
        $I = $this;
        $I->click($I->Element("ShareBtn", "SearchPage"));
    }

    public function ClickOnSearchName($Search) {
        $I = $this;
        if (strpos($Search, "edited") !== false || strpos($Search, "Edited") !== false) {
            $Name = $I->getDataFromJson($this, "EditedSearchName");
        }
        $I->click(str_replace("{{}}", $Name, $I->Element("SearchInList", "SearchPage")));
        $I->WaitForPageToLoad($I);
    }

    public function ClickOnDeleteBtnOnModalWin() {
        $I = $this;
        $I->click($I->Element("DeleteBtn", "SearchPage"));
    }

    public function SearchNotDisplaying($Search) {
        $I = $this;
        if (strpos($Search, "edited") !== false || strpos($Search, "Edited") !== false) {
            $Name = $I->getDataFromJson($this, "EditedSearchName");
        }
        $I->cantSeeElement(str_replace("{{}}", $Name, $I->Element("SearchInList", "SearchPage")));
    }

    
   
}
