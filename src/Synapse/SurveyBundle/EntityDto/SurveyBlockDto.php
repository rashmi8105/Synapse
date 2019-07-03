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
class SurveyBlockDto
{

    /**
     * Id of a survey block.
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * Name of a survey block.
     *
     * @var string @JMS\Type("string")
     */
    private $surveyBlockName;

    /**
     * Number of questions in a survey block.
     *
     * @var string @JMS\Type("string")
     */
    private $questionCount;

    /**
     * Number of factors in a survey block.
     *
     * @var string @JMS\Type("string")
     */
    private $factorCount;

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
     * Langid
     *
     * @var integer @JMS\Type("integer")
     */
    private $langId;

    public function setLang($langId)
    {
        $this->langId = $langId;
    }

    /**
     *
     * @return string
     */
    public function getLang()
    {
        return $this->langId;
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

    public function setQuestionCount($questionCount = 0)
    {
        $this->questionCount = $questionCount;
    }

    public function getQuestionCount()
    {
        return $this->questionCount;
    }

    public function setFactorCount($factorCount = 0)
    {
        $this->factorCount = $factorCount;
    }

    public function getFactorCount(){
        
        return $this->factorCount;
    }
}