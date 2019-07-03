<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * ContactInfo
 *
 * @ORM\Table(name="contact_info")
 * @ORM\Entity(repositoryClass="Synapse\PersonBundle\Repository\ContactInfoRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity("primaryEmail",message="Primary Email  already exists with another ExternalId.")
 */
class ContactInfo extends BaseEntity
{
    /**
     * @var string
     * @Assert\Length(max="100",maxMessage = "Address1 cannot be longer than {{ limit }} characters")
     * @ORM\Column(name="address_1", type="string", length=100, nullable=true)
     */
    private $address1;

    /**
     * @var string
     * @Assert\Length(max="100",maxMessage = "Address2 cannot be longer than {{ limit }} characters")
     * @ORM\Column(name="address_2", type="string", length=100, nullable=true)
     */
    private $address2;

    /**
     * @var string
     * @Assert\Length(max="100",maxMessage = "City cannot be longer than {{ limit }} characters")
     * @ORM\Column(name="city", type="string", length=100, nullable=true)
     */
    private $city;

    /**
     * @var string
     * @Assert\Length(max="20",maxMessage = "Zip cannot be longer than {{ limit }} characters")
     * @ORM\Column(name="zip", type="string", length=20, nullable=true)
     */
    private $zip;

    /**
     * @var string
     * @Assert\Length(max="100",maxMessage = "State cannot be longer than {{ limit }} characters")
     * @ORM\Column(name="state", type="string", length=100, nullable=true)
     */
    private $state;

    /**
     * @var string
     * @Assert\Length(max="100",maxMessage = "Country cannot be longer than {{ limit }} characters")
     * @ORM\Column(name="country", type="string", length=100, nullable=true)
     */
    private $country;

    /**
     * @var string
     * @Assert\Length(max="32", maxMessage = "Primary Mobile cannot be longer than {{ limit }} characters")
     * @ORM\Column(name="primary_mobile", type="string", length=32, nullable=true)
     */
    private $primaryMobile;

    /**
     * @var string
     * @Assert\Length(max="32",maxMessage = "Alternate Mobile cannot be longer than {{ limit }} characters")
     * @ORM\Column(name="alternate_mobile", type="string", length=32, nullable=true)
     */
    private $alternateMobile;

    /**
     * @var string
     * @Assert\Length(max="32" ,maxMessage = "Home Phone cannot be longer than {{ limit }} characters")
     * @ORM\Column(name="home_phone", type="string", length=32, nullable=true)
     */
    private $homePhone;

    /**
     * @var string
     * @Assert\Length(max="32",maxMessage = "Office Phone cannot be longer than {{ limit }} characters")
     * @ORM\Column(name="office_phone", type="string", length=32, nullable=true)
     */
    private $officePhone;

    /**
     * @var string
     * @Assert\Email(
     *     strict = true
     * )
     * @Assert\Length(max="100",maxMessage = "Email ID cannot be longer than {{ limit }} characters")
     * @ORM\Column(name="primary_email", type="string", length=100, nullable=true)
     */
    private $primaryEmail;

    /**
     * @var string
     * @Assert\Email(
     *     strict = true
     * )
     * @Assert\Length(max="100",maxMessage = "Alternate Email cannot be longer than {{ limit }} characters")
     * @ORM\Column(name="alternate_email", type="string", length=100, nullable=true)
     */
    private $alternateEmail;

    /**
     * @var string
     * @Assert\Length(max="45",maxMessage = "Primary Mobile Provider cannot be longer than {{ limit }} characters")
     * @ORM\Column(name="primary_mobile_provider", type="string", length=45, nullable=true)
     */
    private $primaryMobileProvider;

    /**
     * @var string
     * @Assert\Length(max="45",maxMessage = "Alternate Mobile Provider cannot be longer than {{ limit }} characters")
     * @ORM\Column(name="alternate_mobile_provider", type="string", length=45, nullable=true)
     */
    private $alternateMobileProvider;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set address1
     *
     * @param string $address1
     * @return ContactInfo
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Get address1
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     * @return ContactInfo
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get address2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return ContactInfo
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set zip
     *
     * @param string $zip
     * @return ContactInfo
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return ContactInfo
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return ContactInfo
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set primaryMobile
     *
     * @param string $primaryMobile
     * @return ContactInfo
     */
    public function setPrimaryMobile($primaryMobile)
    {
        $this->primaryMobile = $primaryMobile;

        return $this;
    }

    /**
     * Get primaryMobile
     *
     * @return string
     */
    public function getPrimaryMobile()
    {
        return $this->primaryMobile;
    }

    /**
     * Set alternateMobile
     *
     * @param string $alternateMobile
     * @return ContactInfo
     */
    public function setAlternateMobile($alternateMobile)
    {
        $this->alternateMobile = $alternateMobile;

        return $this;
    }

    /**
     * Get alternateMobile
     *
     * @return string
     */
    public function getAlternateMobile()
    {
        return $this->alternateMobile;
    }

    /**
     * Set homePhone
     *
     * @param string $homePhone
     * @return ContactInfo
     */
    public function setHomePhone($homePhone)
    {
        $this->homePhone = $homePhone;

        return $this;
    }

    /**
     * Get homePhone
     *
     * @return string
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * Set officePhone
     *
     * @param string $officePhone
     * @return ContactInfo
     */
    public function setOfficePhone($officePhone)
    {
        $this->officePhone = $officePhone;

        return $this;
    }

    /**
     * Get officePhone
     *
     * @return string
     */
    public function getOfficePhone()
    {
        return $this->officePhone;
    }

    /**
     * Set primaryEmail
     *
     * @param string $primaryEmail
     * @return ContactInfo
     */
    public function setPrimaryEmail($primaryEmail)
    {
        $this->primaryEmail = $primaryEmail;

        return $this;
    }

    /**
     * Get primaryEmail
     *
     * @return string
     */
    public function getPrimaryEmail()
    {
        return $this->primaryEmail;
    }

    /**
     * Set alternateEmail
     *
     * @param string $alternateEmail
     * @return ContactInfo
     */
    public function setAlternateEmail($alternateEmail)
    {
        $this->alternateEmail = $alternateEmail;

        return $this;
    }

    /**
     * Get alternateEmail
     *
     * @return string
     */
    public function getAlternateEmail()
    {
        return $this->alternateEmail;
    }

    /**
     * Set primaryMobileProvider
     *
     * @param string $primaryMobileProvider
     * @return ContactInfo
     */
    public function setPrimaryMobileProvider($primaryMobileProvider)
    {
        $this->primaryMobileProvider = $primaryMobileProvider;

        return $this;
    }

    /**
     * Get primaryMobileProvider
     *
     * @return string
     */
    public function getPrimaryMobileProvider()
    {
        return $this->primaryMobileProvider;
    }

    /**
     * Set alternateMobileProvider
     *
     * @param string $alternateMobileProvider
     * @return ContactInfo
     */
    public function setAlternateMobileProvider($alternateMobileProvider)
    {
        $this->alternateMobileProvider = $alternateMobileProvider;

        return $this;
    }

    /**
     * Get alternateMobileProvider
     *
     * @return string
     */
    public function getAlternateMobileProvider()
    {
        return $this->alternateMobileProvider;
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
