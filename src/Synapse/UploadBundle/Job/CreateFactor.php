<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\SurveyBundle\Entity\FactorQuestions;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RiskBundle\EntityDto\RiskCalculationInputDto;
use Synapse\CoreBundle\Util\Helper;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class CreateFactor extends ContainerAwareJob
{

    const Longitudinal_Id = 'LongitudinalID';

    const Factor_Id = 'FactorID';

    public $errors = [];

    public function run($args)
    {
        $factorsData = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args[UploadConstant::UPLOADID];
        $orgId = $args[UploadConstant::ORGID];
        $userId = $args[UploadConstant::USERID];

        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $ebiQuestionRepo = $repositoryResolver->getRepository('SynapseCoreBundle:EbiQuestion');
        $surveyRepo = $repositoryResolver->getRepository('SynapseCoreBundle:Survey');
        $factorRepo = $repositoryResolver->getRepository('SynapseSurveyBundle:Factor');
        $factorQuestionsRepo = $repositoryResolver->getRepository('SynapseSurveyBundle:FactorQuestions');
        $cache = $this->getContainer()->get('synapse_redis_cache');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        
        /*
         * Find all the question ID from the ebi question table
         */
        
        $ebiObjArr = $ebiQuestionRepo->findAll();
        foreach($ebiObjArr as $ebiObj){
            $ebiArr[$ebiObj->getId()] = $ebiObj;
            $ebiQuestionIdArr =  array_keys($ebiArr);
        }
        
        
        /*
         * Get all the factors from the factor table
         */
       
       $factorObjArr =  $factorRepo->findAll();
       foreach($factorObjArr as $factorObj){
           $allfactorArr[$factorObj->getId()] = $factorObj;
           $factorArr =  array_keys($allfactorArr);
           
       }

       
       /*
        * Get all the factors Questions combination
       */
       $factQuesArr =  array();
       $factorQuesObjArr =  $factorQuestionsRepo->findAll();
       foreach($factorQuesObjArr as $factorQues){
           $factorID = $factorQues->getFactor()->getId();
           if($factorQues->getEbiQuestion()){
               $ebiId = $factorQues->getEbiQuestion()->getId();
           }else{
               $ebiId = '';
           }
           $factQuesArr[] = $factorID ."-".$ebiId;
       }
       
        $validRowCount = 0;
        foreach($factorsData as $id => $factorData){
            
            $questionId = $factorData[strtolower(self::Longitudinal_Id)];
            $factorId = $factorData[strtolower(self::Factor_Id)];
            if(!in_array($questionId,$ebiQuestionIdArr)){
                $this->logErrors($id, self::Longitudinal_Id, $questionId, "$questionId is not  valid");
                continue;
            }
            if(isset($factorArr)){
                if (! in_array($factorId, $factorArr)) {
                    $this->logErrors($id,self::Factor_Id,$factorId, "$factorId is not  valid");
                    continue;
                }
            }else{
                $this->logErrors($id, self::Factor_Id,$factorId, "$factorId is not  valid");
                continue;
            }
            
            $factorQ = $factorId ."-".$questionId;
            if (in_array($factorQ, $factQuesArr)) {
                $this->logErrors($id,self::Longitudinal_Id,$questionId, "$questionId already is assigned to the factor");
                continue;
            }
            
            $factorQuestionObj =  new FactorQuestions();
            $factorQuestionObj->setEbiQuestion($ebiArr[$questionId]);
            $factorQuestionObj->setFactor($allfactorArr[$factorId]);
            $factorQuestionsRepo->persist($factorQuestionObj, false);
            $validRowCount++;
        }
        $allfactorArr =  null;
        $ebiArr =  null;
        $factorQuestionsRepo->flush();
        $factorQuestionsRepo->clear();
        $factorQuestionsRepo =  null;

        $uploadFileLogService->updateValidRowCount($uploadId, $validRowCount);
        $uploadFileLogService->updateCreatedRowCount($uploadId, $validRowCount);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($this->errors));

        /*
         * Marking the jobs finished
         */
        $jobs = $cache->fetch("organization.{$orgId}.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("organization.{$orgId}.upload.{$uploadId}.jobs", $jobs);
        $cache->save("organization:{$orgId}:upload:{$uploadId}:job:{$jobNumber}:errors", $this->errors);
        return $this->errors;
    }
    
    
    private function  logErrors ($id, $name , $value ,$message){
        
        $this->errors[$id][] = [
        'name' => $name,
        UploadConstant::VALUE =>$value,
        UploadConstant::ERRORS => [
            $message
        ]
        ];
    }
}