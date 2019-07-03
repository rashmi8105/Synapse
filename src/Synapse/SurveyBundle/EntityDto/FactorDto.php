<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Factor
 *
 * @package Synapse\SurveyBundle\FactorDto
 */
class FactorDto
{

    /**
     * Id of the factor.
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * Deprecated value representing the language the factor was in. 1 = ENGLISH.
     *
     * @var integer @JMS\Type("integer")
     */
    private $langId;

    /**
     * Id of the survey that the factor applies to.
     *
     * @var integer @JMS\Type("integer")
     */
    private $surveyId;

    /**
     * Name of the factor.
     *
     * @var string @JMS\Type("string")
     *      @Assert\NotBlank(message = "Factor name should not be blank.")
     *      @Assert\Length(min = 1,
     *      max = 50,
     *      minMessage = "Define name before clicking add.",
     *      maxMessage = "Factor Name cannot be longer than {{ limit }} characters long"
     *      )
     */
    private $factorName;

    /**
     * The factor's order that it appears on the survey.
     *
     * @var integer @JMS\Type("integer")
     */
    private $order;

    /**
     * Sets the id of a factor.
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the id of a factor.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the language of a factor.
     *
     * @param int $langId            
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     * Returns the language of a factor.
     *
     * @return int
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     * Sets the name of a factor.
     *
     * @param string $factorName            
     */
    public function setFactorName($factorName)
    {
        $this->factorName = $factorName;
    }

    /**
     * Returns the name of a factor.
     *
     * @return string
     */
    public function getFactorName()
    {
        return $this->factorName;
    }

    /**
     * Sets the order number of a factor.
     *
     * @param int $order            
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Returns the order of a factor.
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Sets the id of the survey that the factor applies to.
     *
     * @param int $surveyId            
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    /**
     * Returns the id of the survey that the factor applies to.
     *
     * @return int
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }
}