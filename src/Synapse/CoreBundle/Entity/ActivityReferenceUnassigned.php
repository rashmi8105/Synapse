<?php

namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * ActivityReferenceUnassigned
 *
 * @ORM\Table(name="activity_reference_unassigned", indexes={@ORM\Index(name="fk_activity_reference_unassigned_activity_reference1", columns={"activity_reference_id"}), @ORM\Index(name="fk_activity_reference_unassigned_organization1", columns={"organization_id"}), @ORM\Index(name="fk_activity_reference_unassigned_person1", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ActivityReferenceUnassignedRepository")
 */
class ActivityReferenceUnassigned
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $person;

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $organization;

    /**
     * @var \Synapse\CoreBundle\Entity\ActivityReference
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ActivityReference")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activity_reference_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $activityReference;
    
     /**
     * @var boolean
     *
     * @ORM\Column(name="is_primary_coordinator", type="boolean", nullable=true)
     * @JMS\Expose
     */ 
    private  $isPrimaryCoordinator;

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
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person
     * @return ActivityReferenceUnassigned
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \Synapse\CoreBundle\Entity\Person 
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return ActivityReferenceUnassigned
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \Synapse\CoreBundle\Entity\Organization 
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set activityReference
     *
     * @param \Synapse\CoreBundle\Entity\ActivityReference $activityReference
     * @return ActivityReferenceUnassigned
     */
    public function setActivityReference(\Synapse\CoreBundle\Entity\ActivityReference $activityReference = null)
    {
        $this->activityReference = $activityReference;

        return $this;
    }

    /**
     * Get activityReference
     *
     * @return \Synapse\CoreBundle\Entity\ActivityReference 
     */
    public function getActivityReference()
    {
        return $this->activityReference;
    }

    /**
     * @param boolean $isPrimaryCoordinator
     */
    public function setIsPrimaryCoordinator($isPrimaryCoordinator)
    {
        $this->isPrimaryCoordinator = $isPrimaryCoordinator;
    }

    /**
     * @return boolean
     */
    public function getIsPrimaryCoordinator()
    {
        return $this->isPrimaryCoordinator;
    }


}
