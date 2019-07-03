<?php
namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * EbiSearchLang
 *
 * @ORM\Table(name="ebi_search_lang")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EbiSearchLangRepository")
 * @ORM\Table(name="ebi_search_lang", indexes={@ORM\Index(name="fk_ebi_search_lang_ebi_search1_idx", columns={"ebi_search_id"}), @ORM\Index(name="fk_ebi_search_lang_language_master1_idx", columns={"language_id"})})
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EbiSearchLang extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *     
     *     
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\EbiSearch @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\EbiSearch")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_search_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $ebiSearch;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\LanguageMaster @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $lang;

    /**
     *
     * @var string @Assert\Length(max="2000")
     *      @ORM\Column(name="description", type="string", nullable=true)
     *     
     *      @JMS\Expose
     */
    private $description;
    
    
    /**
     *
     * @var string @Assert\Length(max="1000")
     *      @ORM\Column(name="sub_category_name", type="string", nullable=true)
     *
     *      @JMS\Expose
     */
    private $subCategoryName;

    /**
     * Set lang
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $lang            
     * @return EbiSearchLang
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
     * Set ebiSearch
     *
     * @param \Synapse\CoreBundle\Entity\EbiSearch $ebiSearch            
     * @return ebiSearchLang
     */
    public function setEbiSearch(\Synapse\CoreBundle\Entity\EbiSearch $ebiSearch = null)
    {
        $this->ebiSearch = $ebiSearch;
        
        return $this;
    }

    /**
     * Get ebiSearchLang
     *
     * @return \Synapse\CoreBundle\Entity\EbiSearch
     */
    public function getEbiSearch()
    {
        return $this->ebiSearch;
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
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}