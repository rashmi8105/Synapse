<?php
namespace Synapse\PersonBundle\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * ContactInfo DTO
 * @JMS\ExclusionPolicy("all")
 */
class ContactInfoDTO
{

    // array of fields that are allowed to be cleared
    private $fieldsAllowedToBeCleared = [

        'address_one',
        'address_two',
        'city',
        'state',
        'zip',
        'country',
        'primary_mobile',
        'alternate_mobile',
        'home_phone',
        'alternate_email',
        'primary_mobile_provider',
        'alternate_mobile_provider'
    ];


    /**
     * Array of fields to be nullified
     *
     * @var array
     * @JMS\Type("array")
     * @JMS\Expose
     */
    private $fieldsToClear;

    /**
     * User's ID at the organization
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    private $externalId;

    /**
     * Address of the user
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    private $addressOne;

    /**
     * Alternate address of the user
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    private $addressTwo;

    /**
     * City in which the user resides
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    private $city;

    /**
     * State in which the user resides
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    private $state;

    /**
     * Zip code in which the user resides
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    private $zip;

    /**
     * Country in which the user resides
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    private $country;

    /**
     * Primary mobile phone number of the user
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    private $primaryMobile;

    /**
     * Alternate mobile phone number of the user
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    private $alternateMobile;

    /**
     * Home phone of the user
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    private $homePhone;

    /**
     * Office phone of the user
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    private $officePhone;

    /**
     * Alternate email of the user
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    private $alternateEmail;

    /**
     * Primary mobile phone provider for the user
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    private $primaryMobileProvider;

    /**
     * Alternate mobile phone provider for the user
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    private $alternateMobileProvider;

    /**
     * @return array
     */
    public function getFieldsToClear()
    {
        $validFieldsToBeCleared = array_intersect($this->fieldsToClear, $this->fieldsAllowedToBeCleared);
        return $validFieldsToBeCleared;
    }

    /**
     * @param array $fieldsToClear
     */
    public function setFieldsToClear($fieldsToClear)
    {
        $this->fieldsToClear = $fieldsToClear;
    }

    /**
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * @return string
     */
    public function getAddressOne()
    {
        return $this->addressOne;
    }

    /**
     * @param string $addressOne
     */
    public function setAddressOne($addressOne)
    {
        $this->addressOne = $addressOne;
    }

    /**
     * @return string
     */
    public function getAddressTwo()
    {
        return $this->addressTwo;
    }

    /**
     * @param string $addressTwo
     */
    public function setAddressTwo($addressTwo)
    {
        $this->addressTwo = $addressTwo;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getPrimaryMobile()
    {
        return $this->primaryMobile;
    }

    /**
     * @param string $primaryMobile
     */
    public function setPrimaryMobile($primaryMobile)
    {
        $this->primaryMobile = $primaryMobile;
    }

    /**
     * @return string
     */
    public function getAlternateMobile()
    {
        return $this->alternateMobile;
    }

    /**
     * @param string $alternateMobile
     */
    public function setAlternateMobile($alternateMobile)
    {
        $this->alternateMobile = $alternateMobile;
    }

    /**
     * @return string
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * @param string $homePhone
     */
    public function setHomePhone($homePhone)
    {
        $this->homePhone = $homePhone;
    }

    /**
     * @return string
     */
    public function getOfficePhone()
    {
        return $this->officePhone;
    }

    /**
     * @param string $officePhone
     */
    public function setOfficePhone($officePhone)
    {
        $this->officePhone = $officePhone;
    }

    /**
     * @return string
     */
    public function getAlternateEmail()
    {
        return $this->alternateEmail;
    }

    /**
     * @param string $alternateEmail
     */
    public function setAlternateEmail($alternateEmail)
    {
        $this->alternateEmail = $alternateEmail;
    }

    /**
     * @return string
     */
    public function getPrimaryMobileProvider()
    {
        return $this->primaryMobileProvider;
    }

    /**
     * @param string $primaryMobileProvider
     */
    public function setPrimaryMobileProvider($primaryMobileProvider)
    {
        $this->primaryMobileProvider = $primaryMobileProvider;
    }

    /**
     * @return string
     */
    public function getAlternateMobileProvider()
    {
        return $this->alternateMobileProvider;
    }

    /**
     * @param string $alternateMobileProvider
     */
    public function setAlternateMobileProvider($alternateMobileProvider)
    {
        $this->alternateMobileProvider = $alternateMobileProvider;
    }
}