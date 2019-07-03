<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\TeamActivityService;
use Synapse\CoreBundle\Service\Impl\TeamsService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Entity\TeamsDto;

/**
 * ClassTeamsController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/teams")
 */
class TeamController extends AbstractAuthController
{

    /**
     * @var TeamActivityService
     *
     *      @DI\Inject(TeamActivityService::SERVICE_KEY)
     */
    private $teamActivityService;

    /**
     *
     * @var TeamsService
     *
     *      @DI\Inject(TeamsService::SERVICE_KEY)
     */
    private $teamsService;

    /**
     * Creates a new team.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create New Team",
     * input = "Synapse\RestBundle\Entity\TeamsDto",
     * output = "Synapse\RestBundle\Entity\TeamsDto",
     * section = "Teams",
     * statusCodes = {
     *                  201 = "Team was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/new", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param TeamsDto $teamsDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createNewTeamAction(TeamsDto $teamsDto, ConstraintViolationListInterface $validationErrors)
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($teamsDto, $errors), 400);
        } else {
            $result = $this->teamsService->createNewTeam($teamsDto);
            return new Response($result);
        }
    }

    /**
     * Gets teams by organization.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Teams by Organization",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Teams",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/list/{orgId}", requirements={"_format"="json"})
     * @QueryParam(name="type", description="type")
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getTeamsAction($orgId ,ParamFetcher $paramFetcher)
    {   
        $type =  $paramFetcher->get('type');
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        if(strtolower($type) == "staff"){
            $result = $this->teamsService->getTeamLeadersTeams($organizationId,$loggedInUserId);
            
        }else{
            $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
            $result = $this->teamsService->getTeams($organizationId);
        }
        return new Response($result);
    }

    /**
     * Deletes a team.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Team",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Coordinator",
     * statusCodes = {
     *                  200 = "Resource(s) deleted. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/delete/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function deleteTeamAction($id)
    {
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        $profile = $this->teamsService->deleteTeam($id);
        return new Response($profile);
    }

    /**
     * Updates teams in an organization.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Synapse\RestBundle\Entity\TeamsDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * description = "Update Teams",
     * section = "Teams",
     * statusCodes = {
     *                  201 = "Resource(s) updated. Representation of resource(s)returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/update", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param TeamsDto $teamsDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updateTeamsAction(TeamsDto $teamsDto, ConstraintViolationListInterface $validationErrors)
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($teamsDto, $errors), 400);
        } else {
            $result = $this->teamsService->updateTeams($teamsDto);
            return new Response($result);
        }
    }

    /**
     * Gets members of a team.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Team Members",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Teams",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/members/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id //Team ID
     * @return Response
     */
    public function getTeamMembersAction($id)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $teamMembers = $this->teamsService->getTeamMembers($id, $organizationId);
        return new Response($teamMembers);
    }

    /**
     * Gets the team that a faculty member is in.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get a Faculty's Team",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Teams",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/orgId/{organizationId}/userId/{personId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $organizationId
     * @param int $personId
     * @return Response
     */
    public function getOrganizationTeamByUserIdAction($organizationId, $personId)
    {
        $teamMembers = $this->teamsService->getOrganizationTeamByUserId($organizationId, $personId);
        return new Response($teamMembers);
    }

    /**
     * Gets the teams that the user is a member of.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get User's Teams",
     * output = "Synapse\RestBundle\Entity\TeamsDto",
     * section = "Teams",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getMyteamsAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $teamMembers = $this->teamsService->getMyTeams($loggedInUserId);
        return new Response($teamMembers);
    }

    /**
     * Gets a user's team members for a specific team.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Team Members in a Team",
     * output = "Synapse\RestBundle\Entity\TeamsDto",
     * section = "Teams",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{teamId}", requirements={"_format"="json","teamId" = "\d+"})
     * @Rest\View(statusCode=200)
     *
     * @param int $teamId
     * @return Response
     */
    public function getTeamMembersByPersonAction($teamId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $teamMembers = $this->teamsService->getTeamMembersByPerson($loggedInUserId, $teamId);
        return new Response($teamMembers);
    }

    /**
     * Gets activity counts for the user's team.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Activity of User's Team",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Teams",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/recent/activities", requirements={"_format"="json"})
     * @QueryParam(name="filter", description="week | month filter", strict=true)
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getActivityCountsOfMyTeamAction(ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $timePeriod = $paramFetcher->get('filter');
        $teamMembers = $this->teamActivityService->getActivityCountsOfMyTeam($organizationId, $loggedInUserId, $timePeriod);
        return new Response($teamMembers);
    }

    /**
     * Gets the details of a user's team's activities.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Details About Team Activities",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Teams",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/activitiesdetail", requirements={"_format"="json"})
     * @QueryParam(name="team-id", description="Team Id")
     * @QueryParam(name="team-member-id", description="Team member id")
     * @QueryParam(name="activity_type", description="Activity type")
     * @QueryParam(name="filter", description="today | week | month | custom filter")
     * @QueryParam(name="start-date", description="Start date if filter is custom")
     * @QueryParam(name="end-date", description="End date if filter is custom")
     * @QueryParam(name="page_number", requirements="\d+", default=1, strict=false, description="page_number")
     * @QueryParam(name="records_per_page", default=25, strict=false, description="records_per_page")
     * @QueryParam(name="sort_by", strict=false, description="sorting field")
     * @QueryParam(name="output-format", strict=false, description="optional param to get result as CSV")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getMyTeamActivitiesDetailAction(ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $teamId = $paramFetcher->get('team-id');
        $teamMemberIdsString = $paramFetcher->get('team-member-id');
        $activityType = $paramFetcher->get('activity_type');
        $timePeriod = $paramFetcher->get('filter');
        $startDate = $paramFetcher->get('start-date');
        $endDate = $paramFetcher->get('end-date');
        
        $pageNumber = $paramFetcher->get('page_number');
        $recordsPerPage = $paramFetcher->get('records_per_page');
        $sortBy =  $paramFetcher->get('sort_by');
        $outputFormat =  $paramFetcher->get('output-format');

        if (strtolower(trim($outputFormat)) == 'csv') {
            $teamMembers = $this->teamActivityService->createActivityDetailsCsvJob($organizationId, $loggedInUserId, $teamId, $teamMemberIdsString, $activityType, $timePeriod, $startDate, $endDate);

        } else {
            $teamMembers = $this->teamActivityService->getActivityDetailsOfMyTeam($organizationId, $loggedInUserId, $teamId, $teamMemberIdsString, $activityType, $timePeriod, $startDate, $endDate, $pageNumber, $recordsPerPage, $sortBy);
        }
        return new Response($teamMembers);
    }
}
