<?php

namespace Synapse\SurveyBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response;
use Synapse\SurveyBundle\Service\Impl\CohortsService;

/**
 * Class CohortsController
 * @package Synapse\SurveyBundle\Controller
 *
 * @Rest\Prefix("/cohorts")
 */
class CohortsController extends AbstractAuthController
{

    /**
     * @var AcademicYearService
     *
     * @DI\Inject("academicyear_service")
     */
    private $academicYearService;

    /**
     * @var Logger
     *
     * @DI\Inject("monolog.logger.api")
     */
    private $apiLogger;

    /**
     * @var CohortsService
     *
     * @DI\Inject("cohorts_service")
     */
    private $cohortsService;


    /**
     * Returns data about cohorts, including cohort names, academic year id for the logged-in user's organization.
     * This API is geared toward reporting, and only includes cohorts for which students have been assigned.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get data about Cohorts that have students assigned to them",
     * section = "Cohorts",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="org_academic_year_id", requirements="\d+", description="org_academic_year_id")
     *
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getCohortsAction(ParamFetcher $paramFetcher)
    {
        $orgAcademicYearId = $paramFetcher->get('org_academic_year_id');
        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $organizationId = $loggedInUser->getOrganization()->getId();

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; OrgAcademicYearId: $orgAcademicYearId; ");

        if ($orgAcademicYearId) {
            $this->academicYearService->validateAcademicYear($orgAcademicYearId, $organizationId);
            $response = $this->cohortsService->getCohortsForOrganization($organizationId, [$orgAcademicYearId]);
        }
        else{
            $response = $this->cohortsService->getCohortsForOrganization($organizationId);
        }

        return new Response($response);
    }


    /**
     * Returns data about surveys for the logged-in user's organization, grouped by cohort and academic year.
     *
     * When used without the "purpose" query parameter, this API is geared toward reporting, and includes the number of students
     * from each cohort who have responded to each survey.  In this case, the response only includes cohort-survey combinations
     * for which the students in the cohort have been assigned the survey.  For this reason, the only survey statuses which will give
     * meaningful information are "launched" and "closed."
     * The retention_track_year query parameter is used to pre-filter the students included to those in the selected retention track.
     *
     * When used with the "purpose" query parameter set to "isq", only includes cohort-survey combinations for which ISQs have been set up.
     * When used with the "purpose" query parameter set to "survey_setup", includes all surveys available to be set up, and includes wess links to do so.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get data about Surveys for a logged-in user's organization",
     * section = "Cohorts",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/surveys", requirements={"_format"="json"})
     * @QueryParam(name="org_academic_year_id", requirements="\d+", description="id for an organization's academic year")
     * @QueryParam(name="status", requirements="(launched|closed|launched,closed)", description="survey status (launched/closed")
     * @QueryParam(name="has_responses", requirements="true", description="boolean for whether students have responded to the survey(true/false)")
     * @QueryParam(name="retention_track_year", requirements="\d+", description="org_academic_year_id for the retention track year")
     * @QueryParam(name="purpose", requirements="isq|survey_setup", description="if API used to list cohorts and surveys for ISQ permissions or survey setup")
     *
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getCohortsAndSurveysAction(ParamFetcher $paramFetcher)
    {
        $orgAcademicYearId = $paramFetcher->get('org_academic_year_id');
        $status = $paramFetcher->get('status');
        $hasResponses = filter_var($paramFetcher->get('has_responses'), FILTER_VALIDATE_BOOLEAN);
        $retentionTrackYearId = $paramFetcher->get('retention_track_year');
        $purpose = $paramFetcher->get('purpose');
        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $organizationId = $loggedInUser->getOrganization()->getId();


        if ($orgAcademicYearId) {
            $this->academicYearService->validateAcademicYear($orgAcademicYearId, $organizationId);
        }
        if ($retentionTrackYearId) {
            $this->academicYearService->validateAcademicYear($retentionTrackYearId, $organizationId);
        }

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; OrgAcademicYearId: $orgAcademicYearId; Status: $status; HasResponses: $hasResponses; RetentionTrackYearId: $retentionTrackYearId; Purpose: $purpose; ");

        if ($status) {
            $status = explode(',', $status);
        }

        if (in_array($purpose, ['isq', 'survey_setup'])) {
            $response = $this->cohortsService->getCohortsAndSurveysForOrganizationForSetup($organizationId, $purpose, $orgAcademicYearId, $status);
        } else {
            $response = $this->cohortsService->getCohortsAndSurveysForOrganizationForReporting($organizationId, $orgAcademicYearId, $status, $hasResponses, $retentionTrackYearId);
        }

        return new Response($response);
    }

}
