<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;


class CoursesDto
{
    /**
     * Determines whether all courses apply to a Request or not.
     *
     * @var boolean
     * @JMS\Type("boolean")
     *
     */
    private $isAll;
    
    /**
     * Determines the courses that apply to a Request.
     *
     * @var string
     * @JMS\Type("string")
     *
     */
    private $selectedCourseIds;

    /**
     * Set whether all courses apply to a Request or not.
     *
     * @param boolean $isAll
     */
    public function setIsAll($isAll)
    {
        $this->isAll = $isAll;
    }

    /**
     * Returns whether all courses apply to a Request or not.
     *
     * @return boolean
     */
    public function getIsAll()
    {
        return $this->isAll;
    }

    /**
     * Set the courses that apply to a Request.
     *
     * @param string $selectedCourseIds
     */
    public function setSelectedCourseIds($selectedCourseIds)
    {
        $this->selectedCourseIds = $selectedCourseIds;
    }

    /**
     * Returns the courses that apply to a request.
     *
     * @return string
     */
    public function getSelectedCourseIds()
    {
        return $this->selectedCourseIds;
    }


   

}