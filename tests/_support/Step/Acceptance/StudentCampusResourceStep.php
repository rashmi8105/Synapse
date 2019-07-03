<?php

namespace Step\Acceptance;

class StudentCampusResourceStep extends \AcceptanceTester {

    /**
     * @Then student is able to see campus resource details
     */
    public function userSeeCampusResourceDetails() {
        $this->verfiyCampusResourcesDetails();
    }

    /**
     * @When Student clicks on website link on student page
     */
    public function studentClicksOnWebsiteLinkOnStudentPage() {
        $this->ClickonCampusResourceLink();
    }

    /**
     * @Then Student is able to navigate to Campus Resources website
     */
    public function studentIsAbleToNavigateToCampusResourcesWebsite() {
        $this->verfiyLinkOnCampusResource();
    }

    /**
     * @Then student is not able see campus resource details
     */
    public function userDoesNotSeeCampusResourceDetails() {
        $this->VerifyAbsenseofCampusResource();
    }

/////////////////////////////////////////////////////////////////////////////////////////

    public function VerifyAbsenseofCampusResource() {
        $I = $this;
        $I->cantSeeElement(str_replace("{{}}", $I->getDataFromJson(new CampusResoucesStep($I->getScenario()), "CampusResourceName"), $I->Element("campusOnStd", "StudentCampusResourcePage")));
        $I->cantSee($I->getDataFromJson(new CampusResoucesStep($I->getScenario()), "phoneNumberField"));
        $I->cantSee($I->getDataFromJson(new CampusResoucesStep($I->getScenario()), "locationName"));
        $I->cantSee($I->getDataFromJson(new CampusResoucesStep($I->getScenario()), "descriptionField"));
    }

    public function verfiyCampusResourcesDetails() {
        $I = $this;
        $I->canSeeElement(str_replace("{{}}", $I->getDataFromJson(new CampusResoucesStep($I->getScenario()), "CampusResourceName"), $I->Element("campusOnStd", "StudentCampusResourcePage")));
        $I->canSee($I->getDataFromJson(new CampusResoucesStep($I->getScenario()), "staffNameField"));
        $I->canSee($I->getDataFromJson(new CampusResoucesStep($I->getScenario()), "phoneNumberField"));
        $I->canSee($I->getDataFromJson(new CampusResoucesStep($I->getScenario()), "locationName"));
        $I->canSee($I->getDataFromJson(new CampusResoucesStep($I->getScenario()), "descriptionField"));
    }

    public function ClickonCampusResourceLink() {
        $I = $this;
        $I->click(str_replace("{{}}", $I->getDataFromJson(new CampusResoucesStep($I->getScenario()), "CampusResourceName"), $I->Element("VisitLink", "StudentCampusResourcePage")));
        $I->wait(3);
    }

    public function verfiyLinkOnCampusResource() {
        $I = $this;
        $I->executeInSelenium(function (\Facebook\WebDriver\Remote\RemoteWebDriver $driver) {
            $handles = $driver->getWindowHandles();
            $last_window = end($handles);
            $driver->close();
            $driver->switchTo()->window($last_window);
            $URL = $driver->getCurrentUrl();
            if ($URL == "http://www.synapse.com/") {
                $GLOBALS["check"] = "TRUE";
            } else {
                $GLOBALS["check"] = "false";
            }
        });

        $I->assertEquals($GLOBALS["check"], "TRUE");
    }

}
