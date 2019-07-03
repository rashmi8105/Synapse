<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class FacultyPermissionDto
{
    /**
     * Course internal ID of the course having a faculty's permissions updated
     *
     * @var integer @JMS\Type("integer")
     */
    private $courseId;
    
    /**
     * Person Id
     *
     * @var integer @JMS\Type("integer")
     */
    private $personId;
    
    /**
     * Permissions Set Id
     *
     * @var integer @JMS\Type("integer")
     */
    private $permissionsetId;
    
    /**
     * Organization Id
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;
    
    
    /**
     * Set course Id
     *
     * @return integer
     */
    public function getCourseId()
    {
        return $this->courseId;
    }

    /**
     * Return course Id
     *
     * @param integer $courseId            
     */
    public function setCourseId($courseId)
    {
        $this->courseId = $courseId;
    }
    
    /**
     * Return person Id
     *
     * @return integer
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Set person Id
     *
     * @param integer $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }
    
    /**
     * Return permissions set Id
     *
     * @return integer
     */
    public function getPermissionsetId()
    {
        return $this->permissionsetId;
    }

    /**
     * Set permissions set Id
     *
     * @param integer $permissionsetId            
     */
    public function setPermissionsetId($permissionsetId)
    {
        $this->permissionsetId = $permissionsetId;
    }
    
    /**
     * Return organization Id
     *
     * @return integer
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Set organization Id
     *
     * @param integer $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }
}