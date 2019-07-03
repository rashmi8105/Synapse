<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * PersonOrgMetadata
 *
 * @ORM\Table(name="person_org_metadata", indexes={@ORM\Index(name="fk_person_org_metadata_person1_idx", columns={"person_id"}), @ORM\Index(name="fk_person_org_metadata_org_metadata1_idx", columns={"org_metadata_id"}), @ORM\Index(name="fk_person_org_metadata_org_academic_year1_idx", columns={"org_academic_year_id"}), @ORM\Index(name="fk_person_org_metadata_org_academic_periods1_idx", columns={"org_academic_periods_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\PersonOrgMetaDataRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class PersonOrgMetadata extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="metadata_value", type="string", length=1024, precision=0, scale=0, nullable=true, unique=false)
     */
    private $metadataValue;



    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $person;

    /**
     * @var \Synapse\CoreBundle\Entity\OrgMetadata
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgMetadata")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_metadata_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $orgMetadata;

    /**
     * @var \Synapse\AcademicBundle\Entity\OrgAcademicYear
     *
     * @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicYear")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_academic_year_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $orgAcademicYear;

    /**
     * @var \Synapse\AcademicBundle\Entity\OrgAcademicTerms
     *
     * @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicTerms")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_academic_periods_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $orgAcademicPeriods;



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
     * Set metadataValue
     *
     * @param string $metadataValue
     * @return PersonOrgMetadata
     */
    public function setMetadataValue($metadataValue)
    {
        $this->metadataValue = $metadataValue;

        return $this;
    }

    /**
     * Get metadataValue
     *
     * @return string
     */
    public function getMetadataValue()
    {
        return $this->metadataValue;
    }


    /**
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person
     * @return PersonOrgMetadata
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set orgMetadata
     *
     * @param \Synapse\CoreBundle\Entity\OrgMetadata $orgMetadata
     * @return PersonOrgMetadata
     */
    public function setOrgMetadata(\Synapse\CoreBundle\Entity\OrgMetadata $orgMetadata = null)
    {
        $this->orgMetadata = $orgMetadata;

        return $this;
    }

    /**
     * Get orgMetadata
     *
     * @return \Synapse\CoreBundle\Entity\OrgMetadata
     */
    public function getOrgMetadata()
    {
        return $this->orgMetadata;
    }

    /**
     * Set orgAcademicYear
     *
     * @param \Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear
     * @return PersonOrgMetadata
     */
    public function setOrgAcademicYear(\Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear = null)
    {
        $this->orgAcademicYear = $orgAcademicYear;

        return $this;
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
     * Set orgAcademicPeriods
     *
     * @param \Synapse\AcademicBundle\Entity\OrgAcademicTerms $orgAcademicPeriods
     * @return PersonOrgMetadata
     */
    public function setOrgAcademicPeriods(\Synapse\AcademicBundle\Entity\OrgAcademicTerms $orgAcademicPeriods = null)
    {
        $this->orgAcademicPeriods = $orgAcademicPeriods;

        return $this;
    }

    /**
     * Get orgAcademicPeriods
     *
     * @return \Synapse\CoreBundle\Entity\OrgAcademicTerms
     */
    public function getOrgAcademicPeriods()
    {
        return $this->orgAcademicPeriods;
    }
}
