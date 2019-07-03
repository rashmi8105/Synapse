<?php

namespace Page\Acceptance\sandbox;

class AboutTheStudentPage {

    // include url of current page
    public static $URL = '';
    public static $DashboardBreadcrumb = '//div[@id="dashboardBox"]//p[contains(@ng-show,"studentProfileData")][contains(.,"{{}}")]';
    public static $StudentInfo = '//div[@class="StudentContainer"]//p[contains(.,"")]';
    public static $MobileInfo = '//div[@class="StudentContainer"]//span[contains(.,"")]';
    public static $EmailInfo = '//div[@class="StudentContainer"]//a[contains(.,"")]';
    public static $dashboardContainer = '//div[contains(@ng-controller,"{{}}")]';
        public static $InactiveStatus='//p[contains(@class,"inactive-student-block")]//span[contains(.,"INACTIVE")]';
    //Activity stream locators//
    public static $Tab = '//a[contains(.,"{{}}")]';
    public static $addnewActivity = '//*[contains(@class,"new-activity")][contains(.,"Add New Activity")]';
    public static $WinPanel = '//div[@class="modal-dialog"]//ul//a[contains(.,"{{}}")]';
    public static $clickSeeAll = "(.//*[@id='activitystream']//span[contains(@class,'seeall')])[1]";
    public static $SeeCreatedActivity = './/*[@id="activitystream"]//span[contains(.,"{{}}")]';
//Contact Locators
    public static $ReasonButton = '//label[contains(.,"Reason")]//..//following-sibling::div[contains(@class,"dropdown")]//button[contains(@id,"dropdownMenu")]';
    public static $SelectReason = '//label[contains(.,"Reason")]//..//following-sibling::div//ul[contains(@class,"dropdown-menu")]/li/span[contains(.,"{{}}")]';
    public static $ContactTypeButton = '//label[contains(.,"Contact Type")]//..//following-sibling::div[contains(@class,"dropdown")]//button[contains(@id,"dropdownMenu")]';
    public static $SelectContact = '//label[contains(.,"Contact Type")]//..//following-sibling::div//ul/li/span[contains(.,"{{}}")]';
    public static $dateOfContact = '//label[contains(.,"Date of Contact")]//..//following-sibling::input[@id="startDate"]';
    public static $selectDate = '//label[contains(.,"Date of Contact")]//..//following-sibling::input[@id="startDate"]//..//following-sibling::table//tbody//span[not(contains(@class,"muted"))][contains(.,"{{}}")]';
    public static $Description = '//textarea';
    public static $DetailsCheckbox = '//label[contains(.,"{{}}")]/span';
    public static $SharingOptions = '//input[contains(@name,"sharing")]//..//following-sibling::label[contains(.,"{{}}")]/span';
    public static $TeamName = '//div[contains(@class,"mw-team-popup")]//label[contains(.,"{{}}")]/span';
    public static $CreateContactBtn = '//li[not(contains(@class,"ng-hide"))]//input[@value="Create a Contact"]';
    public static $ActivityDropDown = '//div[@id="activitystream"]//button';
    public static $SelectDropDownValues = '//div[@id="activitystream"]//button[contains(@id,"dropdownMenu")]//..//following-sibling::ul[contains(@class,"dropdown-menu")]//li//span[contains(.,"{{}}")]';
//Note Locators
    public static $CreateNoteBtn = '//li[not(contains(@class,"ng-hide"))]//input[@value="Create a Note"]';
//Referral Locators
    public static $CreateReferralBtn = '//li[not(contains(@class,"ng-hide"))]//input[@value="Create a Referral"]';
    public static $AssignedTo = '//label[contains(.,"Assigned")]//..//following-sibling::div[contains(@class,"dropdown")]//button[contains(@id,"dropdownMenu")]';
    public static $InterestedParty = '//label[contains(.,"Interested")]//..//following-sibling::div[contains(@class,"dropdown")]//button[contains(@id,"dropdownMenu")]';
    public static $SelectAssignTo = '//label[contains(.,"Assigned")]//..//following-sibling::div//ul[contains(@class,"dropdown-menu")]/li/span[contains(.,"{{}}")]';
    public static $SelectInterestedParty = '//label[contains(.,"Interested")]//..//following-sibling::div//ul[contains(@class,"dropdown-menu")]/li//label[contains(.,"{{}}")]/span';
//Appointment Locator
    public static $SelectAppointmentReason='//label[contains(.,"Reason")]//..//following-sibling::div//ul[contains(@class,"dropdown-menu")]/li/a[contains(.,"{{}}")]';
    public static $StartDateBox = ".//*[contains(@id,'StartDate')]";
    public static $EndDateBox = ".//*[contains(@id,'EndDate')]";
    public static $SelectStartDate = '//*[contains(@id,"StartDate")]//..//following-sibling::ul//button[not(contains(@disabled,"disabled"))]/span[contains(.,"{{}}")]';
    public static $SelectEndDate = '//*[contains(@id,"EndDate")]//..//following-sibling::ul//button[not(contains(@disabled,"disabled"))]/span[contains(.,"{{}}")]';
    public static $StartTime = ".//*[contains(@id,'startTime')]";
    public static $EndTime = ".//*[contains(@id,'endTime')]";
    public static $Location = ".//*[contains(@id,'location')]"; 
    
    //details tab locator 
       public static $linksUnderDetailsTab='//div[@class="menu-profile"]//a[contains(text(),"{{}}")]';
       public static $GroupNameOnDeatilsPage='//td[@class="firstcolumn ng-binding"][contains(text(),"{{}}")]';
    //campus conecctions locator
    public static $detailsTabLeftMenuLinks = '//li[contains(@ng-click,"changeTheDetailsContent")]/a[text()="{{}}"]';
    public static $setPrimaryCampusConnectionBtn = '(//a[contains(text(),"Set Primary Campus Connection")])[1]';
    public static $setChangePrimaryCampusConnectionBtn = "//a[contains(text(),'Set/Change Primary Campus Connection')]";    
   public static $SetPrimaryConnection="(//a[contains(text(),'Set Primary Campus Connection')])[1]";
    public static $NameInput = './/*[@id=\'campus-popup\']//div[@class="user-search-box"]/input';
    public static $NameSelect = '//li//a//strong[text()="{{}}"]//ancestor::li';
    public static $saveBtn = '//input[@value="save"]';    
    public static $RemovePrimaryCampusConnection='//a[contains(@class,"mw-close-button")]';
    public static $PrimaryConnnection='.//*[@id="campusconnection"]//a[contains(.,"Set/Change Primary Campus Connection")]//..//ancestor::div[@ng-show="isPrimaryConnectionSet"][contains(.,"{{}}")]';
    public static $PrimaryConnectionEmail='//a[text()="{{}}"]';
   
    public static $PrimaryConnectionName='//a[contains(@title,"Remove Primary Campus Connection")]/..//div[contains(text(),"{{}}")]';
    public static $PrimaryConnectionPhone='//div[@class="moduleTitle ng-binding"][contains(text(),"<<>>")]/../../div//div//span[text()="{{}}"]';
    public static $ProfileTableText='//td[@class="profile_table_text ng-binding"][contains(text(),"<<>>")]/..//td[@class="profile_table_data ng-binding"][contains(text(),"{{}}")]';
    public static $PrimaryConnectionRemoveOption="//div[@class='campusConnection-details ng-isolate-scope']//a[contains(@title,'Remove Primary Campus Connection')]";
    public static $HistoryLink='//td[text()="{{}}"]//..//following-sibling::td//a[text()="History"]';
    public static $Winheading='.//*[@id="student-course-details-view"]//td[text()="{{}}"]';
    public static $ContentOnWindow='//div[@class="modal-dialog"]//td[contains(text(),"{{}}")]';
    
    public static $CheckSignForSend='//td[contains(text(),"{{}}")]/..//td[contains(@ng-if,"send")]/img[@class="checkmark ng-scope"]';
   public static  $CheckSignForRefer='//td[contains(text(),"{{}}")]/..//td[contains(@ng-if,"refer")]/img[@class="checkmark ng-scope"]';
 
   
   
}
