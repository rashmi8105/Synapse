<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Tier
 *
 * @package Synapse\MultiCampusBundle\EntityDto
 */
class CampusChangeRequestDto
{

    /**
     * Campus Change Request ID
     *
     * @var integer @JMS\Type("integer")
     *      @Assert\NotBlank()
     *     
     */
    private $requestId;

    /**
     * Change Request Status
     *
     * @var string @JMS\Type("string")
     *      @Assert\NotBlank()
     *      @Assert\Choice(choices = {"yes", "no"}, message = "Please select valid status")
     *     
     */
    private $status;

    /**
     * Return campus change request Id
     *
     * @return int
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * Set requested Id
     *
     * @param int $requestId            
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * Return change request status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set change request status
     *
     * @param string $status            
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}