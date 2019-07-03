<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class BlockDto implements DtoInterface
{

    /**
     * Block Id
     *
     * @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $blockId;

    /**
     * Block Name
     *
     * @JMS\Type("string")
     */
    private $blockName;

    /**
     * Block Selection
     *
     * @JMS\Type("boolean")
     */
    private $blockSelection;

    /**
     * lastUpdated
     *
     * @JMS\Type("DateTime")
     */
    private $lastUpdated;

    /**
     *
     * @param mixed $blockId            
     */
    public function setBlockId($blockId)
    {
        $this->blockId = $blockId;
    }

    /**
     *
     * @return mixed
     */
    public function getBlockId()
    {
        return $this->blockId;
    }

    /**
     *
     * @param mixed $blockSelection            
     */
    public function setBlockSelection($blockSelection)
    {
        $this->blockSelection = $blockSelection;
    }

    /**
     *
     * @return mixed
     */
    public function getBlockSelection()
    {
        return $this->blockSelection;
    }

    /**
     *
     * @param mixed $blockName            
     */
    public function setBlockName($blockName)
    {
        $this->blockName = $blockName;
    }

    /**
     *
     * @return mixed
     */
    public function getBlockName()
    {
        return $this->blockName;
    }

    /**
     *
     * @param mixed $lastUpdated            
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;
    }

    /**
     *
     * @return mixed
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * Assign entity properties using an array
     *
     * @param array $attributes assoc array of values to assign
     * @return null
     */
    public function fromArray(array $attributes)
    {
        $this->blockId = isset($attributes['blockId']) ? (int)$attributes['blockId'] : null;
        $this->blockName = isset($attributes['blockName']) ? $attributes['blockName'] : null;
        $this->blockSelection = (isset($attributes['blockSelection'])) ? (bool)$attributes['blockSelection'] : null;

        if (isset($attributes['blockLastUpdated'])) {
            $this->lastUpdated = new \DateTime($attributes['blockLastUpdated']);
        } else {
            $this->lastUpdated = (!empty($attributes['lastUpdated'])) ? new \DateTime($attributes['lastUpdated']) : null;
        }
    }
}
