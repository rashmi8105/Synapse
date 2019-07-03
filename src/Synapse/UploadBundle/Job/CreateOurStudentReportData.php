<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\UploadBundle\Util\Constants\UploadConstant;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\ReportsBundle\Entity\ReportSections;
use Synapse\ReportsBundle\Entity\ReportSectionElements;
use Synapse\ReportsBundle\Entity\ReportElementBuckets;
use Synapse\ReportsBundle\Entity\ReportBucketRange;

class CreateOurStudentReportData extends ContainerAwareJob
{

    const LOGGER = 'logger';

    const VALIDATOR_SERVICE = 'groupupload_validator_service';

    public $errors = [];

    private $groupRepo;

    private $validRows;

    private $reportSectionRepo;

    private $reportSectionElementRepo;

    private $reportBucketElementRepo;
    
    private $reportBucketRangeRepo;

    private $numeratorlowBucket = "NumeratorLow";

    private $numeratorhighBucket = "NumeratorHigh";

    private $denominatorlowBucket = "DenominatorLow";

    private $denominatorhighBucket = "DenominatorHigh";

    private $numeratorchoicesBucket = "NumeratorChoices";

    private $denominatorchoicesBucket = "DenominatorChoices";

    public function run($args)
    {
        $logger = $this->getContainer()->get(self::LOGGER);
        $logger->info("**************************** Starting CreateOurStudentReportData1");
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $cache = $this->getContainer()->get('synapse_redis_cache');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $this->groupRepo = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroup');
        
        /* $this->validateData($args);
        if (count($this->errors) == 0) {
            $this->create($args);
            $this->validRows = count($creates);
        } else {
            $this->validRows = 0;
        } */
        $this->create($args);
        $uploadFileLogService->updateValidRowCount($uploadId, $this->validRows);
        $uploadFileLogService->updateCreatedRowCount($uploadId, $this->validRows);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($this->errors)); // counts the number of ids that have an error logged

        $this->groupRepo->flush();
        
        $jobs = $cache->fetch("ourstudreport.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("ourstudreport.upload.{$uploadId}.jobs", $jobs);
        
        $cache->save("ourstudreport.upload.{$uploadId}.job.{$jobNumber}.errors", $this->errors);
        
        return $this->errors;
    }

    public function continousDataValidation($data)
    {
        $numeratorHighVal = 0;
        $numeratorLowVal = 0;
        
        $deniminatorHighVal = 0;
        $deniminatorLowVal = 0;
        
        if (isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORHIGH)]) && $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORHIGH)] >= 1 && $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORHIGH)] <= 7) {
            $numeratorHighVal = $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORHIGH)];
        } else {
            /**
             * Exception
             */
            throw new ValidationException([
                UploadConstant::OUR_STUD_REPORT_COL_NUMERATORHIGH . ": must be a real number between 1.0 and 7.0"
            ], UploadConstant::OUR_STUD_REPORT_COL_NUMERATORHIGH . ": must be a real number between 1.0 and 7.0", "not_found");
        }
        
        if (isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORLOW)]) && $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORLOW)] >= 1 && $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORLOW)] <= 7) {
            $numeratorLowVal = $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORLOW)];
        } else {
            /**
             * Exception
             */
            throw new ValidationException([
                UploadConstant::OUR_STUD_REPORT_COL_NUMERATORLOW . ": must be a real number between 1.0 and 7.0"
            ], UploadConstant::OUR_STUD_REPORT_COL_NUMERATORLOW . ": must be a real number between 1.0 and 7.0", "not_found");
        }
        
        if (isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORHIGH)]) && $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORHIGH)] >= 1 && $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORHIGH)] <= 7) {
            $deniminatorHighVal = $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORHIGH)];
        } else {
            /**
             * Exception
             */
            throw new ValidationException([
                UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORHIGH . ": must be a real number between 1.0 and 7.0"
            ], UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORHIGH . ": must be a real number between 1.0 and 7.0", "not_found");
        }
        
        if (isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORLOW)]) && $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORLOW)] >= 1 && $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORLOW)] <= 7) {
            $deniminatorLowVal = $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORLOW)];
        } else {
            /**
             * Exception
             */
            throw new ValidationException([
                UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORLOW . ": must be a real number between 1.0 and 7.0"
            ], UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORLOW . ": must be a real number between 1.0 and 7.0", "not_found");
        }
        
        if ($numeratorLowVal > $numeratorHighVal) {
            /**
             * Exception
             */
            throw new ValidationException([
                UploadConstant::OUR_STUD_REPORT_COL_NUMERATORHIGH . "must be >=" . UploadConstant::OUR_STUD_REPORT_COL_NUMERATORLOW
            ], UploadConstant::OUR_STUD_REPORT_COL_NUMERATORHIGH . "must be >=" . UploadConstant::OUR_STUD_REPORT_COL_NUMERATORLOW, "not_found");
        }
        
        if ($deniminatorLowVal > $deniminatorHighVal) {
            /**
             * Exception
             */
            throw new ValidationException([
                UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORHIGH . "must be >=" . UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORLOW
            ], UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORHIGH . "must be >=" . UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORLOW, "not_found");
        }
    }

    private function removeExisting($reportEntity)
    {
        $reportsSectionsRemove = $this->reportSectionRepo->findBy([
            'reports' => $reportEntity
        ]);
        if ($reportsSectionsRemove) {
            foreach ($reportsSectionsRemove as $reportsSectionRemove) {
                /**
                 * find Section Elements
                 */
                $reportElementsRemove = $this->reportSectionElementRepo->findBySectionId($reportsSectionRemove);
                if ($reportElementsRemove) {
                    foreach ($reportElementsRemove as $reportElementRemove) {
                        $reportElementBucketsRemove = $this->reportBucketElementRepo->findByElementId($reportElementRemove);
                        if ($reportElementBucketsRemove) {
                            foreach ($reportElementBucketsRemove as $reportElementBucketRemove) {
                                /**
                                 * Range Remove
                                 */
                                $rangeBucketsRemove = $this->reportBucketRangeRepo->findByElementId($reportElementRemove);
                                if($rangeBucketsRemove)
                                {
                                    foreach ($rangeBucketsRemove as $rangeBucketRemove)
                                    {
                                        $this->reportBucketRangeRepo->delete($rangeBucketRemove,false);
                                    }
                                }
                                
                                $this->reportBucketElementRepo->delete($reportElementBucketRemove, false);
                            }
                        }
                        $this->reportSectionElementRepo->delete($reportElementRemove, false);
                    }
                }
                $this->reportSectionRepo->delete($reportsSectionRemove, false);
            }
        }
    }

    public function validateData($args)
    {
        $logger = $this->getContainer()->get(self::LOGGER);
        $logger->info("**************************** Starting CreateOurStudentReportData1");
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $cache = $this->getContainer()->get('synapse_redis_cache');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $validatorObj = $this->getContainer()->get(self::VALIDATOR_SERVICE);
        
        $reportsRepo = $repositoryResolver->getRepository("SynapseReportsBundle:Reports");
        $this->reportSectionRepo = $repositoryResolver->getRepository("SynapseReportsBundle:ReportSections");
        $this->reportSectionElementRepo = $repositoryResolver->getRepository("SynapseReportsBundle:ReportSectionElements");
        
        $factorRepo = $repositoryResolver->getRepository("SynapseSurveyBundle:Factor");
        $surveyRepo = $repositoryResolver->getRepository("SynapseCoreBundle:Survey");
        $ebiQuestionRepo = $repositoryResolver->getRepository("SynapseCoreBundle:EbiQuestion");
        // $logger = $this->getContainer()->get('logger');
        
        $this->groupRepo = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroup');
        
        $insertFlag = true;
        $reportEntity = $reportsRepo->findOneBy([
            'shortCode' => 'OSR'
        ]);
        // $reportEntity = $reportEntity[0];
        
        $logger->info("**************************** Starting CreateOurStudentReportData2");
        foreach ($creates as $id => $data) {
            $sourceType = '';
            if (! $reportEntity) {
                throw new ValidationException([
                    "Our Student Report Not Found"
                ], "Our Student Report Not Found", "not_found");
            }
            $logger->info("**************************** 1");
            print_r($data);
            $data = array_change_key_case($data, CASE_LOWER);
            
            $requiredMissing = false;
            $ebiQuestionEntity = null;
            $surveyEntity = null;
            $factorEntity = null;
            $logger->info("**************************** 2");
            try {
                $isChoise = false;
                if (isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_LONGITUDINALID)]) && ! empty($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_LONGITUDINALID)])) {
                    /**
                     * Question
                     */
                    $isChoise = true;
                    $sourceType = "Q";
                    $ebiQuestionEntity = $ebiQuestionRepo->find($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_LONGITUDINALID)]);
                    if (! $ebiQuestionEntity) {
                        throw new ValidationException([
                            UploadConstant::OUR_STUD_REPORT_COL_LONGITUDINALID . " Not Found"
                        ], UploadConstant::OUR_STUD_REPORT_COL_LONGITUDINALID . " Not Found", "not_found");
                    }
                } elseif (isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_SURVID)]) && ! empty($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_SURVID)]) && isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_FACTORID)]) && ! empty($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_FACTORID)])) {
                    /**
                     * Survey + Factor
                     */
                    $logger->info("****************************  2 SURVEY FACTOR");
                    $isChoise = false;
                    $sourceType = "F";
                    $surveyEntity = $surveyRepo->find($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_SURVID)]);
                    if (! $surveyEntity) {
                        throw new ValidationException([
                            UploadConstant::OUR_STUD_REPORT_COL_SURVID . " Not Found"
                        ], UploadConstant::OUR_STUD_REPORT_COL_SURVID . " Not Found", "not_found");
                    }
                    
                    $factorEntity = $factorRepo->find($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_FACTORID)]);
                    if (! $factorEntity) {
                        throw new ValidationException([
                            UploadConstant::OUR_STUD_REPORT_COL_FACTORID . "Not Found"
                        ], UploadConstant::OUR_STUD_REPORT_COL_FACTORID . "Not Found", "not_found");
                    }
                } else 

                {
                    
                    /**
                     * Required Missing Error
                     */
                    throw new ValidationException([
                        "Survey Id, Factor Id Required"
                    ], "Survey Id, Factor Id Required", "not_found");
                }
                $logger->info("**************************** 3");
                if (isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONID)]) && ! empty($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONID)])) {
                    $reportSectionId = $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONID)];
                } else {
                    /**
                     * Exception
                     */
                    throw new ValidationException([
                        UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONID . " Required"
                    ], UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONID . " Required", "not_found");
                }
                
                if (isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONNAME)]) && ! empty($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONNAME)])) {
                    $reportSectionName = $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONNAME)];
                } else {
                    /**
                     * Exception
                     */
                    throw new ValidationException([
                        UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONNAME . " Required"
                    ], UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONNAME . " Required", "not_found");
                }
                
                if (isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DISPLAYLABEL)]) && ! empty($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DISPLAYLABEL)])) {
                    $reportSectionName = $data[srtolower(UploadConstant::OUR_STUD_REPORT_COL_DISPLAYLABEL)];
                } else {
                    /**
                     * Exception
                     */
                    throw new ValidationException([
                        UploadConstant::OUR_STUD_REPORT_COL_DISPLAYLABEL . " Required"
                    ], UploadConstant::OUR_STUD_REPORT_COL_DISPLAYLABEL . " Required", "not_found");
                }
                
                $logger->info("**************************** 4");
                
                if ($isChoise === false) {
                    $this->continousDataValidation($data);
                } else {
                    $this->categoryDataValidation($data);
                }
            } catch (ValidationException $valexp) {
                $logger->debug("**************************** Validation Exception $id :" . $valexp->getMessage());
                $this->errors[$id][] = [
                    'name' => '',
                    'value' => '',
                    'errors' => [
                        $valexp->getMessage()
                    ]
                ];
            } catch (\Exception $exp) {
                $logger->debug("****************************  Exception $id : " . $exp->getMessage());
                
                $this->errors[$id][] = [
                    'name' => '',
                    'value' => '',
                    'errors' => [
                        $exp->getMessage()
                    ]
                ];
            }
        }
    }

    public function create($args)
    {
        $logger = $this->getContainer()->get(self::LOGGER);
        $logger->info("**************************** Starting CreateOurStudentReportData1");
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        
        // $this->errors = [];
        $this->validRows = 0;
        
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $cache = $this->getContainer()->get('synapse_redis_cache');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $validatorObj = $this->getContainer()->get(self::VALIDATOR_SERVICE);
        
        $reportsRepo = $repositoryResolver->getRepository("SynapseReportsBundle:Reports");
        $this->reportSectionRepo = $repositoryResolver->getRepository("SynapseReportsBundle:ReportSections");
        $this->reportSectionElementRepo = $repositoryResolver->getRepository("SynapseReportsBundle:ReportSectionElements");
        $this->reportBucketElementRepo = $repositoryResolver->getRepository("SynapseReportsBundle:ReportElementBuckets");
        $this->reportBucketRangeRepo = $repositoryResolver->getRepository("SynapseReportsBundle:ReportBucketRange");
        
        $factorRepo = $repositoryResolver->getRepository("SynapseSurveyBundle:Factor");
        $surveyRepo = $repositoryResolver->getRepository("SynapseCoreBundle:Survey");
        $ebiQuestionRepo = $repositoryResolver->getRepository("SynapseCoreBundle:EbiQuestion");
        // $logger = $this->getContainer()->get('logger');
        
        $this->groupRepo = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroup');
        
        $insertFlag = true;
        $reportEntity = $reportsRepo->findOneBy([
            'shortCode' => 'OSR'
        ]);
        // $reportEntity = $reportEntity[0];
        if ($reportEntity) {
            $this->removeExisting($reportEntity);
        }
        $this->reportSectionElementRepo->flush();
        $logger->info("**************************** Starting CreateOurStudentReportData2");
        foreach ($creates as $id => $data) {
            $sourceType = '';
            if (! $reportEntity) {
                throw new ValidationException([
                    "Our Student Report Not Found"
                ], "Our Student Report Not Found", "not_found");
            }
            $logger->info("**************************** 1");
            print_r($data);
            // $data = array_change_key_case($data, CASE_LOWER);
            
            $requiredMissing = false;
            $ebiQuestionEntity = null;
            $surveyEntity = null;
            $factorEntity = null;
            $logger->info("**************************** 2");
            try {
                $isChoise = false;
                if (isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_LONGITUDINALID)]) && ! empty($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_LONGITUDINALID)])) {
                    /**
                     * Question
                     */
                    $isChoise = true;
                    $sourceType = "Q";
                    $ebiQuestionEntity = $ebiQuestionRepo->find($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_LONGITUDINALID)]);
                    if (! $ebiQuestionEntity) {
                        throw new ValidationException([
                            UploadConstant::OUR_STUD_REPORT_COL_LONGITUDINALID . " Not Found"
                        ], UploadConstant::OUR_STUD_REPORT_COL_LONGITUDINALID . " Not Found", "not_found");
                    }
                } elseif (isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_SURVID)]) && ! empty($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_SURVID)]) && isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_FACTORID)]) && ! empty($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_FACTORID)])) {
                    /**
                     * Survey + Factor
                     */
                    $logger->info("****************************  2 SURVEY FACTOR");
                    $isChoise = false;
                    $sourceType = "F";
                    $surveyEntity = $surveyRepo->find($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_SURVID)]);
                    if (! $surveyEntity) {
                        throw new ValidationException([
                            UploadConstant::OUR_STUD_REPORT_COL_SURVID . " Not Found"
                        ], UploadConstant::OUR_STUD_REPORT_COL_SURVID . " Not Found", "not_found");
                    }
                    
                    $factorEntity = $factorRepo->find($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_FACTORID)]);
                    if (! $factorEntity) {
                        throw new ValidationException([
                            UploadConstant::OUR_STUD_REPORT_COL_FACTORID . "Not Found"
                        ], UploadConstant::OUR_STUD_REPORT_COL_FACTORID . "Not Found", "not_found");
                    }
                } else 

                {
                    
                    /**
                     * Required Missing Error
                     */
                    throw new ValidationException([
                        "Survey Id, Factor Id Required"
                    ], "Survey Id, Factor Id Required", "not_found");
                }
                $logger->info("**************************** 3");
                if (isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONID)]) && ! empty($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONID)])) {
                    $reportSectionId = $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONID)];
                } else {
                    /**
                     * Exception
                     */
                    throw new ValidationException([
                        UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONID . " Required"
                    ], UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONID . " Required", "not_found");
                }
                
                if (isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONNAME)]) && ! empty($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONNAME)])) {
                    $reportSectionName = $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONNAME)];
                } else {
                    /**
                     * Exception
                     */
                    throw new ValidationException([
                        UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONNAME . " Required"
                    ], UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONNAME . " Required", "not_found");
                }
                
                if (isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DISPLAYLABEL)]) && ! empty($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DISPLAYLABEL)])) {
                    $reportSectionName = $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DISPLAYLABEL)];
                } else {
                    /**
                     * Exception
                     */
                    throw new ValidationException([
                        UploadConstant::OUR_STUD_REPORT_COL_DISPLAYLABEL . " Required"
                    ], UploadConstant::OUR_STUD_REPORT_COL_DISPLAYLABEL . " Required", "not_found");
                }
                
                /**
                 * Report Section
                 */
                $reportSectionEntity = $this->reportSectionRepo->findOneBy([
                    'reports' => $reportEntity,
                    'title' => $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONNAME)]
                ]);
                if (! $reportSectionEntity) {
                    $reportSectionEntity = new ReportSections();
                    $reportSectionEntity->setReports($reportEntity);
                    
                    $reportSectionEntity->setTitle($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONNAME)]);
                    $reportSectionEntity->setSequence($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_REPORTSECTIONID)]);
                    // $reportSectionEntity->setTitle('');
                    $this->reportSectionRepo->persist($reportSectionEntity, true);
                }
                $logger->info("**************************** 4");
                
                if ($isChoise === false) {
                    $this->continousDataValidation($data);
                } else {
                    $this->categoryDataValidation($data);
                }
                /**
                 * Report Section Elements
                 */
                $reportSectionElementEntity = new ReportSectionElements();
                $reportSectionElementEntity->setSectionId($reportSectionEntity);
                $reportSectionElementEntity->setTitle($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DISPLAYLABEL)]);
                $reportSectionElementEntity->setDescription($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DISPLAYLABEL)]);
                $reportSectionElementEntity->setSourceType($sourceType);
                if ($sourceType == 'F') {
                    $reportSectionElementEntity->setFactorId($factorEntity);
                    $reportSectionElementEntity->setSurvey($surveyEntity);
                } else {
                    $reportSectionElementEntity->setEbiQuestionId($ebiQuestionEntity);
                }
                $this->reportSectionElementRepo->persist($reportSectionElementEntity, false);
                
                /**
                 */
                if ($isChoise === false) {
                    $this->continousDataValidation($data);
                    
                    for ($bucketNo = 0; $bucketNo < 2; $bucketNo ++) {
                        $reportBucketElementEntity = new ReportElementBuckets();
                        $reportBucketElementEntity->setElementId($reportSectionElementEntity);
                        if ($bucketNo == 0) {
                            $reportBucketElementEntity->setBucketName("Numerator");
                            $reportBucketElementEntity->setBucketText("Numerator");
                            $reportBucketElementEntity->setRangeMin($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORLOW)]);
                            $reportBucketElementEntity->setRangeMax($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORHIGH)]);
                            // $reportBucketElementEntity->setIsChoices(NULL);
                            $logger->debug("**************************** NUMERATOR Exception $id :" . $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORLOW)] . $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORHIGH)]);
                        } else {
                            $reportBucketElementEntity->setBucketName("Denominator");
                            $reportBucketElementEntity->setBucketText("Denominator");
                            $reportBucketElementEntity->setRangeMin($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORLOW)]);
                            $reportBucketElementEntity->setRangeMax($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORHIGH)]);
                            // $reportBucketElementEntity->setIsChoices(NULL);
                        }
                        $this->reportSectionElementRepo->persist($reportBucketElementEntity, true);
                    }
                } else {
                    /**
                     * Category Validation
                     */
                    
                    for ($bucketNo = 0; $bucketNo < 2; $bucketNo ++) {
                        $reportBucketElementEntity = new ReportElementBuckets();
                        $reportBucketElementEntity->setElementId($reportSectionElementEntity);
                        if ($bucketNo == 0) {
                            $reportBucketElementEntity->setBucketName("Numerator");
                            $reportBucketElementEntity->setBucketText("Numerator");
                            
                            $reportBucketElementEntity->setIsChoices(1);
                            $catValues = explode(",", $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORCHOICES)]);
                            $this->reportSectionElementRepo->persist($reportBucketElementEntity, false);
                            foreach ($catValues as $catValue) {
                                $reportBucketRange = new ReportBucketRange();
                                $reportBucketRange->setElementId($reportBucketElementEntity);
                                $reportBucketRange->setValue($catValue);
                                $this->reportSectionElementRepo->persist($reportBucketRange, false);
                            }
                        } else {
                            $reportBucketElementEntity->setBucketName("Denominator");
                            $reportBucketElementEntity->setBucketText("Denominator");
                            $reportBucketElementEntity->setIsChoices(1);
                            $this->reportSectionElementRepo->persist($reportBucketElementEntity, false);
                            $catValues = explode(",", $data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORCHOICES)]);
                            foreach ($catValues as $catValue) {
                                $reportBucketRange = new ReportBucketRange();
                                $reportBucketRange->setElementId($reportBucketElementEntity);
                                $reportBucketRange->setValue($catValue);
                                $this->reportSectionElementRepo->persist($reportBucketRange, false);
                            }
                        }
                    }
                   
                }
                $this->validRows ++;
            } catch (ValidationException $valexp) {
                $logger->debug("**************************** Validation Exception $id :" . $valexp->getMessage());
                $insertFlag = false;
                $this->errors[$id][] = [
                    'name' => '',
                    'value' => '',
                    'errors' => [
                        $valexp->getMessage()
                    ]
                ];
            } catch (\Exception $exp) {
                $logger->debug("****************************  Exception $id : " . $exp->getMessage());
                $insertFlag = false;
                $this->errors[$id][] = [
                    'name' => '',
                    'value' => '',
                    'errors' => [
                        $exp->getMessage()
                    ]
                ];
            }
        }
    }

    private function categoryDataValidation($data)
    {
        if (! isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORCHOICES)]) || empty($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_NUMERATORCHOICES)])) {
            throw new ValidationException([
                UploadConstant::OUR_STUD_REPORT_COL_NUMERATORCHOICES . ": required"
            ], UploadConstant::OUR_STUD_REPORT_COL_NUMERATORCHOICES . ": required", "not_found");
        }
        
        if (! isset($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORCHOICES)]) || empty($data[strtolower(UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORCHOICES)])) {
            throw new ValidationException([
                UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORCHOICES . ": required"
            ], UploadConstant::OUR_STUD_REPORT_COL_DENOMINATORCHOICES . ": required", "not_found");
        }
    }
}