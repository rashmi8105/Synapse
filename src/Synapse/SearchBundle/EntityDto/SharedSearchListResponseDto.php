<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class SharedSearchListResponseDto
{

    /**
     * savedSearchId
     *
     * @var integer @JMS\Type("integer")
     */
    private $savedSearchId;

    /**
     * sharedSearchId
     *
     * @var integer @JMS\Type("integer")
     */
    private $sharedSearchId;

    /**
     * searchName
     *
     * @var string @JMS\Type("string")
     */
    private $searchName;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\SearchBundle\EntityDto\SharedByUsersArrayDto>")
     *     
     */
    private $sharedByUsers;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\SearchBundle\EntityDto\SharedWithUsersArrayDto>")
     *     
     */
    private $sharedWithUsers;

    /**
     *
     * @param integer $savedSearchId            
     */
    public function setSavedSearchId($savedSearchId)
    {
        $this->savedSearchId = $savedSearchId;
    }

    /**
     *
     * @return integer
     */
    public function getSavedSearchId()
    {
        return $this->savedSearchId;
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
     * @param string $searchName            
     */
    public function setSearchName($searchName)
    {
        $this->searchName = $searchName;
    }

    /**
     *
     * @return string
     */
    public function getSearchName()
    {
        return $this->searchName;
    }

    /**
     *
     * @param Object $sharedByUsers            
     */
    public function setSharedByUsers($sharedByUsers)
    {
        $this->sharedByUsers = $sharedByUsers;
    }

    /**
     *
     * @return Object
     */
    public function getSharedByUsers()
    {
        return $this->sharedByUsers;
    }

    /**
     *
     * @param Object $sharedWithUsers            
     */
    public function setSharedWithUsers($sharedWithUsers)
    {
        $this->sharedWithUsers = $sharedWithUsers;
    }

    /**
     *
     * @return Object
     */
    public function getSharedWithUsers()
    {
        return $this->sharedWithUsers;
    }
}