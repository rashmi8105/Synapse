<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("curl_service")
 */
class CurlService {

    const SERVICE_KEY = 'curl_service';


    /**
     * Method used for sending out curl requests
     *
     * @param string $curlUrl -  curl url
     * @param array $curlOptPostFields -  data to be posted in curl
     * @param integer $curlOptPost -  count of the posted fields
     * @return string
     */
    public function sendCurlRequest($curlUrl, $curlOptPostFields  = null , $curlOptPost = null )
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $curlUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if(!is_null($curlOptPostFields)){
            if(!is_null($curlOptPost)){
                curl_setopt($curl, CURLOPT_POST, $curlOptPost);
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $curlOptPostFields);
        }
        $curlResponse = curl_exec($curl);
        if (curl_errno($curl)) {
            $curlResponse = curl_error($curl);
        }
        curl_close($curl);
        return $curlResponse;
    }
}