<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class SharedByUsersArrayDto
{

    /**
     * sharedByPersonId
     *
     * @var integer @JMS\Type("integer")
     */
    private $sharedByPersonId;

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
     * sharedByFirstName
     *
     * @var string @JMS\Type("string")
     */
    private $sharedByFirstName;

    /**
     * sharedByLastName
     *
     * @var string @JMS\Type("string")
     */
    private $sharedByLastName;

    /**
     *
     * @param integer $sharedByPersonId            
     */
    public function setSharedByPersonId($sharedByPersonId)
    {
        $this->sharedByPersonId = $sharedByPersonId;
    }

    /**
     *
     * @return integer
     */
    public function getSharedByPersonId()
    {
        return $this->sharedByPersonId;
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
     * @param string $sharedByFirstName            
     */
    public function setSharedByFirstName($sharedByFirstName)
    {
        $this->sharedByFirstName = $sharedByFirstName;
    }

    /**
     *
     * @return string
     */
    public function getSharedByFirstName()
    {
        return $this->sharedByFirstName;
    }

    /**
     *
     * @param string $sharedByLastName            
     */
    public function setSharedByLastName($sharedByLastName)
    {
        $this->sharedByLastName = $sharedByLastName;
    }

    /**
     *
     * @return string
     */
    public function getSharedByLastName()
    {
        return $this->sharedByLastName;
    }
}