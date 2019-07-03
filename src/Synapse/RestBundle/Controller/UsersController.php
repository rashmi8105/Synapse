<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\DTO\StudentParticipationDTO;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Service\Impl\AuthCodeService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\CoreBundle\Service\Impl\UsersService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\TierConstant;
use Synapse\CoreBundle\Util\Constants\UsersConstant;
use Synapse\MultiCampusBundle\EntityDto\PromoteUserDto;
use Synapse\MultiCampusBundle\EntityDto\RoleDto;
use Synapse\MultiCampusBundle\Service\Impl\TierUsersService;
use Synapse\MultiCampusBundle\Service\Impl\UserConflictsService;
use Synapse\RestBundle\Entity\AuthCodeDto;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Entity\UserDTO;


/**
 * Class UsersController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/users")
 */
class UsersController extends AbstractAuthController
{

	const SECURITY_CONTEXT = 'security.context';


    /**
     * @var AuthCodeService
     *
     *      @DI\Inject(AuthCodeService::SERVICE_KEY)
     */
    private $authCodeService;

    /**
     * @var TierUsersService
     *
     *      @DI\Inject(TierUsersService::SERVICE_KEY)
     */
    private $tierUsersService;

    /**
     * @var TokenService
     *
     *      @DI\Inject(TokenService::SERVICE_KEY)
     */
    private $tokenService;

    /**
     * @var UsersService
     *
     *      @DI\Inject(UsersService::SERVICE_KEY)
     */
    private $usersService;

    /**
     * @var UserConflictsService
     *
     *      @DI\Inject(UserConflictsService::SERVICE_KEY)
     */
    private $userConflictsService;

    /**
     * Create Users
     *
     * @ApiDoc(
     * resource = true,
     * description = "Creates new user",
     * input = "Synapse\RestBundle\Entity\UserDTO",
     * statusCodes = {
     *                  201 = "User was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param UserDTO $userDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @deprecated - Person creation is being consolidated within the PersonBundle. Please look there for this functionality
     * @return Response|View
     */
    public function createUserAction(UserDTO $userDTO, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return View::create(new Response($userDTO, [
                $validationErrors[0]->getMessage()
            ]), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $this->ensureEBIorCoordinatorUserAccess($userDTO->getCampusid());
            // Function marked as deprecated in the service. To be moved within PersonBundle.
            $usersResponse = $this->usersService->createUser($userDTO);
            return new Response($usersResponse);
        }
    }

    /**
     * Updates a user.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Users",
     * input = "Synapse\RestBundle\Entity\UserDTO",
     * output = "Synapse\RestBundle\Entity\UserDTO",
     * section = "Users",
     * statusCodes = {
     *                  201 = "User was updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/{userId}", requirements={"_format"="json","userId" = "\d+"})
     * @Rest\View(statusCode=201)
     *
     * @param UserDTO $userDTO
     * @param int $userId
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updateUserAction($userId, UserDTO $userDTO, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return View::create(new Response($userDTO, [
                $validationErrors[0]->getMessage()
            ]), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            if ($userDTO->getType() != 'switch-campus') {
                $this->ensureEBIorCoordinatorUserAccess($userDTO->getCampusId());
            }

            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();
            // Function marked as deprecated in the service. To be moved within PersonBundle.
            $usersResponse = $this->usersService->updateUser($userDTO, $userId, $organizationId, $loggedInUserId);
            return new Response($usersResponse);
        }
    }

    /**
     * Gets all users.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get all Users",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Users",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/students", requirements={"_format"="json"})
     * @QueryParam(name="filter", description="filter the user based on email, title and Id")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getStudentsAction(ParamFetcher $paramFetcher)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $filter = $paramFetcher->get('filter');
        $usersResponse = $this->usersService->getHierarchyStudents($organizationId, $filter);
        return new Response($usersResponse);
    }

    /**
     * Gets a list organization users based on type.
     *
     * @ApiDoc(
     * resource = true,
     * description = "List Organization Users",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Users",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="type", description="Get the user list based on filter types coordinator|faculty|student")
     * @QueryParam(name="active_filter", requirements="(all|active|inactive)",  default="all", description="indicates if the faculty is active or not")
     * @QueryParam(name="list", description="indicates the type of users to be listed, staff|all ")
     * @QueryParam(name="org_id", requirements="\d+", description="Campus Id to get the users list")
     * @QueryParam(name="page_no", strict=false, description="Page number")
     * @QueryParam(name="offset", strict=false, description="Offset value")
     * @QueryParam(name="search_text", strict=false, description="search user by text")
     * @QueryParam(name="exclude", strict=false, description="indicate if any user type should be excluded from the result set")
     * @QueryParam(name="participant_filter", strict=false, description="all - it will list all students, participants - will list only participant students, non-participants - will display non participant students.")
     * @QueryParam(name="sort_by", strict=false, description="indicate the order of search results")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listOrganizationUsersAction(ParamFetcher $paramFetcher)
    {

        $userType = $paramFetcher->get('type');
        $list = $paramFetcher->get('list');
        if ($userType == 'coordinator') {
            $this->ensureAccess([
                self::PERM_COORINDATOR_SETUP
            ]);
        }

        $loggedInUserId = $this->getLoggedInUserId();
        $checkAccessToOrganization = false;
        if ($loggedInUserId > 0) {
            $checkAccessToOrganization = true;
        }

        $activeFilter =  $paramFetcher->get('active_filter');
        $organizationId = $paramFetcher->get('org_id');
        $exclude = $paramFetcher->get('exclude');
        $searchText = $paramFetcher->get('search_text');
        $participantFilter = $paramFetcher->get('participant_filter');
        $sortBy = $paramFetcher->get('sort_by');
        $pageNumber = $paramFetcher->get('page_no');
        $offset = $paramFetcher->get('offset');

        if ($userType == "userlist") {
            $usersList = $this->usersService->getAllUserList($organizationId, $list);
        } else {
            $usersList = $this->usersService->getUsers($organizationId, $userType, $exclude, $searchText, $participantFilter, $sortBy, $pageNumber, $offset, $checkAccessToOrganization, $activeFilter);
        }
        return new Response($usersList);
    }

    /**
     * Get single user based on contact details.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Single User",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Users",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{userId}", requirements={"_format"="json"})
     * @QueryParam(name="campus-id", requirements="\d+", description="Campus Id", strict=true)
     * @QueryParam(name="user_type", default="", description="Get the User Details Based on Types Faculty|Student")
     * @Rest\View(statusCode=200)
     *
     * @param int $userId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getSingleUserAction($userId, ParamFetcher $paramFetcher)
    {
        $campusId = $paramFetcher->get('campus-id');
        $userType = $paramFetcher->get('user_type');

        $userResponse = $this->usersService->getUser($userId, $campusId, $userType);
        return new Response($userResponse);
    }

    /**
     * Send invitation Link to single primarytier user | secondarytier user | campus user | standalone campus user
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Single User along with contact Details",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Users",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{campusId}/user/{userId}/sendlink", requirements={"_format"="json"})
     * @QueryParam(name="type", requirements="(primarytier|secondarytier|campus|standalone)", default="", description="Get the type Based on Filter Types primarytier|secondarytier|campus|standalone", strict=true)
     * @Rest\View(statusCode=200)
     *
     * @param int $campusId
     * @param int $userId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getSendInvitationAction($campusId, $userId, ParamFetcher $paramFetcher)
    {
        $this->ensureEBIorCoordinatorUserAccess($campusId);

        $sendResponse = $this->usersService->sendInvitation($campusId, $userId);
        return new Response($sendResponse);
    }

    /**
     * Deletes a user or tier user.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete User / Tier User",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Users",
     * statusCodes = {
     *                  204 = "User / tier user was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{userId}", requirements={"_format"="json"})
     * @QueryParam(name="campus-id", requirements="\d+", default="", description="campus Id")
     * @QueryParam(name="tier-level", description="Get the Tier Users for the tier level")
     * @QueryParam(name="type", description="Delete User Based on Filter Types coordinator|faculty|student")
     * @QueryParam(name="tier-id", requirements="\d+", default="", description="Tier Id")
     * @Rest\View(statusCode=204)
     *
     * @param int $userId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function deleteUserAction($userId, ParamFetcher $paramFetcher)
    {
        $campusId = $paramFetcher->get(UsersConstant::CAMPUS_I_D);
        $tierlevel = $paramFetcher->get(TierConstant::TIERLEVEL);
        $tierId = $paramFetcher->get(UsersConstant::TIER_ID);
        $type = $paramFetcher->get(UsersConstant::TYPE);

        // Introducing simple logic check to apply proper permission in the system and handle it in controller
        if (! empty($tierlevel) && ! empty($tierId)) {
            // check ebi access
            $this->ensureAdminAccess();
            $deleteUser = $this->tierUsersService->deleteTierUser($userId, $tierlevel, $tierId);
        } else {
            // check coordinator org access
            $this->ensureEBIorCoordinatorUserAccess($campusId);
            $deleteUser = $this->usersService->deleteUser($userId, $campusId, $type);
        }
        return new Response($deleteUser);
    }

    /**
     * List the dashboard for tier users.
     *
     * @ApiDoc(
     * resource = true,
     * description = "List Tier Users Dashboard",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Users",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/dashboard", requirements={"_format"="json"})
     * @QueryParam(name="campus-id", requirements="\d+", default="", description="campus Id")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function listTierUsersDashboardAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $tierUsersDashboard = $this->usersService->listTierUserDashboard($loggedInUserId);
        return new Response($tierUsersDashboard);
    }

    /**
     * Create a new tier user.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Tier User",
     * input = "Synapse\RestBundle\Entity\UserDTO",
     * output = "Synapse\RestBundle\Entity\UserDTO",
     * section = "Users",
     * statusCodes = {
     *                  201 = "Tier User was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/tiers", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param UserDTO $userDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createTierUserAction(UserDTO $userDTO, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAdminAccess();
        if (count($validationErrors) > 0) {
            return View::create(new Response($userDTO, [
                $validationErrors[0]->getMessage()
            ]), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {

            $usersResponse = $this->usersService->createTierUser($userDTO);
            return new Response($usersResponse);
        }
    }

    /**
     * Updates a tier user.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Tier User",
     * input = "Synapse\RestBundle\Entity\UserDTO",
     * output = "Synapse\RestBundle\Entity\UserDTO",
     * section = "Users",
     * statusCodes = {
     *                  201 = "Tier User was updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/tiers", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param UserDTO $userDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updateTierUserAction(UserDTO $userDTO, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAdminAccess();
        if (count($validationErrors) > 0) {
            return View::create(new Response($userDTO, [
                $validationErrors[0]->getMessage()
            ]), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $usersResponse = $this->usersService->updateTierUser($userDTO);
            return new Response($usersResponse);
        }
    }

    /**
     * Gets a list of tier users.
     *
     * @ApiDoc(
     * resource = true,
     * description = "List Tier Users",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Users",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/list/{tierId}", requirements={"_format"="json"})
     * @QueryParam(name="tier-level", requirements="(primary|secondary)", strict=true, description="tier level")
     * @QueryParam(name="filter", description="filter the user based on email, title and Id")
     * @Rest\View(statusCode=200)
     *
     * @param int $tierId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listExistingUsersAction($tierId, ParamFetcher $paramFetcher)
    {
        $tierlevel = $paramFetcher->get(TierConstant::TIERLEVEL);
        $filter = $paramFetcher->get('filter');
        $tierUsers = $this->tierUsersService->listExistingUsers($tierId, $tierlevel, $filter);
        return new Response($tierUsers);
    }

    /**
     * Updates a coordinator user.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Tier User",
     * input = "Synapse\MultiCampusBundle\EntityDto\RoleDto",
     * output = "Synapse\MultiCampusBundle\EntityDto\RoleDto",
     * section = "Users",
     * statusCodes = {
     *                  201 = "Coordinator was updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/{userId}/role", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param int $userId
     * @param RoleDto $roleDto
     * @return Response
     */
    public function updateCoordinatorRoleAction($userId, RoleDto $roleDto)
    {
        $coordinatorRole = $this->tierUsersService->updateCoordinatorRole($userId, $roleDto);
        return new Response($coordinatorRole);
    }

    /**
     * Promotes a user that already exists.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Promote Existing User",
     * input = "Synapse\MultiCampusBundle\EntityDto\PromoteUserDto",
     * output = "Synapse\MultiCampusBundle\EntityDto\PromoteUserDto",
     * section = "Users",
     * statusCodes = {
     *                  201 = "User was promoted. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/promote", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param PromoteUserDto $promoteUserDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function promoteUserAction(PromoteUserDto $promoteUserDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAdminAccess();
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($promoteUserDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $userPromotion = $this->tierUsersService->promoteUserToTierUser($promoteUserDto);
            return new Response($userPromotion);
        }
    }

    /**
     * List of active campuses and tiers for the user.
     *
     * @ApiDoc(
     * resource = true,
     * description = "List User Tiers and Campuses",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Users",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/institutions", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function listActiveCampusTiersforUserAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $listActiveCampusTiers = $this->tierUsersService->listActiveCampusTiersforUser($loggedInUserId);
        return new Response($listActiveCampusTiers);
    }

    /**
     * List all user conflicts.
     *
     * @ApiDoc(
     * resource = true,
     * description = "List User Conflicts",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Users",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/conflicts", requirements={"_format"="json"})
     * @QueryParam(name="source_id", description="source campus ID")
     * @QueryParam(name="destination_id", description="Destination campus ID")
     * @QueryParam(name="type", requirements="(all|conflicts|new|hierarchy|standalone)", description="Hierarchy Type")
     * @QueryParam(name="viewmode", default ="json", strict=true, requirements="(json|csv)", description="viewmode")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function userConflictsAction(ParamFetcher $paramFetcher)
    {
        $this->ensureAdminAccess();
        $sourceId = $paramFetcher->get('source_id');
        $destinationId = $paramFetcher->get('destination_id');
        $viewmode = $paramFetcher->get('viewmode');

        $userConflicts = $this->userConflictsService->listConflicts($sourceId, $destinationId, $viewmode);
        if ($viewmode == 'csv') {
            return new Response([
                'URL' => $userConflicts
            ]);
        }
        return new Response($userConflicts);
    }

    /**
     * Send a bulk invitation to all coordinators who have not yet logged into the system in the current Academic Year.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Send Bulk Coordinator Invitation",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Users",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{campusId}/bulksendlink", requirements={"_format"="json"})
     * @QueryParam(name="type", requirements="(coordinator|faculty)", description="Get the type Based on UserTypes coordinator|faculty", strict=true)
     * @Rest\View(statusCode=200)
     *
     * @param int $campusId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function bulkUserInviteAction($campusId, ParamFetcher $paramFetcher)
    {
        $type = $paramFetcher->get('type');
        $userInviteResp = $this->usersService->bulkUserInvite($campusId, $type);
        return new Response($userInviteResp);
    }

    /**
     * List primary tier details, all the secondary tier details, and all the campuses within.
     *
     * @ApiDoc(
     * resource = true,
     * description = "List Primary Tier Details",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Users",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/tiers/{primaryTierId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $primaryTierId
     * @return Response
     */
    public function listPrimaryTierDetailsAction($primaryTierId)
    {
        $tierDetails = $this->usersService->listPrimaryTierDetails($primaryTierId);
        return new Response($tierDetails);
    }

    /**
     * List All Users for the primary tier id.
     *
     * @ApiDoc(
     * resource = true,
     * description = "List Primary Tier Users",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Users",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/multicampus/{primaryTierId}", requirements={"_format"="json"})
     * @QueryParam(name="tier-id", description="Get Secondary Tier id")
     * @QueryParam(name="campus-id", description="Get the Campus id")
     * @Rest\View(statusCode=200)
     *
     * @param int $primaryTierId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listUserDetailsAction($primaryTierId, ParamFetcher $paramFetcher)
    {
        $secondaryTierId = $paramFetcher->get(UsersConstant::TIER_ID);
        $campusId = $paramFetcher->get(UsersConstant::CAMPUS_I_D);
        $tierDetails = $this->usersService->listUserDetails($primaryTierId, $secondaryTierId, $campusId);
        return new Response($tierDetails);
    }

    /**
     * Expires an user's AccessToken.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Logout user",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Users",
     * statusCodes = {
     *                  204 = "Access token expired. No representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/logout", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     */
	public function expireTokenAction()
	{
        $loggedInUserId = $this->getLoggedInUserId();
        if (!$this->personBeingProxiedInAs) {
            $token = $this->get('security.context')->getToken()->getToken();
            $this->tokenService->expireToken($loggedInUserId, $token);
        }
	}

    /**
     * Returns a list of all users for that organization. Admin site API.
     *
     * @ApiDoc(
     * resource = true,
     * description = "List all Users",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Users",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/listusers/{orgId}", requirements={"_format"="json"})
     * @QueryParam(name="search",  strict=true, description="search text")
     * @QueryParam(name="page_no", strict=false, description="page_no")
     * @QueryParam(name="offset", strict=false, description="offset")
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listUsersAction($orgId, ParamFetcher $paramFetcher)
    {
        $searchText = $paramFetcher->get('search');
        $page = $paramFetcher->get('page_no');
        $offset = $paramFetcher->get('offset');
        $organizationId = $this->getLoggedInUserOrganizationId();

        if($organizationId != -1) {
            throw new SynapseValidationException('This API is only usable from the Mapworks administrative site.');
        }

        $users = $this->usersService->getAdminSiteUserSearchResult($orgId, $searchText, $page, $offset);
        return new Response($users);
    }


    /**
     * Regenerate authorization code for service account
     *
     * @ApiDoc(
     * section = "Users",
     * resource = true,
     * description = "Regenerate Authorization code for service accounts",
     * input = "Synapse\RestBundle\Entity\AuthCodeDto",
     * output = "Synapse\RestBundle\Entity\AuthCodeDto",
     * statusCodes = {
     *    201 = "Access token generated. Representation of resource(s) was returned",
     *    400 = "Validation error has occurred",
     *    403 = "Throw an access denied exception,Returned when user does not have permissions",
     *    500 = "There were errors either in the body of the request or an internal server error",
     *    504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/serviceaccount/authcode", requirements={"_format"="json"})
     *
     * @param AuthCodeDto $authCodeDto
     * @return Response
     */
    public function regenerateAuthCodeAction(AuthCodeDto $authCodeDto)
    {
        $loggedInUser = $this->getLoggedInUser();
        $isCoordinator = $this->rbacManager->checkIfCoordinator($loggedInUser);
        if (!$isCoordinator) {
            throw new AccessDeniedException();
        } else {
            $organizationId = $this->getLoggedInUserOrganizationId();
            $authCodeDto = $this->authCodeService->reGenerateAuthorizationCode($authCodeDto, $organizationId);
            return new Response($authCodeDto);
        }
    }


    /**
     * Delete Service account
     *
     * @ApiDoc(
     * section="Users",
     * resource = true,
     * description = "Deletes service account and invalidates the client and authorization code",
     * output = "Synapse\RestBundle\Entity\Response",
     * statusCodes = {
     *    204 = "Returned when successful",
     *    400 = "Validation errors have occurred or invalid request",
     *    403 = "Throw an access denied exception,Returned when user does not have permissions",
     *    500 = "There were errors with the body of the request or an internal server error.",
     *    504 = "Request has timed out. Please re-try."
     * }
     * )
     *
     * @Rest\DELETE("/serviceaccount/{serviceAccountId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param integer $serviceAccountId
     */
    public function deleteServiceAccountAction($serviceAccountId)
    {
        $loggedInUser = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $isCoordinator = $this->rbacManager->checkIfCoordinator($loggedInUser);
        if (!$isCoordinator) {
            throw new AccessDeniedException();
        } else {
            $this->usersService->deleteServiceAccount($serviceAccountId, $organizationId);
        }
    }

    /**
     * Update Participation Status For Student
     *
     * @ApiDoc(
     * resource = true,
     * description = "Updates Participation Status for Student",
     * input = "Synapse\CoreBundle\DTO\StudentParticipationDTO",
     * output = "Synapse\CoreBundle\DTO\StudentParticipationDTO",
     * section = "Person",
     * statusCodes = {
     *    204 = "Returned when successful",
     *    400 = "Validation errors have occurred or invalid request",
     *    403 = "Throw an access denied exception,Returned when user does not have permissions",
     *    500 = "There errors with the body of the request or an internal server error.",
     *    504 = "Request has timed out. Please re-try."
     * },
     * )
     *
     * @Rest\Put("/participation", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param StudentParticipationDTO $studentParticipationDTO
     * @return Response
     */
    public function updateParticipationStatusForStudentAction(StudentParticipationDTO $studentParticipationDTO)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $resultDTO = $this->usersService->updateStudentParticipation($loggedInUserId, $organizationId, $studentParticipationDTO);
        return new Response($resultDTO);
    }
}