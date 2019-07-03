<?php
namespace Page\Acceptance\uat;

class LoginPage
{
    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    
    public static $UserName="#email";
    public static $Password="#pwd";
    public static $SignInBtn='//button[contains(.,"Sign in")]';
    public static $errorMessage='//div[@class="error-message"][contains(.,"We can\'t sign you in, your email address and password do not match.")]';
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
