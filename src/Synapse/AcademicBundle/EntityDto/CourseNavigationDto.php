<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class CourseNavigationDto
{

    /**
     * organizationId
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * yearId
     *
     * @var string @JMS\Type("string")
     */
    private $yearId;

    /**
     * termId
     *
     * @var string @JMS\Type("string")
     */
    private $termId;

    /**
     * collegeCode
     *
     * @var string @JMS\Type("string")
     */
    private $collegeCode;

    /**
     * departmentId
     *
     * @var string @JMS\Type("string")
     */
    private $departmentId;

    /**
     * subjectCode
     *
     * @var string @JMS\Type("string")
     */
    private $subjectCode;

    /**
     * courseNumber
     *
     * @var string @JMS\Type("string")
     */
    private $courseNumber;

    /**
     * type
     *
     * @var string @JMS\Type("string")
     */
    private $type;

    /**
     * course
     *
     * @var CourseNavigationListDto[] @JMS\Type("array<Synapse\AcademicBundle\EntityDto\CourseNavigationListDto>")
     */
    private $courseNavigation;

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
     * @param string $yearId            
     */
    public function setYearId($yearId)
    {
        $this->yearId = $yearId;
    }

    /**
     *
     * @return string
     */
    public function getYearId()
    {
        return $this->yearId;
    }

    /**
     *
     * @param string $termId            
     */
    public function setTermId($termId)
    {
        $this->termId = $termId;
    }

    /**
     *
     * @return string
     */
    public function getTermId()
    {
        return $this->termId;
    }

    /**
     *
     * @param string $collegeCode            
     */
    public function setCollegeCode($collegeCode)
    {
        $this->collegeCode = $collegeCode;
    }

    /**
     *
     * @return string
     */
    public function getCollegeCode()
    {
        return $this->collegeCode;
    }

    /**
     *
     * @param string $subjectCode            
     */
    public function setSubjectCode($subjectCode)
    {
        $this->subjectCode = $subjectCode;
    }

    /**
     *
     * @return string
     */
    public function getSubjectCode()
    {
        return $this->subjectCode;
    }

    /**
     *
     * @param string $departmentId            
     */
    public function setDepartmentId($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    /**
     *
     * @return string
     */
    public function getCourseNumber()
    {
        return $this->courseNumber;
    }

    /**
     *
     * @param string $courseNumber            
     */
    public function setCourseNumber($courseNumber)
    {
        $this->courseNumber = $courseNumber;
    }

    /**
     *
     * @return string
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     *
     * @param string $type            
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param CourseNavigationListDto[] $courseNavigation
     */
    public function setCourseNavigation($courseNavigation)
    {
        $this->courseNavigation = $courseNavigation;
    }

    /**
     *
     * @return CourseNavigationListDto[]
     */
    public function getCourseNavigation()
    {
        return $this->courseNavigation;
    }
}