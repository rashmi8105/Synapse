<?php
namespace Synapse\CampusResourceBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 *
 * @package Synapse\CampusResourceBundle\EntityDto
 *         
 */
class CampusAnnouncementDeleteDto
{

    /**
     * id
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $id;

    /**
     * organizationId
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     * personId
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $personId;

    /**
     * status
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $status;

    /**
     * displayType
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $displayType;

    /**
     * notificationId
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $notificationId;

    /**
     *
     * @param mixed $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param mixed $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return mixed
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param mixed $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return mixed
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     *
     * @param mixed $status            
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param string $displayType            
     */
    public function setDisplayType($displayType)
    {
        $this->displayType = $displayType;
    }

    /**
     *
     * @return string
     */
    public function getDisplayType()
    {
        return $this->displayType;
    }

    /**
     *
     * @param mixed $notificationId            
     */
    public function setNotificationId($notificationId)
    {
        $this->notificationId = $notificationId;
    }

    /**
     *
     * @return mixed
     */
    public function getNotificationId()
    {
        return $this->notificationId;
    }
}