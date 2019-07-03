<?php
namespace Synapse\StudentBulkActionsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 * 
 * @package Synapse\SearchBundle\EntityDto
 */
class BulkStudentsDto
{

    /**
     * $organizationId
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $organizationId;
    
    /**
     * type
     *
     * @var string @JMS\Type("string")
     */
    private $type;

    /**
     * $personStaffId
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $personStaffId;
    
    /**
     * $totalStudentCount
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $totalStudentCount;
    
    /**
     * $totalHasPermission
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $totalHasPermission;

    /**
     * searchAttributes
     * 
     * @var array @JMS\Type("array")
     */
    private $students;
    
   
    /**
     *
     * @param integer $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return integer
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

   /**
     *
     * @param integer $personStaffId            
     */
    public function setPersonStaffId($personStaffId)
    {
        $this->personStaffId = $personStaffId;
    }

    /**
     *
     * @return integer
     */
    public function getPersonStaffId()
    {
        return $this->personStaffId;
    }

    /**
     *
     * @param array $students            
     */
    public function setStudents($students)
    {
        $this->students = $students;
    }

    /**
     *
     * @return array
     */
    public function getStudents()
    {
        return $this->students;
    }
    
    /**
     *
     * @param integer $totalStudentCount
     */
    public function setTotalStudentCount($totalStudentCount)
    {
    	$this->totalStudentCount = $totalStudentCount;
    }
    
    /**
     *
     * @return integer
     */
    public function getTotalStudentCount()
    {
    	return $this->totalStudentCount;
    }
    
    /**
     *
     * @param integer $totalHasPermission
     */
    public function setTotalHasPermission($totalHasPermission)
    {
    	$this->totalHasPermission = $totalHasPermission;
    }
    
    /**
     *
     * @return integer
     */
    public function getTotalHasPermission()
    {
    	return $this->totalHasPermission;
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
    

}