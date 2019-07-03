<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class FactorsArrayDto
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * factorName
     *
     * @var integer @JMS\Type("string")
     *     
     */
    private $factorName;

    /**
     * sequence
     *
     * @var integer @JMS\Type("integer")
     */
    private $sequence;
    
    /**
     * questionCount
     *
     * @var integer @JMS\Type("integer")
     */
    private $questionCount;
	
	/**
     * rangeMin
     *
     * @var float @JMS\Type("float")
     */
    private $rangeMin;
	
	/**
     * rangeMax
     *
     * @var float @JMS\Type("float")
     */
    private $rangeMax;

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
     * @param string $factorName            
     */
    public function setFactorName($factorName)
    {
        $this->factorName = $factorName;
    }

    /**
     *
     * @return string
     */
    public function getFactorName()
    {
        return $this->factorName;
    }

    /**
     *
     * @param integer $sequence            
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     *
     * @return integer
     */
    public function getSequence()
    {
        return $this->sequence;
    }
    /**
     *
     * @param integer $questionCount            
     */
    public function setQuestionCount($questionCount)
    {
        $this->questionCount = $questionCount;
    }

    /**
     *
     * @return integer
     */
    public function getQuestionCount()
    {
        return $this->questionCount;
    }
	
	/**
     *
     * @param float $rangeMin            
     */
    public function setRangeMin($rangeMin)
    {
        $this->rangeMin = $rangeMin;
    }

    /**
     *
     * @return float
     */
    public function getRangeMin()
    {
        return $this->rangeMin;
    }
	
	/**
     *
     * @param float $rangeMax            
     */
    public function setRangeMax($rangeMax)
    {
        $this->rangeMax = $rangeMax;
    }

    /**
     *
     * @return float
     */
    public function getRangeMax()
    {
        return $this->rangeMax;
    }
    
}