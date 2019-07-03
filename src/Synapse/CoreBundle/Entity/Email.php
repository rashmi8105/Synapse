<?php
namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\OwnableAssetEntityInterface;

/**
 * Email 
 * @ORM\Table(name="email", indexes={@ORM\Index(name="fk_email_organization1",columns={"organization_id"}),@ORM\Index(name="fk_email_person1",columns={"person_id_student"}),@ORM\Index(name="fk_email_person2",columns={"person_id_faculty"}),@ORM\Index(name="fk_email_activity_category1",columns={"activity_category_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EmailRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * 
 */
class Email extends BaseEntity
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
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $organization;
    
     /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id_student", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $personIdStudent;
    
    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id_faculty", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $personIdFaculty;
    
    /**
     * @var \Synapse\CoreBundle\Entity\ActivityCategory
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ActivityCategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activity_category_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $activityCategory;
    
    /**
     * @var text
     *
     * @ORM\Column(name="email_subject", type="string", length=50, nullable=true)
     * @JMS\Expose
     */
    private $emailSubject;
    
    /**
     * @var text
     *
     * @ORM\Column(name="email_body", type="text", nullable=true)
     * @JMS\Expose
     */
    private $emailBody;
    
    /**
     * @var text
     *
     * @ORM\Column(name="email_bcc_list", type="text", nullable=true, options={"comment":"BCC faculty list in comma separated format"})
     * 
     * @JMS\Expose
     */
    private $emailBccList;
         
    /**
     * @var boolean
     *
     * @ORM\Column(name="access_private", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $accessPrivate;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="access_public", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $accessPublic;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="access_team", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $accessTeam;

    /**
     * @param boolean $accessPrivate
     */
    public function setAccessPrivate($accessPrivate)
    {
        $this->accessPrivate = $accessPrivate;
    }

    /**
     * @return boolean
     */
    public function getAccessPrivate()
    {
        return $this->accessPrivate;
    }

    /**
     * @param boolean $accessPublic
     */
    public function setAccessPublic($accessPublic)
    {
        $this->accessPublic = $accessPublic;
    }

    /**
     * @return boolean
     */
    public function getAccessPublic()
    {
        return $this->accessPublic;
    }

    /**
     * @param boolean $accessTeam
     */
    public function setAccessTeam($accessTeam)
    {
        $this->accessTeam = $accessTeam;
    }

    /**
     * @return boolean
     */
    public function getAccessTeam()
    {
        return $this->accessTeam;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\ActivityCategory $activityCategory
     */
    public function setActivityCategory($activityCategory)
    {
        $this->activityCategory = $activityCategory;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\ActivityCategory
     */
    public function getActivityCategory()
    {
        return $this->activityCategory;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\text $emailBccList
     */
    public function setEmailBccList($emailBccList)
    {
        $this->emailBccList = $emailBccList;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\text
     */
    public function getEmailBccList()
    {
        return $this->emailBccList;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\text $emailBody
     */
    public function setEmailBody($emailBody)
    {
        $this->emailBody = $emailBody;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\text
     */
    public function getEmailBody()
    {
        return $this->emailBody;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\text $emailSubject
     */
    public function setEmailSubject($emailSubject)
    {
        $this->emailSubject = $emailSubject;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\text
     */
    public function getEmailSubject()
    {
        return $this->emailSubject;
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
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\Person $personIdFaculty
     */
    public function setPersonIdFaculty($personIdFaculty)
    {
        $this->personIdFaculty = $personIdFaculty;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdFaculty()
    {
        return $this->personIdFaculty;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\Person $personIdStudent
     */
    public function setPersonIdStudent($personIdStudent)
    {
        $this->personIdStudent = $personIdStudent;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdStudent()
    {
        return $this->personIdStudent;
    }

}
