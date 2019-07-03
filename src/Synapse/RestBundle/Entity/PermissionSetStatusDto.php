<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class PermissionSetStatusDto
{

    /**
     * Id of a permission template.
     *
     * @JMS\Type("integer")
     */
    private $permissionTemplateId;

    /**
     * Language id. Always 1(English).
     *
     * @JMS\Type("integer")
     */
    private $langId;

    /**
     *
     * 
     * @var string @JMS\Type("string")
     *      @Assert\NotBlank()
     *     
     */
    private $permissionTemplateName;

    /**
     * Name of a permission template.
     * 
     * @var string
     * @JMS\Type("string")
     */
    private $permissionTemplateStatus;

    /**
     * Date that a permission set was last updated.
     *
     * @JMS\Type("DateTime")
     */
    private $lastUpdated;

    /**
     * Date that a permission set was last archived.
     *
     * @JMS\Type("DateTime")
     */
    private $lastArchived;

    /**
     *
     * @param string $permissionTemplateName            
     */
    public function setPermissionTemplateName($permissionTemplateName)
    {
        $this->permissionTemplateName = $permissionTemplateName;
    }

    /**
     *
     * @return string
     */
    public function getPermissionTemplateName()
    {
        return $this->permissionTemplateName;
    }

    /**
     *
     * @param mixed $langId            
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     *
     * @return mixed
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     *
     * @param string $permissionTemplateStatus            
     */
    public function setPermissionTemplateStatus($permissionTemplateStatus)
    {
        $this->permissionTemplateStatus = $permissionTemplateStatus;
    }

    /**
     *
     * @return string
     */
    public function getPermissionTemplateStatus()
    {
        return $this->permissionTemplateStatus;
    }

    /**
     *
     * @param mixed $permissionTemplateId            
     */
    public function setPermissionTemplateId($permissionTemplateId)
    {
        $this->permissionTemplateId = $permissionTemplateId;
    }

    /**
     *
     * @return mixed
     */
    public function getPermissionTemplateId()
    {
        return $this->permissionTemplateId;
    }

    /**
     *
     * @param mixed $lastUpdated            
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;
    }

    /**
     *
     * @return mixed
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }
}