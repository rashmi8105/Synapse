<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * 
 * @package Synapse\SurveyBundle\EntityDto
 */
class IssueCreateFactorDto
{
    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $id;
    
    /**
     * $rangeMin
     *
     * @var integer @JMS\Type("double")
     *  @Assert\Range(
     *      min = -999999999999,
     *      max = 999999999999,
     *      minMessage = "min value not less than {{ limit }} to enter",
     *      maxMessage = "min value not grater than {{ limit }} to enter"
     * )
     */
    private $rangeMin;
    
    /**
     * $rangeMax
     *
     * @var integer @JMS\Type("double")
     *
     *
     *  @Assert\Range(
     *      min = -999999999999,
     *      max = 999999999999,
     *      minMessage = "max value not less than {{ limit }} to enter",
     *      maxMessage = "max value not grater than {{ limit }} to enter"
     * )
     *
     */
    private $rangeMax;
    
    /**
     * $text
     *
     * @var string @JMS\Type("string")
     *
     */
    private $text;
    
    /**
     *
     * @param integer $id
     */
    public function setId($id)
    {
    	$this->id = $id;
    	return $this;
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
     * @param int $rangeMin
     */
    public function setRangeMin($rangeMin)
    {
    	$this->rangeMin = $rangeMin;
    }
    
    /**
     *
     * @return int
     */
    public function getRangeMin()
    {
    	return $this->rangeMin;
    }
    
    /**
     *
     * @param int $rangeMax
     */
    public function setRangeMax($rangeMax)
    {
    	$this->rangeMax = $rangeMax;
    }
    
    /**
     *
     * @return int
     */
    public function getRangeMax()
    {
    	return $this->rangeMax;
    }
    
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
    
}