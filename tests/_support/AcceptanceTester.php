<?php

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor {

 use _generated\AcceptanceTesterActions;
    
     /**
     * @Then user is able to see :arg1 in the alert
     */
    public function userisableToSeeTheInTheAlert($MessageText) {
        $I = $this;
        $I->SuccessMsgAppears($I);
        $I->SuccessMsgTextVerification($I, $MessageText);
        if (strpos($MessageText, "successfully")!==false || strpos($MessageText, "Successfully") !== false||  strpos($MessageText, "submitted")!==false || strpos($MessageText,"office hours series has finished being created")!==false ) {
            $I->SuccessMsgDisappears($I);
        }
        elseif(strpos($MessageText, "unable")!==false){
            $I->CloseErrorPopUpBox();
            $I->reloadPage();
            $I->WaitForPageToLoad($I);
        }
     
        
    }
     
    
    public function GetEnv() {
        $env = $this->scenario->current("env");
        $CurrentEnv = explode("-", $env);
        return $CurrentEnv[0];
    }
   
    

    public function Element($LocatorName, $pageName) {
        $env = $this->GetEnv();
        $page = "Page\\Acceptance\\$env";
        return eval("return "."$page\\$pageName::\$$LocatorName;");
        
    }

    public function getDataFromJson($class, $key) {
        $Web = new \WebAppTestData();
        $value = $Web->getTestData($class);
        return $value[$key];
    }

    public function writeDataInJson($class, $key, $value) {
        $Web = new \WebAppTestData();
        $Web->writeTestData($class, $key, $value);
    }

    public function WaitForPageToLoad(\AcceptanceTester $I) {
        $I->waitForElementNotVisible(Page\Acceptance\General::$spinner, Page\Acceptance\General::$wait);
        $I->waitForElementNotVisible(Page\Acceptance\General::$PleaseWait, Page\Acceptance\General::$wait);
        $I->wait(3);
    }

    public function WaitForModalWindowToAppear(\AcceptanceTester $I) {
        $I->waitForElementNotVisible(Page\Acceptance\General::$spinner, Page\Acceptance\General::$wait);
        $I->waitForElementVisible(Page\Acceptance\General::$ModalWin, Page\Acceptance\General::$wait);
    }

    public function WaitForModalWindowToDisappear(\AcceptanceTester $I) {
        $I->waitForElementNotVisible(Page\Acceptance\General::$spinner, Page\Acceptance\General::$wait);
        $I->waitForElementNotVisible(Page\Acceptance\General::$ModalWin, Page\Acceptance\General::$wait);
    }

    public function SuccessMsgAppears(\AcceptanceTester $I) {
        $I->waitForElementVisible(Page\Acceptance\General::$successMsg, Page\Acceptance\General::$wait);
    }
    
    public function SuccessMsgTextVerification(\AcceptanceTester $I,$MessageText){
        $I->canSee($MessageText,Page\Acceptance\General::$successMsg);
        
    }

    public function SuccessMsgDisappears(\AcceptanceTester $I) {
        $I->waitForElementNotVisible(Page\Acceptance\General::$successMsg, Page\Acceptance\General::$wait);
        $I->waitForElementNotVisible(Page\Acceptance\General::$spinner, Page\Acceptance\General::$wait);
        $I->wait(3); //latency issue
    }

    public function UploadFiles(\AcceptanceTester $I,$FileName,$Flag=1) {
        $GLOBALS["fileName"] = $FileName;
        if($Flag!=2){ 
           
        if($Flag!=0){
        $I->waitForElementVisible(Page\Acceptance\General::$Upload_Another_File, 60);
        $I->click(Page\Acceptance\General::$Upload_Another_File);
        $I->WaitForPageToLoad($I);
        }
        $I->CollapseSomethingWin($I);
        $I->click(Page\Acceptance\General::$Upload_XLS_CSV);
        $I->WaitForPageToLoad($I);
        }
        $I->executeInSelenium(function (\Facebook\WebDriver\Remote\RemoteWebDriver $webDriver) {
            $webDriver->findElement(\WebDriverBy::xpath('//input[@type="file"]'))->sendKeys(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'uploadFiles' . DIRECTORY_SEPARATOR . $GLOBALS["fileName"]);
        });
        $I->WaitForPageToLoad($I);
        $I->click(Page\Acceptance\General::$Upload_Button);
        $I->WaitForPageToLoad($I);
        $I->waitForElementNotVisible(Page\Acceptance\General::$Upload_Progress_Icon, 480);
        $I->waitForElementVisible(Page\Acceptance\General::$Upload_Success_Icon, 480);
        $I->canSeeElement(Page\Acceptance\General::$Upload_Success_Icon);
    }
public function DirectUploadFileInGroup(\AcceptanceTester $I, $FileName)
{     $GLOBALS["fileName"] = $FileName;
     $I->executeInSelenium(function (\Facebook\WebDriver\Remote\RemoteWebDriver $webDriver) {
            $webDriver->findElement(\WebDriverBy::xpath('//input[@type="file"]'))->sendKeys(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'uploadFiles' . DIRECTORY_SEPARATOR . $GLOBALS["fileName"]);
        });
        $I->WaitForPageToLoad($I);
        $I->click(Page\Acceptance\General::$Upload_Button_In_Group);
        $I->WaitForPageToLoad($I);
        $I->waitForElementNotVisible(Page\Acceptance\General::$Upload_Progress_Icon, 480);
        $I->waitForElementVisible(Page\Acceptance\General::$Upload_Success_Icon, 480);
        $I->canSeeElement(Page\Acceptance\General::$Upload_Success_Icon);
    
}
public function CloseErrorMessagePopUp()
{  $I=$this;
    $I->click(\Page\Acceptance\General::$CloseErrorPopUpIcon);
    $I->wait(2);
}
    
    
    
    public function VerifyUploadDataRows(\AcceptanceTester $I, $TotalRowsUploaded, $RowsAdded, $RowsUpdated, $TotalError) {
        $I->canSeeElement(str_replace("{{}}", $TotalRowsUploaded, Page\Acceptance\General::$TotalRowsUploaded));
        $I->canSeeElement(str_replace("{{}}", $RowsAdded, Page\Acceptance\General::$RowsAdded));
        $I->canSeeElement(str_replace("{{}}", $RowsUpdated, Page\Acceptance\General::$RowsUpdated));
        $I->canSeeElement(str_replace("{{}}", $TotalError, Page\Acceptance\General::$TotalErrors));
    }

    public function CollapseSomethingWin(\AcceptanceTester $I) {
        if ($I->isElementDisplayed($I,Page\Acceptance\General::$ShowLessLink)) {
            $I->click(Page\Acceptance\General::$ShowLessLink);
            $I->waitForElementNotVisible(Page\Acceptance\General::$ShowLessLink, 60);
            $I->wait(5);
        }
    }
    
    public function GetCurrentDate(\AcceptanceTester $I){
      date_default_timezone_set("Asia/Calcutta");
      $Date=date("m/d/Y");
      $datevalue=explode("/", $Date);
      return $datevalue[1];
    }
    
    
    public function clickOnDeleteDialogBox()
{
    $I=$this;
    $I->waitForElement($I->Element("ConfirmDialogBoxbutton","General"));
    $I->click($I->Element("ConfirmDialogBoxbutton","General"));
    
} 
 public function UserclicksOnDeleteButtonDisplayedOnDialogBox(\AcceptanceTester $I) {
        $I->waitForElement(Page\Acceptance\General::$ConfirmDialogBoxbutton, 60);
        $I->click(Page\Acceptance\General::$ConfirmDialogBoxbutton);
    }
    
    
    public function UploadInvalidFile()
    {   $I=$this;
        $GLOBALS["fileName"] = "InvalidType.png";
        $I->waitForElementVisible(Page\Acceptance\General::$Upload_Another_File, 60);
        $I->click(Page\Acceptance\General::$Upload_Another_File);
        $I->WaitForPageToLoad($I);
        $I->CollapseSomethingWin($I);
        $I->click(Page\Acceptance\General::$Upload_XLS_CSV);
        $I->WaitForPageToLoad($I);
        $I->executeInSelenium(function (\Facebook\WebDriver\Remote\RemoteWebDriver $webDriver) {
            $webDriver->findElement(\WebDriverBy::xpath('//input[@type="file"]'))->sendKeys(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'uploadFiles' . DIRECTORY_SEPARATOR . $GLOBALS["fileName"]);
        });
    }
    
    public function CloseErrorPopUpBox()
    {
        $I=$this;
        $I->click(Page\Acceptance\General::$CloseIcononPopUp);
       
        
    }
    
      public function GetFullCurrentDate(\AcceptanceTester $I){
        date_default_timezone_set("Asia/Calcutta");
        $Date = date("m/d/y");
        return $Date;
    }
      public function VerifyURL(\AcceptanceTester $I,$url){
        $I->canSeeInCurrentUrl($url);
        
    }
    
    
    public function isElementDisplayed(AcceptanceTester $I,$ElementSelectorXpath)
     {   
         $Element=$I->executeJS("return document.evaluate(".'"'.$ElementSelectorXpath.'"'.", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;");
          if(count($Element)>=1)
         {
             return true;
                         
         }
         else
         {
             return false;
             
         }
              
     }

     public function LoginAsStudent(\AcceptanceTester $I,$studentEmail="autoqa013@mailinator.com")
    {
             $I->amOnPage("#/login");
//             $I->SendLink($I,$studentEmail);
//             $Email=  explode("@",$studentEmail);
//             $username=$Email[0];
//             $I->LoginFromEmail($I,$username);
    $I=$this; 
    $I->expectTo("Fill Username in field");
    $I->fillField($I->Element("UserName","LoginPage"), $studentEmail);
    $I->expectTo("Fill Password in field");
    $I->fillField($I->Element("Password","LoginPage"), "Qait@123");
    $I->expectTo("Click on signIn button");
    $I->click($I->Element("SignInBtn","LoginPage"));    
    $I->waitForElement('//div[contains(@id,"user-role-selection")]');
    $I->wait(2);
    $I->click("//a[contains(text(),'student')]");
    $I->WaitForPageToLoad($I);
          
    }
     
    public function SendLink(\AcceptanceTester $I,$studentEmail)
    {        $I->fillField("#studentemail",$studentEmail);
             $I->click('//button[@ng-click="submitEmail()"]');
             $I->SuccessMsgAppears($I); 
             $I->SuccessMsgTextVerification($I,"Student login link emailed to you, use that link to login");
             $I->SuccessMsgDisappears($I);
             $I->wait(3); 
             
    } 
    
    
    public function LoginFromEmail(\AcceptanceTester $I,$username)
    {   
        $I->amOnUrl("https://www.mailinator.com/inbox2.jsp?public_to=$username");
        $I->waitForElement('(//div[contains(text(),"Mapworks Login Link")])[1]');
        $I->wait(8);
        $I->click('(//div[contains(text(),"Mapworks Login Link")])[1]');
        $I->wait(2);
     
        $I->executeInSelenium(function(\Facebook\WebDriver\Remote\RemoteWebDriver $webdriver) { 
             $webdriver->switchTo()->frame("publicshowmaildivcontent");
             });
        $I->wait(2);     
        $I->waitForElement('//a[@target="_other"]'); 
        $I->click('//a[@target="_other"]');   
     
        $I->wait(5);         
        $I->executeInSelenium(function(\Facebook\WebDriver\Remote\RemoteWebDriver $webdriver) { 
            $handles = $webdriver->getWindowHandles(); 
            if(count($handles)>1)
            { 
              $webdriver->close(); 
              $last_window=end($handles);
            $webdriver->switchTo()->window($last_window);
            }
            
           });
           $I->maximizeWindow();
           $I->WaitForPageToLoad($I);
          
    }
    
    
    public function ReadFromJson($FileName,$key)
    {
        $JsonValue= json_decode(file_get_contents("tests/_data/$FileName.json",TRUE));
        return $JsonValue->$key;
        
    }    
         public function ClickOnElementWithJS(AcceptanceTester $I,$ElementSelectorXpath)
     {   
        $I->executeJS(" (document.evaluate(".'"'.$ElementSelectorXpath.'"'.", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue).click();");
      
     }

 
}


