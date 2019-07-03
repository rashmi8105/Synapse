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
 * OrgPersonStudentRetentionTrackingGroup
 *
 * @ORM\Table(name="org_person_student_retention_tracking_group",uniqueConstraints={@UniqueConstraint(name="unique_osprtg_idx", columns={"organization_id", "person_id", "org_academic_year_id","deleted_at"})})
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @JMS\ExclusionPolicy("all")
 */
class OrgPersonStudentRetentionTrackingGroup extends BaseEntity
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
     *  @ORM\JoinColumn(name="organization_id", referencedColumnName="id",nullable=false)
     * })
     */
    private $organization;


    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="person_id", referencedColumnName="id",nullable=false)
     * })
     * @JMS\Expose
     */
    private $person;


    /**
     * @var \Synapse\AcademicBundle\Entity\OrgAcademicYear
     * @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicYear")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_academic_year_id", referencedColumnName="id",nullable=false)
     *      })
     * @JMS\Expose
     */
    private $orgAcademicYear;


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

}