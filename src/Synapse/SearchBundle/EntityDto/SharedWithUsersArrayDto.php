<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class SharedWithUsersArrayDto
{

    /**
     * sharedWithPersonId
     *
     * @var integer @JMS\Type("integer")
     */
    private $sharedWithPersonId;

    /**
     * sharedWithPersonId
     *
     * @var integer @JMS\Type("integer")
     */
    private $sharedSearchId;

    /**
     * dateShared
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $dateShared;

    /**
     * sharedWithFirstName
     *
     * @var string @JMS\Type("string")
     */
    private $sharedWithFirstName;

    /**
     * sharedWithLastName
     *
     * @var string @JMS\Type("string")
     */
    private $sharedWithLastName;

    /**
     *
     * @param integer $sharedWithPersonId            
     */
    public function setSharedWithPersonId($sharedWithPersonId)
    {
        $this->sharedWithPersonId = $sharedWithPersonId;
    }

    /**
     *
     * @return integer
     */
    public function getSharedWithPersonId()
    {
        return $this->sharedWithPersonId;
    }
    
    /**
     *
     * @param integer $sharedSearchId
     */
    public function setSharedSearchId($sharedSearchId)
    {
        $this->sharedSearchId = $sharedSearchId;
    }
    
    /**
     *
     * @return integer
     */
    public function getSharedSearchIdSearchId()
    {
        return $this->sharedSearchId;
    }

    /**
     *
     * @param mixed $dateShared            
     */
    public function setDateShared($dateShared)
    {
        $this->dateShared = $dateShared;
    }

    /**
     *
     * @return mixed
     */
    public function getDateShared()
    {
        return $this->dateShared;
    }

    /**
     *
     * @param string $sharedWithFirstName            
     */
    public function setSharedWithFirstName($sharedWithFirstName)
    {
        $this->sharedWithFirstName = $sharedWithFirstName;
    }

    /**
     *
     * @return string
     */
    public function getSharedWithFirstName()
    {
        return $this->sharedWithFirstName;
    }

    /**
     *
     * @param string $sharedWithLastName            
     */
    public function setSharedWithLastName($sharedWithLastName)
    {
        $this->sharedWithLastName = $sharedWithLastName;
    }

    /**
     *
     * @return string
     */
    public function getSharedWithLastName()
    {
        return $this->sharedWithLastName;
    }
}