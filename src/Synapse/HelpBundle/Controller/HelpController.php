<?php
namespace Synapse\HelpBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\HelpBundle\EntityDto\HelpDto;
use Synapse\HelpBundle\Service\Impl\HelpService;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;

/**
 * Class HelpController
 *
 * @package Synapse\HelpBundle\Controller
 *         
 *          @Rest\Prefix("/help")
 */
class HelpController extends AbstractAuthController
{

    /**
     * @var HelpService
     *     
     *      @DI\Inject(HelpService::SERVICE_KEY)
     */
    private $helpService;

    /**
     * @var UploadFileLogService
     *     
     *      @DI\Inject(UploadFileLogService::SERVICE_KEY)
     */
    private $uploadFileLogService;

    /**
     * Create API to Upload Help for Coordinator
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Help Upload",
     * output = "Synapse\HelpBundle\EntityDto\HelpDto",
     * section = "Help",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/upload",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="org_id")
     * @RequestParam(name="title")
     * @RequestParam(name="description", strict=false, nullable=true)
     * @RequestParam(name="file_name")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createHelpUploadAction(ParamFetcher $paramFetcher)
    {
        $orgId = $paramFetcher->get('org_id');
        $title = $paramFetcher->get('title');
        $description = $paramFetcher->get('description');
        $displayFileName = $paramFetcher->get('file_name');
        $loggedInUserId = $this->getLoggedInUserId();

        $jobNumber = uniqid();
        $newKey = $orgId . "-" . $jobNumber . "-" . $displayFileName;
        $filePath = $newKey;

        $this->helpService->uploadDoc($orgId, $displayFileName, $newKey, $jobNumber, $this->uploadFileLogService);
        $helpSaveResponse = $this->helpService->createHelpDoc($title, $description, $displayFileName, $filePath, $orgId, $loggedInUserId);
        
        $response = new Response($helpSaveResponse);
        return $response;
    }

    /**
     * Update a Help Document for Coordinator
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Help Upload",
     * output = "Synapse\HelpBundle\EntityDto\HelpDto",
     * section = "Help",
     * statusCodes = {
     *                  201 = "Resource(s) updated. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/upload",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @RequestParam(name="id")
     * @RequestParam(name="org_id")
     * @RequestParam(name="title")
     * @RequestParam(name="description", strict=false, nullable=true)
     * @RequestParam(name="file_name")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function updateHelpUploadAction(ParamFetcher $paramFetcher)
    {
        $id = $paramFetcher->get('id');
        $orgId = $paramFetcher->get('org_id');
        $title = $paramFetcher->get('title');
        $description = $paramFetcher->get('description');
        $displayFileName = $paramFetcher->get('file_name');
        $loggedInUserId = $this->getLoggedInUserId();

        $jobNumber = uniqid();
        $newKey = $orgId . "-" . $jobNumber . "-" . $displayFileName;
        $filePath = $newKey;

        $this->helpService->uploadDoc($orgId, $displayFileName, $newKey, $jobNumber, $this->uploadFileLogService);
        $helpSaveResponse = $this->helpService->updateHelpDoc($id, $title, $description, $displayFileName, $filePath, $orgId, $loggedInUserId);
        
        $response = new Response($helpSaveResponse);
        return $response;
    }

    /**
     * Create Help
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Help",
     * input = "Synapse\HelpBundle\EntityDto\HelpDto",
     * output = "Synapse\HelpBundle\EntityDto\HelpDto",
     * section = "Help",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/{orgId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param HelpDto $helpDto
     * @param int $orgId
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createHelpAction(HelpDto $helpDto, $orgId, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            
            return View::create(new Response($helpDto, [
                $validationErrors[0]->getMessage()
            ]), 400);
        } else {
            $loggedInUserId = $this->getLoggedInUserId();
            $helpCreateResponse = $this->helpService->createHelp($helpDto, $orgId, $loggedInUserId);
            
            return new Response($helpCreateResponse);
        }
    }

    /**
     * Update Help
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Help",
     * input = "Synapse\HelpBundle\EntityDto\HelpDto",
     * output = "Synapse\HelpBundle\EntityDto\HelpDto",
     * section = "Help",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/{orgId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param HelpDto $helpDto
     * @param int $orgId
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updateHelpAction(HelpDto $helpDto, $orgId, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($helpDto, $errors), 400);
        } else {
            $loggedInUserId = $this->getLoggedInUserId();
            $helpUpdateResponse = $this->helpService->updateHelp($helpDto, $orgId, $loggedInUserId);
            
            return new Response($helpUpdateResponse);
        }
    }

    /**
     * Delete Help
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Help",
     * section = "Help",
     * statusCodes = {
     *                  204 = "Resource(s) deleted. No representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{orgId}/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $orgId
     * @param int $id
     */
    public function deleteHelpAction($orgId, $id)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $this->helpService->deleteHelp($orgId, $id, $loggedInUserId);
    }

    /**
     * Lists all help sources for a single campus.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Help",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Help",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{orgId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getHelpAction($orgId)
    {
        $getAllHelpResp = $this->helpService->getHelps($orgId);
        return new Response($getAllHelpResp);
    }

    /**
     * Get mapworks support contact details for a single campus.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Mapworks Support Contact",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Help",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{orgId}/supportcontact", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getMapworkSupportAction($orgId)
    {
        $helpMapSupport = $this->helpService->getMapWorksSupportContact($orgId);
        return new Response($helpMapSupport);
    }

    /**
     * Gets details about a single help item by its id.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Help Details",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Help",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{orgId}/{helpId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @param int $helpId
     * @return Response
     */
    public function getHelpDetailsAction($orgId, $helpId)
    {
        $helpMapSupport = $this->helpService->getHelpDetails($orgId, $helpId);
        return new Response($helpMapSupport);
    }

    /**
     * Create Help Upload Policy
     * The policy required for making authenticated requests using HTTP POST is a UTF-8 and Base64 encoded document written in JavaScript Object Notation (JSON)
     * that specifies conditions that the request must meet.
     * Depending on how you design your policy document, you can control per-upload, per-user, for all uploads,
     * or according to other designs that meet your needs
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Help Upload Policy",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Help",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/policy")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     *
     */
    public function createHelpUploadPolicyAction()
    {
        $awsSecret = $this->container->getParameter(CourseConstant::AMAZONSECRET);
        $expire = date(CourseConstant::DATE_FORMAT, strtotime('+ 15 minutes', time()));
        $policyDocument = CourseConstant::EXPIRATION . $expire . '",
        "conditions": [
                        {"bucket": "ebi-synapse-bucket"},
                        ["starts-with", "$key", "help-uploads/"],
                        {"acl": "private"},
                        ["starts-with", "$Content-Type", ""],
                        ["starts-with", "$filename", ""],
                        ["content-length-range", 0, 524288000]
                      ]}';
        $policy = base64_encode($policyDocument);
        $signature = $this->hex2b64($this->hmacsha1($awsSecret, $policy));
        
        return new Response([
            CourseConstant::POLICY => $policy,
            CourseConstant::SIGNATURE => $signature
        ], []);
    }
}
