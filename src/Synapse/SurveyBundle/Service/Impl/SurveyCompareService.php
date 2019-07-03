<?php
namespace Synapse\SurveyBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\SurveyBundle\EntityDto\SurveyCompResponseDto;
use Synapse\SurveyBundle\EntityDto\StudentSurveyResArrayDto;
use Synapse\SurveyBundle\Repository\FactorRepository;
use Synapse\SurveyBundle\Repository\OrgPersonStudentSurveyLinkRepository;
use Synapse\SurveyBundle\Repository\SurveyResponseRepository;
use Synapse\SurveyBundle\Repository\WessLinkRepository;

/**
 * @DI\Service("survey_compare_service")
 */
class SurveyCompareService extends AbstractService
{

    const SERVICE_KEY = 'survey_compare_service';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var SurveyDashboardService
     */
    private $surveyDashboardService;

    /**
     * @var SurveyPermissionService
     */
    private $surveyPermissionService;

    /**
     * @var FactorRepository
     */
    private $factorRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgPersonStudentSurveyLinkRepository
     */
    private $orgPersonStudentSurveyLinkRepository;

    /**
     * @var SurveyResponseRepository
     */
    private $surveyResponseRepository;

    /**
     * @var WessLinkRepository
     */
    private $wessLinkRepository;


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

        // Services
        $this->surveyDashboardService = $this->container->get('survey_dashboard_service');
        $this->surveyPermissionService = $this->container->get('survey_permission_service');

        // Repositories
        $this->factorRepository = $this->repositoryResolver->getRepository("SynapseSurveyBundle:Factor");
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPermissionset');
        $this->orgPersonStudentSurveyLinkRepository = $this->repositoryResolver->getRepository('SynapseSurveyBundle:OrgPersonStudentSurveyLink');
        $this->surveyResponseRepository = $this->repositoryResolver->getRepository("SynapseSurveyBundle:SurveyResponse");
        $this->wessLinkRepository = $this->repositoryResolver->getRepository('SynapseSurveyBundle:WessLink');
    }


    /**
     * Lists the student's responses to questions and factors on the given surveys, matched up so they can be compared.
     *
     * @param array $surveyIds
     * @param int $studentId
     * @param int $orgId
     * @param int $loggedInUserId
     * @return array
     */
    public function listStudentSurveysCompare($surveyIds, $studentId, $orgId, $loggedInUserId)
    {
        // Check that the user has individual access to the student.
        $userHasAccessToStudent = $this->orgPermissionsetRepository->checkAccessToStudent($loggedInUserId, $studentId);
        if (!$userHasAccessToStudent) {
            throw new AccessDeniedException();
        }

        // Get the dates on which each survey opens.
        $openDates = [];

        foreach ($surveyIds as $surveyId) {
            $cohort = $this->orgPersonStudentSurveyLinkRepository->findOneBy(['person' => $studentId, 'survey' => $surveyId])->getCohort();
            $wessLink = $this->wessLinkRepository->findOneBy(['organization' => $orgId, 'survey' => $surveyId, 'cohortCode' => $cohort]);
            $openDates[$surveyId] = $wessLink->getOpenDate();
        }

        // Get all survey responses for this student.
        $surveyResponse = $this->surveyResponseRepository->getSurveyDecimalResponses($studentId, $surveyIds);

        // Get all factor values for this student.
        $factorValues = $this->factorRepository->getStudentFactorValues($studentId, $surveyIds);

        // Remove the factors and questions which the faculty member does not have permission to see.
        $surveyBlocks = $this->surveyPermissionService->getSurveyBlocksByFacultyStudent($loggedInUserId, $studentId);
        $this->surveyDashboardService->removeUnauthorizedFactorsAndQuestions($surveyBlocks, $factorValues, $surveyResponse);

        // Match up the student's responses to the same question/factor on different surveys.
        $compareResponse = $this->sortQuestionsAndFactors($surveyResponse, $factorValues, $openDates);

        return $compareResponse;
    }


    /**
     * Matches responses to corresponding questions and factors on different surveys, and formats them as required by the API.
     *
     * @param array $surveyResponse - a student's responses to survey questions
     * @param array $factorValues - a student's mean_values for factors
     * @param array $openDates - the dates each survey opened, indexed by survey_id
     * @return array
     */
    private function sortQuestionsAndFactors($surveyResponse, $factorValues, $openDates)
    {
        $matchedQuestionArray = array();
        foreach ($surveyResponse as $key => $question) {
            $currentQnbr = $question['qnbr'];
            $matchedQuestionArray[$currentQnbr]['question_text'] = $question['question_text'];

            $responseDto = new StudentSurveyResArrayDto();
            $responseDto->setSurveyId($question['survey_id']);
            $responseDto->setResponseText($question['option_text']);
            $responseDto->setResponseDecimal($question['decimal_value'] + 0);    // Remove any trailing zeros
            $responseDto->setSurveyDate($openDates[$question['survey_id']]);

            $matchedQuestionArray[$currentQnbr]['response'][] = $responseDto;
        }

        $matchedFactorArray = array();
        foreach ($factorValues as $key => $factor) {
            $factorId = $factor['factor_id'];
            $matchedFactorArray[$factorId]['question_text'] = $factor['factor_text'];

            $responseDto = new StudentSurveyResArrayDto();
            $responseDto->setSurveyId($factor['survey_id']);
            $responseDto->setResponseText($factor['mean_value'] + 0);
            $responseDto->setResponseDecimal($factor['mean_value'] + 0);
            $responseDto->setSurveyDate($openDates[$factor['survey_id']]);

            $matchedFactorArray[$factorId]['response'][] = $responseDto;
        }

        $matchedArray = array_merge($matchedQuestionArray, $matchedFactorArray);

        $finalResponse = array();

        foreach ($matchedArray as $key => $question) {
            // Add blank responses where they're missing so the rows are shaded correctly.
            if (count($question['response']) < count($openDates)) {
                $presentSurveys = array();
                foreach ($question['response'] as $response) {
                    $presentSurveys[] = $response->getSurveyId();
                }
                $missingSurveys = array_diff(array_keys($openDates), $presentSurveys);
                foreach ($missingSurveys as $surveyId) {
                    $responseDto = new StudentSurveyResArrayDto();
                    $responseDto->setSurveyId($surveyId);
                    $responseDto->setResponseText(null);
                    $responseDto->setResponseDecimal('-');
                    $responseDto->setSurveyDate($openDates[$surveyId]);

                    $question['response'][] = $responseDto;
                }
            }

            $surveyCompDto = new SurveyCompResponseDto();
            $surveyCompDto->setQuestionText($question['question_text']);
            $surveyCompDto->setResponse($question['response']);
            $finalResponse['survey_comparison'][] = $surveyCompDto;
        }

        return $finalResponse;
    }

}
