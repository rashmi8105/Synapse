<?php
use Synapse\CoreBundle\Util\Helper;
require_once 'tests/api/SynapseTestHelper.php';

class TalkingPointCest extends SynapseTestHelper
{

    private $token;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testGetTalkingPoints(ApiTester $I)
    {
        $I->wantTo('Get Talking Points');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('talkingpoints');
        $talkingPoints = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'question_profile_item' => 1,
            'kind' => 'Survey',
            'weakness_min' => "1",
            'weakness_max' => "4",
            'strength_min' => "5",
            'strength_max' => "8"
        ));
        $I->seeResponseCodeIs(200);
    }
}