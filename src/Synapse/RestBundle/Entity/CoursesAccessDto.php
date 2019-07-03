<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class CoursesAccessDto implements DtoInterface
{

    /**
     * If True, this permission can view courses.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $viewCourses;

    /**
     * If True, this permission can create and view academic updates.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $createViewAcademicUpdate;
    
    /**
     * If True, this permission can view courses with academic updates.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $viewAllAcademicUpdateCourses;
    
    /**
     * If True, this permission can view all final grades.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $viewAllFinalGrades;

    /**
     * @return boolean
     */
    public function getCreateViewAcademicUpdate()
    {
        return $this->createViewAcademicUpdate;
    }

    /**
     * @param boolean $createViewAcademicUpdate
     */
    public function setCreateViewAcademicUpdate($createViewAcademicUpdate)
    {
        $this->createViewAcademicUpdate = $createViewAcademicUpdate;
    }

    /**
     * @return boolean
     */
    public function getViewAllAcademicUpdateCourses()
    {
        return $this->viewAllAcademicUpdateCourses;
    }

    /**
     * @param boolean $viewAllAcademicUpdateCourses
     */
    public function setViewAllAcademicUpdateCourses($viewAllAcademicUpdateCourses)
    {
        $this->viewAllAcademicUpdateCourses = $viewAllAcademicUpdateCourses;
    }

    /**
     * @return boolean
     */
    public function getViewAllFinalGrades()
    {
        return $this->viewAllFinalGrades;
    }

    /**
     * @param boolean $viewAllFinalGrades
     */
    public function setViewAllFinalGrades($viewAllFinalGrades)
    {
        $this->viewAllFinalGrades = $viewAllFinalGrades;
    }

    /**
     * @return boolean
     */
    public function getViewCourses()
    {
        return $this->viewCourses;
    }

    /**
     * @param boolean $viewCourses
     */
    public function setViewCourses($viewCourses)
    {
        $this->viewCourses = $viewCourses;
    }

    /**
     * Assign entity properties using an array
     *
     * @param array $attributes assoc array of values to assign
     * @return null
     */
    public function fromArray(array $attributes)
    {
        // Build from the org_permissionset table.
        $attributesLocal = $attributes;
        if (array_key_exists('view_courses', $attributes))
        {
            // Avoid cyclic memleak.
            unset($attributesLocal);
            $attributesLocal = [
            'viewCourses' => $attributes['view_courses'],
            'createViewAcademicUpdate' => $attributes['create_view_academic_update'],
            'viewAllAcademicUpdateCourses' => $attributes['view_all_academic_update_courses'],
            'viewAllFinalGrades' => $attributes['view_all_final_grades']
            ];
        }
        
        $this->viewCourses = (isset($attributesLocal['viewCourses'])) ? (bool)$attributesLocal['viewCourses'] : false;
        $this->createViewAcademicUpdate = (isset($attributesLocal['createViewAcademicUpdate'])) ? (bool)$attributesLocal['createViewAcademicUpdate'] : false;
        $this->viewAllAcademicUpdateCourses = (isset($attributesLocal['viewAllAcademicUpdateCourses'])) ? (bool)$attributesLocal['viewAllAcademicUpdateCourses'] : false;
        $this->viewAllFinalGrades = (isset($attributesLocal['viewAllFinalGrades'])) ? (bool)$attributesLocal['viewAllFinalGrades'] : false;
        
        // Avoid cyclic memleak.
        unset($attributesLocal);
    }
     
}
