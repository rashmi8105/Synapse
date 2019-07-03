<?php
namespace Synapse\AcademicUpdateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AcademicUpdateRequestStudent
 *
 * @ORM\Table(name="academic_update_request_student", indexes={@ORM\Index(name="fk_academic_update_request_student_person1_idx", columns={"person_id"}), @ORM\Index(name="fk_academic_update_request_student_organization1_idx", columns={"org_id"}), @ORM\Index(name="fk_academic_update_request_student_academic_update_request1_idx", columns={"academic_update_request_id"})})
 * @ORM\Entity
 */
class AcademicUpdateRequestStudent
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
     * @var \Person 
     *      
     *      @ORM\OneToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     *      })
     */
    private $person;

    /**
     *
     * @var \Organization 
     *      
     *      @ORM\OneToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
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
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return AcademicUpdateRequestStudent
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person)
    {
        $this->person = $person;
        
        return $this;
    }

    /**
     * Get person
     *
     * @return \Synapse\AcademicUpdateBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set org
     *
     * @param \Synapse\CoreBundle\Entity\Organization $org            
     * @return AcademicUpdateRequestStudent
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
     * @return AcademicUpdateRequestStudent
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
