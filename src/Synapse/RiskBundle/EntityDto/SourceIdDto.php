<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class SourceIdDto
{

    /**
     * id of a survey
     *
     * @var integer @JMS\Type("integer")
     */
    private $surveyId;

    /**
     * id of a question
     *
     * @var integer @JMS\Type("integer")
     */
    private $questionId;

    /**
     * id of a factor
     *
     * @var integer @JMS\Type("integer")
     */
    private $factorId;

    /**
     * id of a question bank
     *
     * @var integer @JMS\Type("integer")
     */
    private $questionBankId;

    /**
     * id of an ebi profile
     *
     * @var integer @JMS\Type("integer")
     */
    private $ebiProfileId;

    /**
     * id of an isp
     *
     * @var integer @JMS\Type("integer")
     */
    private $ispId;

    /**
     * id of an isq
     *
     * @var integer @JMS\Type("integer")
     */
    private $isqId;

    /**
     * id of a campus
     *
     * @var string @JMS\Type("string")
     */
    private $campusId;

    /**
     * @return string
     */
    public function getCampusId()
    {
        return $this->campusId;
    }

    /**
     * @param string $campusId
     */
    public function setCampusId($campusId)
    {
        $this->campusId = $campusId;
    }

    /**
     * @return int
     */
    public function getSurveyId()
    {
        return $this->surveyId;
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
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * @param int $questionId
     */
    public function setQuestionId($questionId)
    {
        $this->questionId = $questionId;
    }

    /**
     * @return int
     */
    public function getQuestionBankId()
    {
        return $this->questionBankId;
    }

    /**
     * @param int $questionBankId
     */
    public function setQuestionBankId($questionBankId)
    {
        $this->questionBankId = $questionBankId;
    }

    /**
     * @return int
     */
    public function getIsqId()
    {
        return $this->isqId;
    }

    /**
     * @param int $isqId
     */
    public function setIsqId($isqId)
    {
        $this->isqId = $isqId;
    }

    /**
     * @return int
     */
    public function getIspId()
    {
        return $this->ispId;
    }

    /**
     * @param int $ispId
     */
    public function setIspId($ispId)
    {
        $this->ispId = $ispId;
    }

    /**
     * @return int
     */
    public function getFactorId()
    {
        return $this->factorId;
    }

    /**
     * @param int $factorId
     */
    public function setFactorId($factorId)
    {
        $this->factorId = $factorId;
    }

    /**
     * @return int
     */
    public function getEbiProfileId()
    {
        return $this->ebiProfileId;
    }

    /**
     * @param int $ebiProfileId
     */
    public function setEbiProfileId($ebiProfileId)
    {
        $this->ebiProfileId = $ebiProfileId;
    }


}