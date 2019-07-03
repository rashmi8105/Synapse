<?php
namespace Synapse\RiskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * RiskGroupPersonHistory
 *
 * @ORM\Table(name="risk_group_person_history", indexes={@ORM\Index(name="fk_risk_group_person_history_person1_idx", columns={"person_id"}), @ORM\Index(name="fk_risk_group_person_history_risk_group1_idx", columns={"risk_group_id"})})
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\RiskGroupPersonHistoryRepository")
 */
class RiskGroupPersonHistory
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
     *
     * @var \Synapse\CoreBundle\Entity\RiskModelMaster 
     *      @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $person;

    /**
     *
     * @var \Synapse\RiskBundle\Entity\RiskGroup 
     *      @ORM\ManyToOne(targetEntity="Synapse\RiskBundle\Entity\RiskGroup")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="risk_group_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $riskGroup;

    /**
     * 
     * @var \DateTime 
     * @ORM\Column(name="assignment_date", type="datetime", nullable=true)
     */
    private $assignmentDate;

    /**
     * @param \Synapse\RiskBundle\Entity\RiskGroup $riskGroup
     */
    public function setRiskGroup($riskGroup)
    {
        $this->riskGroup = $riskGroup;
    }

    /**
     * @return \Synapse\RiskBundle\Entity\RiskGroup
     */
    public function getRiskGroup()
    {
        return $this->riskGroup;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\RiskModelMaster $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\RiskModelMaster
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param \DateTime $assignmentDate
     */
    public function setAssignmentDate($assignmentDate)
    {
        $this->assignmentDate = $assignmentDate;
    }

    /**
     * @return \DateTime
     */
    public function getAssignmentDate()
    {
        return $this->assignmentDate;
    }

    /** Get id
    *
    * @return integer
    */
    public function getId()
    {
        return $this->id;
    }
    
}
