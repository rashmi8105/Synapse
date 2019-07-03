<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for RiskLevels
 *
 * @package Synapse\SearchBundle\EntityDto
 */
class RiskLevelsDto
{

    /**
     * riskLevel
     *
     * @var string @JMS\Type("integer")
     */
    private $riskLevel;

    /**
     * riskText
     *
     * @var string @JMS\Type("string")
     */
    private $riskText;

    /**
     * imageName
     *
     * @var string @JMS\Type("string")
     */
    private $imageName;

    /**
     * colorHex
     *
     * @var string @JMS\Type("string")
     */
    private $colorHex;

    /**
     *
     * @param integer $riskLevel            
     */
    public function setRiskLevel($riskLevel)
    {
        $this->riskLevel = $riskLevel;
    }

    /**
     *
     * @return integer
     */
    public function getRiskLevel()
    {
        return $this->riskLevel;
    }

    /**
     *
     * @param string $riskText            
     */
    public function setRiskText($riskText)
    {
        $this->riskText = $riskText;
    }

    /**
     *
     * @return string
     */
    public function getRiskText()
    {
        return $this->riskText;
    }

    /**
     *
     * @param string $imageName            
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     *
     * @param string $colorHex            
     */
    public function setColorHex($colorHex)
    {
        $this->colorHex = $colorHex;
    }

    /**
     *
     * @return string
     */
    public function getColorHex()
    {
        return $this->colorHex;
    }
}