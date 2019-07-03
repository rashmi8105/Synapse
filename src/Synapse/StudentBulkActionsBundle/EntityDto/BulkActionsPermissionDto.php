<?php
namespace Synapse\StudentBulkActionsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\CoreBundle\DTO\StudentDto;

/**
 * Class BulkActionPermissionsDto
 *
 * @package Synapse\StudentBulkActionsBundle\EntityDto
 */
class BulkActionsPermissionDto
{
    /**
     * Type of bulk action being performed, i.e. referral
     *
     * @var string
     *
     *      @JMS\Type("string")
     */
    private $type;

    /**
     * Total number of students included in a bulk action.
     * The current loggedInUser may not have access to every student in this count
     *
     * @var int
     *
     *      @JMS\Type("integer")
     */
    private $totalStudentCount;

    /**
     * Number of students that the loggedInUser has access to.
     * Any students that the loggedInUser does not have access to will not be affected by the bulk action
     *
     * @var int
     *
     *      @JMS\Type("integer")
     */
    private $studentCountForFeaturePermission;

    /**
     * Array of student objects included in a bulk action
     *
     * @var StudentDto[]
     *
     *      @JMS\Type("array<Synapse\CoreBundle\DTO\StudentDto>")
     */
    private $students;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getTotalStudentCount()
    {
        return $this->totalStudentCount;
    }

    /**
     * @param int $totalStudentCount
     */
    public function setTotalStudentCount($totalStudentCount)
    {
        $this->totalStudentCount = $totalStudentCount;
    }

    /**
     * @return int
     */
    public function getStudentCountForFeaturePermission()
    {
        return $this->studentCountForFeaturePermission;
    }

    /**
     * @param int $studentCountForFeaturePermission
     */
    public function setStudentCountForFeaturePermission($studentCountForFeaturePermission)
    {
        $this->studentCountForFeaturePermission = $studentCountForFeaturePermission;
    }

    /**
     * @return array
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * @param array $students
     */
    public function setStudents($students)
    {
        $this->students = $students;
    }


}
