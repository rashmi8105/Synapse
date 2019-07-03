<?php
namespace Step\Acceptance\Report;

class ReportStep extends \AcceptanceTester
{   
    
    public function clickOnReport($ReportName)
    {
        $I=$this;
        $I->click(str_replace("{{}}",$ReportName,$I->Element("ReportLink","ReportPage")));
        $I->WaitForModalWindowToAppear($I);
        
    }

    
    
    
}