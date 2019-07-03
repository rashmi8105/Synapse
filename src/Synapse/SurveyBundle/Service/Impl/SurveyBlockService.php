<?php
namespace Synapse\SurveyBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\DatablockMaster;
use Synapse\CoreBundle\Entity\DatablockMasterLang;
use Synapse\CoreBundle\Repository\DatablockMasterRepository;
use Synapse\CoreBundle\Repository\EbiQuestionRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Util\Constants\SurveyBlockConstant;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SurveyBundle\EntityDto\IssueQuestionDto;
use Synapse\SurveyBundle\EntityDto\IssueSurveyQuestionsArrayDto;
use Synapse\SurveyBundle\EntityDto\SurveyBlockDetailsDto;
use Synapse\SurveyBundle\EntityDto\SurveyBlockDetailsResponseDto;
use Synapse\SurveyBundle\EntityDto\SurveyBlockDto;
use Synapse\SurveyBundle\EntityDto\SurveyDataDto;
use Synapse\UploadBundle\Repository\UploadFileLogRepository;
use Synapse\UploadBundle\Service\Impl\SurveyUploadService;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;

/**
 * @DI\Service("surveyblock_service")
 */
class SurveyBlockService extends AbstractService
{

    const SERVICE_KEY = 'surveyblock_service';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var DatablockMasterRepository
     */
    private $datablockMasterRepository;

    /**
     * @var EbiQuestionRepository
     */
    private $ebiQuestionRepository;

    /**
     * @var SurveyUploadService
     */
    private $surveyUploadService;

    private $uploadFileLogService;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "langService" = @DI\Inject("lang_service"),
     *            "container" = @DI\Inject("service_container")
     *            
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        $this->surveyUploadService = $this->container->get(SurveyUploadService::SERVICE_KEY);
        $this->uploadFileLogService = $this->container->get(UploadFileLogService::SERVICE_KEY);

        $this->ebiQuestionRepository = $this->repositoryResolver->getRepository(EbiQuestionRepository::REPOSITORY_KEY);
    }

    public function createSurveyBlock($surveyBlock)
    {
		$logContent = $this->container->get('loggerhelper_service')->getLog($surveyBlock);
        $this->logger->debug(" Creating Survey Marker  -  " . $logContent);
        
        $surveyName = trim($surveyBlock->getSurveyBlockName());
        $lang = $this->repositoryResolver->getRepository('SynapseCoreBundle:LanguageMaster')->findOneById($surveyBlock->getLang());
        $this->isObjectExist($lang, SurveyBlockConstant::ERROR_INVALID_LANG_ID, SurveyBlockConstant::ERROR_INVALID_LANG_ID_KEY);
        if (strlen($surveyName) > 50) {
            $this->isObjectExist(null, SurveyBlockConstant::SURVEY_BLOCK_NAME_VALIDATION, SurveyBlockConstant::SURVEY_BLOCK_NAME_VALIDATION_KEY);
        }
        $this->datablockLangRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:DatablockMasterLang');
        $getSurveyBlock = $this->datablockLangRepository->checkSurveyBlock($surveyName);
        if ($getSurveyBlock) {
            $this->isObjectExist(null, SurveyBlockConstant::SURVEY_BLOCK_NAME_EXISTS, SurveyBlockConstant::SURVEY_BLOCK_NAME_EXISTS_KEY);
        } else {
            $dataBlockMaster = new DatablockMaster();
            $dataBlockMaster->setBlockType("survey");
            $this->datblockRepository = $this->repositoryResolver->getRepository(SurveyBlockConstant::DATA_BLOCK_MASTER_REPO);
            $datablockMaster = $this->datblockRepository->persist($dataBlockMaster);
            $dataBlockMasterLang = new DatablockMasterLang();
            $dataBlockMasterLang->setLang($lang);
            $dataBlockMasterLang->setDatablockDesc($surveyName);
            $dataBlockMasterLang->setDatablock($datablockMaster);
            $resp = $this->datablockLangRepository->persist($dataBlockMasterLang);
            $surveyBlock->setId($datablockMaster->getId());
            $surveyBlock->setSurveyBlockName($resp->getDatablockDesc());
        }
        $this->logger->info(" Created Survey Block ");
        return $surveyBlock;
    }

    public function getSurveyBlocks()
    {
        $this->logger->info(" Get Survey Blocks ");
        $this->datblockRepository = $this->repositoryResolver->getRepository(SurveyBlockConstant::DATA_BLOCK_MASTER_LANG_REPO);
        $getSurveyBlocks = $this->datblockRepository->getSurveyBlockDetails();
        $arr = array();
        foreach ($getSurveyBlocks as $block) {
            $arr[$block[SurveyBlockConstant::ID]][SurveyBlockConstant::ID] = $block[SurveyBlockConstant::ID];
            $arr[$block[SurveyBlockConstant::ID]][SurveyBlockConstant::SURVEY_NAME] = $block[SurveyBlockConstant::SURVEY_NAME];
            if (! isset($arr[$block[SurveyBlockConstant::ID]][SurveyBlockConstant::QN_COUNT])) {
                $arr[$block[SurveyBlockConstant::ID]][SurveyBlockConstant::QN_COUNT] = 0;
            }
            if (! isset($arr[$block[SurveyBlockConstant::ID]]['factor_cnt'])) {
                $arr[$block[SurveyBlockConstant::ID]][SurveyBlockConstant::FACTOR_COUNT] = 0;
            }
            if ($block['type'] == "bank" || $block['type'] == 'survey') {
                $arr[$block[SurveyBlockConstant::ID]][SurveyBlockConstant::QN_COUNT] = $arr[$block[SurveyBlockConstant::ID]][SurveyBlockConstant::QN_COUNT] + 1;
            } elseif ($block['type'] == "factor") {
                $arr[$block[SurveyBlockConstant::ID]][SurveyBlockConstant::FACTOR_COUNT] = $arr[$block[SurveyBlockConstant::ID]][SurveyBlockConstant::FACTOR_COUNT] + 1;
            } else {
                continue;
            }
        }
        $blockArr = array(
            'total_count' => count($arr)
        );
        
        foreach ($arr as $block) {
            $respDto = new SurveyBlockDto();
            $respDto->setId($block[SurveyBlockConstant::ID]);
            $respDto->setSurveyBlockName($block[SurveyBlockConstant::SURVEY_NAME]);
            $respDto->setQuestionCount($block[SurveyBlockConstant::QN_COUNT]);
            $respDto->setFactorCount($block[SurveyBlockConstant::FACTOR_COUNT]);
            $blockArr['survey_blocks'][] = $respDto;
        }
        
        return $blockArr;
    }

    public function getSurveyBlockDetails($id)
    {
        $this->logger->debug(" Get Survey Block Details  by Id" . $id);
        $this->datblockRepository = $this->repositoryResolver->getRepository(SurveyBlockConstant::DATA_BLOCK_MASTER_LANG_REPO);
        $this->datablockMasterRepository = $this->repositoryResolver->getRepository(SurveyBlockConstant::DATA_BLOCK_MASTER_REPO);
        $getSurveyBlocksDetails = "";
        $dataBlockMaster = $this->datablockMasterRepository->findOneBy(array(
            'id' => $id
        ));
        $this->isObjectExist($dataBlockMaster, SurveyBlockConstant::ERROR_INVALID_DATABLOCK_ID, SurveyBlockConstant::ERROR_INVALID_DATABLOCK_ID_KEY);
        $getSurveyBlocksDetails = $this->datblockRepository->getSurveyBlockDetails($id);
        $totalQnCount = 0;
        $totalFactorCount = 0;
        $totalCount = 0;
        
        $cntArr = $this->getQuestionFactorCount($getSurveyBlocksDetails);
        extract($cntArr);
        $respDto = new SurveyBlockDetailsResponseDto();
        $respDto->setId($id);
        $respDto->setSurveyBlockName($getSurveyBlocksDetails[0][SurveyBlockConstant::SURVEY_NAME]);
        $totalCount ? $respDto->setTotalCount($totalCount) : $respDto->setTotalCount(0);
        $totalQnCount ? $respDto->setTotalQuestionCount($totalQnCount) : $respDto->setTotalQuestionCount(0);
        $totalFactorCount ? $respDto->setTotalFactorCount($totalFactorCount) : $respDto->setTotalFactorCount(0);
        $respDto->setBlockData($this->getBlockData($getSurveyBlocksDetails));
        $this->logger->info(" Get Survey Block Details by Id");
        return $respDto;
    }

    private function getQuestionFactorCount($getSurveyBlocksDetails)
    {
        $totalCount = 0;
        $totalQnCount = 0;
        $totalFactorCount = 0;
        foreach ($getSurveyBlocksDetails as $block) {
            $blockName = $block[SurveyBlockConstant::SURVEY_NAME];
            if (is_numeric($block[SurveyBlockConstant::EBI_QN])) {
                $totalQnCount ++;
            } elseif (is_numeric($block[SurveyBlockConstant::FACTOR_ID])) {
                $totalFactorCount ++;
            } elseif (is_numeric($block['survey_questions_id'])) {
                $totalQnCount ++;
            } else {
                $totalCount = 0;
            }
            $totalCount = $totalQnCount + $totalFactorCount;
        }
        
        return array(
            'totalQnCount' => $totalQnCount,
            'totalFactorCount' => $totalFactorCount,
            'totalCount' => $totalCount
        );
    }

    private function getBlockData($getSurveyBlocksDetails)
    {
        $blockDetailsArr = array();
        foreach ($getSurveyBlocksDetails as $blockDetails) {
            $respDtos = new SurveyBlockDetailsDto();
            if (isset($blockDetails[SurveyBlockConstant::EBI_QN]) && ! empty($blockDetails[SurveyBlockConstant::EBI_QN])) {
                $respDtos->setId($blockDetails[SurveyBlockConstant::QID]);
                $respDtos->setText($blockDetails['ebi_questionText']);
                $respDtos->setType($blockDetails[SurveyBlockConstant::TYPE]);
                $respDtos->setSurveyId(- 1);
            } elseif (isset($blockDetails[SurveyBlockConstant::EBI_QNS]) && ! empty($blockDetails[SurveyBlockConstant::EBI_QNS])) {
                $respDtos->setId($blockDetails[SurveyBlockConstant::QID]);
                $respDtos->setText($blockDetails['ebi_questionTexts']);
                $respDtos->setType($blockDetails[SurveyBlockConstant::TYPE]);
                $respDtos->setSurveyId($blockDetails[SurveyBlockConstant::SURVEY_ID]);
            } elseif (isset($blockDetails[SurveyBlockConstant::IND_QN]) && ! empty($blockDetails[SurveyBlockConstant::IND_QN])) {
                $respDtos->setId($blockDetails[SurveyBlockConstant::QID]);
                $respDtos->setText($blockDetails['ind_questionText']);
                $respDtos->setType($blockDetails[SurveyBlockConstant::TYPE]);
                $respDtos->setSurveyId($blockDetails[SurveyBlockConstant::SURVEY_ID]);
            } elseif (isset($blockDetails[SurveyBlockConstant::FACTOR_ID]) && ! empty($blockDetails[SurveyBlockConstant::FACTOR_ID])) {
                $respDtos->setId($blockDetails[SurveyBlockConstant::QID]);
                $respDtos->setText($blockDetails['factor_questionText']);
                $respDtos->setType($blockDetails[SurveyBlockConstant::TYPE]);
                $respDtos->setSurveyId($blockDetails[SurveyBlockConstant::SURVEY_ID]);
            } else {
                continue;
            }
            $blockDetailsArr[] = $respDtos;
        }
        return $blockDetailsArr;
    }

    public function deleteSurveyBlock($id)
    {
        $this->logger->debug(" Delete Survey Blocks by Id" . $id);
        $this->datablockMasterRepository = $this->repositoryResolver->getRepository(SurveyBlockConstant::DATA_BLOCK_MASTER_REPO);
        $this->datablockMasterLangRepository = $this->repositoryResolver->getRepository(SurveyBlockConstant::DATA_BLOCK_MASTER_LANG_REPO);
        $this->datablockQuestionRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:DatablockQuestions');
        $this->orgPermissionDatablockRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPermissionsetDatablock');
        
        $dataBlockMaster = '';
        $dataBlockMaster = $this->datablockMasterRepository->findOneBy(array(
            SurveyBlockConstant::ID => $id
        ));
        $this->isObjectExist($dataBlockMaster, SurveyBlockConstant::ERROR_INVALID_DATABLOCK_ID, SurveyBlockConstant::ERROR_INVALID_DATABLOCK_ID_KEY);
        $this->datablockMasterLangRepository->deleteSurveyBlock($id);
        $this->datablockQuestionRepository->deleteSurveyBlock($id);
        $this->orgPermissionDatablockRepository->deleteSurveyBlock($id);
        $this->datablockMasterRepository->removeDataBlockMaster($dataBlockMaster);
        $this->datablockMasterRepository->flush();
        
        $upload = $this->uploadFileLogService->getLastRowByType('SB');
        $this->surveyUploadService->generateDumpCSV('block', $upload['id']);
        $this->logger->info(" Delete Survey Blocks by Id ");
        return $dataBlockMaster->getId();
    }
    
    public function getDataForBlocks($type, $surveyId)
    {
        $this->logger->debug(" Get Data for Blocks for Type" . $type . "Survey Id" . $surveyId);
        $this->surveyQuestionsRepo = $this->repositoryResolver->getRepository("SynapseSurveyBundle:SurveyQuestions");
        $resultArr = array();
        if ($type == "bank") {
            $result = $this->getEbiQuestions();
            $totalCount = count($result);
            $resultArray['type'] = "bank";
            $type = "questions";
        } elseif ($type == "factors") {
            $result = $this->getFactors($surveyId);
            $resultArray['type'] = "factor";
            $resultArray[SurveyBlockConstant::SURVEY_ID] = (int) $surveyId;
            $totalCount = count($result);
        } else if ($type == 'issuequestions') {
            $issueQuestions = new IssueQuestionDto();
            $issueQuestions->setType('questionissues');
            $surveysQuesRes = $this->surveyQuestionsRepo->getQuestionsForSurvey($surveyId);
            $issueSurveyQuestions = $this->getCategoryIssueQuestions($surveyId, $surveysQuesRes);
            $issueQuestions->setTotalCount(count($surveysQuesRes));
            $issueQuestions->setSurveyId($surveyId);                    
            $issueQuestions->setQuestions($issueSurveyQuestions);            
            return $issueQuestions;
        }else {
            $this->isObjectExist(null, "Invalid Type argument", "invalid_type");
        }
        foreach ($result as $item) {
            $surveyDataDto = new SurveyDataDto();
            $surveyDataDto->setId($item['id']);
            $surveyDataDto->setText($item['text']);
            $resultArr[] = $surveyDataDto;
        }
        $resultArray['total_count'] = $totalCount;
        $resultArray[$type] = $resultArr;
        $this->logger->info(" Get Data for Blocks for Type");
        return $resultArray;
    }

    private function getCategoryIssueQuestions($surveyId, $surveysQuesRes)
    {                    
        $this->surveyQuestionsRepo = $this->repositoryResolver->getRepository("SynapseSurveyBundle:SurveyQuestions");               
        $resultArray = [];
        if(!empty($surveysQuesRes))
        {     
            foreach($surveysQuesRes as $surveyQuestion)
            {
                $questionType = ($surveyQuestion['question_type'] == 'N' ) ? 'number' : 'category';
                $issueSurveyQuestionsArrayDto = new IssueSurveyQuestionsArrayDto();
                $issueSurveyQuestionsArrayDto->setType($questionType);                
                $questionId = $surveyQuestion['survey_ques_id'];
                $issueSurveyQuestionsArrayDto->setText($surveyQuestion['ebi_ques_text']);
                $issueSurveyQuestionsArrayDto->setId($questionId);            
                if($questionType == 'category')
                {
                    $questionOptions = $this->surveyQuestionsRepo->getOptionsForSurveyQuestions($surveyId, $surveyQuestion['ebi_question_id']);                
                    $options = $this->questionOptions($questionOptions); 
                    $issueSurveyQuestionsArrayDto->setOptions($options);                
                }
                $resultArray[] = $issueSurveyQuestionsArrayDto;  
            }            
        }                   
        return $resultArray;
    }

    private function questionOptions($questionOptions)
    {
        if(!empty($questionOptions))
        {
            foreach($questionOptions as $options)
            {
                $option['id'] = $options['ebi_option_id'];
                $option['text'] = $options['ebi_option_text'];
                $option['value'] = $options['ebi_option_value'];
                $optionsArray[] = $option;
            }            
        }
        return $optionsArray;
    } 

    private function getEbiQuestions()
    {
        $ebiQuestions = $this->ebiQuestionRepository->getEbiQuestions();
        return $ebiQuestions;
    }

    private function getFactors($surveyId)
    {
        $this->factorRepo = $this->repositoryResolver->getRepository('SynapseSurveyBundle:Factor');
        $factors = $this->factorRepo->getAllFactors($surveyId);
        return $factors;
    }

    public function deleteSurveyBlockQuestion($id, $dataid)
    {
        $this->logger->debug(" Delete Survey Block Questions for Id  " . $id . " Data Id " . $dataid);
        $datablockQuestion = '';
        $this->datablockQuestionRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:DatablockQuestions');
        $datablockQuestion = $this->datablockQuestionRepository->findOneBy(array(
            "id" => $dataid,
            "datablock" => $id
        ));
        $this->isObjectExist($datablockQuestion, SurveyBlockConstant::ERROR_INVALID_DATABLOCK_OR_QID, SurveyBlockConstant::ERROR_INVALID_DATABLOCK_OR_QID_KEY);
        $this->datablockQuestionRepository->deleteSurveyBlockQuestion($datablockQuestion);
        $this->datablockQuestionRepository->flush();
        $this->logger->info(" Delete Survey Block Question for Id");
        return $datablockQuestion->getId();
    }

    public function editSurveyBlock($surveyBlock)
    {
	    $logContent = $this->container->get('loggerhelper_service')->getLog($surveyBlock);
        $this->logger->debug(" Editing Survey Marker  -  " . $logContent);
        
        $surveyBlockId = $surveyBlock->getId();
        $this->datablockLangRepository = $this->repositoryResolver->getRepository(SurveyBlockConstant::DATA_BLOCK_MASTER_LANG_REPO);
        $lang = $this->repositoryResolver->getRepository('SynapseCoreBundle:LanguageMaster')->findOneById($surveyBlock->getLang());
        $this->isObjectExist($lang, SurveyBlockConstant::ERROR_INVALID_LANG_ID, SurveyBlockConstant::ERROR_INVALID_LANG_ID_KEY);
        
        $surveyName = trim($surveyBlock->getSurveyBlockName());
        if (strlen($surveyName) > 50) {
            $this->isObjectExist(null, SurveyBlockConstant::SURVEY_BLOCK_NAME_VALIDATION, SurveyBlockConstant::SURVEY_BLOCK_NAME_VALIDATION_KEY);
        }
        $getSurveyBlock = $this->datablockLangRepository->checkSurveyBlock($surveyName, $surveyBlock->getId());
        if ($getSurveyBlock) {
            $this->isObjectExist(null, SurveyBlockConstant::SURVEY_BLOCK_NAME_EXISTS, SurveyBlockConstant::SURVEY_BLOCK_NAME_EXISTS_KEY);
        } else {
            $dataBlockMasterLang = $this->datablockLangRepository->findOneByDatablock($surveyBlockId);
            $this->isObjectExist($dataBlockMasterLang, SurveyBlockConstant::ERROR_INVALID_SURVEY_BLOCK, SurveyBlockConstant::ERROR_INVALID_SURVEY_BLOCK_KEY);
            $dataBlockMasterLang->setLang($lang);
            $dataBlockMasterLang->setDatablockDesc($surveyName);
            $this->datablockLangRepository->flush();
        }
        $this->logger->info(" Edit Survey Block ");
        return $surveyBlock;
    }

    private function isObjectExist($object, $message, $key)
    {
        if (! ($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }
    

    /**
     * Returns a combined list of factor_ids and ebi_question_ids in the system,
     * to be used in a template for uploading survey blocks.
     *
     * @return array
     */
    public function getTemplateData()
    {
        $this->logger->info(" Get Template Data");
        $results = $this->ebiQuestionRepository->getEbiQuestionsAndFactors();
        return $results;
    }
}