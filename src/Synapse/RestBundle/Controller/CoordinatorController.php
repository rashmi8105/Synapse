<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Service\Impl\EmailPasswordService;
use Synapse\CoreBundle\Service\Impl\PasswordService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\CoordinatorDTO;
use Synapse\RestBundle\Entity\Response;

/**
 * Class CoordinatorController
 *
 * @package Synapse\RestBundle\Controller
 *         
 * @Rest\Prefix("/coordinators")
 */
class CoordinatorController extends AbstractAuthController
{

    /**
     * @var PersonService
     *
     *      @DI\Inject(PersonService::SERVICE_KEY)
     */
    private $personService;

    /**
     * Creates a coordinator from the passed in DTO and returns the created coordinatorDTO object as a response
     *
     * @ApiDoc(
     * resource = true,
     * description = "Gets Roles",
     * input = "Synapse\RestBundle\Entity\CoordinatorDTO",
     * output = "Synapse\RestBundle\Entity\CoordinatorDTO",
     * section = "Coordinator",
     * statusCodes = {
     *                  201 = "Coordinator was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param CoordinatorDTO $coordinatorDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createCoordinatorAction(CoordinatorDTO $coordinatorDTO, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($coordinatorDTO, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $coordinator = $this->personService->createCoordinator($coordinatorDTO);
            return new Response($coordinator);
        }
    }

    /**
     * Update coordinator
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Coordinator",
     * section = "Coordinator",
     * statusCodes = {
     *                  201 = "Coordinator was updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param CoordinatorDTO $coordinatorDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function updateCoordinatorAction(CoordinatorDTO $coordinatorDTO, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($coordinatorDTO, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $roles = $this->personService->updateCoordinator($coordinatorDTO);
            return new Response($roles);
        }
    }

    /**
     * Get all coordinators
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get All Coordinators",
     * section = "Coordinator",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Get("/{orgId}", requirements={"_format"="json"})
     * @QueryParam(name="filter", description="filter")
     * @Rest\View(200)
     *
     * @param integer $orgId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getAllCoordinatorAction($orgId, ParamFetcher $paramFetcher)
    {
        $filter = $paramFetcher->get('filter');
        $organizationId = $this->getLoggedInUserOrganizationId();
        $persons = $this->personService->getCoordinator($organizationId, $filter);
        return new Response($persons);
    }

    /**
     * Get a coordinator
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get a Coordinator",
     * section = "Coordinator",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Get("/{orgId}/{personid}", requirements={"_format"="json"})
     * @Rest\View(200)
     *
     * @param integer $orgId
     * @param integer $personid
     * @return Response
     */
    public function getCoordinatorByIdAction($orgId, $personid)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $persons = $this->personService->getCoordinatorById($organizationId, $personid);
        return new Response($persons);
    }

    /**
     * Delete coordinator
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Coordinator",
     * section = "Coordinator",
     * statusCodes = {
     *                  204 = "Coordinator was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Delete("/{orgId}/{personid}", requirements={"_format"="json"})
     * @Rest\View(204)
     *
     * @param integer $orgId
     * @param integer $personid
     * @return Response
     */
    public function deleteCoordinatorAction($orgId, $personid)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $persons = $this->personService->deleteCoordinator($organizationId, $personid);
        return new Response($persons);
    }

    /**
     * Get all roles for any coordinator with that specific langId
     *
     * @ApiDoc(
     * resource = true,
     * description = "Gets Roles",
     * section = "Coordinator",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Get("/roles/language/{langid}", requirements={"_format"="json"})
     * @Rest\View(200)
     *
     * @param integer $langid
     * @return Response
     */
    public function getRolesAction($langid)
    {
        $roles = $this->personService->getRoles($langid);
        return new Response($roles);
    }
}