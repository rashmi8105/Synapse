<?php
namespace Step\Acceptance;

class NotificationStep extends \AcceptanceTester
{  
   /**
 * @When user grabs the value of number of notification
 */
 public function userGrabsTheValueOfNumberOfNotification()
 {
 $this->GrabNotficationValue();
 }

/**
 * @Then user should see notifcation with notification count
 */
 public function userShouldSeeNotifcationWithNotificationCount()
 { 
     $this->userSeeNotification();
 } 

/**
 * @When user hover over notification
 */
 public function userHoverOverNotification()
 { 
     
     $this->HoverOverNotification();
 }

/**
 * @Then user see notfication window
 */
 public function userSeeNotficationWindow()
 { 
     $this->seeNotificationWindow();
 }

/**
 * @When user clicks on notfication
 */
 public function userClicksOnNotfication()
 { 
     $this->ClickOnNotification();
 }

/**
 * @When user clicks on :arg1 created by :arg2 to :arg3
 */
 public function userClicksOnCreatedByTo($Activity,$Faculty,$Student)
 { 
     $this->userclicksOnnotification($Activity,$Faculty,$Student);
 }


///////////////////////////////////////////////////////
 
 public function seeNotificationWindow()
 {
     $I=$this;
     $I->waitForElement($I->Element("NotificationBoxOnHover","OverviewPage"),60);
     $I->canSeeElement($I->Element("NotificationBoxOnHover","OverviewPage"));
 }
 
 public function ClickOnNotification()
 {
     $I=$this;
     $I->waitForElement($I->Element("NotificationLink","OverviewPage"),60);
     $I->click($I->Element("NotificationLink","OverviewPage"));
     
 }
 
 public function GrabNotficationValue()
 {
     $I=$this;
     if($I->isElementDisplayed($I,$I->Element("NumberofNotification", "OverviewPage")))
     {  
         $number=(int)$I->grabTextFrom($I->Element("NumberofNotification", "OverviewPage"));
         $I->writeDataInJson($this,"NotificationCount",$number+1);
     }
     else
     {   
       $I->writeDataInJson($this,"NotificationCount","1");
     }
  
 }
 public function userSeeNotification()
 {
      $I=$this;
     $I->canSeeElement(str_replace("{{}}",$I->getDataFromJson($this,"NotificationCount"), $I->Element("NewNumberofNotification","OverviewPage")));
        $notificationColor = $I->executeInSelenium(function(\Facebook\WebDriver\Remote\RemoteWebDriver $webdriver) {
            return $webdriver->findElement(\WebDriverBy::xpath('//div[@class="badgeNumber ng-binding ng-scope"]'))->getCSSValue('background-color');
        });
                 
     $I->assertEquals($notificationColor,"rgba(51, 51, 51, 1)");
  
 }
 
 public function HoverOverNotification()
 {
     $I=$this;
     $I->moveMouseOver($I->Element("NotificationLink","OverviewPage"));
     
    
 }
 
 public function userclicksOnnotification($Activity,$Faculty,$Student)
 {   $I=$this;
     if(strcasecmp($Activity,'referral')==0)
     {   $I->waitForElement(str_replace("{{}}",$Student,str_replace("<<>>",$Faculty,$I->Element("NotificationInNotificationBox","OverviewPage"))),60);
         $I->canSeeElement(str_replace("{{}}",$Student,str_replace("<<>>",$Faculty,$I->Element("NotificationInNotificationBox","OverviewPage"))));
         $I->click(str_replace("{{}}",$Student,str_replace("<<>>",$Faculty,$I->Element("NotificationInNotificationBox","OverviewPage"))));
         $I->WaitForModalWindowToAppear($I);
         
     }

 
     
     
     }
 
 
 }