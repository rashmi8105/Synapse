<?php
namespace Page\Acceptance\uat;

class CampusResourcePage
{
     public static $buttonToAddStaff = '//button[@title="Click here to Add Staff"]';
    public static  $AddAnotherButton='//button[@title="Click here to Add Campus Resource"]';
    public static $Savebutton='//button[@class="btn save"]'; 
    public static $staffNameField = '//input[@placeholder="staff name or email"]';
    public static $phoneNumberField = '//input[@title="Enter Phone Number"]';
    public static $emailAddressField = '//input[@title="Enter Email Address"]';
    public static $locationName = '//input[@title="Enter Location"]';
    public static $urlField = '//input[@title="Enter URL"]';
    public static $descriptionField = '//textarea';
    public static $checkBoxForCampusResourceViewedByStudents = '//div[@class="upper-checkbox"]/label[@class="label-description"][1]/span';
    public static $checkBoxForCampusResourceReceiveReferrals = '//*[text()="Campus resource can receive referrals"]';
    public static $staffNameInTable='//span[contains(@title,"{{}}")]';
    public static $CampusResourceName='//input[@title="Enter Resource Name"]'; 
    public static $StaffOption='//a[contains(@title,"{{}}")]';
    public static $editIcon = '//div[@id="campusResource"]//span[text()="{{}}"]//..//following-sibling::td[contains(@class,"menuIcons")]//img[@class="editImg"]';
    public static $DeleteIcon = '//div[@id="campusResource"]//span[text()="{{}}"]//..//following-sibling::td[contains(@class,"menuIcons")]//img[@class="removeImg"]';
    public static $DelteFaculty='//img[@title="Remove This Staff"]';

}
