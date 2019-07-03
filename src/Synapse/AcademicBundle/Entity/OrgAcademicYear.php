<?php
namespace Synapse\AcademicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\Organization;

/**
 * OrgAcademicYear
 *
 * @ORM\Table(name="org_academic_year", indexes={@ORM\Index(name="relationship9", columns={"organization_id"}), @ORM\Index(name="fk_org_academic_year_year1_idx", columns={"year_id"})})
 * @ORM\Entity(repositoryClass="Synapse\AcademicBundle\Repository\OrgAcademicYearRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"organization","startDate","endDate"},message="Academic year should not overlap with other years for same organization.", ignoreNull=false)
 */
class OrgAcademicYear extends BaseEntity
{

    /**
     * Mapworks year id
     *
     * @var integer
     *
     *      @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Object representation of the organization that contains the academic year
     *
     * @var Organization
     *
     *      @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $organization;

    /**
     * Academic year's name
     *
     * @var string
     *
     *      @ORM\Column(name="name", type="string", length=120, nullable=true)
     *      @JMS\Expose
     */
    private $name;

    /**
     * Organization specific year id
     *
     * @var Year
     *
     *      @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\Year")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="year_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $yearId;

    /**
     * Date that an organization's academic year starts
     *
     * @var Date
     *
     *      @ORM\Column(name="start_date", type="date", nullable=true)
     *      @JMS\Expose
     */
    private $startDate;

    /**
     * Date that an organization's academic year ends
     *
     * @var Date
     *
     *      @ORM\Column(name="end_date", type="date", nullable=true)
     *      @JMS\Expose
     */
    private $endDate;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Organization $organization
     */
    public function setOrganization(Organization $organization = null)
    {
        $this->organization = $organization;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return Date
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return Date
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param Year $yearId
     */
    public function setYearId(Year $yearId = null)
    {
        $this->yearId = $yearId;
    }

    /**
     * @return Year
     */
    public function getYearId()
    {
        return $this->yearId;
    }
}