<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OrganizationLang
 *
 * @ORM\Table(name="organization_lang", indexes={@ORM\Index(name="orglang_langid", columns={"lang_id"}), @ORM\Index(name="orglang_organizationid", columns={"organization_id"})})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrganizationlangRepository")
 * @UniqueEntity(fields={"organizationName"},message="Name already exists.")
 */
class OrganizationLang extends BaseEntity
{

    /**
     *
     * @var string @ORM\Column(name="organization_name", type="string", length=255, nullable=true)
     *      @Assert\Regex("/^[A-Za-z][A-Za-z0-9 .,]+$/", message="Name cannot contain special characters")
     */
    private $organizationName;

    /**
     *
     * @var string @ORM\Column(name="nick_name", type="string", length=45, nullable=true)
     *     
     */
    private $nickName;

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     *      })
     */
    private $organization;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\LanguageMaster @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     *      })
     */
    private $lang;

    /**
     *
     * @var string @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * Set organizationName
     *
     * @param string $organizationName            
     * @return OrganizationLang
     */
    public function setOrganizationName($organizationName)
    {
        $this->organizationName = $organizationName;
        
        return $this;
    }

    /**
     * Get organizationName
     *
     * @return string
     */
    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    /**
     * Set nickName
     *
     * @param string $nickName            
     * @return OrganizationLang
     */
    public function setNickName($nickName)
    {
        $this->nickName = $nickName;
        
        return $this;
    }

    /**
     * Get nickName
     *
     * @return string
     */
    public function getNickName()
    {
        return $this->nickName;
    }

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
     * @return OrganizationLang
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
     * Set lang
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $lang            
     * @return OrganizationLang
     */
    public function setLang(\Synapse\CoreBundle\Entity\LanguageMaster $lang = null)
    {
        $this->lang = $lang;
        
        return $this;
    }

    /**
     * Get lang
     *
     * @return \Synapse\CoreBundle\Entity\LanguageMaster
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set description
     *
     * @return OrganizationLang
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     */
    public function getDescription()
    {
        return $this->description;
    }
}
