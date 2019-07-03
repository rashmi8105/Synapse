<?php
namespace Synapse\SurveyBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SurveyPagesLang
 *
 * @ORM\Table(name="survey_pages_lang", indexes={@ORM\Index(name="fk_survey_pages_lang_survey_pages1_idx", columns={"survey_pages_id"}), @ORM\Index(name="fk_survey_pages_lang_language_master1_idx", columns={"lang_id"})})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Entity(repositoryClass="")
 */
class SurveyPagesLang extends BaseEntity
{

    /**
     *
     * @var \LanguageMaster @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="\Synapse\SurveyBundle\Entity\SurveyPages")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_pages_id", referencedColumnName="id")
     *      })
     */
    private $surveyPages;

    /**
     *
     * @var \LanguageMaster @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="\Synapse\CoreBundle\Entity\LanguageMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     *      })
     */
    private $lang;

    /**
     *
     * @var string @ORM\Column(name="description", type="string", length=200, nullable=true)
     */
    private $description;

    /**
     * Set Lang
     *
     * @param
     *            \Synapse\CoreBundle\Entity\LanguageMaster
     * @return FactorLang
     */
    public function setLang(\Synapse\CoreBundle\Entity\LanguageMaster $lang = null)
    {
        $this->lang = $lang;
        
        return $this;
    }

    /**
     * Get lang
     *
     * @return \Synapse\CoreBundle\Entity\LanguageMaster
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set SurveyPages
     *
     * @param
     *            \Synapse\SurveyBundle\Entity\SurveyPages
     * @return FactorLang
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

    /**
     * Set description
     *
     * @param string $description            
     * @return SurveyPagesLang
     */
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function getId()
    {
        return $this->id;
    }
}