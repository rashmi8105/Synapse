<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Academic Update Details
 *
 * @package Synapse\RestBundle\Entity
 */
class AcademicUpdateDetailsResponseDto
{

    /**
     * request_id
     *
     * @var integer @JMS\Type("integer")
     */
    private $requestId;

    /**
     * request_name
     * @JMS\Type("string")
     *
     * @var string
     */
    private $requestName;

    /**
     * request_description
     * @JMS\Type("string")
     *
     * @var string
     */
    private $requestDescription;

    /**
     *
     * @var string @JMS\Type("string")
     */
    private $requestCreated;

    /**
     *
     * @var string @JMS\Type("string")
     */
    private $requestDue;

    /**
     * update_completed
     *
     * @var integer @JMS\Type("integer")
     */
    private $updateCompleted;

    /**
     * update_total
     *
     * @var integer @JMS\Type("integer")
     */
    private $updateTotal;

    /**
     * status
     * @JMS\Type("string")
     *
     * @var string
     */
    private $status;

    /**
     * request_from
     *
     * @var array @JMS\Type("array")
     *     
     */
    private $requestFrom;

    /**
     * staff
     *
     * @var string @JMS\Type("array<Synapse\AcademicUpdateBundle\EntityDto\FacultiesDetailsResponseDto>")
     *     
     */
    private $staff;

    /**
     *
     * @param int $requestId            
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     *
     * @return int
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     *
     * @param string $requestName            
     */
    public function setRequestName($requestName)
    {
        $this->requestName = $requestName;
    }

    /**
     *
     * @return string
     */
    public function getRequestName()
    {
        return $this->requestName;
    }

    /**
     *
     * @param string $requestDescription            
     */
    public function setRequestDescription($requestDescription)
    {
        $this->requestDescription = $requestDescription;
    }

    /**
     *
     * @return string
     */
    public function getRequestDescription()
    {
        return $this->requestDescription;
    }

    /**
     *
     * @param string $requestCreated
     */
    public function setRequestCreated($requestCreated)
    {
        $this->requestCreated = $requestCreated;
    }

    /**
     *
     * @return string
     */
    public function getRequestCreated()
    {
        return $this->requestCreated;
    }

    /**
     *
     * @param string $requestDue
     */
    public function setRequestDue($requestDue)
    {
        $this->requestDue = $requestDue;
    }

    /**
     *
     * @return string
     */
    public function getRequestDue()
    {
        return $this->requestDue;
    }

    /**
     *
     * @param int $updateCompleted            
     */
    public function setUpdateCompleted($updateCompleted)
    {
        $this->updateCompleted = $updateCompleted;
    }

    /**
     *
     * @return int
     */
    public function getUpdateCompleted()
    {
        return $this->updateCompleted;
    }

    /**
     *
     * @param int $updateTotal            
     */
    public function setUpdateTotal($updateTotal)
    {
        $this->updateTotal = $updateTotal;
    }

    /**
     *
     * @return int
     */
    public function getUpdateTotal()
    {
        return $this->updateTotal;
    }

    /**
     *
     * @param string $status            
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param array $requestFrom            
     */
    public function setRequestFrom($requestFrom)
    {
        $this->requestFrom = $requestFrom;
    }

    /**
     *
     * @return array
     */
    public function getRequestFrom()
    {
        return $this->requestFrom;
    }

    /**
     *
     * @param array $staff            
     */
    public function setStaff($staff)
    {
        $this->staff = $staff;
    }

    /**
     *
     * @return array
     */
    public function getStaff()
    {
        return $this->staff;
    }
}