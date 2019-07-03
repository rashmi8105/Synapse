<?php
namespace Synapse\SurveyBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SurveySectionsLang
 *
 * @ORM\Table(name="survey_sections_lang", indexes={@ORM\Index(name="fk_survey_sections_lang_survey_sections1_idx", columns={"survey_sections_id"}), @ORM\Index(name="fk_survey_sections_lang_language_master1_idx", columns={"lang_id"})})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Entity(repositoryClass="")
 */
class SurveySectionsLang extends BaseEntity
{

    /**
     *
     * @var \LanguageMaster @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="\Synapse\SurveyBundle\Entity\SurveySections")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_sections_id", referencedColumnName="id")
     *      })
     */
    private $surveySections;

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
     * @var string @ORM\Column(name="description_hdr", type="string", length=2000, nullable=true)
     */
    private $descriptionHdr;

    /**
     *
     * @var string @ORM\Column(name="description_dtl", type="string", length=2000, nullable=true)
     */
    private $descriptionDtl;

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
     * Set surveySections
     *
     * @param
     *            \Synapse\SurveyBundle\Entity\SurveySections
     * @return SurveySectionsLang
     */
    public function setSurveySections(\Synapse\SurveyBundle\Entity\SurveySections $surveySections = null)
    {
        $this->surveySections = $surveySections;
        
        return $this;
    }

    /**
     * Get surveySections
     *
     * @return \Synapse\SurveyBundle\Entity\SurveySections
     */
    public function getSurveySections()
    {
        return $this->surveySections;
    }

    /**
     * Set descriptionHdr
     *
     * @param string $descriptionHdr            
     * @return SurveySectionsLang
     */
    public function setDescriptionHdr($descriptionHdr)
    {
        $this->descriptionHdr = $descriptionHdr;
        
        return $this;
    }

    /**
     * Get descriptionHdr
     *
     * @return string
     */
    public function getDescriptionHdr()
    {
        return $this->descriptionHdr;
    }

    /**
     * Set descriptionDtl
     *
     * @param string $descriptionDtl            
     * @return SurveySectionsLang
     */
    public function setDescriptionDtl($descriptionDtl)
    {
        $this->descriptionDtl = $descriptionDtl;
        
        return $this;
    }

    /**
     * Get descriptionDtl
     *
     * @return string
     */
    public function getDescriptionDtl()
    {
        return $this->descriptionDtl;
    }

    public function getId()
    {
        return $this->id;
    }
}