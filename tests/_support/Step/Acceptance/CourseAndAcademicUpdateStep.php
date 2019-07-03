<?php

namespace Step\Acceptance;

class CourseAndAcademicUpdateStep extends \AcceptanceTester {

    private static $paginationCount;
    
      /**
     * @When user clicks on upload academic update button
     */
     public function userClicksOnUploadAcademicUpdateButton()
     { 
         $this->ClickOnUploadAcademicUpdateButton();
     }

    /**
     * @When user uploads grade with details :arg1, :arg2, :arg3, :arg4, :arg5
     */
     public function userUploadsGradeWithDetails($ExternalID, $ProgressGrade, $risk, $absent, $comment)
     { 
         $this->UploadAcademicUpdate($ExternalID, $ProgressGrade, $risk, $absent, $comment);
     }


        /**
     * @When user clicks on update these student link for :arg1 Academic Update
     */
     public function facultyClicksOnUpdateTheseStudentLinkForAcademicUpdate($TypeOfAcademicUpdate)
     {
               $this->clickOnUpdateStudentLink($TypeOfAcademicUpdate);
      }

    
    /**
     * @When Faculty clicks on update these student link
     */
    public function facultyClicksOnUpdateTheseStudentLink() {
        $this->clickOnUpdateStudentLink("Faculty");
    }

    /**
     * @When user clicks on update these student link
     */
    public function userClicksOnUpdateTheseStudentLink() {
        $this->clickOnUpdateStudentLink();
    }

    /**
     * @Then user is not able to see :arg1 in faculty section of course
     */
    public function userIsNotAbleToSeeInFacultySectionOfCourse($FacultyName) {
        $this->verifyFaculty($FacultyName, 'absence');
    }

    /**
     * @Then user is not able to see :arg1 in student section of course
     */
    public function userIsNotAbleToSeeInStudentSectionOfCourse($StudentName) {
        $this->verifyFaculty($StudentName, 'absence');
    }

    /**
     * @When user clicks on adhoc academic update icon for :arg1 course
     */
    public function userClicksOnAdhocAcademicUpdateIconForCourse($TypeOfCourse) {
        $this->ClickOnAdhocAcademicUpdateIconOnCoursePage($TypeOfCourse);
    }

    /**
     * @Then user is able to see grade details  :arg1, :arg2, :arg3, :arg4 :arg5, :arg5
     */
    public function userSeeGradeDetails($DropDownValue, $risk, $absent, $comment, $ReferToAssitance, $SendToStudent) {
        $this->VerifyGradesOnStudentProfile($DropDownValue, $risk, $absent, $comment, $ReferToAssitance, $SendToStudent);
    }

    /**
     * @Then user is able to see :arg1 academic request in Closed academic update list                                                                                                   
     */
    public function userIsAbleToSeeAcademicRequestInClosedAcademicUpdateList($TypeOfAcademicUpdate) {
        $this->VerifyAcdemicUpdateInAcademicUpdatesSection($TypeOfAcademicUpdate, "closed");
    }

    /**
     * @When user refer student for academic assistance
     */
    public function userReferStudent() {
        $this->ReferToStudent();
    }

    /**
     * @When user clicks on roster view of :arg1 course
     */
    public function UserClicksOnRosterViewOfCourse($TypeOfCourse) {
        $this->VerifyAndClickOnCourse($TypeOfCourse);
    }

    /**
     * @Then user is able to see :arg1 and :arg2 of :arg3 course in roster view of course
     */
    public function userIsAbleToSeeAndOfCourseInRosterViewOfCourse($CourseName, $SectionNumber, $TypeOfCourse) {
        $this->SeeRosterView($TypeOfCourse);
    }

    /**
     * @When user uploads faculty with details :arg1, :arg2, :arg3 to :arg4 course
     */
    public function userUploadsFacultyWithDetailsToCourse($FacultyID, $PermissionSet, $Remove, $TypeOfCourse) {
        $this->UploadFaculty($FacultyID, $PermissionSet, $Remove, $TypeOfCourse);
    }

    /**
     * @Then user is able to see :arg1 in faculty section of course
     */
    public function userIsAbleToSeeInFacultySectionOfCourse($FacultyName) {
        $this->verifyFaculty($FacultyName, "presence");
    }

    /**
     * @When user uploads student with details :arg1, :arg2 to :arg3 course
     */
    public function userUploadsStudentWithDetailsToCourse($StudentId, $Remove, $TypeOfCourse) {
        $this->uploadStudent($StudentId, $Remove, $TypeOfCourse);
    }

    /**
     * @Then user is able to see :arg1 in student section of course
     */
    public function userIsAbleToSeeInStudentSectionOfCourse($StudentName) {
        $this->verifyStudent($StudentName, "presence");
    }

    /**
     * @Then user is able to see :arg1 academic update on academic update page
     */
    public function userIsAbleToSeeAcademicUpdateOnAcademicUpdatePage($TypeofAcademicUpdate) {
        $this->verifyAcademicUpdateOnAcademicUpdateSetUpPage($TypeofAcademicUpdate);
    }

    /**
     * @Then user is able to see :arg1 academic request in open academic update list
     */
    public function userSeeAcademicRequestInOpenAcademicUpdateList($TypeofAcademicUpdate) {
        $this->VerifyAcdemicUpdateInAcademicUpdatesSection($TypeofAcademicUpdate, 'open');
    }

    /**
     * @When user click on history link for :arg1 course
     */
    public function userClickOnHistoryLinkForCourse($TypeOfCourse) {
        $this->ClickOnhistoryLink($TypeOfCourse);
    }

    /**
     * @Then user clicks on :arg1 button on course page
     */
    public function userClicksOnButtonOnCoursePage($TypeOfUpload) {
        $this->clickOnUploadButton($TypeOfUpload);
    }

    /**
     * @Then user uploads course with details :arg1, :arg2, :arg3, :arg4, :arg5, :arg6, :arg7, :arg8, :arg9, :arg10, :arg8, :arg12
     */
    public function userUploadsCourseWithDetails($YearId, $TermId, $UniqueCourseSectionId, $SubjectCode, $CourseNumber, $SectionNumber, $CourseName, $CreditHours, $CollegeCode, $DeptCode, $Days, $Location) {
        $this->UploadCourse($YearId, $TermId, $UniqueCourseSectionId, $SubjectCode, $CourseNumber, $SectionNumber, $CourseName, $CreditHours, $CollegeCode, $DeptCode, $Days, $Location);
    }

    /**
     * @Then user clicks on course link on course page
     */
    public function userClicksOnCourseLinkOnCoursePage() {
        $this->ClickOnCourseLink();
    }

    /**
     * @Then user sees :arg1 in student section of course
     */
    public function userSeesInStudentSectionOfCourse($StudentName) {

        $this->verifyStudent($StudentName, "presence");
    }

    /**
     * @When user clicks on delete icon to delete :arg1 from course
     */
    public function userClicksOnDeleteIconToDeleteFromCourse($UserName) {
        $this->clickOnDeleteButton($UserName);
    }

    /**
     * @When user click on confirm Remove button on course page
     */
    public function userClickOnConfirmRemoveButtonOnCoursePage() {

        $this->ClicksOnConfirmRemoveButton();
    }

    /**
     * @When user clicks on add academic button
     */
    public function userClicksOnAddAcademicButton() {

        $this->ClickOnCreateAcademicUpdate();
    }

    /**
     * @When user fills :arg1, :arg2 for academic request
     */
    public function userFillsForAcademicRequest($Name, $Description) {
        $this->FillNameAndDescriptionForAcademicUpdate($Name, $Description);
    }

    /**
     * @When user clicks on continue button
     */
    public function userClicksOnContinueButton() {
        $this->ClickOnContinueButton();
    }

    /**
     * @When user sends academic update with :arg1, :arg2 for email in academic request
     */
    public function userFillsForEmailInAcademicRequest($Subject, $Message) {
        $this->fillSubjectAndMessageForAcademicUpdate($Subject, $Message);
    }

    /**
     * @When user chooses :arg1 in Course filter
     */
    public function userChoosesInCourseFilter($CoursesIncluded) {

        $this->SelectCourseForAcademicUpdate($CoursesIncluded);
    }

    /**
     * @Then user clicks on academic update link
     */
    public function ClickOnAcademicUpdate() {
        $this->ClickOnAcademicUpdateLink();
    }

    /**
     * @When user provide grade to :arg1 with details :arg2, :arg3, :arg4, :arg5 :arg6, :arg6
     */
    public function userProvideGradeToWithDetails($StdName, $DropDownValue, $risk, $absent, $comment, $ReferToAssitance, $SendToStudent) {

        $this->ProvideGradeToStudent($StdName, $DropDownValue, $risk, $absent, $comment, $ReferToAssitance, $SendToStudent);
    }

    /**
     * @When user clicks submit button on Academic update page
     */
    public function userClicksSubmitButtonOnAcademicUpdatePage() {

        $this->SubmitAcademicUpdate();
    }

    /**
     * @When Faculty clicks on adhoc academic update icon
     */
    public function FacultyClicksOnAdhocAcademicUpdateIcon() {

        $this->ClickOnAdhocAcademicUpdateIconOnCoursePage("Faculty");
    }

 /**
     * @Then user is able to see :arg1 academic request in Acadmic update module
     */
    public function facultySeeAcademicUpdateOnDashboard($TypeOFAcademicUpdate) {
        $this->VerifyAcademicUpdateInDashBoard($TypeOFAcademicUpdate);
    }
 
    /**
 * @When user uploads :arg1 student with details :arg2, :arg3 to :arg4 course
 */
 public function userUploadsStudentsWithDetailsToCourse($CountOfStudent,$StudentIDs,$Remove,$TypeOfCourse)
 {
     
     $this->uploadMultipleStudentsToCourse($CountOfStudent,$StudentIDs,$Remove,$TypeOfCourse);
     
 }
   /**
 * @When user clicks on :arg1 academic request in open academic update list
 */
 public function userClicksOnAcademicRequestInOpenAcademicUpdateList($TypeOfAcademicUpdate)
 { 
     $this->ClickOnAcademicUpdateInOpenAcademicUpdateSection($TypeOfAcademicUpdate);
 }

/**
 * @When user selects :arg1 from academic update dropdown to see studets status
 */
 public function userSelectsFromAcademicUpdateDropdownToSeeStudetsStatus($Option)
 {
     $this->SelectOptionFromAcademicUpdateDropDown($Option);
 }

/**
 * @Then user is able to see :arg1 with details  :arg2, :arg3, :arg4, :arg5
 */
 public function userIsAbleToSeeWithDetails($StudentName,$Grade,$Risk,$Absences,$Comments)
 {
     $this->VerifyStudentsubmittedStatus($StudentName, $Risk, $Grade, $Absences, $Comments);
 }
 /**
 * @Then user is able to see :arg1 with Not Submmited Text
 */
 public function userIsAbleToSeeWithNotSubmmitedText($StudentName)
 {
$this->VerifyStudentNotSubmittedStatus($StudentName);
 }
 
 
  /**
  * @Then user is able to see status of Academic update with :arg1 and :arg2 for :arg3 Academic Update
  */
  public function userIsAbleToSeeStatusOfAcademicUpdateWithAndForAcademicUpdate($Updated,$Total,$TypeOfAcademicUpdate)
  {
 $this->VerifyAcademicUpdateStatusOnAcademicupdatePage($Updated, $Total, $TypeOfAcademicUpdate);
 }

 
    
    //////////////////////////////////////////////////////////

 public function ClickOnAcademicUpdateInOpenAcademicUpdateSection($TypeOfAcademicUpdate)
 {
     $I=$this;
     
     if(strpos($TypeOfAcademicUpdate,"Faculty")!==false)
     {       
     $AUName=$I->getDataFromJson($this,"FacultyAcademicUpdateName");
     }
     if(strpos($TypeOfAcademicUpdate,"Coordinator")!==false)
     { 
      $AUName=$I->getDataFromJson($this,"AcademicUpdateName");
     }
       if(strpos($TypeOfAcademicUpdate,"AUScenario")!==false)
     { 
      $AUName=$I->getDataFromJson($this,"AUScenarioAcademicUpdate");
     }
   
   
     $I->click(str_replace("{{}}",$AUName,$I->Element("AcademicUpdateNameOnAUSetUpPage","CourseAndAcademicUpdatePage")));
     $I->WaitForPageToLoad($I);
     }
 
 public function uploadMultipleStudentsToCourse($CountOfStudent,$StudentIDs,$Remove,$TypeOfCourse)
 {    $I=$this;
     $Write=new \ReadAndWriteCsvFile();
      if(strpos($TypeOfCourse,"Coordinator")!==false)
     {           
         $Course=$I->getDataFromJson($this,"UniqueCourseSectionId");
     }
     if(strpos($TypeOfCourse,"Faculty")!==false)
     {
     $Course=$I->getDataFromJson($this,"FacultyUniqueCourseSectionId");
     }
     $Write->WriteUploadFileForMultipleStudentToCourse("AddMultipleStudentTocourse.csv",explode('-',$StudentIDs),  explode('-',$Remove),$Course);
    $I->UploadFiles($I,"AddMultipleStudentTocourse.csv");
     
 }
 
 
 public function SelectOptionFromAcademicUpdateDropDown($Option)
 {  $I=$this;
    $I->click($I->Element("StduentStatusDropdown","CourseAndAcademicUpdatePage"));
     if(strpos($Option,"Complete")!==false)
     {
            $I->click($I->Element("CompleteFromDropDown","CourseAndAcademicUpdatePage"));
 
     }
     else
     {
             $I->click($I->Element("IncompeleteFromDropDown","CourseAndAcademicUpdatePage"));

     }
   $I->WaitForPageToLoad($I);
   
 }
     
 public function VerifyStudentNotSubmittedStatus($StudentName)
 {
     $I=$this;
     $I->canSeeElement(str_replace("{{}}",$StudentName,$I->Element("NotSubmittedText","CourseAndAcademicUpdatePage")));
 }
 
 public function VerifyStudentsubmittedStatus($StudentName, $Risk, $Grade, $Absences, $Comments)
 {
     $I=$this;
     $I->canSeeElement(str_replace("{{}}",$StudentName,  str_replace("<<>>",$Risk,$I->Element("StudentGradesStatus","CourseAndAcademicUpdatePage"))));
     $I->canSeeElement(str_replace("{{}}",$StudentName,  str_replace("<<>>",$Grade,$I->Element("StudentGradesStatus","CourseAndAcademicUpdatePage"))));
     $I->canSeeElement(str_replace("{{}}",$StudentName,  str_replace("<<>>",$Absences,$I->Element("StudentGradesStatus","CourseAndAcademicUpdatePage"))));
     $I->canSeeElement(str_replace("{{}}",$StudentName,  str_replace("<<>>",$Comments,$I->Element("StudentGradesStatus","CourseAndAcademicUpdatePage"))));

     
 }
 
 public function VerifyAcademicUpdateStatusOnAcademicupdatePage($Updated,$Total,$TypeOfAcademicUpdate)
 {
      $I=$this;
      $I->click($I->Element("ShowLessLink", "CourseAndAcademicUpdatePage"));
      $I->wait(3);
         $percentage=($Updated*100)/$Total;
     if(strpos($TypeOfAcademicUpdate,"Faculty")!==false)
     {
         $AcademicUpdate=$I->getDataFromJson($this,"FacultyAcademicUpdateName");
         
     }
      if(strpos($TypeOfAcademicUpdate,"Coordinator")!==false)
     {
         $AcademicUpdate=$I->getDataFromJson($this,"AcademicUpdateName");
         
     }
        if(strpos($TypeOfAcademicUpdate,"AUScenario")!==false)
     {
         $AcademicUpdate=$I->getDataFromJson($this,"AUScenarioAcademicUpdate");
         
     }
   $I->canSeeElement(str_replace("{{}}",$AcademicUpdate,str_replace("<<>>",$percentage,$I->Element("AcademicUpdatepercentage","CourseAndAcademicUpdatePage"))));
     
 }

 
 public function verifyAcademicUpdateOnAcademicUpdateSetUpPage($TypeofAcademicUpdate) {
        $I = $this;
        $I->reloadPage();
        $I->WaitForPageToLoad($I);
        if (strpos($TypeofAcademicUpdate, "Coordinator") !== false) {
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "AcademicUpdateName"), $I->Element("AcademicUpdateNameOnAUSetUpPage", "CourseAndAcademicUpdatePage")));
        }
        if (strpos($TypeofAcademicUpdate, "Faculty") !== false) {
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "FacultyAcademicUpdateName"), $I->Element("AcademicUpdateNameOnAUSetUpPage", "CourseAndAcademicUpdatePage")));
        }
    }

    public function VerifyAcademicUpdateInDashBoard($TypeOfAcademicUpdate) {
        $I = $this;
        if(strpos($TypeOfAcademicUpdate,"Faculty")!==false)
        {
         $I->waitForElement(str_replace("{{}}", $I->getDataFromJson($this, "FacultyAcademicUpdateName"), $I->Element("AcademicUpdateName", "DashboardPage")),30);
        $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "FacultyAcademicUpdateName"), $I->Element("AcademicUpdateName", "DashboardPage")));
        }
        if(strpos($TypeOfAcademicUpdate,"Coordinator")!==false)
        {
         $I->waitForElement(str_replace("{{}}", $I->getDataFromJson($this, "AcademicUpdateName"), $I->Element("AcademicUpdateName", "DashboardPage")),30);
        $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "AcademicUpdateName"), $I->Element("AcademicUpdateName", "DashboardPage")));  
        }
          if(strpos($TypeOfAcademicUpdate,"AUScenario")!==false)
        {
         $I->waitForElement(str_replace("{{}}", $I->getDataFromJson($this, "AUScenarioAcademicUpdate"), $I->Element("AcademicUpdateName", "DashboardPage")),30);
        $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "AUScenarioAcademicUpdate"), $I->Element("AcademicUpdateName", "DashboardPage")));  
        }
        
        
    
    }

    public function clickOnUpdateStudentLink($AcademicUpdate = "Coordinator") {

        $I = $this;
        if (strpos($AcademicUpdate ,"Coordinator")!==false) {
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "AcademicUpdateName"), $I->Element("UpdateStduentLink", "CourseAndAcademicUpdatePage")));
        } 
        if(strpos($AcademicUpdate,"Faculty")!==false)
        {
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "FacultyAcademicUpdateName"), $I->Element("UpdateStduentLink", "CourseAndAcademicUpdatePage")));
        }
          if(strpos($AcademicUpdate,"AUScenario")!==false)
        {
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "AUScenarioAcademicUpdate"), $I->Element("UpdateStduentLink", "CourseAndAcademicUpdatePage")));
        }
        
        $I->WaitForPageToLoad($I);
    }

    public function ClickOnAcademicUpdateLink() {
        $I = $this;
        $I->waitForElement($I->Element("AcademicUpdateLink", "CourseAndAcademicUpdatePage"));
        $I->click($I->Element("AcademicUpdateLink", "CourseAndAcademicUpdatePage"));
        $I->WaitForPageToLoad($I);
    }

    public function clickOnUploadButton($TypeOfUpload) {
        $I = $this;
        $I->wait(3);
        if (strpos($TypeOfUpload, "Course") !== false) {
            if ($I->isElementDisplayed($I, $I->Element("numberOfCourse", "CourseAndAcademicUpdatePage"))) {
                $numberofCourses = $I->grabTextFrom($I->Element("numberOfCourse", "CourseAndAcademicUpdatePage"));
            } else {
                $numberofCourses = 0;
            }
            $I->writeDataInJson($this, "NumberOfCourses", $numberofCourses);
            $I->click($I->Element("CourseUploadButton", "CourseAndAcademicUpdatePage"));
        } elseif (strpos($TypeOfUpload, "Faculty") !== false) {
            $numberofFaculty = $I->grabTextFrom($I->Element("numberOfFaculty", "CourseAndAcademicUpdatePage"));

            $I->writeDataInJson($this, "NumberofFaculty", $numberofFaculty);
            $I->click($I->Element("FacultyUploadButton", "CourseAndAcademicUpdatePage"));
        } else {
            $numberofStudent = $I->grabTextFrom($I->Element("numberOfStudent", "CourseAndAcademicUpdatePage"));
            $I->writeDataInJson($this, "NumberofStudent", $numberofStudent);
            $I->click($I->Element("StudentUploadButton", "CourseAndAcademicUpdatePage"));
        }
        $I->WaitForPageToLoad($I);
    }

    public function UploadCourse($YearId, $TermId, $UniqueCourseSectionId, $SubjectCode, $CourseNumber, $SectionNumber, $CourseName, $CreditHours, $CollegeCode, $DeptCode, $Days, $Location) {
        $I = $this;
        $Write = new \ReadAndWriteCsvFile();
        $UniqueCourseSectionId = $UniqueCourseSectionId . rand(11, 999);
         $SubjectCode = $SubjectCode . rand(11, 999);
        $CourseNumber = $CourseNumber . rand(11, 999);
        $CourseName = $CourseName . rand(11, 999);
        $SectionNumber = $SectionNumber . rand(11, 999);
        $numberofCourses = $I->getDataFromJson($this, "NumberOfCourses");
        codecept_debug($numberofCourses);
        if (strpos($CourseName, "Faculty") !== false) {
            $I->writeDataInJson($this, "FacultyUniqueCourseSectionId", $UniqueCourseSectionId);
            $I->writeDataInJson($this, "FacultyCourseNameID", $SubjectCode . $CourseNumber);
            $I->writeDataInJson($this, "FacultySectionNumber", $SectionNumber);
            $Write->WriteInCSVForCourse("CourseUploadForFacultyRole.csv", $YearId, $TermId, $UniqueCourseSectionId, $SubjectCode, $CourseNumber, $SectionNumber, $CourseName, $CreditHours, $CollegeCode, $DeptCode, $Days, $Location);
            if ($numberofCourses > 0) {
                $I->UploadFiles($I, "CourseUploadForFacultyRole.csv", $numberofCourses, 1);
            } else {
                $I->UploadFiles($I, "CourseUploadForFacultyRole.csv", $numberofCourses, 0);
            }
        } elseif (strpos($CourseName, "Student") !== false) {
            $I->writeDataInJson($this, "StudentUniqueCourseSectionId", $UniqueCourseSectionId);
            $I->writeDataInJson($this, "StudentCourseNameID", $SubjectCode . $CourseNumber);
            $I->writeDataInJson($this, "StudentSectionNumber", $SectionNumber);
            $I->writeDataInJson($this, "StudentCourseName", $CourseName);
            $Write->WriteInCSVForCourse("CourseUploadForStudentRole.csv", $YearId, $TermId, $UniqueCourseSectionId, $SubjectCode, $CourseNumber, $SectionNumber, $CourseName, $CreditHours, $CollegeCode, $DeptCode, $Days, $Location);
            if ($numberofCourses > 0) {
                $I->UploadFiles($I, "CourseUploadForStudentRole.csv", 1);
            } else {
                $I->UploadFiles($I, "CourseUploadForStudentRole.csv", $numberofCourses, 0);
            }
        } else {
            $I->writeDataInJson($this, "UniqueCourseSectionId", $UniqueCourseSectionId);
            $I->writeDataInJson($this, "CourseNameID", $SubjectCode . $CourseNumber);
            $I->writeDataInJson($this, "SectionNumber", $SectionNumber);
            $Write->WriteInCSVForCourse("CourseUploadForCoordinatorRole.csv", $YearId, $TermId, $UniqueCourseSectionId, $SubjectCode, $CourseNumber, $SectionNumber, $CourseName, $CreditHours, $CollegeCode, $DeptCode, $Days, $Location);
            if ($numberofCourses > 0) {
                $I->UploadFiles($I, "CourseUploadForCoordinatorRole.csv", 1);
            } else {
                $I->UploadFiles($I, "CourseUploadForCoordinatorRole.csv", $numberofCourses, 0);
            }
        }
    }

    public function UploadFaculty($FacultyID, $PermissionSet, $Remove, $TypeOfCourse) {
        $I = $this;
        $Read = new \ReadAndWriteCsvFile();
      
        $numberOfFaculty = $I->getDataFromJson($this, "NumberofFaculty");
        if (strpos($TypeOfCourse, "Faculty") !== false) {
            $UniqueCourseSectionId = $I->getDataFromJson($this, "FacultyUniqueCourseSectionId");
            $Read->WriteFacultyForCourse("UploadFacultyToCourseForFacultyRole.csv", $UniqueCourseSectionId, $FacultyID, $PermissionSet, $Remove);
            if ($numberOfFaculty > 0) {
                $I->UploadFiles($I, "UploadFacultyToCourseForFacultyRole.csv", 1);
            } else {
                $I->UploadFiles($I, "UploadFacultyToCourseForFacultyRole.csv", 0);
            }
        }
        if (strpos($TypeOfCourse, "Student") !== false) {
            $UniqueCourseSectionId = $I->getDataFromJson($this, "StudentUniqueCourseSectionId");
            $Read->WriteFacultyForCourse("UploadFacultyToCourseForStudentRole.csv", $UniqueCourseSectionId, $FacultyID, $PermissionSet, $Remove);
            if ($numberOfFaculty > 0) {
                $I->UploadFiles($I, "UploadFacultyToCourseForStudentRole.csv", 1);
            } else {
                $I->UploadFiles($I, "UploadFacultyToCourseForStudentRole.csv", 0);
            }
        }

        if (strpos($TypeOfCourse, "Coordinator") !== false) {
            $UniqueCourseSectionId = $I->getDataFromJson($this, "UniqueCourseSectionId");
            $Read->WriteFacultyForCourse("UploadFacultyToCourseForCoordinatoRole.csv", $UniqueCourseSectionId, $FacultyID, $PermissionSet, $Remove);
            if ($numberOfFaculty > 0) {
                $I->UploadFiles($I, "UploadFacultyToCourseForCoordinatoRole.csv", 1);
            } else {
                $I->UploadFiles($I, "UploadFacultyToCourseForCoordinatoRole.csv", 0);
            }
        }
    }

    public function uploadStudent($StudentId, $Remove, $TypeOfCourse) {

        $I = $this;
        $Read = new \ReadAndWriteCsvFile();
               $numberOfStudent = $I->getDataFromJson($this, "NumberofStudent");
        if (strpos($TypeOfCourse, "Faculty") !== false) {
            $UniqueCourseSectionId = $I->getDataFromJson($this, "FacultyUniqueCourseSectionId");
            $Read->WriteStudentForCourse("UploadStudentToCourseForFaculty.csv", $UniqueCourseSectionId, $StudentId, $Remove);
           if($numberOfStudent>0){
            $I->UploadFiles($I, "UploadStudentToCourseForFaculty.csv", 1);
           }
           else
           {
                          $I->UploadFiles($I, "UploadStudentToCourseForFaculty.csv", 0);
           }
        }
        if (strpos($TypeOfCourse, "Student") !== false) {
            $UniqueCourseSectionId = $I->getDataFromJson($this, "StudentUniqueCourseSectionId");
            $Read->WriteStudentForCourse("UploadStudentToCourseForStudentRole.csv", $UniqueCourseSectionId, $StudentId, $Remove);
          if($numberOfStudent>0){
            $I->UploadFiles($I, "UploadStudentToCourseForStudentRole.csv", 1);
          }
          else
          {
                          $I->UploadFiles($I, "UploadStudentToCourseForStudentRole.csv", 0);
          }
        }
        if (strpos($TypeOfCourse, "Coordinator") !== false) {
            $UniqueCourseSectionId = $I->getDataFromJson($this, "UniqueCourseSectionId");
            $Read->WriteStudentForCourse("UploadStudentToCourseForCoordinatorRole.csv", $UniqueCourseSectionId, $StudentId, $Remove);
             if($numberOfStudent>0){
            $I->UploadFiles($I, "UploadStudentToCourseForCoordinatorRole.csv",1);
             }else{
                             $I->UploadFiles($I, "UploadStudentToCourseForCoordinatorRole.csv",0);
             }
        }
        
    }

    public function VerifyAndClickOnCourse($TypeOfCourse) {
        $I = $this;
        if (strpos($TypeOfCourse, "Coordinator") !== false) {
            $I->VerifyCourse($I->getDataFromJson($this, "CourseNameID"));
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "CourseNameID"), $I->Element("RosterViewButton", "CourseAndAcademicUpdatePage")));
        }
        if (strpos($TypeOfCourse, "Faculty") !== false) {
            $I->VerifyCourse($I->getDataFromJson($this, "FacultyCourseNameID"));
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "FacultyCourseNameID"), $I->Element("RosterViewButton", "CourseAndAcademicUpdatePage")));
        }
        if (strpos($TypeOfCourse, "Student") !== false) {
            $I->VerifyCourse($I->getDataFromJson($this, "StudentCourseNameID"));
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "StudentCourseNameID"), $I->Element("RosterViewButton", "CourseAndAcademicUpdatePage")));
        }
        $I->WaitForPageToLoad($I);
    }

    public function VerifyCourse($CourseName) {
        $I = $this;
        $I->WaitForPageToLoad($I);
        $I->wait(3); //Due to latency issue
        //Added due to performance issues with the Application
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
                    $Course = $I->grabTextFrom("(//td[@ng-bind='course.subject_course'])[$row]");
                    codecept_debug($Course);
                    if ($Course == $CourseName) {
                        $I->canSee($CourseName);
                        return;
                    }
                }
            }
        } else {
            for ($row = 1; $row <= 25; $row++) {//To grab the text of each row containing Permission Set on the page.
                $Course = $I->grabTextFrom("(//td[@ng-bind='course.subject_course'])[$row]");
                if ($Course == $CourseName) {
                    $I->canSee($CourseName, "(//td[@ng-bind='course.subject_course'])[$row]");
                    return;
                }
            }
        }
    }

    public function ClickOnCourseLink() {
        $I = $this;
        $I->click($I->Element("CourseLink", "CourseAndAcademicUpdatePage"));
        $I->WaitForPageToLoad($I);
    }

    public function SeeRosterView($TypeOfCourse = "createdForCoordinator") {
        $I = $this;
        if (strpos($TypeOfCourse, "Coordinator") !== false) {
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "CourseNameID"), $I->Element("CourseNameIDInsideRosterView", "CourseAndAcademicUpdatePage")));
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "SectionNumber"), $I->Element("SectionNumberInsideRosterView", "CourseAndAcademicUpdatePage")));
        }

        if (strpos($TypeOfCourse, "Faculty") !== false) {
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "FacultyCourseNameID"), $I->Element("CourseNameIDInsideRosterView", "CourseAndAcademicUpdatePage")));
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "FacultySectionNumber"), $I->Element("SectionNumberInsideRosterView", "CourseAndAcademicUpdatePage")));
        }

        if (strpos($TypeOfCourse, "Student") !== false) {
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "StudentCourseNameID"), $I->Element("CourseNameIDInsideRosterView", "CourseAndAcademicUpdatePage")));
            $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "StudentSectionNumber"), $I->Element("SectionNumberInsideRosterView", "CourseAndAcademicUpdatePage")));
        }
        $I->canSeeElement($I->Element("RosterViewHeader", "CourseAndAcademicUpdatePage"));
        $I->canSeeInCurrentUrl("courses/view-course");
    }

    public function verifyFaculty($FacultyName, $Type) {
        $I = $this;
        $Faculty = explode(',', $FacultyName);
        if ($Type == 'presence') {
            $I->canSeeElement(str_replace("{{}}", $Faculty[0], $I->Element("FacultyName", "CourseAndAcademicUpdatePage")));
            $I->canSeeElement(str_replace("{{}}", $Faculty[1], $I->Element("FacultyName", "CourseAndAcademicUpdatePage")));
        } else {
            $I->cantSeeElement(str_replace("{{}}", $Faculty[0], $I->Element("FacultyName", "CourseAndAcademicUpdatePage")));
            $I->cantSeeElement(str_replace("{{}}", $Faculty[1], $I->Element("FacultyName", "CourseAndAcademicUpdatePage")));
        }
    }

    public function verifyStudent($StudentName, $Type) {
        $I = $this;
        $Student = explode(',', $StudentName);
        if ($Type == 'presence') {
            $I->canSeeElement(str_replace("{{}}", $Student[0], $I->Element("StudentName", "CourseAndAcademicUpdatePage")));
            $I->canSeeElement(str_replace("{{}}", $Student[1], $I->Element("StudentName", "CourseAndAcademicUpdatePage")));
        } else {
            $I->cantSeeElement(str_replace("{{}}", $Student[0], $I->Element("StudentName", "CourseAndAcademicUpdatePage")));
            $I->cantSeeElement(str_replace("{{}}", $Student[1], $I->Element("StudentName", "CourseAndAcademicUpdatePage")));
        }
    }

    public function clickOnDeleteButton($UserName) {
        $I = $this;
        $User = explode(',', $UserName);
        $I->click(str_replace("{{}}", $User[1], $I->Element("deleteIcon", "CourseAndAcademicUpdatePage")));
    }

    public function ClicksOnConfirmRemoveButton() {
        $I = $this;
        $I->waitForElement($I->Element("ConfirmDelete", "CourseAndAcademicUpdatePage"),60);
        $I->click($I->Element("ConfirmDelete", "CourseAndAcademicUpdatePage"));
 
    }

   

    ///////////////////////////////////////Academic Updates/////////////////////////////////////

    public function ProvideGradeToStudent($StdName, $DropDownValue, $risk, $absent, $comment, $ReferToAssitance, $SendToStudent) {
        $I = $this;
        $studentNameArray = explode(',', $StdName);
        $Std_FirstName = $studentNameArray[0];
        $Std_LastName = $studentNameArray[1];
        $I->WaitForElement(str_replace("{{}}", $Std_FirstName, str_replace("<<>>", $Std_LastName, str_replace("[[]]", $risk, $I->Element("RiskBtn", "CourseAndAcademicUpdatePage")))));
        $I->click(str_replace("{{}}", $Std_FirstName, str_replace("<<>>", $Std_LastName, str_replace("[[]]", $risk, $I->Element("RiskBtn", "CourseAndAcademicUpdatePage")))));
        $I->WaitForElement(str_replace("{{}}", $Std_FirstName, str_replace("<<>>", $Std_LastName, $I->Element("ProgressDropDown", "CourseAndAcademicUpdatePage"))));
        $I->click(str_replace("{{}}", $Std_FirstName, str_replace("<<>>", $Std_LastName, $I->Element("ProgressDropDown", "CourseAndAcademicUpdatePage"))));
       $I->wait(2);
        $I->WaitForElement(str_replace("<<>>", $DropDownValue, $I->Element("ProgressGrades", "CourseAndAcademicUpdatePage")));
        $I->click(str_replace("<<>>", $DropDownValue, $I->Element("ProgressGrades", "CourseAndAcademicUpdatePage")));
        $I->WaitForElement(str_replace("{{}}", $Std_FirstName, str_replace("<<>>", $Std_LastName, $I->Element("Absence", "CourseAndAcademicUpdatePage"))));
        $I->fillField(str_replace("{{}}", $Std_FirstName, str_replace("<<>>", $Std_LastName, $I->Element("Absence", "CourseAndAcademicUpdatePage"))), $absent);
        $I->WaitForElement(str_replace("{{}}", $Std_FirstName, str_replace("<<>>", $Std_LastName, $I->Element("Comment", "CourseAndAcademicUpdatePage"))));
        $I->fillField(str_replace("{{}}", $Std_FirstName, str_replace("<<>>", $Std_LastName, $I->Element("Comment", "CourseAndAcademicUpdatePage"))), $comment);
        if (strpos($ReferToAssitance, "yes") !== false) {
            $I->WaitForElement(str_replace("{{}}", $Std_FirstName, str_replace("<<>>", $Std_LastName, $I->Element("ReferCheckBox", "CourseAndAcademicUpdatePage"))));
            $I->click(str_replace("{{}}", $Std_FirstName, str_replace("<<>>", $Std_LastName, $I->Element("ReferCheckBox", "CourseAndAcademicUpdatePage"))));
        }
        if (strpos($SendToStudent, "yes") !== false) {

            $I->WaitForElement(str_replace("{{}}", $Std_LastName, $I->Element("SendCheckbox", "CourseAndAcademicUpdatePage")));
            $I->click(str_replace("{{}}",$Std_LastName, $I->Element("SendCheckbox", "CourseAndAcademicUpdatePage")));
        }
    }

    public function SubmitAcademicUpdate() {
        $I = $this;
        $I->waitForElement($I->Element("SendUpdatesBtn", "CourseAndAcademicUpdatePage"),60);
        $I->click($I->Element("SendUpdatesBtn", "CourseAndAcademicUpdatePage"));
    }

    public function ReferToStudent() {
        $I = $this;
        $I->wait(3);
        $I->waitForElement($I->Element("ReferModalWin", "CourseAndAcademicUpdatePage"),60);
        $I->canSeeElement($I->Element("ReferModalWin", "CourseAndAcademicUpdatePage"));
        $I->waitForElement($I->Element("AcademicSendText", "CourseAndAcademicUpdatePage"),60);
        $I->canSeeElement($I->Element("AcademicSendText", "CourseAndAcademicUpdatePage"));
        $I->click($I->Element("CreateReferral", "CourseAndAcademicUpdatePage"));
    }

    public function ClickOnCreateAcademicUpdate() {
        $I = $this;
        $I->click($I->Element("CreateAcademicUpdateButton", "CourseAndAcademicUpdatePage"));
        $I->WaitForModalWindowToAppear($I);
    }

    public function FillNameAndDescriptionForAcademicUpdate($Name, $Description) {
        $I = $this;
        $Name = $Name . round(microtime(true) * 1000);
        if (strpos($Name, "Faculty") !== false) {
            $I->writeDataInJson($this, "FacultyAcademicUpdateName", $Name);
        }
        if (strpos($Name, "Coordinator") !== false) {
            $I->writeDataInJson($this, "AcademicUpdateName", $Name);
        }
           if (strpos($Name, "AUScenario") !== false) {
            $I->writeDataInJson($this, "AUScenarioAcademicUpdate", $Name);
        }
        

        $I->waitForElement($I->Element("AcademicRequestname", "CourseAndAcademicUpdatePage"),60);
        $I->fillField($I->Element("AcademicRequestname", "CourseAndAcademicUpdatePage"), $Name);
        $I->fillField($I->Element("AcademicDescription", "CourseAndAcademicUpdatePage"), $Description);
        $I->canSeeElement($I->Element("academicDueDate", "CourseAndAcademicUpdatePage"));
        $I->click($I->Element("academicDueDate", "CourseAndAcademicUpdatePage"));
        $FutureDate = $I->getNextDayDate('Asia/Calcutta');
        $I->click(str_replace("{{}}", $FutureDate, $I->Element("SelectDate", "CourseAndAcademicUpdatePage")));
    }

    public function ClickOnContinueButton() {
        $I = $this;
        $I->click($I->Element("continueButton", "CourseAndAcademicUpdatePage"));
        $I->wait(3);
    }

    public function fillSubjectAndMessageForAcademicUpdate($Subject, $Message) {

        $I = $this;
        $I->waitForElement($I->Element("Subject", "CourseAndAcademicUpdatePage"),60);
        $I->fillField($I->Element("Subject", "CourseAndAcademicUpdatePage"), $Subject);
        $I->fillField($I->Element("OptionalMessage", "CourseAndAcademicUpdatePage"), $Message);
        $I->click($I->Element("SendAcadmicUpdate", "CourseAndAcademicUpdatePage"));
    }

    public function VerifyAcdemicUpdateInAcademicUpdatesSection($TypeOfAcademicUpdate, $TypeOfSection) {
        $I = $this;
        if ($TypeOfSection == 'open') {
            $I->click($I->Element("ShowLessLink", "CourseAndAcademicUpdatePage"));
            $I->wait(3);
            if (strpos($TypeOfAcademicUpdate, "Coordinator") !== false) {
                $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "AcademicUpdateName"), $I->Element("OpenAcademicUpdate", "CourseAndAcademicUpdatePage")));
            }
            if (strpos($TypeOfAcademicUpdate, "Faculty") !== false) {
                $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "FacultyAcademicUpdateName"), $I->Element("OpenAcademicUpdate", "CourseAndAcademicUpdatePage")));
            }
            if (strpos($TypeOfAcademicUpdate, "AUScenario") !== false) {
                $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "AUScenarioAcademicUpdate"), $I->Element("OpenAcademicUpdate", "CourseAndAcademicUpdatePage")));
            }
            
        } 
        
        else {
            if (strpos($TypeOfAcademicUpdate, "Coordinator") !== false) {
                $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "AcademicUpdateName"), $I->Element("ClosdAcademicUpdate", "CourseAndAcademicUpdatePage")));
            }
            if (strpos($TypeOfAcademicUpdate, "Faculty") !== false) {
                $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "FacultyAcademicUpdateName"), $I->Element("ClosdAcademicUpdate", "CourseAndAcademicUpdatePage")));
            }
               if (strpos($TypeOfAcademicUpdate, "AUScenario") !== false) {
                $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson($this, "AUScenarioAcademicUpdate"), $I->Element("ClosdAcademicUpdate", "CourseAndAcademicUpdatePage")));
            }
        }
    }

    public function SelectCourseForAcademicUpdate($CoursesIncluded) {
        $I = $this;
        $I->click(str_replace("{{}}", "Courses", $I->Element("filter", "CourseAndAcademicUpdatePage")));


        if ($CoursesIncluded == 'All') {
            $I->waitForElement($I->Element("SelectAllCourseRadioButton", "CourseAndAcademicUpdatePage"),60);
            $I->click($I->Element("SelectAllCourseRadioButton", "CourseAndAcademicUpdatePage"));
        } else {

            $I->waitForElement($I->Element("SelectCourseRadioButton", "CourseAndAcademicUpdatePage"),60);
            $I->click($I->Element("SelectCourseRadioButton", "CourseAndAcademicUpdatePage"));
        }
        if (strpos($CoursesIncluded, "Faculty") !== false) {
            $I->fillField($I->Element("TextFieldForCourse", "CourseAndAcademicUpdatePage"), $I->getDataFromJson($this, 'FacultyCourseNameID'));
            $I->waitForElement(str_replace("{{}}", $I->getDataFromJson($this, 'FacultyCourseNameID'), $I->Element("SuggestionBox", "CourseAndAcademicUpdatePage")),60);
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, 'FacultyCourseNameID'), $I->Element("SuggestionBox", "CourseAndAcademicUpdatePage")));
        }
        if (strpos($CoursesIncluded, "Coordinator") !== false) {
            $I->fillField($I->Element("TextFieldForCourse", "CourseAndAcademicUpdatePage"), $I->getDataFromJson($this, 'CourseNameID'));
            $I->waitForElement(str_replace("{{}}", $I->getDataFromJson($this, 'CourseNameID'), $I->Element("SuggestionBox", "CourseAndAcademicUpdatePage")),60);
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, 'CourseNameID'), $I->Element("SuggestionBox", "CourseAndAcademicUpdatePage")));
        }

        $I->click($I->Element("AddButton", "CourseAndAcademicUpdatePage"));
    }

    public function getNextDayDate($TimeZone) {
        date_default_timezone_set($TimeZone);
        $array = explode('-', date('d-m-Y', strtotime("+1day")));
        return $array[0];
    }

    public function ClickOnUpdateTheseStudentLink() {
        $I = $this;
        $I->click($I->Element("ShowLessLink", "CourseAndAcademicUpdatePage"));
        $I->click($I->Element("UpdateTheseStudentLink", "CourseAndAcademicUpdatePage"));
    }

    public function ClickOnhistoryLink($TypeOfCourse) {
        $I = $this;
        if (strpos($TypeOfCourse, "Faculty") !== false) {
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "FacultyCourseNameID"), $I->Element("HistoryLink", "AboutTheStudentPage")));
        }
        if (strpos($TypeOfCourse, "Coordinator") !== false) {
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "CourseNameID"), $I->Element("HistoryLink", "AboutTheStudentPage")));
        }
        if (strpos($TypeOfCourse, "Student") !== false) {
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "StudentCourseNameID"), $I->Element("HistoryLink", "AboutTheStudentPage")));
        }

        $I->WaitForModalWindowToAppear($I);
    }

    public function VerifyGradesOnStudentProfile($DropDownValue, $risk, $absent, $comment, $ReferToAssitance, $SendToStudent) {
        $I = $this;
        $I->wait(3);
        $I->canSeeElement(str_replace("{{}}", $DropDownValue, $I->Element("ContentOnWindow", "AboutTheStudentPage")));
        $I->canSeeElement(str_replace("{{}}", $risk, $I->Element("ContentOnWindow", "AboutTheStudentPage")));
        $I->canSeeElement(str_replace("{{}}", $absent, $I->Element("ContentOnWindow", "AboutTheStudentPage")));
        $I->canSeeElement(str_replace("{{}}", $comment, $I->Element("ContentOnWindow", "AboutTheStudentPage")));
        if ($ReferToAssitance == 'yes') {
            $I->canSeeElement(str_replace("{{}}", $comment, $I->Element("CheckSignForRefer", "AboutTheStudentPage")));
        } else {
            $I->cantSeeElement(str_replace("{{}}", $comment, $I->Element("CheckSignForRefer", "AboutTheStudentPage")));
        }
        if ($SendToStudent == 'yes') {
            $I->canSeeElement(str_replace("{{}}", $comment, $I->Element("CheckSignForSend", "AboutTheStudentPage")));
        } else {
            $I->cantSeeElement(str_replace("{{}}", $comment, $I->Element("CheckSignForSend", "AboutTheStudentPage")));
        }
        $I->reloadPage();
        $I->WaitForPageToLoad($I);
    }

    public function ClickOnAdhocAcademicUpdateIconOnCoursePage($TypeOfCourse) {
        $I = $this;
        if (strpos($TypeOfCourse, "Coordinator") !== false) {
            $I->waitForElement(str_replace("{{}}", $I->getDataFromJson($this, "CourseNameID"), $I->Element("AdhocAcdemicIcon", "CourseAndAcademicUpdatePage")),60);
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "CourseNameID"), $I->Element("AdhocAcdemicIcon", "CourseAndAcademicUpdatePage")));
        }
        if (strpos($TypeOfCourse, "Faculty") !== false) {
            $I->waitForElement(str_replace("{{}}", $I->getDataFromJson($this, "FacultyCourseNameID"), $I->Element("AdhocAcdemicIcon", "CourseAndAcademicUpdatePage")),60);
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "FacultyCourseNameID"), $I->Element("AdhocAcdemicIcon", "CourseAndAcademicUpdatePage")));
        }
        if (strpos($TypeOfCourse, "Student") !== false) {
            $I->waitForElement(str_replace("{{}}", $I->getDataFromJson($this, "StudentCourseNameID"), $I->Element("AdhocAcdemicIcon", "CourseAndAcademicUpdatePage")),60);
            $I->click(str_replace("{{}}", $I->getDataFromJson($this, "StudentCourseNameID"), $I->Element("AdhocAcdemicIcon", "CourseAndAcademicUpdatePage")));
        }
        $I->WaitForPageToLoad($I);
    }

    public function ClickOnUploadAcademicUpdateButton() {
        $I = $this;
        $I->waitForElement($I->Element("UploadAcademicUpdateButton", "CourseAndAcademicUpdatePage"),60);
        $I->click($I->Element("UploadAcademicUpdateButton", "CourseAndAcademicUpdatePage"));
        $I->WaitForPageToLoad($I);
    }

    public function UploadAcademicUpdate($ExternalID, $DropDownValueOfProgressGrade, $risk, $absent, $comment) {
        $I = $this;
        $Write = new \ReadAndWriteCsvFile();

        $Write->WriteAcademicUpdate("AcademicUpdate.csv", $I->getDataFromJson($this, "UniqueCourseSectionId"), $ExternalID, $risk, $DropDownValueOfProgressGrade, "", $absent, $comment);
        $I->UploadFiles($I, "AcademicUpdate.csv");
    }

}
