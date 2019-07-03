<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
use Symfony\Component\Config\Definition\BooleanNode;

/**
 * ContactTypes
 *
 * @ORM\Table(name="contact_types")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ContactTypesRepository")
 */
class ContactTypes extends BaseEntity
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
     * @var boolean @ORM\Column(name="is_active",type="boolean",nullable=true)
     *     
     *      @JMS\Expose
     */
    private $isActive;

    /**
     *
     * @var integer @ORM\Column(name="display_seq", type="integer", nullable=true)
     *     
     *      @JMS\Expose
     */
    private $displaySeq;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\ContactTypes @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ContactTypes")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="parent_contact_types_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $parentContactTypesId;

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
     * @param \Synapse\CoreBundle\Entity\ContactTypes $parentContactTypesId            
     */
    public function setParentContactTypesId($parentContactTypesId)
    {
        $this->parentContactTypesId = $parentContactTypesId;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\ContactTypes
     */
    public function getParentContactTypesId()
    {
        return $this->parentContactTypesId;
    }

}
