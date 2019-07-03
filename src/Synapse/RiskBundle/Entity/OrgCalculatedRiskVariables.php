<?php
namespace Synapse\RiskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * OrgCalculatedRiskVariables
 *
 * @ORM\Table(name="org_calculated_risk_variables", indexes={@ORM\Index(name="fk_org_computed_risk_variables_person1_idx", columns={"person_id"}), @ORM\Index(name="fk_org_computed_risk_variables_risk_variable1_idx", columns={"risk_variable_id"}), @ORM\Index(name="fk_org_calculated_risk_variables_risk_model_master1_idx", columns={"risk_model_id"})})
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\OrgCalculatedRiskVariablesRepository")
 */
class OrgCalculatedRiskVariables
{

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\Id
     *      @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $org;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\Id
     *      @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $person;

    /**
     *
     * @var \Synapse\RiskBundle\Entity\RiskVariable @ORM\Id
     *      @ORM\ManyToOne(targetEntity="Synapse\RiskBundle\Entity\RiskVariable")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="risk_variable_id", referencedColumnName="id", nullable=false)
     *      })
     */
    private $riskVariable;

    /**
     *
     * @var integer @ORM\Column(name="calc_bucket_value", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $calcBucketValue;

    /**
     *
     * @var string @ORM\Column(name="calc_weight", type="decimal", precision=8, scale=4, nullable=true, unique=false)
     */
    private $calcWeight;

    /**
     *
     * @var \Synapse\RiskBundle\Entity\RiskModelMaster @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\ManyToOne(targetEntity="Synapse\RiskBundle\Entity\RiskModelMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="risk_model_id", referencedColumnName="id", nullable=false)
     *      })
     */
    private $riskModel;

    /**
     *
     * @var string @ORM\Column(name="risk_source_value", type="decimal", precision=12, scale=4, nullable=true, unique=false)
     */
    private $riskSourceValue;
    
    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     * 
     */
    private $createdAt;

    /**
     * Set org
     *
     * @param \Synapse\CoreBundle\Entity\Organization $org            
     */
    public function setOrg(\Synapse\CoreBundle\Entity\Organization $org = null)
    {
        $this->org = $org;
        
        return $this;
    }

    /**
     * Get org
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrg()
    {
        return $this->org;
    }

    /**
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     *
     * @param \Synapse\RiskBundle\Entity\RiskVariable $riskVariable            
     */
    public function setRiskVariable($riskVariable)
    {
        $this->riskVariable = $riskVariable;
    }

    /**
     *
     * @return \Synapse\RiskBundle\Entity\RiskVariable
     */
    public function getRiskVariable()
    {
        return $this->riskVariable;
    }

    /**
     * Set calcBucketValue
     */
    public function setCalcBucketValue($calcBucketValue)
    {
        $this->calcBucketValue = $calcBucketValue;
        
        return $this;
    }

    /**
     * Get calcBucketValue
     */
    public function getCalcBucketValue()
    {
        return $this->calcBucketValue;
    }

    /**
     * Set calcWeight
     */
    public function setCalcWeight($calcWeight)
    {
        $this->calcWeight = $calcWeight;
        
        return $this;
    }

    /**
     * Get calcWeight
     */
    public function getCalcWeight()
    {
        return $this->calcWeight;
    }

    /**
     *
     * @param \Synapse\RiskBundle\Entity\RiskModelMaster $riskModel            
     */
    public function setRiskModel($riskModel)
    {
        $this->riskModel = $riskModel;
    }

    /**
     *
     * @return \Synapse\RiskBundle\Entity\RiskModelMaster
     */
    public function getRiskModel()
    {
        return $this->riskModel;
    }

    /**
     * Set riskSourceValue
     */
    public function setRiskSourceValue($riskSourceValue)
    {
        $this->riskSourceValue = $riskSourceValue;
        
        return $this;
    }

    /**
     * Get riskSourceValue
     */
    public function getRiskSourceValue()
    {
        return $this->riskSourceValue;
    }
    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * 
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    /**
     * Get createdAt
     *
     * @return \Datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
