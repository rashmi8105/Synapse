<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Synapse\SearchBundle\EntityDto\FilteredStudentListRecordDto;

/**
 * Class ProfileDrilldownRecordDto
 * @package Synapse\ReportsBundle\EntityDto
 */
class ProfileDrilldownRecordDto extends FilteredStudentListRecordDto
{
    /**
     * @var string @JMS\Type("string")
     */
    private $profileItemValue;

    /**
     * $studentIsActive
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $studentIsActive;

    /**
     * @return string
     */
    public function getProfileItemValue()
    {
        return $this->profileItemValue;
    }

    /**
     * @param string $profileItemValue
     */
    public function setProfileItemValue($profileItemValue)
    {
        $this->profileItemValue = $profileItemValue;
    }

    /**
     * @return boolean
     */
    public function getStudentIsActive()
    {
        return $this->studentIsActive;
    }

    /**
     * @param boolean $studentIsActive
     */
    public function setStudentIsActive($studentIsActive)
    {
        $this->studentIsActive = $studentIsActive;
    }
}