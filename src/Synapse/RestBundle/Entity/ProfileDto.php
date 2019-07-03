<?php

namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 *
 * @package Synapse\RestBundle\Entity
 *
 */
class ProfileDto
{
    /**
     * calenderAssignment
     *
     * @var string @JMS\Type("string")
     */
    private $calenderAssignment;

    /**
     * categoryType
     *
     * @var array @JMS\Type("array")
     */
    private $categoryType;

    /**
     * decimalPoints
     *
     * @var integer @JMS\Type("integer")
     */
    private $decimalPoints;

    /**
     * definitionType
     *
     * @var string @JMS\Type("string")
     *
     */
    private $definitionType;

    /**
     * displayName
     *
     * @var string @JMS\Type("string")
     */
    private $displayName;

    /**
     * fieldNameCanBeEditedIfMetaDataMapped
     *
     * @var array @JMS\Type("array")
     */
    private $fieldNameCanBeEditedIfMetaDataMapped;

    /**
     * MetadataId
     *
     * @var string @JMS\Type("integer")
     *
     */
    private $id;

    /**
     * isMetaDataMapped
     *
     * @var bool @JMS\Type("boolean")
     */
    private $isMetaDataMapped;

    /**
     * isrequired
     *
     * @var integer @JMS\Type("integer")
     */
    private $isRequired;

    /**
     * metadataType
     *
     * @var string @JMS\Type("string")
     *
     */
    private $itemDataType;

    /**
     * itemLabel
     *
     * @var string @JMS\Type("string")
     */
    private $itemLabel;

    /**
     * itemSubtext
     *
     * @var string @JMS\Type("string")
     */
    private $itemSubtext;

    /**
     * Langid
     *
     * @var integer @JMS\Type("integer")
     */
    private $langId;

    /**
     * listname
     *
     * @var string @JMS\Type("string")
     */
    private $listName;

    /**
     * listvalue
     *
     * @var string @JMS\Type("string")
     */
    private $listValue;

    /**
     * maxrange
     *
     * @var double @JMS\Type("double")
     */
    private $maxDigits;

    /**
     * minrange
     *
     * @var double @JMS\Type("double")
     */
    private $minDigits;

    /**
     * Number Type
     *
     * @var array @JMS\Type("array")
     */
    private $numberType;

    /**
     * organizationId
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * profileBlockId
     *
     * @var integer @JMS\Type("integer")
     */
    private $profileBlockId;

    /**
     * profileBlockName
     *
     * @var integer @JMS\Type("string")
     */
    private $profileBlockName;

    /**
     * sequenceNo
     *
     * @var integer @JMS\Type("integer")
     */
    private $sequenceNo;

    /**
     * status
     *
     * @var string @JMS\Type("string")
     */
    private $status;

    /**
     *
     * @param array $categoryType
     */
    public function setCategoryType($categoryType)
    {
        $this->categoryType = $categoryType;
    }

    /**
     *
     * @return array
     */
    public function getCategoryType()
    {
        return $this->categoryType;
    }

    /**
     *
     * @param int $decimalPoints
     */
    public function setDecimalPoints($decimalPoints)
    {
        $this->decimalPoints = $decimalPoints;
    }

    /**
     *
     * @return int
     */
    public function getDecimalPoints()
    {
        return $this->decimalPoints;
    }

    /**
     *
     * @param string $definitionType
     */
    public function setDefinitionType($definitionType)
    {
        $this->definitionType = $definitionType;
    }

    /**
     *
     * @return string
     */
    public function getDefinitionType()
    {
        return $this->definitionType;
    }

    /**
     *
     * @param int $isRequired
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequired = $isRequired;
    }

    /**
     *
     * @return int
     */
    public function getIsRequired()
    {
        return $this->isRequired;
    }

    /**
     *
     * @param string $itemDataType
     */
    public function setItemDataType($itemDataType)
    {
        $this->itemDataType = $itemDataType;
    }

    /**
     *
     * @return string
     */
    public function getItemDataType()
    {
        return $this->itemDataType;
    }

    /**
     *
     * @param string $itemLabel
     */
    public function setItemLabel($itemLabel)
    {
        $this->itemLabel = $itemLabel;
    }

    /**
     *
     * @return string
     */
    public function getItemLabel()
    {
        return $this->itemLabel;
    }

    /**
     *
     * @param string $itemSubtext
     */
    public function setItemSubtext($itemSubtext)
    {
        $this->itemSubtext = $itemSubtext;
    }

    /**
     *
     * @return string
     */
    public function getItemSubtext()
    {
        return $this->itemSubtext;
    }

    /**
     *
     * @param int $langId
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     *
     * @return int
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     *
     * @param string $listName
     */
    public function setListName($listName)
    {
        $this->listName = $listName;
    }

    /**
     *
     * @return string
     */
    public function getListName()
    {
        return $this->listName;
    }

    /**
     *
     * @param string $listValue
     */
    public function setListValue($listValue)
    {
        $this->listValue = $listValue;
    }

    /**
     *
     * @return string
     */
    public function getListValue()
    {
        return $this->listValue;
    }

    /**
     *
     * @param int $maxDigits
     */
    public function setMaxDigits($maxDigits)
    {
        $this->maxDigits = $maxDigits;
    }

    /**
     *
     * @return int
     */
    public function getMaxDigits()
    {
        return $this->maxDigits;
    }

    /**
     *
     * @param string $metadataId
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param int $minDigits
     */
    public function setMinDigits($minDigits)
    {
        $this->minDigits = $minDigits;
    }

    /**
     *
     * @return int
     */
    public function getMinDigits()
    {
        return $this->minDigits;
    }

    /**
     *
     * @param array $numberType
     */
    public function setNumberType($numberType)
    {
        $this->numberType = $numberType;
    }

    /**
     *
     * @return array
     */
    public function getNumberType()
    {
        return $this->numberType;
    }

    /**
     *
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param int $sequenceNo
     */
    public function setSequenceNo($sequenceNo)
    {
        $this->sequenceNo = $sequenceNo;
    }

    /**
     *
     * @return int
     */
    public function getSequenceNo()
    {
        return $this->sequenceNo;
    }

    /**
     *
     * @param int $profileBlockId
     */
    public function setProfileBlockId($profileBlockId)
    {
        $this->profileBlockId = $profileBlockId;
    }

    /**
     *
     * @return int
     */
    public function getProfileBlockId()
    {
        return $this->profileBlockId;
    }

    /**
     *
     * @param string $calenderAssignment
     */
    public function setCalenderAssignment($calenderAssignment)
    {
        $this->calenderAssignment = $calenderAssignment;
    }

    /**
     *
     * @return string
     */
    public function getCalenderAssignment()
    {
        return $this->calenderAssignment;
    }

    /**
     *
     * @param string $profileBlockName
     */
    public function setProfileBlockName($profileBlockName)
    {
        $this->profileBlockName = $profileBlockName;
    }

    /**
     *
     * @return string
     */
    public function getProfileBlockName()
    {
        return $this->profileBlockName;
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
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @return bool
     */
    public function getIsMetaDataMapped()
    {
        return $this->isMetaDataMapped;
    }

    /**
     * @param bool $isMetaDataMapped
     */
    public function setIsMetaDataMapped($isMetaDataMapped)
    {
        $this->isMetaDataMapped = $isMetaDataMapped;
    }

    /**
     * @return array
     */
    public function getFieldNameCanBeEditedIfMetaDataMapped()
    {
        return $this->fieldNameCanBeEditedIfMetaDataMapped;
    }

    /**
     * @param array $fieldNameCanBeEditedIfMetaDataMapped
     */
    public function setFieldNameCanBeEditedIfMetaDataMapped($fieldNameCanBeEditedIfMetaDataMapped)
    {
        $this->fieldNameCanBeEditedIfMetaDataMapped = $fieldNameCanBeEditedIfMetaDataMapped;
    }

}