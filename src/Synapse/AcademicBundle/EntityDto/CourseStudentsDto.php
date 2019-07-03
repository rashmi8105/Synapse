<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class CourseStudentsDto
{
    /**
     * External Course Id
     *
     * @var string
     * @JMS\Type("string")
     */
    private $courseId;

    /**
     * Student Ids specific to the course
     *
     * @var array
     * @JMS\Type("array")
     */
    private $students;

    /**
     * Student's ID value
     *
     * @var string
     * @JMS\Type("string")
     */
    private $studentId;


    /**
     *
     * @param string $courseId
     */
    public function setCourseId($courseId)
    {
        $this->courseId = $courseId;
    }

    /**
     *
     * @return string
     */
    public function getCourseId()
    {
        return $this->courseId;
    }

    /**
     *
     * @param mixed $students
     */
    public function setStudents($students)
    {
        $this->students = $students;
    }

    /**
     *
     * @return mixed
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * @return string
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * @param string $studentId
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

}