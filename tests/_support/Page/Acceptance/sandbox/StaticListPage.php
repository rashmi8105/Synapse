<?php

namespace Page\Acceptance\sandbox;

class StaticListPage {

    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    public static $CreateStaticListBtn = '//input[@value="Create New List"]';
    public static $ListName = ".//*[@id='listName']";
    public static $DescriptionName = ".//*[@id='listDes']";
    public static $CreateListBtn = '//input[@value="Create List"]';
    public static $EditList = '//input[@value="save"]';
    public static $StaticListInList = './/*[@id="static-list"]//a[contains(@ng-enter,"getStaticListDetails")][contains(.,"{{}}")]';
    public static $editicon = './/*[@id="static-list"]//a[contains(@ng-enter,"getStaticListDetails")][contains(.,"{{}}")]//..//..//following-sibling::td[@class="icons"]/a[@class="edit"]';
    public static $Deleteicon = './/*[@id="static-list"]//a[contains(@ng-enter,"getStaticListDetails")][contains(.,"{{}}")]//..//..//following-sibling::td[@class="icons"]/a[@class="remove"]';
    public static $ShareIcon = './/*[@id="static-list"]//a[contains(@ng-enter,"getStaticListDetails")][contains(.,"{{}}")]//..//..//following-sibling::td[@class="icons"]/a[@class="share"]';
    public static $DeleteListBtn = '//button[contains(.,"Delete List")]';
    public static $whoItisFor = '//div[@data-selected-user="sharePersonName"]//input';
    public static $SelectSearchUser = '//div[@class="user-search-box"]//div[contains(@title,\'{{}}\')]';
    public static $shareList = '//input[@value="Share this List"]';
    public static $searchStdToAdd = "//div[@data-title=\"'Add a student to the Static List'\"]//input";
    public static $SelectStd = '//div[@class="search-text-overflow"]//strong[contains(.,"{{}}")]';
    public static $AddToList = '//*[@ng-enter="addStudentToList();"]';
    public static $StdInList = '//table[@id="student-list-data"]//tbody//tr//td//a[contains(@id,"student")][contains(.,"{{}}")]';
    public static $StaticListLink = '//a[@title="Navigate to Manage Static Lists"]';
    public static $StdCountInList='//*[@id="static-list"]//a[contains(@ng-enter,"getStaticListDetails")][contains(.,"{{}}")]//..//..//following-sibling::td//p[(contains(@class,"students"))][contains(.,"<<>>")]';

    /**

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
