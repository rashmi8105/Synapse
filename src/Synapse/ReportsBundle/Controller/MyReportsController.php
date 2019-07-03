<?php
namespace Synapse\ReportsBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\AcademicBundle\Service\Impl\AcademicTermService;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\EntityDto\ReportsTemplatesDto;
use Synapse\ReportsBundle\EntityDto\StudentsRequestDto;
use Synapse\ReportsBundle\Service\Impl\FactorReportService;
use Synapse\ReportsBundle\Service\Impl\PdfReportsService;
use Synapse\ReportsBundle\Service\Impl\ProfileSnapshotService;
use Synapse\ReportsBundle\Service\Impl\ReportsService;
use Synapse\ReportsBundle\Service\Impl\ReportTemplateService;
use Synapse\ReportsBundle\Service\Impl\SurveySnapshotService;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response;

/**
 * Class MyReportsController
 *
 * @package Synapse\ReportsBundle\Controller
 *
 *          @Rest\Prefix("/myreports")
 *
 */
class MyReportsController extends AbstractAuthController
{

    /**
     * @var AcademicTermService
     *
     *      @DI\Inject(AcademicTermService::SERVICE_KEY)
     */
    private $academicTermService;

    /**
     * @var AcademicYearService
     *
     *      @DI\Inject(AcademicYearService::SERVICE_KEY)
     */
    private $academicYearService;

    /**
     * @var FactorReportService
     *
     *      @DI\Inject(FactorReportService::SERVICE_KEY)
     */
    private $factorReportService;

    /**
     * @var PdfReportsService
     *
     *      @DI\Inject(PdfReportsService::SERVICE_KEY)
     */
    private $pdfReportService;

    /**
     * @var ProfileSnapshotService
     *
     *      @DI\Inject(ProfileSnapshotService::SERVICE_KEY)
     */
    private $profileSnapshotService;

    /**
     * @var ReportsService
     *
     *      @DI\Inject(ReportsService::SERVICE_KEY)
     */
    private $reportsService;

    /**
     * @var ReportTemplateService
     *
     *      @DI\Inject(ReportTemplateService::SERVICE_KEY)
     */
    private $reportTemplateService;

    /**
     * @var SurveySnapshotService
     *
     *      @DI\Inject(SurveySnapshotService::SERVICE_KEY)
     */
    private $surveySnapshotService;


	/**
     * Gets a list of reports for a person.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get My Report List",
     * output = "Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto",
     * section = "My Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred.",
     *                  404 = "Not found.",
     *                  500 = "Internal server error.",
     *                  504 = "Request has timed out."
     * },
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="person_id", requirements="\d+", description="Person Id")
     * @QueryParam(name="org_id", requirements="\d+", description="organization Id")
	 * @QueryParam(name="page_no", requirements="\d+", description="Page No")
	 * @QueryParam(name="offset", requirements="\d+", description="Offset Limit")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getMyReportListAction(ParamFetcher $paramFetcher)
    {
        $orgId = $paramFetcher->get('org_id');
		$personId = $paramFetcher->get('person_id');
		$limit = $paramFetcher->get('page_no');
        $offset = $paramFetcher->get('offset');
		$myReports = $this->reportsService->getMyReports($orgId, $personId, $limit, $offset);
        return new Response($myReports);
    }


	/**
     * Gets the list of all 'My Reports' templates.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get My Report Templates List",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "My Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred.",
     *                  404 = "Not found.",
     *                  500 = "Internal server error.",
     *                  504 = "Request has timed out."
     * },
     * )
     *
     * @Rest\Get("/templates", requirements={"_format"="json"})
     * @QueryParam(name="person_id", requirements="\d+", description="Person Id")
     * @QueryParam(name="org_id", requirements="\d+", description="organization Id")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getMyReportTemplatesListAction(ParamFetcher $paramFetcher)
    {
        $orgId = $paramFetcher->get('org_id');
		$personId = $paramFetcher->get('person_id');
		$myReports = $this->surveySnapshotService->getMyReportsTemplate($orgId, $personId);
        return new Response($myReports);
    }

	/**
     * Gets a user's permissions.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Permissions",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "My Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred.",
     *                  404 = "Not found.",
     *                  500 = "Internal server error.",
     *                  504 = "Request has timed out."
     * },
     * )
     *
     * @Rest\Get("/access", requirements={"_format"="json"})
     * @QueryParam(name="person_id", requirements="\d+", description="Person Id")
     * @QueryParam(name="report_id", requirements="\d+", description="organization Id")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getPermissionsAction(ParamFetcher $paramFetcher)
    {
		$reportId = $paramFetcher->get('report_id');
		$personId = $paramFetcher->get('person_id');
		$myReports = $this->reportTemplateService->checkPermission($reportId, $personId);
        return new Response($myReports);
    }


	/**
     * Gets 'My Report' details.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get My Report Details",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "My Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred.",
     *                  404 = "Not found.",
     *                  500 = "Internal server error.",
     *                  504 = "Request has timed out."
     * },
     * )
     *
     * @Rest\Get("/old/{reportInstanceId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @QueryParam(name="question_type_code", description="Question Type Code")
	 * @QueryParam(name="filter", description="Question Type")
	 * @QueryParam(name="type", description="type of the reports")
	 * @QueryParam(name="question", description="ebi or org question")
	 * @QueryParam(name="data", strict=false, description="offset")
	 * @QueryParam(name="question_number", requirements="\d+", description="Question Number")
	 * @QueryParam(name="option_values",description="Question Option value")
	 * @QueryParam(name="option_id",description="Question Option value")
	 * @QueryParam(name="page_no", requirements="\d+", description="Page No")
	 * @QueryParam(name="factor_id", requirements="\d+", description="factor_id")
	 * @QueryParam(name="offset", requirements="\d+", description="Offset Limit")
	 * @QueryParam(name="output-format", requirements="(csv)", description="View mode of the response, viewmode= csv")
	 * @QueryParam(name="source", requirements="(drilldown)", description="View mode of the response, viewmode= csv")
	 * @QueryParam(name="sortBy", strict=false, description="sorting field")
	 * @QueryParam(name="print", strict=false, description="print")
	 * @QueryParam(name="timezone", strict=false, description="timezone")
	 * @Rest\View(statusCode=200)
     *
     * @param int $reportInstanceId
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @deprecated // service function call is deprecated
     */
    public function getMyReportDetailsAction($reportInstanceId,  ParamFetcher $paramFetcher)
    {
        $questionTypeCode = $paramFetcher->get('question_type_code');
		$questionNumber = $paramFetcher->get('question_number');
		$optionValues = $paramFetcher->get('option_values');
		$optionId = $paramFetcher->get('option_id');
		$filter = $paramFetcher->get('filter');
		$question = $paramFetcher->get('question');
		$pageNo = $paramFetcher->get('page_no');
		$factorId = $paramFetcher->get('factor_id');
		$offset = $paramFetcher->get('offset');
		$type = $paramFetcher->get('type');
        $loggedInUserId = $this->getLoggedInUserId();
		$viewMode = $paramFetcher->get('output-format');
		$sortBy =  $paramFetcher->get('sortBy');
		$data =  $paramFetcher->get('data');
		$source =  $paramFetcher->get('source');
		$print =  $paramFetcher->get('print');
		$timezone =  $paramFetcher->get('timezone');

		$myReports = $this->surveySnapshotService->getJsonResponseDrilldown($loggedInUserId, $reportInstanceId, $questionTypeCode, $questionNumber, $optionValues, $pageNo, $offset, $filter, $viewMode, $type, $factorId, $sortBy, $data, $source, $question, $optionId , null, $print, $timezone);
        return new Response($myReports);
    }

    /**
     * Get JSON Response for Faculty/Staff Usage Report.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Generated Report JSON",
     * output = "Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto",
     * section = "My Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred.",
     *                  403 = "Access denied",
     *                  404 = "Not found.",
     *                  500 = "Internal server error.",
     *                  504 = "Request has timed out."
     * },
     * )
     *
     * @Rest\Get("/{reportInstanceId}", requirements={"_format"="json"})
     * @QueryParam(name="question_type_code", description="Question Type Code")
     * @QueryParam(name="filter", description="Question Type")
     * @QueryParam(name="print", strict=false, description="print mode of the response as pdf")
     * @QueryParam(name="timezone", strict=false, description="timezone")
     * @QueryParam(name="output-format", requirements="(csv)", description="View mode of the response, viewmode= csv")
     * @QueryParam(name="sort_by", strict=false, description="Column to sort by used for Faculty/Staff Usage Report")
     * @QueryParam(name="page_no", requirements="\d+", description="Page No")
     * @QueryParam(name="offset", requirements="\d+", description="Offset Limit")
     * @Rest\View(statusCode=200)
     *
     * @param int $reportInstanceId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getGeneratedReportJsonAction($reportInstanceId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $questionTypeCode = $paramFetcher->get('question_type_code');
        $filter = $paramFetcher->get('filter');
        $outputFormat = $paramFetcher->get('output-format');
        $print = $paramFetcher->get('print');
        $timezone = $paramFetcher->get('timezone');
        $offset = $paramFetcher->get('offset');
        $pageNumber = $paramFetcher->get('page_no');
        $sortBy = $paramFetcher->get('sort_by');

        $myReport = $this->reportsService->getResponseJson($loggedInUserId, $reportInstanceId, $questionTypeCode, $filter, $outputFormat, $print, $timezone, false, $sortBy, $pageNumber, $offset);
        return new Response($myReport);
    }


    /**
     * Get JSON response for drilldown on Factor Report.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Drill-down For Factor Report",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "My Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred.",
     *                  404 = "Not found.",
     *                  500 = "Internal server error.",
     *                  504 = "Request has timed out."
     * },
     * )
     *
     * @Rest\Get("/{reportInstanceId}/drilldown/factor", requirements={"_format"="json"})
     * @QueryParam(name="factor_id", requirements="\d+", description="factor_id")
     * @QueryParam(name="option_values",description="Question Option value")
     * @QueryParam(name="page_no", requirements="\d+", description="Page No")
     * @QueryParam(name="offset", requirements="\d+", description="Offset Limit")
     * @QueryParam(name="output-format", requirements="(csv)", description="View mode of the response, viewmode= csv")
     * @QueryParam(name="sortBy", strict=false, description="sorting field")
     * @QueryParam(name="data", strict=false, description="")
     * @Rest\View(statusCode=200)
     *
     * @param int $reportInstanceId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getDrilldownForFactorReportAction($reportInstanceId, ParamFetcher $paramFetcher){
        $factorId = $paramFetcher->get('factor_id');
        $optionValues = $paramFetcher->get('option_values');
        $pageNumber = $paramFetcher->get('page_no');
        $offset = $paramFetcher->get('offset');
        $viewMode = $paramFetcher->get('output-format');
        $sortBy = $paramFetcher->get('sortBy');
        $data = $paramFetcher->get('data');
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        if ($data == 'student_list'){
            $drilldownResponse = $this->factorReportService->getSurveyFactorReportDrilldownStudentNamesAndIds($reportInstanceId, $organizationId, $loggedInUserId, $factorId, $optionValues);
        } else {
            $drilldownResponse = $this->factorReportService->getSurveyFactorsReportDrilldown($reportInstanceId, $loggedInUserId, $factorId, $optionValues, $pageNumber, $offset, $viewMode, $sortBy);
        }

        return new Response($drilldownResponse);
    }

    /**
     * Gets JSON response for drilldown on Survey Snapshot Report.
     *
     * ToDo (long-term): Change this API to use the question_bank_id and survey_id rather than "question_number".
     * ToDo (long-term): Change other query parameters to better names: question -> question_source, source -> csv_origin, output-format -> output_format.
     * ToDo (long-term): Remove "data" query parameter.  Instead add an additional option, "names_only" to the output_format query parameter.
     * ToDo (long-term): Add query parameters option_min and option_max, for use in numeric questions.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Drill-down For Survey Snapshot Report",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "My Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred.",
     *                  404 = "Not found.",
     *                  500 = "Internal server error.",
     *                  504 = "Request has timed out."
     * },
     * )
     *
     * @Rest\Get("/{reportInstanceId}/drilldown/survey", requirements={"_format"="json"})
     * @QueryParam(name="question_type_code", description="Question Type Code")
     * @QueryParam(name="question_type", requirements="(ebi|isq)", description="Is this an EBI question or ISQ?")
     * @QueryParam(name="question_number", requirements="\d+", description="survey_question_id or org_question_id")
     * @QueryParam(name="option_values", description="Question option values(s)")
     * @QueryParam(name="option_id", description="Question option id, for multi-response questions")
     * @QueryParam(name="page_number", requirements="\d+", description="Page Number")
     * @QueryParam(name="records_per_page", default=25, description="Records Per Page, an integer or 'unlimited' for the whole list")
     * @QueryParam(name="data", requirements="student_list", strict=false, description="If set, returns the full list of student names and ids, sorted by name, which is used for bulk actions.")
     * @QueryParam(name="output-format", requirements="csv", description="Set if the goal of this API call is to produce a csv")
     * @QueryParam(name="output_format", requirements="(csv|names_only)", description="Set if the goal of this API call is to produce a csv or only student names and ids (as is used for bulk actions)")
     * @QueryParam(name="source", requirements="drilldown", strict=false, description="Differentiate whether the csv came from the drilldown or the main report")
     * @QueryParam(name="sort_by", strict=false, description="sorting field")
     * @Rest\View(statusCode=200)
     *
     * @param $reportInstanceId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getDrilldownForSurveySnapshotReportAction($reportInstanceId, ParamFetcher $paramFetcher)
    {
        $questionSource = $paramFetcher->get('question_type');
        $questionTypeCode = $paramFetcher->get('question_type_code');
        $questionId = $paramFetcher->get('question_number');
        $optionValues = $paramFetcher->get('option_values');
        $pageNumber = $paramFetcher->get('page_number');
        $recordsPerPage = $paramFetcher->get('records_per_page');
        $outputFormat = $paramFetcher->get('output_format');
        $sortBy = $paramFetcher->get('sort_by');
        $csvOrigin = $paramFetcher->get('source');

        if ($optionValues) {
            $optionValues = explode(',', $optionValues);
        }

        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        // TODO (long-term): Remove the following code (as well as the query parameters "output-format" and "data") as part of ESPRJ-9222.
        if (is_null($outputFormat)) {
            $outputFormat = $paramFetcher->get('output-format');
        }

        $data = $paramFetcher->get('data');
        if ($data == 'student_list') {
            $recordsPerPage = null;
            $sortBy = 'name';
            $outputFormat = 'names_only';
        }

        if ($outputFormat == 'csv') {
            $recordsPerPage = null;
        }

        if (in_array($sortBy, ['student_last_name', '+student_last_name'])) {
            $sortBy = 'name';           // It's actually being sorted by lastname AND firstname.
        } elseif ($sortBy == '-student_last_name') {
            $sortBy = '-name';
        }

        // End of section to remove.

        if ($outputFormat == 'csv') {
            if ($csvOrigin == 'drilldown') {
                // Call service method for creating a CSV originating from the drilldown.
                $response = $this->surveySnapshotService->getDrillDownCSV($loggedInUserId, $reportInstanceId, $questionId, $questionSource, $sortBy, $optionValues, $questionTypeCode);
            } else {
                // Call service method for creating a CSV originating from the main report.
                $response = $this->surveySnapshotService->getReportSurveyValuesForCSV($loggedInUserId, $organizationId, $reportInstanceId, $questionSource, $questionId, $optionValues, $questionTypeCode);
                }
        } elseif ($outputFormat == 'names_only') {
            // Call service method for getting the names of all the students in the drilldown, for setting up a bulk action.
            $response = $this->surveySnapshotService->getStudentIdsAndNames($loggedInUserId, $organizationId, $reportInstanceId, $questionSource, $questionId, $optionValues);
        } else {
            $response = $this->surveySnapshotService->getDrilldownJSONResponse($loggedInUserId, $reportInstanceId, $questionId, $questionSource, $pageNumber, $recordsPerPage, $sortBy, $optionValues);
        }

        return new Response($response);
    }


    /**
     * Get JSON response for drilldown on Profile Snapshot Report.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Drilldown For Profile Snapshot Report",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "My Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred.",
     *                  404 = "Not found.",
     *                  500 = "Internal server error.",
     *                  504 = "Request has timed out."
     * },
     * )
     *
     * @Rest\Get("/{reportInstanceId}/drilldown/profile", requirements={"_format"="json"})
     * @QueryParam(name="metadata_id", requirements="\d+", description="ebi_metadata_id or org_metadata_id")
     * @QueryParam(name="metadata_source", requirements="(ebi|isp)", default="ebi", description="Is this an EBI or ISP Item?")
     * @QueryParam(name="page_number", requirements="\d+", default=1, description="Page Number")
     * @QueryParam(name="records_per_page", default=25, description="Records Per Page, an integer or 'unlimited' for the whole list")
     * @QueryParam(name="sort_by", default="risk_color", description="Column to sort by (risk_color|last_name|class_level|profile_item_value) and direction to sort (desc is indicated by '-' preceding column name, asc is indicated by '+' or just the column name)")
     * @QueryParam(name="year_id", requirements="\d+", description="org_academic_year_id, for year-specific profile items")
     * @QueryParam(name="term_id", requirements="\d+", description="org_academic_terms_id, for term-specific profile items")
     * @QueryParam(name="option_value", requirements="\d+", description="For Categorical items, drilldown into a particular option")
     * @QueryParam(name="option_min", description="For Numeric items, minimum of the histogram bin to drill into")
     * @QueryParam(name="option_max", description="For Numeric items, maximum of the histogram bin to drill into")
     * @QueryParam(name="output_format", requirements="(csv|names_only)", description="Set if the goal of this API call is to produce a csv or only student names and ids (as is used for bulk actions)")
     * @QueryParam(name="output-format", requirements="csv", description="Set if the goal of this API call is to produce a csv")
     * @QueryParam(name="source", requirements="(drilldown)", description="Differentiate whether the csv came from the drilldown or the main report")
     * @QueryParam(name="data", requirements="student_list", description="If set, returns the full list of student names and ids, sorted by name, which is used for bulk actions.")
     * @Rest\View(statusCode=200)
     *
     * @param int $reportInstanceId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getDrilldownForProfileReportAction($reportInstanceId, ParamFetcher $paramFetcher)
    {
        $metadataId = $paramFetcher->get('metadata_id');
        $metadataSource = $paramFetcher->get('metadata_source');
        $pageNumber = $paramFetcher->get('page_number');
        $recordsPerPage = $paramFetcher->get('records_per_page');
        $sortBy = $paramFetcher->get('sort_by');
        $orgAcademicYearId = $paramFetcher->get('year_id');
        $orgAcademicTermId = $paramFetcher->get('term_id');
        $optionValue = $paramFetcher->get('option_value');
        $optionMin = $paramFetcher->get('option_min');
        $optionMax = $paramFetcher->get('option_max');
        $outputFormat = $paramFetcher->get('output_format');
        $csvOrigin = $paramFetcher->get('source');

        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        if ($recordsPerPage == 'unlimited') {
            $recordsPerPage = null;
        }

        // TODO: Remove the following code (as well as the query parameters "output-format" and "data") as part of ESPRJ-9222.
        if (is_null($outputFormat)) {
            $outputFormat = $paramFetcher->get('output-format');
        }

        $data = $paramFetcher->get('data');
        if ($data == 'student_list') {
            $recordsPerPage = null;
            $sortBy = 'name';
            $outputFormat = 'names_only';
        }

        if ($outputFormat == 'csv') {
            $recordsPerPage = null;
        }

        if (in_array($sortBy, ['last_name', '+last_name'])) {
            $sortBy = 'name';           // It's actually being sorted by lastname AND firstname.
        } elseif ($sortBy == '-last_name') {
            $sortBy = '-name';
        }

        // End of section to remove.

        $this->profileSnapshotService->validateScope($metadataId, $metadataSource, $orgAcademicYearId, $orgAcademicTermId);

        if (isset($orgAcademicYearId)) {
            $this->academicYearService->validateAcademicYear($orgAcademicYearId, $organizationId);
        }

        if (isset($orgAcademicTermId)) {
            $this->academicTermService->validateAcademicTerm($orgAcademicTermId, $organizationId, $orgAcademicYearId);
        }

        if ($outputFormat == 'csv') {
            if ($csvOrigin == 'drilldown') {
                $response = $this->profileSnapshotService->getDrilldownCSV($loggedInUserId, $organizationId, $reportInstanceId, $metadataId, $metadataSource, $sortBy, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax);
            } else {
                $response = $this->profileSnapshotService->getProfileItemValuesInCSV($loggedInUserId, $organizationId, $reportInstanceId, $metadataId, $metadataSource, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax);
            }
        } elseif ($outputFormat == 'names_only') {
            $response = $this->profileSnapshotService->getStudentIdsAndNames($loggedInUserId, $organizationId, $reportInstanceId, $metadataId, $metadataSource, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax);
        } else {
            $response = $this->profileSnapshotService->getDrilldownJSONResponse($loggedInUserId, $organizationId, $reportInstanceId, $metadataId, $metadataSource, $pageNumber, $recordsPerPage, $sortBy, $orgAcademicYearId, $orgAcademicTermId, $optionValue, $optionMin, $optionMax);
        }

        return new Response($response);
    }


	/**
     * Creates a report template.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Reports Template",
     * input = "Synapse\ReportsBundle\EntityDto\ReportsTemplatesDto",
     * output = "Synapse\ReportsBundle\EntityDto\ReportsTemplatesDto",
     * section = "My Reports",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred.",
     *                  404 = "Not found.",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out."
     * },
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param ReportsTemplatesDto $reportTemplatesDto
     * @return ReportsTemplatesDto
     */
    public function createReportsTemplateAction(ReportsTemplatesDto $reportTemplatesDto)
    {
        $reportsTemplate = $this->reportTemplateService->createReportTemplate($reportTemplatesDto);
		return $reportsTemplate;
    }


    /**
     * Generates a PDF report.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Generate PDF Reports",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "My Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred.",
     *                  404 = "Not found.",
     *                  500 = "Internal server error.",
     *                  504 = "Request has timed out."
     * },
     * )
     *
     * @Rest\Get("/pdf/{personId}", requirements={"_format"="json"})
     * @QueryParam(name="report_instance_id", requirements="\d+", description="Report Instance ID")
     * @QueryParam(name="report_short_code", description="Report short code")
     * @QueryParam(name="zoom", requirements="\d*[\.]{0,1}\d+", description="Magnify Report")
     * @QueryParam(name="timezone", description="timezone")
     * @Rest\View(statusCode=200)
     *
     * @param int $personId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function generatePdfReportsAction($personId, ParamFetcher $paramFetcher)
    {
        $zoom = $paramFetcher->get('zoom');
        $reportInstanceId = $paramFetcher->get('report_instance_id');
        $reportShortCode = $paramFetcher->get('report_short_code');
        $timezone = $paramFetcher->get('timezone');

        if ( empty( $zoom) ) {
            $zoom = 1.042;
        }
        $pdfReport = $this->pdfReportService->generatePdfReport($personId, $reportInstanceId, $reportShortCode, $zoom, $timezone);
        return new Response($pdfReport);
    }

    /**
     * Edits a report template.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Reports Template",
     * input = "Synapse\ReportsBundle\EntityDto\ReportsTemplatesDto",
     * output = "Synapse\ReportsBundle\EntityDto\ReportsTemplatesDto",
     * section = "My Reports",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred.",
     *                  404 = "Not found.",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out."
     * },
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param ReportsTemplatesDto $reportTemplatesDto
     * @return Response
     */
    public function editReportsTemplateAction(ReportsTemplatesDto $reportTemplatesDto)
    {
        $reportsTemplate = $this->reportTemplateService->editReportTemplate($reportTemplatesDto);
        return $reportsTemplate;
    }

    /**
     * Deletes a report template.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Report Template",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "My Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{orgId}/{templateId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $orgId
     * @param int $templateId
     * @return Response
     */
    public function deleteReportTemplateAction($orgId, $templateId)
    {
        $loggedInUser = $this->getLoggedInUserId();
        $reportTemplate = $this->surveySnapshotService->deleteReportTemplate($orgId, $templateId, $loggedInUser);
        return new Response($reportTemplate);
    }

    /**
     * Gets 'My Report' details based on specified students.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get My Report Details Based Students",
     * input = "Synapse\ReportsBundle\EntityDto\StudentsRequestDto",
     * output = "Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto",
     * section = "My Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred.",
     *                  404 = "Not found.",
     *                  500 = "Internal server error.",
     *                  504 = "Request has timed out."
     * },
     * )
     *
     * @Rest\Post("/{reportInstanceId}", requirements={"_format"="json"})
     * @QueryParam(name="question_type_code", description="Question Type Code")
     * @QueryParam(name="filter", description="Question Type")
     * @QueryParam(name="type", description="type of the reports")
     * @QueryParam(name="data", strict=false, description="offset")
     * @QueryParam(name="question_number", requirements="\d+", description="Question Number")
     * @QueryParam(name="option_values",description="Question Option value")
     * @QueryParam(name="page_no", requirements="\d+", description="Page No")
     * @QueryParam(name="factor_id", requirements="\d+", description="factor_id")
     * @QueryParam(name="offset", requirements="\d+", description="Offset Limit")
     * @QueryParam(name="output-format", requirements="(csv)", description="View mode of the response, viewmode= csv")
     * @QueryParam(name="source", requirements="(drilldown)", description="View mode of the response, viewmode= csv")
     * @QueryParam(name="sortBy", strict=false, description="sorting field")
     * @Rest\View(statusCode=200)
     *
     * @param StudentsRequestDto $studentReqDto
     * @param int $reportInstanceId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getMyReportDetailsBasedStudentsAction(StudentsRequestDto $studentReqDto, $reportInstanceId,  ParamFetcher $paramFetcher)
    {
        $questionTypeCode = $paramFetcher->get('question_type_code');
        $questionNumber = $paramFetcher->get('question_number');
        $optionValues = $paramFetcher->get('option_values');
        $filter = $paramFetcher->get('filter');
        $pageNo = $paramFetcher->get('page_no');
        $factorId = $paramFetcher->get('factor_id');
        $offset = $paramFetcher->get('offset');
        $type = $paramFetcher->get('type');
        $loggedUserId = $this->getLoggedInUserId();
        $viewMode = $paramFetcher->get('output-format');
        $sortBy =  $paramFetcher->get('sortBy');
        $data =  $paramFetcher->get('data');
        $source =  $paramFetcher->get('source');
        $myReports = $this->surveySnapshotService->getJsonResponseDrilldown($loggedUserId, $reportInstanceId, $questionTypeCode, $questionNumber, $optionValues, $pageNo, $offset, $filter, $viewMode, $type, $factorId, $sortBy, $data, $source, $studentReqDto);
        return new Response($myReports);
    }

    /**
     * Generates and downloads report data as CSV, for non-drilldown reports
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get CSV Report",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "My Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resources was returned.",
     *                  400 = "Validation error has occurred.",
     *                  404 = "Not found",
     *                  500 = "There was an internal server error OR errors in the body of the request.",
     *                  504 = "Request has timed out. Please re-try."
     * },
     * )
     *
     * @Rest\Get("/generatecsv/{reportInstanceId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $reportInstanceId
     * @return Response
     */
    public function getCSVReportAction($reportInstanceId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $filePath = $this->reportsService->generateReportCSV($loggedInUserId, $reportInstanceId, false);
        return new Response($filePath);
    }

}