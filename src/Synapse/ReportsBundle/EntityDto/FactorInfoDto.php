<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class FactorInfoDto
{

    /**
     * factorNumber
     *
     * @var integer @JMS\Type("integer")
     */
    private $factorNumber;
	
	/**
     * factorText
     *
     * @var string @JMS\Type("string")
     */
    private $factorText;
	
	/**
     * reportOptions 
     *
     * @var array @JMS\Type("array")
     *     
     */
    private $reportOptions;
	
	/**
     * summary 
     *
     * @var array @JMS\Type("array")
     *     
     */
    private $summary;
	/**
     * additionalData 
     *
     * @var array @JMS\Type("array")
     *     
     */
    private $additionalData;	
	
	/**
     *
     * @param int $factorNumber            
     */
    public function setFactorNumber($factorNumber)
    {
        $this->factorNumber = $factorNumber;
    }

    /**
     *
     * @return int
     */
    public function getFactorNumber()
    {
        return $this->factorNumber;
    }
	
	/**
     *
     * @param string $factorText            
     */
    public function setFactorText($factorText)
    {
        $this->factorText = $factorText;
    }

    /**
     *
     * @return string
     */
    public function getFactorText()
    {
        return $this->factorText;
    }

/**
     *
     * @return array
     */
    public function getReportOptions()
    {
        return $this->reportOptions;
    }

    /**
     *
     * @param array $reportOptions            
     */
    public function setReportOptions($reportOptions)
    {
        $this->reportOptions = $reportOptions;
    }	
	
	/**
     *
     * @return array
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     *
     * @param array $summary            
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }	
	
	/**
     *
     * @return array
     */
    public function getAdditionalData()
    {
        return $this->additionalData;
    }

    /**
     *
     * @param array $additionalData            
     */
    public function setAdditionalData($additionalData)
    {
        $this->additionalData = $additionalData;
    }	
}