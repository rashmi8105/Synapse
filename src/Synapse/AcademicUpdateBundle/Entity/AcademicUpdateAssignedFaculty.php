<?php

namespace Synapse\AcademicUpdateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * AcademicUpdateAssignedFaculty
 *
 * @ORM\Table(name="academic_update_assigned_faculty", indexes={@ORM\Index(name="fk_academic_update_assigned_faculty_person1_idx", columns={"person_id_faculty_assigned"}), @ORM\Index(name="fk_academic_update_assigned_faculty_organization1_idx", columns={"org_id"}), @ORM\Index(name="fk_academic_update_assigned_faculty_academic_update1_idx", columns={"academic_update_id"})})
 * @ORM\Entity
 */
class AcademicUpdateAssignedFaculty
{
    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var \Person
     *
     * 
     * 
     * @ORM\OneToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id_faculty_assigned", referencedColumnName="id")
     * })
     */
    private $personFacultyAssigned;

    /**
     * @var \Organization
     *
     * 
     * )
     * @ORM\OneToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     * })
     */
    private $org;

    /**
     * @var \AcademicUpdate
     *
     * 
     * 
     * @ORM\OneToOne(targetEntity="Synapse\AcademicUpdateBundle\Entity\AcademicUpdate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="academic_update_id", referencedColumnName="id")
     * })
     */
    private $academicUpdate;

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
     * Set personFacultyAssigned
     *
     * @param \Synapse\CoreBundle\Entity\Person $personFacultyAssigned
     * @return AcademicUpdateAssignedFaculty
     */
    public function setPersonFacultyAssigned(\Synapse\CoreBundle\Entity\Person $personFacultyAssigned)
    {
        $this->personFacultyAssigned = $personFacultyAssigned;

        return $this;
    }

    /**
     * Get personFacultyAssigned
     *
     * @return \Synapse\AcademicUpdateBundle\Entity\Person 
     */
    public function getPersonFacultyAssigned()
    {
        return $this->personFacultyAssigned;
    }

    /**
     * Set org
     *
     * @param \Synapse\CoreBundle\Entity\Organization $org
     * @return AcademicUpdateAssignedFaculty
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
     * Set academicUpdate
     *
     * @param \Synapse\AcademicUpdateBundle\Entity\AcademicUpdate $academicUpdate
     * @return AcademicUpdateAssignedFaculty
     */
    public function setAcademicUpdate(\Synapse\AcademicUpdateBundle\Entity\AcademicUpdate $academicUpdate)
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
}
