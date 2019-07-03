<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\SearchBundle\Entity\OrgSearch;
use Synapse\StaticListBundle\Entity\OrgStaticList;

/**
 * AlertNotifications
 *
 * @ORM\Table(name="alert_notifications",indexes={@ORM\Index(name="fk_alert_notifications_referrals1_idx",columns={"referrals_id"}),@ORM\Index(name="fk_alert_notifications_appointments1_idx",columns={"appointments_id"}),@ORM\Index(name="fk_alert_notifications_person1_idx", columns={"person_id"}),@ORM\Index(name="fk_alert_notifications_organization1_idx", columns={"organization_id"}),@ORM\Index(name="fk_alert_notifications_org_search1_idx", columns={"org_search_id"}),@ORM\Index(name="fk_alert_notifications_academic_update1_idx", columns={"academic_update_id"}),@ORM\Index(name="fk_alert_notifications_org_announcements1_idx",columns={"org_announcements_id"}),@ORM\Index(name="fk_alert_notifications_report_running_status1_idx",columns={"reports_running_status_id"})});
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\AlertNotificationsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class AlertNotifications extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *     
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization", fetch="EXTRA_LAZY")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     *      })
     *      @Assert\NotBlank()
     */
    private $organization;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Referrals @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Referrals", fetch="EXTRA_LAZY")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="referrals_id", referencedColumnName="id")
     *      })
     *      @Assert\NotBlank()
     */
    private $referrals;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Appointments @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Appointments", fetch="EXTRA_LAZY")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="appointments_id", referencedColumnName="id")
     *      })
     *      @Assert\NotBlank()
     */
    private $appointments;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person", fetch="EXTRA_LAZY")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     *      })
     */
    private $person;

    /**
     *
     * @var string @ORM\Column(name="event", type="string", length=45, nullable=true)
     */
    private $event;

    /**
     * @var string @ORM\Column(name="reason", type="string", length=120, nullable=true)
     * @var string @Assert\Length(max="120", maxMessage = "Notification cannot be longer than {{ limit }} characters")
     */
    private $reason;

    /**
     *
     * @var boolean
     * @ORM\Column(name="is_read", type="boolean", length=1, nullable=false, options={"default" : 0})
     *     
     */
    private $isRead;

    /**
     *
     * @var boolean
     * @ORM\Column(name="is_seen", type="boolean", length=1, nullable=false, options={"default" : 0})
     *
     */
    private $isSeen;

    /**
     *
     * @var \Synapse\SearchBundle\Entity\OrgSearch @ORM\ManyToOne(targetEntity="Synapse\SearchBundle\Entity\OrgSearch", cascade={"persist"}, fetch="EXTRA_LAZY")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_search_id", referencedColumnName="id")
     *      })
     *
     */
    private $orgSearch;

    /**
     *
     * @var string @ORM\Column(name="org_course_upload_file", type="string", length=255, nullable=true)
     */
    private $orgCourseUploadFile;

    /**
     *
     * @var \Synapse\AcademicUpdateBundle\Entity\AcademicUpdate @ORM\ManyToOne(targetEntity="Synapse\AcademicUpdateBundle\Entity\AcademicUpdate", fetch="EXTRA_LAZY")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="academic_update_id", referencedColumnName="id")
     *      })
     */
    private $academicUpdate;

    /**
     *
     * @var \Synapse\StaticListBundle\Entity\OrgStaticList @ORM\ManyToOne(targetEntity="Synapse\StaticListBundle\Entity\OrgStaticList", fetch="EXTRA_LAZY")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_static_list_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgStaticList;

    /**
     *
     * @var \Synapse\CampusResourceBundle\Entity\OrgAnnouncements @ORM\ManyToOne(targetEntity="Synapse\CampusResourceBundle\Entity\OrgAnnouncements", fetch="EXTRA_LAZY")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_announcements_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgAnnouncements;
    
    
    /**
     *
     * @var \Synapse\ReportsBundle\Entity\ReportsRunningStatus @ORM\ManyToOne(targetEntity="Synapse\ReportsBundle\Entity\ReportsRunningStatus", fetch="EXTRA_LAZY")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="reports_running_status_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $reportsRunningStatus;

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
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     * @return AlertNotifications
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
     * Set Referrals
     *
     * @param \Synapse\CoreBundle\Entity\Referrals $referrals
     * @return AlertNotifications
     */
    public function setReferrals(\Synapse\CoreBundle\Entity\Referrals $referrals = null)
    {
        $this->referrals = $referrals;
        return $this;
    }

    /**
     * Get Referrals
     *
     * @return \Synapse\CoreBundle\Entity\Referrals
     */
    public function getReferrals()
    {
        return $this->referrals;
    }

    /**
     * Set Appointments
     *
     * @param \Synapse\CoreBundle\Entity\Appointments $appointments
     * @return AlertNotifications
     */
    public function setAppointments(\Synapse\CoreBundle\Entity\Appointments $appointments = null)
    {
        $this->appointments = $appointments;
        return $this;
    }

    /**
     * Get Appointments
     *
     * @return \Synapse\CoreBundle\Entity\Appointments
     */
    public function getAppointments()
    {
        return $this->appointments;
    }

    /**
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return AlertNotifications
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;
        
        return $this;
    }

    /**
     * Get person
     *
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set Event
     *
     * @param string $event            
     * @return AlertNotifications
     */
    public function setEvent($event)
    {
        $this->event = $event;
        
        return $this;
    }

    /**
     * Get Event
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set Reason
     *
     * @param string $reason            
     * @return AlertNotifications
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
        
        return $this;
    }

    /**
     * Get Reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @return bool
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * @param bool $isRead
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;
    }

    /**
     * @return bool
     */
    public function getIsSeen()
    {
        return $this->isSeen;
    }

    /**
     * @param bool $isSeen
     */
    public function setIsSeen($isSeen)
    {
        $this->isSeen = $isSeen;
    }

    /**
     * Set OrgSearch
     *
     * @param \Synapse\SearchBundle\Entity\OrgSearch $orgSearch
     * @return AlertNotifications
     */
    public function setOrgSearch(\Synapse\SearchBundle\Entity\OrgSearch $orgSearch = null)
    {
        $this->orgSearch = $orgSearch;
        return $this;
    }

    /**
     * Get OrgSearch
     *
     * @return OrgSearch
     */
    public function getOrgSearch()
    {
        return $this->orgSearch;
    }

    /**
     * Set orgCourseUploadFile
     *
     * @param string $orgCourseUploadFile            
     * @return AlertNotifications
     */
    public function setOrgCourseUploadFile($orgCourseUploadFile)
    {
        $this->orgCourseUploadFile = $orgCourseUploadFile;
        
        return $this;
    }

    /**
     * Get orgCourseUploadFile
     *
     * @return string
     */
    public function getOrgCourseUploadFile()
    {
        return $this->orgCourseUploadFile;
    }

    /**
     * Set academicUpdate
     *
     * @param \Synapse\AcademicUpdateBundle\Entity\AcademicUpdate $academicUpdate            
     * @return AlertNotifications
     */
    public function setAcademicUpdate(\Synapse\AcademicUpdateBundle\Entity\AcademicUpdate $academicUpdate = null)
    {
        $this->academicUpdate = $academicUpdate;
        
        return $this;
    }

    /**
     * Get academicUpdate
     *
     * @return \Synapse\AcademicUpdateBundle\Entity\AcademicUpdate
     */
    public function getAcademicUpdate()
    {
        return $this->academicUpdate;
    }

    /**
     * Set academicUpdate
     *
     * @param \Synapse\StaticListBundle\Entity\OrgStaticList $orgStaticList            
     * @return AlertNotifications
     */
    public function setOrgStaticList(\Synapse\StaticListBundle\Entity\OrgStaticList $orgStaticList = null)
    {
        $this->orgStaticList = $orgStaticList;
        
        return $this;
    }

    /**
     * Get orgStaticList
     *
     * @return OrgStaticList
     */
    public function getOrgStaticList()
    {
        return $this->orgStaticList;
    }

    /**
     * Set orgAnnouncements
     *
     * @param \Synapse\CampusResourceBundle\Entity\OrgAnnouncements $orgAnnouncements            
     * @return AlertNotifications
     */
    public function setOrgAnnouncements(\Synapse\CampusResourceBundle\Entity\OrgAnnouncements $orgAnnouncements = null)
    {
        $this->orgAnnouncements = $orgAnnouncements;
        
        return $this;
    }

    /**
     * Get orgAnnouncements
     *
     * @return \Synapse\CampusResourceBundle\Entity\OrgAnnouncements
     */
    public function getOrgAnnouncements()
    {
        return $this->orgAnnouncements;
    }

    /**
     * @param \Synapse\ReportsBundle\Entity\ReportsRunningStatus $reportsRunningStatus
     */
    public function setReportsRunningStatus($reportsRunningStatus)
    {
        $this->reportsRunningStatus = $reportsRunningStatus;
    }

    /**
     * @return \Synapse\ReportsBundle\Entity\ReportsRunningStatus
     */
    public function getReportsRunningStatus()
    {
        return $this->reportsRunningStatus;
    }

}
