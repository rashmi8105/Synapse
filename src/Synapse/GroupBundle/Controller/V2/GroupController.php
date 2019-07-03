<?php

namespace Synapse\GroupBundle\Controller\V2;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\GroupBundle\DTO\GroupPersonInputDto;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Service\Impl\GroupService as CoreBundleGroupService;
use Synapse\CoreBundle\Service\Utility\IDConversionService;
use Synapse\GroupBundle\DTO\GroupFacultyListInputDTO;
use Synapse\GroupBundle\DTO\GroupInputDTO;
use Synapse\GroupBundle\Service\GroupService;
use Synapse\RestBundle\Controller\V2\AbstractAuthV2Controller;
use Synapse\RestBundle\Entity\Response;



/**
 * Class GroupController
 *
 * @package Synapse\GroupBundle\Controller
 *
 * @Rest\Version("2.0")
 * @Rest\Prefix("/groups")
 *
 */
class GroupController extends AbstractAuthV2Controller
{

    /**
     * @var CoreBundleGroupService
     * @DI\Inject(CoreBundleGroupService::SERVICE_KEY)
     */
    private $coreBundleGroupService;


    /**
     * @var GroupService
     * @DI\Inject(GroupService::SERVICE_KEY)
     */
    private $groupService;

    /**
     * @var IDConversionService
     * @DI\Inject(IDConversionService::SERVICE_KEY)
     */
    private $idConversionService;

    /**
     * Gets the list of groups for the organization
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get groups for organization",
     * output = "Synapse\GroupBundle\DTO\GroupListDto",
     * section = "Groups",
     * views = {"public"},
     * statusCodes = {
     *                    200 = "Request was successful. Representation of resources was returned",
     *                    403 = "Access denied.",
     *                    404 = "Not found",
     *                    500 = "Internal server error.",
     *                    504 = "Request has timed out."
     *               }
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="filter", default=null, nullable=true, description="Search groups by external id or group name")
     * @QueryParam(name="page_number", requirements="\d+", default=null, nullable=true, strict=false, description="page number of the result set")
     * @QueryParam(name="records_per_page", requirements="\d+", default=null, nullable=true, strict=false, description="Sets the number of results per page.")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getGroupsAction(ParamFetcher $paramFetcher)
    {
        $filter = $paramFetcher->get('filter');
        $pageNumber = $paramFetcher->get('page_number');
        $recordsPerPage = $paramFetcher->get('records_per_page');

        $groupListDto = $this->groupService->getGroupsByOrganization($this->getLoggedInUserOrganizationId(), $filter, $pageNumber, $recordsPerPage);
        return new Response($groupListDto);
    }

    /**
     * Adds students to the specified group
     *
     * @ApiDoc(
     * resource = true,
     * description = "Adds students to the specified group",
     * input= "Synapse\GroupBundle\DTO\GroupPersonInputDto",
     * section = "Groups",
     * views = {"public"},
     * statusCodes = {
     *                    200 = "Request was successful. Representation of resource(s) was returned.",
     *                    403 = "Access denied.",
     *                    404 = "Not found.",
     *                    500 = "There were errors either in the body of the request or an internal server error.",
     *                    504 = "Request has timed out."
     *               }
     * )
     *
     * @Rest\Post("/students", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param GroupPersonInputDto $groupPersonInputDto
     * @return Response
     */
    public function addStudentsToGroupAction(GroupPersonInputDto $groupPersonInputDto)
    {

        $this->isRequestSizeAllowed('group_person_list');
        $result = $this->groupService->addStudentsToGroup($groupPersonInputDto, $this->getLoggedInUserOrganizationId());
        return new Response($result['data'], $result['error']);
    }

    /**
     * Gets the list of Students for a group
     *
     * @ApiDoc(
     * resource = true,
     * description = "Gets the list of Students for a group",
     * output = "Synapse\GroupBundle\DTO\GroupStudentDto",
     * section = "Groups",
     * views = {"public"},
     * statusCodes = {
     *                    200 = "Request was successful. Representation of resource(s) was returned",
     *                    403 = "Access denied.",
     *                    404 = "Not found",
     *                    500 = "Internal server error.",
     *                    504 = "Request has timed out."
     *               }
     * )
     *
     * @Rest\Get("/{groupExternalId}/students", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param string $groupExternalId
     * @return Response
     */
    public function getStudentsForGroupAction($groupExternalId){

        $organizationId = $this->getLoggedInUserOrganizationId();
        $groupObject = $this->idConversionService->getConvertedGroupObject($groupExternalId, $organizationId, false);
        $groupStudentList = $this->groupService->fetchStudentsForGroup($groupObject);
        return new Response($groupStudentList);
    }


    /**
     * Deletes the specified group
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete a Group",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Groups",
     * statusCodes = {
     *                  204 = "Resource deleted. Representation of resource was not returned.",
     *                  400 = "Validation error has occurred.",
     *                  403 = "Access denied.",
     *                  404 = "Not found",
     *                  504 = "Request has timed out.",
     *                  500 = "Internal server error",
     *               },
     *  view = {"public"}
     * )
     *
     * @Rest\Delete("/{groupId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     * @param string $groupId - External group Id
     */
    public function groupDeleteAction($groupId){
        $organizationId = $this->getLoggedInUserOrganizationId();
        $groupObject = $this->idConversionService->getConvertedGroupObject($groupId, $organizationId, false);
        $this->coreBundleGroupService->deleteGroup($organizationId, $groupObject->getId(), false);
    }


    /**
     * Gets the List of group faculties for the specified group ID
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get groups Faculties for organization",
     * output = "Synapse\GroupBundle\DTO\GroupFacultyDTO",
     * section = "Groups",
     * views = {"public"},
     * statusCodes = {
     *                    200 = "Request was successful. Representation of resources was returned.",
     *                    400 = "Validation error has occurred.",
     *                    403 = "Access denied.",
     *                    404 = "Not found",
     *                    500 = "Internal server error.",
     *                    504 = "Request has timed out."
     *               }
     * )
     *
     * @Rest\Get("/{groupId}/faculty", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @param string $groupId - External Id for group
     * @return Response
     */
    public function getFacultyInGroupAction($groupId)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $groupObject = $this->idConversionService->getConvertedGroupObject($groupId, $organizationId, false);
        $response = $this->groupService->getFacultyByGroup($organizationId, $groupObject, false);
        return new Response($response);
    }

    /**
     * Delete students from the specified group
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete student from the specified group",
     * section = "Groups",
     * statusCodes = {
     *                    204 = "Resource deleted. Representation of resource was not returned.",
     *                    400 = "Validation error has occurred.",
     *                    403 = "Access denied.",
     *                    404 = "Not found",
     *                    500 = "Internal server error.",
     *                    504 = "Request has timed out."
     *               },
     * views = {"public"}
     * )
     *
     * @Rest\Delete("/{groupExternalId}/student/{studentExternalId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param string $groupExternalId
     * @param string $studentExternalId
     */
    public function deleteStudentToGroupAction($groupExternalId, $studentExternalId){
        $this->groupService->deleteStudentFromGroup($groupExternalId, $studentExternalId, $this->getLoggedInUserOrganizationId());
    }


    /**
     * Delete a faculty from a group.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete a faculty from Group",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Groups",
     * statusCodes = {
     *                      204 = "Resource deleted. Representation of resource was not returned.",
     *                      400 = "Validation error has occurred.",
     *                      403 = "Access denied.",
     *                      404 = "Not found",
     *                      500 = "Internal server error",
     *                      504 = "Request has timed out.",
     *               },
     *  views = {"public"}
     * )
     *
     * @Rest\Delete("/{groupId}/faculty/{facultyId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     * @param string $groupId
     * @param string $facultyId
     * @throws SynapseValidationException
     */
    public function deleteFacultyFromGroupAction($groupId, $facultyId)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();

        $orgGroupObject = $this->idConversionService->getConvertedGroupObject($groupId, $organizationId, false);
        $internalFacultyIds = $this->idConversionService->convertPersonIds($facultyId, $organizationId, false);

        $validationErrors = $this->idConversionService->validationErrors;
        $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organizationId, $validationErrors);

        if ($validationErrors) {
            $validationErrorsArray = current($validationErrors);
            $validationErrorString = implode(',', $validationErrorsArray);
            throw new SynapseValidationException($validationErrorString);
        }
        $this->groupService->deleteFacultyFromGroup($internalFacultyIds[0], $orgGroupObject->getId(), $organizationId);
    }

    /**
     * Creates a group
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Groups",
     * input = "Synapse\GroupBundle\DTO\GroupInputDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Groups",
     * statusCodes = {
     *                    201 = "Resource(s) created. Representation of resource(s) was returned.",
     *                    400 = "Validation errors has occurred.",
     *                    403 = "Access denied",
     *                    404 = "Not found",
     *                    500 = "There was either errors with the body of the request or an internal server error.",
     *                    504 = "Request has timed out."
     *               },
     *  views = {"public"}
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param GroupInputDTO $groupInputDTO
     * @return Response
     */
    public function createGroupsAction(GroupInputDTO $groupInputDTO)
    {
        $this->isRequestSizeAllowed('org_groups');
        $result = $this->groupService->createGroups($groupInputDTO, $this->getLoggedInUserOrganization());
        return new Response($result['data'], $result['errors']);
    }

    /**
     * Add a faculty in a group.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add faculty in group",
     * input = "Synapse\GroupBundle\DTO\GroupFacultyListInputDTO",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Groups",
     * statusCodes = {
     *                    201 = "Resource(s) created. Representation of resource(s) was returned.",
     *                    400 = "Validation errors has occurred.",
     *                    403 = "Access denied.",
     *                    404 = "Not found.",
     *                    500 = "There were errors either in the body of the request or an internal server error.",
     *                    504 = "Request has timed out."
     *               },
     *  views = {"public"}
     * )
     *
     * @Rest\Post("/faculty", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param GroupFacultyListInputDTO $groupFacultyListInputDTO
     * @return Response
     */
    public function addFacultiesToGroupsAction(GroupFacultyListInputDTO $groupFacultyListInputDTO)
    {
        $this->isRequestSizeAllowed('group_faculty_list');
        $organizationId = $this->getLoggedInUserOrganizationId();
        $result = $this->groupService->addFacultiesToGroups($organizationId, $groupFacultyListInputDTO);
        return new Response($result['data'], $result['errors']);
    }

    /**
     * Updates the specified groups.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Groups",
     * input = "Synapse\GroupBundle\DTO\GroupInputDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Groups",
     * statusCodes = {
     *                    201 = "Resource(s) updated. Representation of resource(s) was returned.",
     *                    400 = "Validation errors has occurred.",
     *                    403 = "Access denied.",
     *                    404 = "Not found.",
     *                    500 = "There was either errors with the body of the request or an internal server error.",
     *                    504 = "Request has timed out."
     *               },
     *  views = {"public"}
     * )
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param GroupInputDTO $groupInputDTO
     * @return Response
     */
    public function updateGroupsAction(GroupInputDTO $groupInputDTO)
    {
        $this->isRequestSizeAllowed('org_groups');
        $organization = $this->getLoggedInUserOrganization();
        $result = $this->groupService->updateGroups($groupInputDTO, $organization);
        return new Response($result['data'], $result['errors']);
    }
}