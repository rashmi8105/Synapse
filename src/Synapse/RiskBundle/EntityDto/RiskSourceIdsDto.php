<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Risk Variables
 *
 * @package Synapse\RiskBundle\EntityDto
 */
class RiskSourceIdsDto
{
    /**
     * source_type
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $sourceType;
    
    /**
     * survey_id
     *
     * @var string @JMS\Type("string")
     *
     *
     */
    private $surveyId;
    
    /**
     * ids
     *
     * @var string @JMS\Type("string")
     *     
     *     
     */
    private $ids;    
    
    /**
     * org_id
     *
     * @var integer @JMS\Type("integer")
     *
     *
     */
    private $orgId;

    /**
     * campus_id
     *
     * @var string @JMS\Type("string")
     *
     *
     */
    private $campusId;

    /**
     * @param string $orgId
     */
    public function setOrgId($orgId)
    {
        $this->orgId = $orgId;
    }
    
    /**
     * @param string $campusId
     */
    public function setCampusId($campusId)
    {
        $this->campusId = $campusId;
    }

    /**
     * @param int $ids
     */
    public function setIds($ids)
    {
        $this->ids = $ids;
    }

    /**
     * @return int
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * @param string $sourceType
     */
    public function setSourceType($sourceType)
    {
        $this->sourceType = $sourceType;
    }

    /**
     * @return string
     */
    public function getSourceType()
    {
        return $this->sourceType;
    }

    /**
     * @param string $surveyId
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    /**
     * @return string
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }
    
  
}