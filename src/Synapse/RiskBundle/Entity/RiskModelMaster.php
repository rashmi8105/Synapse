<?php
namespace Synapse\RiskBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * RiskModelMaster
 *
 * @ORM\Table(name="risk_model_master")
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\RiskModelMasterRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"name"},message="Risk Model Name already exists.")
 */
class RiskModelMaster extends BaseEntity
{

    const MODEL_STATE_ARCHIVED = 'Archived';

    const MODEL_STATE_ASSIGNED = 'Assigned';

    const MODEL_STATE_UNASSIGNED = 'Unassigned';

    const MODEL_STATE_INPROCESS = 'InProcess';

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="name", type="string", length=100, precision=0, scale=0, nullable=true, unique=false)
     */
    private $name;

    /**
     *
     * @var \DateTime @ORM\Column(name="calculation_start_date", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $calculationStartDate;

    /**
     *
     * @var \DateTime @ORM\Column(name="calculation_end_date", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $calculationEndDate;

    /**
     *
     * @var string @ORM\Column(name="model_state", type="string", precision=0, scale=0, nullable=true, unique=false,columnDefinition="enum('Archived','Assigned','Unassigned','InProcess')")
     */
    private $modelState;

    /**
     *
     * @var \DateTime @ORM\Column(name="enrollment_date", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $enrollmentDate;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name            
     * @return RiskModelMaster
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set calculationStartDate
     *
     * @param \DateTime $calculationStartDate            
     * @return RiskModelMaster
     */
    public function setCalculationStartDate($calculationStartDate)
    {
        $this->calculationStartDate = $calculationStartDate;
        
        return $this;
    }

    /**
     * Get calculationStartDate
     *
     * @return \DateTime
     */
    public function getCalculationStartDate()
    {
        return $this->calculationStartDate;
    }

    /**
     * Set calculationEndDate
     *
     * @param \DateTime $calculationEndDate            
     * @return RiskModelMaster
     */
    public function setCalculationEndDate($calculationEndDate)
    {
        $this->calculationEndDate = $calculationEndDate;
        
        return $this;
    }

    /**
     * Get calculationEndDate
     *
     * @return \DateTime
     */
    public function getCalculationEndDate()
    {
        return $this->calculationEndDate;
    }

    /**
     * Set modelState
     *
     * @param string $modelState            
     * @return RiskModelMaster
     */
    public function setModelState($modelState)
    {
        if (! in_array($modelState, [
            self::MODEL_STATE_ARCHIVED,
            self::MODEL_STATE_ASSIGNED,
            self::MODEL_STATE_UNASSIGNED,
            self::MODEL_STATE_INPROCESS
        ])) {
            throw new \InvalidArgumentException("Invalid Model State");
        }
        $this->modelState = $modelState;
        
        return $this;
    }

    /**
     * Get modelState
     *
     * @return string
     */
    public function getModelState()
    {
        return $this->modelState;
    }

    /**
     * Set enrollmentDate
     *
     * @param \DateTime $enrollmentDate            
     * @return RiskModelMaster
     */
    public function setEnrollmentDate($enrollmentDate)
    {
        $this->enrollmentDate = $enrollmentDate;
        
        return $this;
    }

    /**
     * Get enrollmentDate
     *
     * @return \DateTime
     */
    public function getEnrollmentDate()
    {
        return $this->enrollmentDate;
    }
}
