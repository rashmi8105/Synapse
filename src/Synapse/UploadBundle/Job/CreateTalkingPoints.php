<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\CoreBundle\Entity\TalkingPoints;
use Synapse\CoreBundle\Entity\TalkingPointsLang;
use Synapse\CoreBundle\EntityDto\TalkingPointsDto;
use Synapse\CoreBundle\EntityDto\TalkingPointsLangDto;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class CreateTalkingPoints extends ContainerAwareJob
{

    CONST ITEM = 'item';

    CONST KIND = 'kind';

    CONST WEAKNESSTEXT = 'weaknessText';

    CONST STRENGTHTEXT = 'strengthText';

    CONST WEAKNESSLOW = 'weaknessLow';

    CONST WEAKNESSHIGH = 'weaknessHigh';

    CONST STRENGTHLOW = 'strengthLow';

    CONST STRENGTHHIGH = 'strengthHigh';

    public function run($args)
    {
        $creates = $args['creates'];
        $jobNumber = $args[UploadConstant::JOB_NUM];
        $uploadId = $args[UploadConstant::UPLOADID];
        $talkingPointsService = $this->getContainer()->get('talkingpoint_service');
        $talkingPointsLangService = $this->getContainer()->get('talkingpointlang_service');
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $cache = $this->getContainer()->get('synapse_redis_cache');
        $validator = $this->getContainer()->get('course_upload_validator_service');
        $uploadFileLogService = $this->getContainer()->get(UploadConstant::UPLOAD_FILE_LOG_SERVICE);
        $TalkingPointsRepository = $repositoryResolver->getRepository('SynapseCoreBundle:TalkingPoints');
        $TalkingPointsLangRepository = $repositoryResolver->getRepository('SynapseCoreBundle:TalkingPointsLang');
        $EbiQuestionRepository = $repositoryResolver->getRepository('SynapseCoreBundle:EbiQuestion');
        $EbiMetadataRepository = $repositoryResolver->getRepository('SynapseCoreBundle:EbiMetadata');
        $errors = [];
        $validRows = 0;
        
        $requiredItems = [
            self::ITEM,
            self::KIND,
            self::WEAKNESSTEXT,
            self::STRENGTHTEXT,
            self::WEAKNESSLOW,
            self::WEAKNESSHIGH,
            self::STRENGTHLOW,
            self::STRENGTHHIGH
        ];

        
        foreach ($creates as $id => $data) {
            $requiredMissing = false;
            foreach ($requiredItems as $item) {

                if (! array_key_exists(strtolower($item), $data) || empty($data[strtolower($item)]) && $data[strtolower($item)] != 0) {

                    $errors[$id][] = [
                        'name' => $item,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            "{$item} is a required field"
                        ]
                    ];
                    $requiredMissing = true;
                }
            }
            
            if ($requiredMissing) {
                continue;
            }
            
            $type = (strcmp(trim(strtolower($data[self::KIND])), 'profile') == 0) ? 'P' : 'S';

            $descArr = [];
            $descArr[]    = $data[strtolower(self::WEAKNESSTEXT)];
            $descArr[]    = $data[strtolower(self::STRENGTHTEXT)];
            $weaknessLow  = $data[strtolower(self::WEAKNESSLOW)];
            $weaknessHigh = $data[strtolower(self::WEAKNESSHIGH)];
            $strengthLow  = $data[strtolower(self::STRENGTHLOW)];
            $strengthHigh = $data[strtolower(self::STRENGTHHIGH)];
            
            if ($type == 'P') {
                
                $ebiMetadata = "";
                $ebiMetadata = $EbiMetadataRepository->findOneBy(array(
                    'key' => $data[self::ITEM]
                ));
                if (is_object($ebiMetadata)) {
                    
                    $existingTalkingPoints = "";
                    $existingTalkingPoints = $TalkingPointsRepository->findBy(array(
                        'ebiMetadata' => $ebiMetadata
                    ));
                    
                    $talkingPointsIDs = $talkingPointsService->saveTalkingPoints($ebiMetadata, $type, $strengthLow, $strengthHigh, $weaknessLow, $weaknessHigh, $existingTalkingPoints);
                    $talkingPointsLangService->saveTalkingPoints($talkingPointsIDs, $descArr);
                    $validRows ++;
                } else {
                    $errors[$id][] = [
                        'name' => 'ProfileItem',
                        UploadConstant::VALUE => "",
                        UploadConstant::ERRORS => [
                            "Item name \"{$data[strtolower(self::ITEM)]}\" not found \n"
                        ]
                    ];
                    $requiredMissing = true;
                }
            } else {
                $errors[$id][] = [
                    'name' => self::KIND,
                    UploadConstant::VALUE => "",
                    UploadConstant::ERRORS => [
                        "Invalid Kind! \n"
                    ]
                ];
                $requiredMissing = true;
            }
        }

        $uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        $uploadFileLogService->updateCreatedRowCount($uploadId, $validRows);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($errors));
        $TalkingPointsLangRepository->flush();
        $TalkingPointsLangRepository->clear();
        $jobs = $cache->fetch("organization.talkingpoints.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("organization.talkingpoints.upload.{$uploadId}.jobs", $jobs);
        $cache->save("organization:talkingpoints:upload:{$uploadId}:job:{$jobNumber}:errors", $errors);
        return $errors;
    }

    protected function isObjectExist($object, $message, $key)
    {
        if (! isset($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }
}