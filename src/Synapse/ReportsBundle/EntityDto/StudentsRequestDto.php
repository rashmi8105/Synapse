<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class StudentsRequestDto{
    
    /**
     * ids of students being requested
     *
     * @var string @JMS\Type("string")
     */
    private $studentIds;
    
    /**
     *
     * @param string $studentIds
     */
    public function setStudentIds($studentIds)
    {
        $this->studentIds = $studentIds;
    }
    
    /**
     *
     * @return string
     */
    public function getStudentIds()
    {
        return $this->studentIds;
    }
}