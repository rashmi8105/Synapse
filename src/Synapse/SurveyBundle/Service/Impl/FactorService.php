<?php
namespace Synapse\SurveyBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\LanguageMasterRepository;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Rbac;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SurveyBundle\Entity\Factor;
use Synapse\SurveyBundle\Entity\FactorLang;
use Synapse\SurveyBundle\EntityDto\FactorDto;
use Synapse\SurveyBundle\EntityDto\FactorListDto;
use Synapse\SurveyBundle\EntityDto\FactorQuestionsDto;
use Synapse\SurveyBundle\EntityDto\FactorReorderDto;
use Synapse\SurveyBundle\EntityDto\FactorsArrayDto;
use Synapse\SurveyBundle\EntityDto\SurveyQuestionsDto;
use Synapse\SurveyBundle\Repository\FactorLangRepository;
use Synapse\SurveyBundle\Repository\FactorQuestionsRepository;
use Synapse\SurveyBundle\Repository\FactorRepository;

/**
 * @DI\Service("factor_service")
 */
class FactorService extends AbstractService
{

    const SERVICE_KEY = 'factor_service';

    const FACTOR_MAX_RANGE = 7; // Fators default min val
    const FACTOR_MIN_RANGE = 1; // Fators default max val
    const ADMIN_APP_ORG_ID = -1; // organization id for the admin user
    const UNIQUE_FACTOR_NAME_ERROR = 'Name taken. Select a unique name.';
    const LANG_NOT_FOUND = 'Language Not Found.';
    const FACTOR_NOT_FOUND = 'Factor Not Found.';
    const FACTOR_NOT_FOUND_KEY = 'factor_not_found';

    /**
     *
     * @var Container
     */
    private $container;

    /**
     * @var Rbac
     */
    private $rbacManager;

    /**
     *
     * @var RepositoryResolver
     */
    protected $repositoryResolver;

    /**
     *
     * @var FactorLangRepository
     */
    private $factorLangRepository;

    /**
     *
     * @var FactorQuestionsRepository
     */
    private $factorQuestionsRepository;

    /**
     *
     * @var FactorRepository
     */
    private $factorRepository;

    /**
     * @var LanguageMasterRepository
     */
    private $languageMasterRepository;

    /**
     *
     * @var OrganizationlangRepository
     */
    private $organizationLangRepository;

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->rbacManager = $this->container->get('tinyrbac.manager');

        // Repositories
        $this->factorLangRepository = $this->repositoryResolver->getRepository(FactorLangRepository::REPOSITORY_KEY);
        $this->factorQuestionsRepository = $this->repositoryResolver->getRepository(FactorQuestionsRepository::REPOSITORY_KEY);
        $this->factorRepository = $this->repositoryResolver->getRepository(FactorRepository::REPOSITORY_KEY);
        $this->languageMasterRepository = $this->repositoryResolver->getRepository(LanguageMasterRepository::REPOSITORY_KEY);
        $this->organizationLangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);

        // Services
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);

    }

    /**
     * Create Factor
     *
     * @param FactorDto $factorDto
     * @return FactorDto
     */
    public function createFactor(FactorDto $factorDto)
    {
        $logContent = $this->loggerHelperService->getLog($factorDto);
        $this->logger->debug(" Create new factor  -  " . $logContent);
        $factorName = trim($factorDto->getFactorName());
        $uniqueFactorName = $this->factorLangRepository->checkFactorName($factorName);
        if ($uniqueFactorName) {
            $this->isObjectExist(null, self::UNIQUE_FACTOR_NAME_ERROR, self::UNIQUE_FACTOR_NAME_ERROR);
        } else {
            $sequence = $this->factorRepository->getSequenceOrder();
            $sequence ++;
            // Factor table
            $factor = new Factor();
            $factor->setSequence($sequence);
            $factorObj = $this->factorRepository->persist($factor);
            // Factor Lang table
            $factorLang = new FactorLang();
            $factorLang->setFactor($factorObj);
            $factorLang->setName($factorName);
            $lang = $this->languageMasterRepository->find($factorDto->getLangId());
            $this->isObjectExist($lang, self::LANG_NOT_FOUND, self::LANG_NOT_FOUND);
            $factorLang->setLanguageMaster($lang);
            $this->factorLangRepository->persist($factorLang);
        }
        $factorDto->setId($factorObj->getId());
        $factorDto->setOrder($factorObj->getSequence());
        $this->logger->info(" Factor is created");
        return $factorDto;
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
     * List the Factors based on organization and logged in person.
     *
     * @param int $orgId
     * @param int $personId
     * @return FactorListDto
     */
    public function listFactor($orgId, $personId)
    {
        $this->logger->debug("List Factors");
        $factorList = $this->factorLangRepository->findAll();
        $organizationLang = $this->organizationLangRepository->findOneBy([
            'organization' => self::ADMIN_APP_ORG_ID
        ]);
        $organizationLangId = '';
        if ($organizationLang) {
            $organizationLangId = $organizationLang->getLang()->getId();
        }
        
        $factorListDto = new FactorListDto();
        $factorListDto->setLangId($organizationLangId);
        $factorArray = [];
        if ($orgId != self::ADMIN_APP_ORG_ID) {
            $ebiQuestion = $this->factorQuestionsRepository->getDataBlockQuestionsBasedPermission($orgId, $personId);
            if (! empty($ebiQuestion)) {
                $ebiQuestions = array_column($ebiQuestion, 'ebi_question_id');
                $factorList = $this->factorQuestionsRepository->getFactors($ebiQuestions);
                if (! empty($factorList)) {
                    foreach ($factorList as $factors) {
                        $factorId = $factors['factorId'];
                        $factorQuestions = $this->factorQuestionsRepository->getAllFactorQuestions($factorId);
                        $factorArrayDto = new FactorsArrayDto();
                        $factorArrayDto->setId($factorId);
                        $factorArrayDto->setFactorName($factors['factorName']);
                        $factorArrayDto->setSequence($factors['sequence']);
                        $factorArrayDto->setQuestionCount(count($factorQuestions));
                        $this->setFactorRange($factorArrayDto);
                        $factorArray[] = $factorArrayDto;
                    }
                }
            }
        } else {
            if (! empty($factorList)) {
                foreach ($factorList as $factors) {
                    $factorId = $factors->getFactor()->getId();
                    $factorQuestions = $this->factorQuestionsRepository->getAllFactorQuestions($factorId);
                    $factorArrayDto = new FactorsArrayDto();
                    $factorArrayDto->setId($factorId);
                    $factorArrayDto->setFactorName($factors->getName());
                    $factorArrayDto->setSequence($factors->getFactor()
                        ->getSequence());
                    $factorArrayDto->setQuestionCount(count($factorQuestions));
                    $minimum = isset($range[$factorId]['range_min']) ? $range[$factorId]['range_min'] : '';
                    $maximum = isset($range[$factorId]['range_max']) ? $range[$factorId]['range_max'] : '';
                    $factorArrayDto->setRangeMin($minimum);
                    $factorArrayDto->setRangeMax($maximum);
                    $factorArray[] = $factorArrayDto;
                }
            }
        }
        $factorListDto->setTotalCount(count($factorList));
        $factorListDto->setFactors($factorArray);
        return $factorListDto;
    }

    /**
     * Reorder the factor sequence.
     *
     * @param FactorReorderDto $factorReorderDto
     * @return FactorReorderDto
     */
    public function reorderFactorSequence(FactorReorderDto $factorReorderDto)
    {
        $logContent = $this->loggerHelperService->getLog($factorReorderDto);
        $this->logger->debug(" Reorder factor sequence  -  " . $logContent);

        $factors = $this->factorRepository->findOneBy([
            'id' => $factorReorderDto->getFactorId()
        ]);
        $this->isObjectExist($factors, self::FACTOR_NOT_FOUND, self::FACTOR_NOT_FOUND_KEY);
        $newFactorSequence = $factorReorderDto->getSequence();
        $oldFactorSequence = $factors->getSequence();
        $maxFactorSequence = $this->factorRepository->getSequenceOrder();
        if ($newFactorSequence > $maxFactorSequence) {
            $newFactorSequence = $maxFactorSequence;
        } elseif ($oldFactorSequence < $newFactorSequence) {
            for ($i = $oldFactorSequence + 1; $i <= $newFactorSequence; $i++) {
                $factorSequence = $this->getFactorSequence($i);
                if ($factorSequence) {
                    $j = $i - 1;
                    $factorSequence->setSequence($j);
                }
            }
        } else {
            for ($i = $oldFactorSequence - 1; $i >= $newFactorSequence; $i--) {
                $factorSequence = $this->getFactorSequence($i);
                if ($factorSequence) {
                    $j = $i + 1;
                    $factorSequence->setSequence($j);
                }
            }
        }
        $factors->setSequence($newFactorSequence);
        $this->factorRepository->flush();
        return $factorReorderDto;
    }

    private function getFactorSequence($i)
    {
        $factorSequence = $this->factorRepository->findOneBy([
            'sequence' => $i
        ]);
        return $factorSequence;
    }

    public function getFactorQuestions($factorId)
    {
        $this->logger->info(" Get the factor questions ");
        $factorLang = $this->factorLangRepository->findOneBy([
            'factor' => $factorId
        ]);
        $this->isObjectExist($factorLang, "Invalid Factor Id", 'Invalid_factor_id');
        $factorQuestions = $this->factorQuestionsRepository->getAllFactorQuestions($factorId);
        $factorQuestionsDto = new FactorQuestionsDto();
        $factorQuestionsDto->setlangId($factorLang->getLanguageMaster()
            ->getId());
        $factorQuestionsDto->setFactorId($factorId);
        $factorQuestionsDto->setFactorName($factorLang->getName());
        $factorQuestionsDto->setTotalCount(count($factorQuestions));
        $surveyQuestions = [];
        if (! empty($factorQuestions)) {
            foreach ($factorQuestions as $factorQuestion) {
                $surveyQuestionsDto = new SurveyQuestionsDto();
                $surveyQuestionsDto->setId($factorQuestion['factor_ebi_id']);
                $surveyQuestionsDto->setRptText($factorQuestion['fact_ebi_ques']);
                $surveyQuestions[] = $surveyQuestionsDto;
            }
            $factorQuestionsDto->setSurveyQuestions($surveyQuestions);
        }
        return $factorQuestionsDto;
    }

    public function deleteFactorQuestion($factorId, $ebiQueId)
    {
        $this->logger->debug(" Delete Factor Question - Factor ID" . $factorId . "Question Id " . $ebiQueId);
        $factorObj = $this->factorRepository->find($factorId);
        $this->isObjectExist($factorObj, "Factor Not Found", "factor_not_found");
        $factorQuestion = $this->factorQuestionsRepository->findOneBy([
            'factor' => $factorId,
            'ebiQuestion' => $ebiQueId
        ]);
        $this->isObjectExist($factorQuestion, "Factor question not found", "factor_question_not_found");
        $delmarkerQue = $this->factorQuestionsRepository->delete($factorQuestion);
        $this->factorQuestionsRepository->flush();
        $this->logger->info(" Factor question is deleted");
        return $factorQuestion->getId();
    }

    /**
     * Edit Factor Details.
     *
     * @param FactorDto $factorDto
     * @return FactorDto
     */
    public function editFactor(FactorDto $factorDto)
    {
        $logData = $this->loggerHelperService->getLog($factorDto);
        $this->logger->debug(" Editing Factor  -  " . $logData);
        $factor = $this->factorRepository->find($factorDto->getId());
        $this->isObjectExist($factor, "Factor Not Found", "factor_not_found");
        $factorName = trim($factorDto->getFactorName());
        // Checking Survey Marker name uniqueness
        $uniqueFactorName = $this->factorLangRepository->checkFactorName($factorName, $factorDto->getId());
        if ($uniqueFactorName) {
            $this->isObjectExist(null, self::UNIQUE_FACTOR_NAME_ERROR, self::UNIQUE_FACTOR_NAME_ERROR);
        } else {
            // Updating survey marker name
            $factorLang = $this->factorLangRepository->findOneBy([
                'factor' => $factorDto->getId()
            ]);
            $factorLang->setName($factorName);
            $this->factorLangRepository->flush();
        }
        $this->logger->info(" Factor Edited");
        return $factorDto;
    }

    public function deleteFactor($id)
    {
        $this->logger->debug(" Delete Factor" . $id);
        $factor = $this->factorRepository->find($id);
        $this->isObjectExist($factor, "Factor Not Found", "factor_not_found");
        $oldSeq = $factor->getSequence();

        $factorQuestions = $this->factorQuestionsRepository->findBy([
            'factor' => $id
        ]);
        if (count($factorQuestions) > 0) {
            $deleteQues = $this->factorQuestionsRepository->deleteFactorQuestions($id);
        }
        $factorLang = $this->factorLangRepository->findOneBy([
            'factor' => $id
        ]);
        $this->factorLangRepository->delete($factorLang);
        $delFactor = $this->factorRepository->delete($factor);
        $factorSequence = null;
        $factorSequence = $this->factorRepository->findOneBy([
            'sequence' => ($oldSeq + 1)
        ]);
        /* Reseting Sequence */
        while ($factorSequence) {
            $factorSequence->setSequence(($factorSequence->getSequence() - 1));
            $oldSeq ++;
            $factorSequence = $this->factorRepository->findOneBy([
                'sequence' => ($oldSeq + 1)
            ]);
        }
        $this->factorRepository->flush();
        $this->logger->info(" Factor is Deleted ");
        return $factor->getId();
    }
    
    /**
     * Method for getting the list of factors based on the the survey Id and the permissions on surveyblocks 
     * 
     * @param int $orgId
     * @param int $personId 
     * @param int $surveyId 
     * 
     * @return FactorListDto
     */
    public function listFactorOnPermission($orgId, $personId, $surveyId)
    {
        $this->logger->info("List Factors");

        // Getting the permissible surey blocks from the accessMap
        $accessMap = $this->rbacManager->getAccessMap($personId);
        
        $surveyBlockArray = [];
        
        if (isset($accessMap['surveyBlocks']) && is_array($accessMap['surveyBlocks'])) {
            foreach ($accessMap['surveyBlocks'] as $survey) {
                if ($survey['value'] == "*") {
                    $surveyBlockArray[] = $survey['id'];
                }
            }
        }
        
        if (count($surveyBlockArray) > 0) {
            
            // Finding the factors for the survey blocks the user has permission for.
            $factorList = $this->factorLangRepository->getFactorsBasedOnSurveyBlocks($surveyBlockArray, $surveyId);
        } else {
            
            // else if the user does not have permission to any survey blocks.. no factors selected
            $factorList = [];
        }
        
        $facArr = [];
        foreach ($factorList as $fac) {
            $facArr[] = $fac['factor_id'];
        }
        $organizationLang = $this->organizationLangRepository->findOneBy([
            'organization' => $orgId
        ]);
        $lang = '';
        if ($organizationLang) {
            $lang = $organizationLang->getLang()->getId();
        }
        
        $factorListDto = new FactorListDto();
        $factorListDto->setLangId($lang);
        $factorArray = [];
        
        if (! empty($factorList)) {
            foreach ($factorList as $factors) {
                $factorId = $factors['factor_id'];
                $factorArrayDto = new FactorsArrayDto();
                $factorArrayDto->setId($factorId);
                $factorArrayDto->setFactorName($factors['factorName']);
                $factorArrayDto->setSequence($factors['sequence']);
                $this->setFactorRange($factorArrayDto);
                $factorArray[] = $factorArrayDto;
            }
        }
        
        $factorListDto->setTotalCount(count($factorList));
        $factorListDto->setFactors($factorArray);
        return $factorListDto;
    }
    
    private function setFactorRange($factorArrayDto)
    {
        
        /*
         * Calculation of factor range via getFactorRange method has been changed to use default range of 1-7
        * as query for calculating factor range was timing out, as discussed in ESPRJ-7007
        */
        $factorArrayDto->setRangeMin(self::FACTOR_MIN_RANGE);
        $factorArrayDto->setRangeMax(self::FACTOR_MAX_RANGE);
    }
    
}