<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * EbiUsers
 *
 * @ORM\Table(name="ebi_users")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EbiUsersRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EbiUsers extends BaseEntity
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
     * @var string @ORM\Column(name="first_name", type="string", length=100, nullable=true)
     */
    private $firstName;

    /**
     *
     * @var string @ORM\Column(name="last_name", type="string", length=100, nullable=true)
     */
    private $lastName;

    /**
     *
     * @var string @ORM\Column(name="email_address", type="string", length=200, nullable=true)
     */
    private $emailAddress;

    /**
     *
     * @var string @ORM\Column(name="password", type="string", length=100, nullable=true)
     */
    private $password;

    /**
     *
     * @var string @ORM\Column(name="mobile_number", type="string", length=15, nullable=true)
     */
    private $mobileNumber;

    /**
     *
     * @var string @ORM\Column(name="is_active", type="binary", length=10, nullable=true)
     */
    private $isActive;

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
     *
     * @param string $firstName            
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     *
     * @param string $lastName            
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     *
     * @param string $emailAddress            
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     *
     * @param string $password            
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     *
     * @param string $mobileNumber            
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;
    }

    /**
     *
     * @return string
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     *
     * @param string $isActive            
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @return string
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}