<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class OrgQuesDetailsDto
{

    /**
     * organizationId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     * questionTypeId
     *
     * @var integer @JMS\Type("string")
     *     
     */
    private $questionTypeId;

    /**
     * questionCategoryId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $questionCategoryId;

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

    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return integer
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    public function setQuestionTypeId($questionTypeId)
    {
        $this->questionTypeId = $questionTypeId;
    }

    /**
     *
     * @return string
     */
    public function getQuestionTypeId()
    {
        return $this->questionTypeId;
    }

    public function setQuestionCategoryId($questionCategoryId)
    {
        $this->questionCategoryId = $questionTypeId;
    }

    /**
     *
     * @return integer
     */
    public function getQuestionCategoryId()
    {
        return $this->questionCategoryId;
    }

    public function setQuestionKey($questionKey)
    {
        $this->questionKey = $questionKey;
    }

    /**
     *
     * @return string
     */
    public function getQuestionKey()
    {
        return $this->questionKey;
    }

    public function setQuestionText($questionText)
    {
        $this->questionText = $questionText;
    }

    /**
     *
     * @return string
     */
    public function getQuestionText()
    {
        return $this->questionText;
    }
    
    
    
    
   }
