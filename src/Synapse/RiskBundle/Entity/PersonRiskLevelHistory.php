<?php
namespace Synapse\RiskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * PersonRiskLevelHistory
 *
 * @ORM\Table(name="person_risk_level_history", indexes={@ORM\Index(name="fk_person_risk_level_history_person1_idx", columns={"person_id"}),
 * @ORM\Index(name="fk_person_risk_level_history_risk_model_master1_idx", columns={"risk_model_id"}),
 * @ORM\Index(name="fk_person_risk_level_history_risk_level1_idx", columns={"risk_level"})})
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\PersonRiskLevelHistoryRepository")
 */
class PersonRiskLevelHistory
{

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person
     *      @ORM\Id
     *      @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $person;

    /**
     * @ORM\Id
     * 
     * @var \DateTime
     *      @ORM\Column(name="date_captured", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $dateCaptured;

    /**
     *
     * @var \Synapse\RiskBundle\Entity\RiskLevels @ORM\ManyToOne(targetEntity="Synapse\RiskBundle\Entity\RiskLevels")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="risk_level", referencedColumnName="id", nullable=true)
     *      })
     */
    private $riskLevel;

    /**
     *
     * @var \Synapse\RiskBundle\Entity\RiskModelMaster
     *      @ORM\ManyToOne(targetEntity="Synapse\RiskBundle\Entity\RiskModelMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="risk_model_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $riskModel;

    /**
     *
     * @var string
     *      @ORM\Column(name="risk_score", type="decimal", precision=6, scale=4, nullable=true)
     */
    private $riskScore;

    /**
     *
     * @var string
     *      @ORM\Column(name="weighted_value", type="decimal", precision=9, scale=4, nullable=true)
     */
    private $weightedValue;

    /**
     *
     * @var string
     *      @ORM\Column(name="maximum_weight_value", type="decimal", precision=9, scale=4, nullable=true)
     */
    private $maximumWeightValue;

    /**
     *
     * @var \DateTime
     *      @ORM\Column(name="created_at", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $createdAt;

    /**
     *
     * @var \DateTime
     *      @ORM\Column(name="queued_at", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $queuedAt;

    /**
     * Set maximumWeightValue
     */
    public function setMaximumWeightValue($maximumWeightValue)
    {
        $this->maximumWeightValue = $maximumWeightValue;
        
        return $this;
    }

    /**
     * Get maximumWeightValue
     */
    public function getMaximumWeightValue()
    {
        return $this->maximumWeightValue;
    }

    /**
     * Set weightedValue
     */
    public function setWeightedValue($weightedValue)
    {
        $this->weightedValue = $weightedValue;
        
        return $this;
    }

    /**
     * Get weightedValue
     */
    public function getWeightedValue()
    {
        return $this->weightedValue;
    }

    /**
     * Set riskScore
     */
    public function setRiskScore($riskScore)
    {
        $this->riskScore = $riskScore;
        
        return $this;
    }

    /**
     * Get riskScore
     */
    public function getRiskScore()
    {
        return $this->riskScore;
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
     *
     * @param \Synapse\RiskBundle\Entity\RiskLevels $riskLevel            
     */
    public function setRiskLevel($riskLevel)
    {
        $this->riskLevel = $riskLevel;
    }

    /**
     *
     * @return \Synapse\RiskBundle\Entity\RiskLevels
     */
    public function getRiskLevel()
    {
        return $this->riskLevel;
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
     * @param \DateTime $dateCaptured            
     */
    public function setDateCaptured($dateCaptured)
    {
        $this->dateCaptured = $dateCaptured;
    }

    /**
     *
     * @return \DateTime
     */
    public function getDateCaptured()
    {
        return $this->dateCaptured;
    }

    /**
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     *
     * @param \DateTime $queuedAt
     */
    public function setQueuedAt($queuedAt)
    {
        $this->queuedAt = $queuedAt;
    }

    /**
     *
     * @return \DateTime
     */
    public function getQueuedAt()
    {
        return $this->queuedAt;
    }
}
