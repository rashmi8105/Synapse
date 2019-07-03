<?php
namespace Synapse\CalendarBundle\Service\Impl;

use Synapse\CalendarBundle\Exception\CronofyException;

class CronofyPagedResultIterator
{
    /**
     * @var CronofyCalendarService
     */
    private $cronofy;

    /**
     * @var $itemsKey
     */
    private $itemsKey;

    /**
     * @var $authHeaders
     */
    private $authHeaders;

    /**
     * @var $url
     */
    private $url;

    /**
     * @var $urlParams
     */
    private $urlParams;

    /**
     * CronofyPagedResultIterator constructor.
     *
     * @param CronofyCalendarService $cronofy
     * @param string $itemsKey
     * @param array $authHeaders
     * @param string $url
     * @param string $urlParams
     */
    public function __construct($cronofy, $itemsKey, $authHeaders, $url, $urlParams)
    {
        $this->cronofy = $cronofy;
        $this->itemsKey = $itemsKey;
        $this->authHeaders = $authHeaders;
        $this->url = $url;
        $this->urlParams = $urlParams;
        $this->firstPage = $this->getPage($url, $urlParams);
    }

    /**
     * Get event items by page
     *
     * @return \Generator
     * @throws CronofyException
     */
    public function each()
    {
        $page = $this->firstPage;
        for ($i = 0; $i < count($page[$this->itemsKey]); $i++) {
            yield $page[$this->itemsKey][$i];
        }
        while (isset($page["pages"]["next_page"])) {
            $page = $this->getPage($page["pages"]["next_page"]);
            for ($i = 0; $i < count($page[$this->itemsKey]); $i++) {
                yield $page[$this->itemsKey][$i];
            }
        }
    }

    /**
     * Get Events by page
     *
     * @param string $url
     * @param string $urlParams
     * @return string
     * @throws CronofyException
     */
    private function getPage($url, $urlParams = "")
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url . $urlParams);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->authHeaders);
        curl_setopt($curl, CURLOPT_USERAGENT, CronofyCalendarService::USER_AGENT);
        $result = curl_exec($curl);
        if (curl_errno($curl) > 0) {
            throw new CronofyException(curl_error($curl), 2);
        }
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $this->cronofy->getHandleResponse($result, $status_code);
    }
}