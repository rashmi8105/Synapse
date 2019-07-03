<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class SurveySnapshotSectionResponseDto
{
	/**
     * response_text
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $responseText;
	
	/**
     * no_responded
     *
     * @var integer @JMS\Type("integer")
     */
    private $noResponded;
	
	/**
     * response_percentage
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $responsePercentage;
	
	/**
     * responseText
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $responseValue;
	
	/**
     * percentage_of_response
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $percentageOfResponse;
	
	/**
     * mean
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $mean;
	
	/**
     * stdDeviation
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $stdDeviation;
	
	/**
     * mode
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $mode;
	
	/**
     * median
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $median;
	
	/**
     * min
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $min;
	
	/**
     * max
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $max;
	
	/**
     * optionValue
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $optionValue;	

	/**
     * optionId
     *
     * @var integer @JMS\Type("integer")
     */
    private $optionId;	
	
	/**
     *
     * @param string $responseText            
     */
    public function setResponseText($responseText)
    {
        $this->responseText = $responseText;
    }

    /**
     *
     * @return string
     */
    public function getResponseText()
    {
        return $this->responseText;
    }
	
	/**
     *
     * @param string $responsePercentage            
     */
    public function setResponsePercentage($responsePercentage)
    {
        $this->responsePercentage = $responsePercentage;
    }

    /**
     *
     * @return string
     */
    public function getResponsePercentage()
    {
        return $this->responsePercentage;
    }
	
	/**
     *
     * @param int $noResponded            
     */
    public function setNoResponded($noResponded)
    {
        $this->noResponded = $noResponded;
    }

    /**
     *
     * @return int
     */
    public function getNoResponded()
    {
        return $this->noResponded;
    }
	
	/**
     *
     * @param string $responseValue            
     */
    public function setResponseValue($responseValue)
    {
        $this->responseValue = $responseValue;
    }

    /**
     *
     * @return string
     */
    public function getResponseValue()
    {
        return $this->responseValue;
    }
	
	
	/**
     *
     * @param string $optionValue            
     */
    public function setOptionValue($optionValue)
    {
        $this->optionValue = $optionValue;
    }

    /**
     *
     * @return string
     */
    public function getOptionValue()
    {
        return $this->optionValue;
    }	

	/**
     *
     * @param string $percentageOfResponse            
     */
    public function setPercentageOfResponse($percentageOfResponse)
    {
        $this->percentageOfResponse = $percentageOfResponse;
    }

    /**
     *
     * @return string
     */
    public function getPercentageOfResponse()
    {
        return $this->percentageOfResponse;
    }

	/**
     *
     * @param string $mean            
     */
    public function setMean($mean)
    {
        $this->mean = $mean;
    }

    /**
     *
     * @return string
     */
    public function getMean()
    {
        return $this->mean;
    }	
	
	/**
     *
     * @param string $mode            
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

	/**
     *
     * @param string $median            
     */
    public function setMedian($median)
    {
        $this->median = $median;
    }

    /**
     *
     * @return string
     */
    public function getMedian()
    {
        return $this->median;
    }	
	/**
     *
     * @param string $min            
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     *
     * @return string
     */
    public function getMin()
    {
        return $this->median;
    }	
	/**
     *
     * @param string $max            
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     *
     * @return string
     */
    public function getMax()
    {
        return $this->max;
    }	
	/**
     *
     * @param string $stdDeviation            
     */
    public function setStdDeviation($stdDeviation)
    {
        $this->stdDeviation = $stdDeviation;
    }

    /**
     *
     * @return string
     */
    public function getStdDeviation()
    {
        return $this->stdDeviation;
    }	
	/**
     *
     * @param int $optionId            
     */
    public function setOptionId($optionId)
    {
        $this->optionId = $optionId;
    }

    /**
     *
     * @return int
     */
    public function getOptionId()
    {
        return $this->optionId;
    }
}