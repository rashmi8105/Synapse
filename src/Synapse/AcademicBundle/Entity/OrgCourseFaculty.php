<?php
namespace Synapse\AcademicBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * OrgCourseFaculty
 *
 * @ORM\Table(name="org_course_faculty", indexes={@ORM\Index(name="fk_course_faculty_organization1_idx", columns={"organization_id"}), @ORM\Index(name="fk_course_faculty_org_courses1_idx", columns={"org_courses_id"}), @ORM\Index(name="fk_course_faculty_person1_idx", columns={"person_id"}), @ORM\Index(name="fk_org_course_faculty_org_permissionset1_idx", columns={"org_permissionset_id"})})
 * @ORM\Entity(repositoryClass="Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgCourseFaculty extends BaseEntity
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
     *
     * @var \Synapse\CoreBundle\Entity\OrgPermissionset @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgPermissionset")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_permissionset_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgPermissionset;

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
     * @return Course
     */
    public function setCourse(\Synapse\AcademicBundle\Entity\OrgCourses $course = null)
    {
        $this->course = $course;
        return $this;
    }

    /**
     * Get OrgCourses
     *
     * @return \Synapse\AcademicBundle\Entity\OrgCourses
     */
    public function getCourse()
    {
        return $this->course;
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
     * Get person
     *
     * @return Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set orgPermissionset
     *
     * @param \Synapse\CoreBundle\Entity\OrgPermissionset $orgPermissionset            
     * @return OrgPermissionsetMetadata
     */
    public function setOrgPermissionset(\Synapse\CoreBundle\Entity\OrgPermissionset $orgPermissionset = null)
    {
        $this->orgPermissionset = $orgPermissionset;
        return $this;
    }

    /**
     * Get orgPermissionset
     *
     * @return \Synapse\CoreBundle\Entity\OrgPermissionset
     */
    public function getOrgPermissionset()
    {
        return $this->orgPermissionset;
    }
}