<?php
namespace Step\Acceptance;

class LoginStep extends \AcceptanceTester
{
   
    /**
     * @Given user is on skyfactor login page
     */
     public function userisonSkyfactorLoginPage()
     {
        $this->VerifyUserIsOnLoginPage();
        
       
     }
      /**
     * @When user login into the application with :arg1 and :arg2
     */
     public function userLoginIntoTheApplicationWithAnd($arg1, $arg2)
     {
        $this->VerifyUserLoginIntoTheApplication($arg1,$arg2);
     }
     
     /**
     * @Then User land on Overview page
     */
     public function userLandOnOverviewPage()
     {
        $this->VerifyUserIsOnDesiredPage();
     }
     /**
  /**
     * @Then Faculty land on Dashboard page
     */
     public function facultyLandOnDashboardPage()
     {
           $this->VerifyUserIsOnDesiredPage("Faculty");
      }
 
     /**
     * @Then User should be able to see the error message on the page
     */
     public function userShouldBeAbleToSeeTheErrorMessageOnThePage()
     {
         $this->IsErrorMessageDisplayed();
     }

     
 /**
 * @When Student login into the application with :arg1
 */
 public function studentLoginIntoTheApplicationWith($StudentEmail)
 { 
     $this->LoginAsStudent($this,$StudentEmail);
     
 }

/**
 * @Then Student lands on Survey page
 */
 public function studentLandsOnSurveyPage()
 {
    $this->VerifyUserIsOnDesiredPage("Student");
 }

      
     
     
/////////////////////////////////////////////////////////////////////////
    
    
public function VerifyUserIsOnLoginPage(){
   
    $I=$this;
    $I->amOnPage("#/login");
    $I->maximizeWindow();
    $I->WaitForPageToLoad($I);
    
    
}
public function VerifyUserLoginIntoTheApplication($userName,$password){
    $I=$this; 
    $I->expectTo("Fill Username in field");
    $I->fillField($I->Element("UserName","LoginPage"), $userName);
    $I->expectTo("Fill Password in field");
    $I->fillField($I->Element("Password","LoginPage"), $password);
    $I->expectTo("Click on signIn button");
    $I->click($I->Element("SignInBtn","LoginPage"));   
    
    }
    
    public function VerifyUserIsOnDesiredPage($User="Coordinator"){
        $I=$this;
        $I->WaitForPageToLoad($I);
        $I->expectTo("See Title of the page");
        $I->cantSeeElement($I->Element("errorMessage","LoginPage"));
        $I->maximizeWindow();
        if($User=="Coordinator"){
        $I->canSeeInTitle("Mapworks - Overview");
        }
        if($User=="Faculty")
        {
                  $I->canSeeInTitle("Mapworks - Dashboard");
          }
        if($User=="Student")
        {
            
            $I->canSeeInTitle("Mapworks - Survey List");
            
        }
        $I->WaitForPageToLoad($I);
          
        
    }
    
    public function IsErrorMessageDisplayed(){
        $I=$this;
        $I->WaitForPageToLoad($I);
        $I->canSeeElement($I->Element("errorMessage","LoginPage"));
        
        
    }

}