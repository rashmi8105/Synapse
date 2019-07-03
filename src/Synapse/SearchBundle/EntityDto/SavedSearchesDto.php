<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Savesearch
 *
 * @package Synapse\SearchBundle\EntityDto
 *         
 */
class SavedSearchesDto
{

    /**
     * id of a saved search
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $savedSearchId;

    /**
     * name of a saved search
     *
     * @var string @JMS\Type("string")
     */
    private $searchName;

    /**
     * date that a saved search was created
     *
     * @var \DateTime @JMS\Type("DateTime")
     */
    private $dateCreated;

    /**
     *
     * @return int
     */
    public function getSavedSearchId()
    {
        return $this->savedSearchId;
    }

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
     * @return string
     */
    public function getSearchName()
    {
        return $this->searchName;
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
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     *
     * @param \DateTime $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }
}