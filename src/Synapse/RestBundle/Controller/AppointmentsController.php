<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AppointmentsProxyService;
use Synapse\CoreBundle\Service\Impl\AppointmentsService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Entity\CalendarSharingDto;
use Synapse\RestBundle\Entity\Response;

/**
 * Class Appointments Controller
 *
 * @package Synapse\RestBundle\Controller
 *
 *          @Rest\Prefix("/appointments")
 *
 */
class AppointmentsController extends AbstractAuthController
{

    const SECURITY_CONTEXT = 'security.context';


    /**
     * @var AppointmentsProxyService
     *
     *      @DI\Inject(AppointmentsProxyService::SERVICE_KEY)
     */
    private $appointmentsProxyService;

    /**
     * @var AppointmentsService Appointments Service
     *
     *      @DI\Inject(AppointmentsService::SERVICE_KEY)
     */
    private $appointmentsService;

    /**
     * Creates (Books) an Appointment
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Appointment",
     * input = "Synapse\RestBundle\Entity\AppointmentsDto",
     * section = "Appointments",
     * statusCodes = {
     *                  201 = "Appointment was created. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/{orgId}/{personId}", requirements={"_format"="json","personId" = "\d+"})
     * @Rest\View(statusCode=201)
     *
     * @param AppointmentsDTO $appointmentsDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createAppointmentsAction(AppointmentsDto $appointmentsDto, ConstraintViolationListInterface $validationErrors)
    {
        $personId = $appointmentsDto->getPersonId();
        $appointmentPerms = [self::PERM_BOOKING_PUBLIC_CREATE, self::PERM_BOOKING_PRIVATE_CREATE, self::PERM_BOOKING_TEAMS_CREATE];
        $request = Request::createFromGlobals();
        $switchUser = $request->headers->get('switch-user');
        $checkDelegate = $this->checkDelegateAccessForStaff($personId);
        if($checkDelegate) {
            $this->checkUserPermission($appointmentPerms,$personId);
        }
        else if(!empty($switchUser)){
            $this->checkUserPermission($appointmentPerms,$personId);
        }
        else {
            $this->ensureAccess($appointmentPerms);
        }
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($appointmentsDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $this->appointmentsService->create($appointmentsDto);
            return new Response($appointmentsDto);
        }
    }

    /**
     * Creates a delegate user
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Delegate User",
     * input = "Synapse\RestBundle\Entity\CalendarSharingDto",
     * output = "Synapse\RestBundle\Entity\CalendarSharingDto",
     * section = "Appointments",
     * statusCodes = {
     *                  201 = "Delegate user was created. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/{orgId}/proxy", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param CalendarSharingDto $calendarSharingDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createDelegateUserAction(CalendarSharingDto $calendarSharingDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAccess([SynapseConstant::APPOINTMENTS_PUBLIC_CREATE_PERMISSION, SynapseConstant::APPOINTMENTS_PRIVATE_CREATE_PERMISSION, SynapseConstant::APPOINTMENTS_TEAM_CREATE_PERMISSION]);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($calendarSharingDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $personId = $calendarSharingDto->getPersonId();

            // Verifying passed personId through API to loggedInPersonId
            $this->rbacManager->validateUserAsAuthorizedAppointmentUser($personId);

            $delegateUser = $this->appointmentsProxyService->createDelegateUser($calendarSharingDto);
            return new Response($delegateUser);
        }
    }

    /**
     * Cancel a scheduled appointment
     *
     * @ApiDoc(
     * resource = true,
     * description = "Cancel Appointment",
     * section = "Appointments",
     * statusCodes = {
     *                  204 = "Appointment was canceled. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{orgId}/appointmentId", requirements={"_format"="json"})
     * @QueryParam(name="appointmentId", requirements="\d+", default ="", strict=true, description="Appointment Id")
     * @Rest\View(statusCode=204)
     *
     * @param integer $orgId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function cancelAppointmentAction($orgId, ParamFetcher $paramFetcher)
    {
        $appointmentId = $paramFetcher->get('appointmentId');
        $organizationId = $this->getLoggedInUserOrganizationId();
        $appointments = $this->appointmentsService->cancelAppointment($organizationId, $appointmentId);
        return new Response($appointments);
    }

    /**
     * Gets a list of selected proxy users
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Proxy Users",
     * section = "Appointments",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{organizationId}/proxySelected", requirements={"_format"="json"})
     * @QueryParam(name="user_id", description="user id proxy")
     * @Rest\View(statusCode=200)
     *
     * @param integer $organizationId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listSelectedProxyUsersAction($organizationId, ParamFetcher $paramFetcher)
    {
        $responseArray = array();
        $userId = $paramFetcher->get('user_id');
        if ($userId) {
            // Verifying passed personId through API to loggedInPersonId
            $this->rbacManager->validateUserAsAuthorizedAppointmentUser($userId);
            $responseArray = $this->appointmentsProxyService->listSelectedProxyUsers($organizationId, $userId);
        }

        return new Response($responseArray);
    }

    /**
     * Edit an appointment
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Appointments",
     * input = "Synapse\RestBundle\Entity\AppointmentsDto",
     * output = "Synapse\RestBundle\Entity\AppointmentsDto",
     * section = "Appointments",
     * statusCodes = {
     *                  201 = "Appointment was edited. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/{orgId}/{appointmentId}", requirements={"_format"="json","appointmentId" = "\d+"})
     * @Rest\View(statusCode=201)
     *
     * @param AppointmentsDto $appointmentsDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function editAppointAction(AppointmentsDto $appointmentsDto, ConstraintViolationListInterface $validationErrors)
    {
        $personId = $appointmentsDto->getPersonId();
        $appointmentPerms = [self::PERM_BOOKING_PUBLIC_CREATE, self::PERM_BOOKING_PRIVATE_CREATE, self::PERM_BOOKING_TEAMS_CREATE];
        $request = Request::createFromGlobals();
        $switchUser = $request->headers->get('switch-user');
        $checkDelegate = $this->checkDelegateAccessForStaff($personId);
        if($checkDelegate) {
            $this->checkUserPermission($appointmentPerms,$personId);
        }
        else if(!empty($switchUser)){
            $this->checkUserPermission($appointmentPerms,$personId);
        }
        else {
            $this->ensureAccess($appointmentPerms);
        }

        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($appointmentsDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $loggedInUserId = $this->getLoggedInUserId();
            $appointments = $this->appointmentsService->editAppointment($appointmentsDto,false,$loggedInUserId);

            return new Response($appointments);
        }
    }

    /**
     * Get a list of managed users
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Managed Users",
     * section = "Appointments",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{organizationId}/managedUsers", requirements={"_format"="json"})
     * @QueryParam(name="person_id_proxy", description="person id proxy")
     * @Rest\View(statusCode=200)
     *
     * @param integer $organizationId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listManagedUsersAction($organizationId, ParamFetcher $paramFetcher)
    {
        $responseArray = array();
        $proxyUserId = $paramFetcher->get('person_id_proxy');
        if ($proxyUserId) {
            // Verifying passed personId through API to loggedInPersonId
            $this->rbacManager->validateUserAsAuthorizedAppointmentUser($proxyUserId);

            $responseArray = $this->appointmentsProxyService->listManagedUsers($organizationId, $proxyUserId);
        }

        return new Response($responseArray);
    }

    /**
     * View an appointment
     *
     * @ApiDoc(
     * resource = true,
     * description = "View Appointments",
     * section = "Appointments",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\GET("/{appointmentId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @QueryParam(name="personId", requirements="\d+", strict=true, description="ID of the faculty whose appointment needs to be retrieved")
     * @param integer $appointmentId
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @throws AccessDeniedException
     */
    public function viewAppointmentAction($appointmentId, ParamFetcher $paramFetcher)
    {
        // Provided to allow looking up any of the possible view permissions in RBAC to make sure this user can view "something" on an appointment
        // Likely unnecessary due to the way permissions get re-checked in the viewAppointment service call.
        $allPermissionsForViewingAppointments = [
            SynapseConstant::APPOINTMENT_FEATURE_NAME_IN_RBAC . '-public-view',
            SynapseConstant::APPOINTMENT_FEATURE_NAME_IN_RBAC . '-private-view',
            SynapseConstant::APPOINTMENT_FEATURE_NAME_IN_RBAC . '-teams-view',
        ];

        $personId = $paramFetcher->get('personId');

        // The HTTP request headers will contain a "Switch-User" property with the user ID for the user being proxied as.
        $httpRequest = Request::createFromGlobals();
        $userIdForProxying = $httpRequest->headers->get('switch-user');

        // Delegate access comes from the original appointment owner. They may have allowed other people access to their calendar
        // This uses the security context to determine the currently logged in user.
        $loggedInUserHasDelegateAccessToProvidedUser = $this->checkDelegateAccessForStaff($personId);

        if ($loggedInUserHasDelegateAccessToProvidedUser) {
            // Light-weight (probably invalid) permissions check that the owner of the appointment has view permissions for appointments
            $this->checkUserPermission($allPermissionsForViewingAppointments, $personId);
        } else {
            // If the passed in person ID isn't a delegate, it's still possible it's a proxy user ID. Check for that
            if ($userIdForProxying) {
                if ($personId != $userIdForProxying) {
                    throw new AccessDeniedException("API Spoofing detected");
                }
            } else {
                // If it's not a delegate OR a proxy user ID, it has to be the logged in user ID, or something is wrong
                $loggedInPersonId = $this->getUser()->getId();
                if ($personId != $loggedInPersonId) {
                    throw new AccessDeniedException("API Spoofing detected");
                } else {
                    // Light-weight (probably invalid) checks for feature access and whether or not the person is active
                    $this->ensureAccess($allPermissionsForViewingAppointments);
                }
            }
        }
        $organizationId = $this->getLoggedInUserOrganizationId();
        $appointments = $this->appointmentsService->viewAppointment($organizationId, $personId, $appointmentId);

        return new Response($appointments);
    }

    /**
     * Get list of proxy managed users' current appointments by date range
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Proxy Managed Users Appointments",
     * section = "Appointments",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/proxy/{proxyPersonId}", requirements={"_format"="json"})
     * @QueryParam(name="frequency", description="frequency")
     * @QueryParam(name="managed_person_id", description="person_id")
     * @Rest\View(statusCode=200)
     *
     * @param integer $proxyPersonId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listProxyUsersAppointmentsAction($proxyPersonId, ParamFetcher $paramFetcher)
    {
        /**
         * ESPRJ-4416 - Delegate user do not have Permission to view Appointment.
         * So removing Permission check
         */
        $frequency = $paramFetcher->get('frequency');
        $managed_person_id = $paramFetcher->get('managed_person_id');
        $responseArray = $this->appointmentsProxyService->listProxyAppointments($proxyPersonId, $frequency, $managed_person_id);

        return new Response($responseArray);
    }

    /**
     * Save an appointment attendee.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Save Appointment Attendees",
     * input = "Synapse\RestBundle\Entity\AppointmentsDto",
     * output = "Synapse\RestBundle\Entity\AppointmentsDto",
     * section = "Appointments",
     * statusCodes = {
     *                  201 = "Appointment attendees were saved. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/attendees", requirements={"_format"="json","personId" = "\d+"})
     * @Rest\View(statusCode=201)
     *
     * @param AppointmentsDto $appointmentsDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function saveAppointmentAction(AppointmentsDto $appointmentsDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($appointmentsDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $loggedInUserId = $this->getLoggedInUserId();
            $appointmentPersonId = $appointmentsDto->getPersonId();
            $allPermissionsForCreateAppointments = [
                SynapseConstant::APPOINTMENT_FEATURE_NAME_IN_RBAC . '-public-create',
                SynapseConstant::APPOINTMENT_FEATURE_NAME_IN_RBAC . '-private-create',
                SynapseConstant::APPOINTMENT_FEATURE_NAME_IN_RBAC . '-teams-create',
            ];

            $httpRequest = Request::createFromGlobals();
            $userIdForProxy = $httpRequest->headers->get('switch-user');

            if ($appointmentPersonId == $loggedInUserId) {
                // the logged in user is same as appointmentPersonId, check his permissions
                $this->ensureAccess($allPermissionsForCreateAppointments);
            } else if ($userIdForProxy) {
                // check that the switch user has appointment create permission
                $this->checkUserPermission($allPermissionsForCreateAppointments, $userIdForProxy);
            } else {
                // Delegate access check
                $loggedInUserHasDelegateAccessToProvidedUser = $this->checkDelegateAccessForStaff($appointmentPersonId);
                if ($loggedInUserHasDelegateAccessToProvidedUser) {
                    // check that the owner of the appointment has create permission
                    $this->checkUserPermission($allPermissionsForCreateAppointments, $appointmentPersonId);
                } else {
                    $this->ensureAccess($allPermissionsForCreateAppointments);
                }
            }
            $appointments = $this->appointmentsService->saveAppointmentAttendees($appointmentsDto);

            return new Response($appointments);
        }
    }

    /**
     * Get user appointments
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get User Appointments",
     * section = "Appointments",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{organizationId}/{personId}", defaults={"personId" = -1},requirements={"_format"="json"})
     * @QueryParam(name="time_period", description="time_period")
     * @Rest\View(statusCode=200)
     *
     * @param integer $organizationId
     * @param integer $personId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getAppointmentsByUserAction($organizationId, $personId, ParamFetcher $paramFetcher)
    {
        // Verifying passed personId through API to loggedInPersonId
        $this->rbacManager->validateUserAsAuthorizedAppointmentUser($personId);

        $timePeriod = $paramFetcher->get('time_period');
        $responseArray = $this->appointmentsService->getAppointmentsByUser($organizationId, $personId, $timePeriod);

        return new Response($responseArray);
    }

    /**
     * View a list of today's appointments
     *
     * @ApiDoc(
     * resource = true,
     * description = "View Today's appointment",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\GET("", requirements={"_format"="json"})
     * @QueryParam(name="filter", description="filter")
     * @QueryParam(name="timezone", description="timezone")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function viewTodayAppointmentAction(ParamFetcher $paramFetcher)
    {
        $filter = $paramFetcher->get('filter');
        $personId = $this->get(self::SECURITY_CONTEXT)
            ->getToken()
            ->getUser()
            ->getId();
        $timezone = trim($paramFetcher->get('timezone'));
        $organizationId = $this->getLoggedInUserOrganizationId();
        $appointments = $this->appointmentsService->viewTodayAppointment($filter, $organizationId, $personId, $timezone);

        return new Response($appointments);
    }

    /**
     * Checks Mapworks event conflicting with any external calendar events.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Checks External/Internal Calendar Conflicts",
     * input = "Synapse\RestBundle\Entity\AppointmentsDto",
     * statusCodes = {
     *                  201 = "Check was completed. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/conflicts", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param AppointmentsDto $appointmentsDto
     * @return Response
     */
    public function checkAppointmentConflictWithExternalCalendarAction(AppointmentsDto $appointmentsDto)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $isConflictedFlag = $this->appointmentsService->checkExternalCalendarForAppointmentConflict($appointmentsDto, $loggedInUserId, $organizationId);

        return new Response(["is_conflicted" => $isConflictedFlag]);
    }

}
