<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Nelmio\ApiDocBundle\Annotation;


class StudentsDto
{
    /**
     * Determines whether the Request applies to all students or not.
     *
     * @var boolean
     * @JMS\Type("boolean")
     *
     */
    private $isAll;
    
    /**
     * String of ID's that determines what students are included in the Request.
     *
     * @var string
     * @JMS\Type("string")
     *
     */
    private $selectedStudentIds;

    /**
     * List of student Id's - comma separated values.
     * Regex will accept alpha numeric comma separated values.
     *
     * @var string
     * @Assert\Regex("/^[A-Za-z0-9]+((,\s*|-)[A-Za-z0-9]+)*[A-Za-z0-9]+$/")
     * @JMS\Type("string")
     */
    private $studentIds;

    /**
     * Set whether the Request applies to All students or not.
     *
     * @param boolean $isAll
     */
    public function setIsAll($isAll)
    {
        $this->isAll = $isAll;
    }

    /**
     * Returns whether or not the Request applies to All students or not.
     *
     * @return boolean
     */
    public function getIsAll()
    {
        return $this->isAll;
    }

    /**
     * Set the IDs of the students that apply to a Request.
     *
     * @param string $selectedStudentIds
     */
    public function setSelectedStudentIds($selectedStudentIds)
    {
        $this->selectedStudentIds = $selectedStudentIds;
    }

    /**
     * Returns the IDs of the students that apply to a Request.
     *
     * @return string
     */
    public function getSelectedStudentIds()
    {
        return $this->selectedStudentIds;
    }

    /**
     * @param string $studentIds
     */
    public function setStudentIds($studentIds)
    {
        $studentIds = preg_replace("/ /", "", $studentIds);
        $this->studentIds = $studentIds;
    }

    /**
     * @return string
     */
    public function getStudentIds()
    {
        $this->studentIds = preg_replace("/ /", "", $this->studentIds);
        return $this->studentIds;
    }
}