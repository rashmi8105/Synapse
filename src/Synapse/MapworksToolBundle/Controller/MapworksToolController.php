<?php
namespace Synapse\MapworksToolBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\MapworksToolBundle\EntityDto\IssuesInputDTO;
use Synapse\MapworksToolBundle\EntityDto\MapworksToolPdfDTO;
use Synapse\MapworksToolBundle\Service\Impl\MapworksToolService;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Controller\AbstractSynapseController;

/**
 * Class Tools Controller
 *
 * @package Synapse\MapworksToolBundle\Controller
 *
 * @Rest\Prefix("/tool")
 */
class MapworksToolController extends AbstractAuthController
{
    /**
     *
     * @var MapworksToolService
     *
     *@DI\Inject(MapworksToolService::SERVICE_KEY)
     */
    private $mapworksToolService;

    /**
     *
     * @var OrgPermissionsetService Permissionset service
     *
     *@DI\Inject(OrgPermissionsetService::SERVICE_KEY)
     */
    private $permissionsetService;

    /**
     * Returns a list of mapworks tools
     *
     * @ApiDoc(
     *          resource = true,
     *          description = "Returns a list of tool set permission set",
     *          section = "Tool",
     *          statusCodes = {
     *                          200 = "Request was successful. Representation of resources was returned.",
     *                          400 = "Validation error has occurred.",
     *                          404 = "Not found.",
     *                          500 = "There was an internal server error OR errors in the body of the request.",
     *                          504 = "Request has timed out."
     *                        },
     *
     * )
     * @Rest\GET("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @return Response
     */
    public function getMapworksToolsAction()
    {
        $tools = $this->permissionsetService->getMapworksTools();
        return new Response($tools);
    }

    /**
     * Gets the list of available tools
     *
     * @ApiDoc(
     *          resource = true,
     *          description = "Get available tools",
     *          section = "Tools",
     *          statusCodes = {
     *                          200 = "Request was successful. Representation of resources was returned.",
     *                          400 = "Validation error has occurred.",
     *                          404 = "Not found.",
     *                          500 = "Internal server error.",
     *                          504 = "Request has timed out."
     *                        },
     *
     * )
     * @Rest\GET("/analysis", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @return Response
     */
    public function getToolAnalysisListAction()
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $loggedInUserId = $this->getLoggedInUserId();
        $tools = $this->mapworksToolService->getToolAnalysisData($loggedInUserId, $organizationId);
        return new Response($tools);
    }

    /**
     * Gets Top Issues With attached Student list based on issuesInputDTO and parameters
     * Optional Pagination for Student List
     *
     * @ApiDoc(
     *          resource = true,
     *          input = "Synapse\MapworksToolBundle\EntityDto\IssuesInputDTO",
     *          output = "Synapse\MapworksToolBundle\EntityDto\TopIssuesDTO",
     *          description = "Get top issues with attached student list as json or generate csv",
     *          section = "Issue",
     *          statusCodes = {
     *                          200 = "Request was successful. Representation of resources was returned.",
     *                          400 = "Validation error has occurred.",
     *                          403 = "Access denied.",
     *                          404 = "Not found.",
     *                          500 = "There was an internal server error OR errors in the body of the request.",
     *                          504 = "Request has timed out."
     *                      }
     * )
     *
     * @Rest\Post("/top-issues", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param IssuesInputDTO $issuesInputDTO
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function generateMapworksToolTopIssuesAction(IssuesInputDTO $issuesInputDTO, ParamFetcher $paramFetcher)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $loggedInUserId = $this->getLoggedInUserId();
        if ($issuesInputDTO->getOutputFormat() == "csv") {
            $response = $this->mapworksToolService->createTopIssuesCSVJob($organizationId, $loggedInUserId, $issuesInputDTO);
        } else {
            $response = $this->mapworksToolService->getMapworksToolTopIssues($organizationId, $loggedInUserId, $issuesInputDTO);
        }
        return new Response($response);
    }

    /**
     * Generates PDF for specified tool
     *
     * @ApiDoc(
     *          resource = true,
     *          input = "Synapse\MapworksToolBundle\EntityDto\MapworksToolPdfDTO",
     *          description = "Create PDF for Mapworks Tool",
     *          section = "Issue",
     *          statusCodes = {
     *                          204 = "Request was successful.",
     *                          400 = "Validation error has occurred.",
     *                          403 = "Access denied.",
     *                          404 = "Not found.",
     *                          500 = "There was an internal server error OR errors in the body of the request.",
     *                          504 = "Request has timed out."
     *                      }
     * )
     *
     * @Rest\Post("/pdf", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @return Response
     */
    public function generateToolPDFAction(MapworksToolPdfDTO $mapworksToolPdfDTO)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $loggedInUserId = $this->getLoggedInUserId();
        $toolId = $mapworksToolPdfDTO->getToolId();
        $zoom = $mapworksToolPdfDTO->getZoom();
        $response = $this->mapworksToolService->generateToolPDF($organizationId, $loggedInUserId, $toolId, $zoom);
        return new Response($response);
    }
}