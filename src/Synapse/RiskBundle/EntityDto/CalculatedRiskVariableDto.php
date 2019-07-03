<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class CalculatedRiskVariableDto
{

    /**
     * $riskModelName
     *
     * @var integer @JMS\Type("string")
     *     
     *     
     */
    private $riskModelName;

    /**
     * $personId
     *
     * @var integer @JMS\Type("integer")
     *     
     *     
     */
    private $personId;

    /**
     * $orgId
     *
     * @var integer @JMS\Type("integer")
     *     
     *     
     */
    private $orgId;

    /**
     * profile blocks
     * @var Object
     * @JMS\Type("array<Synapse\RiskBundle\EntityDto\CalculatedSourceDto>")
     * 
     *
     */
    private $riskSource;

    /**
     *
     * @param int $orgId            
     */
    public function setOrgId($orgId)
    {
        $this->orgId = $orgId;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->orgId;
    }

    /**
     *
     * @param int $riskSource            
     */
    public function setRiskSource($riskSource)
    {
        $this->riskSource = $riskSource;
    }

    /**
     *
     * @return int
     */
    public function getRiskSource()
    {
        return $this->riskSource;
    }

    /**
     *
     * @param int $riskModelName            
     */
    public function setRiskModelName($riskModelName)
    {
        $this->riskModelName = $riskModelName;
    }

    /**
     *
     * @return int
     */
    public function getRiskModelName()
    {
        return $this->riskModelName;
    }

    /**
     *
     * @param int $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }
}