<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
/**
 * Data Transfer Object for WessLink
 *
 * @package Synapse\SurveyBundle\EntityDto
 */
class WessLinkDto
{
    /**
     * wessSurveyId
     *
     * @var integer @JMS\Type("integer")
     */
    private $wessSurveyId;

    /**
     * wessCohortId
     *
     * @var integer @JMS\Type("integer")
     */
    private $wessCohortId;

    /**
     * wessProdYear
     *
     * @var integer @JMS\Type("integer")
     */
    private $wessProdYear;

    /**
     * wessOrderId
     *
     * @var integer @JMS\Type("integer")
     */
    private $wessOrderId;

    /**
     * status
     *
     * @var integer @JMS\Type("string")
     */
    private $status;

    /**
     * openDate
     *
     * @var datetime @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $openDate;

    /**
     * closeDate
     *
     * @var datetime @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $closeDate;

    /**
     * wessLaunchedflag
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $wessLaunchedflag;

    /**
     *
     * @param integer $wessSurveyId
     */
    public function setWessSurveyId($wessSurveyId)
    {
        $this->wessSurveyId = $wessSurveyId;
    }

    /**
     *
     * @return integer
     */
    public function getWessSurveyId()
    {
        return $this->wessSurveyId;
    }

    /**
     *
     * @param integer $wessCohortId
     */
    public function setWessCohortId($wessCohortId)
    {
        $this->wessCohortId = $wessCohortId;
    }

    /**
     *
     * @return integer
     */
    public function getWessCohortId()
    {
        return $this->wessCohortId;
    }

    /**
     *
     * @param integer $wessProdYear
     */
    public function setWessProdYear($wessProdYear)
    {
        $this->wessProdYear = $wessProdYear;
    }

    /**
     *
     * @return integer
     */
    public function getWessProdYear()
    {
        return $this->wessProdYear;
    }

    /**
     *
     * @param integer $wessOrderId
     */
    public function setWessOrderId($wessOrderId)
    {
        $this->wessOrderId = $wessOrderId;
    }

    /**
     *
     * @return integer
     */
    public function getWessOrderId()
    {
        return $this->wessOrderId;
    }

    /**
     * @param mixed $closeDate
     */
    public function setCloseDate($closeDate)
    {
        $this->closeDate = $closeDate;
    }

    /**
     * @return mixed
     */
    public function getCloseDate()
    {
        return $this->closeDate;
    }

    /**
     * @param mixed $openDate
     */
    public function setOpenDate($openDate)
    {
        $this->openDate = $openDate;
    }

    /**
     * @return mixed
     */
    public function getOpenDate()
    {
        return $this->openDate;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $wessLaunchedflag
     */
    public function setWessLaunchedflag($wessLaunchedflag)
    {
        $this->wessLaunchedflag = $wessLaunchedflag;
    }

    /**
     * @return boolean
     */
    public function getWessLaunchedflag()
    {
        return $this->wessLaunchedflag;
    }
}