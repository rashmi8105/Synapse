<?php
namespace Synapse\CampusResourceBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgCampusResource
 *
 * @ORM\Table(name="org_campus_resource", indexes={@ORM\Index(name="fk_campus_resource_organization1_idx", columns={"org_id"}), @ORM\Index(name="fk_campus_resource_person1_idx", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CampusResourceBundle\Repository\OrgCampusResourceRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgCampusResource extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", nullable=false, referencedColumnName="id")
     *      })
     */
    private $orgId;

    /**
     *
     * @var string @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     *
     * @var \Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", nullable=false, referencedColumnName="id")
     *      })
     */
    private $personId;

    /**
     *
     * @var string @ORM\Column(name="phone", type="string", length=45, nullable=false)
     */
    private $phone;

    /**
     *
     * @var string @ORM\Column(name="email", type="string", length=120, nullable=false)
     */
    private $email;

    /**
     *
     * @var string @ORM\Column(name="location", type="string", length=45, nullable=true)
     */
    private $location;

    /**
     *
     * @var string @ORM\Column(name="url", type="string", length=500, nullable=true)
     */
    private $url;

    /**
     *
     * @var string @ORM\Column(name="description", type="string", length=300, nullable=true)
     */
    private $description;

    /**
     *
     * @var enum @ORM\Column(name="visible_to_student", type="string", columnDefinition="enum('1', '0')", options={"default='1'"})
     */
    private $visibleToStudent;

    /**
     *
     * @var enum @ORM\Column(name="receive_referals", type="string", columnDefinition="enum('1', '0')", options={"default='1'"})
     */
    private $receiveReferals;

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
     * Set orgId
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OrgCampusResource
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization)
    {
        $this->orgId = $organization;
        
        return $this;
    }

    /**
     * Get orgId
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->orgId;
    }

    /**
     * Set name
     *
     * @param string $name            
     * @return OrgCampusResource
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set personId
     *
     * @param \Synapse\CoreBundle\Entity\Person $personId            
     * @return Person
     */
    public function setPersonId(\Synapse\CoreBundle\Entity\Person $personId)
    {
        $this->personId = $personId;
        
        return $this;
    }

    /**
     * Get personId
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Set phone
     *
     * @param string $phone            
     * @return OrgCampusResource
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        
        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email            
     * @return OrgCampusResource
     */
    public function setEmail($email)
    {
        $this->email = $email;
        
        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set location
     *
     * @param string $location            
     * @return OrgCampusResource
     */
    public function setLocation($location)
    {
        $this->location = $location;
        
        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set url
     *
     * @param string $url            
     * @return OrgCampusResource
     */
    public function setUrl($url)
    {
        $this->url = $url;
        
        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set description
     *
     * @param string $description            
     * @return OrgDocuments
     */
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set visibleToStudent
     *
     * @param string $visibleToStudent            
     * @return OrgDocuments
     */
    public function setVisibleToStudent($visibleToStudent)
    {
        $this->visibleToStudent = $visibleToStudent;
        
        return $this;
    }

    /**
     * Get visibleToStudent
     *
     * @return string
     */
    public function getVisibleToStudent()
    {
        return $this->visibleToStudent;
    }

    /**
     * Set receiveReferals
     *
     * @param string $receiveReferals            
     * @return OrgDocuments
     */
    public function setReceiveReferals($receiveReferals)
    {
        $this->receiveReferals = $receiveReferals;
        
        return $this;
    }

    /**
     * Get receiveReferals
     *
     * @return string
     */
    public function getReceiveReferals()
    {
        return $this->receiveReferals;
    }
}