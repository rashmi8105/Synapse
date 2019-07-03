<?php
namespace Synapse\StudentViewBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class StudentCourseListDto
{

    /**
     * totalCourse
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalCourse;
    
    /**
     * studentId
     *
     * @var integer @JMS\Type("integer")
     */
    private $studentId;

    /**
     * courseListTable
     *
     * @var Object @JMS\Type("array<Synapse\StudentViewBundle\EntityDto\StudentCoursesArrayDto>")
     */
    private $courseListTable;

    /**
     *
     * @param int $totalCourse            
     */
    public function setTotalCourse($totalCourse)
    {
        $this->totalCourse = $totalCourse;
    }

    /**
     *
     * @return int
     */
    public function getTotalCourse()
    {
        return $this->totalCourse;
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

    /**
     *
     * @param array $courseListTable
     */
    public function setCourseListTable($courseListTable)
    {
        $this->courseListTable = $courseListTable;
    }

    /**
     *
     * @return Object
     */
    public function getCourseListTable()
    {
        return $this->courseListTable;
    }
}