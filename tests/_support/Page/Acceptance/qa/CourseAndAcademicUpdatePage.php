<?php
namespace Page\Acceptance\qa;

class CourseAndAcademicUpdatePage
{public static $numberOfStudent="//span[contains(@ng-bind,'data.course_list.total_students')]"; 
   public static $numberOfFaculty="//span[contains(@ng-bind,'data.course_list.total_faculty')]"; 
   public static $numberOfCourse="//span[contains(@ng-bind,'data.course_list.total_course')]";   
   public static $CourseUploadButton='//button[@ng-enter="uploadCourse()"]';
   public static $FacultyUploadButton='//button[@ng-click="uploadFacultyToCourse()"]';
   public static $StudentUploadButton='//button[@ng-click="uploadStudentToCourse()"]';
   public static $CourseLink='//a[@href="/#/courses"]';
   public static $RosterViewButton='//td[contains(text(),"{{}}")]/..//img[@title="View Roster"]';
   public static $RosterViewHeader='(//span[contains(text(),"Roster for")])[1]';
   public static $CourseNameIDInsideRosterView='(//span[@ng-bind="course_detail.subject_code"][text()="{{}}"])[1]';
   public static $SectionNumberInsideRosterView='(//span[@ng-bind="course_detail.section_number"])[1][contains(text(),"{{}}")]';
   public static $FacultyName='//span[@title="{{}}"]';
   public static $StudentName='//span[@title="{{}}"]';
   public static $deleteIcon='//span[@title="{{}}"]/../..//td/a';
   public static $ConfirmDelete='//button[@ng-click="confirm()"]';
   
   ////////////////////////////////////
   //Faculty View
   public static $continueButton='//input[@ng-click="nextStep()"]';
    public static $AdhocAcademicUpdate='//tr[not(@style)]//td[@ng-bind="course.subject_course" and text()="{{}}"]/following-sibling::td//span[@class="add-icon-base"]';
    public static $AcademicUpdateURL='/#/academic-updates';
    public static $Breadcrumb='//div[@id="faculty-academic-update-request-view"]//span[text()="Academic update for {{}} <<>>"]';
    public static $AcademicRequestname='//input[@id="auReqNameId"]';
    public static $AcademicDescription='//textarea[@placeholder="Request description"]';
    public static $StdInTable='//table[@id="academic-updates-table"]//p[@class="student-name ng-binding"][text()="{{}} <<>>"]';
    public static $RiskBtn='//table[@id="academic-updates-table"]//p[@class="student-name ng-binding"][text()="{{}} <<>>"]//..//following-sibling::td//button[text()="[[]]"]';
    public static $FacultyRiskBtn='//a[contains(text(),"(())")]/../..//..//table[@id="academic-updates-table"]//p[@class="student-name ng-binding"][text()="{{}} <<>>"]//..//following-sibling::td//button[text()="[[]]"]';
    public static $ProgressDropDown='//table[@id="academic-updates-table"]//p[@class="student-name ng-binding"][text()="{{}} <<>>"]//..//following-sibling::td//button[contains(@id,"dropdownMenu-list")]';
    public static $FacultyProgressDropDown='//a[contains(text(),"(())")]/../..//..//table[@id="academic-updates-table"]//p[@class="student-name ng-binding"][text()="{{}} <<>>"]//..//following-sibling::td//button[contains(@id,"dropdownMenu-list")]';
    public static $ProgressGrades='//ul[@class="dropdown-menu mw1-dropdown-menu scrollable"]/li/a[contains(text(),"<<>>")]';
    public static $FacultyProgressGrades='//a[contains(text(),"(())")]/../..//..//ul[@class="dropdown-menu mw1-dropdown-menu scrollable"]/li/a[contains(text(),"<<>>")]';
    public static $Absence='//table[@id="academic-updates-table"]//p[@class="student-name ng-binding"][text()="{{}} <<>>"]//..//following-sibling::td//input[contains(@class,"absences")]';
    public static $FacultyAbsence='//a[contains(text(),"(())")]/../..//..//table[@id="academic-updates-table"]//p[@class="student-name ng-binding"][text()="{{}} <<>>"]//..//following-sibling::td//input[contains(@class,"absences")]';
    public static $Comment='//table[@id="academic-updates-table"]//p[@class="student-name ng-binding"][text()="{{}} <<>>"]//..//following-sibling::td//textarea';
    public static $FacultyComment='//a[contains(text(),"(())")]/../..//..//table[@id="academic-updates-table"]//p[@class="student-name ng-binding"][text()="{{}} <<>>"]//..//following-sibling::td//textarea';
    public static $ReferCheckBox='//table[@id="academic-updates-table"]//p[@class="student-name ng-binding"][text()="{{}} <<>>"]//..//following-sibling::td//label[contains(@for,"student_refer")]/span';
    public static $FacultyReferCheckBox='//a[contains(text(),"(())")]/../..//..//table[@id="academic-updates-table"]//p[@class="student-name ng-binding"][text()="{{}} <<>>"]//..//following-sibling::td//label[contains(@for,"student_refer")]/span';
    public static $SendCheckbox='//table[@id="academic-updates-table"]//p[@class="student-name ng-binding"][contains(text(),"{{}}")]//..//following-sibling::td//label[contains(@for,"student_send")]/span';
    public static $FacultySendCheckbox='//a[contains(text(),"(())")]/../..//..//table[@id="academic-updates-table"]//p[@class="student-name ng-binding"][text()="{{}} <<>>"]//..//following-sibling::td//label[contains(@for,"student_send")]/span';
    public static $SendUpdatesBtn='//button[@ng-click="sendUpdate()"]';
    public static $ReferModalWin='//div[@id="academic-assistance-modal"]';
    public static $AcademicSendText='//div[@id="academic-assistance-modal"]//p[text()="1 Academic updates sent !"]';
    public static $CreateReferral='//div[@id="academic-assistance-modal"]//input[@ng-click="createReferral()"]';
    public static $AcademicUpdates='(//a[contains(text(),"Academic Updates")])[last()]';
    public static $CreateAcademicUpdateButton='//a[@class="create-icon"]';
    public static $Subject='//input[@id="subject"]';
    public static $OptionalMessage='//textarea[@name="add-optional-message"]';
    public static $OpenAcademicUpdate='//table[@ng-table="tableParamsForOpenRequest"]//strong[@title="{{}}"]';
    public static $ClosdAcademicUpdate='//table[contains(@ng-show,"(academicUpdateList.academic_updates_closed")]//strong[@title="{{}}"]';
    public static $SelectAllCourseRadioButton='//label[@for="option101"]';
    public static $SelectCourseRadioButton='//label[@for="option102"]';
    public static $TextFieldForCourse='//input[@name="Search Course by name"]';
    public static $SuggestionBox='//strong[text()="{{}}"]/../../../..//a';
    public static $AddButton='//button[@title="Click Here to Add"]';
    public static $filter='//a[@title="{{}}"]';
    public static $academicDueDate = '//input[@id="due_date"]';
    public static $UpdateTheseStudentLink='//a[@title="Update these students"]';
    public static $ShowLessLink='//a[@aria-controls="somethingToKnowCollapse"]';
    public static $SelectDate = './/*[@id="due_date"]//following-sibling::ul//button[not(contains(@disabled,"disabled"))]/span[text()="{{}}"]';
    public static $AdhocAcdemicIcon='//td[text()="{{}}"]/..//a[@ng-click="createAdhocRequest(course)"]/span';
    public static $SendAcadmicUpdate='//input[@ng-click="send()"]';   
    public static $AcademicUpdateLink='//a[@href="/#/academic-updates"]'; 
    public static $UpdateStduentLink='//Strong[text()="{{}}"]/../../..//td/a[@title="Update these students"]';
    public static $UploadAcademicUpdateButton='//a[@href="/#/academic-updates-setup/upload"]';
   public static $AddingCourseForFirstTime="//nav[contains(text(),'Adding courses to Mapworks the first time')]";
   public static $AcademicUpdateNameOnAUSetUpPage='(//strong[@ng-bind="open_academic_update.request_name"][text()="{{}}"])[1]'; 
   public static $AcademicUpateDesOnAUSetUpPage='(//p[@ng-bind="open_academic_update.request_description"][contains(text(),"{{}}")])[1]';
   public static $AcademicUpdatepercentage='(//strong[@ng-bind="open_academic_update.request_name"][text()="{{}}"])[1]/../../..//span[contains(@ng-bind,"open_academic_update.update_percentage")][contains(text(),"<<>>"]';
   public static $AcademicUpdateOnDashoboard='//div[@name="my-academic-updates"]//strong[@ng-bind="request.request_name"]';
   public static $StduentStatusDropdown='//button[contains(@name,"academic_filter_by_student")]';
   public static $NotSubmittedText='//p[contains(@class,"student-name")][contains(text(),"{{}}")]/../../..//td[contains(text(),"Not submitted")]';
   public static $IncompeleteFromDropDown='//a[@title="Incomplete"]';
   public static $CompleteFromDropDown='//a[@title="Complete"]';
   public static $StudentGradesStatus='//p[contains(@class,"student-name")][contains(text(),"{{}}")]/../../..//td/span[contains(text(),"<<>>")]';
  
   
   
}
