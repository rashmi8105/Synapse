<?php
namespace Page\Acceptance\sandbox;

class SystemAlertPage
{
    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    public static $createNewMessageBtn = '//button[contains(text(),"Create new message")]';
    public static $messageTextField=".//*[@id='messagetext']";
    public static $MessageType='//label[contains(.,"{{}}")]/span';
    public static $Stop_DateTimeButton='//button[@name="timeOut"]';
    public static $Stop_DateTimeDropDown='//ul[@role="menu"]//li//a[contains(.,"{{}}")]';
    public static $SaveBtn='//input[@ng-click="createSystemMessage()"]';
    public static $systemalert_inlist='//table//tr[@ng-repeat="systemmessage in $data"]//p[contains(.,"{{}}")]';
    public static $editIcon='//table//tr[@ng-repeat="systemmessage in $data"]//p[contains(.,"{{}}")]//..//..//following-sibling::td//a[@class="edit_Img"]';
    public static $deleteIcon='//table//tr[@ng-repeat="systemmessage in $data"]//p[contains(.,"{{}}")]//..//..//following-sibling::td//a[@class="delete_Img"]';
    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

    /**
     * @var \AcceptanceTester;
     */
    protected $acceptanceTester;

    public function __construct(\AcceptanceTester $I)
    {
        $this->acceptanceTester = $I;
    }

}
