<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * SurveyLang
 *
 * @ORM\Table(name="survey_lang", indexes={@ORM\Index(name="fk_survey_lang_survey1_idx", columns={"survey_id"}), @ORM\Index(name="fk_survey_lang_language_master1_idx", columns={"lang_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\SurveyLangRepository")
 */
class SurveyLang extends BaseEntity
{

    /**
     *
     * @var \Survey @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="Synapse\CoreBundle\Entity\Survey")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     *      })
     */
    private $survey;

    /**
     *
     * @var \LanguageMaster @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="\Synapse\CoreBundle\Entity\LanguageMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     *      })
     */
    private $languageMaster;

    /**
     *
     * @var string @ORM\Column(name="name", type="string", length=200, nullable=true)
     */
    private $name;

    /**
     * Set survey
     *
     * @param
     *            \Synapse\CoreBundle\Entity\Survey
     * @return SurveyLang
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
     * Set LanguageMaster
     *
     * @param
     *            \Synapse\CoreBundle\Entity\LanguageMaster
     * @return SurveyLang
     */
    public function setLanguageMaster(\Synapse\CoreBundle\Entity\LanguageMaster $languageMaster = null)
    {
        $this->languageMaster = $languageMaster;
        
        return $this;
    }

    /**
     * Get LanguageMaster
     *
     * @return \Synapse\CoreBundle\Entity\LanguageMaster
     */
    public function getLanguageMaster()
    {
        return $this->languageMaster;
    }

    /**
     * Set name
     *
     * @param string $name            
     * @return SurveyLang
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

    public function getId()
    {
        return $this;
    }
}
