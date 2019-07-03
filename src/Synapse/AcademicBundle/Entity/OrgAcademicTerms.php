<?php
namespace Synapse\AcademicBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Synapse\CoreBundle\Entity\BaseEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * OrgAcademicTerms
 *
 * @ORM\Table(name="org_academic_terms", indexes={@ORM\Index(name="fk_academicperiod_organizationid", columns={"organization_id"}), @ORM\Index(name="fk_academicperiod_academicyearid", columns={"org_academic_year_id"})})
 * @ORM\Entity(repositoryClass="Synapse\AcademicBundle\Repository\OrgAcademicTermRepository")
 * @UniqueEntity(fields={"termCode", "orgAcademicYearId"},message="Term Id already exists.")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgAcademicTerms extends BaseEntity
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
     * @var \Synapse\AcademicBundle\Entity\OrgAcademicYear @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicYear")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_academic_year_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgAcademicYearId;

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
     * @var string @ORM\Column(name="name", type="string", length=120, nullable=true)
     */
    private $name;

    /**
     *
     * @var string @ORM\Column(name="term_code", type="string", length=10, nullable=true)
     *      @Assert\Length(max="10",maxMessage = "Term ID cannot be longer than {{ limit }} characters")
     */
    private $termCode;

    /**
     *
     * @var \Date @ORM\Column(name="start_date", type="date", nullable=true)
     */
    private $startDate;

    /**
     *
     * @var \Date @ORM\Column(name="end_date", type="date", nullable=true)
     */
    private $endDate;

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
     * Set orgAcademicYearId
     *
     * @param \Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYearId            
     * @return OrgAcademicTerms
     */
    public function setOrgAcademicYear(\Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYearId = null)
    {
        $this->orgAcademicYearId = $orgAcademicYearId;
        
        return $this;
    }

    /**
     * Get $orgAcademicYearId
     *
     * @return \Synapse\AcademicBundle\Entity\OrgAcademicYear
     */
    public function getOrgAcademicYear()
    {
        return $this->orgAcademicYearId;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     * @return OrgAcademicTerms
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
     * Set name
     *
     * @param string $name            
     * @return OrgAcademicTerms
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set termCode
     *
     * @param string $termCode            
     * @return OrgAcademicTerms
     */
    public function setTermCode($termCode)
    {
        $this->termCode = $termCode;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getTermCode()
    {
        return $this->termCode;
    }

    /**
     *
     * @param \Date $startDate            
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     *
     * @return \Date
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     *
     * @param \Date $endDate            
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     *
     * @return \Date
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}