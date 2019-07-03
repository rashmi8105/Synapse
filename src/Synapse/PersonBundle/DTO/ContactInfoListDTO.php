<?php
namespace Synapse\PersonBundle\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 *  ContactInfoList DTO
 */
class ContactInfoListDTO
{
    /**
     * Array of ContactInfo
     *
     * @var ContactInfoDTO[]
     * @JMS\Type("array<Synapse\PersonBundle\DTO\ContactInfoDTO>")
     *
     */
    private $contactInformation;

    /**
     * @return ContactInfoDTO[]
     */
    public function getContactInformation()
    {
        return $this->contactInformation;
    }

    /**
     * @param ContactInfoDTO[] $contactInformation
     */
    public function setContactInformation($contactInformation)
    {
        $this->contactInformation = $contactInformation;
    }
}
