<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;


/**
 * PersonEbiMetadata
 *
 * @ORM\Table(name="person_ebi_metadata", indexes={@ORM\Index(name="fk_person_metadata_person1_idx", columns={"person_id"}), @ORM\Index(name="fk_person_metadata_metadata_master1_idx", columns={"ebi_metadata_id"}), @ORM\Index(name="fk_person_metadata_org_academic_year1_idx", columns={"org_academic_year_id"}), @ORM\Index(name="fk_person_metadata_org_academic_periods1_idx", columns={"org_academic_terms_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class PersonEbiMetadata extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="metadata_value", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $metadataValue;

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
     * @var \Synapse\CoreBundle\Entity\EbiMetadata @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\EbiMetadata")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_metadata_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $ebiMetadata;

    /**
     *
     * @var \Synapse\AcademicBundle\Entity\OrgAcademicYear @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicYear")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_academic_year_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgAcademicYear;

    /**
     *
     * @var \Synapse\AcademicBundle\Entity\OrgAcademicTerms @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicTerms")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_academic_terms_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgAcademicTerms;

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
     * @return PersonEbiMetadata
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
     * @return PersonEbiMetadata
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
     * Set ebiMetadata
     *
     * @param \Synapse\CoreBundle\Entity\EbiMetadata $ebiMetadata            
     * @return PersonEbiMetadata
     */
    public function setEbiMetadata(\Synapse\CoreBundle\Entity\EbiMetadata $ebiMetadata = null)
    {
        $this->ebiMetadata = $ebiMetadata;
        
        return $this;
    }

    /**
     * Get ebiMetadata
     *
     * @return \Synapse\CoreBundle\Entity\EbiMetadata
     */
    public function getEbiMetadata()
    {
        return $this->ebiMetadata;
    }

    /**
     * Set orgAcademicYear
     *
     * @param \Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear            
     * @return PersonEbiMetadata
     */
    public function setOrgAcademicYear(\Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear = null)
    {
        $this->orgAcademicYear = $orgAcademicYear;
        
        return $this;
    }

    /**
     * Get orgAcademicYear
     *
     * @return \Synapse\CoreBundle\Entity\OrgAcademicYear
     */
    public function getOrgAcademicYear()
    {
        return $this->orgAcademicYear;
    }

    /**
     * Set orgAcademicTerms
     *
     * @param \Synapse\AcademicBundle\Entity\OrgAcademicTerms $orgAcademicTerms            
     * @return PersonEbiMetadata
     */
    public function setOrgAcademicTerms(\Synapse\AcademicBundle\Entity\OrgAcademicTerms $orgAcademicTerms = null)
    {
        $this->orgAcademicTerms = $orgAcademicTerms;
        
        return $this;
    }

    /**
     * Get orgAcademicTerms
     *
     * @return \Synapse\CoreBundle\Entity\OrgAcademicTerms
     */
    public function getOrgAcademicTerms()
    {
        return $this->orgAcademicTerms;
    }
}
