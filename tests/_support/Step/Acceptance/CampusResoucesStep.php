<?php
namespace Step\Acceptance;

class CampusResoucesStep extends \AcceptanceTester
{
 /**
 * @When fill details for campus resource with :arg1 , :arg2, :arg3, :arg4, :arg5, :arg6, :arg7, :arg8, :arg8
 */
 public function fillDetailsForCampusResourceWith($CampusResourceName, $StaffName, $Phone, $Email, $Location, $URL, $Description, $CanViewedbystudent,$canRecieveRefferal)
 {
   $this->fillDetails($CampusResourceName, $StaffName, $Phone, $Email, $Location, $URL, $Description, $CanViewedbystudent,$canRecieveRefferal);
     
 }
/**
 * @When user clicks on :arg1 icon on campus resource page
 */
 public function userClicksOnIconOnCampusResourcePage($icon)
 {
        $this->clickOnIcon($icon);
}

/**
 * @When click on save button to save Campus Resource
 */
 public function clickOnSaveButtonToSaveCampusResource()
 {
   $this->ClickonSave();
      
 }
 /**
 * @Then user is able to view Campus Resource in campus Resources list
 */
 public function userIsAbleToViewInCampusResourcesList()
 {
     $this->seeCampusResourceInList();
     
 }
 /**
 * @Then user is not able to view Campus Resource  in campus Resources list
 */
 public function userIsnotAbleToViewInCampusResourcesList()
 {
     $this->notSeeCampusResourceInList();
     
 }
 /**
 * @When user clicks on add campus resource button
 */
 public function userClicksOnAddCampusResourceButton()
 {
    $this->ClickOnAddAnotherCampusResourceButton(); 
 }

 public function  ClickOnAddAnotherCampusResourceButton()
 {
     $I=$this;
     $I->click($I->Element("AddAnotherButton","CampusResourcePage"));
     $I->WaitForPageToLoad($I);
 
 }
 
public function fillDetails($CampusResourceName, $StaffName, $Phone, $Email, $Location, $URL, $Description, $CanViewedbystudent,$canRecieveRefferal)
{
    $I=$this;
    $randomNumber=rand(0,99999);
    $campusResourcesName=$CampusResourceName.$randomNumber;
    if(strpos($StaffName,"ReferralAssigneeBehaviors")!==FALSE)
    {
        $StaffName= $I->getDataFromJson(new CoordinatorUserMgmtStep($I->getScenario()),"FirstName");
    }
    $I->writeDataInJson($this,'CampusResourceName',$campusResourcesName );

    $I->fillField($I->Element("CampusResourceName","CampusResourcePage"),$campusResourcesName);
        
    $I->fillField($I->Element("staffNameField","CampusResourcePage"),$StaffName); 
     
    $I->writeDataInJson($this,'staffNameField',$StaffName );
    
    $I->waitForElement(str_replace("{{}}",$StaffName,$I->Element("StaffOption","CampusResourcePage")),20);
    
    $I->click(str_replace("{{}}",$StaffName,$I->Element("StaffOption","CampusResourcePage")));
    
    $I->click($I->Element("buttonToAddStaff","CampusResourcePage"));
    
    $I->fillField($I->Element("phoneNumberField","CampusResourcePage"),$Phone.$randomNumber); 
     
    $I->writeDataInJson($this,'phoneNumberField',$Phone.$randomNumber);

    $I->fillField($I->Element("emailAddressField","CampusResourcePage"),$Email.$randomNumber."@mailinator.com");
    
    $I->fillField($I->Element("locationName","CampusResourcePage"),$Location.$randomNumber);
    
    $I->writeDataInJson($this,'locationName',$Location.$randomNumber);
    
    $I->fillField($I->Element("urlField","CampusResourcePage"),$URL);
    
    $I->fillField($I->Element("descriptionField","CampusResourcePage"),$Description.$randomNumber);
    
     $I->writeDataInJson($this,'descriptionField',$Description.$randomNumber);
    
    if(($CanViewedbystudent=='yes')||($CanViewedbystudent=='true'))
    {
        $I->click($I->Element("checkBoxForCampusResourceViewedByStudents","CampusResourcePage"));
    }
    
    if(($canRecieveRefferal=='yes')||($canRecieveRefferal=='true'))
    {
        $I->click($I->Element("checkBoxForCampusResourceReceiveReferrals","CampusResourcePage"));
    }
    
     
    
 }
 
 public function ClickonSave()
 {
     $I=$this;
     $I->click($I->Element("Savebutton","CampusResourcePage"));
     
 }
 
 public function seeCampusResourceInList()
 {
     $I=$this;
     $I->canSeeElement(str_replace("{{}}",$I->getDataFromJson($this,"CampusResourceName"),$I->Element("staffNameInTable","CampusResourcePage")));
     
 }
 
  public function notSeeCampusResourceInList()
 {
     $I=$this;
     $I->cantSeeElement(str_replace("{{}}",$I->getDataFromJson($this,"CampusResourceName"),$I->Element("staffNameInTable","CampusResourcePage")));
     
 }
 
 public function deleteFacultyFromCampusResources()
 {
     $I=$this;
     $I->waitForElement($I->Element("DelteFaculty","CampusResourcePage"),30);
     $I->click($I->Element("DelteFaculty","CampusResourcePage"));
     
 }
 
 public function clickOnIcon($icon)
 {
     $I=$this;
     if($icon=='edit')
      {    
         $I->click(str_replace("{{}}",$I->getDataFromJson($this,"CampusResourceName"), $I->Element("editIcon","CampusResourcePage")));
         $I->WaitForPageToLoad($I);
         $I->deleteFacultyFromCampusResources();
     }
     if($icon=='delete')
     {
        $I->click(str_replace("{{}}",$I->getDataFromJson($this,"CampusResourceName"), $I->Element("DeleteIcon","CampusResourcePage"))); 
     }
         
         
 }
 
 
 
 } 