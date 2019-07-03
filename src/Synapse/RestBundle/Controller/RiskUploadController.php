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
use Symfony\Component\HttpFoundation\Request;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\CoreBundle\Util\CSVFile;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Controller\AbstractSynapseController;
use Synapse\RestBundle\Entity\Response;
use Synapse\RiskBundle\Util\Constants\RiskModelConstants;
use Synapse\RiskBundle\Util\Constants\RiskVariableConstants;
use Synapse\UploadBundle\Job\ProcessRiskModelAssignmentsUpload;
use Synapse\UploadBundle\Job\ProcessRiskModelUpload;
use Synapse\UploadBundle\Job\ProcessRiskVariableUpload;
use Synapse\UploadBundle\Service\Impl\RiskModelUploadService;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Class RiskUploadController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/riskupload")
 */
class RiskUploadController extends AbstractAuthController
{

    /**
     * @var RiskModelUploadService riskModelUpload service
     *
     *      @DI\Inject(UploadFileLogService::SERVICE_KEY)
     */
    private $riskModelUploadService;

    /**
     *
     * @var UploadFileLogService uploadFileLog service
     *
     *      @DI\Inject(UploadFileLogService::SERVICE_KEY)
     */
    private $uploadFileLogService;

    /**
     * Template to upload risk model
     *
     * @Rest\Get("/template/modelassignment")
     *
     * @codeCoverageIgnore
     */
    public function getModelAssignmentUploadTemplateAction()
    {
        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="risk-administration-upload-template.csv"');
        
        $assignmentTemplate = [
            RiskModelConstants::ORGID,
            RiskModelConstants::RISKGROUPID,
            RiskModelConstants::MODELID,
            RiskModelConstants::COMMANDS
        ];
        
        $modelAssignmentTemplate = implode(",", $assignmentTemplate);
        echo $modelAssignmentTemplate;
        echo "\n";
        exit();
    }

    /**
     * Template to upload risk model
     *
     * @Rest\Get("/template/riskmodels")
     *
     * @codeCoverageIgnore
     */
    public function getModelUploadTemplateAction()
    {
        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="modelassignment-upload-template.csv"');
        
        $assignmentTemplate = [
            RiskModelConstants::MODELID,
            RiskModelConstants::RISKVARNAME,
            RiskModelConstants::WEIGHT,
            RiskModelConstants::COMMANDS
        ];
        
        $modelAssignmentTemplate = implode(",", $assignmentTemplate);
        echo $modelAssignmentTemplate;
        echo "\n";
        exit();
    }

    /**
     * Template to upload risk model
     *
     * @Rest\Get("/template/riskvariable")
     *
     * @codeCoverageIgnore
     */
    public function getRiskVariabletUploadTemplateAction()
    {
        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="riskvariable-upload-template.csv"');
        
        $template = [
            
            RiskVariableConstants::RISK_VAR_RISKVARNAME,
            RiskVariableConstants::RISK_VAR_RISKVARTYPE,
            RiskVariableConstants::RISK_VAR_CALCULATED,
            RiskVariableConstants::RISK_VAR_SOURCETYPE,
            RiskVariableConstants::RISK_VAR_CAMPUSID,
            RiskVariableConstants::RISK_VAR_SOURCEID,
            
            'B1Min',
            'B1Max',
            'B2Min',
            'B2Max',
            'B3Min',
            'B3Max',
            'B4Min',
            'B4Max',
            'B5Min',
            'B5Max',
            'B6Min',
            'B6Max',
            'B7Min',
            'B7Max',
            'B1Cat',
            'B2Cat',
            'B3Cat',
            'B4Cat',
            'B5Cat',
            'B6Cat',
            'B7Cat',
            RiskVariableConstants::RISK_VAR_CALTYPE,
            RiskVariableConstants::RISK_VAR_CALMIN,
            RiskVariableConstants::RISK_VAR_CALMAX
        ];
        
        $template = implode(",", $template);
        echo $template;
        echo "\n";
        exit();
    }

    /**
     * Creates a new risk variable upload.
     *
     * @ApiDoc(
     * resource = true,
     * output = "Synapse\RiskBundle\Entity\RiskVariable",
     * description = "Create Risk Variable Upload",
     * section = "Risk Upload",
     * statusCodes = {
     *                  201 = "Risk variable upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/riskvariable",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createRiskVariableUploadAction(ParamFetcher $paramFetcher)
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        }
        $key = $paramFetcher->get('key');
        
        $pathParts = pathinfo($key);
        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://risk_uploads/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://risk_uploads/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }
        
        $file = new CSVFile(UploadConstant::DATA_SLASH . RiskModelConstants::RISK_DIR . "$key");
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
        $uploadFile = $this->uploadFileLogService->createRiskVariableUploadLog(1, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId, 'RV');
        
        if (! in_array('RiskVarName', $columns) || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }
        
        $this->uploadFileLogService->updateJobErrorPath($uploadFile);
        
        $job = new ProcessRiskVariableUpload();
        
        $job->args = array(
            
            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId(),
            UploadConstant::USERID => $loggedInUserId
        );
        
        $resque->enqueue($job, true);
        
        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Gets a risk model assignment upload policy.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Model Assignment Upload Policy",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Risk Upload",
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
     * @Rest\Get("/policy")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function riskModelAssignmentUploadPolicyAction()
    {
        $this->authenticate();
        $awsSecret = $this->container->getParameter(CourseConstant::AMAZONSECRET);
        $expire = date(CourseConstant::DATE_FORMAT, strtotime('+ 15 minutes', time()));
        $policyDocument = CourseConstant::EXPIRATION . $expire . '",
        "conditions": [
                        {"bucket": "ebi-synapse-bucket"},
                        ["starts-with", "$key", "risk-uploads/"],
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

    /**
     * Creates a risk model assignment upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Risk Model Assignment Upload",
     * output = "Synapse\RiskBundle\Entity\RiskVariable",
     * section = "Risk Upload",
     * statusCodes = {
     *                  201 = "Risk model assignment upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/riskmodels/assignments",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="organization")
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function riskModelAssignmentUploadAction(ParamFetcher $paramFetcher)
    {
        $key = $paramFetcher->get('key');
        
        $pathParts = pathinfo($key);
        
        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen(UploadConstant::DATA_SLASH . RiskModelConstants::RISK_DIR . "$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", UploadConstant::DATA_SLASH . RiskModelConstants::RISK_DIR . "{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }
        
        $file = new CSVFile(UploadConstant::DATA_SLASH . RiskModelConstants::RISK_DIR . "$key");
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
        $uploadFile = $this->uploadFileLogService->createRiskVariableUploadLog(1, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId, 'RMA');
        if (! in_array(RiskModelConstants::ORGID, $columns) || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }
        
        $this->uploadFileLogService->updateJobErrorPath($uploadFile);
        
        $job = new ProcessRiskModelAssignmentsUpload();
        
        $job->args = array(
            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId(),
            UploadConstant::USERID => $loggedInUserId
        );
        
        $resque->enqueue($job);
        
        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Creates a new risk model upload.
     *
     * @ApiDoc(
     * resource = true,
     * output = "Synapse\RiskBundle\Entity\RiskVariable",
     * description = "Create Risk Model Upload",
     * section = "Risk Upload",
     * statusCodes = {
     *                  201 = "Risk model upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/riskmodels",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function riskModelUploadAction(ParamFetcher $paramFetcher)
    {
        $key = $paramFetcher->get('key');
        
        $pathParts = pathinfo($key);
        
        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen(UploadConstant::DATA_SLASH . RiskModelConstants::RISK_DIR . "$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", UploadConstant::DATA_SLASH . RiskModelConstants::RISK_DIR . "{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }
        
        $file = new CSVFile(UploadConstant::DATA_SLASH . RiskModelConstants::RISK_DIR . "$key");
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
        $uploadFile = $this->uploadFileLogService->createRiskVariableUploadLog(1, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId, 'RM');
        if (! in_array(RiskModelConstants::MODELID, $columns) || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }
        
        $this->uploadFileLogService->updateJobErrorPath($uploadFile);
        
        $job = new ProcessRiskModelUpload();
        
        $job->args = array(
            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId(),
            UploadConstant::USERID => $loggedInUserId
        );
        
        $resque->enqueue($job);
        
        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Gets an existing risk variable to download.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Variable Download",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Risk Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/riskvariable",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createRiskVariableDownloadAction(ParamFetcher $paramFetcher)
    {
        $uploadFile = RiskModelConstants::RISK_AMAZON_URL . "risk-variable-data.csv";
        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Gets an existing risk model to download.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Model Download",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Risk Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/riskmodels",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createRiskModelDownloadAction(ParamFetcher $paramFetcher)
    {
        $uploadFile = RiskModelConstants::RISK_AMAZON_URL . "risk-model-data.csv";
        $response = new Response($uploadFile, []);
        return $response;
    }
    

    /**
     * Gets a risk model assignment to download.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Model Assignment Download",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Risk Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/riskmodels/assignments",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @RequestParam(name="organization")
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createRiskModelAssignmentDownloadAction(ParamFetcher $paramFetcher)
    {
        $uploadFile = RiskModelConstants::RISK_AMAZON_URL . "RiskAdministrationExport.csv";
        $response = new Response($uploadFile, []);
        return $response;
    }
    

    /**
     * Gets a pending risk variable.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Pending Risk Variable",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Risk Upload",
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
     * @Rest\Get("/riskvariable/pending",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function pendingRiskVariableAction()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        }

        $upload = $this->uploadFileLogService->ebiHasPendingView('RV');
        return new Response([
            'upload' => $upload
        ], []);
    }

    /**
     * Gets the upload log for a risk variable.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Variable Upload Log",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Risk Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/riskvariable/{id}",defaults={"id"=-1},requirements={"id" = "^\d+$", "_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getRiskvariableUploadLogAction($id)
    {
        $upload = $this->uploadFileLogService->findUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }
    
    /**
     * Get the upload log for a risk model.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Model Upload Log",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Risk Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/riskmodels/{id}",defaults={"id"=-1},requirements={"id" = "^\d+$", "_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getRiskModelUploadLogAction($id)
    {
        $upload = $this->uploadFileLogService->findUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }
   
    /**
     * Gets the upload log for a risk model assignment.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Model Assignment Upload Log",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Risk Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/riskmodels/assignments/{id}",defaults={"id"=-1},requirements={"id" = "^\d+$", "_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getRiskModelAssignmentUploadLogAction($id)
    {
        $upload = $this->uploadFileLogService->findUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Gets the total number of risk model uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Upload Model Count",
     * section = "Risk Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/riskmodels/count",requirements={ "_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getUploadModelCountAction()
    {
        $result = $this->riskModelUploadService->getTotalRecordsCount();
        $response = new Response($result, array());
        return $response;
    }
    
    private function hex2b64($str)
    {
        $raw = '';
        for ($i = 0; $i < strlen($str); $i += 2) {
            $raw .= chr(hexdec(substr($str, $i, 2)));
        }
        return base64_encode($raw);
    }

    private function hmacsha1($key, $data)
    {
        $blocksize = 64;
        $hashfunc = 'sha1';
        if (strlen($key) > $blocksize) {
            $key = pack('H*', $hashfunc($key));
        }
        $key = str_pad($key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);
        $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
        
        return bin2hex($hmac);
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
    public function authenticate()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        }
    }
}
