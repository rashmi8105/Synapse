<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class CourseDTO
{
    // array of fields that are allowed to be cleared
    private $fieldsAllowedToBeCleared = [
        'days_times',
        'location',
        'credit_hours'
    ];

    /**
     * Year associated with the course's section
     *
     * @var string
     * @JMS\Type("string")
     */
    private $yearId;

    /**
     * Term associated with the course's section
     *
     * @var string
     * @JMS\Type("string")
     */
    private $termId;

    /**
     * College of the course's section.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $collegeCode;

    /**
     * Department of the course's section.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $departmentCode;

    /**
     * Subject code of the course's section.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $subjectCode;

    /**
     * Section number of the course's section
     *
     * @var string @JMS\Type("string")
     */
    private $sectionNumber;

    /**
     * ID of the course's section at the organization
     *
     * @var string
     * @JMS\Type("string")
     */
    private $courseSectionId;

    /**
     * Days and times of the course's section.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $daysTimes;

    /**
     * Credit hours of the course's section.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $creditHours;

    /**
     * Fields from which values should be cleared.
     *
     * @var array
     * @JMS\Type("array")
     */
    private $clearFields = [];

    /**
     * Number of the course's section.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $courseNumber;

    /**
     * Location of the course's section.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $location;

    /**
     * Name of the course's section
     *
     * @var string @JMS\Type("string")
     */
    private $courseName;

    /**
     * Name of the course's section.
     *
     * @var string
     * @JMS\Type("string")
     */
    public function setCourseName($courseName)
    {
        $this->courseName = $courseName;
    }

    /**
     *
     * @return string
     */
    public function getCourseName()
    {
        return $this->courseName;
    }

    /**
     * Section number of the course's section.
     *
     * @var string
     * @JMS\Type("string")
     */
    public function setCourseNumber($courseNumber)
    {
        $this->courseNumber = $courseNumber;
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
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
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
     * @param string $departmentCode
     */
    public function setDepartmentCode($departmentCode)
    {
        $this->departmentCode = $departmentCode;
    }

    /**
     *
     * @return string
     */
    public function getDepartmentCode()
    {
        return $this->departmentCode;
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
     * @param string $sectionNumber
     */
    public function setSectionNumber($sectionNumber)
    {
        $this->sectionNumber = $sectionNumber;
    }

    /**
     *
     * @return string
     */
    public function getSectionNumber()
    {
        return $this->sectionNumber;
    }

    /**
     *
     * @param string $courseSectionId
     */
    public function setCourseSectionId($courseSectionId)
    {
        $this->courseSectionId = $courseSectionId;
    }

    /**
     *
     * @return string
     */
    public function getCourseSectionId()
    {
        return $this->courseSectionId;
    }

    /**
     *
     * @param string $daysTimes
     */
    public function setDaysTimes($daysTimes)
    {
        $this->daysTimes = $daysTimes;
    }

    /**
     *
     * @return string
     */
    public function getDaysTimes()
    {
        return $this->daysTimes;
    }

    /**
     *
     * @param string $creditHours
     */
    public function setCreditHours($creditHours)
    {
        $this->creditHours = $creditHours;
    }

    /**
     *
     * @return string
     */
    public function getCreditHours()
    {
        return $this->creditHours;
    }

    /**
     *
     * @param array $clearFields
     */
    public function setClearFields($clearFields)
    {
        $this->clearFields = $clearFields;
    }

    /**
     *
     * @return array
     */
    public function getClearFields()
    {
        $validFieldsToBeCleared = array_intersect($this->clearFields, $this->fieldsAllowedToBeCleared);
        return $validFieldsToBeCleared;
    }

}