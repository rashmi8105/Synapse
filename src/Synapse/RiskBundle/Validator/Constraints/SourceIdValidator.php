<?php
namespace Synapse\RiskBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SourceIdValidator extends ConstraintValidator
{

    const PERCENT_STRING_PERCENT = '%string%';

    private $constraint;

    public function validate($value, Constraint $constraint)
    {
        $this->constraint = $constraint;
        $sourceType = $value->getSourceType();
        $sourceIds = $value->getSourceId();
        
        switch ($sourceType) {
            case 'profile':
                $this->isEmptyProfile($sourceIds);
                break;
            case 'surveyquestion':
                $this->isEmptySurveyQuestion($sourceIds);
                
                break;
            case 'surveyfactor':
                $this->isEmptySurveyFactor($sourceIds);
                break;
            
            case 'isp':
                $this->isEmptyIsp($sourceIds);
                break;
            case 'isq':
                $this->isEmptyIsq($sourceIds);
                break;
            
            case 'questionbank':
                $this->isEmptyQuestionBank($sourceIds);
                break;
            default:
                return true;
        }
    }

    public function isEmptyProfile($sourceIds)
    {
        $profile = $sourceIds->getEbiProfileId();
        if (empty(trim($profile))) {
            $this->context->buildViolation("profile should not be blank")
                ->setParameter(self::PERCENT_STRING_PERCENT, '')
                ->addViolation();
        } else {
            return;
        }
        return;
    }

    public function isEmptyIsp($sourceIds)
    {
        $campus = $sourceIds->getCampusId();
        
        if (empty(trim($campus))) {
            $this->context->buildViolation("Campus Id should not be blank")
                ->setParameter(self::PERCENT_STRING_PERCENT, '')
                ->addViolation();
        } else {
            return;
        }
    }

    public function isEmptyIsq($sourceIds)
    {
        $campus = $sourceIds->getCampusId();
        
        if (empty(trim($campus))) {
            $this->context->buildViolation("Campus Id should not be blank")
                ->setParameter(self::PERCENT_STRING_PERCENT, '')
                ->addViolation();
        } else {
            return;
        }
    }

    public function isEmptySurveyFactor($sourceIds)
    {
        $survey = $sourceIds->getSurveyId();
        $factor = $sourceIds->getFactorId();
        
        if (empty(trim($survey))) {
            $this->context->buildViolation("Survey should not be blank")
                ->setParameter(self::PERCENT_STRING_PERCENT, '')
                ->addViolation();
        } elseif (empty(trim($factor))) {
            $this->context->buildViolation("Factor should not be blank")
                ->setParameter(self::PERCENT_STRING_PERCENT, '')
                ->addViolation();
        } else {
            return;
        }
    }

    public function isEmptySurveyQuestion($sourceIds)
    {
        $survey = $sourceIds->getSurveyId();
        $question = $sourceIds->getQuestionId();
        
        if (empty(trim($survey))) {
            $this->context->buildViolation("Survey should not be blank")
                ->setParameter(self::PERCENT_STRING_PERCENT, '')
                ->addViolation();
        } elseif (empty(trim($question))) {
            $this->context->buildViolation("Survey Question should not be blank")
                ->setParameter(self::PERCENT_STRING_PERCENT, '')
                ->addViolation();
        } else {
            return;
        }
    }

    public function isEmptyQuestionBank($sourceIds)
    {
        $bank = $sourceIds->getQuestionBankId();
        if (empty(trim($bank))) {
            $this->context->buildViolation("Question Bank should not be blank")
                ->setParameter(self::PERCENT_STRING_PERCENT, '')
                ->addViolation();
        } else {
            return;
        }
    }
}