<?php
namespace Page\Acceptance\stage;

class ActivityDownloadPage
{
    // include url of current page
    public static $URL = '';

    public static $FeatureDesc="//*[contains(@class,'activity-desc')][contains(.,'{{}}')]";    
    public static $ViewLink="//*[contains(@class,'activity-desc')][contains(.,'{{}}')]//..//..//following-sibling::td//a[contains(.,'View')]";
    public static $DescOnWin='//div[@class="modal-dialog"]//p[contains(.,"{{}}")]';
        public static $ClosePopUpWindow='//a[@title="Close"]';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

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
