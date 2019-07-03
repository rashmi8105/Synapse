<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class IspBlockDto implements DtoInterface
{

    /**
     * Id
     *
     * @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $id;

    /**
     * Item label
     *
     * @JMS\Type("string")
     */
    private $itemLabel;
    
    /**
     * display Name
     *
     * @JMS\Type("string")
     */
    private $displayName;

    /**
     * Block Selection
     *
     * @JMS\Type("boolean")
     */
    private $blockSelection;

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
     * @param mixed $itemLabel            
     */
    public function setItemLabel($itemLabel)
    {
        $this->itemLabel = $itemLabel;
    }

    /**
     *
     * @return mixed
     */
    public function getItemLabel()
    {
        return $this->itemLabel;
    }
    
    /**
     *
     * @param mixed $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }
    
    /**
     *
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Assign entity properties using an array
     *
     * @param array $attributes assoc array of values to assign
     * @return null
     */
    public function fromArray(array $attributes)
    {
        $this->id = isset($attributes['ispId']) ? (int)$attributes['ispId'] : null;
        $this->itemLabel = isset($attributes['ispLabel']) ? $attributes['ispLabel'] : null;
        $this->displayName = isset($attributes['ispLabel']) ? $attributes['ispLabel'] : null;
        $this->blockSelection = (isset($attributes['ispSelection'])) ? (bool)$attributes['ispSelection'] : null;
    }
}
