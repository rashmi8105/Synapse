<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgCohortName
 *
 * @ORM\Table(name="org_cohort_name", indexes={@ORM\Index(name="unique_cohort",columns={"organization_id","org_academic_year_id", "cohort"}),@ORM\Index(name="fk_org_academic_year_id_idx",columns={"org_academic_year_id"}),@ORM\Index(name="fk_organization_id_idx",columns={"organization_id"})})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgCohortNameRepository")
 * @JMS\ExclusionPolicy("all")
 */
class OrgCohortName extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $organization;

    /**
     * @var \Synapse\AcademicBundle\Entity\OrgAcademicYear
     * @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicYear")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_academic_year_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $orgAcademicYear;

    /**
     * @var integer
     *
     * @ORM\Column(name="cohort", type="integer", nullable=false)
     * @JMS\Expose
     */
    private $cohort;

    /**
     * @var string
     *
     * @ORM\Column(name="cohort_name", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $cohortName;


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
     *
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
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
     * Set orgAcademicYear
     *
     * @param \Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear
     *
     */
    public function setOrgAcademicYear($orgAcademicYear)
    {
        $this->orgAcademicYear = $orgAcademicYear;
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
     * Set cohort
     *
     * @param integer $cohort
     *
     */
    public function setCohort($cohort)
    {
        $this->cohort = $cohort;

    }

    /**
     * Get cohort
     *
     * @return integer
     */
    public function getCohort()
    {
        return $this->cohort;
    }

    /**
     * Set cohortName
     *
     * @param string $cohortName
     *
     */
    public function setCohortName($cohortName)
    {
        $this->cohortName = $cohortName;

    }

    /**
     * Get cohortName
     *
     * @return string
     */
    public function getCohortName()
    {
        return $this->cohortName;
    }
}

