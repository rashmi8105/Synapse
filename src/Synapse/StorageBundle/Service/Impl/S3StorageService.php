<?php
namespace Synapse\StorageBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\OrgPersonStudentCohort;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentCohortRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\SurveyService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Repository\OrgCalcFlagsStudentReportsRepository;
use Synapse\RestBundle\Entity\SurveyAccessStatusDto;
use Synapse\RestBundle\Exception\RestException;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Handle storage of files on S3
 *
 * @DI\Service("storage_service")
 */
class S3StorageService extends AbstractService
{

    const SERVICE_KEY = 'storage_service';

    // Scaffolding
    /**
     * @var Container
     */
    private $container;

    // Services
    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var Manager
     */
    private $rbacManager;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var SurveyService
     */
    private $surveyService;

    // Repositories
    /**
     * @var OrgCalcFlagsStudentReportsRepository
     */
    private $orgCalcFlagsStudentReportsRepository;
    
    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentCohort
     */
    private $orgPersonStudentCohortRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        // Scaffolding
        $this->container = $container;

        // Services
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->surveyService = $this->container->get(SurveyService::SERVICE_KEY);

        // Repositories
        $this->orgCalcFlagsStudentReportsRepository = $this->repositoryResolver->getRepository(OrgCalcFlagsStudentReportsRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentCohortRepository = $this->repositoryResolver->getRepository(OrgPersonStudentCohortRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
    }

    /**
     *
     * @param int $orgId
     * @param int $userId
     * @return mixed
     * @throws AccessDeniedException
     */
    public function getPolicy($orgId, $userId)
    {
        $this->logger->info(" Get Policy Document ");

        $isCoordinator = $this->personService->getCoordinatorById($orgId, $userId);
        if (!$isCoordinator) {
            $orgPersonFaculty = $this->orgPersonFacultyRepository->findBy(array(
                'person' => $userId,
                'organization' => $orgId
            ));
            if (!$orgPersonFaculty) {
                throw new ValidationException([
                    "Access Denied"
                ], "Access Denied", "Access_Denied");
            }
        }

        $policyDocument = $this->generatePolicyDocument();

        return base64_encode($policyDocument);
    }

    public function getSignature($policy)
    {
        $this->logger->debug(" Get Signature " . $policy);
        $awsSecret = $this->ebiConfigService->get('AWS_Secret');

        return $this->hex2b64($this->hmacsha1($awsSecret, $policy));
    }

    /**
     * returns csv file data from the specified path
     *
     * @param string $type
     * @param string $fileName
     * @param int $orgId
     * @param string $env
     * @param int $userId
     * @param string $errorPath
     * @return string
     * @throws AccessDeniedException | SynapseValidationException
     */
    public function getFilePath($type, $fileName, $orgId, $env, $userId, $errorPath = null)
    {
        $this->logger->debug(" Get File by Type" . $type . "File " . $fileName . "Organization Id" . $orgId . "ENV" . $env);
        // Added for restricting students  to download.
        if ($userId != -1) {
            // This uses the file type to check and see if the file is made for the organization in question
            // We need to see if the file type is reasonable for this check. Student_reports
            // does not have the necessary information required to check for the username.
            if ( $type !== 'policy' && $type !== 'student_reports' ) {

                $this->assertAccessForCampusToDownloadFile($orgId, $type, $fileName);
            }
            $this->assertAccessForUserToDownloadFile($orgId, $type, $userId);
        }

        // TODO: The below code has implication for alias filename generation and will affect reports it has
        // error file generated
        if ($errorPath) {
            $type = $type . '/' . $errorPath;
        }

        if (!file_exists(UploadConstant::DATA_SLASH . $type . "/" . $fileName)) {

            throw new SynapseValidationException("Requested download file was not found.");
        }

        return $this->getAliasNameForFile($orgId, $type, $fileName);
    }

    /**
     * Fetches and returns the raw student URL if it exists and requester has permissions
     *
     * @param  int $studentId
     * @param  int $organizationId
     * @return string => returns raw image url
     * @throws SynapseValidationException
     */
    private function getStudentPhotoUrl($studentId, $organizationId)
    {
        $orgPersonStudent = $this->orgPersonStudentRepository->findOneBy([
            'person' => $studentId,
            'organization' => $organizationId
        ]);

        if (!$orgPersonStudent) {
            throw new SynapseValidationException("Student Not Found.");
        }

        if (is_null($orgPersonStudent->getPhotoUrl())) {
            throw new SynapseValidationException("Requested download file was not found.");
        }

        return $orgPersonStudent->getPhotoUrl();
    }

    /**
     * Returns the Student Survey Report PDF for the passed in $pdfFileName.  Gets the survey report PDF from the student-reports
     * directory in the Amazon S3 bucket.  Also updates the student org_survey_report_access_history table.
     * If the passed $pdfFileName, throws a validation error
     * If the database filename is not found, but does exist in S3 bucket, returns generic PDF to notify student that the report is
     * still generating.
     *
     * @param string $pdfFileName
     * @param string $environmentString
     * @return string $pdfFileName
     * @throws SynapseValidationException
     */
    public function getStudentSurveyReportPdfPath($pdfFileName, $environmentString)
    {
        //Due to the very hard nature to debug Student Reports, please DO NOT remove the below line of code
        $this->logger->debug(" Student Survey Report File: " . $pdfFileName . " ENV: " . $environmentString);
        $this->throwErrorIfS3StudentReportDoesNotExist($pdfFileName, $environmentString);

        $orgCalcFlagStudentReportObject = $this->orgCalcFlagsStudentReportsRepository->findOneBy(array(
            'fileName' => $pdfFileName
        ));

        //If the orgCalcFlagStudenReportObject is not found, there is either an error or the Pdf Generation process is currently generating
        if ($orgCalcFlagStudentReportObject) {

            $studentReportId = $orgCalcFlagStudentReportObject->getId();
            $studentSurveyDetails = $this->orgCalcFlagsStudentReportsRepository->getStudentSurveyDetailsUsingStudentReportID($studentReportId);

            $studentId = (int)$studentSurveyDetails['person_id'];
            $student = $orgCalcFlagStudentReportObject->getPerson();
            $surveyId = (int)$studentSurveyDetails['survey_id'];
            $yearId = $studentSurveyDetails['year_id'];
            $cohort = (int)$studentSurveyDetails['cohort'];

            $surveyAccessStatusDto = new SurveyAccessStatusDto();
            $surveyAccessStatusDto->setStudent($studentId);
            $surveyAccessStatusDto->setSurvey($surveyId);
            $surveyAccessStatusDto->setYear($yearId);
            $surveyAccessStatusDto->setCohort($cohort);

            $this->surveyService->updateSurveyReportStatus($surveyAccessStatusDto, $student);
        } else {
            $pdfFileName = 'NoReportFound.pdf';
            //Due to the very hard nature to debug Student Reports, please DO NOT remove the below line of code
            $this->logger->debug(" Student Survey Report File: " . $pdfFileName . " ENV: " . $environmentString);
            //Throwing Error If NoReportFound.pdf doesn't exist
            $this->throwErrorIfS3StudentReportDoesNotExist($pdfFileName, $environmentString);

        }
        return $pdfFileName;
    }


    private function generatePolicyDocument()
    {
        $awsBucket = $this->ebiConfigService->get('AWS_Bucket');
        $expire = date('Y-m-d\TG:i:s\Z', strtotime('+5 minutes', time()));
        $policyDocument = '{"expiration": "' . $expire . '",
            "conditions": [
              {"bucket": "' . $awsBucket . '"},
              ["starts-with", "$key", ""],
              {"acl": "private"},
              ["starts-with", "$Content-Type", ""],
              ["starts-with", "$filename", ""],
              ["content-length-range", 0, 524288000]
            ]
            }';
        return $policyDocument;
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

    /** This function asserts that the requested file is for a Campus and that the OrgId belongs to the Campus
     * for which the file is requested
     *
     * @param $orgId
     * @param $type
     * @param $file
     * @return void
     * @throws AccessDeniedException
     */
    private function assertAccessForCampusToDownloadFile($orgId, $type, $file)
    {
        $adminUploadDir = $this->ebiConfigService->get('Ebi_Upload_Dir');
        $adminUploadDirArray = explode(",", $adminUploadDir);

        // This is used to get base folder.
        $typeValue = explode("/", $type);

        // Skip the organization check for admin downloads
        if (!in_array($typeValue[0], $adminUploadDirArray)) {

            $matches = [];
            // all organization specific files will be of the format
            // <organization id>-<file information/Id>.csv
            // we need to get the organization id in order to check against the user's organization
            preg_match('/(\d+)-/', $file, $matches);

            if ($matches[1] != $orgId) {
                // if the file has not been made for the organization in question
                // then, throw access denied exception
                throw new AccessDeniedException();
            }
        }
    }

    /** This method asserts if the user (faculty | coordinator) has access to their respective download types
     * @param $orgId
     * @param $type
     * @param $userId
     * @return void
     * @throws AccessDeniedException
     */
    private function assertAccessForUserToDownloadFile($orgId, $type, $userId)
    {
        $coordinatorOnlyDownloadTypes = array(
            'student_uploads',
            'faculty_uploads',
            'group_uploads',
            'course_uploads',
            'course_faculty_uploads',
            'course_student_uploads',
            'academic_update_uploads'
        );

        if ( in_array($type, $coordinatorOnlyDownloadTypes) ) {

            $isCoordinator = $this->personService->getCoordinatorById($orgId, $userId);

            if (!$isCoordinator) {
                throw new AccessDeniedException();
            }
        }

        $facultyDownloadTypes = array(
            'roaster_uploads',
            'staticlist_uploads',
            'report_downloads',
            'export_csvs',
            'student_reports'
            //, 'policy'
        );

        if ( in_array($type, $facultyDownloadTypes) ) {

            $orgPersonFaculty = $this->orgPersonFacultyRepository->findBy(array(
                'person' => $userId,
                'organization' => $orgId
            ));

            if (!$orgPersonFaculty) {
                throw new AccessDeniedException();
            }
        }
    }

    /** This method generates the alias name for a given file of type
     *
     * @param $orgId
     * @param $type
     * @param $fileName
     * @return string
     */
    private function getAliasNameForFile($orgId, $type, $fileName)
    {
        // change the file name for report csv downloads at the last minute, since the old filename was being used for authentication.
        if ($type == "report_downloads") {

            $reportTypeArray = array(
                "GPA-report" => "GPA_Report_",
                "individual-survey-response" => "Individual_Response_Report_",
                "academic-update" => "Academic_Update_Report_",
                "Completion-Report" => "Completion_Report_",
                "Faculty-Staff_Usage_Report" => "Faculty-Staff_Usage_Report_",
                "group-response" => "Group_Response_Report_",
                "Persistence-Retention" => "Persistence_and_Retention_Report_",
                "profile_snapshot_report" => "Profile_Snapshot_Report_"
            );

            foreach ($reportTypeArray as $key => $value) {
                if (substr_count($fileName, $key) > 0) {
                    $dateObj = $this->dateUtilityService->getTimezoneAdjustedCurrentDateTimeForOrganization($orgId);

                    return $value . $dateObj->format("Ymd_His") . ".csv";
                }
            }
        }
        return $fileName;
    }


    /**
     * Throws an Error if the file attempting to be accessed does not exist
     *
     * @param $pdfFileName
     * @param $environmentString
     * @throws SynapseValidationException
     */
    private function throwErrorIfS3StudentReportDoesNotExist($pdfFileName, $environmentString) {
        $filepath = UploadConstant::DATA_SLASH . SynapseConstant::S3_STUDENT_SURVEY_REPORT_DIRECTORY . "/" . $pdfFileName;
        $doesFileExist = file_exists($filepath);
        if (!$doesFileExist) {
            $this->logger->error(" Student Survey Report File does not exist on S3, File: " . $pdfFileName . " Env: " . $environmentString);
            throw new SynapseValidationException('Student Survey report could not be retrieved.  Please contact Mapworks support at support@map-works.com');
        }

    }

    /**
     * This method streams file to web response stream
     *
     * @param string $fileName => the location of the file
     * @param string $aliasFileName => renames the file being streamed to the browser
     * @throws SynapseValidationException
     */
    public function streamFileToWeb ($fileName, $aliasFileName = null)
    {
        $file = fopen($fileName, 'r');
        $mime = mime_content_type($fileName);
        $this->streamGenericFileToWeb($file, $mime, $aliasFileName);
    }

    /**
     * Sets up the URL to stream a image to the browser
     *
     * @param int $loggedInUserId
     * @param int $studentId
     * @param int $organizationId
     * @param null|string $fileNameAlias
     */
    public function streamStudentPhotoURL($loggedInUserId, $studentId, $organizationId, $fileNameAlias = null)
    {
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId], $loggedInUserId);
        $studentURL = $this->getStudentPhotoUrl($studentId, $organizationId);
        $imageMimeType = $this->getImageMimeType($studentURL);
        $file = fopen($studentURL, 'r');
        $this->streamGenericFileToWeb($file, $imageMimeType, $fileNameAlias);
    }

    /**
     * returns the mime type for an image url
     *
     * @param $studentURL
     * @return string
     */
    private function getImageMimeType($studentURL){
        $imageByte  = exif_imagetype($studentURL);
        switch($imageByte) {
            case IMAGETYPE_GIF:
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
            case IMAGETYPE_PNG:
            case IMAGETYPE_BMP:
                return image_type_to_mime_type($imageByte);
            default:
                throw new SynapseValidationException("StudentPhoto references an invalid image type.  Supported image types are JPEG, GIF, PNG, and BMP.");
        }
    }

    /**
     * Streams a file to the web browser
     *
     * @param resource $file => file to be streamed
     * @param string $contentType => string of the mime type of the file
     * @param null|string $fileName => used to change the name of the file
     */
    private function streamGenericFileToWeb($file, $contentType, $fileName = null)
    {
        try {
            header("Content-Type: $contentType");
            if ( $fileName !== null ) {
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
            }
            fpassthru($file);

        } catch(\Exception $e) {
            $synapseValidationException = new SynapseValidationException('Picture file was not found');
            throw $synapseValidationException;
        }
    }

}