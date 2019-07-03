<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for
 * 
 * @package Synapse\SurveyBundle\EntityDto
 */
class IssueCreateDto
{
    /**
     * Id of an issue.
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;
    
    /**
     * Deprecated value representing the language of an organization. Will always be 1 (ENGLISH).
     *
     * @var integer @JMS\Type("integer")
     */
    private $langId;
    
    /**
     * Name of an issue.
     *
     * @var string @JMS\Type("string")
     */
    private $issueName;
    
    /**
     * Id of the survey that covers/contains the specific issue, i.e. (issue=homesickness, financial stress, struggling in classes, etc.)
     *
     * @var integer @JMS\Type("integer")
     */
    private $surveyId;
    
    /**
     * Object representing the factors related to/covered by an issue.
     *
     * @var Object
     * @JMS\Type("Synapse\SurveyBundle\EntityDto\IssueCreateFactorDto")
     */
    private $factors;
    
    /**
     * Object containing survey questions that relate to a specific issue.
     *
     * @var Object
     * @JMS\Type("Synapse\SurveyBundle\EntityDto\IssueCreateQuestionsDto")
     */
    private $questions;
    
    /**
     * Name of the image representing an issue.
     *
     * @var string @JMS\Type("string")
     */
    private $issueImage;
    
    /**
     * Sets the id of an issue.
     *
     * @param integer $id
     */
    public function setId($id)
    {
    	$this->id = $id;
    }
    
    /**
     * Returns the id of an issue.
     *
     * @return integer
     */
    public function getId()
    {
    	return $this->id;
    }
    
    /**
     * Sets the language id of an issue.
     *
     * @param int $langId
     */
    public function setLangId($langId)
    {
    	$this->langId = $langId;
    }
    
    /**
     * Returns the language id of an issue.
     *
     * @return int
     */
    public function getLangId()
    {
    	return $this->langId;
    }
    
    /**
     * Sets the name of an issue.
     *
     * @param string $issueName
     *
     * @return IssueCreateDto
     */
    public function setIssueName($issueName)
    {
    	$this->issueName = $issueName;
    	return $this;
    }
    
    /**
     * Returns the name of an issue.
     *
     * @return string
     */
    public function getIssueName()
    {
    	return $this->issueName;
    }
    
    /**
     * Sets the id of the survey that covers/includes questions for an issue.
     *
     * @param int $surveyId
     *
     * @return IssueCreateDto
     */
    public function setSurveyId($surveyId)
    {
    	$this->surveyId = $surveyId;
    	return $this;
    }
    
    /**
     * Returns the id of the survey that covers/includes questions for an issue.
     *
     * @return int
     */
    public function getSurveyId()
    {
    	return $this->surveyId;
    }
    
    /**
     * Sets the factors that are included in an issue.
     *
     * @param Object $factors
     *
     * @return IssueCreateDto
     */
    public function setFactors($factors)
    {
    	$this->factors = $factors;
    	return $this;
    }
    
    /**
     * Returns the factors that are included in an issue.
     *
     * @return Object
     */
    public function getFactors()
    {
    	return $this->factors;
    }
    
    /**
     * Sets the questions that relate to an issue.
     *
     * @param Object $questions
     *
     * @return IssueCreateDto
     */
    public function setQuestions($questions)
    {
    	$this->questions = $questions;
    	return $this;
    }
    
    /**
     * Returns the questions that relate to an issue.
     *
     * @return Object
     */
    public function getQuestions()
    {
    	return $this->questions;
    }
    
    /**
     * Sets the image for an issue.
     *
     * @param string $issueImage
     *
     * @return IssueCreateDto
     */
    public function setIssueImage($issueImage)
    {
    	$this->issueImage = $issueImage;
    	return $this;
    }
    
    /**
     * Returns the image for an issue.
     *
     * @return string
     */
    public function getIssueImage()
    {
    	return $this->issueImage;
    }
}