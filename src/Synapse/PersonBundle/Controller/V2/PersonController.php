<?php

namespace Synapse\PersonBundle\Controller\V2;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\PersonBundle\DTO\PersonListDTO;
use Synapse\PersonBundle\Service\PersonService;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response as Response;


/**
 * Class PersonController
 *
 * @package Synapse\PersonBundle\Controller
 *
 * @Rest\Version("2.0")
 * @Rest\Prefix("/person")
 *
 */
class PersonController extends AbstractAuthController
{

    /**
     * @var Logger
     * @DI\Inject(SynapseConstant::CONTROLLER_LOGGING_CHANNEL)
     */
    private $apiLogger;


    /**
     * @var PersonService
     * @DI\Inject(PersonService::SERVICE_KEY)
     */
    private $personService;


    /**
     * Gets all users for an organization based on the specified criteria
     *
     * @ApiDoc(
     * resource = true,
     * output = "Synapse\PersonBundle\DTO\PersonSearchResultDTO",
     * description = "Get users for organization",
     * section = "Person",
     * views = {"public"},
     * statusCodes = {
     *                    200 = "Request was successful. Representation of resources was returned.",
     *                    403 = "Access denied exception",
     *                    500 = "There was an internal server error.",
     *                    504 = "Request has timed out."
     *               }
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="user_type", default ="", requirements="(student|faculty|dual_role|orphan|\d+)", description="Filters the result set to the specified user type")
     * @QueryParam(name="filter", description="Searches for users with a firstname, lastname, external id, or primary email similar to the passed string.")
     * @QueryParam(name="page_number", strict=false, description="page number of the result set")
     * @QueryParam(name="records_per_page", strict=false, description="Sets the number of results per page.")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getMapworksPersonsAction(ParamFetcher $paramFetcher)
    {
        $ipAddress = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
        $loggedInUserId = $this->getUser()->getId();
        $organizationId = $this->getUser()->getOrganization()->getId();

        $this->apiValidationService->isAPIIntegrationEnabled();

        $userType = $paramFetcher->get('user_type');
        $filter = $paramFetcher->get('filter');
        $pageNumber = $paramFetcher->get('page_number');
        $recordsPerPage = $paramFetcher->get('records_per_page');

        $this->apiLogger->notice(__FUNCTION__ . "; PersonId: $loggedInUserId; OrganizationId: $organizationId; UserType: $userType; Filter: $filter; PageNumber: $pageNumber; RecordsPerPage: $recordsPerPage; IP Address: $ipAddress");

        $person = $this->personService->getMapworksPersons($organizationId, $filter, $userType, $pageNumber, $recordsPerPage);
        return new Response($person);
    }


    /**
     * Creates a person based on the parameters provided.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Person",
     * input = "Synapse\PersonBundle\DTO\PersonListDTO",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Person",
     * statusCodes = {
     *                    201 = "Object was created. Representation of resources was returned.",
     *                    400 = "Validation error has occurred.",
     *                    404 = "Not found",
     *                    500 = "There was an internal server error OR errors in the body of the request.",
     *                    504 = "Request has timed out."
     *               },
     *  views = {"public"}
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param PersonListDTO $personListDTO
     * @return Response
     */
     public function createPersonAction(PersonListDTO $personListDTO){
         $ipAddress = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
         $loggedInUser = $this->getUser();
         $loggedInUserId = $loggedInUser->getId();
         $organization = $this->getUser()->getOrganization();
         $organizationId = $organization->getId();
         $requestJSON = $this->get("request")->getContent();
         $this->apiLogger->notice(__FUNCTION__ . "; PersonId: $loggedInUserId; OrganizationId: $organizationId; IP Address: $ipAddress;Request Json: $requestJSON");
         $this->apiValidationService->isAPIIntegrationEnabled();
         $requestKey = "person_list";
         $this->apiValidationService->isRequestSizeAllowed($requestJSON, $requestKey);
         $response = $this->personService->createPersons($personListDTO, $organization, $loggedInUser);
         return new Response($response['data'],$response['errors']);
     }


    /**
     * Update persons based on the parameters provided.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Persons",
     * input = "Synapse\PersonBundle\DTO\PersonListDTO",
     * section = "Person",
     * statusCodes = {
     *                    201 = "Object was updated. Representation of resources was returned",
     *                    400 = "Validation error has occurred",
     *                    404 = "Not found",
     *                    500 = "There were errors either in the body of the request or an internal server error",
     *                    504 = "Request has timed out"
     *               },
     *  views = {"public"}
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param PersonListDTO $personListDTO
     * @return Response
     */
    public function updatePersonsAction(PersonListDTO $personListDTO)
    {
        $loggedInUser = $this->getUser();
        $organization = $this->getUser()->getOrganization();

        $response = $this->personService->updatePersons($personListDTO, $organization, $loggedInUser);
        return new Response($response['data'], $response['errors']);
    }


    /**
     * Gets all current Risk and Intent To Leave values for an organization's students based on the specified criteria
     *
     * @ApiDoc(
     * resource = true,
     * output = "Synapse\PersonBundle\DTO\PersonSearchResultDTO",
     * description = "Get risk/intent to leave color per user for an organization",
     * section = "Person",
     * views = {"public"},
     * statusCodes = {
     *                    200 = "Request was successful. Representation of resources was returned.",
     *                    403 = "Access denied exception",
     *                    500 = "There was an internal server error.",
     *                    504 = "Request has timed out."
     *               }
     * )
     *
     * @Rest\Get("/riskintenttoleave", requirements={"_format"="json"})
     * @QueryParam(name="risk_group_id", description="Searches for students currently in the specified risk group.")
     * @QueryParam(name="current_cohort", description="Searches for students currently in the specified cohort in the current academic year.")
     * @QueryParam(name="filter", description="Searches for users with a firstname, lastname, external id, or primary email similar to the passed string.")
     * @QueryParam(name="page_number", strict=false, description="page number of the result set")
     * @QueryParam(name="records_per_page", strict=false, description="Sets the number of results per page.")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function GetCurrentRiskAndIntentToLeaveForStudentsAction(ParamFetcher $paramFetcher)
    {
        $ipAddress = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
        $loggedInUserId = $this->getUser()->getId();
        $organizationId = $this->getUser()->getOrganization()->getId();

        $this->apiValidationService->isAPIIntegrationEnabled();
        $this->apiValidationService->isOrganizationAPICoordinator($organizationId, $loggedInUserId);

        $riskGroupId = $paramFetcher->get('risk_group_id');
        $currentCohort = $paramFetcher->get('current_cohort');
        $filter = $paramFetcher->get('filter');
        $pageNumber = $paramFetcher->get('page_number');
        $recordsPerPage = $paramFetcher->get('records_per_page');

        $this->apiLogger->notice(__FUNCTION__ . "; PersonId: $loggedInUserId; OrganizationId: $organizationId; RiskGroupId: $riskGroupId; CurrentCohort: $currentCohort Filter: $filter; PageNumber: $pageNumber; RecordsPerPage: $recordsPerPage; IP Address: $ipAddress");

        $person = $this->personService->getCurrentRiskAndIntentToLeaveForOrganization($organizationId, $filter, $currentCohort, $riskGroupId, $pageNumber, $recordsPerPage);
        return new Response($person);
    }

}
