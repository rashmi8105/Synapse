<?php

namespace Synapse\PersonBundle\Controller\V2;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\PersonBundle\DTO\ContactInfoListDTO;
use Synapse\PersonBundle\Service\ContactInfoService;
use Synapse\RestBundle\Controller\V2\AbstractAuthV2Controller;
use Synapse\RestBundle\Entity\Response as Response;


/**
 * Class ContactInfoController
 *
 * @package Synapse\PersonBundle\Controller
 *
 * @Rest\Version("2.0")
 * @Rest\Prefix("/person/contact-info")
 *
 */
class ContactInfoController extends AbstractAuthV2Controller
{

    /**
     * @var ContactInfoService
     * @DI\Inject(ContactInfoService::SERVICE_KEY)
     */
    private $contactInfoService;


    /**
     * Create contact information for the specified users.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Contact Info",
     * input = "Synapse\PersonBundle\DTO\ContactInfoListDTO",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Contact Info",
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
     * @param ContactInfoListDTO $contactInfoListDTO
     * @return Response
     */
    public function createUsersContactInfoAction(ContactInfoListDTO $contactInfoListDTO)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $this->isRequestSizeAllowed('contact_information');
        $response = $this->contactInfoService->createUsersContactInfo($organizationId, $contactInfoListDTO);
        return new Response($response['data'], $response['error']);
    }

    /**
     * Update contact information for the specified users
     *
     * @ApiDoc(
     *  resource = true,    
     *  description = "Update Contact Info",
     *  input = "Synapse\PersonBundle\DTO\ContactInfoListDTO",
     *  output = "Synapse\RestBundle\Entity\Response",
     *  section = "Contact Info",
     *  statusCodes = {
     *                    201 = "Resource(s) updated. Representation of resource(s) was returned.",
     *                    400 = "Validation errors has occurred.",
     *                    403 = "Access denied.",
     *                    404 = "Not found.",
     *                    500 = "There was either errors with the body of the request or an internal server error.",
     *                    504 = "Request has timed out."
     *                },
     *  view = {"public"}
     * )
     * @Rest\PUT("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param ContactInfoListDTO $contactInfoListDTO
     *
     * @return Response
     */
    public function updateUsersContactInfoAction(ContactInfoListDTO $contactInfoListDTO)
    {
        $this->isRequestSizeAllowed('contact_information');
        $organizationId = $this->getLoggedInUserOrganizationId();
        $result = $this->contactInfoService->updateUsersContactInfo($organizationId, $contactInfoListDTO);
        return new Response($result['data'], $result['errors']);
    }


    /**
     * Gets all users' contact information
     *
     * @ApiDoc(
     * resource = true,
     * description = "Gets all users' contact information",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Contact Info",
     * views = {"public"},
     * statusCodes = {
     *                    200 = "Request was successful. Representation of resources was returned.",
     *                    403 = "Access denied exception",
     *                    404 = "Not found",
     *                    500 = "There was an internal server error.",
     *                    504 = "Request has timed out."
     *               }
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="person_filter", default=null, nullable=true, description="Filters the returned data by the specified string on the person")
     * @QueryParam(name="contact_filter", default=null, nullable=true, description="Filters the returned data by the specified string on the contact information based on the contact filter type")
     * @QueryParam(name="contact_filter_type", default=null, nullable=true, requirements="(phone|address|all)", strict=true, description="Filters the returned data by the specified string")
     * @QueryParam(name="page_number", default=null, nullable=true, strict=false, description="page number of the result set")
     * @QueryParam(name="records_per_page", default=null, nullable=true, strict=false, description="Sets the number of results per page.")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getUserContactInfoAction(ParamFetcher $paramFetcher)
    {

        $personFilter = $paramFetcher->get('person_filter');
        $contactFilter = $paramFetcher->get('contact_filter');
        $contactFilterType = $paramFetcher->get('contact_filter_type');
        $pageNumber = $paramFetcher->get('page_number');
        $recordsPerPage = $paramFetcher->get('records_per_page');
        $organizationId = $this->getLoggedInUserOrganizationId();
        $result = $this->contactInfoService->getUsersContactInfo($organizationId, $personFilter, $contactFilter, $contactFilterType, $pageNumber, $recordsPerPage);
        return new Response($result);
    }
}

