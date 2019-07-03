<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Campus
 *
 * @package Synapse\MultiCampusBundle\EntityDto
 */
class CampusDto
{

    /**
     * Id of an organization's campus. Unique to mapworks as a whole.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $id;

    /**
     * Id of the organization that a campus belongs to.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * Name of a campus.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $campusName;

    /**
     * Campus' source type.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $sourceCampusType;

    /**
     * Language id. Always 1(English).
     * @var integer
     * @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $langid;

    /**
     * Campus nick name.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $campusNickName;

    /**
     * Subdomain of a campus.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $subdomain;

    /**
     * Id of a campus. Unique to each organization.
     *
     * @var string
     * @JMS\Type("string")
     *      @Assert\Length(min = 1,
     *      max = 12,
     *      minMessage = "Campus Id must be at least {{ limit }} characters long",
     *      maxMessage = "Campus Id cannot be longer than {{ limit }} characters long"
     *      )
     */     
    private $campusId;

    /**
     * Status of a campus.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $status;

    /**
     * Timezone of a campus.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $timezone;

    /**
     * Number of users within a campus.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $countUsers;

    /**
     * Id of a campus' primary tier.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $primaryTierId;

    /**
     * Name of a campus' primary tier.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $primaryTierName;

    /**
     * Id of a campus' secondary tier.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $secondaryTierId;

    /**
     * Name of a campus' secondary tier.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $secondaryTierName;

    /**
     * Type of campus.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $type;

    /**
     * Object representing the coordinators within a campus.
     *
     * @var Object
     * @JMS\Type("array<Synapse\RestBundle\Entity\CoordinatorDTO>")
     */
    private $coordinators;

    /**
     * Id of a campus' organization.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $sourceOrgId;

    /**
     * Array representing the features of an organization.
     *
     * @JMS\Type("array")
     * @var array
     */
    private $orgFeatures;

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
     * @return string
     */
    public function getCampusName()
    {
        return $this->campusName;
    }

    /**
     *
     * @param string $campusName
     */
    public function setCampusName($campusName)
    {
        $this->campusName = $campusName;
    }

    /**
     *
     * @return string
     */
    public function getCampusNickName()
    {
        return $this->campusNickName;
    }

    /**
     *
     * @param string $campusNickName
     */
    public function setCampusNickName($campusNickName)
    {
        $this->campusNickName = $campusNickName;
    }

    /**
     *
     * @return string
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     *
     * @param string $subdomain
     */
    public function setSubdomain($subdomain)
    {
        $this->subdomain = $subdomain;
    }

    /**
     *
     * @return string
     */
    public function getCampusId()
    {
        return $this->campusId;
    }

    /**
     *
     * @param string $campusId
     */
    public function setCampusId($campusId)
    {
        $this->campusId = $campusId;
    }

    /**
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     *
     * @param string $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     *
     * @return int
     */
    public function getLangid()
    {
        return $this->langid;
    }

    /**
     *
     * @param int $langid
     */
    public function setLangid($langid)
    {
        $this->langid = $langid;
    }

    /**
     *
     * @return int
     */
    public function getCountUsers()
    {
        return $this->countUsers;
    }

    /**
     *
     * @param int $countUsers
     */
    public function setCountUsers($countUsers)
    {
        $this->countUsers = $countUsers;
    }

    /**
     *
     * @return int
     */
    public function getPrimaryTierId()
    {
        return $this->primaryTierId;
    }

    /**
     *
     * @param int $primaryTierId
     */
    public function setPrimaryTierId($primaryTierId)
    {
        $this->primaryTierId = $primaryTierId;
    }

    /**
     *
     * @return int
     */
    public function getSecondaryTierId()
    {
        return $this->secondaryTierId;
    }

    /**
     *
     * @param int $secondaryTierId
     */
    public function setSecondaryTierId($secondaryTierId)
    {
        $this->secondaryTierId = $secondaryTierId;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return string
     */
    public function getCoordinators()
    {
        return $this->coordinators;
    }

    /**
     *
     * @param string $coordinators
     */
    public function setCoordinators($coordinators)
    {
        $this->coordinators = $coordinators;
    }

    /**
     *
     * @return string
     */
    public function getPrimaryTierName()
    {
        return $this->primaryTierName;
    }

    /**
     *
     * @param string $primaryTierName
     */
    public function setPrimaryTierName($primaryTierName)
    {
        $this->primaryTierName = $primaryTierName;
    }

    /**
     *
     * @return string
     */
    public function getSecondaryTierName()
    {
        return $this->secondaryTierName;
    }

    /**
     *
     * @param string $secondaryTierName
     */
    public function setSecondaryTierName($secondaryTierName)
    {
        $this->secondaryTierName = $secondaryTierName;
    }

    /**
     *
     * @return string
     */
    public function getSourceCampusType()
    {
        return $this->sourceCampusType;
    }

    /**
     *
     * @param string $sourceCampusType
     */
    public function setSourceCampusType($sourceCampusType)
    {
        $this->sourceCampusType = $sourceCampusType;
    }

    /**
     *
     * @return int
     */
    public function getSourceOrgId()
    {
        return $this->sourceOrgId;
    }

    /**
     *
     * @param int $sourceOrgId
     */
    public function setSourceOrgId($sourceOrgId)
    {
        $this->sourceOrgId = $sourceOrgId;
    }

    /**
     *
     * @return array
     */
    public function getOrgFeatures()
    {
        return $this->orgFeatures;
    }

    /**
     *
     * @param array $orgFeatures
     */
    public function setOrgFeatures($orgFeatures)
    {
        $this->orgFeatures = $orgFeatures;
    }
}