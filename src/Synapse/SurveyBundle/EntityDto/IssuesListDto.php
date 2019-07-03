<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for
 * 
 * @package Synapse\SurveyBundle\EntityDto
 */
class IssuesListDto
{
    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $id;
    
    /**
     * topIssueName
     *
     * @var string @JMS\Type("string")
     *
     */
    private $topIssueName;
    
    /**
     * topIssueImage
     *
     * @var string @JMS\Type("string")
     *
     */
    private $topIssueImage;
    
    /**
     * percentage
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $percentage;
    
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
     * @param string $topIssueName
     */
    public function setTopIssueName($topIssueName)
    {
    	$this->topIssueName = ($topIssueName)?$topIssueName:'';
    }
    
    /**
     *
     * @return string
     */
    public function getTopIssueName()
    {
    	return $this->topIssueName;
    }
    
    /**
     *
     * @param string $topIssueImage
     */
    public function setTopIssueImage($topIssueImage)
    {
    	$this->topIssueImage = ($topIssueImage)?$topIssueImage:'';
    }
    
    /**
     *
     * @return string
     */
    public function getTopIssueImage()
    {
    	return $this->topIssueImage;
    }
    
    /**
     *
     * @param integer $percentage
     */
    public function setPercentage($percentage)
    {
    	$this->percentage = $percentage;
    }
    
    /**
     *
     * @return integer
     */
    public function getPercentage()
    {
    	return $this->percentage;
    }
    
}