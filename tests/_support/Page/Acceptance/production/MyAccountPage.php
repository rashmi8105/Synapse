<?php
namespace Page\Acceptance\production;

class MyAccountPage
{
    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    public static $Save='//button[contains(.,"save")]';
    public static $changePassword='//a[contains(.,"Change password")]';
    public static $newPassword=".//*[@id='pass']";
    public static $ConfPassword=".//*[@id='confpass']";
     public static $phoneField=".//*[@id='phone']";
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
