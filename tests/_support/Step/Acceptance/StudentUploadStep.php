<?php

namespace Step\Acceptance;

class StudentUploadStep extends \AcceptanceTester {

    /**
     * @When user uploads :arg1 of valid student user
     */
    public function userUploadsOfValidStudentUser($UserCount) {
        $CreateCsv = new \ReadAndWriteCsvFile();
        $CreateCsv->WriteInCSV($UserCount, "Valid_Student.csv");
        $this->UploadUser("Valid_Student.csv");
    }

    /**
     * @When user uploads :arg1 of valid faculty user
     */
    public function userUploadsOfValidFacultyUser($UserCount) {
        $CreateCsv = new \ReadAndWriteCsvFile();
        $CreateCsv->WriteInCSV($UserCount, "Valid_Faculty.csv");
        $this->UploadUser("Valid_Faculty.csv");
    }

    /**
     * @When user uploads :arg1 of invalid faculty user
     */
    public function userUploadsOfInvalidFacultyUser($invalidCount) {
        $CreateCsv = new \ReadAndWriteCsvFile();
        $CreateCsv->WriteInCSV($invalidCount, "Invalid_Faculty.csv");
        $this->UploadUser("Invalid_Faculty.csv");
    }

    /**
     * @Then user is able to see correct values :arg1:arg1:arg3:arg3 in the displayed table
     */
    public function userIsAbleToSeeCorrectValuesInTheDisplayedTable($arg1, $arg2, $arg3, $arg4) {

        $this->VerifyTableContent($arg1, $arg2, $arg3, $arg4);
    }

    /**
     * @When user uploads :arg1 of valid student user with profile :arg2 type
     */
    public function userUploadsOfValidStudentUserWithProfileType($studentCount, $profileType) {
        $CreateCsv = new \ReadAndWriteCsvFile();
        $profile = $this->getProfileValue($profileType);
        $CreateCsv->WriteInCSVWithProfile($studentCount, $profile, "StudentWithTextProfile.csv");
        $this->UploadUser("StudentWithTextProfile.csv");
    }

    /**
     * @When user uploads :arg1 type student having :arg2,:arg3,:arg4 and :arg5
     */
     public function userUploadsTypeStudentHavingAnd($status, $externalId,$firstname, $lastname,$PrimaryEmail)
     {
        $this->uploadStudentsWithStatus($status, $externalId, $firstname, $lastname, $PrimaryEmail);
     }
  /**
     * @When user uploads student with year dependent profile :arg1 with value :arg2
     */
     public function userUploadsStudentWithYearDependentProfileWithValue($ProfileName,$Value)
     { 
         $this->UploadStudentWithYearDependentProfile($ProfileName,$Value);
     }

    

    /**
     * @When user uploads student with term dependent profile :arg1 with value :arg2
     */
     public function userUploadsStudentWithTermDependentProfileWithValue($ProfileName,$Value)
     {
  $this->UploadStudentWithTermDependentProfile($ProfileName,$Value);

     }

   

    /**
     * @When user uploads student with year dependent ISP :arg1 with value :arg2
     */
     public function userUploadsStudentWithYearDependentISPWithValue($ISP, $Value)
     {
         $this->UploadStudentWithYearDependentProfile($ISP, $Value);
         
      }

    

    /**
     * @When user uploads student with term dependent ISP :arg1 with value :arg2
     */
     public function userUploadsStudentWithTermDependentISPWithValue($ISP, $Value)
     {
           $this->UploadStudentWithTermDependentProfile($ISP, $Value);
     }

   
    


    //////implementations//////////
     
     public function UploadStudentWithTermDependentProfile($profile, $value)
     {   $I=$this;
         $Write=new \ReadAndWriteCsvFile();
         $firstName="FirstName".rand(0,99999);
         $LastName="LastName".rand(0,9999);
         $PrimaryEmail="EmailofStudent".rand(0,99999999)."@mailinator.com";
         $PrimaryMobile= rand(0,999999999);
         $ExternalId= rand(0,99999999);
         $I->writeDataInJson($this,"StudentExternalID",$ExternalId);
         $I->writeDataInJson($this,"FirstName",$firstName);
         $YearId='201617';
         $TermId='123';
         if($profile=="NumberDonotdelete")
         {
             $Write->UploadForSearch("Upload_StudentWithYearTermAndISP.csv", $ExternalId, $firstName, $LastName, $PrimaryEmail, $PrimaryMobile, $profile, $value, $YearId, $TermId);
        
             $I->UploadFiles($I,"Upload_StudentWithYearTermAndISP.csv");
          
         }
         if($profile=="CampusResident")
         {
           $Write->UploadForSearch("Upload_StudentWithYearTermAndProfile.csv", $ExternalId, $firstName, $LastName, $PrimaryEmail, $PrimaryMobile, $profile, $value, $YearId, $TermId);
          $I->UploadFiles($I,"Upload_StudentWithYearTermAndProfile.csv");

         }
     }
        public function UploadStudentWithYearDependentProfile($profile, $value)
     {   $I=$this;
         $Write=new \ReadAndWriteCsvFile();
         $firstName="FirstName".rand(0,99999);
         $LastName="LastName".rand(0,9999);
         $PrimaryEmail="EmailofStudent".rand(0,99999999)."@mailinator.com";
         $PrimaryMobile= rand(0,999999999);
         $ExternalId= rand(0,99999999);
         if(strpos($value,"current")!==false)
         {
             $value= date("m/d/Y");
         }
       $I->writeDataInJson($this,"StudentExternalID",$ExternalId);
       $I->writeDataInJson($this,"FirstName",$firstName);
         $YearId='201617';
         $TermId='123';
         if($profile=="Datedonotdelete")
         {
             $Write->UploadForSearch("Upload_StudentWithYearAndISP.csv", $ExternalId, $firstName, $LastName, $PrimaryEmail, $PrimaryMobile, $profile, $value, $YearId, $TermId);
          $I->UploadFiles($I,"Upload_StudentWithYearAndISP.csv");

             
         }
         if($profile=="RetentionTrack")
         {
           $Write->UploadForSearch("Upload_StudentWithYearAndProfile.csv", $ExternalId, $firstName, $LastName, $PrimaryEmail, $PrimaryMobile, $profile, $value, $YearId, $TermId);
           $I->UploadFiles($I,"Upload_StudentWithYearAndProfile.csv");

         }
         
     }
     

    public function UploadUser($fileName) {
        $I = $this;
        $I->UploadFiles($I, $fileName);
    }

    public function VerifyTableContent($TotalRowsUploaded, $RowsAdded, $RowsUpdated, $TotalError) {
        $I = $this;
        $I->VerifyUploadDataRows($I, $TotalRowsUploaded, $RowsAdded, $RowsUpdated, $TotalError);
    }

    public function getProfileValue($profile) {
        $I = $this;
        if (strpos($profile, "Text") !== false) {
            $TextProfile = $I->getDataFromJson($this, "TextName");
            return $TextProfile;
        }
    }
    
    public function uploadStudentsWithStatus($status, $externalId,$firstName,$lastName,$emailId){
        $I=$this;
        $CreateCsv = new \ReadAndWriteCsvFile();
        $CreateCsv->WriteInCSVWithStatus($status, $externalId,$firstName,$lastName,$emailId, "StudentWithStatus.csv");
        $this->UploadFiles($I, "StudentWithStatus.csv");
        
    }
    

}
