<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object
 *
 * @package Synapse\RestBundle\Entity
 */
class ResolveConflictDto
{

    /**
     * resolveType
     *
     * @var string @JMS\Type("string")
     */
    private $resolveType;

    /**
     *
     * @param string $resolveType            
     */
    public function setResolveType($resolveType)
    {
        $this->resolveType = $resolveType;
    }

    /**
     *
     * @return string
     */
    public function getResolveType()
    {
        return $this->resolveType;
    }
}
