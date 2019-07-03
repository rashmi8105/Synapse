<?php
namespace Synapse\SurveyBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Repository\DatablockQuestionsRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\SurveyLangRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SurveyBundle\Repository\SuccessMarkerColorRepository;
use Synapse\SurveyBundle\Repository\SuccessMarkerRepository;
use Synapse\SurveyBundle\Repository\SuccessMarkerTopicDetailRepository;
use Synapse\SurveyBundle\Repository\SuccessMarkerTopicRepository;
use Synapse\SurveyBundle\Repository\SurveyResponseRepository;


/**
 * @DI\Service("survey_dashboard_service")
 */
class SurveyDashboardService extends AbstractService
{

    const SERVICE_KEY = 'survey_dashboard_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    private $rbacManager;

    // Services

    /**
     * @var SurveyPermissionService
     */
    private $surveyPermissionService;

    // Repositories

    /**
     * @var DatablockQuestionsRepository
     */
    private $datablockQuestionsRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var SuccessMarkerColorRepository
     */
    private $successMarkerColorRepository;

    /**
     * @var SuccessMarkerRepository
     */
    private $successMarkerRepository;

    /**
     * @var SuccessMarkerTopicDetailRepository
     */
    private $successMarkerTopicDetailRepository;

    /**
     * @var SuccessMarkerTopicRepository
     */
    private $successMarkerTopicRepository;

    /**
     * @var SurveyLangRepository
     */
    private $surveyLangRepository;

    /**
     * @var SurveyResponseRepository
     */
    private $surveyResponseRepository;


    /**
     *  Survey dashboard service constructor
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

        // Scaffolding
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);

        // Services
        $this->surveyPermissionService = $this->container->get(SurveyPermissionService::SERVICE_KEY);

        // Repositories
        $this->datablockQuestionsRepository = $this->repositoryResolver->getRepository(DatablockQuestionsRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->successMarkerRepository = $this->repositoryResolver->getRepository(SuccessMarkerRepository::REPOSITORY_KEY);
        $this->successMarkerColorRepository = $this->repositoryResolver->getRepository(SuccessMarkerColorRepository::REPOSITORY_KEY);
        $this->successMarkerTopicRepository = $this->repositoryResolver->getRepository(SuccessMarkerTopicRepository::REPOSITORY_KEY);
        $this->successMarkerTopicDetailRepository = $this->repositoryResolver->getRepository(SuccessMarkerTopicDetailRepository::REPOSITORY_KEY);
        $this->surveyLangRepository = $this->repositoryResolver->getRepository(SurveyLangRepository::REPOSITORY_KEY);
        $this->surveyResponseRepository = $this->repositoryResolver->getRepository(SurveyResponseRepository::REPOSITORY_KEY);
    }


    /**
     * Returns success marker and topic data for the top level of the given student's Survey Dashboard.
     * If $successMarkerId is set, only returns data about that single success marker.
     *
     * @param int $studentId
     * @param int $loggedInUserId
     * @param int $surveyId
     * @param int|null $successMarkerId
     * @return array
     * @throws AccessDeniedException
     */
    public function listSuccessMarkersAndTopicsForStudent($studentId, $loggedInUserId, $surveyId, $successMarkerId = null)
    {
        // Check that the user has individual access to the student.
        $userHasAccessToStudent = $this->orgPermissionsetRepository->checkAccessToStudent($loggedInUserId, $studentId);
        if (!$userHasAccessToStudent) {
            throw new AccessDeniedException();
        }
        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        // Get the survey blocks for all permission sets connecting this faculty member and this student.
        $surveyBlocksForFacultyAndStudent = $this->surveyPermissionService->getSurveyBlocksByFacultyStudent($loggedInUserId, $studentId);

        // Get a list of topics for which the user has access to the representative factor/question.  These are the topics for which we'll be able to display a color.
        $topicsWithAccessibleRepresentatives = $this->successMarkerTopicRepository->getTopicsWhichHaveAccessibleRepresentatives($surveyBlocksForFacultyAndStudent);

        // Get data about the success markers, topics, and colors to be displayed.
        $successMarkerAndTopicRecords = $this->successMarkerRepository->getSuccessMarkersAndTopicsForStudent($studentId, $surveyId, $successMarkerId);

        // Remove all topics for which the user shouldn't be able to see the associated color.
        $accessibleSuccessMarkerAndTopicRecords = [];

        foreach ($successMarkerAndTopicRecords as $record) {
            if (in_array($record['topic_id'], $topicsWithAccessibleRepresentatives)) {
                $accessibleSuccessMarkerAndTopicRecords[] = $record;
            }
        }

        // Group the data by success marker, as a first pass at formatting the data as desired.
        $accessibleDataGroupedBySuccessMarker = [];

        foreach ($accessibleSuccessMarkerAndTopicRecords as $record) {
            $accessibleDataGroupedBySuccessMarker[$record['success_marker_id']][] = $record;
        }

        // Format the data, with separate cases depending on whether we're returning a single success marker or all of them.
        $successMarkers = [];

        if (empty($successMarkerId)) {
            // Get a list of all possible success markers (even ones for which we don't have any associated topics or color data for the student).
            // Then, where we do have such data, substitute it into this "skeleton."
            // This will allow us to still display success markers where all topics below them are represented as gray.
            $allSuccessMarkers = $this->successMarkerRepository->getSuccessMarkers();

            foreach ($allSuccessMarkers as $successMarkerId => &$successMarkerData) {
                if (isset($accessibleDataGroupedBySuccessMarker[$successMarkerId])) {
                    $successMarkerData = $accessibleDataGroupedBySuccessMarker[$successMarkerId];
                }
            }

            // Format the data as desired, and add gray topics at the bottom of each success marker where appropriate.
            foreach ($allSuccessMarkers as $successMarkerId => $recordsForSuccessMarker) {
                $formattedData = $this->formatDataForSuccessMarkerAndAddGrays($recordsForSuccessMarker, $studentId, $surveyId, $surveyBlocksForFacultyAndStudent);
                if (!empty($formattedData)) {
                    $successMarkers[] = $formattedData;
                }
            }

        } else {
            // If a single success marker is requested, we similarly need to address the case where no topics have colors.
            if (empty($accessibleDataGroupedBySuccessMarker)) {
                $successMarker = $this->successMarkerRepository->find($successMarkerId);
                $accessibleDataGroupedBySuccessMarker[$successMarkerId][] = [
                    'success_marker_id' => $successMarkerId,
                    'success_marker_name' => $successMarker->getName()
                ];
            }
            $formattedData = $this->formatDataForSuccessMarkerAndAddGrays($accessibleDataGroupedBySuccessMarker[$successMarkerId], $studentId, $surveyId, $surveyBlocksForFacultyAndStudent);
            if (!empty($formattedData)) {
                $successMarkers[] = $formattedData;
            }
        }

        $dataToReturn = [];
        $dataToReturn['survey_id'] = $surveyId;
        $dataToReturn['survey_name'] = $this->surveyLangRepository->findOneBy(['survey' => $surveyId])->getName();
        $dataToReturn['success_markers'] = $successMarkers;

        return $dataToReturn;
    }


    /**
     * Given the data $recordsForSuccessMarker, puts this data into the desired format for a single success marker.
     * Using permissions and data availability, appends gray topics as appropriate.
     *
     * @param array $recordsForSuccessMarker -- must contain at least one record with keys "success_marker_id" and "success_marker_name"; usually has topic and color data also
     * @param int $studentId
     * @param int $surveyId
     * @param array $surveyBlocks -- from permission sets connecting the faculty and student
     * @return array
     */
    private function formatDataForSuccessMarkerAndAddGrays($recordsForSuccessMarker, $studentId, $surveyId, $surveyBlocks)
    {
        $successMarkerId = $recordsForSuccessMarker[0]['success_marker_id'];

        $topics = [];

        foreach ($recordsForSuccessMarker as $record) {
            if (isset($record['topic_id'])) {
                $topics[] = [
                    'topic_id' => $record['topic_id'],
                    'topic_name' => $record['topic_name'],
                    'color' => $record['color']
                ];
            }
        }

        // Get a list of topics for which the user has access to any associated factor/question.
        // Use this, along with information about topics already in the list and data availability, to add topics with gray icons.
        $accessibleTopics = $this->successMarkerTopicRepository->getAccessibleTopicsInSuccessMarker($successMarkerId, $surveyBlocks);

        $topicsWhichHaveColors = array_column($topics, 'topic_id');

        foreach ($accessibleTopics as $accessibleTopic) {
            if (!in_array($accessibleTopic, $topicsWhichHaveColors)) {
                $responsesAvailable = $this->successMarkerTopicDetailRepository->responsesAreAvailableInDrilldown($studentId, $surveyId, $accessibleTopic, $surveyBlocks);
                if ($responsesAvailable) {
                    $topics[] = [
                        'topic_id' => $accessibleTopic,
                        'topic_name' => $this->successMarkerTopicRepository->find($accessibleTopic)->getName(),
                        'color' => 'gray'
                    ];
                }
            }
        }

        if (!empty($topics)) {
            $formattedData = [
                'success_marker_id' => $successMarkerId,
                'success_marker_name' => $recordsForSuccessMarker[0]['success_marker_name'],
                'color' => $this->calculateSuccessMarkerColor($recordsForSuccessMarker),
                'topics' => $topics
            ];
        } else {
            $formattedData = [];
        }

        return $formattedData;
    }


    /**
     * Given records for the topics under a success marker, calculates the color of the success marker
     * as an average of the colors of the topics.
     * Returns 'gray' if the given success marker is one where needs_color_calculated is false.
     *
     * @param array $topicRecords - each record must be an associative array with at least the keys 'success_marker_id'
     *                              and 'base_value' (the number corresponding to its color)
     * @return string
     */
    private function calculateSuccessMarkerColor($topicRecords)
    {
        $needsColorCalculated = $this->successMarkerRepository->find($topicRecords[0]['success_marker_id'])->getNeedsColorCalculated();

        if ($needsColorCalculated) {
            $average = array_sum(array_column($topicRecords, 'base_value')) / count($topicRecords);
            $color = $this->successMarkerColorRepository->getSuccessMarkerColor($average);
        } else {
            $color = 'gray';
        }

        return $color;
    }


    /**
     * Returns the data for the drilldown on a single topic on the Student Survey Dashboard.
     *
     * @param int $studentId
     * @param int $loggedInUserId
     * @param int $surveyId
     * @param int $successMarkerId
     * @param int $topicId
     * @return array
     * @throws AccessDeniedException
     */
    public function getStudentSurveyDrilldown($studentId, $loggedInUserId, $surveyId, $successMarkerId, $topicId)
    {
        // Check that the user has individual access to the student.
        $userHasAccessToStudent = $this->orgPermissionsetRepository->checkAccessToStudent($loggedInUserId, $studentId);
        if (!$userHasAccessToStudent) {
            throw new AccessDeniedException();
        }
        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        // Check that the provided $topicId belongs to the provided $successMarkerId.
        $successMarkerAssociatedWithTopic = $this->successMarkerTopicRepository->find($topicId)->getSuccessMarker()->getId();
        if ($successMarkerAssociatedWithTopic != $successMarkerId) {
            throw new ValidationException(['This topic is not associated with this success marker.'], 'This topic is not associated with this success marker.');
        }

        // Get the survey blocks for all permission sets connecting this faculty member and this student.
        $surveyBlocksForFacultyAndStudent = $this->surveyPermissionService->getSurveyBlocksByFacultyStudent($loggedInUserId, $studentId);

        // Get the required data; then remove any factors and questions the user does not have permission to see.
        $factorData = $this->successMarkerTopicDetailRepository->getSuccessMarkerTopicDetailsFromFactors($studentId, $topicId, $surveyId);

        $questionData = $this->successMarkerTopicDetailRepository->getSuccessMarkerTopicDetailsFromQuestions($studentId, $topicId, $surveyId);

        $this->removeUnauthorizedFactorsAndQuestions($surveyBlocksForFacultyAndStudent, $factorData, $questionData);

        // Format the data as desired, including putting the questions associated with a factor under that factor.
        $unassociatedQuestions = [];

        foreach ($questionData as $index => $question) {
            if (!empty($question['associated_factor_id'])) {
                $key = array_search($question['associated_factor_id'], array_column($factorData, 'factor_id'));
                if ($key !== false) {
                    unset($question['associated_factor_id']);
                    $factorData[$key]['questions'][] = $question;
                } else {
                    $unassociatedQuestions[] = $question;
                }
            } else {
                $unassociatedQuestions[] = $question;
            }
        }

        $dataToReturn = [];
        $dataToReturn['survey_id'] = $surveyId;
        $dataToReturn['survey_name'] = $this->surveyLangRepository->findOneBy(['survey' => $surveyId])->getName();
        $dataToReturn['success_marker_id'] = $successMarkerId;
        $dataToReturn['success_marker_name'] = $this->successMarkerRepository->find($successMarkerId)->getName();
        $dataToReturn['topic_id'] = $topicId;
        $dataToReturn['topic_name'] = $this->successMarkerTopicRepository->find($topicId)->getName();

        if (!empty($factorData)) {
            $dataToReturn['factors'] = $factorData;
        }

        if (!empty($unassociatedQuestions)) {
            $dataToReturn['questions'] = $unassociatedQuestions;
        }

        return $dataToReturn;
    }


    /**
     * Given arrays of factors and questions (possibly including success marker data), removes the ones the user
     * does not have permission to see (i.e., which are not in the given survey block array).
     *
     * @param array $surveyBlocks
     * @param array $factors
     * @param array $questions
     */
    public function removeUnauthorizedFactorsAndQuestions($surveyBlocks, &$factors, &$questions)
    {
        // Get all questions and factors the user has permission to see.
        $accessibleFactors = $this->datablockQuestionsRepository->getAccessibleFactorsOrQuestions($surveyBlocks, 'factor');
        $accessibleQuestions = $this->datablockQuestionsRepository->getAccessibleFactorsOrQuestions($surveyBlocks, 'question');

        // Remove from the passed-in arrays the factors/questions that the user should not be allowed to see.
        foreach ($factors as $key => $factor) {
            if (! in_array($factor['factor_id'], $accessibleFactors)) {
                unset($factors[$key]);
            }
        }

        foreach ($questions as $key => $question) {
            if (! in_array($question['ebi_question_id'], $accessibleQuestions)) {
                unset($questions[$key]);
            }
        }
    }


    /**
     * Returns the three free response questions and responses ("Student Comments") for the success marker page,
     * provided the student has answered them and the user has permission to see them.
     *
     * @param int $studentId
     * @param int $loggedInUserId
     * @param int $surveyId
     * @return array
     * @throws AccessDeniedException
     */
    public function listFreeResponseQuestionsAndResponses($studentId, $loggedInUserId, $surveyId)
    {
        // Check that the user has individual access to the student.
        $userHasAccessToStudent = $this->orgPermissionsetRepository->checkAccessToStudent($loggedInUserId, $studentId);
        if (!$userHasAccessToStudent) {
            throw new AccessDeniedException();
        }

        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        // Get the survey blocks for all permission sets connecting this faculty member and this student.
        $surveyBlocksForFacultyAndStudent = $this->surveyPermissionService->getSurveyBlocksByFacultyStudent($loggedInUserId, $studentId);

        // Get free response items for this student on this survey, provided they exist and the user has access to them.
        $freeResponseItems = $this->surveyResponseRepository->getFreeResponsesForSuccessMarkerPage($studentId, $surveyId, $surveyBlocksForFacultyAndStudent);

        $dataToReturn = [];
        $dataToReturn['survey_id'] = $surveyId;
        $dataToReturn['survey_name'] = $this->surveyLangRepository->findOneBy(['survey' => $surveyId])->getName();
        $dataToReturn['free_response_items'] = $freeResponseItems;

        return $dataToReturn;
    }

}
