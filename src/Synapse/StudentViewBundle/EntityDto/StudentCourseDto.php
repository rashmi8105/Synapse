<?php
namespace Synapse\StudentViewBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class StudentCourseDto
{

    /**
     * courseId
     *
     * @var integer @JMS\Type("integer")
     */
    private $courseId;

    /**
     * uniqueCourseSectionId
     *
     * @var integer @JMS\Type("integer")
     */
    private $uniqueCourseSectionId;

    /**
     * subjectCourse
     *
     * @var string @JMS\Type("string")
     */
    private $subjectCourse;

    /**
     * sectionId
     *
     * @var string @JMS\Type("string")
     */
    private $sectionId;

    /**
     * totalFaculty
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalFaculty;

    /**
     * totalStudents
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalStudents;

    /**
     * courseTitle
     *
     * @var string @JMS\Type("string")
     */
    private $courseTitle;

    /**
     * location
     *
     * @var string @JMS\Type("string")
     */
    private $courseNumber;

    /**
     * time
     *
     * @var string @JMS\Type("string")
     */
    private $time;

    /**
     * location
     *
     * @var string @JMS\Type("string")
     */
    private $location;

    /**
     * absense
     *
     * @var integer @JMS\Type("integer")
     */
    private $absense;

    /**
     * finalGrade
     *
     * @var string @JMS\Type("string")
     */
    private $finalGrade;

    /**
     * grade
     *
     * @var string @JMS\Type("string")
     */
    private $inProgressGrade;
    
    /**
     * comments
     *
     * @var string @JMS\Type("string")
     */
    private $comments;

    /**
     * dateStamp
     *
     * @JMS\Type("DateTime")
     */
    private $dateStamp;

    /**
     * $facultyDetails
     *
     * @var Object @JMS\Type("array<Synapse\AcademicBundle\EntityDto\FacultyDetailsDto>")
     */
    private $faculties;

    /**
     *
     * @param int $courseId            
     */
    public function setCourseId($courseId)
    {
        $this->courseId = $courseId;
    }

    /**
     *
     * @return int
     */
    public function getCourseId()
    {
        return $this->courseId;
    }

    /**
     *
     * @param int $uniqueCourseSectionId            
     */
    public function setUniqueCourseSectionId($uniqueCourseSectionId)
    {
        $this->uniqueCourseSectionId = $uniqueCourseSectionId;
    }

    /**
     *
     * @return int
     */
    public function getUniqueCourseSectionId()
    {
        return $this->uniqueCourseSectionId;
    }

    /**
     *
     * @param string $subjectCourse            
     */
    public function setSubjectCourse($subjectCourse)
    {
        $this->subjectCourse = $subjectCourse;
    }

    /**
     *
     * @return string
     */
    public function getSubjectCourse()
    {
        return $this->subjectCourse;
    }

    /**
     *
     * @param string $sectionId            
     */
    public function setSectionId($sectionId)
    {
        $this->sectionId = $sectionId;
    }

    /**
     *
     * @return string
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }

    /**
     *
     * @param int $totalFaculty            
     */
    public function setTotalFaculty($totalFaculty)
    {
        $this->totalFaculty = $totalFaculty;
    }

    /**
     *
     * @return int
     */
    public function getTotalFaculty()
    {
        return $this->totalFaculty;
    }

    /**
     *
     * @param int $totalStudents            
     */
    public function setTotalStudents($totalStudents)
    {
        $this->totalStudents = $totalStudents;
    }

    /**
     *
     * @return int
     */
    public function getTotalStudents()
    {
        return $this->totalStudents;
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
    public function getCourseNumber()
    {
        return $this->courseNumber;
    }

    /**
     *
     * @param string $courseTitle            
     */
    public function setCourseTitle($courseTitle)
    {
        $this->courseTitle = $courseTitle;
    }

    /**
     *
     * @return string
     */
    public function getCourseTitle()
    {
        return $this->courseTitle;
    }

    /**
     *
     * @param string $time            
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     *
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     *
     * @param string $time            
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
     * @param int $absense            
     */
    public function setAbsense($absense)
    {
        $this->absense = $absense;
    }

    /**
     *
     * @return int
     */
    public function getAbsense()
    {
        return $this->absense;
    }

    /**
     *
     * @param string $inProgressGrade            
     */
    public function setInProgressGrade($inProgressGrade)
    {
        $this->inProgressGrade = $inProgressGrade;
    }

    /**
     *
     * @return string
     */
    public function getInProgressGrade()
    {
        return $this->inProgressGrade;
    }

    /**
     *
     * @param string $finalGrade            
     */
    public function setFinalGrade($finalGrade)
    {
        $this->finalGrade = $finalGrade;
    }

    /**
     *
     * @return string
     */
    public function getFinalGrade()
    {
        return $this->finalGrade;
    }

    /**
     *
     * @param mixed $dateStamp            
     */
    public function setDateStamp($dateStamp)
    {
        $this->dateStamp = $dateStamp;
    }

    /**
     *
     * @return mixed
     */
    public function getDateStamp()
    {
        return $this->dateStamp;
    }

    /**
     *
     * @param array $faculties
     */
    public function setFaculties($faculties)
    {
        $this->faculties = $faculties;
    }

    /**
     *
     * @return Object
     */
    public function getFaculties()
    {
        return $this->faculties;
    }
    
    /**
     *
     * @param Object $faculties
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }
    
    /**
     *
     * @return Object
     */
    public function getComments()
    {
        return $this->comments;
    }
}