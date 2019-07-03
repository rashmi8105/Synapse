<?php
namespace Synapse\HelpBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgDocuments
 *
 * @ORM\Table(name="org_documents", indexes={@ORM\Index(name="fk_org_documents_organization1_idx", columns={"org_id"})})
 * @ORM\Entity(repositoryClass="Synapse\HelpBundle\Repository\OrgDocumentsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgDocuments extends BaseEntity
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
     * @var \Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", nullable=false, referencedColumnName="id")
     *      })
     */
    private $orgId;

    /**
     *
     * @var string @ORM\Column(name="title", type="string", length=80, nullable=true)
     */
    private $title;

    /**
     *
     * @var string @ORM\Column(name="description", type="string", length=140, nullable=true)
     */
    private $description;

    /**
     *
     * @var enum @ORM\Column(name="type", type="string", columnDefinition="enum('link', 'file')")
     */
    private $type;

    /**
     *
     * @var string @ORM\Column(name="link", type="string", length=400, nullable=true)
     */
    private $link;

    /**
     *
     * @var string @ORM\Column(name="file_path", type="string", length=200, nullable=true)
     */
    private $filePath;

    /**
     *
     * @var string @ORM\Column(name="display_filename", type="string", length=200, nullable=true)
     */
    private $displayFilename;

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
     * Set orgId
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OrgDocuments
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization)
    {
        $this->orgId = $organization;
        
        return $this;
    }

    /**
     * Get orgId
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->orgId;
    }

    /**
     * Set title
     *
     * @param string $title            
     * @return OrgDocuments
     */
    public function setTitle($title)
    {
        $this->title = $title;
        
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description            
     * @return OrgDocuments
     */
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @param string $type            
     * @return OrgDocuments
     */
    public function setType($type)
    {
        $this->type = $type;
        
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set link
     *
     * @param string $link            
     * @return OrgDocuments
     */
    public function setLink($link)
    {
        $this->link = $link;
        
        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set filePath
     *
     * @param string $filePath            
     * @return OrgDocuments
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        
        return $this;
    }

    /**
     * Get filePath
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Set displayFilename
     *
     * @param string $displayFilename            
     * @return OrgDocuments
     */
    public function setDisplayFilename($displayFilename)
    {
        $this->displayFilename = $displayFilename;
        
        return $this;
    }

    /**
     * Get displayFilename
     *
     * @return string
     */
    public function getDisplayFilename()
    {
        return $this->displayFilename;
    }
}