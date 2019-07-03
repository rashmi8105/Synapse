<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;


abstract class FilteredStudentListRecordDto
{
    /**
     * @var integer @JMS\Type("integer")
     */
    private $studentId;

    /**
     * @var string @JMS\Type("string")
     */
    private $firstName;

    /**
     * @var string @JMS\Type("string")
     */
    private $lastName;

    /**
     * @var string @JMS\Type("string")
     */
    private $riskColor;

    /**
     * @var string @JMS\Type("string")
     */
    private $riskImageName;

    /**
     * @var string @JMS\Type("string")
     */
    private $classLevel;

    /**
     * @return int
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * @param int $studentId
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getRiskColor()
    {
        return $this->riskColor;
    }

    /**
     * @param string $riskColor
     */
    public function setRiskColor($riskColor)
    {
        $this->riskColor = $riskColor;
    }

    /**
     * @return string
     */
    public function getRiskImageName()
    {
        return $this->riskImageName;
    }

    /**
     * @param string $riskImageName
     */
    public function setRiskImageName($riskImageName)
    {
        $this->riskImageName = $riskImageName;
    }

    /**
     * @return string
     */
    public function getClassLevel()
    {
        return $this->classLevel;
    }

    /**
     * @param string $classLevel
     */
    public function setClassLevel($classLevel)
    {
        $this->classLevel = $classLevel;
    }

}