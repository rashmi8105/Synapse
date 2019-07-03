<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
/**
 *
 * @package Synapse\SurveyBundle\EntityDto
 */


class WessLinkInsertDto
{
    /**
     * $surveyIdExternal
     *
     * @var integer @JMS\Type("integer")
     */
    private $surveyIdExternal;

    /**
     * $cohortIdExternal
     *
     * @var integer @JMS\Type("integer")
     */
    private $cohortIdExternal;

    /**
     * $prodYearExternal
     *
     * @var integer @JMS\Type("integer")
     */
    private $prodYearExternal;

    /**
     * $orderIdExternal
     *
     * @var integer @JMS\Type("integer")
     */
    private $orderIdExternal;
    
    /**
     * $mapOrderKeyExternal
     *
     * @var string @JMS\Type("string")
     */
    private $mapOrderKeyExternal;
    
    
    /**
     * $customerId
     *
     * @var string @JMS\Type("string")
     */
    private $customerId;
    
    
    /**
     * $adminLinkExternal
     *
     * @var string @JMS\Type("string")
     */
    private $adminLinkExternal;
    
    /**
     * status
     *
     * @var integer @JMS\Type("string")
     */
    private $surveyStatus;
    
    /**
     * openDate
     *
     * @var datetime @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $surveyOpenDate;
    
    /**
     * closeDate
     *
     * @var datetime @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $surveyCloseDate;
    
    /**
     * wessLaunchedflag
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $wessLaunchedflag;
    
    
    
    
    /**
     *
     * @param string $wessMapOrderKey
     */
    public function setMapOrderKeyExternal($mapOrderKeyExternal)
    {
        $this->mapOrderKeyExternal = $mapOrderKeyExternal;
    }
    
    /**
     *
     * @return string
     */
    public function getMapOrderKeyExternal()
    {
        return $this->mapOrderKeyExternal;
    }
    
    
    /**
     *
     * @param string $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }
    
    /**
     *
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
    
    
    /**
     *
     * @param string $adminLinkExternal
     */
    public function setAdminLinkExternal($adminLinkExternal)
    {
        $this->adminLinkExternal = $adminLinkExternal;
    }
    
    /**
     *
     * @return string
     */
    public function getAdminLinkExternal()
    {
        return $this->adminLinkExternal;
    }
    
   
    /**
     *
     * @param integer $surveyIdExternal
     */
    public function setSurveyIdExternal($surveyIdExternal)
    {
        $this->surveyIdExternal = $surveyIdExternal;
    }

    /**
     *
     * @return integer
     */
    public function getSurveyIdExternal()
    {
        return $this->surveyIdExternal;
    }

    /**
     *
     * @param integer $wessCohortId
     */
    public function setCohortIdExternal($cohortIdExternal)
    {
        $this->cohortIdExternal = $cohortIdExternal;
    }

    /**
     *
     * @return integer
     */
    public function getCohortIdExternal()
    {
        return $this->cohortIdExternal;
    }

    /**
     *
     * @param integer $wessProdYear
     */
    public function setProdYearExternal($prodYearExternal)
    {
        $this->prodYearExternal = $prodYearExternal;
    }

    /**
     *
     * @return integer
     */
    public function getProdYearExternal()
    {
        return $this->prodYearExternal;
    }

    /**
     *
     * @param integer orderIdExternal
     */
    public function setOrderIdExternal($orderIdExternal)
    {
        $this->orderIdExternal = $orderIdExternal;
    }

    /**
     *
     * @return integer
     */
    public function getOrderIdExternal()
    {
        return $this->orderIdExternal;
    }
   
    /**
     * @param string $surveyStatus
     */
    public function setSurveyStatus($surveyStatus)
    {
        $this->surveyStatus = $surveyStatus;
    }
    
    /**
     * @return string
     */
    public function getSurveyStatus()
    {
        return $this->surveyStatus;
    }
    
    /**
     * @param mixed $surveyCloseDate
     */
    public function setSurveyCloseDate($surveyCloseDate)
    {
        $this->surveyCloseDate = $surveyCloseDate;
    }
    
    /**
     * @return mixed
     */
    public function getSurveyCloseDate()
    {
        return $this->surveyCloseDate;
    }
    
    /**
     * @param mixed $surveyOpenDate
     */
    public function setSurveyOpenDate($surveyOpenDate)
    {
        $this->surveyOpenDate = $surveyOpenDate;
    }
    
    /**
     * @return mixed
     */
    public function getSurveyOpenDate()
    {
        return $this->surveyOpenDate;
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