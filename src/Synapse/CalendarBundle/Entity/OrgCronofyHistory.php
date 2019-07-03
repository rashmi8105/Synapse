<?php
namespace Synapse\CalendarBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;

/**
 * OrgCronofyHistory
 *
 * @ORM\Table(name="org_cronofy_history")
 * @ORM\Entity(repositoryClass="Synapse\CalendarBundle\Repository\OrgCronofyHistoryRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 */
class OrgCronofyHistory extends BaseEntity
{
    /**
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $organization;

    /**
     * @var \Synapse\CoreBundle\Entity\Person
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $person;

    /**
     * @var string
     * @ORM\Column(name="reason", type="string", length=100, nullable=false)
     * @JMS\Expose
     */
    private $reason;

    /**
     * @var string @ORM\Column(name="cronofy_profile_name", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $cronofyProfile;

    /**
     * @var string @ORM\Column(name="cronofy_provider_name", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $cronofyProvider;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

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
     * Set organization
     *
     * @param Organization $organization
     *
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set person
     *
     * @param Person $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * @return string
     */
    public function getCronofyProfile()
    {
        return $this->cronofyProfile;
    }

    /**
     * @param string $cronofyProfile
     */
    public function setCronofyProfile($cronofyProfile)
    {
        $this->cronofyProfile = $cronofyProfile;
    }

    /**
     * @return string
     */
    public function getCronofyProvider()
    {
        return $this->cronofyProvider;
    }

    /**
     * @param string $cronofyProvider
     */
    public function setCronofyProvider($cronofyProvider)
    {
        $this->cronofyProvider = $cronofyProvider;
    }
}