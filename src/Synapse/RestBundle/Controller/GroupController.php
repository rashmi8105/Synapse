<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Config\Definition\BooleanNode;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Service\Impl\GroupService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\OrgGroupDto;
use Synapse\RestBundle\Entity\Response;
use Synapse\UploadBundle\Service\Impl\GroupStudentUploadService;

/**
 * Class GroupController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/groups")
 */
class GroupController extends AbstractAuthController
{
    const SECURITY_CONTEXT = 'security.context';


    /**
     *
     * @var GroupService
     *
     *      @DI\Inject(GroupService::SERVICE_KEY)
     */
    private $groupService;

    /**
     * @var GroupStudentUploadService
     *
     *      @DI\Inject(GroupStudentUploadService::SERVICE_KEY)
     */
    private $groupStudentUploadService;


    /**
     * Creates a new group.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Creates a New Group",
     * input = "Synapse\RestBundle\Entity\OrgGroupDto",
     * output = "Synapse\RestBundle\Entity\OrgGroupDto",
     * section = "Groups",
     * statusCodes = {
     *                  201 = "Group was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param OrgGroupDto $orgGroupDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createGroupAction(OrgGroupDto $orgGroupDto, ConstraintViolationListInterface $validationErrors)
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($orgGroupDto, $errors), 400);
        } else {
            $group = $this->groupService->createGroup($orgGroupDto);
            return new Response($group);
        }
    }

    /**
     * Edits an existing group.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit a Group",
     * input = "Synapse\RestBundle\Entity\OrgGroupDto",
     * output = "Synapse\RestBundle\Entity\OrgGroupDto",
     * section = "Groups",
     * statusCodes = {
     *                  201 = "Group was edited. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param OrgGroupDto $orgGroupDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     * @deprecated "Group creation is being consolidated within the group bundle. Please look there for this functionality.
     */
    public function editGroupAction(OrgGroupDto $orgGroupDto, ConstraintViolationListInterface $validationErrors)
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($orgGroupDto, $errors), 400);
        } else {
            $group = $this->groupService->editGroup($orgGroupDto);
            return new Response($group);
        }
    }

    /**
     * Deletes a group.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Group",
     * section = "Courses",
     * statusCodes = {
     *                  204 = "Group was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{groupId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $groupId
     * @return Response
     * @deprecated "Group deletion is being consolidated within the group bundle. Please look there for this functionality".
     */
    public function deleteGroupAction($groupId)
    {
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        $organizationId = $this->getLoggedInUserOrganizationId();
        $group = $this->groupService->deleteGroup($organizationId, $groupId);
        return new Response($group);
    }

    /**
     * Gets a group by its groupId.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Group By Id",
     * section = "Groups",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{groupId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $groupId
     * @return Response
     */
    public function getGroupByIdAction($groupId)
    {
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        $organizationId = $this->getLoggedInUserOrganizationId();
        $group = $this->groupService->getGroupById($organizationId, $groupId);
        return new Response($group);
    }

    /**
     * Gets a list of groups within a user's organization.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Group List",
     * section = "Groups",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="type",requirements="(user)", default ="", strict=true, description="user")
     * @QueryParam(name="rootOnly",requirements="(true|false)", default ="", strict=true, description="fetch only top level group")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getGroupListAction(ParamFetcher $paramFetcher)
    {  
        $type = $paramFetcher->get('type');
        if (! empty($type) && $type == 'user') {
            $loggedUserId = $this->getUser()->getId();
            $group = $this->groupService->getListGroups($loggedUserId);
        } else {
            $organizationId = $this->getLoggedInUserOrganizationId();
            $rootGroup = $paramFetcher->get('rootOnly');
            $group = $this->groupService->getGroupList($organizationId, $rootGroup);
        }
        return new Response($group);
    }

    /**
     * Gets an existing group's members ready for download.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Group Listing",
     * section = "Courses",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/download/{groupId}", requirements={"_format"="json"} )
     * @Rest\View(statusCode=200)
     *
     * @param int $groupId
     * @return Response
     */
    public function downloadExistingAction($groupId)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();

        if (!$this->isCoordinator($this->getUser()->getId())){
            throw new SynapseValidationException("You do not have coordinator access");
        };

        $filename = $this->groupStudentUploadService->createCsvOfStudentsInGroup($organizationId, $groupId);
        return new Response(['URL' => $filename]);
    }
}
