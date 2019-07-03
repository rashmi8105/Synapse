<?php
namespace Synapse\CampusConnectionBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Assign Primary Connection
 *
 * @package Synapse\CampusConnectionBundle\EntityDto
 */
class AssignPrimaryRequestDto
{

    /**
     * Internal organization ID that request is associated with
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * Array of student objects
     *
     * @var StudentListDto[] @JMS\Type("array<Synapse\CampusConnectionBundle\EntityDto\StudentListDto>")
     */
    private $studentList;

    /**
     * @return integer
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @param integer $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * @return StudentListDto[]
     */
    public function getStudentList()

    {
        return $this->studentList;
    }

    /**
     * @param StudentListDto[] $studentList
     */
    public function setStudentList($studentList)
    {
        $this->studentList = $studentList;
    }
}