<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * ActivityCategoryLang 
 * @ORM\Table(name="activity_category_lang", indexes={@ORM\Index(name="fk_activity_reference_lang_activity_reference1_idx",columns={"activity_category_id"}),@ORM\Index(name="fk_activity_reference_lang_language_master1_idx",columns={"language_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ActivityCategoryLangRepository")
 * 
 */
class ActivityCategoryLang extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * 
     * @JMS\Expose
     */
    private $id;

   /**
     * @var \Synapse\CoreBundle\Entity\ActivityCategory
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ActivityCategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activity_category_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $activityCategoryId;
    
     /**
     * @var \Synapse\CoreBundle\Entity\LanguageMaster
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $description;

    /**
     * @param \Synapse\CoreBundle\Entity\ActivityCategory $activityCategoryId
     */
    public function setActivityCategoryId($activityCategoryId)
    {
        $this->activityCategoryId = $activityCategoryId;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\ActivityCategory
     */
    public function getActivityCategoryId()
    {
        return $this->activityCategoryId;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
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

    /**
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\LanguageMaster
     */
    public function getLanguage()
    {
        return $this->language;
    }

    
}