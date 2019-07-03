<?php

namespace Synapse\SurveyBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EbiMetadataListValuesRepository;
use Synapse\CoreBundle\Repository\EbiQuestionLangRepository;
use Synapse\CoreBundle\Repository\EbiQuestionOptionsRepository;
use Synapse\CoreBundle\Repository\EbiQuestionRepository;
use Synapse\CoreBundle\Repository\LanguageMasterRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\SurveyLangRepository;
use Synapse\CoreBundle\Repository\SurveyRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Rbac;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\MapworksToolBundle\DAO\IssueDAO;
use Synapse\MapworksToolBundle\EntityDto\TopIssuesDTO;
use Synapse\MapworksToolBundle\EntityDto\IssuesInputDTO;
use Synapse\SearchBundle\Service\Impl\StudentListService;
use Synapse\SurveyBundle\Entity\Issue;
use Synapse\SurveyBundle\Entity\IssueLang;
use Synapse\SurveyBundle\Entity\IssueOptions;
use Synapse\SurveyBundle\EntityDto\IssueCreateDto;
use Synapse\SurveyBundle\EntityDto\IssueCreateFactorDto;
use Synapse\SurveyBundle\EntityDto\IssueCreateQuesOptionsDto;
use Synapse\SurveyBundle\EntityDto\IssueCreateQuestionsDto;
use Synapse\SurveyBundle\EntityDto\IssuesListDto;
use Synapse\SurveyBundle\Repository\FactorLangRepository;
use Synapse\SurveyBundle\Repository\FactorRepository;
use Synapse\SurveyBundle\Repository\IssueLangRepository;
use Synapse\SurveyBundle\Repository\IssueOptionsRepository;
use Synapse\SurveyBundle\Repository\IssueRepository;
use Synapse\SurveyBundle\Repository\SurveyQuestionsRepository;
use Synapse\SurveyBundle\Repository\WessLinkRepository;

/**
 * @DI\Service("issue_service")
 */
class IssueService extends AbstractService
{

    const SERVICE_KEY = 'issue_service';

    // Scaffolding

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var Container
     */
    private $container;

    // Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var Rbac
     */
    private $rbacManager;

    /**
     * @var StudentListService
     */
    private $studentListService;

    //DAO

    /**
     * @var issueDAO
     */
    private $issueDAO;

    // Repositories

    /**
     * @var IssueRepository
     */
    private $issueRepository;

    /**
     * @var IssueLangRepository
     */
    private $issueLangRepository;

    /**
     * @var IssueOptionsRepository
     */
    private $issueOptionsRepository;

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EbiQuestionRepository
     */
    private $ebiQuestionRepository;

    /**
     * @var EbiQuestionLangRepository
     */
    private $ebiQuestionLangRepository;

    /**
     * @var EbiQuestionOptionsRepository
     */
    private $ebiQuestionOptionsRepository;

    /**
     * @var FactorRepository
     */
    private $factorRepository;

    /**
     * @var FactorLangRepository
     */
    private $factorLangRepository;

    /**
     * @var LanguageMasterRepository
     */
    private $languageMasterRepository;

    /**
     * @var EbiMetadataListValuesRepository
     */
    private $metadataListValuesRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;
    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var SurveyLangRepository
     */
    private $surveyLangRepository;

    /**
     * @var SurveyRepository
     */
    private $surveyRepository;

    /**
     * @var SurveyQuestionsRepository
     */
    private $surveyQuestionsRepository;

    /**
     * @var WessLinkRepository
     */
    private $wessLinkRepository;


    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        // Scaffolding.
        $this->container = $container;
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->studentListService = $this->container->get(StudentListService::SERVICE_KEY);

        // DAO
        $this->issueDAO = $this->container->get(IssueDAO::DAO_KEY);

        // Repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->ebiQuestionRepository = $this->repositoryResolver->getRepository(EbiQuestionRepository::REPOSITORY_KEY);
        $this->ebiQuestionLangRepository = $this->repositoryResolver->getRepository(EbiQuestionLangRepository::REPOSITORY_KEY);
        $this->ebiQuestionOptionsRepository = $this->repositoryResolver->getRepository(EbiQuestionOptionsRepository::REPOSITORY_KEY);
        $this->factorRepository = $this->repositoryResolver->getRepository(FactorRepository::REPOSITORY_KEY);
        $this->factorLangRepository = $this->repositoryResolver->getRepository(FactorLangRepository::REPOSITORY_KEY);
        $this->issueRepository = $this->repositoryResolver->getRepository(IssueRepository::REPOSITORY_KEY);
        $this->issueLangRepository = $this->repositoryResolver->getRepository(IssueLangRepository::REPOSITORY_KEY);
        $this->issueOptionsRepository = $this->repositoryResolver->getRepository(IssueOptionsRepository::REPOSITORY_KEY);
        $this->languageMasterRepository = $this->repositoryResolver->getRepository(LanguageMasterRepository::REPOSITORY_KEY);
        $this->metadataListValuesRepository = $this->repositoryResolver->getRepository(EbiMetadataListValuesRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->surveyLangRepository = $this->repositoryResolver->getRepository(SurveyLangRepository::REPOSITORY_KEY);
        $this->surveyRepository = $this->repositoryResolver->getRepository(SurveyRepository::REPOSITORY_KEY);
        $this->surveyQuestionsRepository = $this->repositoryResolver->getRepository(SurveyQuestionsRepository::REPOSITORY_KEY);
        $this->wessLinkRepository = $this->repositoryResolver->getRepository(WessLinkRepository::REPOSITORY_KEY);
    }


    /**
     * Create new issue
     *
     * @param IssueCreateDto $issueCreateDto
     * @return mixed
     */
    public function createIssue($issueCreateDto)
    {
        $issueInsertFlag = 0;
        $issue = new Issue();
        $surveyObject = $this->surveyRepository->find($issueCreateDto->getSurveyId());
        if (!$surveyObject) {
            throw new SynapseValidationException('Survey Id Not Found');
        }
        $issue->setSurvey($surveyObject);
        $issue->setIcon($issueCreateDto->getIssueImage());
        if ($issueCreateDto->getfactors()) {
            $factorObject = $this->factorRepository->find($issueCreateDto->getFactors()
                ->getId());
            if (!$factorObject) {
                throw new SynapseValidationException('Factor Id Not Found');
            }
            $issue->setFactor($factorObject);
            $issue->setMin($issueCreateDto->getFactors()
                ->getRangeMin());
            $issue->setMax($issueCreateDto->getFactors()
                ->getRangeMax());
        } else {
            // question
            $questionObject = $this->surveyQuestionsRepository->find($issueCreateDto->getQuestions()
                ->getId());
            if (!$questionObject) {
                throw new SynapseValidationException('Question Id Not Found');
            }
            $issue->setSurveyQuestions($questionObject);
            if ($issueCreateDto->getQuestions()->getType() == "number") {
                $issue->setMin($issueCreateDto->getQuestions()
                    ->getMinRange());
                $issue->setMax($issueCreateDto->getQuestions()
                    ->getMaxRange());
            } elseif ($issueCreateDto->getQuestions()->getType() == "date") {
                $issue->setStartDate($issueCreateDto->getQuestions()
                    ->getStartDate());
                $issue->setEndDate($issueCreateDto->getQuestions()
                    ->getEndDate());
            } elseif ($issueCreateDto->getQuestions()->getType() == "category") {
                $options = $issueCreateDto->getQuestions()->getOptions();
                if (count($options) > 0) {
                    $issueIns = $this->issueRepository->persist($issue);
                    $issueInsertFlag = 1;
                    foreach ($options as $option) {
                        $ebiQuestionOptionsObject = $this->ebiQuestionOptionsRepository->find($option->getId());
                        if (!$ebiQuestionOptionsObject) {
                            throw new SynapseValidationException('Option Id Not Found');
                        }
                        $optionSet = new IssueOptions();
                        $optionSet->setEbiQuestionOptions($ebiQuestionOptionsObject);
                        $optionSet->setIssue($issueIns);
                        $this->issueOptionsRepository->persist($optionSet);
                    }
                }
            }
        }

        if (!$issueInsertFlag) {
            $issueIns = $this->issueRepository->persist($issue);
        }

        // Setting issue lang entity
        $issueLang = new IssueLang();
        $issueLang->setIssue($issueIns);
        $issueLang->setName($issueCreateDto->getIssueName());
        $languageObject = $this->languageMasterRepository->find($issueCreateDto->getLangId());
        if (!$languageObject) {
            throw new SynapseValidationException('Language Id Not Found');
        }
        $issueLang->setLang($languageObject);
        $issueLangIns = $this->issueLangRepository->persist($issueLang, false);
        $this->issueRepository->flush();

        $issueCreateDto->setId($issueIns->getId());
        return $issueCreateDto;
    }


    /**
     * Get Issues by Survey Id
     *
     * @param int $surveyId
     * @return array
     * @throws SynapseValidationException
     */
    public function listIssues($surveyId = null)
    {
        $response = [];
        if (is_numeric($surveyId)) {
            $surveyObject = $this->surveyRepository->find($surveyId);
            if (!$surveyObject) {
                throw new SynapseValidationException('Survey Id Not Found');
            } else {
                $allIssues = $this->issueRepository->getIssuesList($surveyId);
            }
        } else {
            $allIssues = $this->issueRepository->getIssuesList();
        }
        if (count($allIssues) > 0) {
            foreach ($allIssues as $issue) {
                $issueList = new IssuesListDto();
                $issueList->setId($issue['id']);
                $issueList->setTopIssueName($issue['issue_name']);
                $issueList->setTopIssueImage($issue['issue_icon']);
                $response[] = $issueList;
            }
        }
        return $response;
    }


    /**
     * Delete issue by issueId
     *
     * @param int $issueId
     * @throws SynapseValidationException
     */
    public function deleteIssue($issueId)
    {
        $issueObject = $this->issueRepository->find($issueId);
        if (!$issueObject) {
            throw new SynapseValidationException('Issue Id Not Found');
        } else {
            $this->issueRepository->delete($issueObject);
        }
    }


    /**
     * Edit Issue
     *
     * @param IssueCreateDto $issueCreateDto
     * @return mixed
     */
    public function editIssue($issueCreateDto)
    {
        $issue = $this->issueRepository->find($issueCreateDto->getId());
        if (!$issue) {
            throw new SynapseValidationException('Issue Id Not Found');
        }
        $surveyObject = $this->surveyRepository->find($issueCreateDto->getSurveyId());
        if (!$surveyObject) {
            throw new SynapseValidationException('Survey Id Not Found');
        }
        $issue->setSurvey($surveyObject);
        $issue->setIcon($issueCreateDto->getIssueImage());
        if ($issueCreateDto->getfactors()) {
            $factorObject = $this->factorRepository->find($issueCreateDto->getFactors()
                ->getId());
            $issue->setFactor($factorObject);
            $issue->setMin($issueCreateDto->getFactors()
                ->getRangeMin());
            $issue->setMax($issueCreateDto->getFactors()
                ->getRangeMax());
            $this->issueRepository->flush();
        } else {
            // question
            $questionObject = $this->surveyQuestionsRepository->find($issueCreateDto->getQuestions()
                ->getId());
            if (!$questionObject) {
                throw new SynapseValidationException('Question Id Not Found');
            }
            $issue->setSurveyQuestions($questionObject);
            if ($issueCreateDto->getQuestions()->getType() == "number") {
                $issue->setMin($issueCreateDto->getQuestions()
                    ->getMinRange());
                $issue->setMax($issueCreateDto->getQuestions()
                    ->getMaxRange());
            } elseif ($issueCreateDto->getQuestions()->getType() == "date") {
                $issue->setStartDate($issueCreateDto->getQuestions()
                    ->getStartDate());
                $issue->setEndDate($issueCreateDto->getQuestions()
                    ->getEndDate());
            } elseif ($issueCreateDto->getQuestions()->getType() == "category") {
                $options = $issueCreateDto->getQuestions()->getOptions();
                if (count($options) > 0) {

                    foreach ($options as $option) {
                        $ebiQuestionOptionsObject = $this->ebiQuestionOptionsRepository->find($option->getId());
                        if (!$ebiQuestionOptionsObject) {
                            throw new SynapseValidationException('Option Id Not Found');
                        }
                        $optionSet = $this->issueOptionsRepository->findOneBy(array(
                            'id' => $option->getId(),
                            'issue' => $issueCreateDto->getId()
                        ));
                        if (!$optionSet) {
                            throw new SynapseValidationException('Option Id / Issue Id Not Found');
                        }
                        if ($optionSet) {
                            $optionSet->setEbiQuestionOptions($ebiQuestionOptionsObject);
                            $optionSet->setIssue($issue);
                        } else {
                            $optionSetNew = new IssueOptions();
                            $optionSetNew->setEbiQuestionOptions($ebiQuestionOptionsObject);
                            $optionSetNew->setIssue($issue);
                            $this->issueOptionsRepository->persist($optionSetNew);
                            $this->issueOptionsRepository->flush();
                        }
                    }
                }
            }
        }

        // Setting issue lang entity
        $issueLang = $this->issueLangRepository->findOneBy(array(
            'issue' => $issueCreateDto->getId()
        ));
        if (!$issueLang) {
            throw new SynapseValidationException('Issue Id Not Found');
        }
        $issueLang->setName($issueCreateDto->getIssueName());
        $lang = $this->languageMasterRepository->find($issueCreateDto->getLangId());
        if (!$lang) {
            throw new SynapseValidationException('Language Id Not Found');
        }
        $issueLang->setLang($lang);

        $this->issueLangRepository->flush();
        return $issueCreateDto;
    }


    /**
     * Upload issue icon
     *
     * @param int $issueId
     * @param string $fileName
     * @return Issue $issue
     * @throws SynapseValidationException
     */
    public function uploadIssueIcon($issueId, $fileName)
    {
        $issue = $this->issueRepository->find($issueId);
        if (!$issue) {
            throw new SynapseValidationException("Invalid Issue Id");
        } else {
            $issue->setIcon($fileName);
            $result = $this->issueRepository->update($issue);
            if ($result) {
                return true;
            } else {
                return false;
            }

        }
    }


    /**
     * Get issue by issueId
     *
     * @param int $issueId
     * @return array
     * @throws SynapseValidationException
     */
    public function getIssue($issueId)
    {
        $response = [];

        $issueObject = $this->issueLangRepository->findOneBy(array(
            'issue' => $issueId
        ));
        if (!$issueObject) {
            throw new SynapseValidationException('Issue Not Found');
        } else {

            $issueList = new IssueCreateDto();
            $issueList->setId($issueObject->getIssue()
                ->getId());
            $issueList->setIssueName($issueObject->getName());
            $issueList->setLangId($issueObject->getLang()
                ->getId());
            $issueList->setSurveyId($issueObject->getIssue()
                ->getSurvey()
                ->getId());
            $issueList->setIssueImage($issueObject->getIssue()
                ->getIcon());

            if ($issueObject->getIssue()->getSurveyQuestions()) {
                $ebiQuestionId = $issueObject->getIssue()->getSurveyQuestions()->getId();
                $ebiQuestionObject = $this->ebiQuestionLangRepository->findOneBy(array(
                    'ebiQuestion' => $ebiQuestionId
                ));
                if (!$ebiQuestionObject) {
                    throw new SynapseValidationException('EbiQuestion Not Found');
                }
                $issueList->setQuestions($issueObject->getIssue()
                    ->getSurveyQuestions()
                    ->getId());

                $questionList = new IssueCreateQuestionsDto();
                $questionList->setId($issueObject->getIssue()
                    ->getSurveyQuestions()
                    ->getId());
                $questionList->setText($ebiQuestionObject->getQuestionText());

                if ($issueObject->getIssue()->getMin() && $issueObject->getIssue()->getMax()) {
                    $questionList->setType('number');
                    $questionList->setMinRange($issueObject->getIssue()
                        ->getMin());
                    $questionList->setMaxRange($issueObject->getIssue()
                        ->getMax());
                } elseif ($issueObject->getIssue()->getStartDate() && $issueObject->getIssue()->getEndDate()) {
                    $questionList->setType('date');
                    $questionList->setStartDate($issueObject->getIssue()
                        ->getStartDate());
                    $questionList->setEndDate($issueObject->getIssue()
                        ->getEndDate());
                } else {
                    $optionObject = $this->issueOptionsRepository->findOneBy(array(
                        'issue' => $issueObject->getIssue()
                    ));
                    $optionsArray = array();
                    if ($optionObject) {
                        $questionList->setType('category');
                        foreach ($optionObject as $option) {
                            $optionSetNew = new IssueCreateQuesOptionsDto();
                            $optionSetNew->setId($option->getId());
                            $optionSetNew->setText($option->getEbiQuestionOptions()
                                ->getOptionText());
                            $optionSetNew->setValue($option->getEbiQuestionOptions()
                                ->getOptionValue());
                            $optionsArray[] = $optionSetNew;
                        }
                    }
                    $questionList->setOptions($optionsArray);
                }
                $issueList->setQuestions($questionList);
            } elseif ($issueObject->getIssue()->getFactor()) {
                $factorId = $issueObject->getIssue()->getFactor()->getId();
                $factorObject = $this->factorLangRepository->findOneBy(array(
                    'factor' => $factorId
                ));
                if (!$factorObject) {
                    throw new SynapseValidationException('Factor Not Found');
                }
                $factorList = new IssueCreateFactorDto();
                $factorList->setId($issueObject->getIssue()
                    ->getFactor()
                    ->getId());
                $factorList->setText($factorObject->getName());
                $factorList->setRangeMin($issueObject->getIssue()
                    ->getMin());
                $factorList->setRangeMax($issueObject->getIssue()
                    ->getMax());
                $issueList->setFactors($factorList);
            }
            $response[] = $issueList;
        }
        return $response;
    }


    /**
     * gets the top issues along with percentages and details
     *
     * @param integer $topIssuesCount
     * @param integer $organizationId
     * @param integer $facultyId
     * @param integer $orgAcademicYearId
     * @param integer $cohort
     * @param integer $surveyId
     * @param array|null $studentIds
     * @return array
     */
    public function getTopIssues($topIssuesCount, $organizationId, $facultyId, $orgAcademicYearId, $cohort, $surveyId, $studentIds = null)
    {

        $this->issueDAO->generateStudentIssuesTemporaryTable($organizationId, $facultyId, $orgAcademicYearId, $surveyId, $cohort);
        $topIssues = $this->issueDAO->getTopIssuesFromStudentIssues($topIssuesCount, $studentIds);
        return $topIssues;
    }


    /**
     * gets the top issues along with percentages and a student list
     *
     * @param integer $organizationId
     * @param integer $facultyId
     * @param IssuesInputDTO $issuesInputDTO
     * @return TopIssuesDTO
     * @throws SynapseValidationException
     */
    public function getTopIssuesWithStudentList($organizationId, $facultyId, $issuesInputDTO)
    {

        $orgAcademicYearId = $issuesInputDTO->getOrgAcademicYearId();
        $currentOrgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);

        $cohort = $issuesInputDTO->getCohort();
        $surveyId = $issuesInputDTO->getSurveyId();
        $topIssuesCount = $issuesInputDTO->getNumberOfTopIssues();
        $topIssueList = $this->getTopIssues($topIssuesCount, $organizationId, $facultyId, $orgAcademicYearId, $cohort, $surveyId);
        $topIssueIds = array_column($topIssueList, 'issue_id');
        $orgAcademicYear = $this->orgAcademicYearRepository->find($orgAcademicYearId, new SynapseValidationException('Academic year specified was not found.'));
        $survey = $this->surveyLangRepository->findOneBy(['survey' => $surveyId], new SynapseValidationException('Survey specified does not exist.'));
        $participatingStudentsByIssueIds = [];
        foreach ($topIssueList as $issue) {
            $issueId = ($issue['issue_id']) ? $issue['issue_id'] : $issue->getIssueId();
            $participatingStudentsByIssueIds[$issueId] = $this->issueDAO->getDistinctParticipantStudentPopulationCount([$issueId], $currentOrgAcademicYearId, 1);
        }

        //Need the get the count of unique students
        if (empty($topIssueIds)) {
            $totalStudentsFromTopIssue = 0;
            $totalNonParticipantCount = 0;
        } else {
            $totalStudentsFromTopIssue = $this->issueDAO->getDistinctStudentPopulationCount($topIssueIds);
            $totalParticipantStudentCount = $this->issueDAO->getDistinctParticipantStudentPopulationCount($topIssueIds, $currentOrgAcademicYearId);
            $totalNonParticipantCount = $totalStudentsFromTopIssue - $totalParticipantStudentCount;
        }

        $hasStudentPopulationOrIssuesChanged = $this->hasStudentPopulationOrIssuesChanged($issuesInputDTO, $totalStudentsFromTopIssue, $participatingStudentsByIssueIds);
        if ($hasStudentPopulationOrIssuesChanged) {
            for ($issueIndex = 0; $issueIndex < count($issuesInputDTO->getTopIssuesPagination()); $issueIndex++) {
                $issuesInputDTO->getTopIssuesPagination()[$issueIndex]->setCurrentPage(SynapseConstant::DEFAULT_PAGE_NUMBER);
                $issuesInputDTO->getTopIssuesPagination()[$issueIndex]->setRecordsPerPage(SynapseConstant::DEFAULT_RECORD_COUNT);
            }
        }
        $topIssuesStudentListArray = $this->getStudentsList($issuesInputDTO, $topIssueList, $facultyId, $organizationId, $participatingStudentsByIssueIds);
        $personObject = $this->personRepository->find($facultyId, new SynapseValidationException('Person specified does not exist.'));
        $topIssuesDTO = new TopIssuesDTO();
        $this->setTopIssuesDTOProperties($topIssuesDTO, $totalStudentsFromTopIssue, $facultyId, $personObject->getFirstname(), $personObject->getLastname(), $orgAcademicYear->getName(), $surveyId, $survey->getName(), $cohort, $hasStudentPopulationOrIssuesChanged, count($topIssuesStudentListArray), $topIssuesStudentListArray, $totalNonParticipantCount);
        return $topIssuesDTO;
    }


    /**
     * This function will return the Array of students for given issue
     *
     * @param IssuesInputDTO $issuesInputDTO
     * @param array $topIssueList
     * @param int $facultyId
     * @param int $organizationId
     * @param array $participatingStudentsByIssueArray
     * @return array
     */
    public function getStudentsList($issuesInputDTO, $topIssueList, $facultyId, $organizationId, $participatingStudentsByIssueArray)
    {
        $topIssuesStudentListArray = array();
        $counter = 0;
        foreach ($topIssueList as $issue) {
            $inputIssueObject = $issuesInputDTO->getTopIssuesPagination()[$counter];
            if ($inputIssueObject->getDisplayStudents()) {
                $issueId = ($issue['issue_id']) ? $issue['issue_id'] : $inputIssueObject->getIssueId();
                $studentsIds = $this->issueDAO->getStudentListFromStudentIssues(array($issueId));
                $studentListWithDetails = $this->studentListService->getStudentListWithMetadata($studentsIds, $facultyId, $organizationId, $inputIssueObject->getSortBy(), $inputIssueObject->getCurrentPage(), $inputIssueObject->getRecordsPerPage());
                $issueName = $issue['issue_name'];
                $percent = $issue['percent'];
                $iconUrl = $issue['icon'];
                $issueParticipantStudentCount = (int)$participatingStudentsByIssueArray[$issueId];
                $issueNonParticipantCount = $issue['numerator'] - $issueParticipantStudentCount;
                $totalStudentPopulationAvailable = (int)$issue['denominator'];
                $topIssuesStudentListArray[] = $this->setIssueDetails($issueId, $inputIssueObject->getTopIssue(), $issueName, $percent, $iconUrl, $studentListWithDetails, $inputIssueObject->getSortBy(), $issueNonParticipantCount, $totalStudentPopulationAvailable, $issueParticipantStudentCount);

            }
            $counter++;
        }
        return $topIssuesStudentListArray;
    }


    /**
     * This is to set topIssuesDTO properties based on given parameters
     *
     * @param TopIssuesDTO $topIssuesDTO
     * @param integer $totalStudentsFromTopIssue
     * @param integer $facultyId
     * @param string $firstName
     * @param string $lastName
     * @param string $yearName
     * @param integer $surveyId
     * @param string $surveyName
     * @param integer $cohort
     * @param boolean $hasStudentPopulationOrIssuesChanged
     * @param integer $totalIssues
     * @param array $topIssuesArray
     * @param int $totalNonParticipantCount
     * @param string $when
     * @return TopIssuesDTO
     */
    public function setTopIssuesDTOProperties(TopIssuesDTO $topIssuesDTO, $totalStudentsFromTopIssue, $facultyId, $firstName, $lastName, $yearName, $surveyId, $surveyName, $cohort, $hasStudentPopulationOrIssuesChanged, $totalIssues, $topIssuesArray, $totalNonParticipantCount, $when = 'now')
    {
        $currentDateTime = new \DateTime($when);
        $dateTime = new \DateTime($currentDateTime->format(SynapseConstant::DATE_FORMAT_WITH_TIMEZONE));

        $topIssuesDTO->setTotalStudents($totalStudentsFromTopIssue);
        $topIssuesDTO->setFacultyId($facultyId);
        $topIssuesDTO->setFacultyFirstname($firstName);
        $topIssuesDTO->setFacultyLastname($lastName);
        $topIssuesDTO->setCurrentDatetime($dateTime);
        $topIssuesDTO->setYear($currentDateTime->format('Y'));
        $topIssuesDTO->setAcademicYearName($yearName);
        $topIssuesDTO->setSurveyId($surveyId);
        $topIssuesDTO->setSurveyName($surveyName);
        $topIssuesDTO->setCohort($cohort);
        $topIssuesDTO->setStudentPopulationChange($hasStudentPopulationOrIssuesChanged);
        $topIssuesDTO->setIssueCount($totalIssues);
        $topIssuesDTO->setTopIssues($topIssuesArray);
        $topIssuesDTO->setTotalNonParticipantCount($totalNonParticipantCount);
        return $topIssuesDTO;
    }


    /**
     * This method sets the issueDetails based on given parameters
     *
     * @param integer $issueId
     * @param integer $topIssue
     * @param string $issueName
     * @param float $percent
     * @param string $icon
     * @param array $studentListWithDetails
     * @param string $sortBy
     * @param int $nonParticipantCount
     * @param int $totalStudents
     * @param int $issueParticipantStudentCount
     * @return array
     */
    public function setIssueDetails($issueId, $topIssue, $issueName, $percent, $icon, $studentListWithDetails, $sortBy, $nonParticipantCount, $totalStudents, $issueParticipantStudentCount)
    {

        $individualIssueArray = array();
        $individualIssueArray['issue_id'] = $issueId;
        $individualIssueArray['top_issue'] = $topIssue;
        $individualIssueArray['name'] = $issueName;
        $individualIssueArray['non_participant_count_with_issue'] = $nonParticipantCount;
        $individualIssueArray['participant_count_with_issue'] = $issueParticipantStudentCount;
        $individualIssueArray['total_student_population_available_for_issue'] = $totalStudents;
        $individualIssueArray['percentage'] = $percent;
        $individualIssueArray['image'] = $icon;
        $individualIssueArray['current_page'] = $studentListWithDetails['current_page'];
        $individualIssueArray['records_per_page'] = $studentListWithDetails['records_per_page'];
        $individualIssueArray['total_pages'] = $studentListWithDetails['total_pages'];
        $individualIssueArray['sort_by'] = $sortBy;
        $individualIssueArray['students_with_issue_paginated_list'] = $studentListWithDetails['search_result'];
        return $individualIssueArray;
    }


    /**
     * Compares passed values for Issues and Student Population to see if changes have occurred
     *
     * @param IssuesInputDTO $issuesInputDTO
     * @param integer $totalStudents
     * @param array $participatingStudentsByIssuesArray
     * @return boolean
     */
    public function hasStudentPopulationOrIssuesChanged(IssuesInputDTO $issuesInputDTO, $totalStudents, $participatingStudentsByIssuesArray)
    {
        $hasChanged = false;
        if (!$issuesInputDTO->getRootCall()) {
            if ($issuesInputDTO->getTotalStudentPopulation() != $totalStudents) {
                $hasChanged = true;
            } else {
                foreach ($issuesInputDTO->getTopIssuesPagination() as $issuePaginationDTO) {
                    $issueId = $issuePaginationDTO->getIssueId();
                    if (!array_key_exists($issueId, $participatingStudentsByIssuesArray)
                        || $issuePaginationDTO->getParticipantCountWithIssue() != $participatingStudentsByIssuesArray[$issueId]
                    ) {
                        $hasChanged = true;
                    }
                }
            }
        } else {
            $hasChanged = false;
        }
        return $hasChanged;
    }

}