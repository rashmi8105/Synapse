<?php
namespace Synapse\StaticListBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class StaticListDetailsDto
{

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $staticlistId;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $personId;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $studentId;

    /**
     * createdBy
     *
     * @var string @JMS\Type("string")
     */
    private $createdBy;

    /**
     * createdByUserName
     *
     * @var string @JMS\Type("string")
     */
    private $createdByUserName;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $staticlistName;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $staticlistDescription;

    /**
     * createdAt
     *
     * @var string
     */
    private $createdAt;

    /**
     * modifiedAt
     *
     * @var string
     */
    private $modifiedAt;

    /**
     * modifiedByUserName
     *
     * @var string @JMS\Type("string")
     */
    private $modifiedByUserName;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\StaticListBundle\EntityDto\StaticListDto>")
     *     
     */
    private $staticLists;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\StaticListBundle\EntityDto\StaticListDto>")
     *     
     */
    private $studentDetails;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $studentCount;

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
     * @return int
     */
    public function getStaticlistId()
    {
        return $this->staticlistId;
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
    public function getStaticlistName()
    {
        return $this->staticlistName;
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
     * @return string
     */
    public function getStaticlistDescription()
    {
        return $this->staticlistDescription;
    }

    /**
     *
     * @param string $createdBy            
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     *
     * @param string $createdByUserName            
     */
    public function setCreatedByUserName($createdByUserName)
    {
        $this->createdByUserName = $createdByUserName;
    }

    /**
     *
     * @return string
     */
    public function getCreatedByUserName()
    {
        return $this->createdBy;
    }

    /**
     *
     * @param string $createdAt            
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     *
     * @param string $modifiedAt            
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     *
     * @return string
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
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
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @return integer $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
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
     * @param integer $studentCount            
     */
    public function setStudentCount($studentCount)
    {
        $this->studentCount = $studentCount;
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
     * @param string $getModifiedByUserName            
     */
    public function setModifiedByUserName($modifiedByUserName)
    {
        $this->modifiedByUserName = $modifiedByUserName;
    }

    /**
     *
     * @return integer
     */
    public function getModifiedByUserName()
    {
        return $this->modifiedByUserName;
    }

    /**
     *
     * @param Object $staticlistDetails            
     */
    public function setStaticLists($staticLists)
    {
        $this->staticLists = $staticLists;
    }

    /**
     *
     * @return Object
     */
    public function getStaticLists()
    {
        return $this->staticLists;
    }

    /**
     *
     * @param Object $studentDetails
     */
    public function setStudentDetails($studentDetails)
    {
        $this->studentDetails = $studentDetails;
    }

    /**
     *
     * @return Object
     */
    public function getStudentDetails()
    {
        return $this->studentDetails;
    }

    /**
     *
     * @param integer $studentId            
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     *
     * @return int
     */
    public function getStudentId()
    {
        return $this->studentId;
    }
}