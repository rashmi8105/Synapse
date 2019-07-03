<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class CoordinatorCourseDto
{

    /**
     * totalCourse
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalCourse;

    /**
     * totalFaculty
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalFaculty;
    
    /**
     * studentId
     *
     * @var integer @JMS\Type("integer")
     */
    private $studentId;

    /**
     * totalStudents
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalStudents;

    /**
     * courseListTable
     *
     * @var Object @JMS\Type("array<Synapse\AcademicBundle\EntityDto\AcademicUpdateCourseListDTO>")
     */
    private $courseListTable;
    
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
     * $viewAllFinalGrades
     *
     * @JMS\Type("boolean")
     */
    private $viewAllFinalGrades;

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
     * @param Object | array $courseListTable
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
    
    /**
     *
     *  @return boolean $viewAllFinalGrades
     */
    public function setViewAllFinalGrades($viewAllFinalGrades)
    {
    	$this->viewAllFinalGrades = $viewAllFinalGrades;
    }
 
    
}