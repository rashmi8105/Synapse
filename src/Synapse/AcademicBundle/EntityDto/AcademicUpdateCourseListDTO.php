<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class AcademicUpdateCourseListDTO
{

    /**
     * year
     *
     * @var string @JMS\Type("string")
     */
    private $year;

    /**
     * term
     *
     * @var string @JMS\Type("string")
     */
    private $term;

    /**
     * college
     *
     * @var string @JMS\Type("string")
     */
    private $college;

    /**
     * department
     *
     * @var string @JMS\Type("string")
     */
    private $department;

    /**
     * course
     *
     * @var AcademicUpdateCourseDTO[] @JMS\Type("array<Synapse\AcademicBundle\EntityDto\AcademicUpdateCourseDTO>")
     */
    private $course;

    /**
     * Indicates whether the term is a past term (false), or a current or future term (true)
     *
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $currentOrFutureTerm;

    /**
     *
     * @param string $year            
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     *
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     *
     * @param string $term            
     */
    public function setTerm($term)
    {
        $this->term = $term;
    }

    /**
     *
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     *
     * @param string $college            
     */
    public function setCollege($college)
    {
        $this->college = $college;
    }

    /**
     *
     * @return string
     */
    public function getCollege()
    {
        return $this->college;
    }

    /**
     *
     * @param string $department            
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     *
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     *
     * @param AcademicUpdateCourseDTO[] $course
     */
    public function setCourse($course)
    {
        $this->course = $course;
    }

    /**
     *
     * @return AcademicUpdateCourseDTO[]
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @return bool
     */
    public function getCurrentOrFutureTerm()
    {
        return $this->currentOrFutureTerm;
    }

    /**
     * @param bool $currentOrFutureTerm
     */
    public function setCurrentOrFutureTerm($currentOrFutureTerm)
    {
        $this->currentOrFutureTerm = $currentOrFutureTerm;
    }
}