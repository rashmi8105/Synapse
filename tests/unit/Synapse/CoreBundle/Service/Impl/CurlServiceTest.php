<?php
use Synapse\CoreBundle\Service\Impl\CurlService;


class CurlServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    private $curlUrl = "https://ortc-developers-useast1-s0001.realtime.co/sendbatch";


    public function testSendCurlRequest()
    {
        $this->specify("Testing curl request with realtime service", function ($curlUrl, $curlOptPostFields, $curlOptPost, $message) {
            $curlService = new CurlService();
            $curlResponse = $curlService->sendCurlRequest($curlUrl, $curlOptPostFields, $curlOptPost);
            verify($curlResponse)->equals($message);
        }, ['examples' => [
            // correct data sent, curl response is success
            [$this->curlUrl, "AK=CO6vUS&AT=SOMETOKEN&M=referral_create_creator&C=2-95-C433B7D8-AF03-44A1-8D74-001F0DF0E784&", 4, 'Message "referral_create_creator" sent to channels "2-95-C433B7D8-AF03-44A1-8D74-001F0DF0E784"'],
            // application key is wrong, hence the request is failing
            [$this->curlUrl, "AK=CO6v&AT=SOMETOKEN&M=referral_create_creator&C=2-95-C433B7D8-AF03-44A1-8D74-001F0DF0E784&", 4, 'Batch not sent. Error getting application profile.'],
            // curl url is empty, throws error
            ["", "AK=CO6v&AT=SOMETOKEN&M=referral_create_creator&C=2-95-C433B7D8-AF03-44A1-8D74-001F0DF0E784&", 4, '<url> malformed']


        ]]);
    }

}