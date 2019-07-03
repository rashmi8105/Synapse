<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class SurveyListDetailsDto
{
    /**
     * organizationId
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $organizationId;
    
    /**
     * langId
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $langId;
    
    /**
     *
     * @var Object @JMS\Type("array<Synapse\SurveyBundle\EntityDto\SurveysDetailsArrayDto>")
     *
     */
    private $surveys;

    /**
     * @param int $langId
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     * @return int
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @param Object $surveys
     */
    public function setSurveys($surveys)
    {
        $this->surveys = $surveys;
    }

    /**
     * @return Object
     */
    public function getSurveys()
    {
        return $this->surveys;
    }


}