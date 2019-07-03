<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class ReportSelectionDto implements DtoInterface
{

    /**
     * Block Id
     *
     * @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $id;

    /**
     * Block Name
     *
     * @JMS\Type("string")
     */
    private $name;
    
    /**
     * Block short_code
     *
     * @JMS\Type("string")
     */
    private $shortCode;

    /**
     * Block Selection
     *
     * @JMS\Type("boolean")
     */
    private $selection;

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $selection
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;
    }

    /**
     * @return mixed
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @param mixed $shortCode
     */
    public function setShortCode($shortCode)
    {
        $this->shortCode = $shortCode;
    }

    /**
     * @return mixed
     */
    public function getShortCode()
    {
        return $this->shortCode;
    }




    /**
     * Assign entity properties using an array
     *
     * @param array $attributes assoc array of values to assign
     * @return null
     */
    public function fromArray(array $attributes)
    {
        $this->id = isset($attributes['id']) ? (int)$attributes['id'] : null;
        $this->name = isset($attributes['name']) ? $attributes['name'] : null;
        $this->selection = (isset($attributes['selection'])) ? (bool)$attributes['selection'] : null;
        $this->shortCode = (isset($attributes['shortCode'])) ? $attributes['shortCode'] : null;
    }
}
