<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Campus
 *
 * @package Synapse\MultiCampusBundle\EntityDto
 */
class TilesDto
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $id;

    /**
     * type
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $type;

    /**
     * organization_id
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $organizationId;

    /**
     * name
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $name;

    /**
     * logo
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $logo;

    /**
     * url
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $url;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\RoleDto>")
     *     
     *     
     */
    private $roles;

    /**
     * isHierarchyCampus
     *
     * @var boolean @JMS\Type("boolean")
     *     
     *     
     */
    private $isHierarchyCampus;

    /**
     *
     * @param            
     *
     *
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     *
     * @return
     *
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     *
     * @param string $name            
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param string $logo            
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     *
     * @param string $url            
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     *
     * @param string $type            
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

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
     * @param int $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param boolean $resolveType            
     */
    public function setIsHierarchyCampus($isHierarchyCampus)
    {
        $this->isHierarchyCampus = $isHierarchyCampus;
    }

    /**
     *
     * @return boolean
     */
    public function getIsHierarchyCampus()
    {
        return $this->isHierarchyCampus;
    }
}