<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Tier
 *
 * @package Synapse\MultiCampusBundle\EntityDto
 */
class TierDto
{

    /**
     * Id of a tier.
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * Id of a person within a tier.
     *
     * @var integer @JMS\Type("integer")
     */
    private $personId;

    /**
     * Id of a tier that is unique to mapworks.
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $externalId;

    /**
     * Level of a tier. (Primary, secondary).
     *
     * @var string @JMS\Type("string")
     */
    private $tierLevel;

    /**
     * Name of a primary tier.
     *
     * @var string @JMS\Type("string")
     *      @Assert\Length(min = 1,
     *      max = 140,
     *      minMessage = "Name must be at least {{ limit }} characters long",
     *      maxMessage = "Name cannot be longer than {{ limit }} characters long"
     *      )
     *     
     */
    private $primaryTierName;

    /**
     * Id of a secondary tier.
     *
     * @var string @JMS\Type("string")
     *      @Assert\Length(min = 1,
     *      max = 10,
     *      minMessage = "Secondary Tier Id must be at least {{ limit }} characters long",
     *      maxMessage = "Secondary Tier Id cannot be longer than {{ limit }} characters long"
     *      )
     *     
     */
    private $secondaryTierId;

    /**
     * Name of a secondary tier.
     *
     * @var string @JMS\Type("string")
     *      @Assert\Length(min = 1,
     *      max = 140,
     *      minMessage = "Name must be at least {{ limit }} characters long",
     *      maxMessage = "Name cannot be longer than {{ limit }} characters long"
     *      )
     *     
     */
    private $secondaryTierName;

    /**
     * Id of a primary tier.
     *
     * @var string @JMS\Type("string")
     *      @Assert\Length(min = 1,
     *      max = 10,
     *      minMessage = "Primary Tier Id must be at least {{ limit }} characters long",
     *      maxMessage = "Primary Tier Id cannot be longer than {{ limit }} characters long"
     *      )
     *     
     */
    private $primaryTierId;

    /**
     * Description of a tier.
     *
     * @var string @JMS\Type("string") *
     *      @Assert\Length(min = 1,
     *      max = 500,
     *      minMessage = "Description must be at least {{ limit }} characters long",
     *      maxMessage = "Description cannot be longer than {{ limit }} characters long"
     *      )
     *     
     */
    private $description;

    /**
     * Id of the campus that a tier belongs to.
     *
     * @var integer @JMS\Type("integer")
     */
    private $campusId;

    /**
     * Language id. Always 1(English).
     *
     * @var integer
     * @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $langid;

    /**
     * Total number of secondary tiers within an organization.
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $totalSecondaryTiers;

    /**
     * Total number of campus' within a tier.
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $totalCampus;

    /**
     * Total number of users between tiers.
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalUsers;

    /**
     * Total number of users within a primary tier.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $totalPrimaryTierUsers;

    /**
     * Total number of users within a secondary tier.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $totalSecondaryTierUsers;

    /**
     * Total number of coordinators.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $totalCoordinators;

    /**
     * Object representing a campus' hierarchy.
     *
     * @var Object
     * @JMS\Type("Synapse\MultiCampusBundle\EntityDto\PrimaryTierDto")
     */
    private $hierarchyCampuses;

    /**
     * Object representing the solo campuses within mapworks.
     *
     * @var Object
     * @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\campusDto>")
     */
    private $soloCampuses;

    /**
     * Object representing the users within a tier.
     *
     * @var Object
     * @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\UsersDto>")
     */
    private $users;

    /**
     * Object representing a campus.
     *
     * @var Object
     * @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\CampusDto>")
     */
    private $campus;

    /**
     * Object representing campuses within a tier.
     *
     * @var Object
     * @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\CampusDto>")
     */
    private $campuses;

    /**
     * Tiles.
     *
     * @var Object
     * @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\TilesDto>")
     */
    private $tiles;

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
    public function getId()
    {
        return $this->id;
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
    public function getPrimaryTierName()
    {
        return $this->primaryTierName;
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
    public function getPrimaryTierId()
    {
        return $this->primaryTierId;
    }

    /**
     *
     * @param string $description            
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     *
     * @param string $tierLevel            
     */
    public function setTierLevel($tierLevel)
    {
        $this->tierLevel = $tierLevel;
    }

    /**
     *
     * @return string
     */
    public function getTierLevel()
    {
        return $this->tierLevel;
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
     * @return int
     */
    public function getSecondaryTierId()
    {
        return $this->secondaryTierId;
    }

    /**
     *
     * @param int $totalCoordinators            
     */
    public function setTotalCoordinators($totalCoordinators)
    {
        $this->totalCoordinators = $totalCoordinators;
    }

    /**
     *
     * @return int
     */
    public function getTotalCoordinators()
    {
        return $this->totalCoordinators;
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
    public function getSecondaryTierName()
    {
        return $this->secondaryTierName;
    }

    /**
     *
     * @param string $users            
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     *
     * @return string
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     *
     * @param string $totalPrimaryTierUsers            
     */
    public function setTotalPrimaryTierUsers($totalPrimaryTierUsers)
    {
        $this->totalPrimaryTierUsers = $totalPrimaryTierUsers;
    }

    /**
     *
     * @return string
     */
    public function getTotalPrimaryTierUsers()
    {
        return $this->totalPrimaryTierUsers;
    }

    /**
     *
     * @param string $totalSecondaryTierUsers            
     */
    public function setTotalSecondaryTierUsers($totalSecondaryTierUsers)
    {
        $this->totalSecondaryTierUsers = $totalSecondaryTierUsers;
    }

    /**
     *
     * @return string
     */
    public function getTotalSecondaryTierUsers()
    {
        return $this->totalSecondaryTierUsers;
    }

    /**
     *
     * @param string $totalSecondaryTiers            
     */
    public function setTotalSecondaryTiers($totalSecondaryTiers)
    {
        $this->totalSecondaryTiers = $totalSecondaryTiers;
    }

    /**
     *
     * @return string
     */
    public function getTotalSecondaryTiers()
    {
        return $this->totalSecondaryTiers;
    }

    /**
     *
     * @param integer $totalCampus            
     */
    public function setTotalCampus($totalCampus)
    {
        $this->totalCampus = $totalCampus;
    }

    /**
     *
     * @return integer
     */
    public function getTotalCampus()
    {
        return $this->totalCampus;
    }

    /**
     *
     * @param integer $totalUsers            
     */
    public function setTotalUsers($totalUsers)
    {
        $this->totalUsers = $totalUsers;
    }

    /**
     *
     * @return integer
     */
    public function getTotalUsers()
    {
        return $this->totalUsers;
    }

    /**
     *
     * @param Object $hierarchyCampuses            
     */
    public function setHierarchyCampuses($hierarchyCampuses)
    {
        $this->hierarchyCampuses = $hierarchyCampuses;
    }

    /**
     *
     * @return Object
     */
    public function getHierarchyCampuses()
    {
        return $this->hierarchyCampuses;
    }

    /**
     *
     * @param Object $soloCampuses            
     */
    public function setSoloCampuses($soloCampuses)
    {
        $this->soloCampuses = $soloCampuses;
    }

    /**
     *
     * @return Object
     */
    public function getSoloCampuses()
    {
        return $this->soloCampuses;
    }

    /**
     *
     * @param string $campus            
     */
    public function setCampus($campus)
    {
        $this->campus = $campus;
    }

    /**
     *
     * @return string
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     *
     * @param string $campuses            
     */
    public function setCampuses($campuses)
    {
        $this->campuses = $campuses;
    }

    /**
     *
     * @return string
     */
    public function getCampuses()
    {
        return $this->campuses;
    }

    /**
     *
     * @param integer $campusId            
     */
    public function setCampusId($campusId)
    {
        $this->campusId = $campusId;
    }

    /**
     *
     * @return string
     */
    public function getCampusId()
    {
        return $this->campusId;
    }

    public function setTiles($tiles)
    {
        $this->tiles = $tiles;
    }

    public function getTiles()
    {
        return $this->tiles;
    }

    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    public function getPersonId()
    {
        return $this->personId;
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
    public function getLangid()
    {
        return $this->langid;
    }

    /**
     *
     * @param string $externalId            
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     *
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }
}
