<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 *
 * 
 * @package Synapse\RestBundle\Entity
 *         
 */
class ReOrderProfileDto
{

    /**
     * Metadata Id.
     * 
     * @var string @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $id;

    /**
     * Placeholder for the order location of a profile.
     * 
     * @var integer @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $sequenceNo;

    /**
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param int $sequenceNo            
     */
    public function setSequenceNo($sequenceNo)
    {
        $this->sequenceNo = $sequenceNo;
    }

    /**
     *
     * @return int
     */
    public function getSequenceNo()
    {
        return $this->sequenceNo;
    }
}