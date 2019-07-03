<?php
namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * SurveyPages
 *
 * @ORM\Table(name="survey_pages", indexes={@ORM\Index(name="fk_survey_pages_survey1_idx", columns={"survey_id"})})
 * @ORM\Entity
 */
class SurveyPages extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var integer @ORM\Column(name="sequence", type="integer", nullable=true)
     */
    private $sequence;

    /**
     *
     * @var boolean @ORM\Column(name="set_completed", type="boolean", nullable=true)
     */
    private $setCompleted;

    /**
     *
     * @var boolean @ORM\Column(name="must_branch", type="boolean", nullable=true)
     */
    private $mustBranch;

    /**
     *
     * @var string @ORM\Column(name="external_id", type="string", length=45, nullable=true)
     */
    private $externalId;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Survey @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\Survey")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     *      })
     */
    private $survey;

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
     * Set sequence
     *
     * @param integer $sequence            
     * @return SurveyPages
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
        
        return $this;
    }

    /**
     * Get sequence
     *
     * @return integer
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set setCompleted
     *
     * @param boolean $setCompleted            
     * @return SurveyPages
     */
    public function setSetCompleted($setCompleted)
    {
        $this->setCompleted = $setCompleted;
        
        return $this;
    }

    /**
     * Get setCompleted
     *
     * @return boolean
     */
    public function getSetCompleted()
    {
        return $this->setCompleted;
    }

    /**
     * Set mustBranch
     *
     * @param boolean $mustBranch            
     * @return SurveyPages
     */
    public function setMustBranch($mustBranch)
    {
        $this->mustBranch = $mustBranch;
        
        return $this;
    }

    /**
     * Get mustBranch
     *
     * @return boolean
     */
    public function getMustBranch()
    {
        return $this->mustBranch;
    }

    /**
     * Set externalId
     *
     * @param string $externalId            
     * @return SurveyPages
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        
        return $this;
    }

    /**
     * Get externalId
     *
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey            
     * @return SurveyPages
     */
    public function setSurvey(\Synapse\CoreBundle\Entity\Survey $survey = null)
    {
        $this->survey = $survey;
        
        return $this;
    }

    /**
     * Get survey
     *
     * @return \Synapse\CoreBundle\Entity\Survey
     */
    public function getSurvey()
    {
        return $this->survey;
    }
}
