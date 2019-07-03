<?php
namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Factor Lang
 *
 * @ORM\Table(name="factor_lang", indexes={@ORM\Index(name="fk_factors_lang_factors1_idx", columns={"factor_id"})})
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\FactorLangRepository")
 * @UniqueEntity(fields={"name"},message="Factor name already exists.")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class FactorLang extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Factor @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="Synapse\SurveyBundle\Entity\Factor")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="factor_id", referencedColumnName="id")
     *      })
     */
    private $factor;

    /**
     *
     * @var \LanguageMaster @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="\Synapse\CoreBundle\Entity\LanguageMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     *      })
     */
    private $languageMaster;

    /**
     *
     * @var string @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

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
     * Set Factor
     *
     * @param
     *            \Synapse\SurveyBundle\Entity\Factor
     * @return FactorLang
     */
    public function setFactor(\Synapse\SurveyBundle\Entity\Factor $factor = null)
    {
        $this->factor = $factor;
        
        return $this;
    }

    /**
     * Get Factor
     *
     * @return \Synapse\SurveyBundle\Entity\Factor
     */
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * Set LanguageMaster
     *
     * @param
     *            \Synapse\CoreBundle\Entity\LanguageMaster
     * @return FactorLang
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
     * @return FactorLang
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
}

