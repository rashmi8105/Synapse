<?php
namespace Synapse\RiskBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;
use Synapse\RiskBundle\EntityDto\RiskGroupDto;
use Synapse\RiskBundle\Service\Impl\RiskGroupService;
use Synapse\UploadBundle\Service\Impl\StudentUploadService;

/**
 * Class RiskModelController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("riskgroups")
 */
class RiskGroupController extends AbstractAuthController
{

    /**
     * @var RiskGroupService
     *     
     *      @DI\Inject(RiskGroupService::SERVICE_KEY)
     */
    private $riskGroupService;

    /**
     * @var StudentUploadService
     *
     *      @DI\Inject(StudentUploadService::SERVICE_KEY)
     */
    private $studentUploadService;

    /**
     * Creates a risk group.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Risk Calculation Input",
     * output = "Synapse\RiskBundle\EntityDto\RiskGroupDto",
     * input = "Synapse\RiskBundle\EntityDto\RiskGroupDto",
     * section = "Risk Groups",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param RiskGroupDto $riskGroupDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createRiskGroupAction(RiskGroupDto $riskGroupDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return View::create(new Response($riskGroupDto, [
                $validationErrors[0]->getMessage()]), 400);
        } else {
            $riskVariables = $this->riskGroupService->createGroup($riskGroupDto);
            return new Response($riskVariables);
        }
    }

    /**
     * Updates a risk group.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Risk Group",
     * output = "Synapse\RiskBundle\EntityDto\RiskGroupDto",
     * input = "Synapse\RiskBundle\EntityDto\RiskGroupDto",
     * section = "Risk Groups",
     * statusCodes = {
     *                  201 = "Resource(s) updated. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param RiskGroupDto $riskGroupDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updateRiskGroupAction(RiskGroupDto $riskGroupDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return View::create(new Response($riskGroupDto, [
                $validationErrors[0]->getMessage()]), 400);
        } else {
            $riskVariables = $this->riskGroupService->editGroup($riskGroupDto);
            return new Response($riskVariables);
        }
    }

    /**
     * Gets the risk groups list.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Groups",
     * output = "Synapse\RiskBundle\EntityDto\RiskGroupDto",
     * section = "Risk Groups",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getRiskGroupAction()
    {
        $riskVariables = $this->riskGroupService->getRiskGroups();
        return new Response($riskVariables);
    }
    
    /**
     * Gets a risk group model.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Group Model",
     * output = "Synapse\RiskBundle\EntityDto\RiskGroupModelDto",
     * section = "Risk Groups",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/org", requirements={"_format"="json"})
     * @QueryParam(name="orgId", requirements="\d+", default ="", strict=true, description="Organization Id")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getRiskGroupModelAction(ParamFetcher $paramFetcher)
    {
        $id = $paramFetcher->get('orgId');
        $riskVariables = $this->riskGroupService->getRiskModelAssingment($id);
        return new Response($riskVariables);
    }
    
    

    /**
     * Gets a risk group by its id.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Group by Id",
     * output = "Synapse\RiskBundle\EntityDto\RiskGroupDto",
     * section = "Risk Groups",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
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
    public function getRiskGroupByIdAction($id)
    {
        $riskVariables = $this->riskGroupService->getRiskGroupById($id);
        return new Response($riskVariables);
    }
    
    /**
     * Gets a risk group download.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Group Download",
     * output = "Synapse\RiskBundle\EntityDto\RiskGroupDto",
     * section = "Risk Groups",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/download/{riskid}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $riskid
     * @return Response
     */
    
    public function getRiskGroupDownloadAction($riskid)
    {
        $orgId = $this->get('security.context')
        ->getToken()
        ->getUser()
        ->getOrganization()
        ->getId();
        
        $filename = $this->studentUploadService->generateRiskCsvByRiskGroup($orgId, $riskid);
        return new Response(['URL' => $filename]);
    }
    
}