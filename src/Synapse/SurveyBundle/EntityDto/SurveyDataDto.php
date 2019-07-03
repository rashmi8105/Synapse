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
class SurveyDataDto
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
     * @var string @JMS\Type("string")
     *     
     */
    private $surveyId;

    /**
     * selected
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $selected;

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

    /**
     *
     * @param int $surveyId            
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
    }

    /**
     *
     * @return int
     */
    public function getSelected()
    {
        return $this->selected;
    }

   
}