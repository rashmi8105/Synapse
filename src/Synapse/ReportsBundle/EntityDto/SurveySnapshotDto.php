<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class SurveySnapshotDto
{

    /**
     * $cohort
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $cohort;

    /**
     * $surveyId
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $surveyId;

    /**
     * $filterAttributes
     *
     * @var array @JMS\Type("array")
     */
    private $filterAttributes;

    public function setCohort($cohort)
    {
        $this->cohort = $cohort;
    }

    public function getCohort()
    {
        return $this->cohort;
    }

    public function setSurvey($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    public function getSurvey()
    {
        return $this->surveyId;
    }

    public function setFilterAttributes($filterAttributes)
    {
        $this->filterAttributes = $filterAttributes;
    }

    public function getFilterAttributes()
    {
        return $this->filterAttributes;
    }
}