<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Data Transfer Object for Academic Update Details
 *
 * @package Synapse\RestBundle\Entity
 */
class AcademicUpdateDto
{

    /**
     * The ID of an Academic Update Request.
     *
     * @var integer @JMS\Type("integer")
     */
    private $requestId;

    /**
     * Boolean value that determines whether or not the user is an Adhoc user.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $isAdhoc;

    /**
     * The ID of the course that an Academic Update Request applies to.
     *
     * @var integer @JMS\Type("integer")
     */
    private $courseId;

    /**
     * The name of an Academic Update Request.
     *
     * @var string @JMS\Type("string")
     */
    private $requestName;

    /**
     * The description of an Academic Update Request.
     *
     * @var string @JMS\Type("string")
     */
    private $requestDescription;

    /**
     * The date that an Academic Update Request is created.
     *
     * @var DateTime @JMS\Type("DateTime<'d/m/Y'>")
     */
    private $requestCreated;

    /**
     * The date that an Academic Update Request is due.
     *
     * @var DateTime @JMS\Type("DateTime<'d/m/Y'>")
     */
    private $requestDue;

    /**
     * The amount of an Academic Update Request that has been completed, shown as a percent.
     *
     * @var integer @JMS\Type("integer")
     */
    private $requestCompletePercent;

    /**
     * Save status of the Academic Update. ['saved'=Academic Update has been saved but not submitted, 'send'=Academic Update has been marked to send.]
     *
     * @var string @JMS\Type("string")
     */
    private $saveType;

    /**
     * A string that holds specific details about an Academic Update Request.
     *
     * @var AcademicUpdateDetailsDto[] @JMS\Type("array<Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDetailsDto>")
     */
    private $requestDetails;

    /**
     * Returns that amount that an Academic Update Request has been completed, displayed as a percentage.
     *
     * @return integer
     */
    public function getRequestCompletePercent()
    {
        return $this->requestCompletePercent;
    }

    /**
     * Set the percentage value that an Academic Update Request has been completed.
     *
     * @param integer $requestCompletePercent
     */
    public function setRequestCompletePercent($requestCompletePercent)
    {
        $this->requestCompletePercent = $requestCompletePercent;
    }

    /**
     * Returns the name of an Academic Update Request.
     *
     * @return string
     */
    public function getRequestName()
    {
        return $this->requestName;
    }

    /**
     * Set the name of an Academic Update Request.
     *
     * @param string $requestName
     */
    public function setRequestName($requestName)
    {
        $this->requestName = $requestName;
    }

    /**
     * Returns whether an Academic Update is 'saved' or 'send'.
     *
     * @return string
     */
    public function getSaveType()
    {
        return $this->saveType;
    }

    /**
     * Set the save status of an Academic Update. Can be 'saved' or 'send'.
     *
     * @param string $saveType
     */
    public function setSaveType($saveType)
    {
        $this->saveType = $saveType;
    }

    /**
     * Returns the ID of an Academic Update Request.
     *
     * @return int
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * Set the ID of an Academic Update Request.
     *
     * @param int $requestId
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * Returns the date that an Academic Update Request is due.
     *
     * @return DateTime
     */
    public function getRequestDue()
    {
        return $this->requestDue;
    }

    /**
     * Set the date that an Academic Update Request is due.
     *
     * @param DateTime $requestDue
     */
    public function setRequestDue($requestDue)
    {
        $this->requestDue = $requestDue;
    }

    /**
     * Returns the details of an Academic Update Request.
     *
     * @return AcademicUpdateDetailsDto[]
     */
    public function getRequestDetails()
    {
        return $this->requestDetails;
    }

    /**
     * Set the details of an Academic Update Request.
     *
     * @param AcademicUpdateDetailsDto[] $requestDetails
     */
    public function setRequestDetails($requestDetails)
    {
        $this->requestDetails = $requestDetails;
    }

    /**
     * Returns the name of an Academic Update Request.
     *
     * @return string
     */
    public function getRequestDescription()
    {
        return $this->requestDescription;
    }

    /**
     * Set the description of an Academic Update Request.
     *
     * @param string $requestDescription
     */
    public function setRequestDescription($requestDescription)
    {
        $this->requestDescription = $requestDescription;
    }

    /**
     * Returns the date that an Academic Update Request was created.
     *
     * @return DateTime
     */
    public function getRequestCreated()
    {
        return $this->requestCreated;
    }

    /**
     * Set the date that an Academic Update Request was created.
     *
     * @param DateTime $requestCreated
     */
    public function setRequestCreated($requestCreated)
    {
        $this->requestCreated = $requestCreated;
    }

    /**
     * Returns whether or not the user is an Adhoc user.
     *
     * @return boolean
     */
    public function getIsAdhoc()
    {
        return $this->isAdhoc;
    }

    /**
     * Set whether or not a user is an Adhoc user.
     *
     * @param boolean $isAdhoc
     */
    public function setIsAdhoc($isAdhoc)
    {
        $this->isAdhoc = $isAdhoc;
    }

    /**
     * Returns the CourseId of the course that an Academic Update Request applies to.
     *
     * @return integer
     */
    public function getCourseId()
    {
        return $this->courseId;
    }

    /**
     * Function to set the CourseId of the course that an Academic Update Request applies to.
     *
     * @param integer $courseId
     */
    public function setCourseId($courseId)
    {
        $this->courseId = $courseId;
    }

}