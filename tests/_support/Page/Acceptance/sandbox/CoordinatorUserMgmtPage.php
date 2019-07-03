<?php
namespace Page\Acceptance\sandbox;

class CoordinatorUserMgmtPage
{
    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    
    public static $addNewCoordinatorBTn='//input[@value="Add New Coordinator"]';
    public static $ExistingUserLink='//a[contains(.,"Or choose an existing user")]';
    public static $CoordinatorCount='//div[@class="primary-tier-admin"]//span[contains(@class,"admin")]';
    public static $FirstName="#firstName";
    public static $LastName="#lastName";
    public static $Title="#title";
    public static $contactInfo="#email";
    public static $phone='#phone';
    public static $MobileCheckBox='.mobile>span';
    public static $Id='#id';
    public static $TypeDropdown='//div[contains(@class,"coordinator-content-bar-dropdown")]/button';
    public static $SelectType='//div[contains(@class,"coordinator-content-bar-dropdown open")]//ul//li/span[contains(.,"{{}}")]';
    public static $CancelBtn='.secondary-button.nevermind';
    public static $SaveBtn='//input[@value="Save"]';
    public static $coordinatorDetails = "//table//tbody//p[contains(text(),'{{}}')]";
    public static $CoordinatorType='//table//tr//p[contains(.,"<<>>")]//..//following-sibling::td/p[contains(@class,"type")][contains(.,"{{}}")]';
    public static $EditIcon='//table//tr//p[contains(.,"{{}}")]//..//following-sibling::td[contains(@class,"icons")]/a[@class="edit"]';
    public static $SendInvite='//table//tr//p[contains(.,"{{}}")]//..//following-sibling::td[contains(@class,"icons")]/a[contains(@class,"inv")]';
    public static $DeleteIcon='//table//tr//p[contains(.,"{{}}")]//..//following-sibling::td[contains(@class,"icons")]/a[contains(@class,"remove")]';
    public static $ProxyIcon='//table//tr//p[contains(.,"{{}}")]//..//following-sibling::td[contains(@class,"icons")]/a[contains(@class,"proxy")]';
    public static $RemoveBtn='//button[@ng-click="confirm()"]';  
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
