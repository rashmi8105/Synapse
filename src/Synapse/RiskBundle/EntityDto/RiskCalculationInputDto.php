<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class RiskCalculationInputDto
{

    /**
     * risk variable id
     *
     * @var string
     *
     *      @JMS\Type("string")
     */
    private $id;
    
    /**
     * organization's id
     *
     * @var integer
     *
     *      @JMS\Type("integer")
     */
    private $orgId;
    
    /**
     * person's id
     *
     * @var integer
     *
     *      @JMS\Type("integer")
     */
    private $personId;
    
    /**
     * whether a risk variable needs to be calculated or not
     *
     * @var boolean
     *
     *      @JMS\Type("boolean")
     */
    private $isRiskvalCalcRequired;
    
    /**
     * whether a person's success marker needs to be calculated or not
     *
     * @var boolean
     *
     *      @JMS\Type("boolean")
     */
    private $isSuccessMarkerCalcReqd;
    
    /**
     * whether a person's talking points need to be calculated or not
     *
     * @var boolean
     *
     *      @JMS\Type("boolean")
     */
    private $isTalkingPointCalcReqd;
    
    /**
     * whether a factor needs to be calculated or not
     *
     * @var boolean
     *
     *      @JMS\Type("boolean")
     */
    private $isFactorCalcReqd;


    /**
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return integer
     */
    public function getOrganizationId()
    {
        return $this->orgId;
    }

    /**
     *
     * @param integer $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->orgId = $organizationId;
    }

    /**
     *
     * @return integer
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     *
     * @param integer $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return boolean
     */
    public function getIsRiskvalCalcRequired()
    {
        return $this->isRiskvalCalcRequired;
    }

    /**
     * @param boolean $isRiskvalCalcRequired
     */
    public function setIsRiskvalCalcRequired($isRiskvalCalcRequired)
    {
    	$this->isRiskvalCalcRequired = $isRiskvalCalcRequired;
    }

    /**
     *
     * @return boolean
     */
    public function getIsSuccessMarkerCalcReqd()
    {
        return $this->isSuccessMarkerCalcReqd;
    }

    /**
     * @param boolean $isSuccessMarkerCalcReqd
     */
    public function setIsSuccessMarkerCalcReqd($isSuccessMarkerCalcReqd)
    {
    	$this->isSuccessMarkerCalcReqd = $isSuccessMarkerCalcReqd;
    }

    /**
     *
     * @return boolean
     */
    public function getIsTalkingPointCalcReqd()
    {
        return $this->isTalkingPointCalcReqd;
    }

    /**
     * @param boolean $isTalkingPointCalcReqd
     */
    public function setIsTalkingPointCalcReqd($isTalkingPointCalcReqd)
    {
    	$this->isTalkingPointCalcReqd = $isTalkingPointCalcReqd;
    }

    /**
     *
     * @return boolean
     */
    public function getIsFactorCalcReqd()
    {
        return $this->isFactorCalcReqd;
    }

    /**
     * @param boolean $isFactorCalcReqd
     */
    public function setIsFactorCalcReqd($isFactorCalcReqd)
    {
    	$this->isFactorCalcReqd = $isFactorCalcReqd;
    }
}