<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OrgPersonStudent
 *
 * @ORM\Table(name="org_person_student",indexes={@ORM\Index(name="fk_org_person_student_organization1", columns={"organization_id"}), @ORM\Index(name="fk_org_person_student_person1", columns={"person_id"})}))
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgPersonStudentRepository")
 * @ORM\EntityListeners({ "Synapse\CoreBundle\Listener\OrgPersonStudentListener" })
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgPersonStudent extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *      @JMS\Expose
     *
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
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
     * @var string @ORM\Column(name="status", type="string", length=1, options={"default": "1"})
     *
     */
    private $status;

    /**
     *
     * @var string @ORM\Column(name="photo_url", type="string", length=200, nullable=true)
     *
     */
    private $photoUrl;

    /**
     *
     * @var string @ORM\Column(name="auth_key", type="string", length=200, nullable=true)
     *
     */
    private $authKey;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_primary_connect", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $personIdPrimaryConnect;

    /**
     *
     * @var string @ORM\Column(name="is_home_campus", type="string", length=1, nullable=true)
     *      @JMS\Expose
     */
    private $isHomeCampus;

	/**
     *
     * @var string @ORM\Column(name="is_privacy_policy_accepted", type="string", columnDefinition="enum('y','n')", options={"default":"y"}, precision=0, scale=0, nullable=false, unique=false)
     */
    private $isPrivacyPolicyAccepted;

	/**
     *
     * @var \DateTime
     * @ORM\Column(name="privacy_policy_accepted_date", type="datetime", nullable=true)
     */
    private $privacyPolicyAcceptedDate;

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
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return Organization
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
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
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person
     * @return Person
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param string $photoUrl
     */
    public function setPhotoUrl($photoUrl)
    {
        $this->photoUrl = $photoUrl;
    }

    /**
     *
     * @return string
     */
    public function getPhotoUrl()
    {
        return $this->photoUrl;
    }

    /**
     * Sets the value of authKey.
     *
     * @param string
     *
     * @return self
     */
    public function setAuthKey($authKey)
    {
        $this->authKey = $authKey;

        return $this;
    }

    /**
     * Gets the value of authKey.
     *
     * @return string
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * Set personIdPrimaryConnect
     *
     * @param \Synapse\CoreBundle\Entity\Person $person
     * @return Person
     */
    public function setPersonIdPrimaryConnect(\Synapse\CoreBundle\Entity\Person $personIdPrimaryConnect = null)
    {
        $this->personIdPrimaryConnect = $personIdPrimaryConnect;

        return $this;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdPrimaryConnect()
    {
        return $this->personIdPrimaryConnect;
    }

    /**
     *
     * @param string $isHomeCampus
     */
    public function setIsHomeCampus($isHomeCampus)
    {
        $this->isHomeCampus = $isHomeCampus;
    }

    /**
     *
     * @return string
     */
    public function getIsHomeCampus()
    {
        return $this->isHomeCampus;
    }

	/**
     * Set isPrivacyPolicyAccepted
     *
     * @param string $isPrivacyPolicyAccepted
     */
    public function setIsPrivacyPolicyAccepted($isPrivacyPolicyAccepted)
    {
        $this->isPrivacyPolicyAccepted = $isPrivacyPolicyAccepted;

        return $this;
    }

    /**
     * Get isPrivacyPolicyAccepted
     *
     * @return string
     */
    public function getIsPrivacyPolicyAccepted()
    {
        return $this->isPrivacyPolicyAccepted;
    }

	/**
     * Set privacyPolicyAcceptedDate
     *
     * @param \DateTime $privacyPolicyAcceptedDate
     * @return privacyPolicyAcceptedDate
     */
    public function setPrivacyPolicyAcceptedDate($privacyPolicyAcceptedDate)
    {
        $this->privacyPolicyAcceptedDate = $privacyPolicyAcceptedDate;
        return $this;
    }

    /**
     * Get privacyPolicyAcceptedDate
     *
     * @return \DateTime
     */
    public function getPrivacyPolicyAcceptedDate()
    {
        return $this->privacyPolicyAcceptedDate;
    }
}