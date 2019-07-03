<?php

namespace Synapse\RestBundle\DTO;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class BulkActionsDto
 *
 * @package Synapse\RestBundle\DTO
 */
class BulkActionsDto
{
    /**
     * Type of bulk action, i.e. referral
     *
     * @var string @JMS\Type("string")
     */
    private $type;

    /**
     * Array of student ids included in a bulk action
     *
     * @var array @JMS\Type("array")
     */
    private $studentIds;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getStudentIds()
    {
        return $this->studentIds;
    }

    /**
     * @param array $studentIds
     */
    public function setStudentIds($studentIds)
    {
        $this->studentIds = $studentIds;
    }
}