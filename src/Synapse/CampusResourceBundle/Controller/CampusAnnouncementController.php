<?php
namespace Synapse\CampusResourceBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CampusResourceBundle\EntityDto\CampusAnnouncementDeleteDto;
use Synapse\CampusResourceBundle\EntityDto\CampusAnnouncementDto;
use Synapse\CampusResourceBundle\Service\Impl\CampusAnnouncementService;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response;

/**
 * Class CampusAnnouncementController
 *
 * @package Synapse\CampusResourceBundle\Controller
 *         
 *          @Rest\Prefix("/campusannouncements")
 */
class CampusAnnouncementController extends AbstractAuthController
{

    /**
     * @var Logger
     *
     * @DI\Inject("monolog.logger.api")
     */
    private $apiLogger;

    /**
     * @var CampusAnnouncementService
     *     
     * @DI\Inject("campusannouncement_service")
     */
    private $campusAnnouncementService;

    /**
     * Creates a new Campus Announcement
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Campus Announcement",
     * input = "Synapse\CampusResourceBundle\EntityDto\CampusAnnouncementDto",
     * output = "Synapse\CampusResourceBundle\EntityDto\CampusAnnouncementDto",
     * section = "Campus Announcements",
     * statusCodes = {
     *  201 = "Campus Announcement was created. Representation of resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param CampusAnnouncementDto $campusAnnouncementDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createCampusAnnouncementAction(CampusAnnouncementDto $campusAnnouncementDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            
            return View::create(new Response($campusAnnouncementDto, [
                $validationErrors[0]->getMessage()
            ]), 400);
        } else {
            $loggedInUser = $this->getUser();
            $loggedInUserId = $loggedInUser->getId();
            $organizationId = $loggedInUser->getOrganization()->getId();
            $requestJSON = $this->get("request")->getContent();

            $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; Request: $requestJSON");

            $campAnnouncementCreateResponse = $this->campusAnnouncementService->createCampusAnnouncement($campusAnnouncementDto, $loggedInUser);

            return new Response($campAnnouncementCreateResponse);
        }
    }

    /**
     * Returns a list of Campus Announcements filtered by the type of announcement, i.e.[bell, announcement-banner]
     *
     * @ApiDoc(
     * resource = true,
     * description = "List Campus Announcements",
     * section = "Campus Announcements",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("",requirements={"_format"="json"})
     * @QueryParam(name="type", default ="scheduled", strict=false, description="type")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listCampusAnnouncementsAction(ParamFetcher $paramFetcher)
    {
        $type = $paramFetcher->get('type');
        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $organizationId = $loggedInUser->getOrganization()->getId();
        $requestJSON = $this->get("request")->getContent();

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; AnnouncementType: $type; Request: $requestJSON");

        $campusAnnouncements = $this->campusAnnouncementService->listCampusAnnouncements($type, $loggedInUser, $organizationId);

        return new Response($campusAnnouncements);
    }

    /**
     * Edits a specific Campus Announcement that has already been created
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Campus Announcement",
     * input = "Synapse\CampusResourceBundle\EntityDto\CampusAnnouncementDto",
     * output = "Synapse\CampusResourceBundle\EntityDto\CampusAnnouncementDto",
     * section = "Campus Announcements",
     * statusCodes = {
     *  204 = "Campus Announcement was updated. No representation of resource was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param CampusAnnouncementDto $campusAnnouncementDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function editCampusAnnouncementAction(CampusAnnouncementDto $campusAnnouncementDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($campusAnnouncementDto, $errors), 400);
        } else {
            $loggedInUser = $this->getUser();
            $loggedInUserId = $loggedInUser->getId();
            $organizationId = $loggedInUser->getOrganization()->getId();
            $requestJSON = $this->get("request")->getContent();

            $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; Request: $requestJSON");

            $campusAnnouncement = $this->campusAnnouncementService->editCampusAnnouncement($campusAnnouncementDto, $loggedInUser);

            return new Response($campusAnnouncement);
        }
    }

    /**
     * Deletes a specific Campus Announcement
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Campus Announcement",
     * section = "Campus Announcements",
     * statusCodes = {
     *  204 = "Campus Announcement was deleted. No representation of resource was returned",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $id
     * @return Response
     */
    public function deleteCampusAnnouncementAction($id)
    {
        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $organizationId = $loggedInUser->getOrganization()->getId();

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; CampusAnnouncementId: $id; ");

        $campusAnnouncement = $this->campusAnnouncementService->deleteCampusAnnouncement($id, $loggedInUser, $organizationId);

        return new Response($campusAnnouncement);
    }

    /**
     * Gets information for a specific Campus Announcement when a user selects it from the announcements list
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Campus Announcement",
     * section = "Campus Announcements",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{id}", requirements={"id"="\d+", "_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getCampusAnnouncementAction($id)
    {
        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $organizationId = $loggedInUser->getOrganization()->getId();

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; CampusAnnouncementId: $id; ");

        $campusAnnouncement = $this->campusAnnouncementService->getCampusAnnouncement($id, $loggedInUser, $organizationId);

        return new Response($campusAnnouncement);
    }

    /**
     * Cancels a Campus Announcement for a specific user. Cancellation occurs when a user views a campus announcement as 'Viewed'[boolean=1].
     *
     * @ApiDoc(
     * resource = true,
     * description = "Cancel Campus Announcement",
     * input = "Synapse\CampusResourceBundle\EntityDto\CampusAnnouncementDto",
     * output = "Synapse\CampusResourceBundle\EntityDto\CampusAnnouncementDto",
     * section = "Campus Announcements",
     * statusCodes = {
     *  201 = "Campus Announcement was deleted(canceled). Representation of resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\PUT("/{campusAnnouncementId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @return Response | View
     */
    public function cancelCampusAnnouncementAction(CampusAnnouncementDeleteDto $campusAnnouncementDto, ConstraintViolationListInterface $validationErrors, $campusAnnouncementId)
    {
        if (count($validationErrors) > 0) {
            return View::create(new Response($campusAnnouncementDto, [$validationErrors[0]->getMessage()]), 400);
        }

        $loggedInUser = $this->getUser();
        $displayType = $campusAnnouncementDto->getDisplayType();

        $alertNotifications = $this->campusAnnouncementService->markOrgAnnouncementAsRead($loggedInUser,$campusAnnouncementId, $displayType);

        return new Response($alertNotifications);
    }

    /**
     * List Campus Announcement Banner
     *
     * @ApiDoc(
     * resource = true,
     * description = "List Campus Announcement Banner",
     * section = "Campus Announcements",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/banner",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listCampusAnnouncementBannerAction(ParamFetcher $paramFetcher)
    {
    	$loggedInUser = $this->getUser();
    	$orgId = $loggedInUser->getOrganization()->getId();
    	$campusAnnouncements = $this->campusAnnouncementService->listBannerOrgAnnouncements($loggedInUser->getId(), $orgId);
    	return new Response($campusAnnouncements);
    }
}
