<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Response;

/**
 * Class AlertNotificationsController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/notification")
 */
class AlertNotificationsController extends AbstractAuthController
{

    /**
     * @var AlertNotificationsService
     *
     *      @DI\Inject(AlertNotificationsService::SERVICE_KEY)
     */
    private $alertNotificationsService;


    /**
     * Gets the 100 most recent notifications for the logged in user.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Gets the notifications for the user",
     * output = "Synapse\RestBundle\Entity\AlertNotificationsDto",
     * section = "Alert Notification",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Get("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function alertNotificationsAction(ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $alertNotifications = $this->alertNotificationsService->listNotifications($loggedInUserId);
        return new Response($alertNotifications);
    }

    /**
     * Delete alert notification.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Alert Notification",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Notification",
     * statusCodes = {
     *                  204 = "Resource(s) deleted. Representation of resource(s) was not returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("", requirements={"_format"="json"})
     * @QueryParam(name="alert-id", strict=true, description="Alert Id")
     * @Rest\View(statusCode=204)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @deprecated API looks to no longer be used.
     */
    public function deleteNotificationViewStatusAction(ParamFetcher $paramFetcher)
    {
        $alertNotificationId = $paramFetcher->get('alert-id');
        $alertNotification = $this->alertNotificationsService->deleteNotificationViewStatus($alertNotificationId);
        return new Response($alertNotification);
    }


    /**
     * Mark all unseen notifications as seen for the loggedInUser.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Unseen Notifications",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Notification",
     * statusCodes = {
     *                  204 = "Resource(s) updated. Representation of resource(s) was not returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/bulk/seen", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @return Response
     */
    public function updateAllUnseenNotificationsAction()
    {
        $loggedInUserId = $this->getUser()->getId();
        if (!$this->personBeingProxiedInAs) {
            $this->alertNotificationsService->updateAllUnseenNotificationsAsSeenForUser($loggedInUserId);
        }
        return new Response("");

    }

    /**
     * Mark all unread notifications as seen and read for the loggedInUser.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Unread Notifications",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Notification",
     * statusCodes = {
     *                  204 = "Resource(s) updated. Representation of resource(s) was not returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/bulk/read", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @return Response
     */
    public function updateAllUnreadNotificationAction()
    {
        $loggedInUserId = $this->getUser()->getId();
        if (!$this->personBeingProxiedInAs) {
            $this->alertNotificationsService->updateAllUnreadNotificationsAsReadForUser($loggedInUserId);
        }
        return new Response("");
    }


    /**
     * Marks an individual academic update as read.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Notification Read Status",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Notification",
     * statusCodes = {
     *                  204 = "Resource(s) updated. Representation of resource(s) was not returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/read/{alertNotificationId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $alertNotificationId
     * @return Response
     */
    public function updateNotificationReadStatusAction($alertNotificationId)
    {
        $loggedInUser = $this->getLoggedInUser();

        $alertNotification = "";
        if (!$this->personBeingProxiedInAs) {
            $alertNotification = $this->alertNotificationsService->updateNotificationReadStatus($alertNotificationId, $loggedInUser);
        }
        return new Response($alertNotification);
    }


}