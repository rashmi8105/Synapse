<?php

namespace Page\Acceptance\training;

class ManageStudentPage {

    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    public static $mainStdCount = '//span[contains(@ng-bind,"data.count")]';
    public static $participantCount = '//span[contains(@ng-bind,"students_participants_count")]';
    public static $ActiveParticipantsCount = '//span[contains(@ng-bind,"students_active_participants_count")]';
    public static $AddStd = '//*[@title="Click here to Add Student"]';
    public static $LdapUser = '#ldapuser';
    public static $MarkInActive = '//label[contains(.,"Mark as inactive")]/span';
    public static $NotParticipating = '//label[contains(.,"Not Participating")]/span';
    public static $SearchStd = '#studentName';
    public static $searchBtn = "//a[contains(@title,'Search for')]";
    public static $SearchIcon = '.btn.save.savebtn';
    public static $SaveButton='//input[@title="Save"]';
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
