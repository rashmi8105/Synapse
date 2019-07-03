<?php

namespace Synapse\RiskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * OrgRiskGroupModel
 *
 * @ORM\Table(name="org_risk_group_model", indexes={@ORM\Index(name="fk_orgriskmodel_orgid", columns={"org_id"}), @ORM\Index(name="fk_orgriskmodel_riskmodelid", columns={"risk_model_id"}), @ORM\Index(name="fk_org_risk_group_model_risk_group1_idx", columns={"risk_group_id"})})
 * @UniqueEntity(fields={"org", "riskModel","riskGroup"},message="Combination of Campus, Risk Group, and Model already exists.")
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\OrgRiskGroupModelRepository")
 * 
 * 
 */
class OrgRiskGroupModel extends BaseEntity
{    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;
    
    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *     
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="org_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $org;

    /**
     * @var \Synapse\CoreBundle\Entity\RiskModelMaster
     *
     * @ORM\ManyToOne(targetEntity="Synapse\RiskBundle\Entity\RiskModelMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="risk_model_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $riskModel;

    /**
     * @var \Synapse\RiskBundle\Entity\RiskGroup
     *
     * @ORM\ManyToOne(targetEntity="Synapse\RiskBundle\Entity\RiskGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="risk_group_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $riskGroup;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="assignment_date", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $assignmentDate;

    /**
     * Set assignmentDate
     *
     * @param \DateTime $assignmentDate
     * @return OrgRiskGroupModel
     */
    public function setAssignmentDate($assignmentDate)
    {
        $this->assignmentDate = $assignmentDate;

        return $this;
    }

    /**
     * Get assignmentDate
     *
     * @return \DateTime 
     */
    public function getAssignmentDate()
    {
        return $this->assignmentDate;
    }

    /**
     * Set org
     *
     * @param \Synapse\CoreBundle\Entity\Organization $org
     * @return OrgRiskGroupModel
     */
    public function setOrg(\Synapse\CoreBundle\Entity\Organization $org)
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
     * Set riskModel
     *
     * @param \Synapse\RiskBundle\Entity\RiskModelMaster $riskModel
     * @return OrgRiskGroupModel
     */
    public function setRiskModel(\Synapse\RiskBundle\Entity\RiskModelMaster $riskModel=null)
    {
        $this->riskModel = $riskModel;

        return $this;
    }

    /**
     * Get riskModel
     *
     * @return \Synapse\RiskBundle\Entity\RiskModelMaster 
     */
    public function getRiskModel()
    {
        return $this->riskModel;
    }

    /**
     * Set riskGroup
     *
     * @param \Synapse\RiskBundle\Entity\RiskGroup $riskGroup
     * @return OrgRiskGroupModel
     */
    public function setRiskGroup(\Synapse\RiskBundle\Entity\RiskGroup $riskGroup)
    {
        $this->riskGroup = $riskGroup;

        return $this;
    }

    /**
     * Get riskGroup
     *
     * @return \Synapse\RiskBundle\Entity\RiskGroup 
     */
    public function getRiskGroup()
    {
        return $this->riskGroup;
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
}
