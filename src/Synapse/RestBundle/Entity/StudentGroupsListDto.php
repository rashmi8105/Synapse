<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class StudentGroupsListDto
{

    /**
     * studentId
     *
     * @var integer @JMS\Type("integer")
     */
    private $studentId;

    /**
     * organizationId
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\OrgGroupDto>")
     *     
     */
    private $groups;

    /**
     *
     * @param Object $groups            
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    /**
     *
     * @return Object
     */
    public function getGroups()
    {
        return $this->groups;
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
     * @param int $studentId            
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     *
     * @return int
     */
    public function getStudentId()
    {
        return $this->studentId;
    }
}