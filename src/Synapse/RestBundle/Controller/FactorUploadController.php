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
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\CoreBundle\Util\CSVFile;
use Synapse\RestBundle\Entity\Response;
use Synapse\UploadBundle\Job\ProcessFactorUpload;
use Synapse\UploadBundle\Service\Impl\FactorUploadService;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\UploadBundle\Service\UploadFileLogServiceInterface;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Class FactorUploadController
 *
 * @package Synapse\RestBundle\Controller
 *         
 * @Rest\Prefix("/factorupload")
 */
class FactorUploadController extends AbstractAuthController
{

    /**
     * @var UploadFileLogService
     *     
     *      @DI\Inject(UploadFileLogService::SERVICE_KEY)
     */
    private $uploadFileLogService;
    
    /**
     * @var FactorUploadService
     *
     *      @DI\Inject(FactorUploadService::SERVICE_KEY)
     */
    private $factorUploadService;
    

    private $mandatoryColoumns = [
        'LongitudinalID',
        'FactorID'
    ];

    /**
     * Creates an upload policy for a factor.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Factor Upload Policy",
     * section = "Factor Upload",
     * statusCodes = {
     *                  201 = "Factor upload policy created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/policy")
     * @Rest\View(statusCode=201)
     *
     * @return Response
     */
    public function createFactorUploadPolicyAction()
    {
        $awsSecret = UploadConstant::AWS_SECRET;
        $expire = date(UploadConstant::DATETIMEZONE, strtotime(UploadConstant::PLUS15MIN, time()));
        $policyDocument = '{"expiration": "' . $expire . '",
            "conditions": [
              {"bucket": "ebi-synapse-bucket"},
              ["starts-with", "$key", "factor-uploads/"],
              {"acl": "private"},
              ["starts-with", "$Content-Type", ""],
              ["starts-with", "$filename", ""],
              ["content-length-range", 0, 524288000]
            ]
        }';

        $policy = base64_encode($policyDocument);
        $signature = $this->hex2b64($this->hmacsha1($awsSecret, $policy));
        
        return new Response([
            UploadConstant::POLICY => $policy,
            UploadConstant::SIGNATURE => $signature
        ], []);
    }

    /**
     * Creates an upload for a factor.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Factor Upload",
     * section = "Factor Upload",
     * statusCodes = {
     *                  201 = "Factor upload created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/factor")
     * @Rest\View(statusCode=201)
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createFactorUploadAction(ParamFetcher $paramFetcher)
    {
        $key = $paramFetcher->get('key');
        $organization = - 1;
        $pathParts = pathinfo($key);
        
        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://factor_uploads/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://factor_uploads/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }
        
        $file = new CSVFile("data://factor_uploads/$key");
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
        $loggedInUserId = $this->getLoggedInUserId();
        $uploadFile = $this->uploadFileLogService->createUploadService($organization, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId, UploadConstant::UPLOAD_TYPE_FACTOR);
        
        foreach ($this->mandatoryColoumns as $col) {
            if (! in_array($col, $columns)) {
                $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
                $response = new Response($uploadFile, []);
                return $response;
            }
        }
        
        $this->uploadFileLogService->updateJobErrorPath($uploadFile);
        
        $job = new ProcessFactorUpload();
        $job->args = array(
            UploadConstant::ORGN => $organization,
            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId()
        );
        $resque->enqueue($job);

        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Get Api to downloading the existing factor.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Download Existing Factor",
     * section = "Factor Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/download-existing")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getFactorDownloadExistingAction()
    {
        $type = 'FA';
        $upload = $this->uploadFileLogService->getLastRowByType($type);
        $fileName = "{$upload['id']}-factor-data.csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        $factordata = $this->factorUploadService->getFactorDownloadData();
        
        $factorCols = $factordata['cols'];
        $factorCols = implode(",", $factorCols);
        echo $factorCols;
        echo "\n";
        $factorData = $factordata['data'];
        foreach($factorData as $fac){
            echo $fac['ebi_ques_id'].",".$fac['factor_id'];
            echo "\n";
        }
        exit;
    }
    
    
    /**
     * Get pending factor uploads
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Pending Factor Uploads",
     * section = "Factor Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/pending")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getPendingFacultyUploadAction()
    {
        $upload = $this->uploadFileLogService->ebiHasPendingView('FA');
        return new Response([
            UploadConstant::UPLOAD => $upload
            ], []);
    }
    
    
    /**
     * Gets factor uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Factor Upload",
     * section = "Factor Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/factor/{id}")
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getFactorUploadAction($id)
    {
        $upload = $this->uploadFileLogService->findUploadLog($id);
        $response = new Response($upload, array());
        return $response;
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

    /**
     * Get Factor Upload Template
     *
     * @Rest\Get("/template")
     *
     * @codeCoverageIgnore
     */
    public function getFactorTemplateAction()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="factor-template.csv"');
        $factorTemplateCols = [
            'LongitudinalID',
            'FactorID'
        ];
        
        $factorTemplateData = implode(",", $factorTemplateCols);
        echo $factorTemplateData;
        echo "\n";
        exit();
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