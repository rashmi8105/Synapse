<?php
namespace Synapse\RestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Synapse\CoreBundle\Entity\Institution
 *
 * @package Synapse\RestBundle\Entity
 */
class InstitutionDTO
{

    /**
     * Institution id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     *
     * @var string @JMS\Type("string")
     */
    private $subdomain;

    /**
     *
     * @var string @JMS\Type("integer")
     */
    private $parentorganizationid;

    /**
     *
     * @var string @JMS\Type("string")
     */
    private $status;

    /**
     *
     * @var string @JMS\Type("string")
     */
    private $timezone;

    /**
     *
     * @var string @JMS\Type("string")
     */
    private $website;

    /**
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param string $parentorganizationid            
     */
    public function setParentorganizationid($parentorganizationid)
    {
        $this->parentorganizationid = $parentorganizationid;
    }

    /**
     *
     * @return string
     */
    public function getParentorganizationid()
    {
        return $this->parentorganizationid;
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
}