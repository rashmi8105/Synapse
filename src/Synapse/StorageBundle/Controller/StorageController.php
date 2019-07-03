<?php

namespace Synapse\StorageBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Controller\AbstractSynapseController;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Exception\RestException;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\StorageBundle\Service\Impl\S3StorageService;

/**
 * Storage controller
 *
 * @package Synapse\StorageBundle\Controller
 *
 * @Rest\Prefix("storage")
 *
 */
class StorageController extends AbstractAuthController
{
    /**
     * @var S3StorageService
     *
     *      @DI\Inject(S3StorageService::SERVICE_KEY)
     */
    private $storageService;


    /**
     * Creates an upload policy.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Upload Policy",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Help",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/policy")
     * @Rest\View(statusCode=201)
     *
     * @return Response
     */
    public function createPolicyAction()
    {
        $isAuthenticated = $this->get(SynapseConstant::SECURITY_CONTEXT_CLASS_KEY)->isGranted("IS_AUTHENTICATED_FULLY");
        if (!($isAuthenticated)) {
            throw new AccessDeniedException();
        }

        $organizationId = $this->getLoggedInUserOrganizationId();
        $loggedInUserId = $this->getLoggedInUserId();

        $policy = $this->storageService->getPolicy($organizationId, $loggedInUserId);
        $signature = $this->storageService->getSignature($policy);
        return new Response(['policy' => $policy, 'signature' => $signature], []);
    }

    /**
     * Gets student photo details.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student Photo",
     * section = "Help",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/studentphoto/{studentId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @throws SynapseValidationException | AccessDeniedException
     */
    public function getStudentPhotoAction($studentId)
    {
        $isAuthenticated = $this->get(SynapseConstant::SECURITY_CONTEXT_CLASS_KEY)->isGranted("IS_AUTHENTICATED_FULLY");

        if (!($isAuthenticated)) {
            throw new AccessDeniedException();
        }

        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        // Streaming the student photo file to web.
        $this->storageService->streamStudentPhotoURL($loggedInUserId, $studentId, $organizationId);
    }

    /**
     * Generates file.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get File",
     * section = "Help",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{type}/{file}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param string $type
     * @param string $file
     * @throws SynapseValidationException | AccessDeniedException
     */
    public function getFileAction($type, $file)
    {
        $aliasFileName = null;

        $environment = $this->get('kernel')->getEnvironment();
        if ($type === SynapseConstant::S3_STUDENT_SURVEY_REPORT_DIRECTORY) {
            //Adding the '.pdf' back onto the file name since it is removed when we send the link in the email so that
            //it looks less like a file name and more like a token in the link.
            $fullFileName = $file . '.pdf';
            $file = $this->storageService->getStudentSurveyReportPdfPath($fullFileName, $environment);
        } else {

            $isAuthenticated = $this->get(SynapseConstant::SECURITY_CONTEXT_CLASS_KEY)->isGranted("IS_AUTHENTICATED_FULLY");
            if (!($isAuthenticated)) {
                throw new AccessDeniedException();
            }
            $organizationId = $this->getUser()->getOrganization()->getId();
            $userId = $this->getUser()->getId();

            if ($organizationId === -1 && $this->container->has('proxy_user')) {
                $organizationId = $this->get('proxy_user')->getOrganization()->getId();
                $userId = -1;
            }

            // Generates and returns the file based on the type.
            $aliasFileName = $this->storageService->getFilePath($type, $file, $organizationId, $environment, $userId);
        }

        // Streaming the file to web.
        $this->streamS3FileToWeb($type, $file, $aliasFileName);
    }

    /**
     * Generates error file and return file name.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Error File",
     * section = "Help",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{type}/{errors}/{file}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param string $type
     * @param string $errors
     * @param string $file
     * @throws AccessDeniedException | SynapseValidationException
     */
    public function getErrorFileAction($type, $errors, $file)
    {
        $isAuthenticated = $this->get(SynapseConstant::SECURITY_CONTEXT_CLASS_KEY)->isGranted("IS_AUTHENTICATED_FULLY");
        if (!($isAuthenticated)) {
            throw new AccessDeniedException();
        }

        $organizationId = $this->getUser()->getOrganization()->getId();
        $userId = $this->getUser()->getId();
        if ($organizationId === -1 && $this->container->has('proxy_user')) {
            $organizationId = $this->get('proxy_user')->getOrganization()->getId();
            $userId = $this->container->get('proxy_user')->getId();
        }
        $environment = $this->get('kernel')->getEnvironment();

        // Generates and returns the file name based on the type.
        $aliasFileName = $this->storageService->getFilePath($type, $file, $organizationId, $environment, $userId, $errors);
        // Streaming the file to web.
        $type = $type . '/' . $errors;
        $this->streamS3FileToWeb($type, $file, $aliasFileName);
    }

    /**
     * This method streams the file from S3 storage to web response stream
     *
     * @param string $folderName
     * @param string $fileName
     * @param string $aliasFileName (optional)
     * @return void
     */
    private function streamS3FileToWeb ($folderName, $fileName, $aliasFileName=null)
    {
        $this->storageService->streamFileToWeb( "data://" . $folderName . "/" . $fileName, $aliasFileName);
    }

}
