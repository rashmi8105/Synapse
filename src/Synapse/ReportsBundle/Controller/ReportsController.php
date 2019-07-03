<?php
namespace Synapse\ReportsBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PHPExcel_IOFactory;
use Resque;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\CSVFile;
use Synapse\ReportsBundle\EntityDto\ElementDto;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\EntityDto\SectionDto;
use Synapse\ReportsBundle\EntityDto\TipsDto;
use Synapse\ReportsBundle\Service\Impl\AcademicUpdateReportService;
use Synapse\ReportsBundle\Service\Impl\ActivityReportService;
use Synapse\ReportsBundle\Service\Impl\PdfReportsService;
use Synapse\ReportsBundle\Service\Impl\ReportSetupService;
use Synapse\ReportsBundle\Service\Impl\ReportsService;
use Synapse\ReportsBundle\Service\Impl\StudentSurveyReportService;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\UploadBundle\Job\ProcessElementsUpload;
use Synapse\UploadBundle\Job\ProcessOurStudentReport;
use Synapse\UploadBundle\Job\ProcessTipUpload;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;


/**
 * Class ReportsController
 *
 * @package Synapse\ReportsBundle\Controller
 *
 *          @Rest\Prefix("/reports")
 *
 */
class ReportsController extends AbstractAuthController
{
    const SECURITY_CONTEXT = 'security.context';


    /**
     * @var AcademicUpdateReportService
     * @DI\Inject(AcademicUpdateReportService::SERVICE_KEY)
     */
    private $academicUpdateReportService;

    /**
     * @var ReportSetupService
     *
     *      @DI\Inject(ReportSetupService::SERVICE_KEY)
     */
    private $reportSetupService;

    /**
     * @var ReportsService
     *
     *      @DI\Inject(ReportsService::SERVICE_KEY)
     */
    private $reportsService;

    /**
     * @var StudentSurveyReportService
     *
     *      @DI\Inject(StudentSurveyReportService::SERVICE_KEY)
     */
    private $studentSurveyReportService;

    /**
     * @var UploadFileLogService
     *
     *      @DI\Inject(UploadFileLogService::SERVICE_KEY)
     */
    private $uploadFileLogService;

    /**
     * Reports search criteria
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student Count Based Criteria",
     * input = "Synapse\SearchBundle\EntityDto\SaveSearchDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/student_population", requirements={"_format"="json"})
     * @QueryParam(name="report_short_code",strict=false, description="debug")
     * @Rest\View(statusCode=201)
     *
     * @param SaveSearchDto $customSearchDto
     * @param ConstraintViolationListInterface $validationErrors
     * @param ParamFetcher $paramFetcher
     * @return Response|View
     */
    public function getStudentCountBasedCriteriaAction(SaveSearchDto $customSearchDto, ConstraintViolationListInterface $validationErrors, ParamFetcher $paramFetcher)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($customSearchDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {

            $loggedUserId = $this->getLoggedInUserId();
            $reportShortCode = $paramFetcher->get('report_short_code');
            $studentCount = $this->reportsService->getStudentCountBasedCriteria($customSearchDto, $loggedUserId, $reportShortCode);
            return new Response($studentCount);
        }
    }


    /**
     * Generates the Academic Update Report based on the specified filter criteria.
     *
     * API format /academic-update-report
     * @ApiDoc(
     *          resource = true,
     *          description = "Generates Academic Update Report",
     *          input = "Synapse\SearchBundle\EntityDto\SaveSearchDto",
     *          output = "Synapse\RestBundle\Entity\Response",
     *          section = "Reports",
     *          statusCodes = {
     *                          201 = "Resource(s) created. Representation of resource(s) was returned",
     *                          400 = "Validation error has occurred",
     *                          403 = "Access denied",
     *                          500 = "There were errors in the body of the request or an internal server error",
     *                          504 = "Request has timed out"
     *                        }
     * )
     * @Rest\Post("/academic-update-report", requirements={"_format"="json"})
     * @QueryParam(name="output-format",strict=false, description="output format")
     * @QueryParam(name="page_no", strict=false, description="page_no")
     * @QueryParam(name="offset", strict=false, description="offset")
     * @QueryParam(name="data", strict=false, description="offset")
     * @QueryParam(name="sortBy", strict=false, description="offset")
     * @Rest\View(statusCode=201)
     *
     * @param SaveSearchDto $saveSearchDto
     * @param ParamFetcher $paramFetcher
     * @return \Synapse\RestBundle\Entity\Response
     */
    public function academicUpdateReportAction(SaveSearchDto $saveSearchDto, ParamFetcher $paramFetcher){

        $loggedUserId = $this->getLoggedInUserId();
        $outputFormat = $paramFetcher->get('output-format');
        $pageNumber = $paramFetcher->get('page_no');
        $recordsPerPage = $paramFetcher->get('offset');
        $data = $paramFetcher->get('data');
        $sortBy = $paramFetcher->get('sortBy');

        if($data == "student_list"){
            $result = $this->academicUpdateReportService->getStudentsForAcademicUpdateReport($saveSearchDto,$loggedUserId);
        }else if($outputFormat == "csv"){
            $result = $this->academicUpdateReportService->createAcademicUpdateCsv($saveSearchDto,$loggedUserId);
        }else{

            $currentDate =  new \DateTime();
            $result =  $this->academicUpdateReportService->generateReport($saveSearchDto,$loggedUserId,$pageNumber,$recordsPerPage,$sortBy,$currentDate);
        }
        return new Response($result);
    }


    /**
     * Gets survey reports status based on search criteria.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Survey Status Report",
     * input = "Synapse\SearchBundle\EntityDto\SaveSearchDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/{report_id}", requirements={"_format"="json"})
     * @QueryParam(name="output-format",strict=false, description="output format")
     * @QueryParam(name="debug",strict=false, description="debug")
     * @QueryParam(name="page_no", strict=false, description="page_no")
     * @QueryParam(name="offset", strict=false, description="offset")
     * @QueryParam(name="data", strict=false, description="offset")
     * @QueryParam(name="sortBy", strict=false, description="offset")
     * @Rest\View(statusCode=201)
     *
     * @param int $report_id
     * @param SaveSearchDto $customSearchDto
     * @param ConstraintViolationListInterface $validationErrors
     * @param ParamFetcher $paramFetcher
     * @return Response|View
     */
    public function getSurveyStatusReportAction($report_id, SaveSearchDto $customSearchDto, ConstraintViolationListInterface $validationErrors, ParamFetcher $paramFetcher)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($customSearchDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $loggedUserId = $this->getLoggedInUserId();
            $outputFormat = $paramFetcher->get('output-format');
            $pageNo = $paramFetcher->get('page_no');
            $offset = $paramFetcher->get('offset');
            $debug = $paramFetcher->get('debug');
            $data = $paramFetcher->get('data');
            $sortBy = $paramFetcher->get('sortBy');

            if ($report_id == 1) {
                $report = $this->reportsService->getIndividualResponseReportData($customSearchDto, $loggedUserId, $outputFormat, $pageNo, $offset, $data, $sortBy);
            } else if ($report_id == 'ourstudents') {
                $report = $this->reportsService->getOurStudentsReport($customSearchDto, $loggedUserId,[]);
            }
            return new Response($report);
        }
    }

    /**
     * Get Student Survey Report.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Generate Student Survey Report",
     * output = "Synapse\ReportsBundle\EntityDto\ReportDto",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred.",
     *                  404 = "Not found.",
     *                  500 = "Internal server error.",
     *                  504 = "Request has timed out."
     * },
     * )
     *
     * @Rest\Get("/studentsurveyreport", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function generateStudentSurveyReportAction(ParamFetcher $paramFetcher)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        // Get student survey report data
        $loggedInStudentId = $this->getLoggedInUserId();
        $response = $this->studentSurveyReportService->getStudentReport($organizationId, $loggedInStudentId, 'student-report');
        return new Response($response);
    }


    /**
     * Gets a list of all reports the user has access to.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Gets a list of all reports the user has access to",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred.",
     *                  404 = "Not found.",
     *                  500 = "There was either errors with the body of the request or an internal server error.",
     *                  504 = "Request has timed out. Please re-try."
     * },
     * )
     *
     * @Rest\GET("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @QueryParam(name="filter", description="'all' will fetch all non coordinators reports")
     * @QueryParam(name="source", description="Source value either permission or null, if permission it is being called from permission tab")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getUserReportsAction(ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $filter = $paramFetcher->get('filter');
        $source = $paramFetcher->get('source');
        // if no filter provided set it to all
        if ($filter == null) {
            $filter = "all";
        }
        // Get all reports based on user access
        $response = $this->reportsService->getUserReports($loggedInUserId, $filter, $source);
        return new Response($response);
    }

    /**
     * Create my report activity.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Running Status",
     * input = "Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto",
     * output = "Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto",
     * section = "Reports",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @QueryParam(name="output-format",strict=false, description="output format")
     * @QueryParam(name="page_number", strict=false, description="Page number (currently only used for Group Response Report)")
     * @QueryParam(name="records_per_page", strict=false, description="Records per page (currently only used for Group Response Report)")
     * @QueryParam(name="sort_by", strict=false, description="Column to sort by (currently only used for Group Response Report)")
     * @QueryParam(name="no_queue", strict=false, description="offset")
     * @QueryParam(name="debug", strict=false, description="debug")
     * @Rest\View(statusCode=201)
     *
     * @param ReportRunningStatusDto $reportRunningDto
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createRunningStatusAction(ReportRunningStatusDto $reportRunningDto ,ParamFetcher $paramFetcher)
    {

        $rawData = $this->get('request')->request->all();
        $loggedUserId = $this->getLoggedInUserId();
        $orgId = $this->getLoggedInUserOrganizationId();
        $outputFormat = $paramFetcher->get('output-format');
        $pageNumber = $paramFetcher->get('page_number');
        $recordsPerPage = $paramFetcher->get('records_per_page');
        $sortBy = $paramFetcher->get('sort_by');
        $runImmediate = $paramFetcher->get('no_queue');
        $debug = $paramFetcher->get('debug');
        $returnData = $this->reportsService->generateReport($reportRunningDto, $loggedUserId, $orgId, $outputFormat, $pageNumber, $recordsPerPage, $sortBy, $runImmediate, $rawData, $debug);
        return new Response($returnData);
    }

    /**
     * Our Students Report Data
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Our Students Report",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/upload/ourstudentsreport", requirements={"_format"="json"})
     * @RequestParam(name="key")
     * @Rest\View(statusCode=201)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createOurStudentsReportAction(ParamFetcher $paramFetcher)
    {
        $key = $paramFetcher->get('key');
        $pathParts = pathinfo($key);

        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://".UploadConstant::OUR_STUD_REPORT_UPLOAD_DIR."/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://".UploadConstant::OUR_STUD_REPORT_UPLOAD_DIR."/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }

        $file = new CSVFile("data://".UploadConstant::OUR_STUD_REPORT_UPLOAD_DIR."/$key");
        //print "data://".UploadConstant::OUR_STUD_REPORT_UPLOAD_DIR."/$key";exit;
        $file->seek(PHP_INT_MAX);
        $rowsTotal = $file->key();
        $file->seek(0);
        foreach ($file as $idx => $row) {
            $columns = array_keys($row);
            break;
        }

        $resque = $this->get(UploadConstant::RESQUE);

        $jobNumber = uniqid();
        /*
         * Passing logged in user id
        */
        $loggedInUserId = $this->getLoggedInUserId();
        if (!$loggedInUserId) {
            $loggedInUserId = null;
        }

        $uploadFile = $this->uploadFileLogService->createUploadService(-1, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId, 'OSR');

        if ( $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }

        $this->uploadFileLogService->updateJobErrorPath($uploadFile);

        $job = new ProcessOurStudentReport();
        //$job->queue = 'facultyUpload_queue';
        $job->args = array(
            //UploadConstant::ORGN => $organization,
            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId()
        );

        $resque->enqueue($job);

        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Gets the Our Students Report template.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Our Students Report Template",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/upload/ourstudentsreport/template", requirements={"_format"="json"})
     * @RequestParam(name="key")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     */
    public function ourStudentsReportTemplateAction(ParamFetcher $paramFetcher)
    {
        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="ourstudentreport-upload-template.csv"');

        $template = [



            'LongitudinalID',
            'SurvID',
            'FactorID',
            'ReportSectionID',
            'ReportSectionName',
            'DisplayLabel',
            'NumeratorLow',
            'NumeratorHigh',
            'DenominatorLow',
            'DenominatorHigh',
            'NumeratorChoices',
            'DenominatorChoices',

        ];

        $template = implode(",", $template);
        echo $template;
        echo "\n";
        exit();
    }

    /**
     * Gets the 'Our Students Report' upload log.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Our Students Report Upload Log",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/upload/ourstudentsreport/{id}",defaults={"id"=-1},requirements={"id" = "^\d+$", "_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function ourStudentsReportUploadLogAction($id)
    {
        $upload = $this->uploadFileLogService->findUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Gets pending 'Our Students Report' uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Pending Our Students Report",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/upload/ourstudentsreport/pending",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function pendingOurStudentsReportAction()
    {
        $upload = $this->uploadFileLogService->ebiHasPendingView('OSR');
        return new Response([
            'upload' => $upload
        ], []);
    }



    /**
     * Gets the 'Our Student Report' list.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Our Student Report List",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/upload/ourstudentsreport/list",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getOurStudentReportListAction()
    {
        $returnData = $this->reportsService->getOurStudentReportList();
        return new Response($returnData);
    }


    /**
     * Gets a campus's activity report.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Campus Activity",
     * output = "Synapse\ReportsBundle\EntityDto\CampusActivityDto",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/studentactivity", requirements={"_format"="json"})
     * @QueryParam(name="year", description="Year Id")
     * @QueryParam(name="page_no", requirements="\d+", description="Page Number")
     * @QueryParam(name="offset", requirements="\d+", description="Offset details")
     * @QueryParam(name="type", description="Type of activity")
     * @QueryParam(name="access", requirements="\d+", description="access type")
     * @QueryParam(name="debug",strict=false, description="access type")
     * @QueryParam(name="sortBy", strict=false, description="sorting field")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getCampusActivityAction(ParamFetcher $paramFetcher)
    {
        $personId = $this->get(self::SECURITY_CONTEXT)
            ->getToken()
            ->getUser()
            ->getId();

        $organizationId = $this->get(self::SECURITY_CONTEXT)
            ->getToken()
            ->getUser()
            ->getOrganization()
            ->getId();

        $year = $paramFetcher->get('year');
        $access = $paramFetcher->get('access');
        $type = $paramFetcher->get('type');
        $pageNo = $paramFetcher->get('page_no');
        $offSet = $paramFetcher->get('offset');
        $sortBy = $paramFetcher->get('sortBy');
        $debug = $paramFetcher->get('debug');
        $getActivityResp = $this->reportsService->getCampusActivity($organizationId, $year, $access, $type, $pageNo, $offSet, $personId, $debug, $sortBy);
        return new Response($getActivityResp);
    }

    /**
     * Download Activity Report
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Campus Activity Download",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/downloadactivity", requirements={"_format"="json"})
     * @QueryParam(name="year", description="Year Id")
     * @QueryParam(name="type", description="Type of activity")
     * @QueryParam(name="access", requirements="\d+", description="access type")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getDownloadCampusActivityAction(ParamFetcher $paramFetcher)
    {
        $orgId = $this->get(self::SECURITY_CONTEXT)
            ->getToken()
            ->getUser()
            ->getOrganization()
            ->getId();
        $personId = $this->get(self::SECURITY_CONTEXT)
            ->getToken()
            ->getUser()
            ->getId();

        $year = $paramFetcher->get('year');
        $access = $paramFetcher->get('access');
        $type = $paramFetcher->get('type');
        $getActivityResp = $this->reportsService->getDownloadCampusActivity($orgId, $year, $access, $type, $personId);
        return new Response($getActivityResp);
    }

    /**
     * Gets cohort key.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Cohort Key",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\GET("/cohortkey", requirements={"_format"="json"})
     * @QueryParam(name="cohort_id", description="Cohort Id")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     *
     */
    public function getCohortKeyAction(ParamFetcher $paramFetcher)
    {
        $cohortId = $paramFetcher->get('cohort_id');
        $orgId = $this->getLoggedInUserOrganizationId();
        $cohortKeyResp = $this->reportsService->cohortsKeyDownload($orgId, $cohortId);
        return new Response($cohortKeyResp);
    }

    /**
     * Gets a cohort's survey report details.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Cohorts Survey Report",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/cohortssurveyreport", requirements={"_format"="json"})
     * @QueryParam(name="cohort_id", description="Cohort Id")
     * @QueryParam(name="year", strict=true, description="Year Id")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function cohortsSurveyReportAction(ParamFetcher $paramFetcher)
    {
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        $cohortId = $paramFetcher->get('cohort_id');
        $yearId = $paramFetcher->get('year');
        $orgId = $this->getLoggedInUserOrganizationId();
        $personId = $this->get(self::SECURITY_CONTEXT)
            ->getToken()
            ->getUser()
            ->getId();
        $cohortKeyResp = $this->reportsService->cohortsSurveyReport($orgId, $cohortId, $personId, $yearId);
        return new Response($cohortKeyResp);
    }

    /**
     * Gets options to display the 'Our Student's' report.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Our Student Report Display Options",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/osr/display",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getOurStudentReportDisplayOptionsAction()
    {
        $returnData = $this->reportsService->ourStudentsDisplayOptions();
        return new Response($returnData);
    }

    /**
     * Creates a new report section.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Section",
     * input = "Synapse\ReportsBundle\EntityDto\sectionDto",
     * output = "Synapse\ReportsBundle\EntityDto\sectionDto",
     * section = "Reports",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/sections", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param sectionDto $sectionDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createSectionAction(SectionDto $sectionDto,  ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($sectionDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $section = $this->reportSetupService->createReportSection($sectionDto);
            return new Response($section);
        }
    }

    /**
     * Updates a section of a report.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Section",
     * input = "Synapse\ReportsBundle\EntityDto\SectionDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  201 = "Resource(s) updated. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/sections",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param SectionDto $sectionDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function sectionEditAction(SectionDto $sectionDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($sectionDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $section = $this->reportSetupService->updateSection($sectionDto);
            return new Response($section);
        }
    }

    /**
     * Deletes a section of a report.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete section",
     * section = "Reports",
     * statusCodes = {
     *                  204 = "Resource(s) deleted. No representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/sections", requirements={"_format"="json"})
     * @QueryParam(name="section_id", requirements="\d+", strict=true, description="section ID")
     * @Rest\View(statusCode=204)
     *
     * @param ParamFetcher $paramFetcher
     */
    public function deleteSectionAction(ParamFetcher $paramFetcher)
    {
        $section_id = $paramFetcher->get('section_id');
        $this->reportSetupService->deleteSection($section_id);
    }

    /**
     * Gets the details of a report section.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Section Details",
     * output = "Synapse\ReportsBundle\EntityDto\SectionDto",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/sections", requirements={"_format"="json"})
     * @QueryParam(name="section_id", description="Section Id", requirements="\d+")
     * @QueryParam(name="report_id", description="Report Id", requirements="\d+")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getSectionDetailsAction(ParamFetcher $paramFetcher)
    {
        $sectionId = $paramFetcher->get('section_id');
        $reportId = $paramFetcher->get('report_id');
        $section = $this->reportSetupService->sectionDetails($sectionId, $reportId);
        return new Response($section);
    }

    /**
     * Gets the elements of a report's section.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Element Details",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/elements", requirements={"_format"="json"})
     * @QueryParam(name="element_id", description="Element Id", requirements="\d+")
     * @QueryParam(name="section_id", description="Section Id", requirements="\d+")
     * @QueryParam(name="report_id", description="Report Id", requirements="\d+")
     * @QueryParam(name="has_text", description="return only elements that have descriptions in the database", requirements="true")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getElementDetailsAction(ParamFetcher $paramFetcher)
    {
        $elementId = $paramFetcher->get('element_id');
        $sectionId = $paramFetcher->get('section_id');
        $reportId = $paramFetcher->get('report_id');
        $hasText = filter_var($paramFetcher->get('has_text'), FILTER_VALIDATE_BOOLEAN);     // Convert string "true" to boolean true.

        if (isset($sectionId)) {
            $elements = $this->reportsService->getReportSectionElements($sectionId, $hasText);
        } else {
            $elements = $this->reportSetupService->elementDetails($elementId, $reportId);
        }
        return new Response($elements);
    }

    /**
     * Updates an element within a section.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Element",
     * input = "Synapse\ReportsBundle\EntityDto\ElementDto",
     * output = "Synapse\ReportsBundle\EntityDto\ElementDto",
     * section = "Reports",
     * statusCodes = {
     *                  201 = "Resource(s) updated. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/elements",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param ElementDto $elementDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function elementEditAction(ElementDto $elementDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($elementDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $element = $this->reportSetupService->editElement($elementDto);
            return new Response($element);
        }
    }

    /**
     * Deletes an element within a section.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete element",
     * section = "Reports",
     * statusCodes = {
     *                  204 = "Resource(s) deleted. No representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/elements", requirements={"_format"="json"})
     * @QueryParam(name="element_id", requirements="\d+", strict=true, description="element ID")
     * @Rest\View(statusCode=204)
     *
     * @param ParamFetcher $paramFetcher
     */
    public function deleteElementAction(ParamFetcher $paramFetcher)
    {
        $element_id = $paramFetcher->get('element_id');
        $this->reportSetupService->deleteElement($element_id);
    }

    /**
     * Gets details about the tips in a report.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Tip Details",
     * output = "Synapse\ReportsBundle\EntityDto\TipsDto",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/tips", requirements={"_format"="json"})
     * @QueryParam(name="report_id", description="Report Id", requirements="\d+")
     * @QueryParam(name="tip_id", requirements="\d+", description="Tip ID")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getTipsDetailsAction(ParamFetcher $paramFetcher)
    {
        $reportId = $paramFetcher->get('report_id');
        $tipId = $paramFetcher->get('tip_id');
        $section = $this->reportSetupService->tipDetails($tipId, $reportId);
        return new Response($section);
    }

    /**
     * Edits a tip.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Tips",
     * input = "Synapse\ReportsBundle\EntityDto\TipsDto",
     * output = "Synapse\ReportsBundle\EntityDto\TipsDto",
     * section = "Reports",
     * statusCodes = {
     *                  201 = "Resource(s) updated. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/tips",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param TipsDto $tipsDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function tipsEditAction(TipsDto $tipsDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($tipsDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $tips = $this->reportSetupService->editTips($tipsDto);
            return new Response($tips);
        }
    }


    /**
     * Deletes specified tips.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete tips",
     * section = "Reports",
     * statusCodes = {
     *                  204 = "Resource(s) deleted. No representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/tips", requirements={"_format"="json"})
     * @QueryParam(name="tips_id", requirements="\d+", strict=true, description="Tips ID")
     * @Rest\View(statusCode=204)
     *
     * @param ParamFetcher $paramFetcher
     */
    public function deleteTipsAction(ParamFetcher $paramFetcher)
    {
        $tips_id = $paramFetcher->get('tips_id');
        $this->reportSetupService->deleteTips($tips_id);
    }

    /**
     * Create API to upload elements.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Elements",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/elementsupload",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createElementsUploadAction(ParamFetcher $paramFetcher)
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        }
        $key = $paramFetcher->get('key');

        $pathParts = pathinfo($key);
        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://reports_master/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://reports_master/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }

        $file = new CSVFile("data://reports_master/$key");
        $file->seek(PHP_INT_MAX);
        $rowsTotal = $file->key();
        $file->seek(0);
        foreach ($file as $idx => $row) {
            $columns = array_keys($row);
            break;
        }

        $resque = $this->get('bcc_resque.resque');

        $jobNumber = uniqid();
        $user = $this->getUser();

        $uploadFile = $this->uploadFileLogService->createElementsUploadLog(1, $key, $columns, $rowsTotal, $jobNumber, $user->getId(), 'SRE');

        if (! in_array('ElementName', $columns) || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }

        $this->uploadFileLogService->updateJobErrorPath($uploadFile);

        $job = new ProcessElementsUpload();

        $job->args = array(

            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId(),
            UploadConstant::USERID => $user->getId()
        );

        $resque->enqueue($job, true);

        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Create API to upload a tip.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Tip Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/tipsupload",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createTipUploadAction(ParamFetcher $paramFetcher)
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        }
        $key = $paramFetcher->get('key');

        $pathParts = pathinfo($key);
        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://reports_master/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://reports_master/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }

        $file = new CSVFile("data://reports_master/$key");
        $file->seek(PHP_INT_MAX);
        $rowsTotal = $file->key();
        $file->seek(0);
        foreach ($file as $idx => $row) {
            $columns = array_keys($row);
            break;
        }

        $resque = $this->get('bcc_resque.resque');

        $jobNumber = uniqid();
        $loggedInUserId = $this->getLoggedInUserId();

        $uploadFile = $this->uploadFileLogService->createElementsUploadLog(1, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId, 'SRT');

        if (! in_array('TipText', $columns) || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }

        $this->uploadFileLogService->updateJobErrorPath($uploadFile);

        $job = new ProcessTipUpload();

        $job->args = array(

            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId(),
            UploadConstant::USERID => $loggedInUserId
        );

        $resque->enqueue($job, true);

        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Gets the report element template.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Report Element Template",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/template/elementsupload")
     */
    public function getReportElementTemplateAction()
    {
        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="student-report-element-template.csv"');

        $template = [

            'SectionID',
            'ElementName',
            'DataType',
            'DataSource',
            'RedLow',
            'RedHigh',
            'RedText',
            'YellowLow',
            'YellowHigh',
            'YellowText',
            'GreenLow',
            'GreenHigh',
            'GreenText'
        ];

        $template = implode(",", $template);
        echo $template;
        echo "\n";
        exit();
    }

    /**
     * Download existing report elements.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Element Download",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/elementsupload",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createElementDownloadAction(ParamFetcher $paramFetcher)
    {
        $uploadFile = 'https://ebi-synapse-bucket.s3.amazonaws.com/reports-master/report-elements-data.csv';
        $response = new Response($uploadFile, []);
        return $response;
    }

    private function convertXLStoCSV($infile, $outfile)
    {
        $fileType = PHPExcel_IOFactory::identify($infile);
        $objReader = PHPExcel_IOFactory::createReader($fileType);

        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($infile);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->save($outfile);
    }

    /**
     * Creates an element.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Element",
     * input = "Synapse\ReportsBundle\EntityDto\ElementDto",
     * output = "Synapse\ReportsBundle\EntityDto\ElementDto",
     * section = "Reports",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/sec", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param ElementDto $elementDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createElementAction(ElementDto $elementDto,  ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($elementDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $section = $this->reportSetupService->createElements($elementDto);
            return new Response($section);
        }
    }

    /**
     * Gets the template for tip uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Tips Template",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/template/tipsupload")
     */
    public function getTipsTemplateAction()
    {
        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="student-report-tip-template.csv"');

        $template = [
            'SectionID',
            'TipName',
            'TipText',
            'DisplayOrder'
        ];

        $template = implode(",", $template);
        echo $template;
        echo "\n";
        exit();
    }

    /**
     * Downloads existing report tips.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Tips Download",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/tipsupload",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createTipsDownloadAction(ParamFetcher $paramFetcher)
    {
        $uploadFile = 'https://ebi-synapse-bucket.s3.amazonaws.com/reports-master/report-tip-data.csv';
        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Gets the 'Tips' upload log.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Tips Upload Log",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/tipsupload/{id}",defaults={"id"=-1},requirements={"id" = "^\d+$", "_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getTipsUploadLogAction($id)
    {
        $upload = $this->uploadFileLogService->findUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Gets the 'Elements' upload log.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Elements Upload Log",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/elementsupload/{id}",defaults={"id"=-1},requirements={"id" = "^\d+$", "_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getElementsUploadLogAction($id)
    {
        $upload = $this->uploadFileLogService->findUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Gets the current and past retention tracking years.
     * The query parameter report_id is used to give a report-specific message.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Retention Tracking Years",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/retention_track_years", requirements={"_format"="json"})
     * @QueryParam(name="report_id", requirements="\d+", description="id representing the type of report")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getRetentionTrackYearsAction(ParamFetcher $paramFetcher)
    {
        $reportId = $paramFetcher->get('report_id');
        $orgId = $this->getLoggedInUserOrganizationId();

        $response = $this->reportsService->getRetentionTrackYears($orgId, $reportId);
        return new Response($response);
    }


    /**
     * Gets a list of report sections for the given report, optionally filtered by retention_tracking_type,
     * optionally including report_section_elements for the sections which have them.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Report Sections",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{reportId}/sections", requirements={"_format"="json"})
     * @QueryParam(name="retention_tracking_type", requirements="(required|optional|none)", description="only list sections where a retention tracking group is required/optional/irrelevant")
     * @QueryParam(name="include_elements", requirements="true", description="include report section elements")
     * @Rest\View(statusCode=200)
     *
     * @param $reportId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getReportSectionsAction($reportId, ParamFetcher $paramFetcher)
    {
        $retentionTrackingType = $paramFetcher->get('retention_tracking_type');
        $includeElements = filter_var($paramFetcher->get('include_elements'), FILTER_VALIDATE_BOOLEAN);     // Convert string "true" to boolean true.

        $response = $this->reportsService->getReportSections($reportId, $retentionTrackingType, $includeElements);
        return new Response($response);
    }


    /**
     * Gets report elements that are pending for upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Pending Report Elements",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/elementsupload/pending")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function pendingReportElementsAction()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        }

        $upload = $this->uploadFileLogService->ebiHasPendingView('SRE');
        return new Response([
            'upload' => $upload
        ], []);
    }

    /**
     * Gets section tips that are pending for upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Pending Section Tips",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/tipsupload/pending")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function pendingSectionTipsAction()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        }

        $upload = $this->uploadFileLogService->ebiHasPendingView('SRT');
        return new Response([
            'upload' => $upload
        ], []);
    }
}