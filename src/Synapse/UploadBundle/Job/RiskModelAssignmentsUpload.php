<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RiskBundle\Entity\OrgRiskGroupModel;

class RiskModelAssignmentsUpload extends ContainerAwareJob
{

    const UNIQUE_SECTIONID = 'UniqueCourseSectionId';

    const RISKGROUPID = 'riskgroupid';

    const COMMAND = 'commands';

    const COMMANDCLEAR = '#clear';

    const COMMANDCLEARMODEL = '#model';

    const MODELID = 'modelid';

    const VALUE = 'value';

    const ERRORS = 'errors';

    const ORGID = 'orgid';

    public function run($args)
    {
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $logger = $this->getContainer()->get('logger');

        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelAssingmentUpload--");

        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $cache = $this->getContainer()->get('synapse_redis_cache');
        $organizationRepository = $repositoryResolver->getRepository('SynapseCoreBundle:Organization');
        $riskGroupRepository = $repositoryResolver->getRepository('SynapseRiskBundle:RiskGroup');
        $riskModelRepository = $repositoryResolver->getRepository('SynapseRiskBundle:RiskModelMaster');
        $orgRiskGroupModelRepository = $repositoryResolver->getRepository('SynapseRiskBundle:OrgRiskGroupModel');

        $validatorObj = $this->getContainer()->get('risk_model_assignment_validator_service');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');

        $validator = $this->getContainer()->get('validator');

        $errors = [];

        $validRows = 0;

        foreach ($creates as $id => $data) {
            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> checking empty of values");
            $validatorObj->isValueEmpty($data[self::ORGID], self::ORGID);
            $validatorObj->isValueEmpty($data[self::RISKGROUPID], self::RISKGROUPID);
            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> empty check done");
            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> validating data");
            $validatorObj->validateModelData($data);
            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> validated");

            $errorsTrack = $validatorObj->getErrors();
            $errCount = count($errorsTrack);
            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> no. of error found - " . $errCount);
            if (sizeof($errorsTrack) > 0) {
                $errors[$id] = $errorsTrack;
            } else {
                try {
                    $organization = $organizationRepository->findOneById($data[self::ORGID]);
                    $riskGroup = $riskGroupRepository->findOneById($data[self::RISKGROUPID]);
                    $riskModel = null;
                    if (isset($data[self::MODELID])) {
                        $riskModel = $riskModelRepository->findOneById($data[self::MODELID]);
                    }

                    $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelAssingmentUpload--Validated");
                    /**
                     * #clear
                     */
                    if ($data[self::COMMAND] == self::COMMANDCLEAR || $data[self::COMMAND] == self::COMMANDCLEARMODEL) {
                        if ($data[self::MODELID]) {
                            $clearRow = $orgRiskGroupModelRepository->findOneBy([
                                'org' => $organization,
                                'riskGroup' => $riskGroup,
                                'riskModel' => $riskModel
                            ]);
                            if ($clearRow) {
                                $validatorObj->validateModelDate($riskModel);
                                if ($data[self::COMMAND] == self::COMMANDCLEAR) {
                                    $orgRiskGroupModelRepository->delete($clearRow);
                                } else {
                                    $clearRow->setRiskModel(NULL);
                                    $orgRiskGroupModelRepository->update($clearRow);
                                }
                            } else {
                                throw new ValidationException([
                                    'Record does not match existing values'
                                ], 'Record does not match existing valuesr', 'ERRRM010');
                            }
                        } else {
                            throw new ValidationException([
                                'Model Id Required To clear'
                            ], 'Model Id Required To clear', 'ERRRM010');
                        }
                    } else {
                        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> IS UPDATE/INSERT");
                        $orgRiskGroupModel = $orgRiskGroupModelRepository->findOneBy([
                            'org' => $organization,
                            'riskGroup' => $riskGroup,
                            'riskModel' => null
                        ]);
                        if (!empty($orgRiskGroupModel)) {
                            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> UPDATE");
                            $orgRiskGroupModel->setRiskModel($riskModel);
                            $entityErrors = $validator->validate($orgRiskGroupModel);
                            $this->validateEntity($entityErrors);
                            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelAssingmentUpload--UPDATED");
                        } else {
                            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> INSERT");
                            $riskGroupModel = new OrgRiskGroupModel();
                            $riskGroupModel->setOrg($organization);
                            $riskGroupModel->setRiskGroup($riskGroup);
                            $riskGroupModel->setRiskModel($riskModel);
                            $entityErrors = $validator->validate($riskGroupModel);
                            $this->validateEntity($entityErrors);
                            $orgRiskGroupModelRepository->createAssignment($riskGroupModel);
                            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelAssingmentUpload--Persisted");
                        }

                        // Start job for adding student to org calc flag risk table

                        if (!empty($riskModel)) {
                            $resque = $this->getContainer()->get('bcc_resque.resque');
                            $riskCalcArray = [];
                            $riskCalcArray['modelid'] = $data[self::MODELID];
                            $riskCalcArray['groupid'] = $data[self::RISKGROUPID];
                            $riskCalcArray['orgid'] = $organization->getId();

                            $createObject = 'Synapse\UploadBundle\Job\AddOrgCalcFlagRisk';
                            $job = new $createObject();

                            $job->args = $riskCalcArray;
                            $resque->enqueue($job, true);
                        }

                    }
                    $orgRiskGroupModelRepository->flush();
                    $orgRiskGroupModelRepository->clear();
                    $validRows++;
                } catch (ValidationException $e) {
                    $logger->error(">>>>>>>>>>>>>>>>>>>> " . $e->getMessage());
                    $errors[$id][] = [
                        'name' => '',
                        'value' => '',
                        'errors' => [
                            $e->getMessage()
                        ]
                    ];
                }
            }
        }
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> updating valid row count.");
        $uploadFileLogService->updateValidRowCount($uploadId, $validRows);

        $orgRiskGroupModelRepository->flush();
        $orgRiskGroupModelRepository->clear();
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelAssingmentUpload--Flushed");
        $jobs = $cache->fetch("riskmodelassignment.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("riskmodelassignment.upload.{$uploadId}.jobs", $jobs);

        $cache->save("riskmodelassignment:upload:{$uploadId}:job:{$jobNumber}:errors", $errors);

        return $errors;
    }

    private function validateEntity($entityErrors)
    {
        $logger = $this->getContainer()->get('logger');
        $logger->info("------------------------------------ Validating Entity".count($entityErrors));
        if (count($entityErrors) > 0) {
            $errorsString = "";
            foreach ($entityErrors as $error) {
                $logger->info("-------------------------- ERROR String ".$error->getMessage());
                $errorsString .= $error->getMessage();
            }

            throw new ValidationException([
                $errorsString
                ], $errorsString, 'orgriskmodelassingment_duplicate_error');
        }
    }
}
