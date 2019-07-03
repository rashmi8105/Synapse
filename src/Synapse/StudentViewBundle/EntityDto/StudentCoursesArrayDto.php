<?php
namespace Synapse\StudentViewBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class StudentCoursesArrayDto
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
     * campusName
     *
     * @var string @JMS\Type("string")
     */
    private $campusName;

    /**
     * courses
     *
     * @var Object @JMS\Type("array<Synapse\StudentViewBundle\EntityDto\StudentCourseDto>")
     */
    private $courses;

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
     * @param mixed $course            
     */
    public function setCourses($courses)
    {
        $this->courses = $courses;
    }

    /**
     *
     * @return mixed
     */
    public function getCourses()
    {
        return $this->courses;
    }
    
    /**
     *
     * @param string $campusName
     */
    public function setCampusName($campusName)
    {
    	$this->campusName = $campusName;
    }
    
    /**
     *
     * @return string
     */
    public function getCampusName()
    {
    	return $this->campusName;
    }
}