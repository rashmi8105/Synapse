<?php
namespace Step\Acceptance;

class TeamStep extends \AcceptanceTester
{
/**
     * @When user clicks on add another team button
     */
     public function userClickOnAddAnotherTeamButton()
     {
         $this->ClickonAddTeamBtn();
     }

     /**
     * @When click on cancel team button
     */
     public function clickOnCancelTeamButton()
     {
         $this->ClickCancelTeamBtn();
     }

     /**
     * @When click on save team button
     */
     public function clickOnSaveTeamButton()
     {
         $this->ClickSaveTeamBtn();
     }

     
    /**
     * @Then user is not able to view the team on the page
     */
     public function userIsNotAbleToViewTheTeamOnThePage()
     {
         
        
     }
     
     /**
     * @Then user is not able to view the :arg1 on the page
     */
     public function userIsNotAbleToViewTheOnThePage($Message)
     {
        $this->MessageIsNotDisplaying($Message);
     }


    /**
     * @When fill :arg1 and :arg2,:arg3 with Role :arg4 and :arg4 respectively
     */
     public function fillAndWithRoleAndRespectively($teamName, $Faculty1, $Faculty2, $Role1, $Role2)
     {
         $this->filldataOnTeamPage($teamName, $Faculty1, $Faculty2, $Role1, $Role2);
     }

    /**
     * @Then user is able to view the team on the page
     */
     public function userIsAbleToViewTheTeamOnThePage()
     {
         $this->TeamIsDisplayingOnThePage();
     }

    /**
     * @Then user is able to view the :arg1 on the page
     */
     public function userIsAbleToViewTheOnThePage($Message)
     {
         $this->VerifyMessageText($Message);
     }


    /**
     * @When user click on edit icon in front of the created team
     */
     public function userClickOnEditIconInFrontOfTheCreatedTeam()
     {
        $this->ClickOnTeamEditicon();
     }

    /**
     * @When remove the faculties :arg1 and :arg2 assigned in the team
     */
     public function removeTheFacultiesAndAssignedInTheTeam($faculty1, $faculty2)
     {
         $this->RemoveFacultyFromTeam($faculty1, $faculty2);
     }


    /**
     * @When user click on delete icon in front of the created team
     */
     public function userClickOnDeleteIconInFrontOfTheCreatedTeam()
     {
      $this->ClickOnTeamDeleteicon();  
     }

    /**
     * @When click on delete button displayed on the dialog-box
     */
     public function clickOnDeleteButtonDisplayedOnTheDialogbox()
     {
         $this->confirmDeleteTeamButton();
     }
     
     /**
     * @Then user should not be able to view Team widget
     */
     public function userShouldNotBeAbleToViewTeamWidget()
     {
        $this->TeamWidgetNotDisplaying();
     }

    /**
     * @Then user should be able to view Team widget
     */
     public function userShouldBeAbleToViewTeamWidget()
     {
        $this->ViewTeamWidget();
     }


     
     ////////////////////////////
     public function ClickonAddTeamBtn(){
         $I=$this;
         $I->click($I->Element("AddTeamButton", "TeamPage"));
      // $I->ClickOnElementWithJS($I,$I->Element("AddTeamButton", "TeamPage"));
         $I->WaitForPageToLoad($I);
     }
     
     public function filldataOnTeamPage($teamName, $Faculty1, $Faculty2, $Role1, $Role2){
         $I=$this;
         $TeamName=$teamName.rand(1, 10000);
         $I->writeDataInJson($this,"TeamName", $TeamName);
         $team=$I->getDataFromJson($this,"TeamName");
         $I->fillField($I->Element("TeamName", "TeamPage"), $team);
         $I->AddFacultyAndAssignRole($Faculty1,$Role1);
         $I->AddFacultyAndAssignRole($Faculty2,$Role2);        
         
     }
     
     public function AddFacultyAndAssignRole($Faculty,$Role){
         $I=$this;
         $I->fillField($I->Element("SearchFacultyField", "TeamPage"), $Faculty);
         $I->click(str_replace("{{}}", $Faculty,$I->Element("selectStaff", "TeamPage")));
         $I->click($I->Element("addBtn", "TeamPage"));
         $I->waitForElementVisible(str_replace("{{}}",$Faculty , $I->Element("FacultyNameDisplayed", "TeamPage")),60);
         $I->click(str_replace("{{}}", $Faculty ,$I->Element("RoleDropDownBtn", "TeamPage")));
         $I->waitForElementVisible(str_replace("{{}}", $Faculty,$I->Element("TeaMDropDwnPanel", "TeamPage")),60);
         $I->click(str_replace("<<>>", $Faculty, str_replace("{{}}", $Role, $I->Element("selectTeamRole", "TeamPage"))));
         
     }
     
     public function ClickSaveTeamBtn(){
        $I=$this;
         $I->click($I->Element("SaveTeamBTn", "TeamPage"));
     }
     public function ClickCancelTeamBtn(){
         $I=$this;
         $I->click($I->Element("cancelTeambtn", "TeamPage"));
         $I->WaitForPageToLoad($I);
     }
     
     public function TeamIsDisplayingOnThePage(){
         $I=$this;
         $TeamName=$I->getDataFromJson($this,"TeamName");
         $I->IsTeamDisplayed($TeamName);
     }
     public function VerifyMessageText($Message){
         $I=$this;
         $TeamName=$I->getDataFromJson($this, "TeamName");
         if($Message=="No leader assigned No member assigned"){
             $message1="No leader assigned";
             $message2="No member assigned";
             $I->canSeeElement(str_replace("<<>>", $TeamName, str_replace("{{}}",$message1,$I->Element("MessageText", "TeamPage"))));
             $I->canSeeElement(str_replace("<<>>", $TeamName, str_replace("{{}}",$message2,$I->Element("MessageText", "TeamPage"))));
         }
         else{
             $I->canSeeElement(str_replace("<<>>", $TeamName, str_replace("{{}}",$Message,$I->Element("MessageText", "TeamPage"))));
         }
         
     }
     
     public function ClickOnTeamEditicon(){
         $I=$this;
         $TeamName=$I->getDataFromJson($this, "TeamName");
         $I->click(str_replace("{{}}", $TeamName, $I->Element("EditTeamIcon", "TeamPage")));
         $I->WaitForPageToLoad($I);
     }
     
     public function ClickOnTeamDeleteicon(){
         $I=$this;
         $TeamName=$I->getDataFromJson($this, "TeamName");
         $I->click(str_replace("{{}}", $TeamName, $I->Element("RemoveTeamIcon", "TeamPage")));
         $I->WaitForPageToLoad($I);
     }
     
     public function RemoveFacultyFromTeam($faculty1,$faculty2){
         $I=$this;
         $I->RemoveFaculty($faculty1);
         $I->RemoveFaculty($faculty2);   
         
         
     }
     
     public function RemoveFaculty($faculty){
         $I=$this;
         $I->click(str_replace("{{}}",$faculty , $I->Element("RemoveTeamIcon", "TeamPage")));
         $I->WaitForModalWindowToAppear($I);
         $I->click($I->Element("RemoveStaffBtn", "TeamPage"));
         $I->WaitForPageToLoad($I);
         
     }
     
     public function confirmDeleteTeamButton(){
         $I=$this;
         $I->click($I->Element("DeleteTeamBTn", "TeamPage"));
     }
     
     
     public function MessageIsNotDisplaying($Message){
         $I=$this;
         $TeamName=$I->getDataFromJson($this, "TeamName");
         $I->cantSeeElement(str_replace("<<>>", $TeamName, str_replace("{{}}",$Message,$I->Element("MessageText", "TeamPage"))));
         
     }

     public function ViewTeamWidget(){
         $I=$this;
         $I->canSeeElement($I->Element("TeamWidget", "TeamPage"));
     }
     
     public function TeamWidgetNotDisplaying(){
         $I=$this;
         $I->cantSeeElement($I->Element("TeamWidget", "TeamPage"));
     }

     public function  IsTeamDisplayed($TeamNameData){
         $I = $this;
         $I->reloadPage();
         $I->WaitForPageToLoad($I);
        $count = $I->grabTextFrom($I->Element("Count", "TeamPage"));
        $pagesCount = ceil($count / 10);
        for ($page = 1; $page <= $pagesCount; $page++) {
            if ($page > 1) { // To click on the paginations
                $I->click(str_replace("{{}}", $page, $I->Element("PaginationBar", "TeamPage")));
            }
            for ($row = 2; $row <= 11; $row++) {//To grab the text of each row containing Permission Set on the page.
                $TeamName = $I->grabTextFrom('//table//tbody//tr[' . $row . ']/td[contains(@class,"bold-text")]');
                //codecept_debug($TeamName);               
                if (strpos($TeamName,$TeamNameData)!==false) {
                    $I->canSee($TeamNameData,'//table//tbody//tr[' . $row . ']/td[1]');
                    return;
                }
            }
        
            
                }
    
        
        
                }
}