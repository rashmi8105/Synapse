<?php
namespace Synapse\SurveyBundle\EntityDto;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Survey Blocks
 *
 * @package Synapse\SurveyBundle\EntityDto
 */
class SurveyBlockDetailsResponseDto
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $id;

    /**
     * surveyBlockName
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $surveyBlockName;

    /**
     * totalCount
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $totalCount;

    /**
     * totalQuestionCount
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $totalQuestionCount;

    /**
     * totalFactorCount
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $totalFactorCount;


    /**
     *
     * @var Object @JMS\Type("array<Synapse\SurveyBundle\EntityDto\SurveyBlockDetailsDto>")
     *     
     */
    private $blockData;

    /**
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param string $surveyBlockName            
     */
    public function setSurveyBlockName($surveyBlockName)
    {
        if (! $surveyBlockName) {
            $surveyBlockName = '';
        }
        $this->surveyBlockName = $surveyBlockName;
    }

    /**
     *
     * @return string
     */
    public function getSurveyBlockName()
    {
        return $this->surveyBlockName;
    }

    public function setTotalCount($totalCount = 0)
    {
        $this->totalCount = $totalCount;
    }

    public function getTotalCount()
    {
        return $this->totalCount;
    }

    public function setTotalQuestionCount($totalQuestionCount = 0)
    {
        $this->totalQuestionCount = $totalQuestionCount;
    }

    public function getTotalQuestionCount()
    {
        return $this->totalQuestionCount;
    }

    public function setTotalFactorCount($totalFactorCount = 0)
    {
        $this->totalFactorCount = $totalFactorCount;
    }

    public function getTotalFactorCount()
    {
        return $this->totalFactorCount;
    }

    /**
     *
     * @param Object $blockData            
     */
    public function setBlockData($blockData)
    {
        $this->blockData = $blockData;
    }

    /**
     *
     * @return Object
     */
    public function getBlockData()
    {
        return $this->blockData;
    }
}