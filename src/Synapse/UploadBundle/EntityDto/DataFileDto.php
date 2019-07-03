<?php
namespace Synapse\UploadBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for the generation of the datafile dump
 * *
 *
 * @package Synapse\UploadBundle\EntityDto
 *
 */
class DataFileDto
{
    /**
     * type is the type of datafile that is being created
     *
     * @var string @JMS\Type("integer")
     */
    private $uploadTypeId;

    /**
     * UploadTypeId is ids for specific datafile information
     * e.x. Group Id and Static List Id
     *
     * @var string @JMS\Type("string")
     */
    private $type;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getUploadTypeId()
    {
        return $this->uploadTypeId;
    }

    /**
     * @param string $uploadTypeId
     */
    public function setUploadTypeId($uploadTypeId)
    {
        $this->uploadTypeId = $uploadTypeId;
    }
}

