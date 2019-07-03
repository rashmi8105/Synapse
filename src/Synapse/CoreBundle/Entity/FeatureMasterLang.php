<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * FeatureMasterLang
 *
 * @ORM\Table(name="feature_master_lang", indexes={@ORM\Index(name="fk_feature_master_lang_feature_master1_idx", columns={"feature_master_id"}), @ORM\Index(name="fk_feature_master_lang_language_master1_idx", columns={"lang_id"})})
 * @ORM\Entity (repositoryClass="Synapse\CoreBundle\Repository\FeatureMasterLangRepository")
 */
class FeatureMasterLang extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\FeatureMaster @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\FeatureMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="feature_master_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $featureMaster;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\LanguageMaster @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="lang_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $lang;

    /**
     *
     * @var string @ORM\Column(name="feature_name", type="string", length=100, precision=0, scale=0, nullable=true, unique=false)
     */
    private $featureName;

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
     * Set featureMaster
     *
     * @param \Synapse\CoreBundle\Entity\FeatureMaster $featureMaster            
     * @return FeatureMasterLang
     */
    public function setFeatureMaster(\Synapse\CoreBundle\Entity\FeatureMaster $featureMaster = null)
    {
        $this->featureMaster = $featureMaster;
        
        return $this;
    }

    /**
     * Get featureMaster
     *
     * @return \Synapse\CoreBundle\Entity\FeatureMaster
     */
    public function getFeatureMaster()
    {
        return $this->featureMaster;
    }

    /**
     * Set featureName
     *
     * @param string $featureName            
     * @return DatablockMasterLang
     */
    public function setFeatureName($featureName)
    {
        $this->featureName = $featureName;
        
        return $this;
    }

    /**
     * Get featureName
     *
     * @return string
     */
    public function getFeatureName()
    {
        return $this->featureName;
    }

    /**
     * Set lang
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $lang            
     * @return DatablockMasterLang
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
}
