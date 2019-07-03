<?php
namespace Page\Acceptance\production;

class PermissionPage
{
    
    public static $addPermissionBtn='//button[contains(@title,"Permission Set")]'; 
    public static $CancelBtn='//input[@value="Cancel"]';
    public static $SaveBtn='//input[@value="Save this Permission Set"]';
    public static $permissionTextbox=".//*[@id='permission_template_name']";
    public static $Count="//div[@class='permission_container']//p[contains(@class,'totalResult')]/span";
    public static $PaginationPage='.//*[@id="mac-pagination-container"]/ul/li[contains(.,"{{}}")]/a';
    public static $editIcon='//span[contains(@class,"permission-temp-name")][contains(.,"{{}}")]//..//following-sibling::td[contains(@class,"menuIcons")]/a';
     public static $permissionParameter='//div[@class="mw-modal-vnav"]//li//a[contains(text(),"{{}}")]';
   public static $permissionCheckBox='//p[contains(text(),"{{}}")]';
   public static $featureLinkOnPermissionWindow="//a[contains(text(),'{{}}')]";
   public static $featurePermission='//h4[text()="<<>>"]/..//label[text()="{{}}"]'; 
    public static $DirectReferral='(//p[contains(.,"Direct Referrals")]//..//following-sibling::div//p[contains(.,"<<>>")]//following-sibling::ul//label[contains(.,"{{}}")])[1]';
    public static $ReferralReasonRouting='//p[contains(.,"Reason Routed Referrals")]//..//following-sibling::div//p[contains(.,"<<>>")]//following-sibling::ul//label[contains(.,"{{}}")]';
    public static $isSelectAllOptionSelected="//p[contains(text(),'Select all')]/../../input[@aria-checked='true']";


}
