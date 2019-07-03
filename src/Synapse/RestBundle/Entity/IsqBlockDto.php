<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class IsqBlockDto implements DtoInterface
{

    /**
     * Id
     *
     * @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $id;

    /**
     * Question label
     *
     * @JMS\Type("string")
     */
    private $questionLabel;
    
    /**
     * surveyId
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $surveyId;
    
    /**
     * cohortId
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $cohortId;

    /**
     * Block Selection
     *
     * @JMS\Type("boolean")
     */
    private $blockSelection;

    /**
     * Sequence No
     *
     * @JMS\Type("integer")
     */
    private $sequenceNo;
    
    /**
     * Question text
     *
     * @JMS\Type("string")
     */
    private $questionText;
    
    /**
     *
     * @param mixed $questionText
     */
    public function setQuestionText($questionText)
    {
    	$this->questionText = $questionText;
    }
    
    /**
     *
     * @return mixed
     */
    public function getQuestionText()
    {
    	return $this->questionText;
    }

    /**
     *
     * @param mixed $blockSelection            
     */
    public function setBlockSelection($blockSelection)
    {
        $this->blockSelection = $blockSelection;
    }

    /**
     *
     * @return mixed
     */
    public function getBlockSelection()
    {
        return $this->blockSelection;
    }

    /**
     *
     * @param mixed $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param mixed $questionLabel            
     */
    public function setQuestionLabel($questionLabel)
    {
        $this->questionLabel = $questionLabel;
    }

    /**
     *
     * @return mixed
     */
    public function getQuestionLabel()
    {
        return $this->questionLabel;
    }

    /**
     *
     * @param mixed $sequenceNo            
     */
    public function setSequenceNo($sequenceNo)
    {
        $this->sequenceNo = $sequenceNo;
    }

    /**
     *
     * @return mixed
     */
    public function getSequenceNo()
    {
        return $this->sequenceNo;
    }
    /**
     * @param int $surveyId
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
    }
    
    /**
     * @return int
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }
    
    /**
     * @param int $cohortId
     */
    public function setCohortId($cohortId)
    {
        $this->cohortId = $cohortId;
    }
    
    /**
     * @return int
     */
    public function getCohortId()
    {
        return $this->cohortId;
    }
    /**
     * Assign entity properties using an array
     *
     * @param array $attributes assoc array of values to assign
     * @return null
     */
    public function fromArray(array $attributes)
    {
        $this->id = isset($attributes['isqId']) ? (int)$attributes['isqId'] : null;
        $this->questionLabel = isset($attributes['questionLabel']) ? $attributes['questionLabel'] : null;
        $this->surveyId = isset($attributes['surveyId']) ? $attributes['surveyId'] : null;
        $this->cohortId = isset($attributes['cohortId']) ? $attributes['cohortId'] : null;
        $this->blockSelection = (isset($attributes['isqSelection'])) ? (bool)$attributes['isqSelection'] : null;
        $this->sequenceNo = isset($attributes['sequenceNo']) ? (int)$attributes['sequenceNo'] : null;
        $this->questionText = isset($attributes['questionText']) ? $attributes['questionText'] : null;
    }
}
