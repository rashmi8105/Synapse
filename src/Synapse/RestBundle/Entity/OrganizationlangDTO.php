<?php
// OrganizationlangDTO
namespace Synapse\RestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Synapse\CoreBundle\Entity\Organization
 *
 * @package Synapse\RestBundle\Entity
 */
class OrganizationlangDTO
{

    /**
     * Institution id
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * Institution id
     *
     * @var integer @JMS\Type("integer")
     */
    private $parentorganizationid;

    /**
     * Organization id
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationid;

    /**
     * Language id
     *
     * @var integer @JMS\Type("integer")
     */
    private $langid;

    /**
     * organizationname
     *
     * @var string @JMS\Type("string")
     */
    private $organizationname;

    /**
     * nickname
     *
     * @var string @JMS\Type("string")
     */
    private $nickName;

    /**
     * subdomain
     *
     * @var string @JMS\Type("string")
     */
    private $subdomain;

    /**
     * status
     *
     * @var string @JMS\Type("string")
     */
    private $status;

    /**
     * website
     *
     * @var string @JMS\Type("string")
     */
    private $website;

    /**
     * description
     *
     * @var string @JMS\Type("description")
     */
    private $description;

    /**
     *
     * @param string $timezone            
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * timezone
     *
     * @var string @JMS\Type("string")
     */
    private $timezone;

    /**
     *
     * @param int $langid            
     */
    public function setLangid($langid)
    {
        $this->langid = $langid;
    }

    /**
     *
     * @return int
     */
    public function getLangid()
    {
        return $this->langid;
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
     *
     * @param int $organizationid            
     */
    public function setOrganizationid($organizationid)
    {
        $this->organizationid = $organizationid;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationid()
    {
        return $this->organizationid;
    }

    /**
     *
     * @param string $organizationname            
     */
    public function setOrganizationname($organizationname)
    {
        $this->organizationname = $organizationname;
    }

    /**
     *
     * @return string
     */
    public function getOrganizationname()
    {
        return $this->organizationname;
    }

    /**
     *
     * @param string $status            
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param string $subdomain            
     */
    public function setSubdomain($subdomain)
    {
        $this->subdomain = $subdomain;
    }

    /**
     *
     * @return string
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     *
     * @param string $website            
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     *
     * @param string $description            
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}