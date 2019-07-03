<?php
namespace Synapse\AcademicUpdateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AcademicUpdateRequestCourse
 *
 * @ORM\Table(name="academic_update_request_course", indexes={@ORM\Index(name="fk_academic_update_request_course_org_courses1_idx", columns={"org_courses_id"}), @ORM\Index(name="fk_academic_update_request_course_organization1_idx", columns={"org_id"}), @ORM\Index(name="fk_academic_update_request_course_academic_update_request1_idx", columns={"academic_update_request_id"})})
 * @ORM\Entity(repositoryClass="Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestCourseRepository")
 */
class AcademicUpdateRequestCourse
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
     * @var \OrgCourses @ORM\OneToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgCourses")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_courses_id", referencedColumnName="id")
     *      })
     */
    private $orgCourses;

    /**
     *
     * @var \Organization @ORM\OneToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     *      })
     */
    private $org;

    /**
     *
     * @var \AcademicUpdateRequest 
     *      
     *      @ORM\OneToOne(targetEntity="Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="academic_update_request_id", referencedColumnName="id")
     *      })
     */
    private $academicUpdateRequest;

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
     * Set orgCourses
     *
     * @param \Synapse\AcademicBundle\Entity\OrgCourses $orgCourses            
     * @return AcademicUpdateRequestCourse
     */
    public function setOrgCourses(\Synapse\AcademicBundle\Entity\OrgCourses $orgCourses)
    {
        $this->orgCourses = $orgCourses;
        
        return $this;
    }

    /**
     * Get orgCourses
     *
     * @return \Synapse\AcademicUpdateBundle\Entity\OrgCourses
     */
    public function getOrgCourses()
    {
        return $this->orgCourses;
    }

    /**
     * Set org
     *
     * @param \Synapse\CoreBundle\Entity\Organization $org            
     * @return AcademicUpdateRequestCourse
     */
    public function setOrg(\Synapse\CoreBundle\Entity\Organization $org)
    {
        $this->org = $org;
        
        return $this;
    }

    /**
     * Get org
     *
     * @return \Synapse\AcademicUpdateBundle\Entity\Organization
     */
    public function getOrg()
    {
        return $this->org;
    }

    /**
     * Set academicUpdateRequest
     *
     * @param \Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest $academicUpdateRequest            
     * @return AcademicUpdateRequestCourse
     */
    public function setAcademicUpdateRequest(\Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest $academicUpdateRequest)
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
}
