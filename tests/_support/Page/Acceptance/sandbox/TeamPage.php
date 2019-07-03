<?php
namespace Page\Acceptance\sandbox;

class TeamPage
{
    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    public static $AddTeamButton = '.btn.buttonLargeSecondary.setup_large_btn';
    public static $TeamName = '//input[@name="labelname"]';
    public static $SearchFacultyField = ".//*[@id='userSearchInput']";
    public static $selectStaff = './/*[@role="option"][contains(.,"{{}}")]';
    public static $addBtn = '//button[contains(.,"add")]';
    public static $FacultyNameDisplayed = '//table//tr[contains(@ng-repeat,"manageteam")]/td[contains(.,"{{}}")]';
    public static $RoleDropDownBtn = '//table//tr[contains(@ng-repeat,"manageteam")]/td[contains(.,"{{}}")]//..//following-sibling::td//div[contains(@ng-include,"teamDropdown")]//button';
    public static $TeaMDropDwnPanel = '//table//tr[contains(@ng-repeat,"manageteam")]/td[contains(.,"{{}}")]//..//following-sibling::td//div[contains(@ng-include,"teamDropdown")]//ul';
    public static $selectTeamRole = '//table//tr[contains(@ng-repeat,"manageteam")]/td[contains(.,"<<>>")]//..//following-sibling::td//ul//li[contains(.,"{{}}")]';
    public static $deleteFacultyIcon = '//table//tr[contains(@ng-repeat,"manageteam")]/td[contains(.,"{{}}")]//..//following-sibling::td[@class="menuIcons"]/a';
    public static $cancelTeambtn = '.btn.nevermind';
    public static $SaveTeamBTn = '.btn.save';
    public static $Count = '//p[contains(@class,"totalResult")]/span';
    public static $PaginationBar = './/*[@id="mac-pagination-container"]/ul/li[contains(.,"{{}}")]/a';
    public static $MessageText = '//table//tr//td[contains(@class,"bold-text")][contains(.,"<<>>")]/span[contains(.,"{{}}")]';
    public static $EditTeamIcon = '//table//tr[contains(@ng-repeat,"manageteam")]/td[contains(.,"{{}}")]//..//following-sibling::td[contains(@class,"menuIcons")]/a[contains(@title,"Edit")]';
    public static $RemoveTeamIcon = '//table//tr[contains(@ng-repeat,"manageteam")]/td[contains(.,"{{}}")]//..//following-sibling::td[contains(@class,"menuIcons")]/a[contains(@title,"Remove")]';
    public static $RemoveStaffBtn = '//div[@class="modal-content"]//button[contains(.,"Remove Staff")]';
    public static $DeleteTeamBTn = '//div[@class="modal-dialog"]//button[contains(.,"Delete")]';
     public static $TeamWidget="//div[@name='my-team-activities']";
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
