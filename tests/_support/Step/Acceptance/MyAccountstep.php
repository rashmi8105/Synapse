<?php

namespace Step\Acceptance;

class MyAccountstep extends \AcceptanceTester {

    /**
     * @Then user is able to update the :arg1 for my user
     */
    public function userIsAbleToUpdateTheForMyUser($arg1) {
        $this->changePassword($arg1);
    }

///////////////////// Implementations ////////////////////////////

    public function changePassword($value) {
        $I = $this;
        if (strpos($value, "@") !== false) {
            $I->click($I->Element("changePassword", "MyAccountPage"));
            $I->waitForElement($I->Element("newPassword", "MyAccountPage"), 60);
            $I->fillField($I->Element("newPassword", "MyAccountPage"), $value);
            $I->fillField($I->Element("ConfPassword", "MyAccountPage"), $value);
        }
        else{
            $I->fillField($I->Element("phoneField", "MyAccountPage"), $value);
        }
        $I->click($I->Element("Save", "MyAccountPage"));
        $I->SuccessMsgAppears($I);
        $I->SuccessMsgTextVerification($I, "Your account has been updated.");
        $I->SuccessMsgDisappears($I);
        $I->wait(2);
    }

}
