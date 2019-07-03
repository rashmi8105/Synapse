<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class SurveySnapshotSectionDto
{

    /**
     * $question_type
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $questionType;
	
	/**
     * $question_text
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $questionText;
	
	/**
     * $question_qnbr
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $questionQnbr;
	
	/**
     * total_students_responded
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalStudentsResponded;
    
    /**
     * total_students
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalStudents;
	
	/**
     * survey_question_id
     *
     * @var integer @JMS\Type("integer")
     */
    private $surveyQuestionId;
	
	/**
     * $responded_percentage
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $respondedPercentage;
	
	
	
	/**
     * redResponses
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $redResponses;
	
	/**
     * yellowResponses
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $yellowResponses;
	
	/**
     * greenResponses
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $greenResponses;
	
	/**
     * mean
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $mean;
	
	/**
     * question_type_code
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $questionTypeCode;
	
	/**
     * stdDeviation
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $stdDeviation;
	
	/**
     * redOptions
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $redOptions;
	
	/**
     * yellowOptions
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $yellowOptions;
	
	/**
     * greenOptions
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $greenOptions;
	
	/**
     * responseSummary
     *
     * @var array @JMS\Type("array")
     */
    private $responseSummary;
	
	/**
     * responseOptions
     *
     * @var array @JMS\Type("array")
     */
    private $responseOptions;
	
	/**
     * branchDetails
     *
     * @var array @JMS\Type("array")
     */
    private $branchDetails;
	
	/**
     * $type
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $type;
	
	/**
     *
     * @param string $questionType            
     */
    public function setQuestionType($questionType)
    {
        $this->questionType = $questionType;
    }

    /**
     *
     * @return string
     */
    public function getQuestionType()
    {
        return $this->questionType;
    }
	
	/**
     *
     * @param string $questionText            
     */
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
	
	/**
     *
     * @param string $questionQnbr            
     */
    public function setQuestionQnbr($questionQnbr)
    {
        $this->questionQnbr = $questionQnbr;
    }

    /**
     *
     * @return string
     */
    public function getQuestionQnbr()
    {
        return $this->questionQnbr;
    }
	
	/**
     *
     * @param int $totalStudentsResponded            
     */
    public function setTotalStudentsResponded($totalStudentsResponded)
    {
        $this->totalStudentsResponded = $totalStudentsResponded;
    }

    /**
     *
     * @return int
     */
    public function getTotalStudentsResponded()
    {
        return $this->totalStudentsResponded;
    }
	
	/**
     *
     * @param string $respondedPercentage            
     */
    public function setRespondedPercentage($respondedPercentage)
    {
        $this->respondedPercentage = $respondedPercentage;
    }

    /**
     *
     * @return string
     */
    public function getRespondedPercentage()
    {
        return $this->respondedPercentage;
    }
	
	
	/**
     *
     * @param string $redResponses            
     */
    public function setRedResponses($redResponses)
    {
        $this->redResponses = $redResponses;
    }

    /**
     *
     * @return string
     */
    public function getRedResponses()
    {
        return $this->redResponses;
    }
	
	/**
     *
     * @param string $yellowResponses            
     */
    public function setYellowResponses($yellowResponses)
    {
        $this->yellowResponses = $yellowResponses;
    }

    /**
     *
     * @return string
     */
    public function getYellowResponses()
    {
        return $this->yellowResponses;
    }
	
	/**
     *
     * @param string $greenResponses            
     */
    public function setGreenResponses($greenResponses)
    {
        $this->greenResponses = $greenResponses;
    }

    /**
     *
     * @return string
     */
    public function getGreenResponses()
    {
        return $this->greenResponses;
    }
	
	/**
     *
     * @param string $mean            
     */
    public function setMean($mean)
    {
        $this->mean = $mean;
    }

    /**
     *
     * @return string
     */
    public function getMean()
    {
        return $this->mean;
    }
	
	/**
     *
     * @param string $stdDeviation            
     */
    public function setStdDeviation($stdDeviation)
    {
        $this->stdDeviation = $stdDeviation;
    }

    /**
     *
     * @return string
     */
    public function getStdDeviation()
    {
        return $this->stdDeviation;
    }
	
	/**
     *
     * @param string $questionTypeCode            
     */
    public function setQuestionTypeCode($questionTypeCode)
    {
        $this->questionTypeCode = $questionTypeCode;
    }

    /**
     *
     * @return string
     */
    public function getQuestionTypeCode()
    {
        return $this->questionTypeCode;
    }	
	
	/**
     *
     * @param string $redOptions            
     */
    public function setRedOptions($redOptions)
    {
        $this->redOptions = $redOptions;
    }

    /**
     *
     * @return string
     */
    public function getRedOptions()
    {
        return $this->redOptions;
    }

	/**
     *
     * @param string $yellowOptions            
     */
    public function setYellowOptions($yellowOptions)
    {
        $this->yellowOptions = $yellowOptions;
    }

    /**
     *
     * @return string
     */
    public function getYellowOptions()
    {
        return $this->yellowOptions;
    }	
	
	/**
     *
     * @param string $greenOptions            
     */
    public function setGreenOptions($greenOptions)
    {
        $this->greenOptions = $greenOptions;
    }

    /**
     *
     * @return string
     */
    public function getGreenOptions()
    {
        return $this->greenOptions;
    }	
	
	public function setResponseSummary($responseSummary)
    {
        $this->responseSummary = $responseSummary;
    }

    public function getResponseSummary()
    {
        return $this->responseSummary;
    }
	
	public function setResponseOptions($responseOptions)
    {
        $this->responseOptions = $responseOptions;
    }

    public function getResponseOptions()
    {
        return $this->responseOptions;
    }
	
	
	public function setBranchDetails($branchDetails)
    {
        $this->branchDetails = $branchDetails;
    }

    public function getBranchDetails()
    {
        return $this->branchDetails;
    }
	
	/**
     *
     * @param int $surveyQuestionId            
     */
    public function setSurveyQuestionId($surveyQuestionId)
    {
        $this->surveyQuestionId = $surveyQuestionId;
    }

    /**
     *
     * @return int
     */
    public function getSurveyQuestionId()
    {
        return $this->surveyQuestionId;
    }
	
    /**
     *
     * @param int $totalStudents
     */
    public function setTotalStudents($totalStudents)
    {
        $this->totalStudents = $totalStudents;
    }
    
    /**
     *
     * @return int
     */
    public function getTotalStudents()
    {
        return $this->totalStudents;
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