<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\RiskBundle\Entity\RiskLevels;
use Synapse\SearchBundle\Entity\IntentToLeave;

/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\PersonRepository")
 * @ORM\Table(name="person", indexes={@ORM\Index(name="fk_person_organization1",columns={"organization_id"}),@ORM\Index(name="fk_person_risk_level1_idx",columns={"risk_level"}), @ORM\Index(name="fk_person_intent_to_leave1_idx",columns={"intent_to_leave"}), @ORM\Index(name="username_idx",columns={"username"}), @ORM\Index(name="deleted_at_idx",columns={"deleted_at"}), @ORM\Index(name="activate_token_idx",columns={"activation_token"}) })
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @UniqueEntity(fields={"username"}, groups={"required"}, message="Primary Email is already in use.")
 * @UniqueEntity(fields={"organization", "externalId"}, groups={"required"}, message="External ID is already in use at this organization.")
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Person extends BaseEntity implements UserInterface
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *
     *
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="external_id", type="string", nullable=true)
     *
     * @JMS\Expose
     * @Assert\Length(max=45, maxMessage = "External Id cannot be longer than {{ limit }} characters")
     * @Assert\Length(max=45, groups={"required"}, maxMessage = "External Id cannot be longer than {{ limit }} characters")
     * @Assert\NotNull(groups={"required"}, message = "External Id cannot be empty.")
     * @Assert\NotBlank(groups={"required"}, message = "External Id cannot be empty.")
     * @Assert\Regex(
     *     pattern="/^[\w \.,#@\-]+$/",
     *     message="External Id can only contain the following characters: alphaNumeric . , # @ - _"
     * )
     * @Assert\Regex(
     *     groups={"required"},
     *     pattern="/^[\w \.,#@\-]+$/",
     *     message="External Id can only contain the following characters: alphaNumeric . , # @ - _"
     * )
     */
    private $externalId;

    /**
     *
     * @var string
     * @Assert\Length(max="100",maxMessage = "Username cannot be longer than {{ limit }} characters")
     * @Assert\Length(max="100", groups={"required"}, maxMessage = "Username cannot be longer than {{ limit }} characters")
     * @Assert\NotNull(groups={"required"}, message = "Username cannot be empty.")
     * @Assert\NotBlank(groups={"required"}, message = "Username cannot be empty.")
     * @Assert\Email(groups={"required"}, message = "Invalid Primary Email")
     *
     * @ORM\Column(name="username", type="string", length=45, nullable=true)
     */
    private $username;

    /**
     *
     * @var string @Assert\Length(max="100",maxMessage = "Authusername cannot be longer than {{ limit }} characters")
     *      @ORM\Column(name="auth_username", type="string", length=100, nullable=true)
     *
     *      @JMS\Expose
     */
    private $authUsername;

    /**
     *
     * @var string @Assert\Length(max="255")
     *      @ORM\Column(name="password", type="string", length=255, nullable=true)
     *
     *      @JMS\Expose
     */
    private $password;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection @ORM\ManyToMany(targetEntity="Entity", inversedBy="person")
     *      @ORM\JoinTable(name="person_entity",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="entity_id", referencedColumnName="id")}
     *      )
     *      @JMS\Expose
     */
    private $entities;

    /**
     *
     * @var ContactInfo[]|\Doctrine\Common\Collections\ArrayCollection @ORM\ManyToMany(targetEntity="ContactInfo", cascade={"persist"})
     *      @ORM\JoinTable(name="person_contact_info",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id", unique=true)}
     *      )
     *      @JMS\Expose
     */
    private $contacts;

    /**
     *
     * @var string @ORM\Column(name="firstname", type="string", length=45, nullable=true)
     * @Assert\Length(max="45",maxMessage = "Firstname cannot be longer than {{ limit }} characters")
     * @Assert\Length(max="45", groups={"required"}, maxMessage = "Firstname cannot be longer than {{ limit }} characters")
     * @Assert\NotNull(groups={"required"}, message = "Firstname cannot be empty.")
     * @Assert\NotBlank(groups={"required"}, message = "Firstname cannot be empty.")
     * @JMS\Expose
     */
    private $firstname;

    /**
     *
     * @var string @ORM\Column(name="lastname", type="string", length=45, nullable=true)
     * @Assert\Length(max="45",maxMessage = "Lastname cannot be longer than {{ limit }} characters")
     * @Assert\Length(max="45", groups={"required"}, maxMessage = "Lastname cannot be longer than {{ limit }} characters")
     * @Assert\NotNull(groups={"required"}, message = "Lastname cannot be empty.")
     * @Assert\NotBlank(groups={"required"}, message = "Lastname cannot be empty.")
     * @JMS\Expose
     */
    private $lastname;

    /**
     *
     * @var string
     *     @Assert\Length(max=100,maxMessage = "Title cannot be longer than {{ limit }} characters");)
     *      @ORM\Column(name="title", type="string", length=100, nullable=true)
     *
     *      @JMS\Expose
     */
    private $title;

    /**
     *
     * @var \Date @ORM\Column(name="date_of_birth", type="date", nullable=true)
     *
     *      @JMS\Expose
     */
    private $dateofbirth;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection @ORM\OneToMany(targetEntity="PersonEbiMetadata", mappedBy="person")
     *      @JMS\Expose
     */
    private $ebiMetadata;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection @ORM\OneToMany(targetEntity="PersonOrgMetadata", mappedBy="person")
     *      @JMS\Expose
     */
    private $orgMetadata;

    /**
     *
     * @var string @ORM\Column(name="activation_token", type="string", length=500, nullable=true)
     *      @JMS\Expose
     */
    private $activationToken;

    /**
     *
     * @var \DateTime @ORM\Column(name="confidentiality_stmt_accept_date", type="datetime", nullable=true)
     *      @JMS\Expose
     */
    private $confidentialityStmtAcceptDate;

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
     * @var \DateTime @ORM\Column(name="token_expiry_date", type="datetime", nullable=true)
     */
    private $tokenExpiryDate;

    /**
     *
     * @var \Date @ORM\Column(name="welcome_email_sent_date", type="date", nullable=true)
     *
     *      @JMS\Expose
     */
    private $welcomeEmailSentDate;

    /**
     *
     * @var \Synapse\RiskBundle\Entity\RiskLevels @ORM\ManyToOne(targetEntity="Synapse\RiskBundle\Entity\RiskLevels")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="risk_level", referencedColumnName="id")
     *      })
     */
    private $riskLevel;

    /**
     *
     * @var \DateTime @ORM\Column(name="risk_update_date", type="datetime", nullable=true)
     *
     *      @JMS\Expose
     */
    private $riskUpdateDate;

    /**
     *
     * @var \Synapse\SearchBundle\Entity\IntentToLeave @ORM\ManyToOne(targetEntity="Synapse\SearchBundle\Entity\IntentToLeave")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="intent_to_leave", referencedColumnName="id")
     *      })
     */
    private $intentToLeave;

    /**
     *
     * @var \DateTime @ORM\Column(name="intent_to_leave_update_date", type="datetime", nullable=true)
     *
     *      @JMS\Expose
     */
    private $intentToLeaveUpdateDate;

    /**
     *
     * @var \DateTime @ORM\Column(name="last_contact_date", type="datetime", nullable=true)
     *
     *      @JMS\Expose
     */
    private $lastContactDate;

    /**
     *
     * @var string @Assert\Length(max="255")
     *      @ORM\Column(name="last_activity", type="string", length=255, nullable=true)
     *
     *      @JMS\Expose
     */
    private $lastActivity;

    /**
     *
     * @var string @ORM\Column(name="record_type", type="string", columnDefinition="enum('home','master','both')")
     */
    private $recordType;

    /**
     *
     * @var string @ORM\Column(name="is_locked", type="string", columnDefinition="enum('y','n')")
     */
    private $isLocked = 'y';

    public function __construct()
    {
        $this->entities = new ArrayCollection();
        $this->metadata = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    /**
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
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
     * @param string $externalId
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     *
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Person
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Person
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getEntities()
    {
        return $this->entities;
    }

    public function addEntity($entity)
    {
        $this->entities[] = $entity;
    }

    public function getContacts()
    {
        return $this->contacts;
    }

    public function addContact($contact)
    {
        $this->contacts[] = $contact;
    }

    /**
     *
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param \Date $dateofbirth
     */
    public function setDateofbirth($dateofbirth)
    {
        $this->dateofbirth = $dateofbirth;
    }

    /**
     *
     * @return \Date
     */
    public function getDateofbirth()
    {
        return $this->dateofbirth;
    }

    public function getOrgMetadata()
    {
        return $this->orgMetadata;
    }

    public function addOrgMetadata($metadataItem)
    {
        $this->orgMetadata[] = $metadataItem;
    }

    public function getEbiMetadata()
    {
        return $this->ebiMetadata;
    }

    public function addEbiMetadata($metadataItem)
    {
        $this->ebiMetadata[] = $metadataItem;
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     * return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return array(
            'ROLE_USER'
        );
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

    }

    /**
     * Set confidentialityStmtAcceptDate
     *
     * @param \DateTime $confidentialityStmtAcceptDate
     * @return Person
     */
    public function setConfidentialityStmtAcceptDate($confidentialityStmtAcceptDate)
    {
        $this->confidentialityStmtAcceptDate = $confidentialityStmtAcceptDate;

        return $this;
    }

    /**
     * Get confidentialityStmtAcceptDate
     *
     * @return \DateTime
     */
    public function getConfidentialityStmtAcceptDate()
    {
        return $this->confidentialityStmtAcceptDate;
    }

    /**
     * Get ActivateToken
     *
     * @return string
     */
    public function getActivationToken()
    {
        return $this->activationToken;
    }

    /**
     * Set ActivateToken
     *
     * @return string
     */
    public function setActivationToken($activationToken)
    {
        $this->activationToken = $activationToken;

        return $this;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return Person
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
     *
     * @param \DateTime $tokenExpiryDate
     */
    public function setTokenExpiryDate($tokenExpiryDate)
    {
        $this->tokenExpiryDate = $tokenExpiryDate;
    }

    /**
     *
     * @return \DateTime
     */
    public function getTokenExpiryDate()
    {
        return $this->tokenExpiryDate;
    }

    /**
     *
     * @param \DateTime $welcomeEmailSentDate
     */
    public function setWelcomeEmailSentDate($welcomeEmailSentDate)
    {
        $this->welcomeEmailSentDate = $welcomeEmailSentDate;
    }

    /**
     *
     * @return \DateTime
     */
    public function getWelcomeEmailSentDate()
    {
        return $this->welcomeEmailSentDate;
    }

    /**
     *
     * @param RiskLevels $riskLevel
     */
    public function setRiskLevel($riskLevel)
    {
        $this->riskLevel = $riskLevel;
    }

    /**
     *
     * @return RiskLevels
     */
    public function getRiskLevel()
    {
        return $this->riskLevel;
    }

    /**
     *
     * @param IntentToLeave $intentToLeave
     */
    public function setIntentToLeave($intentToLeave)
    {
        $this->intentToLeave = $intentToLeave;
    }

    /**
     *
     * @return IntentToLeave
     */
    public function getIntentToLeave()
    {
        return $this->intentToLeave;
    }

    /**
     *
     * @param \Date $intentToLeaveUpdateDate
     */
    public function setIntentToLeaveUpdateDate($intentToLeaveUpdateDate)
    {
        $this->intentToLeaveUpdateDate = $intentToLeaveUpdateDate;
    }

    /**
     *
     * @return \Date
     */
    public function getIntentToLeaveUpdateDate()
    {
        return $this->intentToLeaveUpdateDate;
    }

    /**
     *
     * @param \Date $lastContactDate
     */
    public function setLastContactDate($lastContactDate)
    {
        $this->lastContactDate = $lastContactDate;
    }

    /**
     *
     * @return \Date
     */
    public function getLastContactDate()
    {
        return $this->lastContactDate;
    }

    /**
     *
     * @param string $last_activity
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;
    }

    /**
     *
     * @return string
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     *
     * @param string $riskUpdateDate
     */
    public function setRiskUpdateDate($riskUpdateDate)
    {
        $this->riskUpdateDate = $riskUpdateDate;
    }

    /**
     *
     * @return string
     */
    public function getRiskUpdateDate()
    {
        return $this->riskUpdateDate;
    }


    /**
     *
     * @param string $recordType
     */
    public function setRecordType($recordType)
    {
        $this->recordType = $recordType;
    }

    /**
     *
     * @return string
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     *
     * @param string $isLocked
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;
    }

    /**
     *
     * @return string
     */
    public function getRecordType()
    {
        return $this->recordType;
    }

    /**
     * Sets the value of authUsername.
     *
     * @return self
     */
    public function setAuthUsername($authUsername)
    {
        $this->authUsername = $authUsername;

        return $this;
    }

    /**
     * Gets the value of authUsername.
     *
     */
    public function getAuthUsername()
    {
        return $this->authUsername;
    }
}