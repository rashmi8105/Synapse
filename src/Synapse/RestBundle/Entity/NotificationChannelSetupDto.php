<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class NotificationChannelSetupDto
{
    /**
     * push notification channel API key.
     *
     * @var string @JMS\Type("string")
     */
    private $apiKey;

    /**
     * push notification channel Auth domain.
     *
     * @var string @JMS\Type("string")
     */
    private $authDomain;

    /**
     * push notification channel Database URL.
     *
     * @var string @JMS\Type("string")
     */
    private $databaseUrl;

    /**
     * push notification channel Project Id.
     *
     * @var string @JMS\Type("string")
     */
    private $projectId;

    /**
     * push notification channel Storage bucket.
     *
     * @var string @JMS\Type("string")
     */
    private $storageBucket;

    /**
     * push notification channel sender id.
     *
     * @var string @JMS\Type("string")
     */
    private $messagingSenderId;

    /**
     * push notification channel application key.
     *
     * @var string @JMS\Type("string")
     */
    private $applicationKey;

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getAuthDomain()
    {
        return $this->authDomain;
    }

    /**
     * @param string $authDomain
     */
    public function setAuthDomain($authDomain)
    {
        $this->authDomain = $authDomain;
    }

    /**
     * @return string
     */
    public function getDatabaseUrl()
    {
        return $this->databaseUrl;
    }

    /**
     * @param string $databaseUrl
     */
    public function setDatabaseUrl($databaseUrl)
    {
        $this->databaseUrl = $databaseUrl;
    }

    /**
     * @return string
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param string $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * @return string
     */
    public function getStorageBucket()
    {
        return $this->storageBucket;
    }

    /**
     * @param string $storageBucket
     */
    public function setStorageBucket($storageBucket)
    {
        $this->storageBucket = $storageBucket;
    }

    /**
     * @return string
     */
    public function getMessagingSenderId()
    {
        return $this->messagingSenderId;
    }

    /**
     * @param string $messagingSenderId
     */
    public function setMessagingSenderId($messagingSenderId)
    {
        $this->messagingSenderId = $messagingSenderId;
    }

    /**
     * @return string
     */
    public function getApplicationKey()
    {
        return $this->applicationKey;
    }

    /**
     * @param string $applicationKey
     */
    public function setApplicationKey($applicationKey)
    {
        $this->applicationKey = $applicationKey;
    }
}