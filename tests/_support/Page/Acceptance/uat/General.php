<?php
namespace Page\Acceptance\uat;

class General
{
    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    public static $wait=240;
    public static $spinner='#loading-bar-spinner';
    public static $successMsg = '//table[@class="message-modal-window-message-table"]//div|//div[@class="message-modal-window-message"]/label';
    public static $ModalWin = '//div[@class="modal-content"]';
    public static $PleaseWait='//div[contains(@cg-busy,"Please Wait...")]/div[contains(@class,"cg-busy-animation")]';
    
    
    //Upload LOcators
    public static $Upload_Another_File = './/*[@id="mw-upload-details"]/a[@ng-click="nextStep()"]';
    public static $Upload_XLS_CSV = './/*[@id="mw-uploadStart"]//a[@ng-click="lookupFiles()"]';
    public static $Upload_Button = '//div[@class="modal-dialog"]//button[@ng-click="confirmUpload()"]';
    public static $Upload_Progress_Icon = ".//*[@id='mw-upload-progress']";
    public static $Upload_Success_Icon = '//div[contains(@class,"adding-stu-cont")]//p/img[@title="Process Confirmation"]';
    
    //Upload Feature Locators
    public static $TotalRowsUploaded='//table/tbody/tr/td[1][contains(.,"{{}}")]';
    public static $RowsAdded='//table/tbody/tr/td[2][contains(.,"{{}}")]';
    public static $RowsUpdated='//table/tbody/tr/td[3][contains(.,"{{}}")]';
    public static $TotalErrors='//table/tbody/tr/td[4][contains(.,"{{}}")]';
    
    //showless panel locator
    public static $ShowLessLink='//div[@class="something-to-know"]//span[contains(.,"Show less")]';
   // locator for delete confirm button
 public static $ConfirmDialogBoxbutton='//button[@ng-click="confirm()"]';

    
    
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
