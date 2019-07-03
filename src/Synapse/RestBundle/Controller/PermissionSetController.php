<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\PermissionSetService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\PermissionSetDto;
use Synapse\RestBundle\Entity\PermissionSetStatusDto;
use Synapse\RestBundle\Entity\Response;

/**
 * Class PermissionSetController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/permissionset")
 */
class PermissionSetController extends AbstractAuthController
{

    /**
     * @var PermissionSetService
     *
     *      @DI\Inject(PermissionSetService::SERVICE_KEY)
     */
    private $permissionSetService;

    /**
     * Creates an EBI permission set.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create EBI Permission Set",
     * input = "Synapse\RestBundle\Entity\PermissionSetDto",
     * output = "Synapse\RestBundle\Entity\PermissionSetDto",
     * section = "Permission Set",
     * statusCodes = {
     *                  201 = "EBI permission set was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param PermissionSetDto $permissionSetDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createEBIPermissionSetAction(PermissionSetDto $permissionSetDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAdminAccess();

        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($permissionSetDto, $errors), 400);
        } else {
            $permissionSet = $this->permissionSetService->create($permissionSetDto);
            return new Response($permissionSet);
        }
    }

    /**
     * Edits an EBI permission set.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Permission Set",
     * input = "Synapse\RestBundle\Entity\PermissionSetDto",
     * output = "Synapse\RestBundle\Entity\PermissionSetDto",
     * section = "Permission Set",
     * statusCodes = {
     *                  201 = "EBI permission set was updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param PermissionSetDto $permissionSetDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function editPermissionSetAction(PermissionSetDto $permissionSetDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAdminAccess();

        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($permissionSetDto, $errors), 400);
        } else {
            $permissionSet = $this->permissionSetService->edit($permissionSetDto);
            return new Response($permissionSet);
        }
    }

    /**
     * Updates an EBI permission set's status.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Permission Set Status",
     * input = "Synapse\RestBundle\Entity\PermissionSetStatusDto",
     * output = "Synapse\RestBundle\Entity\PermissionSetStatusDto",
     * section = "Organization Permission Set",
     * statusCodes = {
     *                  201 = "EBI permission set status was updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/updatestatus", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param PermissionSetStatusDto $permissionSetStatusDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updatePermissionSetStatusAction(PermissionSetStatusDto $permissionSetStatusDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAdminAccess();

        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($permissionSetStatusDto, $errors), 400);
        } else {
            $permissionSet = $this->permissionSetService->updateStatus($permissionSetStatusDto);
            return new Response($permissionSet);
        }
    }

    /**
     * Get profile datablocks or survey datablocks.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Datablocks",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
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
     * @Rest\Get("/{langid}/type/{type}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $langid
     * @param string $type
     * @return Response
     */
    public function getDatablocksAction($langid, $type)
    {
        $this->ensureAdminAccess();

        $datablocks = $this->permissionSetService->getDataBlocksByType($langid, $type);
        return new Response($datablocks);
    }

    /**
     * Gets a permissionset by its id.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Permissionset By Id",
     * output = "Synapse\RestBundle\Entity\PermissionSetDto",
     * section = "Permission Set",
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
     * @Rest\Get("/{langid}/id/{Id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $langid
     * @param int $Id
     * @return Response
     */
    public function getPermissionSetAction($langid, $Id)
    {
        $this->ensureAdminAccess();

        $permissonset = $this->permissionSetService->getPermissionSet($langid, $Id);
        return new Response($permissonset);
    }

    /**
     * Gets all permission sets of an permissionset by status.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Permission Sets By Status",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
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
     * @Rest\Get("/{langid}/list", requirements={"_format"="json"})
     * @QueryParam(name="status", requirements="(active|archive)", default ="", strict=true, description="Permission template status")
     * @Rest\View(statusCode=200)
     *
     * @param int $langid
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listPermissionSetByStatusAction($langid, ParamFetcher $paramFetcher)
    {
        $this->ensureAdminAccess();
        $status = $paramFetcher->get('status');

        $listPermissionset = $this->permissionSetService->listPermissionSetByStatus($langid, $status);
        return new Response($listPermissionset);
    }

    /**
     * Gets whether a permissionset exists or not.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get IsPermissionSet Exists",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
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
     * @Rest\Get("/exists/{name}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param string $name
     * @return Response
     */
    public function isPermissionSetExistsAction($name)
    {
        $this->ensureAdminAccess();

        $searchList = $this->permissionSetService->isPermissionSetExists($name);
        return new Response($searchList);
    }
}
?>