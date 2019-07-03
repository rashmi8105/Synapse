<?php


namespace Step\Acceptance;

class StudentReferralsStep extends \AcceptanceTester {
  /**
     * @Then student is able to see referrals with details :arg1, :arg2 and :arg3
     */
     public function studentSeeReferralsWithDetailsAnd($Reason_type,$Assign_To,$CreatedBy)
     {
         $this->verifyPresenceOfReffarlOnstudentPage($Reason_type,$Assign_To,$CreatedBy);
     }

    /**
     * @Then student is not able see referrals
     */
     public function studentShouldSeeReferralsWithDetailsAnd()
     {
                  $this->verifyAbsenceOfReffarlOnstudentPage();
     }

///////////////////////////////////////////////////////
     
     public function verifyPresenceOfReffarlOnstudentPage($Reason_type,$Assign_To,$CreatedBy)
     {  $I=$this;
        date_default_timezone_set("Asia/Calcutta");
        $array=explode("-", date('d-M-Y-l'));
        $comment=$I->getDataFromJson(new AboutTheStudentStep($I->getScenario()),"ReferralCommentDesc");
        $I->canSeeElement(str_replace("{{}}",$comment, $I->Element("Description","StudentReferralPage")));
        $I->canSeeElement(str_replace("{{}}", ltrim($array[0],0), $I->Element("ReferralDate","StudentReferralPage")));
        $I->canSeeElement(str_replace("{{}}", $array[3], $I->Element("referralDay","StudentReferralPage")));
        $I->canSeeElement(str_replace("{{}}", $array[1], $I->Element("ReferralMonth","StudentReferralPage")));
        $I->canSeeElement(str_replace("{{}}", $array[2],$I->Element("ReferralYr","StudentReferralPage")));
        $I->canSeeElement(str_replace("{{}}",$Reason_type,$I->Element("why","StudentReferralPage")));
        $I->canSeeElement(str_replace("{{}}",$Assign_To,$I->Element("AssignedTo","StudentReferralPage")));        
       $I->canSeeElement(str_replace("{{}}",$CreatedBy,$I->Element("CreatedBy","StudentReferralPage")));        

        
         
         
     }
public function  verifyAbsenceOfReffarlOnstudentPage()
{
    $I=$this;
        $comment=$I->getDataFromJson(new AboutTheStudentStep($I->getScenario()),"ReferralCommentDesc");
        $I->cantSeeElement(str_replace("{{}}",$comment, $I->Element("Description","StudentReferralPage")));
        
}
     
     
}
