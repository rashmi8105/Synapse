<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Academic Update
 *
 * @package Synapse\RestBundle\Entity
 */
class AcademicUpdateDetailsDto
{

    /**
     * Name of the Academic Year.
     * @JMS\Type("string")
     *
     * @var string
     */
    private $academicYearName;

    /**
     * Name of the Academic Term.
     * @JMS\Type("string")
     *
     * @var string
     */
    private $academicTermName;

    /**
     * Name of the Department.
     * @JMS\Type("string")
     *
     * @var string
     */
    private $departmentName;

    /**
     * Name of a Course Subject.
     * @JMS\Type("string")
     *
     * @var string
     */
    private $subjectCourse;

    /**
     * Name of a Course's Section.
     * @JMS\Type("string")
     *
     * @var string
     */
    private $courseSectionName;

    /**
     * Array holding Details about a Student.
     * @JMS\Type("array<Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDetailsStudentDto>")
     *
     * @var AcademicUpdateDetailsStudentDto[]
     */
    private $studentDetails;

    /**
     * Set a Course Subject.
     *
     * @param string $subjectCourse
     */
    public function setSubjectCourse($subjectCourse)
    {
        $this->subjectCourse = $subjectCourse;
    }

    /**
     * Returns a Course Subject.
     *
     * @return string
     */
    public function getSubjectCourse()
    {
        return $this->subjectCourse;
    }

    /**
     * Set details to a Student.
     *
     * @param AcademicUpdateDetailsStudentDto[] $studentDetails
     */
    public function setStudentDetails($studentDetails)
    {
        $this->studentDetails = $studentDetails;
    }

    /**
     * Returns the details of a Student.
     *
     * @return AcademicUpdateDetailsStudentDto[]
     */
    public function getStudentDetails()
    {
        return $this->studentDetails;
    }

    /**
     * Set the name of a Department.
     *
     * @param string $departmentName
     */
    public function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;
    }

    /**
     * Returns the name of a Department.
     *
     * @return string
     */
    public function getDepartmentName()
    {
        return $this->departmentName;
    }

    /**
     * Set the Name of a Course Section.
     *
     * @param string $courseSectionName
     */
    public function setCourseSectionName($courseSectionName)
    {
        $this->courseSectionName = $courseSectionName;
    }

    /**
     * Returns the Name of a Course Section.
     *
     * @return string
     */
    public function getCourseSectionName()
    {
        return $this->courseSectionName;
    }

    /**
     * Set an Academic Year Name.
     *
     * @param string $academicYearName
     */
    public function setAcademicYearName($academicYearName)
    {
        $this->academicYearName = $academicYearName;
    }

    /**
     * Returns an Academic Year Name.
     *
     * @return string
     */
    public function getAcademicYearName()
    {
        return $this->academicYearName;
    }

    /**
     * Set the name of an Academic Term.
     *
     * @param string $academicTermName
     */
    public function setAcademicTermName($academicTermName)
    {
        $this->academicTermName = $academicTermName;
    }

    /**
     * Returns the name of an Academic Term.
     *
     * @return string
     */
    public function getAcademicTermName()
    {
        return $this->academicTermName;
    }


}