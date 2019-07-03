<?php

namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\CoreBundle\Service\Impl\NotificationChannelService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Response;


/**
 * Class NotificationChannelController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/notificationchannel")
 */
class NotificationChannelController extends AbstractAuthController
{

    /**
     * @var NotificationChannelService
     *
     *      @DI\Inject(NotificationChannelService::SERVICE_KEY)
     */
    private $notificationChannelService;

    /**
     * Generates a unique channel name for the logged-in user. This is used by the UI to create a channel between itself and the push server.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Generate Channel Name",
     * section = "Notification Push",
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
     * @Rest\GET("/channel", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function registerChannelAction()
    {
        $loggedInUser = $this->getLoggedInUser();
        $channelName = $this->notificationChannelService->registerChannel($loggedInUser);
        return new Response(['channel_name' => $channelName]);

    }

    /**
     * Un-registers a channel when a user logs out.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Un-Register User Channel",
     * section = "Notification Push",
     * statusCodes = {
     *                  204 = "User channel was un-registered. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\DELETE("/unregisterchannel/{channelName}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param string $channelName
     */
    public function unRegisterChannelAction($channelName)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $this->notificationChannelService->unRegisterChannel($loggedInUserId, $organizationId, $channelName);
        // NO RETURN!!!
    }

    /**
     * Get notification channel configuration details.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get notification channel configuration details",
     * section="Push Notification",
     * statusCodes = {
     *    201 = "Request was successful. Representation of resources was returned.",
     *    400 = "Validation error has occurred.",
     *    403 = "Throw an access denied exception,Returned when user does not have permissions",
     *    404 = "Not Found",
     *    500 = "There were errors either in the body of the request or an internal server error",
     *    504 = "Request has timed out."
     * }
     * )
     * @Rest\GET("/configurations", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getNotificationChannelConfigurationsAction()
    {
        $pushNotificationSetupValues = $this->notificationChannelService->getNotificationChannelConfigurations();
        return new Response($pushNotificationSetupValues);
    }
}