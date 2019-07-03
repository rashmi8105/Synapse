<?php
namespace Page\Acceptance\production;

class GroupPage
{
   public static $AddAnotherGroupButton='//button[contains(@title,"Add Another Group")]';
   public static $GroupName='#groupName';
   public static $GroupID='.//input[@id="externalId"]';
   public static $Cancel='//button[text()="Cancel"]';
   public static $paginationBar="//ul[contains(@class,'pagination')]//li[contains(@ng-repeat,'pageNumber')]";
   public static $SaveGroupButton='.save'; 
   public static $Page ='//span[@ng-bind="page.number"][text()="{{}}"]|//a[contains(@ng-click,"setCurrent")][text()="{{}}"]';
   public static $Staff_Field = './/*[@id=\'group-page-form\']//div[@class="user-search-box"]/input';
   public static $DropDownValue=".//*[contains(@id,'typeahead')]//div[contains(@title,'{{}}')][1]";
   public static $GroupLink='//span[@class="group-name"]/a[contains(@id,"{{}}")]';
   public static  $FacultyonGroupPage=' //td[contains(text(),"{{}}")]';
   public static $AddStaffButton = '//button[@ng-click="addStaff()"]';
   public static $PermissionDropDown='//td[contains(text(),"{{}}")]/..//td[@id="groupDropdown"]';
   public static $SelectPermission='//td[contains(text(),"{{}}")]/..//*[@id="groupDropdown"]//li[contains(@title,"<<>>")]'; 
   public static $AddSubgroup='.//button[contains(text(),"Add Subgroup")]';
   public static $GroupExpandIcon = '//a[contains(.,"{{}}")]/ancestor::tr//div[@ng-click="displaySubgroup( group)"]'; 
   public static $subgroupOnGroupSummaryPage='//span[@class="sub-group-name"]/a[text()="{{}}"]';
   public static $subgroupIDonGroupSummaryPage='//span[@class="sub-group-name"]/..//i[contains(text(),"{{}}")]';
   public static $removeStaffWithName='//td[contains(text(),"{{}}")]/..//td//img[@class="removeImg"]';
   public static $GroupIdInList='//a[text()="{{}}"]/../..//i[contains(.,"<<>>")]';
     public static $Error='//span[text()="{{}}"]';
     public static $DeleteGroup='//button[@title="Click Here to Delete This Group"]';

     /////////////////////////////////////
     
public static $UploadLink='//button[contains(@ng-click,"upload-{{}}")]'; 

public static $StudentUploadButtonInsideGroup='//button[contains(@ng-click,"studentUpload()")]';
public static $UploadWindow='//div[@class="modal-content"]';
public static $NumberOfStudentInGroup='//label[contains(@ng-if,"data.groups.students_count")][contains(text(),"{{}}")]';
public static $StudentTextWithZeroText='//label[contains(@ng-if,"data.groups.students_count")][text()="Student"]';
public static $NumberOfStudentsInGroupHeader='//span[contains(@ng-if,"data.groups.students_count")]/b[contains(text(),"{{}}")][1]'; 
     
public static $GroupLinkToNavigateToGroupSummaryPage='//a[contains(@href,"/#/groupsummary")]';     
public static $InvalidFielTypeError="//div[@class='mw-errormessage ng-binding'][text()='Please enter a valid file type.']";     

public static $VisibilityCheckBox="//label[contains(@for,'checkbox')]//..//following-sibling::input[@aria-checked='true']";
public static $FacultyVisiblity='//td[contains(text(),"{{}}")]/..//label';
public static $UploadStudentButton='//button[contains(@ng-click,"studentUpload()")]';     
     
     
} 
