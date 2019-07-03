<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 *
 *
 * @package Synapse\RestBundle\Entity
 */
class SystemAlertDto
{

    /**
     * Id of the alert.
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * User's organization id.
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * Id of the person that is receiving the alert.
     *
     * @var integer @JMS\Type("integer")
     */
    private $personId;

    /**
     * Message describing the intent of the alert. Cannot be blank.
     *
     * @var string
     * @JMS\Type("string")
     * @Assert\NotBlank()
     */
    private $message;

    /**
     * Date that the alert is applied on.
     *
     * @var \Datetime
     * @JMS\Type("DateTime<'Y-m-d h:i A'>")
     */
    private $startDateTime;

    /**
     * Date that the alert ends on.
     *
     * @var \Datetime
     * @JMS\Type("DateTime<'Y-m-d h:i A'>")
     */
    private $endDateTime;

    /**
     * If True, then the alert is enabled(active).
     *
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $isEnabled;

    /**
     *
     * @return \Datetime
     */
    public function getEndDateTime()
    {
        return $this->endDateTime;
    }

    /**
     *
     * @param \Datetime $endDateTime
     */
    public function setEndDateTime($endDateTime)
    {
        $this->endDateTime = $endDateTime;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return boolean
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     *
     * @param boolean $isEnabled
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    /**
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     *
     * @param int $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return \Datetime
     */
    public function getStartDateTime()
    {
        return $this->startDateTime;
    }

    /**
     *
     * @param \Datetime $startDateTime
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;
    }
}