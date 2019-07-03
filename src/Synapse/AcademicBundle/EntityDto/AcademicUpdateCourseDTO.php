<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use DateTime;

class AcademicUpdateCourseDTO
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
     * Number of the course
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
     * createViewAcademicUpdate
     *
     * @JMS\Type("boolean")
     */
    private $createViewAcademicUpdate;

    /**
     * view_all_academic_update_courses
     *
     * @JMS\Type("boolean")
     */
    private $viewAllAcademicUpdateCourses;

    /**
     * $facultyDetails
     *
     * @var Object @JMS\Type("array<Synapse\AcademicBundle\EntityDto\FacultyDetailsDto>")
     */
    private $facultyDetails;

    /**
     * courseName
     *
     * @var string @JMS\Type("string")
     */
    private $courseName;

    /**
     * termCode
     *
     * @var string @JMS\Type("string")
     */
    private $termCode;

    /**
     * termName
     *
     * @var string @JMS\Type("string")
     */
    private $termName;

    /**
     * @param DateTime
     * @JMS\Type("DateTime")
     */
    private $absencesUpdateDate;

    /**
     * @param DateTime
     * @JMS\Type("DateTime")
     */
    private $inProgressGradeUpdateDate;

    /**
     * @param DateTime
     * @JMS\Type("DateTime")
     */
    private $finalGradeUpdateDate;


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
     * @param string | int $uniqueCourseSectionId
     */
    public function setUniqueCourseSectionId($uniqueCourseSectionId)
    {
        $this->uniqueCourseSectionId = $uniqueCourseSectionId;
    }

    /**
     *
     * @return string | int
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
     * @param Object | array $facultyDetails
     */
    public function setFacultyDetails($facultyDetails)
    {
        $this->facultyDetails = $facultyDetails;
    }

    /**
     *
     * @return Object
     */
    public function getFacultyDetails()
    {
        return $this->facultyDetails;
    }

    /**
     *
     * @var boolean $createViewAcademicUpdate
     */
    public function setCreateViewAcademicUpdate($createViewAcademicUpdate)
    {
        $this->createViewAcademicUpdate = $createViewAcademicUpdate;
    }

    /**
     *
     *  @var boolean $createViewAcademicUpdate
     */
    public function setViewAllAcademicUpdateCourses($viewAllAcademicUpdateCourses)
    {
        $this->viewAllAcademicUpdateCourses = $viewAllAcademicUpdateCourses;
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
     * @param string $courseName
     */
    public function setCourseName($courseName)
    {
        $this->courseName = $courseName;
    }

    /**
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
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
     *
     * @param string $termCode
     */
    public function setTermCode($termCode)
    {
        $this->termCode = $termCode;
    }

    /**
     *
     * @return string
     */
    public function getTermCode()
    {
        return $this->termCode;
    }

    /**
     *
     * @param string $termName
     */
    public function setTermName($termName)
    {
        $this->termName = $termName;
    }

    /**
     *
     * @return string
     */
    public function getTermName()
    {
        return $this->termName;
    }

    /**
     * @return DateTime
     */
    public function getAbsencesUpdateDate()
    {
        return $this->absencesUpdateDate;
    }

    /**
     * @param DateTime $absencesUpdateDate
     */
    public function setAbsencesUpdateDate($absencesUpdateDate)
    {
        $this->absencesUpdateDate = $absencesUpdateDate;
    }

    /**
     * @return DateTime
     */
    public function getInProgressGradeUpdateDate()
    {
        return $this->inProgressGradeUpdateDate;
    }

    /**
     * @param DateTime $inProgressGradeUpdateDate
     */
    public function setInProgressGradeUpdateDate($inProgressGradeUpdateDate)
    {
        $this->inProgressGradeUpdateDate = $inProgressGradeUpdateDate;
    }

    /**
     * @return DateTime
     */
    public function getFinalGradeUpdateDate()
    {
        return $this->finalGradeUpdateDate;
    }

    /**
     * @param DateTime $finalGradeUpdateDate
     */
    public function setFinalGradeUpdateDate($finalGradeUpdateDate)
    {
        $this->finalGradeUpdateDate = $finalGradeUpdateDate;
    }

}