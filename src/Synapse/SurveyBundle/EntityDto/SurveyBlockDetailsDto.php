<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for SurveyBlock
 *
 * @package Synapse\SurveyBundle\EntityDto
 */
class SurveyBlockDetailsDto
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $id;

    /**
     * text
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $text;

    /**
     * type
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $type;
    

    /**
     * surveyId
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $surveyId;
    

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
     * @param string $text            
     */
    public function setText($text)
    {
        if (! $text) {
            $text = '';
        }
        $this->text = $text;
    }

    /**
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     *
     * @param string $type            
     */
    public function setType($type)
    {
        if (! $type) {
            $type = '';
        }
        $this->type = $type;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    

    /**
     *
     * @param int $surveyId
     */
    public function setSurveyId($surveyId)
    {
    	$this->surveyId = $surveyId;
    }
    
    /**
     *
     * @return int
     */
    public function getSurveyId()
    {
    	return $this->surveyId;
    }
}