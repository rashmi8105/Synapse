<?php
namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;


/**
 * OrgGroupTree
 *
 * @ORM\Table(name="org_group_tree", indexes={@ORM\Index(name="FK_ancestor_group_id_IDX", columns={"ancestor_group_id"}), @ORM\Index(name="FK_descendant_group_id_IDX", columns={"descendant_group_id"}), @ORM\Index(name="IDX_ancestor_descendant", columns={"ancestor_group_id", "descendant_group_id", "deleted_at"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgGroupTreeRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * 
 */
class OrgGroupTree extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", length=11)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *      @JMS\Expose
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\OrgGroup @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgGroup")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ancestor_group_id", referencedColumnName="id", nullable=false)
     *      })
     *      @JMS\Expose
     */
    private $ancestorGroupId;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\OrgGroup @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgGroup")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="descendant_group_id", referencedColumnName="id", nullable=false)
     *      })
     *      @JMS\Expose
     */
    private $descendantGroupId;

    /**
     *
     * @var smallint
     * @ORM\Column(name="path_length", type="smallint", options={"default"=0} ,nullable=false)
     * @JMS\Expose
     */
    private $pathLength;
    
    /**
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Set ancestorGroupId
     *
     * @param \Synapse\CoreBundle\Entity\OrgGroup $ancestorGroupId
     */
    public function setAncestorGroupId(\Synapse\CoreBundle\Entity\OrgGroup $ancestorGroupId = null) {
        $this->ancestorGroupId = $ancestorGroupId;
    }
    
    /**
     *
     * @return \Synapse\CoreBundle\Entity\OrgGroup
     */
    public function getAncestorGroupId() {
        return $this->ancestorGroupId;
    }
    
    /**
     * Set descendantGroupId
     *
     * @param \Synapse\CoreBundle\Entity\OrgGroup $descendantGroupId
     *  
     */
    public function setDescendantGroupId(\Synapse\CoreBundle\Entity\OrgGroup $descendantGroupId = null) {
        $this->descendantGroupId = $descendantGroupId;
    }
    
    /**
     *
     * @return \Synapse\CoreBundle\Entity\OrgGroup
     */
    public function getDescendantGroupId() {
        return $this->descendantGroupId;
    }
    
    
    /**
     *
     * @param smallint $pathLength
     */
    public function setPathLength($pathLength) {
        $this->pathLength = $pathLength;
    }
    
    /**
     *
     * @return smallint
     */
    public function getPathLength() {
        return $this->pathLength;
    }
    
}