<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object
 *
 * @package Synapse\RestBundle\Entity
 */
class UserConflictsDto
{

    /**
     * conflictCategory
     *
     * @var string @JMS\Type("string")
     */
    private $conflictCategory;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\ConflictsByCategoryDto>")
     *     
     *     
     */
    private $conflicts;

    /**
     *
     * @param string $conflictCategory            
     */
    public function setConflictCategory($conflictCategory)
    {
        $this->conflictCategory = $conflictCategory;
    }

    /**
     *
     * @return string
     */
    public function getConflictCategory()
    {
        return $this->conflictCategory;
    }

    /**
     *
     * @param array $conflicts            
     */
    public function setConflicts($conflicts)
    {
        $this->conflicts = $conflicts;
    }

    /**
     *
     * @return array
     */
    public function getConflicts()
    {
        return $this->conflicts;
    }
}