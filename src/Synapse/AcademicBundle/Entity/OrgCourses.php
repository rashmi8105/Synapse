<?php
namespace Synapse\AcademicBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * OrgCourses
 *
 * @ORM\Table(name="org_courses", indexes={@ORM\Index(name="fk_org_courses_organization1_idx", columns={"organization_id"}), @ORM\Index(name="fk_org_courses_org_academic_year1_idx", columns={"org_academic_year_id"}), @ORM\Index(name="fk_org_courses_org_academic_terms1_idx", columns={"org_academic_terms_id"}),@ORM\Index(name="idx_year", columns={"org_academic_year_id"}),@ORM\Index(name="idx_term", columns={"org_academic_terms_id"}),@ORM\Index(name="idx_college", columns={"college_code"}),@ORM\Index(name="idx_dept", columns={"dept_code"})}
 ,uniqueConstraints = {@ORM\UniqueConstraint(name="uniquecoursesectionid", columns={"organization_id","course_section_id"})})
 * @ORM\Entity(repositoryClass="Synapse\AcademicBundle\Repository\OrgCoursesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"organization", "courseSectionId"}, errorPath="courseSectionId", message="Course Section Id already exists.", groups={"required", "Default"})
 */
class OrgCourses extends BaseEntity
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
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=false)
     *      })
     */
    private $organization;

    /**
     *
     * @var \Synapse\AcademicBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicYear")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_academic_year_id", referencedColumnName="id", nullable=false)
     *      })
     * @Assert\NotNull(groups={"required"}, message = "Year Id cannot be empty.")
     * @Assert\NotBlank(groups={"required"}, message = "Year Id cannot be empty.")
     */
    private $orgAcademicYear;

    /**
     *
     * @var \Synapse\AcademicBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicTerms")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_academic_terms_id", referencedColumnName="id", nullable=false)
     *      })
     * @Assert\NotNull(groups={"required"}, message = "Term Id cannot be empty.")
     * @Assert\NotBlank(groups={"required"}, message = "Term Id cannot be empty.")
     */
    private $orgAcademicTerms;

    /**
     *
     * @var string @ORM\Column(name="course_section_id", type="string", length=50, nullable=true)
     * @Assert\Length(max=50, groups={"required", "Default"}, maxMessage = "Course Section cannot be longer than {{ limit }} characters");
     * @Assert\NotNull(groups={"required"}, message = "Course Section cannot be empty.")
     * @Assert\NotBlank(groups={"required"}, message = "Course Section cannot be empty.")
     */
    private $courseSectionId;

    /**
     *
     * @var string @ORM\Column(name="college_code", type="string", length=10, nullable=true)
     * @Assert\Length(max=10, groups={"required", "Default"}, maxMessage = "College Code cannot be longer than {{ limit }} characters");
     * @Assert\NotNull(groups={"required"}, message = "College Code cannot be empty.")
     * @Assert\NotBlank(groups={"required"}, message = "College Code cannot be empty.")
     */
    private $collegeCode;

    /**
     *
     * @var string @ORM\Column(name="dept_code", type="string", length=10, nullable=true)
     * @Assert\Length(max=10, groups={"required", "Default"}, maxMessage = "Dept Code cannot be longer than {{ limit }} characters");
     * @Assert\NotNull(groups={"required"}, message = "Department Code cannot be empty.")
     * @Assert\NotBlank(groups={"required"}, message = "Department Code cannot be empty.")
     */
    private $deptCode;

    /**
     *
     * @var string @ORM\Column(name="subject_code", type="string", length=10, nullable=true)
     * @Assert\Length(max=10, groups={"required", "Default"}, maxMessage = "Subject Code cannot be longer than {{ limit }} characters");
     * @Assert\NotNull(groups={"required"}, message = "Subject Code cannot be empty.")
     * @Assert\NotBlank(groups={"required"}, message = "Subject Code cannot be empty.")
     */
    private $subjectCode;

    /**
     *
     * @var string @ORM\Column(name="course_number", type="string", length=10, nullable=true)
     * @Assert\Length(max=10, groups={"required", "Default"}, maxMessage = "Course Number cannot be longer than {{ limit }} characters");
     * @Assert\NotNull(groups={"required"}, message = "Course Number cannot be empty.")
     * @Assert\NotBlank(groups={"required"}, message = "Course Number cannot be empty.")
     */
    private $courseNumber;

    /**
     *
     * @var string @ORM\Column(name="course_name", type="string", length=200, nullable=true)
     * @Assert\Length(max=200, groups={"required", "Default"}, maxMessage = "Course Name cannot be longer than {{ limit }} characters");
     * @Assert\NotNull(groups={"required"}, message = "Course Name cannot be empty.")
     * @Assert\NotBlank(groups={"required"}, message = "Course Name cannot be empty.")
     */
    private $courseName;

    /**
     *
     * @var string @ORM\Column(name="section_number", type="string", length=10, nullable=true)
     * @Assert\Length(max=10, groups={"required", "Default"}, maxMessage = "Section Number cannot be longer than {{ limit }} characters");
     * @Assert\NotNull(groups={"required"}, message = "Section Number cannot be empty.")
     * @Assert\NotBlank(groups={"required"}, message = "Section Number cannot be empty.")
     */
    private $sectionNumber;

    /**
     *
     * @var string @ORM\Column(name="days_times", type="string", length=45, nullable=true)
     * @Assert\Length(max=45,maxMessage = "Day Times cannot be longer than {{ limit }} characters");
     */
    private $daysTimes;

    /**
     *
     * @var string @ORM\Column(name="location", type="string", length=45, nullable=true)
     * @Assert\Length(max=45,maxMessage = "Location cannot be longer than {{ limit }} characters");
     */
    private $location;

    /**
     *
     * @var string @ORM\Column(name="credit_hours", type="decimal", precision=5, scale=2, nullable=true)
     * @Assert\Range(
     *     min = 0,
     *     max = 40,
     *     invalidMessage = "Credit hours must be a number",
     *     minMessage = "Credit hours must be more than 0",
     *     maxMessage = "Credit hours must be less than 40"
     * );
     */
    private $creditHours;

    /**
     *
     * @var string @ORM\Column(name="externalId", type="string", length=50, nullable=true)
     * @Assert\Length(max=50,maxMessage = "External Id cannot be longer than {{ limit }} characters");
     *
     */
    private $externalId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest", mappedBy="orgCourses")
     */
    private $academicUpdateRequest;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->academicUpdateRequest = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OrgCourses
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
     * Set $orgAcademicYear
     *
     * @param \Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear
     * @return OrgCourses
     */
    public function setOrgAcademicYear(\Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear = null)
    {
        $this->orgAcademicYear = $orgAcademicYear;

        return $this;
    }

    /**
     * Get orgAcademicYear
     *
     * @return \Synapse\AcademicBundle\Entity\OrgAcademicYear
     */
    public function getOrgAcademicYear()
    {
        return $this->orgAcademicYear;
    }

    /**
     * Set orgAcademicTerms
     *
     * @param \Synapse\AcademicBundle\Entity\OrgAcademicTerms $orgAcademicTerms
     * @return OrgCourses
     */
    public function setOrgAcademicTerms(\Synapse\AcademicBundle\Entity\OrgAcademicTerms $orgAcademicTerms = null)
    {
        $this->orgAcademicTerms = $orgAcademicTerms;

        return $this;
    }

    /**
     * Get orgAcademicTerms
     *
     * @return \Synapse\CoreBundle\Entity\OrgAcademicTerms
     */
    public function getOrgAcademicTerms()
    {
        return $this->orgAcademicTerms;
    }

    /**
     * Set courseSectionId
     *
     * @param string $courseSectionId
     * @return OrgCourses
     */
    public function setCourseSectionId($courseSectionId)
    {
        $this->courseSectionId = $courseSectionId;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getCourseSectionId()
    {
        return $this->courseSectionId;
    }

    /**
     * Set collegeCode
     *
     * @param string $collegeCode
     * @return OrgCourses
     */
    public function setCollegeCode($collegeCode)
    {
        $this->collegeCode = $collegeCode;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getCollegeCode()
    {
        return $this->collegeCode;
    }

    /**
     * Set deptCode
     *
     * @param string $deptCode
     * @return OrgCourses
     */
    public function setDeptCode($deptCode)
    {
        $this->deptCode = $deptCode;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getDeptCode()
    {
        return $this->deptCode;
    }

    /**
     * Set subjectCode
     *
     * @param string $subjectCode
     * @return OrgCourses
     */
    public function setSubjectCode($subjectCode)
    {
        $this->subjectCode = $subjectCode;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getSubjectCode()
    {
        return $this->subjectCode;
    }

    /**
     * Set courseNumber
     *
     * @param string $courseNumber
     * @return OrgCourses
     */
    public function setCourseNumber($courseNumber)
    {
        $this->courseNumber = $courseNumber;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getCourseNumber()
    {
        return $this->courseNumber;
    }

    /**
     * Set courseNumber
     *
     * @param string $courseName
     * @return OrgCourses
     */
    public function setCourseName($courseName)
    {
        $this->courseName = $courseName;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getCourseName()
    {
        return $this->courseName;
    }

    /**
     * Set sectionNumber
     *
     * @param string $sectionNumber
     * @return OrgCourses
     */
    public function setSectionNumber($sectionNumber)
    {
        $this->sectionNumber = $sectionNumber;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getSectionNumber()
    {
        return $this->sectionNumber;
    }

    /**
     * Set daysTimes
     *
     * @param string $daysTimes
     * @return OrgCourses
     */
    public function setDaysTimes($daysTimes)
    {
        $this->daysTimes = $daysTimes;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getDaysTimes()
    {
        return $this->daysTimes;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return OrgCourses
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set creditHours
     *
     * @param string $creditHours
     * @return OrgCourses
     */
    public function setCreditHours($creditHours)
    {
        $this->creditHours = $creditHours;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getCreditHours()
    {
        return $this->creditHours;
    }

    /**
     * Set externalId
     *
     * @param string $externalId
     * @return OrgCourses
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
     * Add academicUpdateRequest
     *
     * @param \Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest $academicUpdateRequest
     * @return OrgCourses
     */
    public function addAcademicUpdateRequest(\Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest $academicUpdateRequest)
    {
        $this->academicUpdateRequest[] = $academicUpdateRequest;

        return $this;
    }

    /**
     * Remove academicUpdateRequest
     *
     * @param \Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest $academicUpdateRequest
     */
    public function removeAcademicUpdateRequest(\Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest $academicUpdateRequest)
    {
        $this->academicUpdateRequest->removeElement($academicUpdateRequest);
    }

    /**
     * Get academicUpdateRequest
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAcademicUpdateRequest()
    {
        return $this->academicUpdateRequest;
    }
}