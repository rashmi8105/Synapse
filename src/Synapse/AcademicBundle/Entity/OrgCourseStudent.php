<?php
namespace Synapse\AcademicBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OrgCourseStudent
 *
 * @ORM\Table(name="org_course_student", indexes={@ORM\Index(name="fk_course_student_organization1_idx", columns={"organization_id"}), @ORM\Index(name="fk_course_student_org_courses1_idx", columns={"org_courses_id"}), @ORM\Index(name="fk_course_student_person1_idx", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\AcademicBundle\Repository\OrgCourseStudentRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"course", "person"}, errorPath="person_id", message="Person already exists in course.", groups={"required"}))
 */
class OrgCourseStudent extends BaseEntity
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
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $organization;

    /**
     *
     * @var \Synapse\AcademicBundle\Entity\OrgCourses @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgCourses")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_courses_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $course;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $person;

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
     * Set Organization
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
     * Get Organization
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set OrgCourses
     *
     * @param \Synapse\AcademicBundle\Entity\OrgCourses $course            
     * @return OrgCourses
     */
    public function setCourse(\Synapse\AcademicBundle\Entity\OrgCourses $course = null)
    {
        $this->course = $course;
        return $this;
    }

    /**
     * Get OrgCourses
     *
     * @return \Synapse\AcademicBundle\Entity\Courses
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return Appointments
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;
        return $this;
    }

    /**
     * Get person
     *
     * @return Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }
}