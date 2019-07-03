<?php

namespace Step\Acceptance ;


class StudentCourseAndAcademicUpdateStep extends \AcceptanceTester {
 
     /**
     * @Then student is able to see details of :arg1 course  on student course page with :arg2, :arg3 and :arg4
     */
     public function studentSeeCourseDetailsOnStudentCoursePageWithAnd($TypeOfCourse,$Faculty,$Days, $Location)
     {
            $this->VerifyCourseDetails($Faculty,$Days, $Location);
     }

    /**
     * @Then student is able to see grades with details :arg1,:arg2 and :arg3 on student course page
     */
     public function studentSeeGradesWithDetailsAndOnStudentCoursePage($Absences,$Grades,$Comment)
     {
         $this->VerifyPresenceOfGrades($Absences,$Grades,$Comment);
     }

    /**
     * @Then student is not able to see grades with details :arg1,:arg2 and :arg3 on student course page
     */
    public function studentShouldNotSeeGradesWithDetailsAndOnStudentCoursePage($Absences,$Grades,$Comment)
     {
         $this->VerifyAbsenceOfGrades($Absences,$Grades,$Comment);
     }


    //////////////////////////////
     public function VerifyCourseDetails($Faculty,$Days, $Location)
     { $I=$this;
         
       $CourseTitle=$I->getDataFromJson(new CourseAndAcademicUpdateStep($I->getScenario()),"StudentCourseName");
       $CourseNameID=$I->getDataFromJson(new CourseAndAcademicUpdateStep($I->getScenario()),"StudentCourseNameID");
        $sectionNumber=$I->getDataFromJson(new CourseAndAcademicUpdateStep($I->getScenario()),"StudentSectionNumber");

        $I->canSeeElement(str_replace("{{}}",$CourseNameID,$I->Element("SubAndCourseNum","StudentCourseAndAcademicUpdatePage")));
        $I->canSeeElement(str_replace("{{}}",$sectionNumber,$I->Element("Section","StudentCourseAndAcademicUpdatePage")));
       $I->canSeeElement(str_replace("{{}}",$CourseTitle,$I->Element("CourseTitle","StudentCourseAndAcademicUpdatePage")));
       $I->canSeeElement(str_replace("{{}}",$Faculty,$I->Element("FacultyName","StudentCourseAndAcademicUpdatePage")));
       $I->canSeeElement(str_replace("{{}}",$Days, str_replace("<<>>",$CourseTitle,$I->Element("Time","StudentCourseAndAcademicUpdatePage"))));
       $I->canSeeElement(str_replace("{{}}",$Location, str_replace("<<>>",$CourseTitle,$I->Element("Location","StudentCourseAndAcademicUpdatePage"))));
      
     }
    
    public function VerifyPresenceOfGrades($Absences,$Grades,$Comment)
    {
     $I=$this;
     $CourseTitle=$I->getDataFromJson(new CourseAndAcademicUpdateStep($I->getScenario()),"StudentCourseName");
     $I->canSeeElement(str_replace("{{}}",$CourseTitle, str_replace("<<>>",$Absences,$I->Element("Absences","StudentCourseAndAcademicUpdatePage"))));
      $I->canSeeElement(str_replace("{{}}",$CourseTitle, str_replace("<<>>",$Grades,$I->Element("InProgressGrade","StudentCourseAndAcademicUpdatePage"))));
      $I->canSeeElement(str_replace("{{}}",$CourseTitle, str_replace("<<>>",$Comment,$I->Element("Comment","StudentCourseAndAcademicUpdatePage"))));
  
    }
     
    public function VerifyAbsenceOfGrades($Absences,$Grades,$Comment)
    {
        $I=$this;
     $CourseTitle=$I->getDataFromJson(new CourseAndAcademicUpdateStep($I->getScenario()),"StudentCourseName");
     $I->cantSeeElement(str_replace("{{}}",$CourseTitle, str_replace("<<>>",$Absences,$I->Element("Absences","StudentCourseAndAcademicUpdatePage"))));
      $I->cantSeeElement(str_replace("{{}}",$CourseTitle, str_replace("<<>>",$Grades,$I->Element("InProgressGrade","StudentCourseAndAcademicUpdatePage"))));
      $I->cantSeeElement(str_replace("{{}}",$CourseTitle, str_replace("<<>>",$Comment,$I->Element("Comment","StudentCourseAndAcademicUpdatePage"))));
       
    }  

}
