<?php
namespace Synapse\SearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * IntentToLeave
 *
 * @ORM\Table(name="intent_to_leave")
 * @ORM\Entity(repositoryClass="Synapse\SearchBundle\Repository\IntentToLeaveRepository")
 */
class IntentToLeave extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *      @JMS\Expose
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="text", type="string", length=10, nullable=true)
     *      @JMS\Expose
     */
    private $text;

    /**
     *
     * @var string @ORM\Column(name="image_name", type="string", length=200, nullable=true)
     *      @JMS\Expose
     */
    private $imageName;

    /**
     *
     * @var string @ORM\Column(name="color_hex", type="string", length=7, nullable=true)
     *      @JMS\Expose
     */
    private $colorHex;
    
    /**
     * @var string
     *
     * @ORM\Column(name="min_value", type="decimal", precision=8, scale=4, nullable=true, unique=false)
     */
    private $minValue;
    
    /**
     * @var string
     *
     * @ORM\Column(name="max_value", type="decimal", precision=8, scale=4, nullable=true, unique=false)
     */
    private $maxValue;

    /**
     *
     * @param string $text            
     */
    public function setText($text)
    {
        $this->text = $text;
        
        return $this;
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
        
        return $this;
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
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
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
     * @param string $colorHex            
     */
    public function setColorHex($colorHex)
    {
        $this->colorHex = $colorHex;
        
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getColorHex()
    {
        return $this->colorHex;
    }
    
    /**
     * Set minValue
     *
     * @param string $minValue
     * @return IntentToLeave
     */
    public function setMinValue($minValue)
    {
    	$this->minValue = $minValue;
    
    	return $this;
    }
    
    /**
     * Get minValue
     *
     * @return string
     */
    public function getMinValue()
    {
    	return $this->minValue;
    }
    
    /**
     * Set maxValue
     *
     * @param string $maxValue
     * @return IntentToLeave
     */
    public function setMaxValue($maxValue)
    {
    	$this->maxValue = $maxValue;
    
    	return $this;
    }
    
    /**
     * Get maxValue
     *
     * @return string
     */
    public function getMaxValue()
    {
    	return $this->maxValue;
    }
}