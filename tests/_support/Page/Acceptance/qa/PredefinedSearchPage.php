<?php
namespace Page\Acceptance\qa;

class PredefinedSearchPage
{
    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    public static $RightPanelTab='//div[@id="predefined-search"]//ul//li//a[contains(.,"{{}}")]';
    public static $LinkText='//table//a[contains(.,"{{}}")]';
    public static $GrabDate='(//tr[contains(@ng-repeat,"academicPerformance")])[{{}}]//a//..//..//following-sibling::td/p[contains(@class,"lr")]';
    public static $grabText='(//tr[contains(@ng-repeat,"academicPerformance")])[{{}}]//a';
    public static $TestRunDate='//tr[contains(@ng-repeat,"academicPerformance")]//a[contains(.,"<<>>")]//..//..//following-sibling::td/p[contains(@class,"lr")][contains(.,"{{}}")]';
   public static $NeverInFrontOfSreach='//a[contains(text(),"{{}}")]/../../..//p[contains(text(),"Never")]';
    
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
