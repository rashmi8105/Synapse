<?php
namespace Step\Acceptance;

class ManageFacultyStep extends \AcceptanceTester
{
/**
     * @When user grabs Faculty count
     */
     public function userGrabsFacultyCount()
     {
         $this->grabCount();  
     }

    /**
     * @When user clicks on AddFaculty button
     */
     public function userClicksOnAddFacultyButton()
     {
         $this->ClickOnAddFacultyBtn();
     }
     
     /**
     * @Then count of the faculty increases by one
     */
     public function countOfTheFacultyIncreasesByOne()
     {
         $this->CountIncreases();
     }

     
     /////////////////////
     
     public function grabCount(){
         $I=$this;
         $Count=$I->grabTextFrom($I->Element("FacultyCount", "ManageFacultyPage"));
         $count_new=  explode(" ", $Count);
         $I->writeDataInJson($this, "facultyCount", $count_new[1]);
     }
     
     public function ClickOnAddFacultyBtn(){
         $I=$this;
         $I->click($I->Element("AddFaculty", "ManageFacultyPage"));
     }
     
     public function CountIncreases(){
         $I=$this;
         $Count=$I->grabTextFrom($I->Element("FacultyCount", "ManageFacultyPage"));
         $NewCount=  explode(" ", $Count);
         $earlierCount=$I->getDataFromJson($this, "facultyCount");
         $I->assertEquals($NewCount[1], $earlierCount+1);
         
     }
     
     

}