<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * TalkingPointsLang
 *
 * @ORM\Table(name="talking_points_lang", indexes={@ORM\Index(name="fk_talking_points_lang_talking_points1_idx", columns={"talking_points_id"}), @ORM\Index(name="fk_talking_points_lang_language_master1_idx", columns={"language_master_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\TalkingPointsLangRepository")
 */
class TalkingPointsLang extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *     
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\TalkingPoints @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\TalkingPoints")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="talking_points_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $talkingPoints;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\LanguageMaster @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="language_master_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $languageMaster;

    /**
     *
     * @var string @ORM\Column(name="title", type="string", length=400, nullable=true)
     *      @JMS\Expose
     */
    private $title;

    /**
     *
     * @var string @ORM\Column(name="description", type="string", length=5000, nullable=true)
     *      @JMS\Expose
     */
    private $description;

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set talkingPoints
     *
     * @param \Synapse\CoreBundle\Entity\TalkingPoints $talkingPoints            
     * @return TalkingPointsLang
     */
    public function setTalkingPoints(\Synapse\CoreBundle\Entity\TalkingPoints $talkingPoints = null)
    {
        $this->talkingPoints = $talkingPoints;
        
        return $this;
    }

    /**
     * Get talkingPoints
     *
     * @return \Synapse\CoreBundle\Entity\TalkingPoints
     */
    public function getTalkingPoints()
    {
        return $this->talkingPoints;
    }

    /**
     * Set languageMaster
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $languageMaster            
     * @return TalkingPointsLang
     */
    public function setLanguageMaster(\Synapse\CoreBundle\Entity\LanguageMaster $languageMaster = null)
    {
        $this->languageMaster = $languageMaster;
        
        return $this;
    }

    /**
     * Get languageMaster
     *
     * @return \Synapse\CoreBundle\Entity\LanguageMaster
     */
    public function getLanguageMaster()
    {
        return $this->languageMaster;
    }

    /**
     * Set title
     * 
     * @param string $title            
     */
    public function setTitle($title)
    {
        $this->title = $title;
        
        return $this;
    }

    /**
     * Get title
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     * 
     * @param string $description            
     */
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }
    /**
     * Get description     
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
 
 }