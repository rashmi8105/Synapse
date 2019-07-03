<?php
namespace Synapse\UploadBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Stopwatch\Stopwatch;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\EntityNotFoundException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Response;
use Synapse\StaticListBundle\Repository\OrgStaticListRepository;
use Synapse\UploadBundle\Repository\UploadColumnHeaderDownloadMapRepository;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Generic Upload functions
 *
 * @DI\Service("upload_service")
 */
class UploadService extends AbstractService
{

    // constants
    const SERVICE_KEY = 'upload_service';

    // Scaffolding
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Resque
     */
    private $resque;

    // repository
    /**
     * @var OrgStaticListRepository
     */
    private $orgStaticListRepository;

    /**
     * @var UploadColumnHeaderDownloadMapRepository
     */
    private $uploadColumnHeaderDownloadMapRepository;

    // service
    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var CSVUtilityService
     */
    private $CSVUtilityService;

    /**
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "doctrine" = @DI\Inject("doctrine"),
     *            "container" = @DI\Inject("service_container"),
     * })
     *
     * @param $repositoryResolver
     * @param $container
     * @param $logger
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        // Service
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->CSVUtilityService =  $this->container->get(CSVUtilityService::SERVICE_KEY);

        //Repository
        $this->orgStaticListRepository = $this->repositoryResolver->getRepository(OrgStaticListRepository::REPOSITORY_KEY);
        $this->uploadColumnHeaderDownloadMapRepository =  $this->repositoryResolver->getRepository(UploadColumnHeaderDownloadMapRepository::REPOSITORY_KEY);
    }

    /**
     * This will prepare the datafile for a given uploads
     *
     * @param Person $person
     * @param string $type
     * @param organization $organization
     * @param int $uploadTypeId
     * @return Response
     * @throws EntityNotFoundException|SynapseValidationException
     */
    public function prepareDatafileJob($person, $type, $organization, $uploadTypeId)
    {
        $errorString = $this->checkDatafilePermissionReturnsErrorString($person, $type, $organization->getId(), $uploadTypeId);
        if ($errorString) {
            throw new SynapseValidationException($errorString);
        }

        $updateClass = 'Synapse\UploadBundle\Job\Update' . ucfirst($type) . 'DataFile';
        if (!class_exists($updateClass)) {
            throw new EntityNotFoundException($type . ' is not a valid datafile type');
        }

        $job = new $updateClass();
        $job->args = $this->getArgumentsForUpdateClass($type, $uploadTypeId, $organization, $person);

        $this->resque->enqueue($job);

        $response = new Response(['status' => 'queued'], []);
        return $response;
    }

    /**
     * This will get the arguments needed for the update<upload type>Datafile to run
     *
     * @param Int $uploadTypeId
     * @param Organization $organization
     * @param Person $person
     * @return array
     */
    public function getArgumentsForUpdateClass($type, $uploadTypeId, $organization, $person)
    {
        // This is required for all uploads
        $arguments = array(
            'orgId' => $organization->getId(),
            'person' => $person->getId()
        );

        switch (strtolower($type)) {
            case 'staticlist':
                $arguments['staticListId'] = $uploadTypeId;
                break;
            // TODO: add other specific data file dump creations variables to the case statement as needed
            default:
                break;
        }

        return $arguments;
    }

    /**
     * Runs additional checks to make sure the person is able to generate
     * the datafile dump the person is requesting. Returns null if there is
     * no error, returns a string of the error if there is one.
     *
     * @param Person $person
     * @param String $type
     * @param Int $organizationId
     * @param Int $uploadTypeId
     * @return String|Null
     */
    public function checkDatafilePermissionReturnsErrorString($person, $type, $organizationId, $uploadTypeId)
    {
        $returnErrorString = null;
        if (strtolower($type) == 'staticlist') {
            // Kind of silly but getCurrentOrgAcademicYearId will throw an error if there is not a current academic year
            // This needs to happen before the static list job is ran so the job doesn't die when an error is thrown
            if (!$this->academicYearService->getCurrentOrgAcademicYearId($organizationId)) {
                $returnErrorString .= 'There is no currently active academic year; ';
            }

            $doesPersonHaveAccessToStaticList = $this->orgStaticListRepository->FindOneBy(['person' => $person, 'id' => $uploadTypeId]);
            if (!$doesPersonHaveAccessToStaticList) {
                $returnErrorString .= 'You do not have access to this Static List; ';
            }

        }
        return $returnErrorString;
    }

    /**
     * Used to download the faculty upload template
     *
     * @param string $templateName
     * @param string $downloadFileName
     * @param array $additionalColumnHeaders
     * @throws SynapseValidationException
     * @return string
     */
    public function downloadUploadTemplate($templateName, $downloadFileName, $additionalColumnHeaders = [])
    {
        if (empty($templateName)) {
            throw new SynapseValidationException("Invalid template name.");
        }

        if (empty($downloadFileName)) {
            throw new SynapseValidationException("Invalid download file name");
        }


        $columns = $this->uploadColumnHeaderDownloadMapRepository->getUploadHeaders($templateName, 'template', 'upload_column_display_name');
        if (!is_array($columns)) {
            $columns = [];
        }
        if (!is_array($additionalColumnHeaders)) {
            $additionalColumnHeaders = [];
        }

        $columns = array_unique(array_merge($columns, $additionalColumnHeaders));
        $downloadFile = $this->createDownloadTemplate($columns, $downloadFileName);
        return $downloadFile;
    }

    /**
     * creates the temporary csv for downloads.
     *
     * @param array $headerColumns
     * @param string $fileName
     * @return string
     */
    private function createDownloadTemplate($headerColumns, $fileName)
    {
        $tempFileName = "/tmp/$fileName";
        $CSVWriter = $this->CSVUtilityService->createCSVFileInTempFolder($fileName);
        $this->CSVUtilityService->writeToFile($CSVWriter, $headerColumns, true);
        return $tempFileName;
    }

}