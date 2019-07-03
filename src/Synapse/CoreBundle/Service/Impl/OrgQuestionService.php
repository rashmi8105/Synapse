<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgQuestionRepository;
use Synapse\CoreBundle\Repository\SurveyLangRepository;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\SurveyBundle\EntityDto\ISQResponseDto;
use Synapse\SurveyBundle\EntityDto\SurveyQuestionsArrayDto;
use Synapse\SurveyBundle\Repository\SurveyQuestionsRepository;

/**
 * @DI\Service("orgquestion_service")
 */
class OrgQuestionService extends AbstractService
{

    const SERVICE_KEY = "orgquestion_service";

    //Class Constants

    /**
     * @var array
     */
    private $questionTypes = [
        'D',
        'Q'
    ];


    //Scaffolding

    /**
     * @var Container
     */
    private $container;


    //Services

    /**
     * @var dateUtilityService
     */
    private $dateUtilityService;


    //Repositories

    /**
     * @var orgQuestionRepository
     */
    private $orgQuestionRepository;

    /**
     * @var surveyLangRepository
     */
    private $surveyLangRepository;

    /**
     * @var surveyQuestionsRepository
     */
    private $surveyQuestionsRepository;

    /**
     * org question service constructor.
     *
     * @DI\InjectParams({
     *     "repositoryResolver" = @DI\Inject("repository_resolver"),
     *     "logger" = @DI\Inject("logger"),
     *     "container" = @DI\Inject("service_container")
     * })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        // Services
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);

        // Repositories
        $this->orgQuestionRepository = $this->repositoryResolver->getRepository(OrgQuestionRepository::REPOSITORY_KEY);
        $this->surveyLangRepository = $this->repositoryResolver->getRepository(SurveyLangRepository::REPOSITORY_KEY);
        $this->surveyQuestionsRepository = $this->repositoryResolver->getRepository(SurveyQuestionsRepository::REPOSITORY_KEY);
    }

    /**
     * Gets cohort-survey combinations for the specified user and organization.
     *
     * @param int $organizationId
     * @param int $userId
     * @param int|null $orgAcademicYearId
     * @param string|null $surveyStatus - survey status launched,closed.
     * @param string $excludeQuestionType
     * @return array
     */
    public function getISQcohortsAndSurveysWithRespectToYears($organizationId, $userId, $orgAcademicYearId = null, $surveyStatus = null, $excludeQuestionType = null)
    {
        $rawCohortSurveyData = $this->orgQuestionRepository->getISQcohortsAndSurveysWithRespectToYearsForOrganization($organizationId, $userId, $orgAcademicYearId, $surveyStatus, $excludeQuestionType);
        return $this->formatSurveyAndCohortData($organizationId, $rawCohortSurveyData, 'ISQ');
    }

    /**
     * Gets all survey cohort data for the specified years
     *
     * @param int $organizationId
     * @param int $userId
     * @param int|null $orgAcademicYearId
     * @param string|null $surveyStatus - survey status launched,closed.
     * @param string $excludeQuestionType
     * @return array
     */
    public function getSurveysAndCohortsWithRespectToYears($organizationId, $userId, $orgAcademicYearId = null, $surveyStatus = null, $excludeQuestionType = null)
    {
        $rawCohortSurveyData = $this->orgQuestionRepository->getSurveysAndCohortsWithRespectToYearsForOrganization($organizationId, $userId, $orgAcademicYearId, $surveyStatus, $excludeQuestionType);
        return $this->formatSurveyAndCohortData($organizationId, $rawCohortSurveyData, 'survey');
    }

    /**
     * This method will format SurveyAndCohort data according to there appearance in the year.
     * Formats the survey and cohort specific data by year, then survey, then cohort.
     *
     * @param int $organizationId
     * @param array $surveysAndCohortsData
     * @param string $purpose - isq or survey question
     * @return array
     */
    private function formatSurveyAndCohortData($organizationId, $surveysAndCohortsData, $purpose)
    {
        $dataGroupByYear = [];
        $dataGroupBySurvey = [];
        $dataGroupByCohort = [];
        $dataGroupByQuestion = [];

        foreach ($surveysAndCohortsData as $surveyAndYearData) {
            $surveyId = $surveyAndYearData['survey_id'];
            $cohortCode = $surveyAndYearData['cohort'];
            $yearId = $surveyAndYearData['year_id'];
            // data for years
            $dataGroupByYear[$yearId]['org_academic_year_id'] = $surveyAndYearData['org_academic_year_id'];
            $dataGroupByYear[$yearId]['name'] = $surveyAndYearData['year_name'];
            $dataGroupByYear[$yearId]['id'] = $yearId;
            // data for survey
            $dataGroupBySurvey[$yearId]['surveys'][$surveyId]['id'] = $surveyId;
            $dataGroupBySurvey[$yearId]['surveys'][$surveyId]['name'] = $surveyAndYearData['survey_name'];
            // data for cohort
            $dataGroupByCohort[$yearId]['surveys'][$surveyId]['cohorts'][$cohortCode]['id'] = $surveyAndYearData['cohort'];
            $dataGroupByCohort[$yearId]['surveys'][$surveyId]['cohorts'][$cohortCode]['name'] = $surveyAndYearData['cohort_name'];
            $dataGroupByCohort[$yearId]['surveys'][$surveyId]['cohorts'][$cohortCode]['status'] = $surveyAndYearData['status'];
            $dataGroupByCohort[$yearId]['surveys'][$surveyId]['cohorts'][$cohortCode]['open_date'] = $this->dateUtilityService->convertDatabaseStringToISOString($surveyAndYearData['open_date']);
            $dataGroupByCohort[$yearId]['surveys'][$surveyId]['cohorts'][$cohortCode]['close_date'] = $this->dateUtilityService->convertDatabaseStringToISOString($surveyAndYearData['close_date']);
            // data for questions
            $questionId = $surveyAndYearData['question_id'];
            $dataGroupByQuestion[$yearId]['surveys'][$surveyId]['cohorts'][$cohortCode]['survey_questions'][$questionId]['question_id'] = $surveyAndYearData['question_id'];
            $dataGroupByQuestion[$yearId]['surveys'][$surveyId]['cohorts'][$cohortCode]['survey_questions'][$questionId]['question_type'] = $surveyAndYearData['question_type'];
            $dataGroupByQuestion[$yearId]['surveys'][$surveyId]['cohorts'][$cohortCode]['survey_questions'][$questionId]['question_text'] = $surveyAndYearData['question_text'];
        }

        krsort($dataGroupByYear);
        krsort($dataGroupBySurvey);
        krsort($dataGroupByCohort);
        krsort($dataGroupByQuestion);

        $dataToReturn = [];
        // Format above group of data to tree structure for compare modal and reset array keys to read by front-end easily
        $yearCount = 0;
        foreach ($dataGroupByYear as $yearId => $surveyYear) {
            // Year Data
            $dataToReturn[$yearCount]['id'] = (int)$yearId;
            $dataToReturn[$yearCount]['name'] = (string)$surveyYear['name'];
            $dataToReturn[$yearCount]['org_academic_year_id'] = (int)$surveyYear['org_academic_year_id'];
            // Survey Data
            $surveyCount = 0;
            foreach ($dataGroupBySurvey[$yearId]['surveys'] as $surveyId => $surveyDetails) {
                $dataToReturn[$yearCount]['surveys'][$surveyCount]['id'] = (int)$surveyDetails['id'];
                $dataToReturn[$yearCount]['surveys'][$surveyCount]['name'] = (string)$surveyDetails['name'];
                // Cohort Data
                $cohortCount = 0;
                foreach ($dataGroupByCohort[$yearId]['surveys'][$surveyId]['cohorts'] as $cohortCode => $cohortDetails) {
                    $dataToReturn[$yearCount]['surveys'][$surveyCount]['cohorts'][$cohortCount]['id'] = (int)$cohortDetails['id'];
                    $dataToReturn[$yearCount]['surveys'][$surveyCount]['cohorts'][$cohortCount]['name'] = (string)$cohortDetails['name'];
                    $dataToReturn[$yearCount]['surveys'][$surveyCount]['cohorts'][$cohortCount]['status'] = (string)$cohortDetails['status'];
                    $dataToReturn[$yearCount]['surveys'][$surveyCount]['cohorts'][$cohortCount]['open_date'] = $this->dateUtilityService->convertDatabaseStringToISOString($cohortDetails['open_date']);
                    $dataToReturn[$yearCount]['surveys'][$surveyCount]['cohorts'][$cohortCount]['close_date'] = $this->dateUtilityService->convertDatabaseStringToISOString($cohortDetails['close_date']);
                    // Survey Question Data
                    $questionCount = 0;
                    foreach ($dataGroupByQuestion[$yearId]['surveys'][$surveyId]['cohorts'][$cohortCode]['survey_questions'] as $questionId => $surveyQuestionDetails) {
                        $dataToReturn[$yearCount]['surveys'][$surveyCount]['cohorts'][$cohortCount]['survey_questions'][$questionCount]['type'] = (string)$purpose;
                        $dataToReturn[$yearCount]['surveys'][$surveyCount]['cohorts'][$cohortCount]['survey_questions'][$questionCount]['question_id'] = (int)$surveyQuestionDetails['question_id'];
                        $dataToReturn[$yearCount]['surveys'][$surveyCount]['cohorts'][$cohortCount]['survey_questions'][$questionCount]['question_type'] = (string)$surveyQuestionDetails['question_type'];
                        $dataToReturn[$yearCount]['surveys'][$surveyCount]['cohorts'][$cohortCount]['survey_questions'][$questionCount]['question_text'] = (string)$surveyQuestionDetails['question_text'];
                        $questionCount++;
                    }
                    $cohortCount++;
                }
                $surveyCount++;
            }
            $yearCount++;
        }

        $returnData = [];
        $returnData['organization_id'] = $organizationId;
        $returnData['years'] = $dataToReturn;
        return $returnData;
    }

    /**
     * Gets the options for the specified ISQ and survey.
     *
     * @param int|null $surveyId
     * @param int|null $questionId
     * @return array
     * @throws SynapseValidationException
     */
    public function getISQsurveyQuestionOptions($surveyId, $questionId)
    {
        $survey = $this->surveyLangRepository->findOneBy(['survey' => $surveyId]);
        if (!$survey) {
            throw new SynapseValidationException('Survey Not Found');
        }

        $surveyQuestionId = $this->surveyQuestionsRepository->findOneBy(['orgQuestion' => $questionId]);
        if (!$surveyQuestionId) {
            throw new SynapseValidationException('Question Not Found');
        }

        $questionOptionsResult = $this->surveyQuestionsRepository->getOrgQuestionOptions($surveyId, $questionId);
        return $questionOptionsResult;
    }

}