<?php

namespace Page\Acceptance\production;

class Settings_FeaturesPage {

    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    public static $ExpandIcon = '//div[contains(@class,"featureheading")][contains(.,"{{}}")]//..//..//following-sibling::td//div[@class="Plus"]';
    public static $LabelName = './/*[@id="campusFeatures"]//label[contains(.,"{{}}")]';
    public static $LabelOptionValue = './/*[@id="campusFeatures"]//label[contains(@for,"{{}}")]/span[not(contains(@class,"ng-hide"))]';
   
    public static $SendAcademicUpdateToStudent='//label[@for="academic-updates-{{}}"]';
    
    public static $sendProgressGrade='//label[@for="progressgrades-{{}}"]';
    
    public static $sendAbsence='//label[@for="academicabsences-{{}}"]';
    
    public static $sendComment='//label[@for="academiccomments-{{}}"]';
    
    public static $AcademicRefer='//label[@for="academic-updates-refer-{{}}"]';


    
    
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
