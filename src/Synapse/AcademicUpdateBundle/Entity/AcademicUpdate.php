<?php
namespace Synapse\AcademicUpdateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AcademicUpdate
 *
 * @ORM\Table(name="academic_update", indexes={@ORM\Index(name="fk_academic_update_organization1_idx", columns={"org_id"}), @ORM\Index(name="fk_academic_update_org_courses1_idx", columns={"org_courses_id"}), @ORM\Index(name="fk_academic_update_academic_update_request1_idx", columns={"academic_update_request_id"}), @ORM\Index(name="fk_academic_update_person2_idx", columns={"person_id_faculty_responded"}), @ORM\Index(name="fk_academic_update_person3_idx", columns={"person_id_student"})})
 * @ORM\Entity(repositoryClass="Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class AcademicUpdate extends BaseEntity
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
     * @var string @ORM\Column(name="update_type", type="string", nullable=true, columnDefinition="enum('bulk','targeted','adhoc','ftp')")
     */
    private $updateType;

    /**
     *
     * @var string @ORM\Column(name="status", type="string", nullable=true, columnDefinition="enum('open','closed','cancelled','saved')")
     */
    private $status;

    /**
     *
     * @var \DateTime @ORM\Column(name="request_date", type="datetime", nullable=true)
     */
    private $requestDate;

    /**
     *
     * @var \DateTime @ORM\Column(name="due_date", type="datetime", nullable=true)
     */
    private $dueDate;

    /**
     *
     * @var \DateTime @ORM\Column(name="update_date", type="datetime", nullable=true)
     */
    private $updateDate;

    /**
     *
     * @var string @ORM\Column(name="failure_risk_level", type="string", length=10, nullable=true)
     * @Assert\Choice(choices = {"High", "Low"}, message = "Not a valid value for failure risk level, valid values are 'High' or 'Low'")
     */
    private $failureRiskLevel;

    /**
     *
     * @var string @ORM\Column(name="grade", type="string", length=20, nullable=true)
     * @Assert\Choice(choices = {"A", "B", "C", "D", "F", "P", "Pass"}, message = "Not a valid value for grade, valid values are 'A', 'B', 'C', 'D' , 'F', 'P', 'Pass'")
     */
    private $grade;

    /**
     *
     * @var integer @ORM\Column(name="absence", type="integer", nullable=true)
     * @Assert\Range(
     *      min = 0,
     *      max = 99,
     *      minMessage = "absence value should be at least {{ limit }}",
     *      maxMessage = "absence value should not be greater than {{ limit }}"
     * )
     */
    private $absence;

    /**
     *
     * @var string @ORM\Column(name="comment", type="string", length=300, nullable=true)
     * @Assert\Type("string")
     * @Assert\Length(max=300,maxMessage = "Comment can not exceed {{ limit }} characters ");
     */
    private $comment;

    /**
     *
     * @var boolean @ORM\Column(name="refer_for_assistance", type="boolean", nullable=true)
     */
    private $referForAssistance;

    /**
     *
     * @var boolean @ORM\Column(name="send_to_student", type="boolean", nullable=true)
     */
    private $sendToStudent;

    /**
     *
     * @var boolean @ORM\Column(name="is_upload", type="boolean", nullable=true)
     */
    private $isUpload;

    /**
     *
     * @var boolean @ORM\Column(name="is_adhoc", type="boolean", nullable=true)
     */
    private $isAdhoc;
    
    /**
     *
     * @var boolean @ORM\Column(name="is_submitted_without_change", type="boolean", nullable=true)
     */
    private $isBypassed;
    
    

    /**
     *
     * @var string @ORM\Column(name="final_grade", type="string", length=20, nullable=true)
     * @Assert\Choice(choices = {"A", "A-", "B+", "B", "B-", "C+", "C", "C-", "D+", "D", "D-" , "F", "F/No Pass", "P", "Pass", "Withdraw", "Incomplete", "In Progress", "Not for Credit"}, message = "Not a valid value for final grade, valid values are 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-' , 'F', 'F/No Pass', 'P', 'Pass', 'Withdraw', 'Incomplete', 'In Progress', 'Not for Credit'")
     */
    private $finalGrade;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     *      })
     */
    private $org;

    /**
     *
     * @var \Synapse\AcademicBundle\Entity\OrgCourses @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgCourses")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_courses_id", referencedColumnName="id")
     *      })
     */
    private $orgCourses;

    /**
     *
     * @var \Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest @ORM\ManyToOne(targetEntity="Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="academic_update_request_id", referencedColumnName="id")
     *      })
     */
    private $academicUpdateRequest;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_faculty_responded", referencedColumnName="id")
     *      })
     */
    private $personFacultyResponded;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_student", referencedColumnName="id")
     *      })
     */
    private $personStudent;

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
     * Set updateType
     *
     * @param string $updateType            
     * @return AcademicUpdate
     */
    public function setUpdateType($updateType)
    {
        $this->updateType = $updateType;
        
        return $this;
    }

    /**
     * Get updateType
     *
     * @return string
     */
    public function getUpdateType()
    {
        return $this->updateType;
    }

    /**
     * Set status
     *
     * @param string $status            
     * @return AcademicUpdate
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
     * Set requestDate
     *
     * @param \DateTime $requestDate            
     * @return AcademicUpdate
     */
    public function setRequestDate($requestDate)
    {
        $this->requestDate = $requestDate;
        
        return $this;
    }

    /**
     * Get requestDate
     *
     * @return \DateTime
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate            
     * @return AcademicUpdate
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;
        
        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate            
     * @return AcademicUpdate
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
        
        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set failureRiskLevel
     *
     * @param string $failureRiskLevel            
     * @return AcademicUpdate
     */
    public function setFailureRiskLevel($failureRiskLevel)
    {
        $this->failureRiskLevel = $failureRiskLevel;
        
        return $this;
    }

    /**
     * Get failureRiskLevel
     *
     * @return string
     */
    public function getFailureRiskLevel()
    {
        return $this->failureRiskLevel;
    }

    /**
     * Set grade
     *
     * @param string $grade            
     * @return AcademicUpdate
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
        
        return $this;
    }

    /**
     * Get grade
     *
     * @return string
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Set absence
     *
     * @param integer $absence            
     * @return AcademicUpdate
     */
    public function setAbsence($absence)
    {
        $this->absence = $absence;
        
        return $this;
    }

    /**
     * Get absence
     *
     * @return integer
     */
    public function getAbsence()
    {
        return $this->absence;
    }

    /**
     * Set comment
     *
     * @param string $comment            
     * @return AcademicUpdate
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        
        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set referForAssistance
     *
     * @param boolean $referForAssistance            
     * @return AcademicUpdate
     */
    public function setReferForAssistance($referForAssistance)
    {
        $this->referForAssistance = $referForAssistance;
        
        return $this;
    }

    /**
     * Get referForAssistance
     *
     * @return boolean
     */
    public function getReferForAssistance()
    {
        return $this->referForAssistance;
    }

    /**
     * Set sendToStudent
     *
     * @param boolean $sendToStudent            
     * @return AcademicUpdate
     */
    public function setSendToStudent($sendToStudent)
    {
        $this->sendToStudent = $sendToStudent;
        
        return $this;
    }

    /**
     * Get sendToStudent
     *
     * @return boolean
     */
    public function getSendToStudent()
    {
        return $this->sendToStudent;
    }

    /**
     * Set isUpload
     *
     * @param boolean $isUpload            
     * @return AcademicUpdate
     */
    public function setIsUpload($isUpload)
    {
        $this->isUpload = $isUpload;
        
        return $this;
    }

    /**
     * Get isUpload
     *
     * @return boolean
     */
    public function getIsUpload()
    {
        return $this->isUpload;
    }

    /**
     * Set isAdhoc
     *
     * @param boolean $isAdhoc            
     * @return AcademicUpdate
     */
    public function setIsAdhoc($isAdhoc)
    {
        $this->isAdhoc = $isAdhoc;
        
        return $this;
    }

    /**
     * Get isAdhoc
     *
     * @return boolean
     */
    public function getIsAdhoc()
    {
        return $this->isAdhoc;
    }

    /**
     * Set finalGrade
     *
     * @param string $finalGrade            
     * @return AcademicUpdate
     */
    public function setFinalGrade($finalGrade)
    {
        $this->finalGrade = $finalGrade;
        
        return $this;
    }

    /**
     * Get finalGrade
     *
     * @return string
     */
    public function getFinalGrade()
    {
        return $this->finalGrade;
    }

    /**
     * Set org
     *
     * @param \Synapse\CoreBundle\Entity\Organization $org
     * @return AcademicUpdate
     */
    public function setOrg(\Synapse\CoreBundle\Entity\Organization $org = null)
    {
        $this->org = $org;
        
        return $this;
    }

    /**
     * Get org
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrg()
    {
        return $this->org;
    }

    /**
     * Set orgCourses
     *
     * @param \Synapse\AcademicBundle\Entity\OrgCourses $orgCourses            
     * @return AcademicUpdate
     */
    public function setOrgCourses(\Synapse\AcademicBundle\Entity\OrgCourses $orgCourses = null)
    {
        $this->orgCourses = $orgCourses;
        
        return $this;
    }

    /**
     * Get orgCourses
     *
     * @return \Synapse\AcademicBundle\Entity\OrgCourses
     */
    public function getOrgCourses()
    {
        return $this->orgCourses;
    }

    /**
     * Set academicUpdateRequest
     *
     * @param \Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest $academicUpdateRequest            
     * @return AcademicUpdate
     */
    public function setAcademicUpdateRequest(AcademicUpdateRequest $academicUpdateRequest = null)
    {
        $this->academicUpdateRequest = $academicUpdateRequest;
        
        return $this;
    }

    /**
     * Get academicUpdateRequest
     *
     * @return \Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest
     */
    public function getAcademicUpdateRequest()
    {
        return $this->academicUpdateRequest;
    }

    /**
     * Set personFacultyResponded
     *
     * @param \Synapse\CoreBundle\Entity\Person $personFacultyResponded            
     * @return AcademicUpdate
     */
    public function setPersonFacultyResponded(\Synapse\CoreBundle\Entity\Person $personFacultyResponded = null)
    {
        $this->personFacultyResponded = $personFacultyResponded;
        
        return $this;
    }

    /**
     * Get personFacultyResponded
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonFacultyResponded()
    {
        return $this->personFacultyResponded;
    }

    /**
     * Set personStudent
     *
     * @param \Synapse\CoreBundle\Entity\Person $personStudent            
     * @return AcademicUpdate
     */
    public function setPersonStudent(\Synapse\CoreBundle\Entity\Person $personStudent = null)
    {
        $this->personStudent = $personStudent;
        
        return $this;
    }

    /**
     * Get personStudent
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonStudent()
    {
        return $this->personStudent;
    }

    /**
     * @param boolean $isBypassed
     */
    public function setIsBypassed($isBypassed)
    {
        $this->isBypassed = $isBypassed;
    }

    /**
     * @return boolean
     */
    public function getIsBypassed()
    {
        return $this->isBypassed;
    }

}