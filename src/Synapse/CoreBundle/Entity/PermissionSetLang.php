<?php
namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * PermissionSetLang
 * @ORM\Table(name="ebi_permissionset_lang", indexes={@ORM\Index(name="ebi_permissionset_lang_language_master1_idx", columns={"language_id"}), @ORM\Index(name="ebi_permissionset_lang_ebi_permissionset1_idx", columns={"ebi_permissionset_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\PermissionSetLangRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"permissionsetName"},message="Permission Template Name already exists.")
 */
class PermissionSetLang extends BaseEntity
{

    /**
     *
     * @var string @Assert\NotBlank()
     *      @ORM\Column(name="permissionset_name", type="string", length=100, nullable=true)
     *      @Assert\NotBlank()
     *       @Assert\Length(max=100,maxMessage = "Permission Template Name cannot be longer than {{ limit }} characters");
     */
    private $permissionsetName;

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\LanguageMaster @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     *      })
     *      @Assert\NotBlank()
     */
    private $lang;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\PermissionSet @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\PermissionSet")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_permissionset_id", referencedColumnName="id")
     *      })
     *      @Assert\NotBlank()
     */
    private $ebiPermissionSet;

    /**
     * Set permissionsetName
     *
     * @param string $permissionsetName            
     * @return Permissionset
     */
    public function setPermissionsetName($permissionsetName)
    {
        $this->permissionsetName = $permissionsetName;
        
        return $this;
    }

    /**
     * Get permissionsetName
     *
     * @return string
     */
    public function getPermissionsetName()
    {
        return $this->permissionsetName;
    }

    /**
     * Set lang
     *
     * @param int $lang            
     * @return Permissionset
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
        
        return $this;
    }

    /**
     * Get lang
     *
     * @return int
     */
    public function getEbiPermissionSet()
    {
        return $this->ebiPermissionSet;
    }

    /**
     * Set lang
     *
     * @param int $lang            
     * @return Permissionset
     */
    public function setEbiPermissionSet($ebiPermissionSet)
    {
        $this->ebiPermissionSet = $ebiPermissionSet;
        
        return $this;
    }

    /**
     * Get lang
     *
     * @return int
     */
    public function getLang()
    {
        return $this->lang;
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

}
