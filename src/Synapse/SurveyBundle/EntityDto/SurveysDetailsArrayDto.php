<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class SurveysDetailsArrayDto
{

    /**
     * surveyId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $surveyId;

    /**
     * surveyName
     *
     * @var integer @JMS\Type("string")
     *     
     */
    private $surveyName;

    /**
     * openDate
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $openDate;

    /**
     * closeDate
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $closeDate;

    /**
     * status
     *
     * @var integer @JMS\Type("string")
     *     
     */
    private $status;

    /**
     * wessAdminLink
     *
     * @var integer @JMS\Type("string")
     *     
     */
    private $wessAdminLink;

    /**
     *
     * @param int $surveyId            
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    /**
     *
     * @return int
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     *
     * @param string $surveyName            
     */
    public function setSurveyName($surveyName)
    {
        $this->surveyName = $surveyName;
    }

    /**
     *
     * @return string
     */
    public function getSurveyName()
    {
        return $this->surveyName;
    }

    /**
     * - * @param mixed $openDate
     */
    public function setOpenDate($openDate)
    {
        $this->openDate = $openDate;
        
        return $this;
    }

    /**
     *
     * @return mixed
     */
    public function getOpenDate()
    {
        return $this->openDate;
    }

    /**
     *
     * @param mixed $closeDate            
     */
    public function setCloseDate($closeDate)
    {
        $this->closeDate = $closeDate;
        
        return $this;
    }

    /**
     *
     * @return mixed
     */
    public function getCloseDate()
    {
        return $this->closeDate;
    }

    /**
     *
     * @param string $status            
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param string $wessAdminLink            
     */
    public function setWessAdminLink($wessAdminLink)
    {
        $this->wessAdminLink = $wessAdminLink;
    }

    /**
     *
     * @return string
     */
    public function getWessAdminLink()
    {
        return $this->wessAdminLink;
    }
}
