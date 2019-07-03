<?php

class ReadAndWriteCsvFile {
  public Static $paginationCount;
    public function WriteInCSV($Count, $fileName) {
        $csv_filename = realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..") . DIRECTORY_SEPARATOR . 'uploadFiles' . DIRECTORY_SEPARATOR . $fileName;
        for ($i = 0; $i < $Count; $i++) {
            $ExternalId[$i] = strval(rand(10, 999) . round(microtime(true)));
            $firstName[$i] = "Test" . strval(rand(10, 999) . round(microtime(true)));
            $LastName[$i] = "Qa" . strval(rand(10, 999) . round(microtime(true)));
            $PrimaryMobile[$i] = strval(rand(10, 999) . round(microtime(true)));
            $PrimaryEmail[$i] = "Test" . strval(rand(10, 999) . round(microtime(true))) . "@mailinator.com";
        }
        $fd = fopen($csv_filename, "w+");
        if (strpos($fileName, "Invalid_Faculty")!==false) {
            fputcsv($fd, array("ExternalId", "Firstname", "Lastname", "PrimaryMobile", "PrimaryEmail"));
            for ($i = 0; $i < $Count; $i++) {
                fputcsv($fd, array("", $firstName[$i], $LastName[$i], $PrimaryMobile[$i], $PrimaryEmail[$i]));
            }
        } else {
            fputcsv($fd, array("ExternalId", "Firstname", "Lastname", "PrimaryMobile", "PrimaryEmail"));
            for ($i = 0; $i < $Count; $i++) {
                fputcsv($fd, array($ExternalId[$i], $firstName[$i], $LastName[$i],
                    $PrimaryMobile[$i], $PrimaryEmail[$i]));
            }
        }

        fclose($fd);
    }
    
    public function WriteDataForUploadingStudentToGroup($count,$NameOfFile,$GroupID,$GroupHeader,$Skip=0)
    {     
        
        $csv_filename = realpath(__DIR__ .DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..") .DIRECTORY_SEPARATOR. 'uploadFiles'.DIRECTORY_SEPARATOR.$NameOfFile;
        $studentFileName=fopen('tests/Student_Upload.csv','r');
         $fd = fopen($csv_filename, "w+");
          fputcsv($fd, array("Externalid", "FirstName", "LastName","PrimaryEmail",$GroupHeader));
          for($i=0;$i<=$Skip;$i++){
          fgetcsv($studentFileName);
          } 
       for($i=0;$i<$count;$i++)
        {
            $StudentArray=  fgetcsv($studentFileName);
            fputcsv($fd, array($StudentArray[0],$StudentArray[1],$StudentArray[2],$StudentArray[3],$GroupID));
        }
        
        fclose($fd);
        fclose($studentFileName);
    }
    
    
    
   public function  WriteStudentsForUploadingStudentFromGroupEditPage($count,$NameOfFile)
   {  
       $csv_filename = realpath(__DIR__ .DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..") .DIRECTORY_SEPARATOR. 'uploadFiles'.DIRECTORY_SEPARATOR.$NameOfFile;
        $studentFileName=fopen('tests/Student_Upload.csv','r');
         $fd = fopen($csv_filename, "w+");
          fputcsv($fd, array("ExternalId"));
          fgetcsv($studentFileName);
        for($i=0;$i<$count;$i++)
        {
            $StudentArray=  fgetcsv($studentFileName);
            fputcsv($fd, array($StudentArray[0]));
            
        }
        
        fclose($fd);
        fclose($studentFileName);
       
   }
           
    
   public function WriteStudentFileWithoutExternalID($NameOfFile,$GroupOperaion,$GroupHeader)
   {
       $csv_filename = realpath(__DIR__ .DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..") .DIRECTORY_SEPARATOR. 'uploadFiles'.DIRECTORY_SEPARATOR.$NameOfFile;
        $studentFileName=fopen('tests/Student_Upload.csv','r');
         $fd = fopen($csv_filename, "w+");
          fputcsv($fd, array("ExternalID","Firstname", "Lastname","PrimaryEmail",$GroupHeader));
          fgetcsv($studentFileName);
          $StudentArray=  fgetcsv($studentFileName);
            fputcsv($fd, array("",$StudentArray[1],$StudentArray[2],$StudentArray[3],$GroupOperaion));
       
        fclose($fd);
        fclose($studentFileName);
       
       
   }
   
   
   public function WriteDataForSubGroup($FileName,$subgroupName, $SubgroupID,$ParentGroupID)
   {
        $csv_filename = realpath(__DIR__ .DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..") .DIRECTORY_SEPARATOR. 'uploadFiles'.DIRECTORY_SEPARATOR.$FileName;
        $fd = fopen($csv_filename, "w+");
        fputcsv($fd, array("ParentGroupId","GroupId","GroupName"));
        fputcsv($fd, array($ParentGroupID,$SubgroupID,$subgroupName));
        fclose($fd);
   }
   
   public function WriteDataForFacultyToGroup($FileName,$ExternalID, $FirstName, $LastName, $Email, $GroupName,$Permission, $invisible, $Remove)
   {
        $csv_filename = realpath(__DIR__ .DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..") .DIRECTORY_SEPARATOR. 'uploadFiles'.DIRECTORY_SEPARATOR.$FileName;
        $fd = fopen($csv_filename, "w+");
        fputcsv($fd, array("ExternalId","FirstName","LastName","PrimaryEmail","GroupID","PermissionSet","invisible","Remove"));
        fputcsv($fd,array($ExternalID, $FirstName, $LastName, $Email, $GroupName,$Permission, $invisible, $Remove));       
        fclose($fd);
       
       
       
   }
   
     public function WriteInCSVWithProfile($Count,$profile, $fileName) {
        $csv_filename = realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..") . DIRECTORY_SEPARATOR . 'uploadFiles' . DIRECTORY_SEPARATOR . $fileName;
        for ($i = 0; $i < $Count; $i++) {
            $ExternalId[$i] = strval(rand(10, 999) . round(microtime(true)));
            $firstName[$i] = "Test" . strval(rand(10, 999) . round(microtime(true)));
            $LastName[$i] = "Qa" . strval(rand(10, 999) . round(microtime(true)));
            $PrimaryMobile[$i] = strval(rand(10, 999) . round(microtime(true)));
            $PrimaryEmail[$i] = "Test" . strval(rand(10, 999) . round(microtime(true))) . "@mailinator.com";
        }
        $fd = fopen($csv_filename, "w+");
        if (strpos($fileName, "StudentWithTextProfile")!==false) {
            fputcsv($fd, array("ExternalId", "Firstname", "Lastname", "PrimaryMobile", "PrimaryEmail",$profile));
            for ($i = 0; $i < $Count; $i++) {
                fputcsv($fd, array($ExternalId[$i], $firstName[$i], $LastName[$i], $PrimaryMobile[$i], $PrimaryEmail[$i],""));
            }
        } else {
            fputcsv($fd, array("ExternalId", "Firstname", "Lastname", "PrimaryMobile", "PrimaryEmail"));
            for ($i = 0; $i < $Count; $i++) {
                fputcsv($fd, array($ExternalId[$i], $firstName[$i], $LastName[$i],$PrimaryMobile[$i], $PrimaryEmail[$i]));
            }
        }

        fclose($fd);
    }
    
      public function WriteInCSVWithStatus($status, $externalId,$firstName,$lastName,$emailId,$fileName) {
        $csv_filename = realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..") . DIRECTORY_SEPARATOR . 'uploadFiles' . DIRECTORY_SEPARATOR . $fileName;
        $fd = fopen($csv_filename, "w+");
        if (strpos($status, "inactive") !== false) {
            fputcsv($fd, array("ExternalId", "Firstname", "Lastname", "PrimaryEmail", "IsActive", "YearID", "Participating"));
            fputcsv($fd, array($externalId, $firstName, $lastName, $emailId, "0", "201617", "1"));
        } else {
            fputcsv($fd, array("ExternalId", "Firstname", "Lastname", "PrimaryEmail", "IsActive", "YearID", "Participating"));
            fputcsv($fd, array($externalId, $firstName, $lastName, $emailId, "1", "201617", "1"));
        }
        fclose($fd);
    }
   
   public function WriteInCSVForCourse($Filename,$YearId,$TermId,$UniqueCourseSectionId,$SubjectCode,$CourseNumber,$SectionNumber,$CourseName,$CreditHours,$CollegeCode,$DeptCode,$Days,$Location)
   {
       $csv_filename = realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..") . DIRECTORY_SEPARATOR . 'uploadFiles' . DIRECTORY_SEPARATOR . $Filename;
       $fd = fopen($csv_filename, "w+");
       fputcsv($fd,array("YearId","TermId","UniqueCourseSectionId","SubjectCode","CourseNumber","SectionNumber","CourseName","CreditHours","CollegeCode","DeptCode","Days/Times","Location"));
       fputcsv($fd,array($YearId,$TermId,$UniqueCourseSectionId,$SubjectCode,$CourseNumber,$SectionNumber,$CourseName,$CreditHours,$CollegeCode,$DeptCode,$Days,$Location));
       fclose($fd); 
       
   }
   
   
   public function WriteFacultyForCourse($Filename,$UniqueCourseSectionId,$FacultyID,$PermissionSet,$Remove)
   {
       $csv_filename = realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..") . DIRECTORY_SEPARATOR . 'uploadFiles' . DIRECTORY_SEPARATOR . $Filename;
        $fd = fopen($csv_filename, "w+");
        fputcsv($fd,array("UniqueCourseSectionId","FacultyID","PermissionSet","Remove"));
        fputcsv($fd,array($UniqueCourseSectionId,$FacultyID,$PermissionSet,$Remove) );
        fclose($fd);
   }
   
   public function WriteStudentForCourse($Filename,$UniqueCourseSectionId,$StudentId,$Remove)
   {
       $csv_filename = realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..") . DIRECTORY_SEPARATOR . 'uploadFiles' . DIRECTORY_SEPARATOR . $Filename;
        $fd = fopen($csv_filename, "w+");
        fputcsv($fd,array("UniqueCourseSectionId","StudentId","Remove"));
        fputcsv($fd,array($UniqueCourseSectionId,$StudentId,$Remove));
        fclose($fd);
   }
   
   public function WriteAcademicUpdate($FileName,$UniqueCourseSectionId,$StudentId,$FailureRisk,$InProgressGrade,$FinalGrade,$Absences,$Comments)
   {
      $csv_filename = realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..") . DIRECTORY_SEPARATOR . 'uploadFiles' . DIRECTORY_SEPARATOR . $FileName;
        $fd = fopen($csv_filename, "w+");
        fputcsv($fd,array("UniqueCourseSectionId","StudentId","FailureRisk","InProgressGrade","FinalGrade","Absences","Comments"));
        fputcsv($fd,array($UniqueCourseSectionId,$StudentId,$FailureRisk,$InProgressGrade,$FinalGrade,$Absences,$Comments));
        fclose($fd); 
       
   }
   
   
    public function UploadForSearch($fileName,$ExternalId,$firstName,$LastName,$PrimaryEmail,$PrimaryMobile,$Profile,$value,$YearId,$TermId='') {
      $csv_filename = realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..") . DIRECTORY_SEPARATOR . 'uploadFiles' . DIRECTORY_SEPARATOR . $fileName;
        $fd = fopen($csv_filename, "w+");
        if (strpos($fileName,"Upload_StudentWithYearAndProfile")!==false) {
            fputcsv($fd, array("ExternalId", "Firstname", "Lastname", "PrimaryMobile", "PrimaryEmail", "YearId", "$Profile","participating"));
            fputcsv($fd, array($ExternalId, $firstName, $LastName, $PrimaryMobile, $PrimaryEmail, $YearId,$value,"1"));
        } elseif (strpos($fileName,"Upload_StudentWithYearTermAndProfile")!==false) {
            fputcsv($fd, array("ExternalId", "Firstname", "Lastname", "PrimaryMobile", "PrimaryEmail", "YearId", "TermId", "$Profile","participating"));
            fputcsv($fd, array($ExternalId, $firstName, $LastName, $PrimaryMobile, $PrimaryEmail, $YearId, $TermId, $value,"1"));
        } elseif (strpos($fileName ,"Upload_StudentWithYearAndISP")!==false) {
            fputcsv($fd, array("ExternalId", "Firstname", "Lastname", "PrimaryMobile", "PrimaryEmail", "YearId", "$Profile","participating"));
            fputcsv($fd, array($ExternalId, $firstName, $LastName, $PrimaryMobile, $PrimaryEmail, $YearId, $value,"1"));
        } elseif (strpos($fileName,"Upload_StudentWithYearTermAndISP")!==false) {
            fputcsv($fd, array("ExternalId", "Firstname", "Lastname", "PrimaryMobile", "PrimaryEmail", "YearId", "TermId", "$Profile","participating"));
            fputcsv($fd, array($ExternalId, $firstName, $LastName, $PrimaryMobile, $PrimaryEmail, $YearId, $TermId, $value,"1"));
        }
        fclose($fd);
    }

   public function WriteUploadFileForMultipleStudentToCourse($FileName,$StudentIDs,$Remove,$Course)
   {
        $csv_filename = realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..") . DIRECTORY_SEPARATOR . 'uploadFiles' . DIRECTORY_SEPARATOR . $FileName;
        $fd = fopen($csv_filename, "w+"); 
       fputcsv($fd,array("UniqueCourseSectionId","StudentId","Remove"));
        for($i=0;$i<count($StudentIDs);$i++)
        {
            fputcsv($fd,array($Course,$StudentIDs[$i],$Remove[$i]));
        } 
        fclose($fd);
        
   }
   
   
   public function WriteFileForUploadingStudentFromInsideTheGroup($FileName,$ExternalID)
   {
        $csv_filename = realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..") . DIRECTORY_SEPARATOR . 'uploadFiles' . DIRECTORY_SEPARATOR . $FileName;
        $fd = fopen($csv_filename, "w+"); 
       fputcsv($fd,array("ExternalID"));
       fputcsv($fd,array($ExternalID));
        fclose($fd);
       
   }
   
    
}
