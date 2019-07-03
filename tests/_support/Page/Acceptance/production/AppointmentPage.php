<?php
namespace Page\Acceptance\production;

class AppointmentPage
{
    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
public static $Links = '//a[contains(.,"{{}}")]';
    public static $ReasonDropDown = '//div[contains(@id,"reasonDropDownId")]//button';
    public static $ReasonDropDownValues = '//div[contains(@id,"reasonDropDownId")]//button//..//following-sibling::ul//li/a[contains(.,"{{}}")]';
    public static $StartDateBox = ".//*[contains(@id,'StartDate')]";
    public static $EndDateBox = ".//*[contains(@id,'EndDate')]";
    public static $SelectStartDate = '//*[contains(@id,"StartDate")]//..//following-sibling::ul//button[not(contains(@disabled,"disabled"))]/span[contains(.,"{{}}")]';
    public static $SelectEndDate = '//*[contains(@id,"EndDate")]//..//following-sibling::ul//button[not(contains(@disabled,"disabled"))]/span[contains(.,"{{}}")]';
    public static $StartTime = ".//*[contains(@id,'startTime')]";
    public static $EndTime = ".//*[contains(@id,'endTime')]";
    public static $Location = ".//*[contains(@id,'location')]";
    public static $SharingOption = '//label[contains(@for,"{{}}")]/span[contains(@class,"button")]';
    public static $SearchAttendes = ".//*[contains(@id,'searchAttendees')]";
    public static $selectAttendees = '//div[@class="modal-dialog"]//div[contains(@title,\'{{}}\')]';
    public static $addAttendees = '//div[@class="modal-dialog"]//button[contains(text(),\'add\')]';
    public static $CancelBtn = '//div[@class="modal-dialog"]//button[contains(.,"Cancel")]|//div[@class="modal-dialog"]//input[@class="nevermind"]';
    public static $BookAppointmentBtn = '//button[contains(@class,"save-template") and not(contains(@class,"hide"))]|//li[not(contains(@class,"ng-hide"))]//input[@value="Book Appointment"]';
    public static $BookAppointmentOnDialog = '//div[contains(@class,"confirmation-dialog-content")]//button[contains(.,"Book Appointment")]|//div[contains(@class,"confirmation-dialog-content")]//button[contains(.,"Modify Appointment")]';
    public static $AppointmentLocation = '//td[not(contains(@class,"hide"))][contains(.,"{{}}")]';
    public static $RemoveStdIcon = '//li[contains(@ng-repeat,"book_appointment.attendees")]//a[contains(.,"{{}}")]//..//following-sibling::a/img[@class="removeImg"]';
    public static $MenuArrow = '//td[not(contains(@class,"hide"))][contains(.,"{{}}")]//..//following-sibling::td[contains(@class,"menu")]';
    public static $Edit = './/*[@id="dropdown"][@class="ng-isolate-scope"]/li[contains(.,"Edit Appointment")]';
    public static $CancelRemove = './/*[@id="dropdown"][@class="ng-isolate-scope"]/li[contains(.,"Cancel and Remove")]';
    public static $OfficeStrtTime = './/*[@id="addOfficeHour"]//input[@name="strtime"]';
    public static $OfficeEndTime = './/*[@id="addOfficeHour"]//input[@name="endtime"]';
    public static $SlotButton = '//div[@title="Select Appointment Slot from Dropdown"]//button';
    public static $SelectSlots = '//div[@title="Select Appointment Slot from Dropdown"]//ul//li//a[contains(.,"{{}}")]';
    public static $OfficeHrDate = './/input[@name="strdate"]';
    public static $SelectOfficeHrsDate = '//*[contains(@id,"office_hour_one_time_date")]//..//following-sibling::ul//button[not(contains(@disabled,"disabled"))]/span[contains(.,"{{}}")]';
    public static $AddOfficeHrsBtn = '//div[@class="modal-dialog"]//input[@class="save-template"]';
    public static $OfficeHrsLocation = './/div[@id="addOfficeHour"]//input[@id="location"]';
    public static $AddOfficeHoursBtn = '//input[@class="save-template"]';
    public static $ConfirmBtn = '//button[contains(.,"Confirm")]';
    public static $BookIcon = '//td[not(contains(@class,"hide"))][contains(.,"{{}}")]//..//following-sibling::td//a[contains(.,"Book")]';
    public static $CancelIcon = '//td[not(contains(@class,"hide"))][contains(.,"{{}}")]//..//following-sibling::td//a[contains(.,"Cancel")]';
    public static $SeriesRadio = '//label[@for="series"]/span';
    public static $RepeatButton = '//div[contains(@class,"repeatPatternDropdown")]//button';
    public static $RepeatOptions = '//div[contains(@class,"repeatPatternDropdown")]//ul//li//a[contains(.,"{{}}")]';
    public static $RepeatDaysCount = '//div[contains(@class,"repeatpattern")]//input[@id="count"]';
    public static $includeSatSunday = '//div[contains(@class,"repeatpattern")]//label[@for="IncludeSatSunday"]/span';
    public static $SeriesStartDate = '#start_date_id';
    public static $SelectSeriesStartDate = '//input[@id="start_date_id"]//..//following-sibling::ul//button[not(contains(@disabled,"disabled"))]/span[contains(.,"{{}}")]';
    public static $NoEndDateRadioBtn = '//label[@for="noend"]/span';
    public static $SeriesEndByRadioBTn = '//label[@for="endDate"]/span';
    public static $SeriesEndDate = './/*[@name="endate"]';
    public static $SelectSeriesEndDate = './/*[@name="endate"]//..//following-sibling::ul//button[not(contains(@disabled,"disabled"))]/span[contains(.,"{{}}")]';
    public static $SeriesEndAfterRadioBtn = '//label[@for="end"]/span';
    public static $EndAfterOccurenceInput = '//label[@for="end"]//..//following-sibling::label//input[@name="occurrences"]';
    public static $RemoveOfficeHr='.//*[@id="dropdown"][@class="ng-isolate-scope"]/li[contains(.,"Remove Office Hour")]';
    public static $ManageThisSlot='.//*[@id="dropdown"][@class="ng-isolate-scope"]/li[contains(.,"Manage this slot")]';
    public static $ManageThisSeries='.//*[@id="dropdown"][@class="ng-isolate-scope"]/li[contains(.,"Manage this series")]';
    public static $RemoveThisOffcHrBtn='//button[contains(.,"Remove this Office Hour")]';
    public static $facultySearchBox='//span[@class="auto-search-faculty"]//input[contains(@class,"user-search-input")]';
    public static $delegateSaveChanges='.btn.save-template.ng-binding';
    public static $CoordinatorEmail='//*[@class="staffname"][contains(.,"{{}}")]//..//td/label/span';
    public static $OpenAllSelectedSchedules = '//div[@class="open-schedule"]//a';
    public static $AgendaMessage='//h2[@class="proxyview" and contains(text(),"You are viewing")]/b[contains(text(),"{{}}")]';
   public static $DeleteDelegateAccessDeleteIcon="//p[contains(text(),'{{}}')]/../..//img";
    
}
