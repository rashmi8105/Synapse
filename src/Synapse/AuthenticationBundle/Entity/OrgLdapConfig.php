<?php
namespace Synapse\AuthenticationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgLdapConfig
 *
 * @ORM\Table(name="org_ldap_config", indexes={@ORM\Index(name="fk_org_ldap_config_organization1_idx", columns={"org_id"})})
 * @ORM\Entity(repositoryClass="Synapse\AuthenticationBundle\Repository\OrgLdapConfigRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgLdapConfig extends BaseEntity
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Synapse\CoreBundle\Entity\Organization
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="org_id", nullable=false, referencedColumnName="id")
     * })
     */
    private $organization;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", nullable=false, columnDefinition="enum('AD', 'LDAP')")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(name="student_hostname", type="text", nullable=false)
     */
    private $studentHostname;

    /**
     * @var string
     * @ORM\Column(name="staff_hostname", type="text", nullable=false)
     */
    private $staffHostname;

    /**
     * @var string
     * @ORM\Column(name="student_initial_user", type="string", nullable=true)
     */
    private $studentInitialUser;

    /**
     * @var string
     * @ORM\Column(name="student_initial_password", type="string", nullable=true)
     */
    private $studentInitialPassword;

    /**
     * @var string
     * @ORM\Column(name="student_user_base_domain", type="text", nullable=true)
     */
    private $studentUserBaseDomain;

    /**
     * @var string
     * @ORM\Column(name="student_username_attribute", type="string", nullable=true)
     */
    private $studentUsernameAttribute;

    /**
     * @var string
     * @ORM\Column(name="staff_initial_user", type="string", nullable=true)
     */
    private $staffInitialUser;

    /**
     * @var string
     * @ORM\Column(name="staff_initial_password", type="string", nullable=true)
     */
    private $staffInitialPassword;

    /**
     * @var string
     * @ORM\Column(name="staff_user_base_domain", type="text", nullable=true)
     */
    private $staffUserBaseDomain;

    /**
     * @var string
     * @ORM\Column(name="staff_username_attribute", type="string", nullable=true)
     */
    private $staffUsernameAttribute;



    public function setStaffHostname($hostname)
    {
        $this->staffHostname = $hostname;
    }

    public function getStaffHostname()
    {
        return $this->staffHostname;
    }

    public function setStudentHostname($hostname)
    {
        $this->studentHostname = $hostname;
    }

    public function getStudentHostname()
    {
        return $this->studentHostname;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    public function getOrganization()
    {
        return $this->organization;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }



    /**
     * Sets the value of studentInitialUser.
     *
     * @param string $studentInitialUser the student initial user
     *
     * @return self
     */
    public function setStudentInitialUser($studentInitialUser)
    {
        $this->studentInitialUser = $studentInitialUser;

        return $this;
    }

    /**
     * Gets the value of studentInitialUser.
     *
     * @return string
     */
    public function getStudentInitialUser()
    {
        return $this->studentInitialUser;
    }

    /**
     * Sets the value of studentInitialPassword.
     *
     * @param string $studentInitialPassword the student initial password
     *
     * @return self
     */
    public function setStudentInitialPassword($studentInitialPassword)
    {
        $this->studentInitialPassword = $studentInitialPassword;

        return $this;
    }

    /**
     * Gets the value of studentInitialPassword.
     *
     * @return string
     */
    public function getStudentInitialPassword()
    {
        return $this->studentInitialPassword;
    }

    /**
     * Sets the value of studentUserBaseDomain.
     *
     * @param string $studentUserBaseDomain the student user base domain
     *
     * @return self
     */
    public function setStudentUserBaseDomain($studentUserBaseDomain)
    {
        $this->studentUserBaseDomain = $studentUserBaseDomain;

        return $this;
    }

    /**
     * Gets the value of studentUserBaseDomain.
     *
     * @return string
     */
    public function getStudentUserBaseDomain()
    {
        return $this->studentUserBaseDomain;
    }

    /**
     * Sets the value of studentUsernameAttribute.
     *
     * @param string $studentUsernameAttribute the student username attribute
     *
     * @return self
     */
    public function setStudentUsernameAttribute($studentUsernameAttribute)
    {
        $this->studentUsernameAttribute = $studentUsernameAttribute;

        return $this;
    }

    /**
     * Gets the value of studentUsernameAttribute.
     *
     * @return string
     */
    public function getStudentUsernameAttribute()
    {
        return $this->studentUsernameAttribute;
    }

    /**
     * Sets the value of staffInitialUser.
     *
     * @param string $staffInitialUser the staff initial user
     *
     * @return self
     */
    public function setStaffInitialUser($staffInitialUser)
    {
        $this->staffInitialUser = $staffInitialUser;

        return $this;
    }

    /**
     * Gets the value of staffInitialUser.
     *
     * @return string
     */
    public function getStaffInitialUser()
    {
        return $this->staffInitialUser;
    }

    /**
     * Sets the value of staffInitialPassword.
     *
     * @param string $staffInitialPassword the staff initial password
     *
     * @return self
     */
    public function setStaffInitialPassword($staffInitialPassword)
    {
        $this->staffInitialPassword = $staffInitialPassword;

        return $this;
    }

    /**
     * Gets the value of staffInitialPassword.
     *
     * @return string
     */
    public function getStaffInitialPassword()
    {
        return $this->staffInitialPassword;
    }

    /**
     * Sets the value of staffUserBaseDomain.
     *
     * @param string $staffUserBaseDomain the staff user base domain
     *
     * @return self
     */
    public function setStaffUserBaseDomain($staffUserBaseDomain)
    {
        $this->staffUserBaseDomain = $staffUserBaseDomain;

        return $this;
    }

    /**
     * Gets the value of staffUserBaseDomain.
     *
     * @return string
     */
    public function getStaffUserBaseDomain()
    {
        return $this->staffUserBaseDomain;
    }

    /**
     * Sets the value of staffUsernameAttribute.
     *
     * @param string $staffUsernameAttribute the staff username attribute
     *
     * @return self
     */
    public function setStaffUsernameAttribute($staffUsernameAttribute)
    {
        $this->staffUsernameAttribute = $staffUsernameAttribute;

        return $this;
    }

    /**
     * Gets the value of staffUsernameAttribute.
     *
     * @return string
     */
    public function getStaffUsernameAttribute()
    {
        return $this->staffUsernameAttribute;
    }
}
