<?php
namespace Synapse\SurveyBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Repository\DatablockQuestionsRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Repository\SurveyLangRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\UserManagementService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\Util\Helper;
use Synapse\ReportsBundle\Repository\OrgCalcFlagsStudentReportsRepository;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SurveyBundle\EntityDto\StudentSurveyDetailsDto;
use Synapse\SurveyBundle\Repository\OrgPersonStudentSurveyLinkRepository;

/**
 * @DI\Service("studentsurvey_service")
 */
class StudentSurveyService extends AbstractService
{

    const SERVICE_KEY = 'studentsurvey_service';

    const SURVEY_ID = 'survey_id';

    const SURVEY_NAME = 'survey_name';

    const SURVEY_NOT_FOUND = 'Survey Not Found.';

    const SURVEY_NOT_FOUND_KEY = 'survey_not_found.';

    const SURVEY_QUE_ID = 'survey_que_id';

    const QUESTION_TEXT = 'question_text';

    const RESPONSE_TYPE = 'response_type';

    const DECIMAL = 'decimal';

    const DECIMAL_VALUE = 'decimalValue';

    const DECIMAL_VAL = 'decimal_value';

    const CHAR = 'char';

    const COHORT_CODE = 'cohort_code';

    const ID = 'id';

    const OPEN_DATE = 'open_date';

    const STATUS = 'status';

    const CLOSE_DATE = 'close_date';

    const WESS_LINK = 'wess_admin_link';

    const SURVEY = 'survey';

    const INVALID_STUDENT_ID = "Invalid Student Id";

    const PERSON = 'person';

    const INVALID_LIST_TYPE = "Invalid List Type";

    //Scaffolding
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Manager
     */
    public $rbacManager;

    /**
     * @var RepositoryResolver
     */
    protected $repositoryResolver;

    //Services
    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var SurveyPermissionService
     */
    private $surveyPermissionService;

    /**
     * @var UserManagementService
     */
    private $userManagementService;

    //Repositories
    /**
     * @var DatablockQuestionsRepository
     */
    private $datablockQuestionsRepository;

    /**
     * @var OrgCalcFlagsStudentReportsRepository
     */
    private $orgCalcFlagsStudentReportsRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgPersonStudentSurveyLinkRepository
     */
    private $orgPersonStudentSurveyLinkRepository;

    /**
     * @var SurveyLangRepository
     */
    private $surveyLangRepository;


    /**
     * Student survey service construct
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
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->surveyPermissionService = $this->container->get(SurveyPermissionService::SERVICE_KEY);
        $this->userManagementService = $this->container->get(UserManagementService::SERVICE_KEY);

        // Repositories
        $this->datablockQuestionsRepository = $this->repositoryResolver->getRepository(DatablockQuestionsRepository::REPOSITORY_KEY);
        $this->orgCalcFlagsStudentReportsRepository = $this->repositoryResolver->getRepository(OrgCalcFlagsStudentReportsRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPersonStudentSurveyLinkRepository = $this->repositoryResolver->getRepository(OrgPersonStudentSurveyLinkRepository::REPOSITORY_KEY);
        $this->surveyLangRepository = $this->repositoryResolver->getRepository(SurveyLangRepository::REPOSITORY_KEY);
    }


    /**
     * Lists all surveys that the student has been assigned, along with lots of metadata,
     * such as survey status and whether the student has responded.
     * Throws an exception if the user is not the student and should not have individual access to the student.
     *
     * @param int $studentId
     * @param int $loggedInUserId
     * @param int $orgId
     * @param int $orgAcademicYearId
     * @param bool $hasResponses
     * @return array $studentSurveyDetails
     * @throws AccessDeniedException
     */
    public function listSurveysForStudent($studentId, $loggedInUserId, $orgId, $orgAcademicYearId, $hasResponses)
    {
        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);
        // Check that the user either is the student (and is active) or has individual access to the student.
        if ($studentId == $loggedInUserId) {
            $studentIsActive = $this->userManagementService->isStudentActive($studentId, $orgId);
            if (!$studentIsActive) {
                throw new AccessDeniedException();
            }
        } else {
            $userHasAccessToStudent = $this->orgPermissionsetRepository->checkAccessToStudent($loggedInUserId, $studentId);
            if (!$userHasAccessToStudent) {
                throw new AccessDeniedException();
            }
        }

        // Get the survey data.
        $surveys = $this->orgPersonStudentSurveyLinkRepository->listSurveysForStudent($studentId, $orgId, $orgAcademicYearId, $hasResponses);

        // Reformat the data.
        foreach ($surveys as &$survey) {
            if ($survey['has_responses'] == 'Yes') {
                $survey['has_responses'] = true;
            } else {
                $survey['has_responses'] = false;
            }

            $survey['open_date'] = $this->dateUtilityService->convertDatabaseStringToISOString($survey['open_date']);
            $survey['close_date'] = $this->dateUtilityService->convertDatabaseStringToISOString($survey['close_date']);
            $survey['survey_completion_date'] = $this->dateUtilityService->convertDatabaseStringToISOString($survey['survey_completion_date']);
        }

        $studentSurveyDetails = [];
        $studentSurveyDetails['student_id'] = $studentId;
        $studentSurveyDetails['organization_id'] = $orgId;
        $studentSurveyDetails['surveys'] = $surveys;

        return $studentSurveyDetails;
    }


    /**
     * Returns the name of the pdf file for the student survey report for the given student and survey.
     * Throws an exception if the user is not the student and should not have individual access to the student.
     * Returns the string "NoAccessToReport.pdf" if the user doesn't have access to all the survey blocks needed
     * to see the data in the report.
     *
     * @param int $studentId
     * @param int $loggedInUserId
     * @param int $surveyId
     * @return array
     * @throws AccessDeniedException
     */
    public function getStudentSurveyReport($studentId, $loggedInUserId, $surveyId)
    {
        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        // Check that the user either is the student (and is active) or has individual access to the student.
        if ($studentId == $loggedInUserId) {
            $studentIsActive = $this->userManagementService->isStudentActive($studentId);
            if (!$studentIsActive) {
                throw new AccessDeniedException();
            }
        } else {
            $userHasAccessToStudent = $this->orgPermissionsetRepository->checkAccessToStudent($loggedInUserId, $studentId);
            if (!$userHasAccessToStudent) {
                throw new AccessDeniedException();
            }

            // Check whether the user should have access to the data in the report.
            $surveyBlocksForFacultyAndStudent = $this->surveyPermissionService->getSurveyBlocksByFacultyStudent($loggedInUserId, $studentId);

            $surveyBlocksNeededToAccessReport = $this->datablockQuestionsRepository->getDatablocksNeededToViewStudentSurveyReport();

            if (!empty(array_diff($surveyBlocksNeededToAccessReport, $surveyBlocksForFacultyAndStudent))) {
                $reportPdf = 'NoAccessToReport.pdf';
            }
        }

        // If the user should have access to the report, get the name of the report.
        if (empty($reportPdf)) {
            $reportPdf = $this->orgCalcFlagsStudentReportsRepository->getLastStudentReportGeneratedPdfName($surveyId, $studentId);
        }

        $dataToReturn = [];
        $dataToReturn['survey_id'] = $surveyId;
        $dataToReturn['survey_name'] = $this->surveyLangRepository->findOneBy(['survey' => $surveyId])->getName();
        $dataToReturn['report_pdf'] = $reportPdf;

        return $dataToReturn;
    }


    // TODO: Remove this function when the /surveys/deprecated API is removed.
    public function listStudentsSurveysData($studentId, $listType, $lang = 1)
    {
        $this->logger->debug(" List Students Surveys Data for Student Id " . $studentId . "List Type" . $listType . "Lang" . $lang);
        $this->personRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Person');
        $this->orgPersonStudentRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgPersonStudent");
        $person = $this->personRepository->findOneById($studentId);
        if (! $person) {
            throw new ValidationException([
                SELF::INVALID_STUDENT_ID
            ], SELF::INVALID_STUDENT_ID, SELF::INVALID_STUDENT_ID);
        } else {
            $orgStudent = $this->orgPersonStudentRepo->findBy(array(
                SELF::PERSON => $person
            ));
            if ($listType == "list") {
                $surveys = $this->getSurveyList($orgStudent, $studentId, $lang);
            }elseif($listType == "report"){
                $surveys = $this->getSurveyListClosed($orgStudent, $studentId, $lang);
            } else {
                $this->logger->error("Student Survey - List Student Survey Data - ".SELF::INVALID_LIST_TYPE);
                throw new ValidationException([
                    SELF::INVALID_LIST_TYPE
                ], SELF::INVALID_LIST_TYPE, SELF::INVALID_LIST_TYPE);
            }
            $this->logger->info(" List Students Surveys Data for Student Id and List Type");
            return $surveys;
        }
    }


    // TODO: Remove this function when the /surveys/deprecated API is removed.
    private function getSurveyList($orgStudent, $studentId, $lang)
    {
        $this->orgLangRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrganizationLang");
        $this->wessLinkRepository = $this->repositoryResolver->getRepository('SynapseSurveyBundle:WessLink');        
        $surveys = array();
        // ESPRJ-6539
        $aqw = '';
        
        $currentDate = new \DateTime('now');
        foreach ($orgStudent as $student) {
            $orgId = $student->getOrganization()->getId();
            $orgLangObj = $this->orgLangRepo->findOneBy(array(
                'organization' => $orgId,
                'lang' => $lang
            ));
            if ($orgLangObj) {
                $orgName = $orgLangObj->getOrganizationName();
            } else {
                $orgName = "";
            }
            $timeZone = $student->getOrganization()->getTimeZone();
             /*
             * commented below code since we need to consider cohortCode from 
             * org_person_student_survey_link table $cohortCode = $student->getSurveyCohort();
             */
            $cohortCode = $this->container->get('util_service')->getCohotCodesForStudent($studentId);
			if($cohortCode)
			{
				$totalSurveyList = $this->wessLinkRepository->getStudentSurveyList($cohortCode, $orgId, true, $studentId);                                        
                foreach ($totalSurveyList as $survey) { 
                    $surveyDetails = new StudentSurveyDetailsDto();
					$surveyDetails->setSurveyId($survey[self::SURVEY_ID]);
					$surveyDetails->setCampusName($orgName);
					$surveyDetails->setSurveyName($survey[self::SURVEY_NAME]);
					$date = new \DateTime($survey[self::CLOSE_DATE]);
					$date->setTimezone(new \DateTimeZone('UTC'));
					//Helper::setOrganizationDate($date, $timeZone);				
					$surveyDetails->setSurveyLastDate($date);
                    //ESPRJ-6537 reviewed
                    if($survey['status']){
                        if($survey['status'] == 'Assigned' && $date >= $currentDate){
                            $surveyDetails->setStatus('OPEN');
                        }
                        if(
                            $survey['status'] == 'CompletedMandatory' && $date >= $currentDate
                        ){
                            $surveyDetails->setStatus('INCOMPLETE');
                        }
                        if( 
                            $survey['status'] == 'CompletedAll'
                        ){
                            $surveyDetails->setStatus('COMPLETE');
                        }
                        
                        if(($survey['status'] == 'Assigned' || $survey['status'] == 'CompletedMandatory') && $date < $currentDate){
                            
                            $surveyDetails->setStatus('NOT RESPONDED');
                        }
                    } 
                    else {
                        $surveyDetails->setStatus('INCOMPLETE');
                    }
                    $surveyDetails->setSurveyUrl($survey['survey_link']);
                /*
					if ($survey[self::STATUS] == 'Assigned' || $survey[self::STATUS] == '') {
						$surveyDetails->setStatus('OPEN');
					} else {
						$surveyDetails->setStatus('INCOMPLETE');
					}
                */
					
					$surveys[] = $surveyDetails;
				}
			}
        }
        return $surveys;
    }


    // TODO: Remove this function when the /surveys/deprecated API is removed.
    public function getSurveyUrl($cohortCode, $studentId, $academicYearId, $surveyId)
    {
        $this->orgSurveyLink = $this->repositoryResolver->getRepository('SynapseSurveyBundle:OrgPersonStudentSurveyLink');
        $surveyLink = $this->orgSurveyLink->findOneBy(array(
            self::PERSON => $studentId,
            'orgAcademicYear' => $academicYearId,
            'cohort' => $cohortCode, 
			'survey' => $surveyId
        ));
        if ($surveyLink) {
            return $surveyLink->getSurveyLink();
        } else {
            return "";
        }
    }


    // TODO: Remove this function when the /surveys/deprecated API is removed.
    public function getSurveyListClosed($orgStudent, $studentId, $lang)
    {
        $this->orgLangRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrganizationLang");
        $this->academicYear = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgAcademicYear');
        $this->orgPersonStudentSurveyLink = $this->repositoryResolver->getRepository('SynapseSurveyBundle:OrgPersonStudentSurveyLink');
    
        $surveys = array();
        foreach ($orgStudent as $student) {
            $orgId = $student->getOrganization()->getId();
            $orgLangObj = $this->orgLangRepo->findOneBy(array(
                'organization' => $orgId,
                'lang' => $lang
            ));
            if ($orgLangObj) {
                $orgName = $orgLangObj->getOrganizationName();
            } else {
                $orgName = "";
            }
            $timeZone = $student->getOrganization()->getTimeZone();
            $totalSurveyList = $this->orgPersonStudentSurveyLink->getStudentSurveysByOrgId($orgId, $studentId);
    
            $date = new \DateTime('now');
            $date = $date->format('Y-m-d');
            
            /*
             * deleted the below line as we need to consider the cohort from org_person_student_survey_link 
             * -- $cohortCode = $student->getSurveyCohort(); 
            */
            
            $academicYearDetails = $this->academicYear->getCurrentAcademicDetails($date, $orgId);
            
            // ESPRJ-6539 reviewed
            $aqw = '';
            foreach ($totalSurveyList as $survey) {
                $surveyUrl = $this->getSurveyUrl($survey['cohort'], $studentId, $academicYearDetails[0]['id'], $survey['id']);
                $surveyDetails = new StudentSurveyDetailsDto();
                $surveyDetails->setSurveyId($survey['id']);
                $surveyDetails->setCampusName($orgName);
                $surveyDetails->setSurveyName($survey[self::SURVEY_NAME]);
                Helper::setOrganizationDate($survey[self::CLOSE_DATE], $timeZone);
                $surveyDetails->setSurveyLastDate($survey[self::CLOSE_DATE]);
                $surveyDetails->setYear($academicYearDetails[0]['yearId']);
                $surveyDetails->setCohort($survey['cohort']);
                //$surveyDetails->setStatus('CLOSED');  Commented out until we can get the correct status in here.  We can't have it say complete/closed when it is not.

                $surveyDetails->setSurveyUrl($surveyUrl);
    
                $this->orgCalcFlagsStudentReportsRepository = $this->repositoryResolver->getRepository("SynapseReportsBundle:OrgCalcFlagsStudentReports");
                $pdfFilePath = $this->orgCalcFlagsStudentReportsRepository->getLastStudentReportGeneratedPdfName($survey['id'], $studentId);
                $surveyDetails->setReportPdf($pdfFilePath);
                // ESPRJ-6539
                if($survey['status']){
                    if($survey['status'] == 'Assigned'){$aqw = 'Not responded';}
                    if(
                        ($survey['status'] == 'CompletedMandatory' || 
                        $survey['status'] == 'CompletedAll') && $pdfFilePath == 'NoReportFound.pdf'
                    ){
                        $aqw = 'Survey in Progress';
                    }
                    if(
                        ($survey['status'] == 'CompletedMandatory' || 
                        $survey['status'] == 'CompletedAll') && $pdfFilePath != 'NoReportFound.pdf'
                    ){
                        $aqw = 'Ready to View';
                    }
                } 
                else {
                    $aqw = 'Not responded';
                }

                $surveyDetails->setStatus($aqw);
                $surveys[] = $surveyDetails;
            }
        }
        return $surveys;
    }
}