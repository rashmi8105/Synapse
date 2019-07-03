<?php
namespace Synapse\SurveyBundle\EntityDto;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Survey Links
 *
 * @package Synapse\SurveyBundle\EntityDto
 */
class SurveyLinkDto
{

    /**
     * $customerId
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $customerId;

    /**
     * $personExternalId
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $personExternalId;

    /**
     * $surveyLink
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $surveyLink;

    /**
     * surveyId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $surveyId;

    /**
     * cohort
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $cohort;
    
    
    /**
     * academicYearId
     *
     * @var string @JMS\Type("string")
     *
     */
    private $academicYearId;

    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     *
     * @return integer
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function setPersonExternalId($personExternalId)
    {
        $this->personExternalId = $personExternalId;
    }

    public function getPersonExternalId()
    {
        return $this->personExternalId;
    }

    public function setSurveyLink($surveyLink)
    {
        $this->surveyLink = $surveyLink;
    }

    public function getSurveyLink()
    {
        return $this->surveyLink;
    }

    /**
     *
     * @param integer $surveyId            
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    /**
     *
     * @return integer
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     *
     * @param string $cohort            
     */
    public function setCohort($cohort)
    {
        $this->cohort = $cohort;
    }
    
    /**
     *
     * @return string
     */
    public function getCohort()
    {
        return $this->cohort;
    }
    
    
    /**
     *
     * @param string $academicYearId
     */
    public function setAcademicYearId($academicYearId)
    {
        $this->academicYearId = $academicYearId;
    }
    
    /**
     *
     * @return string
     */
    public function getAcademicYearId()
    {
        return $this->academicYearId;
    }
}