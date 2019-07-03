<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\OfficeHoursService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\OfficeHoursDto;
use Synapse\RestBundle\Entity\Response;

/**
 * Class BookingController
 *
 * @package Synapse\RestBundle\Controller
 *
 *          @Rest\Prefix("booking")
 */
class BookingController extends AbstractAuthController
{


    /**
     * @var OfficeHoursService
     *
     *      @DI\Inject(OfficeHoursService::SERVICE_KEY)
     */
    private $officeHoursService;

    /**
     * @var Serializer
     *
     *      @DI\Inject(SynapseConstant::JMS_SERIALIZER_CLASS_KEY)
     */
    private $serializer;

    /**
     * Creates an office hour (slot_type 'I') or office hours series (slot_type 'S') based on the slot type specified in the POST body.
     *
     * @ApiDoc(
     *          resource = true,
     *          description = "Create an office hour (Slot type 'I') / office hour series (Slot type 'S')",
     *          input = "Synapse\RestBundle\Entity\OfficeHoursDto",
     *          output = "Synapse\RestBundle\Entity\OfficeHoursDto",
     *          section = "Office Hours",
     *          statusCodes = {
     *                          201 = "Resource(s) created. Representation of resource(s) was returned.",
     *                          400 = "Validation error has occurred",
     *                          403 = "Access denied",
     *                          404 = "Not found",
     *                          500 = "There was either errors with the body of the request or an internal server error.",
     *                          504 = "Request has timed out. Please re-try."
     *                        },
     *
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param OfficeHoursDto $officeHoursDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createOfficeHoursAction(OfficeHoursDto $officeHoursDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($officeHoursDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {

            $personId = $officeHoursDto->getPersonId();
            // Verifying passed personId through API to loggedInPersonId
            $this->rbacManager->validateUserAsAuthorizedAppointmentUser($personId);

            if ($officeHoursDto->getSlotType() == "I") {
                $officeHours = $this->officeHoursService->createOfficeHour($officeHoursDto);
            } else {
                $officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
            }

            return new Response($officeHours);
        }
    }

    /**
     * Edits an office hour (slot_type 'I') or office hours series (slot_type 'S') based on the slot type specified in the PUT body.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Office Hours",
     * input = "Synapse\RestBundle\Entity\OfficeHoursDto",
     * output = "Synapse\RestBundle\Entity\OfficeHoursDto",
     * section = "Office Hours",
     * statusCodes = {
     *                  201 = "Resource(s) updated. Representation of resource(s) was returned.",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param OfficeHoursDto $officeHoursDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function editOfficeHoursAction(OfficeHoursDto $officeHoursDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($officeHoursDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {

            $personId = $officeHoursDto->getPersonId();
            $loggedInUserId = $this->getLoggedInUserId();
            // Verifying passed personId through API to loggedInPersonId
            $this->rbacManager->validateUserAsAuthorizedAppointmentUser($personId);

            if ($officeHoursDto->getSlotType() == "I") {
                $officeHours = $this->officeHoursService->editOfficeHour($officeHoursDto);
            } else {
                $officeHours = $this->officeHoursService->editOfficeHourSeries($officeHoursDto, $loggedInUserId);
            }

            return new Response($officeHours);
        }
    }

    /**
     * Gets an Office Hour series
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Series of Office Hours",
     * output = "Synapse\RestBundle\Entity\OfficeHoursDto",
     * section = "Office Hours",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="type", strict=true, description="Office Hour Type")
     * @QueryParam(name="id", requirements="\d+", strict=true, description="Office Hour Id")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getOfficeHourSeriesAction(ParamFetcher $paramFetcher)
    {
        $type = $paramFetcher->get('type');
        $id = (int) $paramFetcher->get('id');

        if ($type == 'S') {
            $officehours = $this->officeHoursService->getOfficeHourSeries($id);
        } else {
            $officehours = $this->officeHoursService->getOfficeHour($id);
        }
        return new Response($officehours);
    }

    /**
     * Cancel office hour based on office_hour_id and person_id
     *
     * @ApiDoc(
     * resource = true,
     * description = "Cancel existing office hour",
     * section = "Office Hours",
     * statusCodes = {
     *                  204 = "Office hours were canceled. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("", requirements={"_format"="json"})
     * @QueryParam(name="person", strict=true, description="Staff or Proxy User")
     * @QueryParam(name="isproxy", strict=true, description="Check Person if Proxy or not")
     * @QueryParam(name="id", requirements="\d+", strict=true, description="Office Hour Id")
     * @Rest\View(statusCode=204)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function cancelOfficeHourAction(ParamFetcher $paramFetcher)
    {
        $person = (int) $paramFetcher->get('person');
        $isProxy = (bool) $paramFetcher->get('isproxy');
        $id = (int) $paramFetcher->get('id');

        // Verifying passed personId through API to loggedInPersonId
        $this->rbacManager->validateUserAsAuthorizedAppointmentUser($person);

        $this->officeHoursService->cancel($person, $isProxy, $id);
    }

    /**
     * Deletes a series of office hours
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Series of Office Hours",
     * section = "Office Hours",
     * statusCodes = {
     *                  204 = "Series of office hours was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/series/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param integer $id
     * @return Response
     */
    public function deleteOfficeHourSeriesAction($id)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        if ($organizationId === - 1 && $this->container->has('proxy_user')) {
            $organizationId = $this->get('proxy_user')->getOrganization()->getId();
        }
        $this->officeHoursService->deleteOfficeHourSeries($id, $organizationId, $loggedInUserId);
    }

    /**
     * Delete stand-alone office hour
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete stand-alone office hour",
     * section = "Office Hours",
     * statusCodes = {
     *                  204 = "Office hour was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/standalone/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param integer $id
     */
    public function deleteOfficeHourAction($id)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        if ($organizationId === - 1 && $this->container->has('proxy_user')) {
            $organizationId = $this->get('proxy_user')->getOrganization()->getId();
        }

        $this->officeHoursService->deleteOfficeHour($id, $organizationId);
    }
}
?>