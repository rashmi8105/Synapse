<?php
namespace Synapse\RestBundle\Controller;

use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Service\Impl\TalkingPointService;
use Synapse\UploadBundle\Job\ProcessTalkingPointsUpload;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\RestBundle\Entity\Response;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\CSVFile;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Resque;
use PHPExcel_IOFactory;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * TalkingPointsController
 *
 * @package Synapse\RestBundle\Controller
 *          @Rest\Prefix("/talkingpoints")
 */
class TalkingPointsController extends AbstractAuthController
{
    
    /**
     * @var Logger
     *
     *      @DI\Inject(SynapseConstant::LOGGER_KEY)
     */
    private $logger;
    
    /**
     * @var TalkingPointService
     *     
     *      @DI\Inject(TalkingPointService::SERVICE_KEY)
     */
    private $talkingPoint;

    /**
     * @var UploadFileLogService
     *     
     *      @DI\Inject(UploadFileLogService::SERVICE_KEY)
     */
    private $uploadFileLogService;

    /**
     * Gets talking points.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Talking Points",
     * output = "Synapse\RestBundle\Entity\TalkingPointsResponseDto",
     * section = "Talking Points",
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
     * @Rest\GET("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getTalkingPointsAction()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            $this->logger->error("Get Talking Point- Not an authenticated user.");
            throw new AccessDeniedException();
        }

        $talkingPoints = $this->talkingPoint->getTalkingPoints();
        return new Response($talkingPoints);
    }

    /**
     * Get TalkingPoints upload template.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Talking Points Upload Template",
     * section = "Talking Points",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/template")
     * @QueryParam(name="type", strict=true, description="type of template, type = talkingpoints")
     *
     * @codeCoverageIgnore
     */
    public function getTalkingPointsUploadTemplateAction()
    {
        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="talking-points-template.csv"');
        
        $talkingPointsTemplate = [
            'QuestionProfileItem',
            'Item',
            'Kind',
            'WeaknessText',
            'StrengthText',
            'WeaknessLow',
            'WeaknessHigh',
            'StrengthLow',
            'StrengthHigh'
        ];

        $talkingPoints = implode(",", $talkingPointsTemplate);
        echo $talkingPoints;
        
        echo "\n";
        
        exit();
    }

    /**
     * Create TalkingPoints Upload Policy
     * The policy required for making authenticated requests using HTTP POST is a UTF-8 and Base64 encoded document written in JavaScript Object Notation (JSON)
     * that specifies conditions that the request must meet.
     * Depending on how you design your policy document, you can control per-upload, per-user, for all uploads,
     * or according to other designs that meet your needs
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Talking Points Upload Policy",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Talking Points",
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
    public function createTalkingPointsUploadPolicyAction()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            $this->logger->error("Create Talking Points Upload Policy Action- Not an authenticated user.");
            throw new AccessDeniedException();
        }
        
        $awsSecret = $this->container->getParameter(CourseConstant::AMAZONSECRET);
        $expire = date(CourseConstant::DATE_FORMAT, strtotime(UploadConstant::PLUS15MIN, time()));
        $policyDocument = CourseConstant::EXPIRATION . $expire . '",
        "conditions": [
                        {"bucket": "ebi-synapse-bucket"},
                        ["starts-with", "$key", "talking-points/"],
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
     * Gets existing talking points.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Existing Talking Points",
     * section = "Talking Points",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/download-existing")
     * @Rest\View(statusCode=200)
     *
     *
     */
    public function getTalkingPointsDownloadExistingAction()
    {
        $type = 'TP';
        $upload = $this->uploadFileLogService->getLastRowByType($type);
        $fileName = "{$upload['id']}-talking-points-data.csv";
    
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');

        $file = fopen("data://talking_points_uploads/{$fileName}", 'r');

        if ($file) {
            fpassthru($file);
            fClose($file);
        } else {
            throw new SynapseValidationException('File Name Not Found');
        }
    }

    /**
     * Gets pending talkingpoints uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Upload Pending Talking Points",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Talking Points",
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
     * @Rest\Get("/pending/{orgId}")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getPendingTalkingPointsUploadAction()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            $this->logger->error("Get Pending Talking Points Upload Action- Not an authenticated user.");
            throw new AccessDeniedException();
        }
        //Need to get EBI Org Id
        $upload = $this->uploadFileLogService->hasPendingView(1, 'TP');
        return new Response([
            'upload' => $upload
        ], []);
    }

    /**
     * Create API to Upload Talking Points for Faculty.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Talking Points Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Talking Points",
     * statusCodes = {
     *                  201 = "Academic Update Request was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="organization")
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createTalkingPointsUploadAction(ParamFetcher $paramFetcher)
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            $this->logger->error("Create Talking Points Upload Action- Not an authenticated user.");
            throw new AccessDeniedException();
        }
        
		//Need to get EBI Org Id
        $organization = 1;
        $key = $paramFetcher->get('key');
        
        $pathParts = pathinfo($key);
        
        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://talking_points_uploads/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://talking_points_uploads/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }
        
        $file = new CSVFile("data://talking_points_uploads/$key");
        $file->seek(PHP_INT_MAX);
        $rowsTotal = $file->key();
        $file->seek(0);
        foreach ($file as $idx => $row) {
            $columns = array_keys($row);
            break;
        }
        
        $resque = $this->get(UploadConstant::RESQUE);
        
        $jobNumber = uniqid();
        /*
         * Passing logged in user id
        */

        $loggedInUserId = $this->getLoggedInUserId()?:null;
        $uploadFile = $this->uploadFileLogService->createTalkingPointsUploadLog($organization, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId);
        
        if ($rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }
        
        $this->uploadFileLogService->updateJobErrorPath($uploadFile);
        
        $job = new ProcessTalkingPointsUpload();
        
        $job->args = array(
            'organization' => $organization,
            'key' => $key,
            'jobNumber' => $jobNumber,
            'uploadId' => $uploadFile->getId()
        );
        
        $resque->enqueue($job);

        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Gets talking points.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Talking Points",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Campus",
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
     * @Rest\Get("/{id}")
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getTalkingPointsUploadAction($id)
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            $this->logger->error("Get Talking Points Upload Action - Not an authenticated user.");
            throw new AccessDeniedException();
        }
        
        $upload = $this->uploadFileLogService->findAllUploadLogs($id, 'TP');
        $response = new Response($upload, array());
        return $response;
    }
    
    /*
     * @codeCoverageIgnore
     */
    private function myInArray($array, $value, $key)
    {
        // loop through the array
        foreach ($array as $val) {
            // if $val is an array cal myInArray again with $val as array input
            if (is_array($val)) {
                if ($this->myInArray($val, $value, $key)) {
                    return true;
                }
            } else {
                if ($array[$key] == $value) {
                    return true;
                }
            }
        }
        return false;
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

    private function hex2b64($str)
    {
        $raw = '';
        for ($i = 0; $i < strlen($str); $i += 2) {
            $raw .= chr(hexdec(substr($str, $i, 2)));
        }
        return base64_encode($raw);
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