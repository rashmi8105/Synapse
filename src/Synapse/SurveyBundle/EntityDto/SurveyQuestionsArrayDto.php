<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
/**
 * Data Transfer Object for Survey Question Array
 *
 * @package Synapse\SurveyBundle\EntityDto
 */
class SurveyQuestionsArrayDto
{
    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $id;
    
    /**
     * questionKey
     *
     * @var string @JMS\Type("string")
     *
     */
    private $questionKey;
    
    /**
     * questionText
     *
     * @var string @JMS\Type("string")
     *
     */
    private $questionText;
    
    /**
     * type
     *
     * @var string @JMS\Type("string")
     *
     */
    private $type;
    
    /**
     * options
     *
     * @var array @JMS\Type("array")
     */
    private $options;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * @param string $questionKey
     */
    public function setQuestionKey($questionKey)
    {
        $this->questionKey = $questionKey;
    }
    
    /**
     * @return string
     */
    public function getQuestionKey()
    {
        return $this->questionKey;
    }
    

    /**
     * @param string $questionText
     */
    public function setQuestionText($questionText)
    {
        $this->questionText = $questionText;
    }

    /**
     * @return string
     */
    public function getQuestionText()
    {
        return $this->questionText;
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
    public function getType()
    {
        return $this->type;
    }    
}