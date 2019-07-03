<?php

namespace Step\Acceptance;

class PredefinedSearchStep extends \AcceptanceTester {

    public static $element;
    public static $TextName;

    /**
     * @When user clicks on :arg1 on Right Panel under Predefined Search
     */
    public function userClicksOnOnRightPanelUnderPredefinedSearch($LInk) {
        $this->ClickOnRightPanel($LInk);
    }

    /**
     * @Then user is able to view :arg1 link on the page
     */
    public function userIsAbleToViewLinkOnThePage($content) {
        $this->ViewLinksOnPage($content);
    }

    /**
     * @When user clicks on link with Never in last run
     */
    public function userClicksOnLinkWithNeverInLastRun() {
        $this->ClickNever();
    }

    /**
     * @Then user is able to see :arg1 in URL
     */
    public function userIsAbleToSeeInURL($url) {
        $I=$this;
        $this->VerifyURL($I, $url);
    }

    /**
     * @Then user is able to see updated last run column with current date
     */
    public function userIsAbleToSeeUpdatedLastRunColumnWithCurrentDate() {
        $this->SeeUpdatedTestRunDate();
    } 
    
    /**
     * @Then user is able to see Never text in front :arg1 link under Activity panel
     */
     public function userIsAbleToSeeNeverTextInFrontLinkUnderActivityPanel($SeacrhName)
     {
            $this->VerifyNeverInFrontOfSearch($SeacrhName);
     }
  
     /**
     * @Then user clicks on :arg1 search link
     */
     public function userClicksOnSearchLink($SearchName)
     { 
         $this->ClickOnSearch($SearchName);
     }

    /**
     * @Then user is able to see updated last run column with current date for :arg1 search
     */
     public function userIsAbleToSeeUpdatedLastRunColumnWithCurrentDateForSearch($SearchName)
     { 
        $this->SeeUpdatedTestRunDate($SearchName);
     }
     
     
    //////Implementation/////////////////////
     
   public function ClickOnSearch($SearchName)
   {
    $I=$this;
    $I->waitForElement(str_replace("{{}}",$SearchName,$I->Element("LinkText","PredefinedSearchPage")),60);
    $I->click(str_replace("{{}}",$SearchName,$I->Element("LinkText","PredefinedSearchPage")));
     $I->WaitForPageToLoad($I);
     $I->wait(2);
       
   }
   
     

  public function VerifyNeverInFrontOfSearch($SeacrhName)
  {
      $I=$this;
      $I->canSeeElement(str_replace("{{}}",$SeacrhName,$I->Element("NeverInFrontOfSreach","PredefinedSearchPage")));
      
  }
     
     
    public function ClickOnRightPanel($LInk) {
        $I = $this;
        $I->click(str_replace("{{}}", $LInk, $I->Element("RightPanelTab", "PredefinedSearchPage")));
        $I->WaitForPageToLoad($I);
    }

    public function ViewLinksOnPage($content) {
        $I = $this;
        $I->canSeeElement(str_replace("{{}}", $content, $I->Element("LinkText", "PredefinedSearchPage")));
    }

    public function ClickNever() {
        $I = $this;
        $I->executeInSelenium(function (\Facebook\WebDriver\Remote\RemoteWebDriver $webDriver) {
            PredefinedSearchStep::$element = $webDriver->findElements(\WebDriverBy::xpath('//tr[contains(@ng-repeat,"academicPerformance")]'));
        });
        for ($i = 1; $i <= count(PredefinedSearchStep::$element); $i++) {
            $DateValue[$i] = $I->grabTextFrom(str_replace("{{}}", $i, $I->Element("GrabDate", "PredefinedSearchPage")));
            if (strpos($DateValue[$i], "Never") !== false) {
                PredefinedSearchStep::$TextName = $I->grabTextFrom(str_replace("{{}}", $i, $I->Element("grabText", "PredefinedSearchPage")));
                $I->click(str_replace("{{}}", $i, $I->Element("grabText", "PredefinedSearchPage")));
                $I->WaitForPageToLoad($I);
                break;
            }
        }
    }

    public function SeeUpdatedTestRunDate($SearchName) {
        $I = $this; 
        $currentDate = $I->GetFullCurrentDate($I);
        $I->canSeeElement(str_replace("<<>>",$SearchName, str_replace("{{}}", $currentDate, $I->Element("TestRunDate", "PredefinedSearchPage"))));
    }

}
