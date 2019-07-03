<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RoleLang
 *
 * @ORM\Table(name="role_lang", indexes={@ORM\Index(name="rolelang_langid", columns={"lang_id"}), @ORM\Index(name="rolelang_roleid", columns={"role_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\RoleLangRepository")
 */
class RoleLang extends BaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="role_name", type="string", length=45, nullable=true)
     */
    private $roleName;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\Role
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Role")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     * })
     */
    private $role;

    /**
     * @var \Synapse\CoreBundle\Entity\LanguageMaster
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     * })
     */
    private $lang;



    /**
     * Set roleName
     *
     * @param string $roleName
     * @return RoleLang
     */
    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;

        return $this;
    }

    /**
     * Get roleName
     *
     * @return string 
     */
    public function getRoleName()
    {
        return $this->roleName;
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
     * Set role
     *
     * @param \Synapse\CoreBundle\Entity\Role $role
     * @return RoleLang
     */
    public function setRole(\Synapse\CoreBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Synapse\CoreBundle\Entity\Role 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set lang
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $lang
     * @return RoleLang
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
}
