<?php
namespace Synapse\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * CalendarSharing
 * @ORM\Table(name="calendar_sharing", indexes={@ORM\Index(name="fk_calendar_sharing_person1_idx",columns={"person_id_sharedby"}),@ORM\Index(name="fk_calendar_sharing_person2_idx",columns={"person_id_sharedto"}),@ORM\Index(name="fk_calendar_sharing_organization1_idx",columns={"organization_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\CalendarSharingRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class CalendarSharing extends BaseEntity
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
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     *      })
     *      @Assert\NotBlank()
     */
    private $organization;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_sharedby", referencedColumnName="id")
     *      })
     *      @Assert\NotBlank()
     */
    private $personIdSharedby;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_sharedto", referencedColumnName="id")
     *      })
     *      @Assert\NotBlank()
     */
    private $personIdSharedto;

    /**
     *
     * @var datetime @ORM\Column(name="shared_on", type="datetime", nullable=true)
     *      @JMS\Expose
     */
    private $sharedOn;

    /**
     *
     * @var boolean @ORM\Column(name="is_selected", type="boolean", nullable=true)
     *      @JMS\Expose
     */
    private $isSelected;

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
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     *
     * @param \Synapse\CoreBundle\Entity\Person $personIdSharedby            
     */
    public function setPersonIdSharedby($personIdSharedby)
    {
        $this->personIdSharedby = $personIdSharedby;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdSharedby()
    {
        return $this->personIdSharedby;
    }

    /**
     *
     * @param \Synapse\CoreBundle\Entity\Person $personIdSharedto            
     */
    public function setPersonIdSharedto($personIdSharedto)
    {
        $this->personIdSharedto = $personIdSharedto;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdSharedto()
    {
        return $this->personIdSharedto;
    }

    /**
     *
     * @param
     *            $sharedOn
     */
    public function setSharedOn($sharedOn)
    {
        $this->sharedOn = $sharedOn;
    }

    /**
     *
     * @return datetime
     */
    public function getSharedOn()
    {
        return $this->sharedOn;
    }

    /**
     *
     * @param boolean $isSelected            
     */
    public function setIsSelected($isSelected)
    {
        $this->isSelected = $isSelected;
    }

    /**
     *
     * @return boolean
     */
    public function getIsSelected()
    {
        return $this->isSelected;
    }
}