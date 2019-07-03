<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\PersonDTO;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * Class PersonController - contains API's to handle request related to Person in Synapse application.
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/person")
 */
class PersonController extends AbstractAuthController
{

    /**
     * @var PersonService
     *
     *      @DI\Inject(PersonService::SERVICE_KEY)
     */
    private $personService;

    /**
     * Creates a new person.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Person",
     * input = "Synapse\RestBundle\Entity\PersonDTO",
     * output = "Synapse\RestBundle\Entity\PersonDTO",
     * section = "Person",
     * statusCodes = {
     *                  201 = "Person was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param PersonDTO $personDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createPersonAction(PersonDTO $personDTO, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($personDTO, $errors), 400);
        } else {
            $profile = $this->personService->createPerson($personDTO);

            return new Response($profile);
        }
    }

    /**
     * Checks whether an email exists or not.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Is Email Exists",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Person",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/emailexists/{email}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param string $email
     * @return Response
     */
    public function emailExistsAction($email)
    {
        $searchList = $this->personService->primaryEmailExists($email);
        return new Response($searchList);
    }

    /**
     * Gets a person's details.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Person Details",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Person",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getPersonAction($id)
    {
        $personDetails = $this->personService->getPerson($id);
        return new Response($personDetails);
    }

    /**
     * Gets a user's AuthUsername. Requires Coordinator rights.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Person Details",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Person",
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
     * @Rest\Get("/authuser/{personId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param $personId
     * @return Response
     */
    public function getAuthUsernameAction($personId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $this->ensureCoordinatorOrgAccess($loggedInUserId);

        try {
            $authUsername = $this->personService->getAuthUsername($personId);
            $status = true;
        } catch (ValidationException $e) {
            $status = false;
        }

        return new Response([
            'success' => $status
        ], [
            'authUsername' => $authUsername
        ]);

    }
}
