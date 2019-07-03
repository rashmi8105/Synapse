<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
/**
 * Data Transfer Object
 *
 * @package Synapse\RestBundle\Entity
 */
class SourceOrgDto
{

    /**
     * type
     * 
     * @var string @JMS\Type("string")
     */
    private $type;

  /**
     * primaryTierId
     * 
     * @var integer @JMS\Type("integer")
     */
    private $primaryTierId;

    /**
     * primaryTierName
     * 
     * @var string @JMS\Type("string")
     */
    private $primaryTierName;
	 /**
     * secondaryTierName
     * 
     * @var integer @JMS\Type("integer")
     */
	private $secondaryTierId;
	 /**
     * secondaryTierName
     * 
     * @var string @JMS\Type("string")
     */
	private $secondaryTierName;
	
	
	/**
     * campusId
     * 
     * @var integer @JMS\Type("integer")
     */
    private $campusId;

    /**
     * campusName
     * 
     * @var string @JMS\Type("string")
     */
    private $campusName;

   
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
    public function getType()
    {
        return $this->type;
    }
	
	 /**
     *
     * @param string $primaryTierId            
     */
    public function setPrimaryTierId($primaryTierId)
    {
        $this->primaryTierId = $primaryTierId;
    }

    /**
     *
     * @return string
     */
    public function getPrimaryTierId()
    {
        return $this->primaryTierId;
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
     * @param string $secondaryTierId            
     */
    public function setSecondaryTierId($secondaryTierId)
    {
        $this->secondaryTierId = $secondaryTierId;
    }

    /**
     *
     * @return string
     */
    public function getSecondaryTierId()
    {
        return $this->secondaryTierId;
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
    public function getCampusId()
    {
        return $this->campusId;
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
    public function getCampusName()
    {
        return $this->campusName;
    }
} 