<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ReferralRoutingRules
 *
 * @ORM\Table(name="referral_routing_rules", indexes={@ORM\Index(name="fk_referral_routing_rules_activity_category_id_idx", columns={"activity_category_id"}), @ORM\Index(name="fk_referral_routing_rules_organization_id_idx", columns={"organization_id"}), @ORM\Index(name="fk_referral_routing_rules_person_id_idx", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ReferralRoutingRulesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ReferralRoutingRules extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *      @JMS\Expose
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\ActivityCategory @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ActivityCategory")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="activity_category_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $activityCategory;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     *      })
     */
    private $organization;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $person;

    /**
     *
     * @var boolean @ORM\Column(name="is_primary_coordinator", type="boolean", nullable=true)
     *      @JMS\Expose
     */
    private $isPrimaryCoordinator;
    
    /**
     *
     * @var boolean @ORM\Column(name="is_primary_campus_connection", type="boolean", nullable=true)
     *      @JMS\Expose
     */
    private $isPrimaryCampusConnection;
    
    /**
     *
     * @param \Synapse\CoreBundle\Entity\ActivityCategory $activityCategory            
     */
    public function setActivityCategory(\Synapse\CoreBundle\Entity\ActivityCategory $activityCategory = null)
    {
        $this->activityCategory = $activityCategory;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\ActivityCategory
     */
    public function getActivityCategory()
    {
        return $this->activityCategory;
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
     * @param boolean $isPrimaryCoordinator            
     */
    public function setIsPrimaryCoordinator($isPrimaryCoordinator)
    {
        $this->isPrimaryCoordinator = $isPrimaryCoordinator;
    }

    /**
     *
     * @return boolean
     */
    public function getIsPrimaryCoordinator()
    {
        return $this->isPrimaryCoordinator;
    }
    
    /**
     *
     * @param boolean $isPrimaryCampusConnection
     */
    public function setIsPrimaryCampusConnection($isPrimaryCampusConnection)
    {
        $this->isPrimaryCampusConnection = $isPrimaryCampusConnection;
    }
    
    /**
     *
     * @return boolean
     */
    public function getIsPrimaryCampusConnection()
    {
        return $this->isPrimaryCampusConnection;
    }

    /**
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
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
     * @param \Synapse\CoreBundle\Entity\Person $person            
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }
}