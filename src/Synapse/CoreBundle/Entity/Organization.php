<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Organization
 *
 * @ORM\Table(name="organization")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrganizationRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @JMS\ExclusionPolicy("all")
 * @UniqueEntity(fields={"subdomain"},message="Subdomain already exists.")
 * @UniqueEntity(fields={"campusId"},message="Id already exists.")
 */
class Organization extends BaseEntity
{

    /**
     *
     * @var string @ORM\Column(name="subdomain", type="string", length=45, nullable=true)
     *      @Assert\Regex("/^(?:[A-Za-z0-9][A-Za-z0-9\-]{0,61}[A-Za-z0-9]|[A-Za-z0-9])$/", message="No special characters or spaces allowed")
     *      @JMS\Expose
     */
    private $subdomain;

    /**
     *
     * @var integer @ORM\Column(name="parent_organization_id", type="integer", nullable=true)
     *      @JMS\Expose
     */
    private $parentOrganizationId;

    /**
     *
     * @var string @ORM\Column(name="status", type="string", length=1, nullable=true)
     *      @JMS\Expose
     */
    private $status;

    /**
     *
     * @var string @ORM\Column(name="time_zone", type="string", length=45, nullable=true)
     *      @JMS\Expose
     */
    private $timeZone;

    /**
     *
     * @var string @ORM\Column(name="website", type="string", length=100, nullable=true)
     *      @JMS\Expose
     */
    private $website;

    /**
     *
     * @var string @ORM\Column(name="logo_file_name", type="text", nullable=true)
     *      @JMS\Expose
     */
    private $logoFileName;

    /**
     *
     * @var string @ORM\Column(name="primary_color", type="string", length=45, nullable=true)
     *      @JMS\Expose
     */
    private $primaryColor;

    /**
     *
     * @var string @ORM\Column(name="secondary_color", type="string", length=45, nullable=true)
     *      @JMS\Expose
     */
    private $secondaryColor;

    /**
     *
     * @var string @ORM\Column(name="ebi_confidentiality_statement", type="string", length=5000, nullable=true)
     *      @JMS\Expose
     */
    private $ebiConfidentialityStatement;

    /**
     *
     * @var string @ORM\Column(name="custom_confidentiality_statement", type="string", length=5000, nullable=true)
     *      @JMS\Expose
     */
    private $customConfidentialityStatement;

    /**
     *
     * @var integer @ORM\Column(name="inactivity_timeout", type="integer", nullable=true)
     *      @JMS\Expose
     */
    private $inactivityTimeout;

    /**
     *
     * @var string @ORM\Column(name="ftp_user", type="string", length=45, nullable=true)
     *      @JMS\Expose
     */
    private $ftpUser;

    /**
     *
     * @var string @ORM\Column(name="ftp_password", type="string", length=100, nullable=true)
     *      @JMS\Expose
     */
    private $ftpPassword;

    /**
     *
     * @var string @ORM\Column(name="ftp_home", type="string", length=200, nullable=true)
     *      @JMS\Expose
     */
    private $ftpHome;

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
     * @var boolean @ORM\Column(name="academic_update_notification", type="boolean", length=1, nullable=true)
     *     
     */
    private $academicUpdateNotification;

    /**
     *
     * @var boolean @ORM\Column(name="refer_for_academic_assistance", type="boolean", length=1, nullable=true)
     *     
     */
    private $referForAcademicAssistance;

    /**
     *
     * @var boolean @ORM\Column(name="send_to_student", type="boolean", length=1, nullable=true)
     *     
     */
    private $sendToStudent;

    /**
     *
     * @var string @ORM\Column(name="external_id", type="string", length=45, nullable=true)
     *      @JMS\Expose
     */
    private $externalId;

    /**
     *
     * @var string @ORM\Column(name="campus_id", type="string", length=15, nullable=true)     
     *      @Assert\Regex("/^[A-Za-z0-9]+$/", message="Id cannot contain special characters")
     *      @JMS\Expose
     */
    private $campusId;

    /**
     *
     * @var string @ORM\Column(name="tier", type="string", columnDefinition="enum('0','1','2','3')")
     */
    private $tier;

    /**
     *
     * @var string @ORM\Column(name="pcs", type="string", columnDefinition="enum('G','E')")
     */
    private $pcs;
    
    /**
     *
     * @var boolean @ORM\Column(name="is_ldap_saml_enabled ", type="boolean", length=1, nullable=true)
     *
     */
    private $isLdapSamlEnabled ;
    
    
    /**
     *
     * @var boolean @ORM\Column(name="can_view_in_progress_grade", type="boolean", length=1, nullable=true)
     *
     */
    private $canViewInProgressGrade;
    

    /**
     *
     * @var boolean @ORM\Column(name="can_view_absences", type="boolean", length=1, nullable=true)
     *
     */
    private $canViewAbsences;
    
    /**
     *
     * @var boolean @ORM\Column(name="can_view_comments", type="boolean", length=1, nullable=true)
     *
     */
    private $canViewComments;
    
    /**
     *
     * @var string @ORM\Column(name="is_mock", type="string", columnDefinition="enum('y','n')", options={"default":"n"}, precision=0, scale=0, nullable=false, unique=false)
     */
    private $isMock;

    /**
     *
     * @var boolean @ORM\Column(name="calendar_sync", type="boolean", nullable=true)
     *      @JMS\Expose
     */
    private $calendarSync;

    /**
     * Set subdomain
     *
     * @param string $subdomain            
     * @return Organization
     */
    public function setSubdomain($subdomain)
    {
        $this->subdomain = $subdomain;
        
        return $this;
    }

    /**
     * Get subdomain
     *
     * @return string
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * Set parentOrganizationId
     *
     * @param integer $parentOrganizationId            
     * @return Organization
     */
    public function setParentOrganizationId($parentOrganizationId)
    {
        $this->parentOrganizationId = $parentOrganizationId;
        
        return $this;
    }

    /**
     * Get parentOrganizationId
     *
     * @return integer
     */
    public function getParentOrganizationId()
    {
        return $this->parentOrganizationId;
    }

    /**
     * Set status
     *
     * @param string $status            
     * @return Organization
     */
    public function setStatus($status)
    {
        $this->status = $status;
        
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set timeZone
     *
     * @param string $timeZone            
     * @return Organization
     */
    public function setTimeZone($timeZone)
    {
        $this->timeZone = $timeZone;
        
        return $this;
    }

    /**
     * Get timeZone
     *
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * Set website
     *
     * @param string $website            
     * @return Organization
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        
        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set logoFileName
     *
     * @param string $logoFileName            
     * @return Organization
     */
    public function setLogoFileName($logoFileName)
    {
        $this->logoFileName = $logoFileName;
        
        return $this;
    }

    /**
     * Get logoFileName
     *
     * @return string
     */
    public function getLogoFileName()
    {
        return $this->logoFileName;
    }

    /**
     * Set primaryColor
     *
     * @param string $primaryColor            
     * @return Organization
     */
    public function setPrimaryColor($primaryColor)
    {
        $this->primaryColor = $primaryColor;
        
        return $this;
    }

    /**
     * Get primaryColor
     *
     * @return string
     */
    public function getPrimaryColor()
    {
        return $this->primaryColor;
    }

    /**
     * Set secondaryColor
     *
     * @param string $secondaryColor            
     * @return Organization
     */
    public function setSecondaryColor($secondaryColor)
    {
        $this->secondaryColor = $secondaryColor;
        
        return $this;
    }

    /**
     * Get secondaryColor
     *
     * @return string
     */
    public function getSecondaryColor()
    {
        return $this->secondaryColor;
    }

    /**
     * Set ebiConfidentialityStatement
     *
     * @param string $ebiConfidentialityStatement            
     * @return Organization
     */
    public function setEbiConfidentialityStatement($ebiConfidentialityStatement)
    {
        $this->ebiConfidentialityStatement = $ebiConfidentialityStatement;
        
        return $this;
    }

    /**
     * Get ebiConfidentialityStatement
     *
     * @return string
     */
    public function getEbiConfidentialityStatement()
    {
        return $this->ebiConfidentialityStatement;
    }

    /**
     *
     * @param string $customConfidentialityStatement            
     */
    public function setCustomConfidentialityStatement($customConfidentialityStatement)
    {
        $this->customConfidentialityStatement = $customConfidentialityStatement;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getCustomConfidentialityStatement()
    {
        return $this->customConfidentialityStatement;
    }

    /**
     *
     * @return int
     */
    public function getInactivityTimeout()
    {
        return $this->inactivityTimeout;
    }

    /**
     *
     * @param int $inactivityTimeout            
     */
    public function setInactivityTimeout($inactivityTimeout)
    {
        $this->inactivityTimeout = $inactivityTimeout;
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
     * Sets the value of ftpUser.
     *
     * @param string $ftpUser
     *            the ftp user
     *            
     * @return self
     */
    public function setFtpUser($ftpUser)
    {
        $this->ftpUser = $ftpUser;
        
        return $this;
    }

    /**
     * Gets the value of ftpUser.
     *
     * @return string
     */
    public function getFtpUser()
    {
        return $this->ftpUser;
    }

    /**
     * Sets the value of ftpPassword.
     *
     * @param string $ftpPassword
     *            the ftp password
     *            
     * @return self
     */
    public function setFtpPassword($ftpPassword)
    {
        $this->ftpPassword = $ftpPassword;
        
        return $this;
    }

    /**
     * Gets the value of ftpPassword.
     *
     * @return string
     */
    public function getFtpPassword()
    {
        return $this->ftpPassword;
    }

    /**
     * Sets the value of ftpHome.
     *
     * @param string $ftpHome
     *            the ftp home
     *            
     * @return self
     */
    public function setFtpHome($ftpHome)
    {
        $this->ftpHome = $ftpHome;
        
        return $this;
    }

    /**
     * Gets the value of ftpHome.
     *
     * @return string
     */
    public function getFtpHome()
    {
        return $this->ftpHome;
    }

    /**
     *
     * @param boolean $academicUpdateNotification            
     */
    public function setAcademicUpdateNotification($academicUpdateNotification)
    {
        $this->academicUpdateNotification = $academicUpdateNotification;
    }

    /**
     *
     * @return boolean
     */
    public function getAcademicUpdateNotification()
    {
        return $this->academicUpdateNotification;
    }

    /**
     *
     * @param boolean $referForAcademicAssistance            
     */
    public function setReferForAcademicAssistance($referForAcademicAssistance)
    {
        $this->referForAcademicAssistance = $referForAcademicAssistance;
    }

    /**
     *
     * @return boolean
     */
    public function getReferForAcademicAssistance()
    {
        return $this->referForAcademicAssistance;
    }

    /**
     *
     * @param boolean $sendToStudent            
     */
    public function setSendToStudent($sendToStudent)
    {
        $this->sendToStudent = $sendToStudent;
    }

    /**
     *
     * @return boolean
     */
    public function getSendToStudent()
    {
        return $this->sendToStudent;
    }

    /**
     *
     * @param string $campusId            
     */
    public function setCampusId($campusId)
    {
        $this->campusId = $campusId;
    }

    /**
     *
     * @return string
     */
    public function getCampusId()
    {
        return $this->campusId;
    }

    /**
     *
     * @param string $tier            
     */
    public function setTier($tier)
    {
        $this->tier = $tier;
    }

    /**
     *
     * @return string
     */
    public function getTier()
    {
        return $this->tier;
    }

    /**
     *
     * @param string $externalId            
     *
     * @return self
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        
        return $this;
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
     *
     * @param string $pcs            
     */
    public function setPcs($pcs)
    {
        $this->pcs = $pcs;
    }

    /**
     *
     * @return string
     */
    public function getPcs()
    {
        return $this->pcs;
    }
    
    /**
     *
     * @param boolean $isLdapSamlEnabled
     */
    public function setIsLdapSamlEnabled($isLdapSamlEnabled)
    {
    	$this->isLdapSamlEnabled = $isLdapSamlEnabled;
    }
    
    /**
     *
     * @return boolean
     */
    public function getIsLdapSamlEnabled()
    {
    	return $this->isLdapSamlEnabled;
    }

    /**
     * @param boolean $canViewAbsences
     */
    public function setCanViewAbsences($canViewAbsences)
    {
        $this->canViewAbsences = $canViewAbsences;
    }

    /**
     * @return boolean
     */
    public function getCanViewAbsences()
    {
        return $this->canViewAbsences;
    }

    /**
     * @param boolean $canViewInProgressGrade
     */
    public function setCanViewInProgressGrade($canViewInProgressGrade)
    {
        $this->canViewInProgressGrade = $canViewInProgressGrade;
    }

    /**
     * @return boolean
     */
    public function getCanViewInProgressGrade()
    {
        return $this->canViewInProgressGrade;
    }

    /**
     * @param boolean $canViewComments
     */
    public function setCanViewComments($canViewComments)
    {
        $this->canViewComments = $canViewComments;
    }

    /**
     *
     * @return boolean
     */
    public function getCanViewComments()
    {
        return $this->canViewComments;
    }

    /**
     *
     * @param string $isMock            
     */
    public function setIsMock($isMock)
    {
        $this->isMock = $isMock;
    }
    
    /**
     *
     * @return string
     */
    public function getIsMock()
    {
        return $this->isMock;
    }

    /**
     * @return boolean
     */
    public function getCalendarSync()
    {
        return $this->calendarSync;
    }

    /**
     *
     * @param boolean $calendarSync
     */
    public function setCalendarSync($calendarSync)
    {
        $this->calendarSync = $calendarSync;
    }
}
