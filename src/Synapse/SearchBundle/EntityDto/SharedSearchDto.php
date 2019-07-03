<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class SharedSearchDto
{

    /**
     * Shared search ID
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * Organization ID
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * Saved search ID
     *
     * @var integer @JMS\Type("integer")
     */
    private $savedSearchId;

    /**
     * Saved search name
     *
     * @var string @JMS\Type("string")
     */
    private $savedSearchName;

    /**
     * Person's ID who shared the search
     *
     * @var integer @JMS\Type("integer")
     */
    private $sharedByPersonId;

    /**
     * ID's of people have have access to the search
     *
     * @var string @JMS\Type("string")
     */
    private $sharedWithPersonIds;

    /**
     * Search attributes
     *
     * @var array @JMS\Type("array")
     */
    private $searchAttributes;

    /**
     * Returns the shared search ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the shared search ID
     *
     * @param integer $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns organization ID
     *
     * @return integer
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Sets organization ID
     *
     * @param integer $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Returns saved search ID
     *
     * @return integer
     */
    public function getSavedSearchId()
    {
        return $this->savedSearchId;
    }

    /**
     * Sets saved search ID
     *
     * @param integer $savedSearchId            
     */
    public function setSavedSearchId($savedSearchId)
    {
        $this->savedSearchId = $savedSearchId;
    }

    /**
     * Returns saved search's name
     *
     * @return string
     */
    public function getSavedSearchName()
    {
        return $this->savedSearchName;
    }
    /**
     * Sets saved search's name
     *
     * @param string $savedSearchName            
     */
    public function setSavedSearchName($savedSearchName)
    {
        $this->savedSearchName = $savedSearchName;
    }

    /**
     * Returns person's ID who shared the search
     *
     * @return integer
     */
    public function getSharedByPersonId()
    {
        return $this->sharedByPersonId;
    }

    /**
     * Sets person's ID who shared the search
     *
     * @param integer $sharedByPersonId            
     */
    public function setSharedByPersonId($sharedByPersonId)
    {
        $this->sharedByPersonId = $sharedByPersonId;
    }

    /**
     * Returns ID's of people who have access to the search
     *
     * @return string
     */
    public function getsharedWithPersonIds()
    {
        return $this->sharedWithPersonIds;
    }

    /**
     * Sets ID's of people who have access to the search
     *
     * @param string $sharedWithPersonIds            
     */
    public function setsharedWithPersonIds($sharedWithPersonIds)
    {
        $this->sharedWithPersonIds = $sharedWithPersonIds;
    }

    /**
     * Returns search attributes
     *
     * @return array
     */
    public function getSearchAttributes()
    {
        return $this->searchAttributes;
    }

    /**
     * Sets search attributes
     *
     * @param array $searchAttributes            
     */
    public function setSearchAttributes($searchAttributes)
    {
        $this->searchAttributes = $searchAttributes;
    }

}