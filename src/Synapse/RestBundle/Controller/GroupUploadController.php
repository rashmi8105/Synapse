<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PHPExcel_IOFactory;
use Resque;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\HttpFoundation\Request;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\GroupUploadConstants;
use Synapse\CoreBundle\Util\CSVFile;
use Synapse\RestBundle\Controller\AbstractSynapseController;
use Synapse\RestBundle\Entity\Response;
use Synapse\UploadBundle\Job\ProcessGroupFacultyBulkUpload;
use Synapse\UploadBundle\Job\ProcessGroupStudentBulkUpload;
use Synapse\UploadBundle\Job\ProcessManageGroupStudentBulkUpload;
use Synapse\UploadBundle\Job\ProcessSubGroupUpload;
use Synapse\UploadBundle\Service\Impl\GroupUploadService;
use Synapse\UploadBundle\Service\Impl\ManageGroupStudentBulkUploadService;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;



/**
 * Class GroupUploadController
 *
 * @package Synapse\RestBundle\Controller
 *
 *          @Rest\Prefix("/groupupload")
 */
class GroupUploadController extends AbstractAuthController
{

    /**
     * @var UploadFileLogService
     *
     *      @DI\Inject(UploadFileLogService::SERVICE_KEY)
     */
    private $uploadFileLogService;

    /**
     * @var GroupUploadService
     *
     *      @DI\Inject(GroupUploadService::SERVICE_KEY)
     */
    private $groupUploadService;

    /**
     * @var ManageGroupStudentBulkUploadService
     *
     *      @DI\Inject(ManageGroupStudentBulkUploadService::SERVICE_KEY)
     */
    private $uploadManageFileLogService;

    /**
     * @var EbiConfigService
     *
     *      @DI\Inject(EbiConfigService::SERVICE_KEY)
     */
    private $ebiConfigService;

    /**
     * Create API to upload a subgroup.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Subgroup Upload",
     * section = "Sub Group Upload",
     * statusCodes = {
     *                  201 = "Subgroup upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/subgroup",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function processGroupUploadAction(ParamFetcher $paramFetcher)
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        } else {
            $fileName = $paramFetcher->get('key');
            $pathParts = pathinfo($fileName);
            if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
                file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$fileName", fopen(UploadConstant::DATA_SLASH . GroupUploadConstants::GROUP_DIR . "/$fileName", 'r'));
                $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$fileName", UploadConstant::DATA_SLASH . GroupUploadConstants::GROUP_DIR . "/{$pathParts['filename']}.csv");
                unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$fileName");
                $fileName = "{$pathParts['filename']}.csv";
            }
            $file = new CSVFile(UploadConstant::DATA_SLASH . GroupUploadConstants::GROUP_DIR . "$fileName");
            $file->seek(PHP_INT_MAX);
            $rowsTotal = $file->key();
            $file->seek(0);
            foreach ($file as $idx => $row) {
                $columns = array_keys($row);
                break;
            }

            $resque = $this->get(UploadConstant::RESQUE);

            $jobNumber = uniqid();
            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();
            if ($organizationId === -1 && $this->container->has('proxy_user')) {
                $organizationId = $this->get('proxy_user')->getOrganization()->getId();
            }
            $lowerColumn = array_map('strtolower', $columns);
            $uploadFile = $this->uploadFileLogService->createUploadService($organizationId, $fileName, $columns, $rowsTotal, $jobNumber, $loggedInUserId, 'G');

            if (!in_array(GroupUploadConstants::PARENT_GROUP_ID_LOWER, $lowerColumn) || !in_array(GroupUploadConstants::GROUP_ID_LOWER, $lowerColumn) || !in_array(GroupUploadConstants::GROUP_NAME_LOWER, $lowerColumn) || $rowsTotal == 0) {
                $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
                $response = new Response($uploadFile, []);
                return $response;
            }

            $this->uploadFileLogService->updateJobErrorPath($uploadFile);

            $job = new ProcessSubGroupUpload();

            $job->args = array(

                'key' => $fileName,
                UploadConstant::JOB_NUM => $jobNumber,
                UploadConstant::ORGN => $organizationId,
                UploadConstant::UPLOADID => $uploadFile->getId(),
                UploadConstant::USERID => $loggedInUserId
            );

            $resque->enqueue($job, true);
            $response = new Response($uploadFile, []);
            return $response;
        }
    }

    /**
     * Get Upload log for a subgroup by its subgroupId.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Subgroup Upload Log",
     * section = "Sub Group Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/subgroup/{id}",defaults={"id"=-1},requirements={"id" = "^\d+$", "_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getSubgroupUploadLogAction($id)
    {
        $upload = $this->uploadFileLogService->findUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Creates a faculty group upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Faculty Group Upload",
     * section = "Faculty Group Upload",
     * statusCodes = {
     *                  201 = "Faculty group upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/faculty",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function processFacultyGroupUploadAction(ParamFetcher $paramFetcher)
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        } else {
            $fileName = $paramFetcher->get('key');
            $pathParts = pathinfo($fileName);
            if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
                file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$fileName", fopen(UploadConstant::DATA_SLASH . GroupUploadConstants::GROUP_DIR . "/$fileName", 'r'));
                $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$fileName", UploadConstant::DATA_SLASH . GroupUploadConstants::GROUP_DIR . "/{$pathParts['filename']}.csv");
                unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$fileName");
                $fileName = "{$pathParts['filename']}.csv";
            }
            $file = new CSVFile(UploadConstant::DATA_SLASH . GroupUploadConstants::GROUP_DIR . "$fileName");
            $file->seek(PHP_INT_MAX);
            $rowsTotal = $file->key();
            $file->seek(0);
            foreach ($file as $idx => $row) {
                $columns = array_keys($row);
                break;
            }

            $resque = $this->get(UploadConstant::RESQUE);

            $jobNumber = uniqid();
            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();
            if ($organizationId === -1 && $this->container->has('proxy_user')) {
                $organizationId = $this->get('proxy_user')->getOrganization()->getId();
            }
            $uploadFile = $this->uploadFileLogService->createUploadService($organizationId, $fileName, $columns, $rowsTotal, $jobNumber, $loggedInUserId, 'GF');
            $lowerCol = array_map('strtolower',$columns);
            if (! in_array(GroupUploadConstants::GROUP_ID_LOWER, $lowerCol) || $rowsTotal == 0) {
                $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
                $response = new Response($uploadFile, []);
                return $response;
            }

            $this->uploadFileLogService->updateJobErrorPath($uploadFile);

            $job = new ProcessGroupFacultyBulkUpload();

            $job->args = array(

                'key' => $fileName,
                UploadConstant::JOB_NUM => $jobNumber,
                UploadConstant::ORGN => $organizationId,
                UploadConstant::UPLOADID => $uploadFile->getId(),
                UploadConstant::USERID => $loggedInUserId
            );

            $resque->enqueue($job, true);

            $response = new Response($uploadFile, []);
            return $response;
        }
    }

    /**
     * Gets the upload log for a faculty group upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Faculty Group Upload Log",
     * section = "Faculty Group Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/faculty/{id}", defaults={"id"=-1},requirements={"id" = "^\d+$", "_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getFacultyUploadLogAction($id)
    {
        $upload = $this->uploadFileLogService->findUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Gets a faculty template upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Faculty Template Upload",
     * section = "Template Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/faculty/template")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getFacultyTemplateUploadAction()
    {
        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="groupfaculty-upload-template.csv"');

        $assignmentTemplate = [
            GroupUploadConstants::EXTERNAL_ID,
            GroupUploadConstants::FIRSTNAME,
            GroupUploadConstants::LASTNAME,
            GroupUploadConstants::PRIMARY_EMAIL,
            GroupUploadConstants::FULL_PATH_NAMES,
            GroupUploadConstants::FULL_PATH_GROUP_IDS,
            GroupUploadConstants::GROUP_NAME,
            GroupUploadConstants::GROUP_ID,
            GroupUploadConstants::PERMISSION_SET,
            GroupUploadConstants::INVISIBLE,
            GroupUploadConstants::REMOVE
        ];

        $modelAssignmentTemplate = implode(",", $assignmentTemplate);
        echo $modelAssignmentTemplate;
        echo "\n";
        exit();
        // NO RETURN!!!
    }

    /**
     * Gets a student template upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student Template Upload",
     * section = "Template Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/student/template/{organizationId}")
     * @Rest\View(statusCode=200)
     *
     * @param int $organizationId
     * @return Response
     */
    public function getStudentTemplateUploadAction($organizationId)
    {
        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="groupstudent-upload-template.csv"');

        $orgId = $this->getLoggedInUserOrganizationId();
        if ($orgId === -1 && $organizationId != null) {
            $orgId = $organizationId;
        }
        $headers = $this->uploadManageFileLogService->getTemplateHeaders($orgId);
        $headers = implode(",", $headers);
        echo $headers;
        echo "\n";
        exit();
        // NO RETURN!!!
    }

    /**
     * Gets a subgroup template upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Subgroup Template Upload",
     * section = "Template Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/subgroup/template")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getSubgroupTemplateUploadAction()
    {
        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="subgroup-upload-template.csv"');

        $assignmentTemplate = [
            GroupUploadConstants::PARENT_GROUP_ID,
            GroupUploadConstants::GROUP_ID,
            GroupUploadConstants::GROUP_NAME
        ];

        $modelAssignmentTemplate = implode(",", $assignmentTemplate);
        echo $modelAssignmentTemplate;
        echo "\n";
        exit();
        // NO RETURN!!!
    }

    /**
     * Gets a download for a subgroup.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Subgroup Download",
     * section = "Downloads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/subgroup/download")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getSubgroupsDownloadAction()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        } else {
            $organizationId = $this->getLoggedInUserOrganizationId();
            $filename = "{$organizationId}-subgroup-bulk-existing.csv";
            $awsBucket = $this->ebiConfigService->get(UploadConstant::AWS_BUCKET);
            $url = "https://{$awsBucket}.s3.amazonaws.com/group-uploads/" . $filename;
            $response = new Response($url, []);
            return $response;
        }
    }

    /**
     * Gets a download for a faculty.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get faculty download",
     * section = "Downloads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/faculty/download")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getFacultyDownloadAction()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        } else {
            $organizationId = $this->getLoggedInUserOrganizationId();
            $filename = "{$organizationId}-faculty-bulk-existing.csv";
            $awsBucket = $this->ebiConfigService->get(UploadConstant::AWS_BUCKET);
            $url = "https://{$awsBucket}.s3.amazonaws.com/group-uploads/" . $filename;
            $response = new Response($url, []);
            return $response;
        }
    }

    /**
     * Gets a download for a student.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student Download",
     * section = "Downloads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/student/download")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getStudentsDownloadAction()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        } else {
            $organizationId = $this->getLoggedInUserOrganizationId();
            $filename = "{$organizationId}-students-bulk-existing.csv";
            $awsBucket = $this->ebiConfigService->get(UploadConstant::AWS_BUCKET);
            $url = "https://{$awsBucket}.s3.amazonaws.com/group-uploads/" . $filename;
            $response = new Response($url, []);
            return $response;
        }
    }

    /**
     * Create API to Upload Subgroups
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Subgroup Upload",
     * section = "Sub Group Upload",
     * statusCodes = {
     *                  201 = "Subgroup upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/student",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function processStudentGroupUploadAction(ParamFetcher $paramFetcher)
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        } else {
            $fileName = $paramFetcher->get('key');
            $pathParts = pathinfo($fileName);
            if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
                file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$fileName", fopen(UploadConstant::DATA_SLASH . GroupUploadConstants::GROUP_DIR . "/$fileName", 'r'));
                $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$fileName", UploadConstant::DATA_SLASH . GroupUploadConstants::GROUP_DIR . "/{$pathParts['filename']}.csv");
                unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$fileName");
                $fileName = "{$pathParts['filename']}.csv";
            }
            $file = new CSVFile(UploadConstant::DATA_SLASH . GroupUploadConstants::GROUP_DIR . "$fileName");
            $file->seek(PHP_INT_MAX);
            $rowsTotal = $file->key();
            $file->seek(0);
            foreach ($file as $idx => $row) {
                $columns = array_keys($row);
                break;
            }

            $resque = $this->get(UploadConstant::RESQUE);

            $jobNumber = uniqid();
            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();
            if ($organizationId === -1 && $this->container->has('proxy_user')) {
                $organizationId = $this->get('proxy_user')->getOrganization()->getId();
            }
            $uploadFile = $this->uploadFileLogService->createUploadService($organizationId, $fileName, $columns, $rowsTotal, $jobNumber, $loggedInUserId, 'GS');
            $lowerCol = array_map('strtolower',$columns);
            if (! in_array(strtolower(GroupUploadConstants::EXTERNAL_ID), $lowerCol) || $rowsTotal == 0) {
                $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
                $response = new Response($uploadFile, []);
                return $response;
            }

            $this->uploadFileLogService->updateJobErrorPath($uploadFile);

            // this will discern the difference between
            // and old upload and new upload
            if (in_array(strtolower(GroupUploadConstants::GROUP_ID), $lowerCol) || $rowsTotal == 0) {
                $job = new ProcessGroupStudentBulkUpload();
            } else {
                $job = new ProcessManageGroupStudentBulkUpload();
            }
            $job->args = array(

                'key' => $fileName,
                UploadConstant::JOB_NUM => $jobNumber,
                UploadConstant::ORGN => $organizationId,
                UploadConstant::UPLOADID => $uploadFile->getId(),
                UploadConstant::USERID => $loggedInUserId
            );

            $resque->enqueue($job);
            $response = new Response($uploadFile, []);
            return $response;
        }
    }

    /**
     * Get Count.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Count",
     * section = "Counts",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/count")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getCountAction()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        } else {
            $request = Request::createFromGlobals();
            $switchUser = $request->headers->get('switch-user');
            $organizationId = $this->getLoggedInUserOrganizationId();
            if($switchUser!=null){
                $proxyUserObj = $this->container->get('proxy_user');
                if ($proxyUserObj) {
                    $organizationId = $proxyUserObj->getOrganization()->getId();
                }
            }
            $response = $this->groupUploadService->getCounts($organizationId);
            return new Response($response);
        }
    }

    /**
     * Pending API
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Subgroup Upload",
     * section = "Sub Group Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/subgroup/pending",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function pendingSubgroupAction()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        } else {
            $organizationId = $this->getLoggedInUserOrganizationId();

			//Changed from SG to G as upload is using G
            $upload = $this->uploadFileLogService->hasPendingView($organizationId, 'G');
            return new Response([
                UploadConstant::UPLOAD => $upload
            ], []);
        }
    }

    /**
     * Pending API
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Subgroup Upload",
     * section = "Sub Group Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/faculty/pending",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function pendingFacultyAction()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        } else {
            $organizationId = $this->getLoggedInUserOrganizationId();
            //Changed from GF to G as upload writes as G
            $upload = $this->uploadFileLogService->hasPendingView($organizationId, 'GF');
            return new Response([
                UploadConstant::UPLOAD => $upload
            ], []);
        }
    }

    /**
     * Pending API
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Subgroup Upload",
     * section = "Sub Group Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/student/pending",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function pendingStudentAction()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        } else {
            $organizationId = $this->getLoggedInUserOrganizationId();// Changed from GS to G as upload writes the log as G
            $upload = $this->uploadFileLogService->hasPendingView($organizationId, 'GS');
            return new Response([
                UploadConstant::UPLOAD => $upload
            ], []);
        }
    }

    /**
     * Get Status
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Subgroup Upload",
     * section = "Sub Group Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/student/{id}", defaults={"id"=-1},requirements={"id" = "^\d+$", "_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getStudentUploadLogAction($id)
    {
        $upload = $this->uploadFileLogService->findUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }

    private function convertXLStoCSV($infile, $outfile)
    {
        $fileType = PHPExcel_IOFactory::identify($infile);
        $objReader = PHPExcel_IOFactory::createReader($fileType);

        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($infile);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->save($outfile);
    }
}