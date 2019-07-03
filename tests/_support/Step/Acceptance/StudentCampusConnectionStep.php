<?php

namespace Step\Acceptance;
class StudentCampusConnectionStep extends \AcceptanceTester {
/**
 * @When student clicks on :arg1 tab
 */
 public function studentClicksOnTab($tabName)
 {
 
     $this->ClickOnTabForStudent($tabName);
     
}

/**
 * @Then student see :arg1 as header on student page
 */
 public function studentSeeAsHeaderOnStudentPage($Heading)
 { 
     $this->SeeHeading($Heading);
     
 }
  /**
 * @When student clicks on schedule Appointment button  for Campus Connection faculty :arg1
 */
 public function studentClicksOnScheduleAppointmentButtonForCampusConnectionFaculty($FacultyName)
 {

     $this->clickOnSchedulAppointmentLinkForCampusConnection($FacultyName);
}

/**
 * @Then Student is able to see Faculty :arg1 as campus connection
 */
 public function studentSeeFacultyAsCampusConnection($FacultyName)
 { 
     $this->canSeeFacultyAsCampusConnection($FacultyName);
 }

/**
 * @Then student is able to see primary connection heading for :arg1
 */
 public function studentSeePrimaryConnectionHeadingFor($PrimaryConnectionFaculty)
 {
  $this->CanSeeFacultyAsPrimaryConnection($PrimaryConnectionFaculty);     
 }

/**
 * @Then student is able to see :arg1 for primary connection
 */
 public function studentSeeForPrimaryConnection($Name)
 { 
     $this->verifyPrimaryConnectionDetails($Name);
     
 }

/**
 * @Then student is not able to see primary connection heading for :arg1
 */
 public function studentDoesNotSeePrimaryConnectionHeadingFor($PrimaryConnectionFaculty)
 { 
     $this->CantSeeFacultyAsPrimaryConnection($PrimaryConnectionFaculty);
 }


//////////////////////////////////////////////////////////////////////////////////////
 
public function verifyPrimaryConnectionDetails($Name)
{
    $I=$this;
    $I->canSee($Name);
    
}

public function canSeeFacultyAsCampusConnection($FacultyName)
{
    $I=$this;
    $I->wait(3);
    $I->waitForElement(str_replace("{{}}",$FacultyName,$I->Element("FacultyNames","StudentCampusConnectionPage")),60);
    $I->canSeeElement(str_replace("{{}}",$FacultyName,$I->Element("FacultyNames","StudentCampusConnectionPage")));
    
}
 public function SeeHeading($Heading)
 {
     $I=$this;
     $I->canSee($Heading);
     if($Heading=="Courses"){
     $I->canSeeInCurrentUrl('/student-course-list');
     }
     if($Heading=="Campus Resources"){
     $I->canSeeInCurrentUrl('/student-campus-resources');
     }
     if($Heading=="Appointments"){
     $I->canSeeInCurrentUrl('/student-agenda');
     }
     if($Heading=="Surveys"){
     $I->canSeeInCurrentUrl('/student');
     }
     if($Heading=="Campus Connections"){
     $I->canSeeInCurrentUrl('/student-campus-connections');
     }
  if($Heading=="Referrals"){
     $I->canSeeInCurrentUrl('/student-referrals');
     }
       
     }
 
 
 public function ClickOnTabForStudent($tabName)
 {
   $I=$this;
   if(strpos($tabName,"Course")!==false)
   {
       $I->waitForElement($I->Element("Courses","StudentCommonLocatorPage"),60);
       $I->ClickOnElementWithJS($I,$I->Element("Courses","StudentCommonLocatorPage"));
   //  $I->click($I->Element("Courses","StudentCommonLocatorPage"));
       $I->WaitForPageToLoad($I);
       
       
   }
else
{
       $I->waitForElement(str_replace("{{}}",$tabName,$I->Element("tabName","StudentCommonLocatorPage")),60);
     $I->ClickOnElementWithJS($I, str_replace("{{}}",$tabName,$I->Element("tabName","StudentCommonLocatorPage")));
     //   $I->click(str_replace("{{}}",$tabName,$I->Element("tabName","StudentCommonLocatorPage")));
       $I->WaitForPageToLoad($I);
    
}
    
 }
  
 public function CantSeeFacultyAsPrimaryConnection($PrimaryConnectionFaculty)
 {
     $I=$this;
     $I->wait(3);
     $I->cantSeeElement(str_replace("{{}}",$PrimaryConnectionFaculty,$I->Element("PrimaryConnectionLink","StudentCampusConnectionPage")));

     
 }
 
 public function CanSeeFacultyAsPrimaryConnection($PrimaryConnectionFaculty)
 {
     
     $I=$this;
     $I->wait(2);
     $I->canSeeElement(str_replace("{{}}",$PrimaryConnectionFaculty,$I->Element("PrimaryConnectionLink","StudentCampusConnectionPage")));
     
 } 
   
 public function clickOnSchedulAppointmentLinkForCampusConnection($FacultyName)
 {
     $I=$this;
     $I->click(str_replace("{{}}",$FacultyName,$I->Element("campusScheduleButton","StudentCampusConnectionPage")));
     $I->WaitForModalWindowToAppear($I);
     
 }
 
 
 
}
