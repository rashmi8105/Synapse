<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class WessOrgRespDto
{

    /**
     * organizationId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     * personId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $personId;

    /**
     * surveyId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $surveyId;

    /**
     * academicYearId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $academicYearId;

    /**
     * academicTermId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $academicTermId;

    /**
     * surveyQuestionId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $orgQuestionId;

    /**
     * responseType
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $responseType;

    /**
     * responseValue
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $responseValue;

    /**
     *
     * @param integer $organizationId            
     */
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

    /**
     *
     * @param integer $surveyId            
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    /**
     *
     * @return integer
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     *
     * @param integer $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return integer
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     *
     * @param integer $academicYearId            
     */
    public function setAcademicYearId($academicYearId)
    {
        $this->academicYearId = $academicYearId;
    }

    /**
     *
     * @return integer
     */
    public function getAcademicTermId()
    {
        return $this->academicTermId;
    }

    /**
     *
     * @param integer $academicTermId            
     */
    public function setAcademicTermId($academicTermId)
    {
        $this->academicTermId = $academicTermId;
    }

    /**
     *
     * @return integer
     */
    public function getAcademicYearId()
    {
        return $this->academicYearId;
    }

    /**
     *
     * @param integer $orgQuestionId            
     */
    public function setOrgQuestionId($orgQuestionId)
    {
        $this->orgQuestionId = $orgQuestionId;
    }

    /**
     *
     * @return integer
     */
    public function getOrgQuestionId()
    {
        return $this->orgQuestionId;
    }

    /**
     *
     * @param string $responseType            
     */
    public function setResponseType($responseType)
    {
        $this->responseType = $responseType;
    }

    /**
     *
     * @return string
     */
    public function getResponseType()
    {
        return $this->responseType;
    }

    /**
     *
     * @param mixed $responseType            
     */
    public function setResponseValue($responseValue)
    {
        $this->responseValue = $responseValue;
    }

    /**
     *
     * @return mixed
     */
    public function getResponseValue()
    {
        return $this->responseValue;
    }
}
