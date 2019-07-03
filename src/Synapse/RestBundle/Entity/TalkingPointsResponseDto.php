<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for talking points
 *
 * @package Synapse\RestBundle\Entity
 */
class TalkingPointsResponseDto
{

    /**
     * talkingPointId
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $id;
    
    /**
     * talkingPointId
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $questionProfileItem;

    /**
     * @JMS\Type("string")
     * 
     * @var string
     */
    
    private $kind;
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $questionText;

    /**
     * @JMS\Type("string")
     * 
     * @var string
     */
    private $weaknessText;
    
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $strengthText;
    
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $weaknessMin;
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $weaknessMax;
    
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $strengthMin;
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $strengthMax;
        
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function setQuestionProfileItem($questionProfileItem){
        $this->questionProfileItem = $questionProfileItem;
    }
    
    public function setKind($kind){
        $this->kind = $kind;
    }
    
    public function setWeaknessText($weaknessText){
    
        $this->weaknessText = $weaknessText;
    }
    
    public function setStrengthText($strengthText){
        
        $this->strengthText = $strengthText;
    }
    
    public function setStrengthMin($strengthMin){
    
        $this->strengthMin = $strengthMin;
    }
    
    public function setStrengthMax($strengthMax){
    
        $this->strengthMax = $strengthMax;
    }
    
    
    public function setWeaknessMin($weaknessMin){
        $this->weaknessMin = $weaknessMin;
    }
    
    public function setWeaknessMax($weaknessMax){
        $this->weaknessMax = $weaknessMax;
    }
    
    public function setQuestionText($questionText){
        $this->questionText = $questionText;
    }
    
    public function setUpdatedDate($updatedDate){
        $this->updatedDate = $updatedDate;
    }
   
}