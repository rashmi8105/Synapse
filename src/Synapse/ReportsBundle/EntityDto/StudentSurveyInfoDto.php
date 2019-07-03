<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class StudentSurveyInfoDto
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * survey_name
     *
     * @var string @JMS\Type("string")
     */
    private $surveyName;

    /**
     * year
     *
     * @var string @JMS\Type("string")
     */
    private $year;

    /**
     * startDate
     *
     * @var string @JMS\Type("string")
     */
    private $startDate;

    /**
     * endDate
     *
     * @var string @JMS\Type("string")
     */
    private $endDate;

    /**
     * survey_status
     *
     * @var string @JMS\Type("string")
     */
    private $surveyStatus;

    /**
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     *
     * @param string $year            
     */
    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     *
     * @param string $startDate            
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     *
     * @param string $endDate            
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     *
     * @param string $surveyStatus            
     */
    public function setSurveyStatus($surveyStatus)
    {
        $this->surveyStatus = $surveyStatus;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getSurveyStatus()
    {
        return $this->surveyStatus;
    }
}