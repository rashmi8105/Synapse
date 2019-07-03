<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * ActivityCategory
 * @ORM\Table(name="activity_category", indexes={@ORM\Index(name="fk_activity_category_activity_category1_idx",columns={"parent_activity_category_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ActivityCategoryRepository")
 */
class ActivityCategory extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *     
     *      @JMS\Expose
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="short_name", type="string", length=45, nullable=true)
     *      @JMS\Expose
     */
    private $shortName;

    /**
     *
     * @var boolean @ORM\Column(name="is_active",type="boolean",length=1,nullable=true)
     *      @JMS\Expose
     */
    private $isActive;

    /**
     *
     * @var integer @ORM\Column(name="display_seq", type="integer",nullable=true)
     *      @JMS\Expose
     */
    private $displaySeq;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\ActivityCategory @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ActivityCategory")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="parent_activity_category_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $parentActivityCategoryId;

    /**
     *
     * @param int $displaySeq            
     */
    public function setDisplaySeq($displaySeq)
    {
        $this->displaySeq = $displaySeq;
    }

    /**
     *
     * @return int
     */
    public function getDisplaySeq()
    {
        return $this->displaySeq;
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
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param boolean $isActive            
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @param string $shortName            
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
    }

    /**
     *
     * @param \Synapse\CoreBundle\Entity\ActivityCategory $parentActivityCategoryId            
     */
    public function setParentActivityCategoryId($parentActivityCategoryId)
    {
        $this->parentActivityCategoryId = $parentActivityCategoryId;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\ActivityCategory
     */
    public function getParentActivityCategoryId()
    {
        return $this->parentActivityCategoryId;
    }

    /**
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    
}