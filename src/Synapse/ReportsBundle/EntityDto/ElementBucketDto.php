<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 * 
 * @package Synapse\ReportsBundle\EntityDto
 */
class ElementBucketDto
{
	/**
     * id of an element bucket
     * 
     * @var integer @JMS\Type("integer")
     */
    private $id;
	
	/**
     * Name of an element bucket
     *
     * @var string @JMS\Type("string")     
     *
     */
    private $bucketName; 
	
	/**
     * contents of an element bucket
     *
     * @var string @JMS\Type("string")
     */
    private $bucketText; 
	
	/**
     * minimum size/length that a bucket can be
     *
     * @var string @JMS\Type("string")     
     */
    private $rangeMin; 
	
	/**
     * maximum size/length that a bucket can be
     *
     * @var string @JMS\Type("string")     
     */
    private $rangeMax; 
	
	/**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
	
	/**
     * @param string $bucketText
     */
    public function setBucketText($bucketText)
    {
        $this->bucketText = $bucketText;
    }

    /**
     * @return string
     */
    public function getBucketText()
    {
        return $this->bucketText;
    }
	
	/**
     * @param string $bucketName
     */
    public function setBucketName($bucketName)
    {
        $this->bucketName = $bucketName;
    }

    /**
     * @return string
     */
    public function getBucketName()
    {
        return $this->bucketName;
    }
	
	/**
     * @param string $rangeMin
     */
    public function setRangeMin($rangeMin)
    {
        $this->rangeMin = $rangeMin;
    }

    /**
     * @return string
     */
    public function getRangeMin()
    {
        return $this->rangeMin;
    }
	
	/**
     * @param string $rangeMax
     */
    public function setRangeMax($rangeMax)
    {
        $this->rangeMax = $rangeMax;
    }

    /**
     * @return string
     */
    public function getRangeMax()
    {
        return $this->rangeMax;
    }
}	