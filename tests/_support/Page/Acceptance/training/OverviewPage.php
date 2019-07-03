<?php
namespace Page\Acceptance\training;

class OverviewPage
{
    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    public static $WidgetsButtons="//h2[contains(.,'{{}}')]//..//..//following-sibling::button";
    public static $AccountDropdown=".//*[contains(@id,'dropdownMenu')]";  
    public static $Account=".//*[contains(@id,'dropdownMenu')]//..//following-sibling::ul//li/a[contains(.,'{{}}')]";
    public static $AdditionalSetup='//div[contains(@class,"module-content")]//a[contains(text(),"{{}}")]';  
    public static $HelpIcon='//a[@href="#/help/"]';
   public static $TabName="//a[contains(.,'{{}}')]"; 
    public static $NumberofNotification="//div[@ng-bind='unviewedCount']";
    public static $NewNumberofNotification='//div[@ng-bind="unviewedCount"][text()="{{}}"]';
    public static $NotificationLink='//a[@class="alert-badge-cursor-hand"]';
    public static $NotificationBoxOnHover='//div[@class="mw-alert-window ng-scope"]';
    public static $NotificationBoxOnClick='//div[@class="notification-alert-click ng-scope"]';
    public static $NotificationInNotificationBox='//span[contains(text(),"<<>>  has assigned you a new referral for {{}}")]/..';
    public static $DescriptionOfActivity='//p[contains(@class,"desc-txt")][text()=""]';
    public static $SubTabName="//a[contains(.,'{{}}')]";
    public static $ActivityComment="//p[contains(text(),'{{}}')]";


}
