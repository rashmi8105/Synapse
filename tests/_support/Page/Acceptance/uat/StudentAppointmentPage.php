<?php
namespace Page\Acceptance\uat;

class StudentAppointmentPage
{
    public static $Who='//div[contains(.,"Where:")]//following-sibling::p[contains(text(),"{{}}")]//..//ancestor::div[1]//strong[contains(.,"<<>>")]';
    public static $whoTitle='//div[@class="who_name"]/span[contains(.,"{{}}")]';
    public static $cancelAppointment="//p[contains(.,'{{}}')]//..//ancestor::div[2]//button[contains(@class,'cancel_stud vma_cancel_appointment_desk')]";
    public static $Modalwin='//div[@class="modal-content"]';
    public static $CancelBtn='(//button[@ng-click="cancelAppointment()"])[last()]';
    public static $Std_ScheduleAnAppointment="//button[contains(@class,'book_appointment_desk')]";
    public static $PersonName="//a[contains(.,'{{}}')]";
    public static $WinHeader='//*[contains(@class,"ast_desk_title")]//strong[contains(.,"{{}}")]';
    public static $ActionLink='//div[@class="ast_table_list"]//tbody//td/p[contains(.,"{{}}")]//..//following-sibling::td/a[contains(.,"schedule")]';
    public static $ReasonDropDown=".//*[contains(@id,'dropdownMenu-list')]";
    public static $SelectReason='//a[contains(.,"{{}}")]';
    public static $FinalScheduleBtn='(//button[@ng-click="scheduleAppointmentWithFaculty()"])[last()]';
    public static $successMsg='//table[@class="message-modal-window-message-table"]//div|//div[@class="message-modal-window-message"]/label';
    public static $DateInHeader="//div[contains(@class,'vma_time')]/span[contains(text(),'{{}}')]";
    public static $OfficeHoursOnAvailbilityWindow='//p[contains(text(),"{{}}")]/../../..//a'; 
    public static $ScheduleButton='//button[contains(@class,"book_appointment")][1]';
    public static $SelectAWindow='(//div[contains(text(),"Select a Person")])[last()]';
    public static $ScheduleButtonOnModalWindow='(//button[@ng-enter="scheduleAppointmentWithFaculty()"])[1]'; 
    public static $DescriptionTextField='//textarea[@placeholder="Description (Optional)"]';
    
}
