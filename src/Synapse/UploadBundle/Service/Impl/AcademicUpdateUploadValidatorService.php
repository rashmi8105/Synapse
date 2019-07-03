<?php
namespace Synapse\UploadBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\UploadBundle\Util\Constants\UploadConstant;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdate;

/**
 * Synapse Upload Validation
 *
 * @DI\Service("academicupdate_validator_service")
 */
class AcademicUpdateUploadValidatorService extends SynapseValidatorService
{

    const SERVICE_KEY = 'academicupdate_validator_service';

    private $failureRisk;

    private $inProgressGrade;

    private $finalGrade;

    private $sentToStudent;

    public function childConstruct()
    {
        $this->failureRisk = array(
            'Low',
            'High'
        );

        $this->inProgressGrade = array(
            'A',
            'B',
            'C',
            'D',
            'F/No Pass',
            'Pass'
        );

        $this->finalGrade = array(
            'A',
            'A-',
            'B+',
            'B',
            'B-',
            'C+',
            'C',
            'C-',
            'D+',
            'D',
            'D-',
            'F/No Pass',
            'Pass',
            'Withdraw',
            'Incomplete',
            'In Progress',
            'Not for Credit'
        );

        $this->sentToStudent = array(
            'No' => 0,
            'Yes' => 1
        );
    }

    public function validateAU($field, $value)
    {
        /**
         *  White space check for Field value
         */
        if (ctype_space($value)) {
            $this->errors[] = [
                'name' => $field,
                UploadConstant::VALUE => '',
                UploadConstant::ERRORS => [
                    'Field can not contain any empty spaces.'
                ]
            ];
        }
        $value = trim($value);

        if (strlen($value) == 0 && $field != UploadConstant::SENTTOSTUDENT) {
            return null;
        }

        if (strlen($value) == 0 && $field == UploadConstant::SENTTOSTUDENT) {
            return 0;
        }

        $arrayHolder = array();
        $arrayHolder[UploadConstant::FAILURERISK] = $this->failureRisk;
        $arrayHolder[UploadConstant::IN_PROGRESS_GRADE] = $this->inProgressGrade;
        $arrayHolder[UploadConstant::FINAL_GRADE] = $this->finalGrade;
        $arrayHolder[UploadConstant::SENTTOSTUDENT] = $this->sentToStudent;

        if (is_numeric($value)) {
            return $this->validateAUNumbers($field, $value);
        } else {
            return $this->validateAULiterals($field, $arrayHolder, $value);
        }
    }

    /**
     * Validates academic update numeric fields
     *
     * @param string $field
     * @param string $value
     * @return bool|int
     */
    private function validateAUNumbers($field, $value)
    {
        switch ($field) {
            case UploadConstant::FAILURERISK:
                if ($this->checkLimit($field, $value, 0, 1)) {
                    return $this->failureRisk[$value];
                }
                return null;
                break;
            case UploadConstant::IN_PROGRESS_GRADE:
                if ($this->checkLimit($field, $value, 0, 5)) {
                    return $this->inProgressGrade[$value];
                }
                return null;
                break;
            case UploadConstant::FINAL_GRADE:
                if ($this->checkLimit($field, $value, 0, 16)) {
                    return $this->finalGrade[$value];
                }
                return null;
                break;
            case 'Absences':
                if ($value < 0) {
                    $this->errors[] = [
                        'name' => $field,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            'Value cannot be less than 0.'
                        ]
                    ];
                    return null;
                }
                if ($value > 99) {
                    $this->errors[] = [
                        'name' => $field,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            'Value cannot exceed 99.'
                        ]
                    ];
                    return null;
                }
                if(!ctype_digit($value)){
                    $this->errors[] = [
                        'name' => $field,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            'Value cannot have decimal digits.'
                        ]
                    ];
                    return null;
                }
                return $value;
                break;
            case UploadConstant::SENTTOSTUDENT:
                if ($value != 0 && $value != 1) {
                    $this->errors[] = [
                        'name' => $field,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            'Field must contain either 1 (Yes) or 0 (No).'
                        ]
                    ];
                    return 0;
                }
                return $value;
                break;
            default:
                break;
        }
        return null;
    }

    /**
     * validates the uploaded value for a field and  sets them in an error array
     *
     * @param string $field
     * @param array $arrayHolder
     * @param string $value
     * @return bool|int
     */
    private function validateAULiterals($field, $arrayHolder, $value)
    {
        switch ($field) {
            case UploadConstant::FAILURERISK:
                if (! in_array($value, $arrayHolder[$field])) {
                    $this->setErrors([
                        'name' => $field,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            'Field must contain one of the following values: 0(Low) or 1(High)'
                        ]
                    ]);
                    return null;
                }
                else{
                    return $value;
                }
                break;
            case UploadConstant::IN_PROGRESS_GRADE:
                if (! in_array($value, $arrayHolder[$field])) {
                    $this->setErrors([
                        'name' => $field,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            'Field must contain one of the following values: 0(A), 1(B), 2(C), 3(D), 4(F/No Pass), 5(Pass).'
                        ]
                    ]);
                    return null;
                }
                else{
                    return $value;
                }
                break;
            case UploadConstant::FINAL_GRADE:
                if (! in_array($value, $arrayHolder[$field])) {
                    $this->setErrors([
                        'name' => $field,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            'Field must contain one of the following values: 0(A), 1(A-), 2(B+), 3(B), 4(B-), 5(C+), 6(C), 7(C-), 8(D+), 9(D), 10(D-), 11(F/No Pass), 12(Pass), 13(Withdraw), 14(Incomplete), 15(In Progress), 16(Not for Credit).'
                        ]
                    ]);
                    return null;
                }
                else{
                    return $value;
                }
                break;
            case UploadConstant::SENTTOSTUDENT:
                if (strtolower($value) != 'no' && strtolower($value) != 'yes') {
                    $this->setErrors([
                        'name' => $field,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            'Field must contain either 1 (Yes) or 0 (No).'
                        ]
                    ]);
                    return 0;
                }
                elseif(strtolower($value) == 'no'){
                    return 0;
                }
                elseif(strtolower($value) == 'yes'){
                    return  1;
                } else {
                    return 0;
                }
                break;
            case 'Absences':
                $this->errors[] = [
                    'name' => $field,
                    UploadConstant::VALUE => '',
                    UploadConstant::ERRORS => [
                        $field .' '. UploadConstant::INVALID_VALUE
                    ]
                ];
                return null;
                break;
            default:
                break;
        }
        return null;
    }

    /**
     * Using the data coming in from a new academic update upload, determines if the data represents a duplicate of $previousAcademicUpdate.
     * Skips duplication checking if $previousAcademicUpdate was not an upload, i.e. was a UI update
     *
     * @param AcademicUpdate $previousAcademicUpdate
     * @param Organization $organization
     * @param string $failureRisk
     * @param string $inProgressGrade
     * @param string $comments
     * @param string $finalGrade
     * @param int $absences
     * @param int $sendToStudent
     * @return bool
     */
    public function isCurrentAcademicUpdateDuplicate($previousAcademicUpdate, $organization, $failureRisk, $inProgressGrade, $comments, $finalGrade, $absences, $sendToStudent)
    {

        // isUpload==1 if the academic update was uploaded
        if ($previousAcademicUpdate->getIsUpload() != 1) {
            return false;
        }
        if ($previousAcademicUpdate->getFailureRiskLevel() != $failureRisk) {
            return false;
        }
        if ($previousAcademicUpdate->getGrade() != $inProgressGrade) {
            return false;
        }
        if ($previousAcademicUpdate->getComment() != $comments) {
            return false;
        }
        if ($previousAcademicUpdate->getFinalGrade() != $finalGrade) {
            return false;
        }
        if ($previousAcademicUpdate->getAbsence() != $absences) {
            return false;
        }
        // If the organization has disabled send to student, checking its value doesn't matter in the case of duplicates. So skip this check in that case.
        if (($organization->getSendToStudent() == 1 || $organization->getSendToStudent() === null) && $previousAcademicUpdate->getSendToStudent() != $sendToStudent) {
            return false;
        }
        return true;
    }
}