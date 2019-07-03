<?php
namespace Synapse\PdfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * EbiTemplateLang
 *
 * @ORM\Table(name="ebi_template_lang", indexes={@ORM\Index(name="fk_ebi_template_lang_language_master1_idx", columns={"lang_id"}), @ORM\Index(name="fk_ebi_template_key_idx", columns={"ebi_template_key"})})
 * @ORM\Entity(repositoryClass="Synapse\PdfBundle\Repository\EbiTemplateLangRepository")
 */
class EbiTemplateLang
{

    /**
     *
     * @var \Synapse\PdfBundle\Entity\EbiTemplate @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="Synapse\PdfBundle\Entity\EbiTemplate")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_template_key", referencedColumnName="key")
     *      })
     */
    private $ebiTemplateKey;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\LanguageMaster @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     *      })
     */
    private $langId;

    /**
     *
     * @var string @ORM\Column(name="description", type="string", length=100, nullable=true)
     */
    private $description;

    /**
     *
     * @var string @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     *
     * @param string $body            
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     *
     * @param string $description            
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     *
     * @param \Synapse\PdfBundle\Entity\EbiTemplate $ebiTemplateKey            
     */
    public function setEbiTemplateKey(\Synapse\PdfBundle\Entity\EbiTemplate $ebiTemplateKey)
    {
        $this->ebiTemplateKey = $ebiTemplateKey;
    }

    /**
     *
     * @return \Synapse\PdfBundle\Entity\EbiTemplate
     */
    public function getEbiTemplateKey()
    {
        return $this->ebiTemplateKey;
    }

    /**
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $langId            
     */
    public function setLangId(\Synapse\CoreBundle\Entity\LanguageMaster $langId)
    {
        $this->langId = $langId;
    }

    /**
     *
     * @return \EbiTemplateLang
     */
    public function getLangId()
    {
        return $this->langId;
    }
}