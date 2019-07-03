<?php

namespace Synapse\RiskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * RiskGroupLang
 *
 * @ORM\Table(name="risk_group_lang", indexes={@ORM\Index(name="fk_risk_group_lang_risk_group1_idx", columns={"risk_group_id"}), @ORM\Index(name="fk_risk_group_lang_language_master1_idx", columns={"lang_id"})})
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\RiskGroupLangRepository")
 */
class RiskGroupLang
{
    

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=true)
     * @Assert\Length(max=50,maxMessage = "Risk Group Name cannot be longer than {{ limit }} characters");
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=2000, nullable=true)
     * @Assert\Length(max=250,maxMessage = "Risk Group Description cannot be longer than {{ limit }} characters");
     */
    private $description;

    /**
     * @var \LanguageMaster
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     * })
     */
    private $lang;

    /**
     * @var \RiskGroup
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Synapse\RiskBundle\Entity\RiskGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="risk_group_id", referencedColumnName="id")
     * })
     */
    private $riskGroup;
    
    /**
     * Set name
     *
     * @param string $name
     * @return RiskGroupLang
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
     * Set description
     *
     * @param string $description
     * @return RiskGroupLang
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
    
    /**
     * Set lang
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $lang
     * @return RiskGroupLang
     */
    public function setLang(\Synapse\CoreBundle\Entity\LanguageMaster $lang)
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
     * Set riskGroup
     *
     * @param \Synapse\RiskBundle\Entity\RiskGroup $riskGroup
     * @return RiskGroupLang
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


}
