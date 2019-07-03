<?php

namespace Page\Acceptance\qa;

class InstituteSettingsPage {

    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    public static $ExpandIcon = '//div[contains(@class,"featureheading")][contains(.,"{{}}")]//..//..//following-sibling::td//div[@class="Plus"]';
    public static $CollapseIcon = '//div[contains(@class,"featureheading")][contains(.,"{{}}")]//..//..//following-sibling::td//div[@class="Minus"]';
    public static $saveBtn = '#btnSave';
    public static $CancelBtn = '.btn.nevermind.institute-settings-nevermind';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param) {
        return static::$URL . $param;
    }

    /**
     * @var \AcceptanceTester;
     */
    protected $acceptanceTester;

    public function __construct(\AcceptanceTester $I) {
        $this->acceptanceTester = $I;
    }

}
