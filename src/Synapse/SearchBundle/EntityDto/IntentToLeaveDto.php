<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for IntentToLeave
 *
 * @package Synapse\SearchBundle\EntityDto
 */
class IntentToLeaveDto
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * text
     *
     * @var string @JMS\Type("string")
     */
    private $text;

    /**
     * imageName
     *
     * @var string @JMS\Type("string")
     */
    private $imageName;

    /**
     * colorHex
     *
     * @var string @JMS\Type("string")
     */
    private $colorHex;

    /**
     *
     * @param integer $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param string $text            
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     *
     * @param string $imageName            
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     *
     * @param string $colorHex            
     */
    public function setColorHex($colorHex)
    {
        $this->colorHex = $colorHex;
    }

    /**
     *
     * @return string
     */
    public function getColorHex()
    {
        return $this->colorHex;
    }
}