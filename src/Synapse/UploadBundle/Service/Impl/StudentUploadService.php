<?php

namespace Synapse\UploadBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Stopwatch\Stopwatch;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\RiskBundle\Repository\RiskGroupPersonHistoryRepository;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
* Handle student uploads
*
* @DI\Service("student_upload_service")
*/
class StudentUploadService extends AbstractService
{

    const SERVICE_KEY = 'student_upload_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    // Services

    /**
     * @var CSVUtilityService
     */
    private $csvUtilityService;


    // Repositories

    /**
     * @var RiskGroupPersonHistoryRepository
     */
    private $riskGroupPersonHistoryRepository;


    /**
     * StudentUploadService constructor.
     *
     * @DI\InjectParams({
     *     "repositoryResolver" = @DI\Inject("repository_resolver"),
     *     "logger" = @DI\Inject("logger"),
     *     "container" = @DI\Inject("service_container")
     * })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        // Services
        $this->csvUtilityService = $this->container->get(CSVUtilityService::SERVICE_KEY);

        // Repositories
        $this->riskGroupPersonHistoryRepository = $repositoryResolver->getRepository(RiskGroupPersonHistoryRepository::REPOSITORY_KEY);

    }

    /**
     * Generates risk upload CSV
     *
     * @param int $organizationId
     * @return null
     */
    public function generateRiskUploadCSV($organizationId)
    {
        $riskGroups = $this->riskGroupPersonHistoryRepository->getRiskGroupsByOrg($organizationId);
        if (count($riskGroups)) {
            foreach ($riskGroups as $riskGroup) {

                $persons = $this->riskGroupPersonHistoryRepository->getRiskGroupByOrg($organizationId, $riskGroup['riskGroupId']);
                $this->createPersonRisk($organizationId, $riskGroup['riskGroupId'], $persons);
            }
        }
    }

    /**
     * Creates risk CSV
     *
     * @param int $organizationId
     * @param int $riskGroupId
     * @param array $personRiskArray
     * @return null
     */
    public function createPersonRisk($organizationId, $riskGroupId, $personRiskArray)
    {
        $fileName = "{$organizationId}-{$riskGroupId}-risk-groups-data.csv";
        $compileFilePath = UploadConstant::DATA_SLASH . 'risk_uploads/';
        $csvHeaders = array('externalId' => 'External Id');

        if (count($personRiskArray)) {
            // CSV utility service function: Generates the CSV and stores it to defined path
            $this->csvUtilityService->generateCSV($compileFilePath, $fileName, $personRiskArray, $csvHeaders);
        }
    }

    /**
     *  Creates risk CSV of a risk group
     *
     * @param int $organizationId
     * @param int $riskGroupId
     * @return string
     */
    public function generateRiskCsvByRiskGroup($organizationId,$riskGroupId)
    {
        $persons = $this->riskGroupPersonHistoryRepository->getRiskGroupByOrg($organizationId, $riskGroupId);
        $this->createPersonRisk($organizationId, $riskGroupId, $persons);

        return "{$organizationId}-{$riskGroupId}-risk-groups-data.csv";
    }
}