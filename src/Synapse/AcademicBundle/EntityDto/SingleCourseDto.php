<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class SingleCourseDto
{

    /**
     * totalStudents
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalStudents;

    /**
     * totalFaculties
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalFaculties;

    /**
     * courseId
     *
     * @var integer @JMS\Type("integer")
     */
    private $courseId;

    /**
     * courseName
     *
     * @var string @JMS\Type("string")
     */
    private $courseName;

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
     * sectionNumber
     *
     * @var string @JMS\Type("string")
     */
    private $sectionNumber;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\AcademicBundle\EntityDto\FacultyDetailsDto>")
     *     
     *     
     */
    private $facultyDetails;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\AcademicBundle\EntityDto\StudentDetailsDto>")
     *     
     *     
     */
    private $studentDetails;
    
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
     *
     * @param integer $totalStudents            
     */
    public function setTotalStudents($totalStudents)
    {
        $this->totalStudents = $totalStudents;
    }

    /**
     *
     * @return integer
     */
    public function getTotalStudents()
    {
        return $this->totalStudents;
    }

    /**
     *
     * @param integer $totalFaculties            
     */
    public function setTotalFaculties($totalFaculties)
    {
        $this->totalFaculties = $totalFaculties;
    }

    /**
     *
     * @return integer
     */
    public function getTotalFaculties()
    {
        return $this->totalFaculties;
    }

    /**
     *
     * @param integer $courseId            
     */
    public function setCourseId($courseId)
    {
        $this->courseId = $courseId;
    }

    /**
     *
     * @return integer
     */
    public function getCourseId()
    {
        return $this->courseId;
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
    public function getCourseName()
    {
        return $this->courseName;
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
     * @param mixed $facultyDetails            
     */
    public function setFacultyDetails($facultyDetails)
    {
        $this->facultyDetails = $facultyDetails;
    }

    /**
     *
     * @return mixed
     */
    public function getFacultyDetails()
    {
        return $this->facultyDetails;
    }

    /**
     *
     * @param mixed $studentDetails            
     */
    public function setStudentDetails($studentDetails)
    {
        $this->studentDetails = $studentDetails;
    }

    /**
     *
     * @return mixed
     */
    public function getStudentDetails()
    {
        return $this->studentDetails;
    }
    
    /**
     *
     * @return boolean $createViewAcademicUpdate
     */
    public function setCreateViewAcademicUpdate($createViewAcademicUpdate)
    {
    	$this->createViewAcademicUpdate = $createViewAcademicUpdate;
    }
    
    /**
     *
     *  @return boolean $createViewAcademicUpdate
     */
    public function setViewAllAcademicUpdateCourses($viewAllAcademicUpdateCourses)
    {
    	$this->viewAllAcademicUpdateCourses = $viewAllAcademicUpdateCourses;
    }
}