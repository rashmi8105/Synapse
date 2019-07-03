<?php
namespace Synapse\SurveyBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\LanguageMasterRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgQuestionRepository;
use Synapse\CoreBundle\Repository\SurveyLangRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\ISQPermissionsetService;
use Synapse\CoreBundle\Util\Helper;
use Synapse\RestBundle\Entity\OrgCoordinatorNotificationDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SurveyBundle\EntityDto\ISQResponseDto;
use Synapse\SurveyBundle\EntityDto\SurveyListDetailsDto;
use Synapse\SurveyBundle\EntityDto\SurveyQuestionsArrayDto;
use Synapse\SurveyBundle\EntityDto\SurveyQuestionsResponseDto;
use Synapse\SurveyBundle\EntityDto\SurveysDetailsArrayDto;
use Synapse\SurveyBundle\Repository\SurveyQuestionsRepository;
use Synapse\SurveyBundle\Util\Constants\SurveyErrorConstants;

/**
 * @DI\Service("survey_questions_service")
 */
class SurveyQuestionsService extends AbstractService
{
    const ID = 'id';
    const OPTIONS = 'options';
    const SERVICE_KEY = 'survey_questions_service';
    const SURVEY_QUES_ID = 'survey_ques_id';
    const TEXT = 'text';
    const VALUE = 'value';

    private $questionTypesShortName = [
        'D',
        'Q',
    ];

    private $questionTypes = [
        'Q' => 'category',
        'D' => 'category',
        'NA' => 'number',
        'MR' => 'multiresponse'
    ];

    /**
     * @var Container
     */
    private $container;

    // Services

    /**
     * @var ISQPermissionsetService
     */
    private $ISQPermissionsetService;

    //Repositories

    /**
     * @var LanguageMasterRepository
     */
    private $languageMasterRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgQuestionRepository
     */
    private $orgQuestionsRepository;

    /**
     * @var SurveyLangRepository
     */
    private $surveyLangRepository;

    /**
     * @var SurveyQuestionsRepository
     */
    private $surveyQuestionsRepository;

    /**
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     *
     * @DI\InjectParams({
     *     "repositoryResolver" = @DI\Inject("repository_resolver"),
     *     "logger" = @DI\Inject("logger"),
     *     "container" = @DI\Inject("service_container")
     * })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgQuestionsRepository = $this->repositoryResolver->getRepository(OrgQuestionRepository::REPOSITORY_KEY);
        $this->surveyLangRepository = $this->repositoryResolver->getRepository(SurveyLangRepository::REPOSITORY_KEY);
        $this->surveyQuestionsRepository = $this->repositoryResolver->getRepository(SurveyQuestionsRepository::REPOSITORY_KEY);

        $this->ISQPermissionsetService = $this->container->get(ISQPermissionsetService::SERVICE_KEY);
        $this->languageMasterRepository = $this->repositoryResolver->getRepository(languageMasterRepository::REPOSITORY_KEY);
    }


    /**
     * Returns a list of all survey questions on the given survey that the user is permitted to see,
     * excluding short answer and long answer questions, along with the options available for each question.
     *
     * @param int $organizationId
     * @param int $surveyId
     * @param int $userId
     * @return array
     */
    public function getSurveyQuestions($organizationId, $surveyId, $userId)
    {
        // Get the ebi_question_ids the user has permission to see.
        $allowedEbiQuestions = $this->surveyQuestionsRepository->getDataBlockQuestionsBasedPermission($organizationId, $userId, $surveyId, null);
        if (count($allowedEbiQuestions) > 0) {
            $allowedEbiQuestions = array_column($allowedEbiQuestions, 'ebi_question_id');
        } else {
            $allowedEbiQuestions = [];
        }

        // Get all questions on the given survey, along with all available options for each question.

        $questionsAndOptions = $this->surveyQuestionsRepository->getSurveyQuestions($surveyId);

        // Group the data by question, as a first pass at formatting the data as desired.
        $questionsAndOptionsGroupedByQuestion = [];

        foreach ($questionsAndOptions as $questionAndOption) {
            if (in_array($questionAndOption['ebi_question_id'], $allowedEbiQuestions)) {
                $questionsAndOptionsGroupedByQuestion[$questionAndOption['survey_questions_id']][] = $questionAndOption;
            }
        }

        // Format the data.
        $questionsToReturn = [];

        foreach ($questionsAndOptionsGroupedByQuestion as $surveyQuestionsId => $recordsForQuestion) {
            $options = [];

            foreach ($recordsForQuestion as $record) {
                if (isset($record['option_id'])) {
                    $options[] = [
                        'id' => $record['option_id'],
                        'text' => $record['option_text'],
                        'value' => $record['option_value']
                    ];
                }
            }

            if (empty($options)) {
                $options = null;
            }

            $questionsToReturn[] = [
                'id' => $surveyQuestionsId,
                'question_text' => $recordsForQuestion[0]['question_text'],
                'type' => $this->questionTypes[$recordsForQuestion[0]['question_type_id']],
                'ebi_question_id' => $recordsForQuestion[0]['ebi_question_id'],
                'options' => $options
            ];
        }

        $dataToReturn = [
            'organization_id' => $organizationId,
            'survey_id' => $surveyId,
            'survey_name' => $this->surveyLangRepository->findOneBy(['survey' => $surveyId])->getName(),
            'survey_questions' => $questionsToReturn
        ];

        return $dataToReturn;
    }

    /**
     * This method will return all questions along with their options ISQ specific in which they appears in the survey.
     *
     * @param int $organizationId
     * @param int $languageId
     * @param int $surveyId
     * @param int $userId
     * @param string $cohortId
     * @return ISQResponseDto
     * @throws SynapseValidationException
     */
    public function getISQWithOptions($organizationId, $languageId, $surveyId, $userId, $cohortId = '')
    {
        $language = $this->languageMasterRepository->find($languageId);
        if (!is_object($language)) {
            throw new SynapseValidationException('Language Not Found');
        }

        $survey = $this->surveyLangRepository->findOneBy([
            "survey" => $surveyId
        ]);
        if (!is_object($survey)) {
            throw new SynapseValidationException('Survey Not Found');
        }

        $currentDate = new \DateTime();
        $currentDate = $currentDate->setTime(0, 0, 0);

        $allowAggregatePermissionSets = false;
        if (!empty(trim($cohortId))) {
            $allowAggregatePermissionSets = true;
        }

        // This will get the ISQs that the user has in the organization with cohortId and SurveyId as limiting factors.
        $isqQuestionsArray = $this->ISQPermissionsetService->getFilteredISQIds($userId, $organizationId, null, null, $cohortId, $allowAggregatePermissionSets, $surveyId);

        // Get Allowed Questions
        if (count($isqQuestionsArray) > 0) {
            $isqQuestionsArray = array_filter(array_column($isqQuestionsArray, 'org_question_id'));
        } else {
            $isqQuestionsArray = [];
        }
        $organizationQuestions = $this->surveyQuestionsRepository->getIsqWithOptions($organizationId, $currentDate, $surveyId, $languageId, $cohortId);

        // Parse the orgQuestions resultSet to get the options per questions
        $questions = [];
        foreach ($organizationQuestions as $organizationQuestion) {
            if (in_array($organizationQuestion['org_ques_id'], $isqQuestionsArray)) {
                $questions[$organizationQuestion['survey_ques_id']]['org_question_id'] = $organizationQuestion['org_ques_id'];
                $questions[$organizationQuestion['survey_ques_id']]['question_text'] = $organizationQuestion['ques_text'];
                $questions[$organizationQuestion['survey_ques_id']]['question_type'] = $organizationQuestion['question_type'];

                if ($organizationQuestion['option_id']) {
                    $options = [];
                    $options['id'] = $organizationQuestion['option_id'];
                    $options['text'] = $organizationQuestion['option_text'];
                    $options['value'] = $organizationQuestion['option_value'];
                    $questions[$organizationQuestion['survey_ques_id']]['options'][] = $options;
                } else {
                    $questions[$organizationQuestion['survey_ques_id']]['options'] = [];
                }
            } else {
                continue;
            }
        }

        // Create the response for ISQ's
        $isqResponse = new ISQResponseDto();
        $isqResponse->setOrganizationId($organizationId);
        $isqResponse->setLangId($languageId);
        $isqResponse->setSurveyId($surveyId);
        $isqResponse->setSurveyName($survey->getName());
        $isqQuestions = [];
        foreach ($questions as $question) {
            $options = $question['options'];
            $surveyQuestions = new SurveyQuestionsArrayDto();
            $surveyQuestions->setId($question['org_question_id']);
            $surveyQuestions->setQuestionText($question['question_text']);

            if (in_array(trim($question['question_type']), $this->questionTypesShortName)) {
                $type = 'category';
            } elseif (strtoupper($question['question_type']) == 'NA') {
                $type = 'number';
            } elseif (strtoupper($question['question_type']) == 'MR') {
                $type = 'multiresponse';
            } else {
                $type = '';
            }
            $surveyQuestions->setType($type);
            $surveyQuestions->setOptions($options);
            $isqQuestions[] = $surveyQuestions;
        }
        $isqResponse->setIsqs($isqQuestions);
        return $isqResponse;
    }

    public function getSurveyCompletionStatus($orgId, $langId, $timeZone)
    {
        $language = $this->languageMasterRepository->findOneById($langId);
        $this->isObjectExist($language, SurveyErrorConstants::ERR_102, SurveyErrorConstants::ERR_102, SurveyErrorConstants::ERR_102, $this->logger);
        $surveyRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:Survey");
        $date = new \DateTime('now');
        $currentDate = $date->setTime(0, 0, 0);
        $surveys = $surveyRepo->getOrganizationSurveys($orgId, $langId, $currentDate);
        $timezone = $this->repositoryResolver->getRepository('SynapseCoreBundle:MetadataListValues')->findByListName($timeZone);
        if ($timezone) {
            $timeZone = $timezone[0]->getListValue();
        }
        $surveyDetails = new SurveyListDetailsDto();
        $surveyDetails->setOrganizationId($orgId);
        $surveyDetails->setLangId($langId);
        $surveyArr = array();
        foreach ($surveys as $survey){
            $surveyIns = new SurveysDetailsArrayDto();
            $surveyIns->setSurveyId($survey['survey_id']);
            $surveyIns->setSurveyName($survey['survey_name']);
            Helper::getOrganizationDate($survey['start_date'], $timeZone);
            $surveyIns->setOpenDate($survey['start_date']);
            Helper::getOrganizationDate($survey['end_date'], $timeZone);
            $surveyIns->setCloseDate($survey['end_date']);
            $surveyArr[] = $surveyIns;
        }
        $surveyDetails->setSurveys($surveyArr);
        return $surveyDetails;
    }

    protected function isObjectExist($object, $message, $key, $errorConst = '', $logger)
    {
        if (! ($object)) {
            $logger->error("Survey Questions Service - Is Object Exist - ".$errorConst . $message . " " . $key);
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }


    /**
     * Creating notification to organization coordinators
     * @param OrgCoordinatorNotificationDto $OrgCoordinatorNotification
     * @return string
     */
    public function notifiyToCoordinator($OrgCoordinatorNotification)
    {
        $organizationRole = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrganizationRole");

        $alertService = $this->container->get('alertNotifications_service');
        $orgService = $this->container->get('org_service');

        $orgId = $OrgCoordinatorNotification->getOrganizationId();
        $organization = $orgService->find($orgId);
        $coordinatorList = $organizationRole->getCoordinators($orgId);
        $coordinatorCount = count($coordinatorList);
        if($coordinatorCount > 0){
           foreach($coordinatorList as $coordinator){
            $coordinatorId = $coordinator->getPerson();
            $event = 'New_ISQ_Added';
            $reason = 'Institutional Specific Questions are now available for you to edit in your permission settings';

            $alertService->createNotification($event, $reason, $coordinatorId, null, null, null, null, null, null, $organization);

            }
            $msg = 'Success';
        }
        else {
            $msg = 'Failed';
        }

        return $msg;
    }

    /**
     * Get Survey ISQ with cohort
     * @param int $orgId
     * @param int $surveyId
     * @param int $cohortId
     * @return SurveyQuestionsResponseDto
     * @throws ValidationException
     */
    public function getSurveyCohortISQ($orgId, $surveyId, $cohortId) {
        $survey = $this->surveyLangRepository->findOneBySurvey($surveyId);
        $this->isObjectExist($survey, SurveyErrorConstants::ERR_101, SurveyErrorConstants::ERR_101, SurveyErrorConstants::ERR_101, $this->logger);

        $surveyQuestions = $this->surveyQuestionsRepository->getSurveyCohortISQ($orgId, $surveyId, $cohortId);
        $surveyQuestionsDto = new SurveyQuestionsResponseDto();
        $surveyQuestionsDto->setOrganizationId($orgId);
        $questions = array();
        foreach ($surveyQuestions as $question) {

            $questionsArrayDto = new SurveyQuestionsArrayDto();
            $questionsArrayDto->setId($question['question_id']);
            $questionsArrayDto->setQuestionText($question['question_text']);
            $questionsArrayDto->setQuestionKey($question['question_key']);
            $questions[] = $questionsArrayDto;
        }
        $surveyQuestionsDto->setSurveyId($surveyId);
        $surveyQuestionsDto->setCohortId($cohortId);
        $surveyQuestionsDto->setSurveyName($survey->getName());
        $surveyQuestionsDto->setSurveyQuestions($questions);
        $this->logger->debug('>>>>>>>>Get Survey Questions with cohort<<<<<<<<<');
        return $surveyQuestionsDto;


    }

    /**
     * Returns a list of all survey questions with options on the given survey and Question Id,
     *
     * @param int|null $surveyId
     * @param int|null $questionId
     * @return array
     * @throws SynapseValidationException
     */
    public function getSurveyQuestionOptions($surveyId, $questionId)
    {
        $survey = $this->surveyLangRepository->findOneBy(['survey' => $surveyId]);

        if (!$survey) {
            throw new SynapseValidationException('Survey Not Found');
        }

        $surveyEbiQuestionId = $this->surveyQuestionsRepository->findOneBy(['ebiQuestion' => $questionId]);

        if (!$surveyEbiQuestionId) {
            throw new SynapseValidationException('Question Not Found');
        }

        $questionOptionsResult = $this->surveyQuestionsRepository->getOptionsForSurveyQuestions($surveyId, $questionId);
        return $questionOptionsResult;
    }

}
