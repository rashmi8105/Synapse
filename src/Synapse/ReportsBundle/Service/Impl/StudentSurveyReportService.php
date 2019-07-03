<?php
namespace Synapse\ReportsBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Process\Process;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CampusConnectionBundle\Service\Impl\CampusConnectionService;
use Synapse\CampusResourceBundle\Service\Impl\CampusResourceService;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\EntityDto\ElementDto;
use Synapse\ReportsBundle\EntityDto\ReportDto;
use Synapse\ReportsBundle\EntityDto\ReportSectionsDto;
use Synapse\ReportsBundle\EntityDto\StudentReportDto;
use Synapse\ReportsBundle\EntityDto\StudentSurveyInfoDto;
use Synapse\ReportsBundle\EntityDto\SurveyScoreDto;
use Synapse\ReportsBundle\EntityDto\TakingActionDto;
use Synapse\ReportsBundle\EntityDto\TipsDto;
use Synapse\ReportsBundle\ReportsBundleConstant;
use Synapse\ReportsBundle\Repository\OrgCalcFlagsStudentReportsRepository;
use Synapse\ReportsBundle\Repository\ReportCalculatedValuesRepository;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportTipsRepository;
use Synapse\SurveyBundle\Repository\OrgPersonStudentSurveyLinkRepository;

/**
 * @DI\Service("student_survey_report_service")
 */
class StudentSurveyReportService extends AbstractService
{

    const SERVICE_KEY = 'student_survey_report_service';

    // Scaffolding
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
     * @var CampusConnectionService
     */
    private $campusConnectionService;

    /**
     * @var CampusResourceService
     */
    private $campusResourceService;

    /**
     * @var dataProcessingUtilityService
     */
    private $dataProcessingUtilityService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var ReportsService
     */
    private $reportsService;

    /**
     * @var TokenService
     */
    private $tokenService;

    // Repositories
    /**
     * @var OrganizationlangRepository
     */
    private $organizationlangRepository;

    /**
     * @var OrgCalcFlagsStudentReportsRepository
     */
    private $orgCalcFlagsStudentReportsRepository;

    /**
     * @var OrgPersonStudentSurveyLinkRepository
     */
    private $orgPersonStudentSurveyLinkRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var ReportCalculatedValuesRepository
     */
    private $reportCalculatedValuesRepository;

    /**
     * @var ReportsRepository
     */
    private $reportsRepository;

    /**
     * @var ReportTipsRepository
     */
    private $reportTipsRepository;

    /**
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->campusConnectionService =  $this->container->get(CampusConnectionService::SERVICE_KEY);
        $this->campusResourceService = $this->container->get(CampusResourceService::SERVICE_KEY);
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->reportsService = $this->container->get(ReportsService::SERVICE_KEY);
        $this->tokenService = $this->container->get(TokenService::SERVICE_KEY);

        // Repositories
        $this->organizationlangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->orgCalcFlagsStudentReportsRepository = $this->repositoryResolver->getRepository(OrgCalcFlagsStudentReportsRepository::REPOSITORY_KEY);
        $this->orgPersonStudentSurveyLinkRepository = $this->repositoryResolver->getRepository(OrgPersonStudentSurveyLinkRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->reportCalculatedValuesRepository = $this->repositoryResolver->getRepository(ReportCalculatedValuesRepository::REPOSITORY_KEY);
        $this->reportsRepository = $this->repositoryResolver->getRepository(ReportsRepository::REPOSITORY_KEY);
        $this->reportTipsRepository = $this->repositoryResolver->getRepository(ReportTipsRepository::REPOSITORY_KEY);

    }

    /**
     * Generates the student survey report PDF.
     *
     * @param int $totalServers
     * @param int $thisServer
     * @return array
     */
    public function generateStudentSurveyReport($totalServers, $thisServer)
    {
        $studentList = $this->orgCalcFlagsStudentReportsRepository->getStudentsWithCalculatedReportAndNoPDF();

        $serverStudentList = [];
        $successFailureResultsArray = [];
        $queuedStudentReports = count($studentList);
        $successFailureResultsArray['reports_queued_for_generation'] = $queuedStudentReports;
        $successFailureResultsArray['successful_PDF_count'] = 0;
        $successFailureResultsArray['failed_PDF_count'] = 0;
        $successFailureResultsArray['message'] = '';
        if (!empty($studentList)) {
            //allocating students to servers
            foreach ($studentList as $student) {
                $studentId = $student['student_id'];
                //Determine if this job on this server should be processing the student
                if ($studentId % $totalServers === $thisServer - 1) {
                    $serverStudentList[] = $student;
                }
            }

            foreach ($serverStudentList as $student) {
                $studentId = $student['student_id'];
                $surveyId = $student['survey_id'];
                
                $resultsArray = $this->generateStudentSurveyReportPDF($studentId, $surveyId);

                if (!$resultsArray['success']) {
                    $resultsArray['processing_end_time'] = date(SynapseConstant::DEFAULT_DATETIME_FORMAT);
                    $successFailureResultsArray['PDF_generation_errors'][] = $resultsArray;
                    $successFailureResultsArray['failed_PDF_count']++;
                } else {
                    $successFailureResultsArray['successful_PDF_count']++;
                    $successFailureResultsArray['student_ids_with_successful_pdf_generation'][] = $studentId;
                }

            }
        } else {
            $successFailureResultsArray['message'] = 'There are currently no students who have reports that qualify for pdf generation.';
            return $successFailureResultsArray;
        }

        if ($successFailureResultsArray['failed_PDF_count'] == 0 && $successFailureResultsArray['successful_PDF_count'] > 0) {
            $successFailureResultsArray['message'] = $successFailureResultsArray['successful_PDF_count'] . ' PDF(s) generated correctly.  No Errors were reported';
        } else if ($successFailureResultsArray['failed_PDF_count'] > 0 && $successFailureResultsArray['successful_PDF_count'] > 0) {
            $successFailureResultsArray['message'] = $successFailureResultsArray['successful_PDF_count'] . ' PDF(s) generated correctly. ' . $successFailureResultsArray['failed_PDF_count'] . ' PDF(s) failed';
        } else {
            $successFailureResultsArray['message'] = $successFailureResultsArray['failed_PDF_count'].  ' PDF(s) failed to generate.  All attempts at generation failed.';
        }

        return $successFailureResultsArray;
    }

    /**
     *  Generates the student survey report PDF.
     *
     * @param int $personId
     * @param int $surveyId
     * @param float $zoom
     * @return array
     */
    public function generateStudentSurveyReportPDF($personId, $surveyId, $zoom = ReportsBundleConstant::PDF_ZOOM)
    {
        $token = $this->tokenService->generateToken($personId)->getToken();
        $person = $this->personRepository->find($personId);
        $organizationId = $person->getOrganization()->getId();
        $pageUrl = '#/reports/student-survey-report?access_token=' . $token;



        //No organization should be handed to getSystemUrl as this is NOT used with LDAP/SAML and does not require special routing
        //The default parameters should get the unaltered skyfactor base system URL
        $systemUrl = $this->ebiConfigService->getSystemUrl();
        $pdfUrl = $systemUrl . $pageUrl;
        $fileName = $organizationId . '-' . $personId . '-' . $surveyId . '-student-report';
        $fileName = md5($fileName) . '.pdf';

        $temporaryFile = '/tmp/' . $fileName;

        
        $pdfInverse = ReportsBundleConstant::PDF_INVERSE;
        $pdfDpi = ReportsBundleConstant::PDF_DPI;

        $process = new Process(ReportsBundleConstant::PHANTOM_JS_PATH.
            $this->container->getParameter('kernel.root_dir') . ReportsBundleConstant::PDFIFY_JS .
            "'$pdfUrl' $temporaryFile $pdfInverse $pdfDpi $zoom"
        );
        $error = $process->run();

        if ($error != 0) {
            $responseArray['results'] = 'PhantomJS experienced a problem and generated ERROR CODE: ' . $error;
            $responseArray['success'] = false;
            return $responseArray;
        }

        $contents = file_get_contents($temporaryFile);

        $contentSize = strlen($contents);
        $responseArray['person_id'] = $personId;
        $responseArray['file_name'] = $fileName;
        $responseArray['size'] = $contentSize;
        if ($contentSize > ReportsBundleConstant::MINIMUM_STUDENT_SURVEY_REPORT_BYTE_SIZE) {
            file_put_contents("data://student_reports_uploads/" . $fileName, $contents);
            $this->updateOrgCalcFlagsStudentReportFilePath($personId, $surveyId, $fileName);
            unlink($temporaryFile);
            $responseArray['success'] = true;
        } else {
            $responseArray['results'] = ReportsBundleConstant::GENERATED_REPORT_TOO_SMALL_MESSAGE;
            $responseArray['success'] = false;
        }

        return $responseArray;

    }

    /**
     * Update report file path for the existing student report id
     *
     * @param Int $personId
     * @param Int $surveyId
     * @param String $fileName
     * @return bool
     */
    public function updateOrgCalcFlagsStudentReportFilePath($personId, $surveyId, $fileName)
    {
        $studentReportIdArray = $this->orgCalcFlagsStudentReportsRepository->getStudentReportId($personId, $surveyId);
        if (count($studentReportIdArray) > 0) {
            $updateSet = $this->orgCalcFlagsStudentReportsRepository->find($studentReportIdArray[0]['calculated_student_report_id']);
            $updateSet->setId($studentReportIdArray[0]['calculated_student_report_id']);
            $updateSet->setFileName($fileName);
            $this->orgCalcFlagsStudentReportsRepository->flush();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get student survey report data
     *
     * @param Int $organizationId
     * @param Int $studentId
     * @param String $reportType --> 'student-report'. Type of report being generated.
     * @return ReportDto
     * @throws SynapseValidationException
     */
    public function getStudentReport($organizationId, $studentId, $reportType)
    {
        $studentReports = $this->reportsRepository->findOneBy(array(
            'name' => $reportType
        ));

        if (!$studentReports) {
            throw new SynapseValidationException("Report type $reportType not found.");
        }
        $reportId = $studentReports->getId();
        $reportDTO = new ReportDto();
        $reportDTO->setReportId($reportId);
        $reportDTO->setReportInstanceId($studentReports->getName());

        $studentDetails = $this->personRepository->getUsersByUserIds($studentId)[0];


        if (empty($studentDetails)) {
            throw new SynapseValidationException("Person ID Not Found.");
        }


        $studentInfo = array(
            'student_id' => $studentDetails['user_id'],
            'student_first_name' => $studentDetails['user_firstname'],
            'student_last_name' => $studentDetails['user_lastname'],
            'student_email_id' => $studentDetails['user_email']
        );

        $organization = $this->organizationlangRepository->findOneBy([
            'organization' => $organizationId
        ]);
        if (!$organization) {
            throw new SynapseValidationException("Organization ID Not Found.");
        }

        $calculatedAtDate = $this->orgCalcFlagsStudentReportsRepository->getLastCalculatedAtDateForStudent($studentInfo['student_id']);
        if ($calculatedAtDate) {
            $reportDTO->setGeneratedOn($calculatedAtDate);

        } else {
            $reportDTO->setGeneratedOn(null);

        }




        $organizationObject = $organization->getOrganization();

        $organizationInformation = array(
            'campus_id' => $organizationObject->getId(),
            'campus_name' => $organization->getOrganizationName(),
            'campus_logo' => $organizationObject->getLogoFileName(),
            'campus_color' => $organizationObject->getPrimaryColor()
        );

        $studentReportDTO = new StudentReportDto();
        $studentReportDTO->setStudentInfo($studentInfo);
        $studentReportDTO->setCampusInfo($organizationInformation);

        $surveyBlocksResponse = array();

        $orgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId, true);
        $studentSurveys = $this->orgPersonStudentSurveyLinkRepository->listSurveysForStudent($studentId, $organizationId, $orgAcademicYearId, true);
        if (!empty($studentSurveys)) {
            foreach ($studentSurveys as $studentSurvey) {
                $studentSurveyInfoDTO = new StudentSurveyInfoDto();
                $studentSurveyInfoDTO->setId($studentSurvey['survey_id']);
                $studentSurveyInfoDTO->setSurveyName($studentSurvey['survey_name']);
                $studentSurveyInfoDTO->setYear($studentSurvey['year_id']);
                $studentSurveyInfoDTO->setStartDate($studentSurvey['open_date']);
                $studentSurveyInfoDTO->setSurveyStatus($studentSurvey['survey_completion_status']);
                $surveyBlocksResponse[] = $studentSurveyInfoDTO;
            }
        }

        $reportCalculatedValuesForStudent = $this->reportCalculatedValuesRepository->getReportDetailsByRepId($studentId, $reportId);
        $takingActionItem = [];
        $reportSectionsInformation = [];
        if (!empty($reportCalculatedValuesForStudent)) {

            foreach ($reportCalculatedValuesForStudent as $studentReportCalculatedValue) {

                $reportSectionsDTO = new ReportSectionsDto();
                $reportSectionsDTO->setSectionId($studentReportCalculatedValue['section_id']);
                $reportSectionsDTO->setSectionName($studentReportCalculatedValue['title']);
                $reportElementSectionDetails = $this->reportCalculatedValuesRepository->getReportElementDetailBySecId($studentReportCalculatedValue['section_id'], $reportId, $studentId);
                $elementInformation = [];
                if (!empty($reportElementSectionDetails)) {
                    $reportElementSectionDetails = $this->dataProcessingUtilityService->removeDuplicateElements($reportElementSectionDetails, 'element_name');
                    foreach ($reportElementSectionDetails as $elementDetail) {
                        $elementDTO = new ElementDto();
                        $elementDTO->setElementId($elementDetail['element_id']);
                        $elementDTO->setElementName($elementDetail['element_name']);
                        $elementDTO->setElementIcon($elementDetail['element_icon']);

                        $elementBucketDetails = $this->reportCalculatedValuesRepository->getElementBucketByElementName($studentReportCalculatedValue['section_id'], $elementDetail['element_name'], $reportId, $studentId);

                        if (!empty($elementBucketDetails)) {

                            $surveyInformation = [];
                            foreach ($elementBucketDetails as $elementBucketDetail) {
                                $color = strtolower($elementBucketDetail['element_color']);

                                if ($color == 'yellow' || $color == 'red') {
                                    $takingActionDTO = new TakingActionDto();
                                    $takingActionDTO->setElementId($elementDetail['element_id']);
                                    $takingActionDTO->setElementName($elementDetail['element_name']);
                                    $takingActionDTO->setElementIcon($elementDetail['element_icon']);
                                    $takingActionDTO->setElementColor($elementBucketDetail['element_color']);
                                    $takingActionDTO->setElementScore($elementBucketDetail['element_score']);
                                    $takingActionDTO->setElementText($elementBucketDetail['element_text']);
                                    $takingActionDTO->setSurveyId($elementBucketDetail['survey_id']);
                                    $takingActionItem[] = $takingActionDTO;
                                }

                                $surveyScoreDTO = new SurveyScoreDto();
                                $surveyScoreDTO->setSurveyId($elementBucketDetail['survey_id']);
                                $surveyScoreDTO->setElementScore($elementBucketDetail['element_score']);
                                $surveyScoreDTO->setElementColor($elementBucketDetail['element_color']);
                                $surveyScoreDTO->setElementText($elementBucketDetail['element_text']);
                                $surveyInformation[] = $surveyScoreDTO;
                                $color = NULL;
                            }
                        }
                        $elementDTO->setElementScores($surveyInformation);
                        $elementInformation[] = $elementDTO;
                    }
                }
                $reportSectionsDTO->setElements($elementInformation);
                // Get Tips for Section
                $reportTipsDetails = $this->reportTipsRepository->getTipsForSection($studentReportCalculatedValue['section_id']);
                $tipsInformation = [];
                if (!empty($reportTipsDetails)) {
                    foreach ($reportTipsDetails as $tipDetail) {
                        $tipsDto = new TipsDto();
                        $tipsDto->setTipsName($tipDetail['title']);
                        $tipsDto->setTipsDescription($tipDetail['description']);
                        $tipsInformation[] = $tipsDto;
                    }
                }

                $reportSectionsDTO->setTips($tipsInformation);

                $reportSectionsInformation[] = $reportSectionsDTO;
            }
        }

        $studentReportDTO->setSurveyInfo($surveyBlocksResponse);
        $studentReportDTO->setReportSections($reportSectionsInformation);

        $studentReportDTO->setTakingAction($takingActionItem);
        $studentCampusConnections = $this->campusConnectionService->getStudentCampusConnections($organizationId, $studentId);
        $campusConnectionInformation = array();
        if (!empty($studentCampusConnections->getCampusConnections())) {
            foreach ($studentCampusConnections->getCampusConnections() as $campusConnection) {
                $campusConnection->setPersonId(NULL);
                $campusConnection->setGroups(NULL);
                $campusConnection->setCourses(NULL);
                $campusConnection->setIsInvisible(NULL);
                $campusConnectionInformation[] = $campusConnection;
            }
        }
        $studentReportDTO->setCampusConnections($campusConnectionInformation);

        $campusResources = $this->campusResourceService->getCampusResources($organizationId);
        $campusResourceInformation = array();
        if (!empty($campusResources)) {
            foreach ($campusResources as $campusResource) {
                if ($campusResource->getVisibleToStudents()) {
                    $campusResource->setId(NULL);
                    $campusResource->setOrganizationId(NULL);
                    $campusResource->setStaffId(NULL);
                    $campusResource->setReceiveReferals(NULL);
                    $campusResource->setVisibleToStudents(NULL);
                    $campusResourceInformation[] = $campusResource;
                }
            }
        }

        $studentReportDTO->setCampusResources($campusResourceInformation);
        $studentReport[] = $studentReportDTO;
        $reportDTO->setStudentReport($studentReport);
        return $reportDTO;
    }

}