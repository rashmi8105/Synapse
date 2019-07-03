<?php
namespace Synapse\StaticListBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for StaticList
 *
 * @package Synapse\StaticListBundle\EntityDto
 */
class StaticListDto
{

    /**
     * id of a static list
     *
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $staticlistId;

    /**
     * name of a static list
     *
     * @JMS\Type("string")
     *
     * @var string
     */
    private $staticlistName;

    /**
     * description of a static list
     *
     * @JMS\Type("string")
     *
     * @var string
     */
    private $staticlistDescription;

    /**
     * if of the organization that contains the static list
     *
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $organizationId;

    /**
     * id of the person that created the static list
     *
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $personId;

    /**
     * number of students within a static list
     *
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $studentCount;

    /**
     * datetime that a static list was created
     *
     * @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * datetime that a static list was last modified at
     *
     * modifiedAt
     *
     * @var \Datetime @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $modifiedAt;

    /**
     * name of he person that the static list was shared with
     *
     * @JMS\Type("string")
     *
     * @var string
     */
    private $sharedPersonName;

    /**
     * email of the shared person
     *
     * @JMS\Type("string")
     *
     * @var string
     */
    private $sharedPersonEmail;

    /**
     * datetime that a static list was shared
     *
     * @var \Datetime @JMS\Type("DateTime<'Y-m-d'>")
     */
    private $sharedOn;

    /**
     * person name that modified a static list
     *
     * @var string @JMS\Type("string")
     */
    private $modifiedBy;

    /**
     * details of a static list
     *
     * @var array @JMS\Type("array")
     */
    private $staticListDetails;

    /**
     * details of the students within a static list
     *
     * @var array @JMS\Type("array")
     */
    private $studentsDetails;

    /**
     * id of a student in a static list
     *
     * @var integer @JMS\Type("string")
     */
    private $studentId;

    /**
     * type of edit action being performed on a student
     *
     * @var string @JMS\Type("string")
     */
    private $studentEditType;

    /**
     *
     * @return int
     */
    public function getStaticlistId()
    {
        return $this->staticlistId;
    }

    /**
     *
     * @param integer $staticlistId
     */
    public function setStaticlistId($staticlistId)
    {
        $this->staticlistId = $staticlistId;
    }

    /**
     *
     * @return string
     */
    public function getStaticlistName()
    {
        return $this->staticlistName;
    }

    /**
     *
     * @param string $staticlistName
     */
    public function setStaticlistName($staticlistName)
    {
        $this->staticlistName = $staticlistName;
    }

    /**
     *
     * @return string
     */
    public function getStaticlistDescription()
    {
        return $this->staticlistDescription;
    }

    /**
     *
     * @param string $staticlistDescription
     */
    public function setStaticlistDescription($staticlistDescription)
    {
        $this->staticlistDescription = $staticlistDescription;
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
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     *
     * @param integer $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return integer
     */
    public function getStudentCount()
    {
        return $this->studentCount;
    }

    /**
     *
     * @param integer $studentCount
     */
    public function setStudentCount($studentCount)
    {
        $this->studentCount = $studentCount;
    }

    /**
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     *
     * @param \DateTime $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     *
     * @return string
     */
    public function getSharedPersonName()
    {
        return $this->sharedPersonName;
    }

    /**
     *
     * @param string $sharedPersonName
     */
    public function setSharedPersonName($sharedPersonName)
    {
        $this->sharedPersonName = $sharedPersonName;
    }

    /**
     *
     * @return string
     */
    public function getSharedPersonEmail()
    {
        return $this->sharedPersonEmail;
    }

    /**
     *
     * @param string $sharedPersonEmail
     */
    public function setSharedPersonEmail($sharedPersonEmail)
    {
        $this->sharedPersonEmail = $sharedPersonEmail;
    }

    /**
     *
     * @return \DateTime
     */
    public function getSharedOn()
    {
        return $this->sharedOn;
    }

    /**
     *
     * @param \DateTime $sharedOn
     */
    public function setSharedOn($sharedOn)
    {
        $this->sharedOn = $sharedOn;
    }

    /**
     *
     * @return string
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     *
     * @param string $modifiedBy
     */
    public function setModifiedBy($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;
    }

    /**
     *
     * @return array
     */
    public function getStaticListDetails()
    {
        return $this->staticListDetails;
    }

    /**
     *
     * @param array $staticListDetails
     */
    public function setStaticListDetails($staticListDetails)
    {
        $this->staticListDetails = $staticListDetails;
    }

    /**
     *
     * @return array
     */
    public function getStudentsDetails()
    {
        return $this->studentsDetails;
    }

    /**
     *
     * @param array $studentsDetails
     */
    public function setStudentsDetails($studentsDetails)
    {
        $this->studentsDetails = $studentsDetails;
    }


    /**
     *
     * @return string
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     *
     * @param string $studentId
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     *
     * @return string
     */
    public function getStudentEditType()
    {
        return $this->studentEditType;
    }

    /**
     *
     * @param string $studentEditType
     */
    public function setStudentEditType($studentEditType)
    {
        $this->studentEditType = $studentEditType;
    }
}