<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class IssueSurveyQuestionsArrayDto
{
    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;
    
    /**
     * type
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $type;
    
    /**
     * text
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $text;
    
    /**
     *
      * @var object @JMS\Type("array")
     *     
     */
    private $options;
    
    /**
     *
     * @param integer $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return integer
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
     * @param array $options            
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
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
}
    