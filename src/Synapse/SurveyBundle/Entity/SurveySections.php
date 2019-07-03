<?php
namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * SurveySections
 *
 * @ORM\Table(name="survey_sections", indexes={@ORM\Index(name="fk_survey_sections_survey1_idx", columns={"survey_id"}), @ORM\Index(name="fk_survey_sections_survey_pages1_idx", columns={"survey_pages_id"})})
 * @ORM\Entity
 */
class SurveySections extends BaseEntity
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
     * \Synapse\SurveyBundle\Entity\SurveyPages
     *
     * @ORM\ManyToOne(targetEntity="\Synapse\SurveyBundle\Entity\SurveyPages")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="survey_pages_id", referencedColumnName="id")
     * })
     */
    private $surveyPages;

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
     * @return SurveySections
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
     * Set externalId
     *
     * @param string $externalId            
     * @return SurveySections
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
     * @return SurveySections
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

    /**
     * Set surveyPages
     *
     * @param \Synapse\SurveyBundle\Entity\SurveyPages $surveyPages            
     * @return SurveySections
     */
    public function setSurveyPages(\Synapse\SurveyBundle\Entity\SurveyPages $surveyPages = null)
    {
        $this->surveyPages = $surveyPages;
        
        return $this;
    }

    /**
     * Get surveyPages
     *
     * @return \Synapse\SurveyBundle\Entity\SurveyPages
     */
    public function getSurveyPages()
    {
        return $this->surveyPages;
    }
}
