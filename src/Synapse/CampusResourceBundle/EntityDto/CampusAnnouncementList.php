<?php
namespace Synapse\CampusResourceBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class CampusAnnouncementList
{

    /**
     * @var int
     */
    private $organizationId;

    /**
     * @var int
     */
    private $personId;

    /**
     *
     * @var SystemMessage[]
     * @JMS\Type("array<Synapse\CampusResourceBundle\EntityDto\SystemMessage>")
     *
     */
    private $systemMessage;

    /**
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     */
    public function setSystemMessage($systemMessage)
    {
        $this->systemMessage = $systemMessage;
    }

    /**
     * @return SystemMessage[]
     */
    public function getSystemMessage()
    {
        return $this->systemMessage;
    }
}

