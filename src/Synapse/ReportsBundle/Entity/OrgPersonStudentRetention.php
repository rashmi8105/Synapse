<?php
namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\AcademicBundle\Entity\OrgAcademicYear;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * OrgPersonStudentRetention
 *
 * @ORM\Table(name="org_person_student_retention",uniqueConstraints={@UniqueConstraint(name="unique_opsr_idx", columns={"organization_id", "person_id", "org_academic_year_id","deleted_at"})})
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @JMS\ExclusionPolicy("all")
 */
class OrgPersonStudentRetention extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=false)
     * })
     * @JMS\Expose
     */
    private $organization;


    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=false)
     * })
     * @JMS\Expose
     */
    private $person;


    /**
     * @var \Synapse\AcademicBundle\Entity\OrgAcademicYear
     * @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicYear")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_academic_year_id", referencedColumnName="id", nullable=false)
     *      })
     * @JMS\Expose
     */
    private $orgAcademicYear;


    /**
     * @var integer
     * @ORM\Column(name="is_enrolled_beginning_year", type="boolean", length=1, nullable=true)
     * @JMS\Expose
     */

    private $isEnrolledBeginningYear;


    /**
     * @var integer
     * @ORM\Column(name="is_enrolled_midyear", type="boolean", length=1, nullable=true)
     * @JMS\Expose
     */
    private $isEnrolledMidyear;

    /**
     * @var integer
     * @ORM\Column(name="is_degree_completed", type="boolean", length=1, nullable=true)
     * @JMS\Expose
     */
    private $isDegreeCompleted;


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
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return OrgAcademicYear
     */
    public function getOrgAcademicYear()
    {
        return $this->orgAcademicYear;
    }

    /**
     * @return integer
     */
    public function getEnrolledBeginningYear()
    {
        return $this->isEnrolledBeginningYear;
    }

    /**
     * @return integer
     */
    public function getEnrolledMidyear()
    {
        return $this->isEnrolledMidyear;
    }

    /**
     * @return integer
     */
    public function getIsDegreeCompleted()
    {
        return $this->isDegreeCompleted;
    }

    /**
     * @param Organization $organization
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * @param OrgAcademicYear $orgAcademicYear
     */
    public function setOrgAcademicYear(OrgAcademicYear $orgAcademicYear)
    {
        $this->orgAcademicYear = $orgAcademicYear;
    }

    /**
     * @param Person $person
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;
    }

    /**
     * @param integer $isEnrolledBeginningYear
     */
    public function setEnrolledBeginningYear($isEnrolledBeginningYear)
    {
        $this->isEnrolledBeginningYear = $isEnrolledBeginningYear;
    }

    /**
     * @param integer $isEnrolledMidyear
     */
    public function setEnrolledMidyear($isEnrolledMidyear)
    {
        $this->isEnrolledMidyear = $isEnrolledMidyear;
    }

    /**
     * @param integer $isDegreeCompleted
     */
    public function setIsDegreeCompleted($isDegreeCompleted)
    {
        $this->isDegreeCompleted = $isDegreeCompleted;
    }

}