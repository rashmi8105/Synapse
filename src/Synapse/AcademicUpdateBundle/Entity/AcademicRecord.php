<?php
namespace Synapse\AcademicUpdateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;


/**
 * AcademicRecord
 *
 * @ORM\Table(name="academic_record",
 *  indexes={
 *     @ORM\Index(name="risk_index", columns={"organization_id", "person_id_student", "failure_risk_level", "in_progress_grade"}),
 *     @ORM\Index(name="org_course_student_update_index", columns={"organization_id", "org_courses_id", "person_id_student", "update_date"})
 * },
 *  uniqueConstraints={
 *     @ORM\UniqueConstraint(name="org_course_student_unique", columns={"organization_id", "org_courses_id", "person_id_student"})
 * })
 * @ORM\Entity(repositoryClass="Synapse\AcademicUpdateBundle\Repository\AcademicRecordRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @UniqueEntity(fields={"organization", "orgCourses", "personStudent"},message="Academic Record for the student for the course already exists.")
 */
class AcademicRecord extends BaseEntity
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var Organization
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=false)
     *  })
     * @Assert\NotBlank()
     */
    private $organization;

    /**
     *
     * @var OrgCourses
     * @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgCourses")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="org_courses_id", referencedColumnName="id", nullable=false)
     *  })
     * @Assert\NotBlank()
     */
    private $orgCourses;

    /**
     *
     * @var Person
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="person_id_student", referencedColumnName="id", nullable=false)
     * })
     * @Assert\NotBlank()
     */
    private $personStudent;

    /**
     * @var string
     * @ORM\Column(name="failure_risk_level", type="string", length=10, nullable=true)
     * @Assert\Choice(choices = {null, "High", "Low"}, message = "Not a valid value for failure risk level")
     */
    private $failureRiskLevel;

    /**
     * @var string
     * @ORM\Column(name="in_progress_grade", type="string", length=20, nullable=true)
     * @Assert\Choice(choices = {null, "A", "B", "C", "D", "F", "No Pass", "Pass", "F/No Pass"}, message = "Not a valid value for In progress grade")
     */
    private $inProgressGrade;

    /**
     * @var integer
     * @ORM\Column(name="absence", type="integer", nullable=true)
     * @Assert\Range(
     *      min = 0,
     *      max = 99,
     *      minMessage = "absence should be at least {{ limit }}",
     *      maxMessage = "absence should not be greater than {{ limit }}"
     * )
     */
    private $absence;

    /**
     * @var string
     * @ORM\Column(name="comment", type="string", length=300, nullable=true)
     * @Assert\Length(max=300,maxMessage = "Comment can not exceed {{ limit }} characters ");
     */
    private $comment;

    /**
     *
     * @var string
     * @ORM\Column(name="final_grade", type="string", length=20, nullable=true)
     * @Assert\Choice(choices = {null, "A", "A-", "B+", "B", "B-", "C+", "C", "C-", "D+", "D", "D-" , "F", "No Pass", "F/No Pass", "Pass", "Withdraw", "Incomplete", "In Progress", "Not for Credit"}, message = "Not a valid value for final grade")
     */
    private $finalGrade;



    /**
     * @var \DateTime
     * @ORM\Column(name="update_date", type="datetime", nullable=true)
     */
    private $updateDate;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="failure_risk_level_update_date", type="datetime", nullable=true)
     */
    private $failureRiskLevelUpdateDate;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="in_progress_grade_update_date", type="datetime", nullable=true)
     */
    private $inProgressGradeUpdateDate;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="absence_update_date", type="datetime", nullable=true)
     */
    private $absenceUpdateDate;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="comment_update_date", type="datetime", nullable=true)
     */
    private $commentUpdateDate;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="final_grade_update_date", type="datetime", nullable=true)
     */
    private $finalGradeUpdateDate;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param Organization $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return OrgCourses
     */
    public function getOrgCourses()
    {
        return $this->orgCourses;
    }

    /**
     * @param OrgCourses $orgCourses
     */
    public function setOrgCourses($orgCourses)
    {
        $this->orgCourses = $orgCourses;
    }

    /**
     * @return Person
     */
    public function getPersonStudent()
    {
        return $this->personStudent;
    }

    /**
     * @param Person $personStudent
     */
    public function setPersonStudent($personStudent)
    {
        $this->personStudent = $personStudent;
    }

    /**
     * @return string
     */
    public function getFailureRiskLevel()
    {
        return $this->failureRiskLevel;
    }

    /**
     * @param string $failureRiskLevel
     */
    public function setFailureRiskLevel($failureRiskLevel)
    {
        $this->failureRiskLevel = $failureRiskLevel;
    }

    /**
     * @return string
     */
    public function getInProgressGrade()
    {
        return $this->inProgressGrade;
    }

    /**
     * @param string $inProgressGrade
     */
    public function setInProgressGrade($inProgressGrade)
    {
        $this->inProgressGrade = $inProgressGrade;
    }

    /**
     * @return int
     */
    public function getAbsence()
    {
        return $this->absence;
    }

    /**
     * @param int $absence
     */
    public function setAbsence($absence)
    {
        $this->absence = $absence;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getFinalGrade()
    {
        return $this->finalGrade;
    }

    /**
     * @param string $finalGrade
     */
    public function setFinalGrade($finalGrade)
    {
        $this->finalGrade = $finalGrade;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * @param \DateTime $updateDate
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
    }

    /**
     * @return \DateTime
     */
    public function getFailureRiskLevelUpdateDate()
    {
        return $this->failureRiskLevelUpdateDate;
    }

    /**
     * @param \DateTime $failureRiskLevelUpdateDate
     */
    public function setFailureRiskLevelUpdateDate($failureRiskLevelUpdateDate)
    {
        $this->failureRiskLevelUpdateDate = $failureRiskLevelUpdateDate;
    }

    /**
     * @return \DateTime
     */
    public function getInProgressGradeUpdateDate()
    {
        return $this->inProgressGradeUpdateDate;
    }

    /**
     * @param \DateTime $inProgressGradeUpdateDate
     */
    public function setInProgressGradeUpdateDate($inProgressGradeUpdateDate)
    {
        $this->inProgressGradeUpdateDate = $inProgressGradeUpdateDate;
    }

    /**
     * @return \DateTime
     */
    public function getAbsenceUpdateDate()
    {
        return $this->absenceUpdateDate;
    }

    /**
     * @param \DateTime $absenceUpdateDate
     */
    public function setAbsenceUpdateDate($absenceUpdateDate)
    {
        $this->absenceUpdateDate = $absenceUpdateDate;
    }

    /**
     * @return \DateTime
     */
    public function getCommentUpdateDate()
    {
        return $this->commentUpdateDate;
    }

    /**
     * @param \DateTime $commentUpdateDate
     */
    public function setCommentUpdateDate($commentUpdateDate)
    {
        $this->commentUpdateDate = $commentUpdateDate;
    }

    /**
     * @return \DateTime
     */
    public function getFinalGradeUpdateDate()
    {
        return $this->finalGradeUpdateDate;
    }

    /**
     * @param \DateTime $finalGradeUpdateDate
     */
    public function setFinalGradeUpdateDate($finalGradeUpdateDate)
    {
        $this->finalGradeUpdateDate = $finalGradeUpdateDate;
    }
}