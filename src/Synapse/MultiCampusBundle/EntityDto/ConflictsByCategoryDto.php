<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object
 *
 * @package Synapse\RestBundle\Entity
 */
class ConflictsByCategoryDto
{

    /**
     *
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\ConflictPersonDetailsDto>")
     *     
     *     
     */
    private $conflictRecords;

    /**
     *
     * @param array $conflictRecords            
     */
    public function setConflictRecords($conflictRecords)
    {
        $this->conflictRecords = $conflictRecords;
    }

    /**
     *
     * @return array
     */
    public function getConflictRecords()
    {
        return $this->conflictRecords;
    }
}