<?php

namespace Step\Acceptance;

class ReportStep extends \AcceptanceTester {

    /**
     * @Then user is able to see all reports
     */
    public function userSeeAllReports() {
        $this->VerifyAllReport();
    }

    /**
     * @Then user is able to see :arg1 Report
     */
    public function userSeeReport($ReportName) {
        $this->VerifyOneReport($ReportName);
    }

    /**
     * @Then user navigates to reports page
     */
    public function userNavigatesToReportsPage() {
        $this->VerfiyUserIsonReportsTab();
    }

    /**
     * @Then user navigates to my activity download page
     */
    public function userNavigatesToMyActivityDownloadPage() {
        $this->VerifyUserIsOnActivityDownloadPage();
    } 
    
    

///////////////////////////////////////////// 

    public function VerifyAllReport() {
        $I = $this;
        $I->canSeeElement(str_replace("{{}}", "All Academic Updates Report", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Completion Report", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Executive Summary Report", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Faculty/Staff Usage Report", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "GPA Report", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Group Response Report", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Individual Response Report", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Our Mapworks Activity", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Our Students Report", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Persistence and Retention Repor", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Profile Snapshot Report", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Survey Factors Report", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Survey Snapshot Report", $I->Element("ReportLink", "ReportPage")));
    }

    public function VerifyOneReport($ReportName) {
        $I = $this;
        $I->canSeeElement(str_replace("{{}}", "Our Mapworks Activity", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Persistence and Retention Repor", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "GPA Report", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Faculty/Staff Usage Report", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Executive Summary Report", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", "Completion Report", $I->Element("ReportLink", "ReportPage")));
        $I->canSeeElement(str_replace("{{}}", $ReportName, $I->Element("ReportLink", "ReportPage")));
    }

    public function VerfiyUserIsonReportsTab() {

        $I = $this;
        $I->canSeeInCurrentUrl("#/report-center");
    }

    public function VerifyUserIsOnActivityDownloadPage() {

        $I = $this;
        $I->canSeeInCurrentUrl("activity");
    }

}
