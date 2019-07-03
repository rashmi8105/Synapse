<?php
namespace Synapse\CalendarBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Synapse\CalendarBundle\Service\Impl\CronofyWrapperService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Controller\AbstractAuthController;

/**
 * Class CronofyController
 *
 * @package Synapse\CalendarBundle\Controller
 *
 * @Rest\Prefix("/cronofy")
 */
class CronofyController
{

    /**
     * @var CronofyWrapperService
     *
     *      @DI\Inject(CronofyWrapperService::SERVICE_KEY)
     */
    private $cronofyWrapperService;

    /**
     * Creates the push notification when there is a change in external calendar.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Cronofy Push Notification",
     * section = "Cronofy Calendar Integration",
     * statusCodes = {
     *                  204 = "Push notification sent. No representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/notifications/{userId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $userId - User ID.
     * @param Request $request
     */
    public function cronofyPushNotificationAction($userId, Request $request)
    {
        $pushNotificationResponse = $request->request->all();
        $this->cronofyWrapperService->updatePushNotificationToUser($userId, $pushNotificationResponse);
    }

}
