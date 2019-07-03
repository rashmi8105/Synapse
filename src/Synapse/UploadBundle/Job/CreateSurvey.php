<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\CoreBundle\Entity\DatablockQuestions;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SurveyBundle\Util\Constants\SurveyConstant;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class CreateSurvey extends ContainerAwareJob
{

    public function run($args)
    {
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $orgId = 1;
        $type = strtolower($args['type']);
        
        $errors = [];
        $validRows = 0;
        
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $datablockQuestionsRepository = $repositoryResolver->getRepository('SynapseCoreBundle:DatablockQuestions');

        $cache = $this->getContainer()->get('synapse_redis_cache');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $surveyUploadService = $this->getContainer()->get('survey_upload_service');
        $synapseValidatorObj = $this->getContainer()->get('synapse_validator_service');

        $requiredItems = [
            SurveyConstant::SURVEYBLOCKID
        ];

        foreach ($creates as $id => $data) {
            
            $requiredMissing = false;
            
            foreach ($requiredItems as $item) {
                
                if (! array_key_exists(strtolower($item), $data) || empty($data[strtolower($item)]) && $data[strtolower($item)] != 0) {
                    $errors[$id][] = [
                        'name' => $item,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            "This is a required field"
                        ]
                    ];
                    $requiredMissing = true;
                }
            }
            
            $ebiQuestionId = $data[strtolower(SurveyConstant::LONGITUDINALID)];
            $surveyId = $data[strtolower(SurveyConstant::SURVEYID)];
            $factorId = $data[strtolower(SurveyConstant::FACTORID)];
            
            if (strlen(trim($ebiQuestionId)) == 0 && strlen(trim($surveyId)) == 0 && strlen(trim($factorId)) == 0) {
                $errors[$id][] = [
                    'name' => 'Error',
                    UploadConstant::VALUE => '',
                    UploadConstant::ERRORS => [
                        "Source not defined. "
                    ]
                ];
                $requiredMissing = true;
            }
            
            if ($requiredMissing) {
                continue;
            }
            
            if (strlen(trim($ebiQuestionId)) > 0) {
                $ebiQuestionObj = $synapseValidatorObj->validateContents(SurveyConstant::LONGITUDINALID, $ebiQuestionId, array(
                    UploadConstant::CLASS_CONST => 'SynapseCoreBundle:EbiQuestion',
                    'keys' => array(
                        'id' => $ebiQuestionId
                    )
                ));
            } else {
                $surveyObj = $synapseValidatorObj->validateContents(SurveyConstant::SURVEYID, $surveyId, array(
                    UploadConstant::CLASS_CONST => 'SynapseCoreBundle:Survey',
                    'keys' => array(
                        'id' => $surveyId
                    )
                ));
                $factorObj = $synapseValidatorObj->validateContents(SurveyConstant::FACTORID, $factorId, array(
                    UploadConstant::CLASS_CONST => 'SynapseSurveyBundle:Factor',
                    'keys' => array(
                        'id' => $factorId
                    )
                ));
            }
            
            $surveyBlockId = $data[strtolower(SurveyConstant::SURVEYBLOCKID)];
            $surveyBlockObj = $synapseValidatorObj->validateContents(SurveyConstant::SURVEYBLOCKID, $surveyBlockId, array(
                UploadConstant::CLASS_CONST => 'SynapseCoreBundle:DatablockMaster',
                'keys' => array(
                    'id' => $surveyBlockId
                )
            ));
            
            $errorsTrack = $synapseValidatorObj->getErrors();
            if (sizeof($errorsTrack) > 0) {
                $errors[$id] = $errorsTrack;
            } else {
                $synapseValidatorObj->validateSU($data);
                $errorsTrack = $synapseValidatorObj->getErrors();
                $errors[$id] = (isset($errorsTrack) && is_array($errorsTrack)) ? $errorsTrack : [];
                $QuestionEntity = $surveyUploadService->saveSurveyBlock($type, $data);
                if ($QuestionEntity->getId()) {
                    $errors[$id][] = [
                        'name' => SurveyConstant::SURVEYBLOCKID,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            "{$data[strtolower(SurveyConstant::SURVEYBLOCKID)]} - Duplicate entry not allowed"
                        ]
                    ];
                } else {
                    $datablockQuestionsRepository->persist($QuestionEntity, false);
                    $validRows ++;
                }
                
            }
        }
        $uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        $uploadFileLogService->updateCreatedRowCount($uploadId, $validRows);
        $uploadFileLogService->updateCreatedRowCount($uploadId, count($errors));

        $datablockQuestionsRepository->flush();
        $datablockQuestionsRepository->clear();
        $jobs = $cache->fetch("organization.{$orgId}.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("organization.{$orgId}.upload.{$uploadId}.jobs", $jobs);
        $cache->save("organization:{$orgId}:upload:{$uploadId}:job:{$jobNumber}:errors", $errors);
        return $errors;
    }
}