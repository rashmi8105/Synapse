<?php
namespace Synapse\ReportsBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Process\Process;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\CoreBundle\Util\Helper;
use Synapse\ReportsBundle\Entity\ReportsRunningStatus;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Service\PdfReportsServiceInterface;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("pdf_reports_service")
 */
class PdfReportsService extends AbstractService implements PdfReportsServiceInterface
{

    const SERVICE_KEY = 'pdf_reports_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;


    // Services

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var TokenService
     */
    private $tokenService;


    // Repositories

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var ReportsRepository
     */
    private $reportsRepository;


    /**
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
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
        $this->ebiConfigService = $this->container->get('ebi_config_service');
        $this->tokenService = $this->container->get('token_service');

        // Repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:EbiConfig');
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgAcademicYear');
        $this->personRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Person');
        $this->reportsRepository = $this->repositoryResolver->getRepository('SynapseReportsBundle:Reports');

    }

	public function createReportRunningStatus(ReportRunningStatusDto $reportRunningDto , $retentionStudentArr =  null)
	{
	    $this->logger->info("createReportRunningStatus");
		$this->reportsRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_RUNNING_STATUS_REPO);
		$this->personRepository = $this->repositoryResolver->getRepository(ReportsConstants::PERSON_REPO);
		$this->organizationRepository = $this->repositoryResolver->getRepository(ReportsConstants::ORG_REPO);
		$this->reportRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORTS_REPO);

		$personId = $reportRunningDto->getPersonId();
		$person = $this->personRepository->find($personId);
		$this->isObjectExist($person, 'Person Not Found', 'person_not_found');

		$orgId = $reportRunningDto->getOrganizationId();
		$organization = $this->organizationRepository->find($orgId);
		$this->isObjectExist($organization, 'Organization Not Found', 'organization_not_found');


		$reportSection = $reportRunningDto->getReportSections();



		$reportId = $reportSection['reportId'];

		$reports = $this->reportRepository->find($reportId);

		$this->isObjectExist($reports, 'Reports Not Found', 'reports_not_found');


		$reportShortCode = $reports->getShortCode();


		$reportRunningStatus = new ReportsRunningStatus();

		$reportRunningStatus->setPerson($person);
		$reportRunningStatus->setOrganization($organization);
		$reportRunningStatus->setReports($reports);
		$reportRunningStatus->setReportCustomTitle($reportRunningDto->getReportCustomTitle());
		$reportRunningStatus->setStatus('Q');
		$filterCriteria = $reportRunningDto->getSearchAttributes();
		$sql = $this->container->get('search_service')->getStudentListBasedCriteria($filterCriteria, $orgId, $personId, '', true);

		$sql = $this->container->get('reports_service')->replacePlaceHolders($sql, $organization->getId());

		/*
		 * Added for PRR and CR report
		 */

		$studentArrText =  '';
		if(!is_null($retentionStudentArr) && ($reportShortCode == "PRR" || $reportShortCode == "CR")){

		    if(count($retentionStudentArr) > 0){
		        $studentArrText =  implode(",",$retentionStudentArr);
		    }else{
		        $studentArrText =  "-1" ;
		    }
		    $sql = $sql . " AND p.id IN ($studentArrText)";
		}

		/*
		 * Added for PRR report
		*/

		$filteredStudents = $this->reportsRunningStatusRepository->getFilteredStudents($sql, $organization->getId());
		if(!empty($filteredStudents))
		{
			foreach($filteredStudents as $studentList)
			{
				$students[] = $studentList['person_id'];
			}
			$filter_students = implode(',',$students);
		}else {
			$filter_students = NULL;
		}

		$reportRunningStatus->setFilteredStudentIds($filter_students);
        $filterCriteriaJson = json_encode($filterCriteria);
		$reportRunningStatus->setFilterCriteria($filterCriteriaJson);
		$reportRunningStatus = $this->reportsRunningStatusRepository->create($reportRunningStatus);
		$this->reportsRunningStatusRepository->flush();
		$this->logger->info("Report Running status for report-id".$reportRunningDto->getReportId().' person-id'.$reportRunningDto->getPersonId(). 'is created successfully');
		$reportRunningDto->setId($reportRunningStatus->getId());
		return $reportRunningDto;
	}

	private function isObjectExist($object, $message, $key)
    {
        if (! isset($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    /**
     * Generate PDF Report using personId, instanceId, reportShortCode, zoom and timezone
     *
     * @param int $personId
     * @param string $reportInstanceId
     * @param string $reportShortCode
     * @param float $zoom
     * @param string $timeZone
     */
    public function generatePdfReport($personId, $reportInstanceId, $reportShortCode, $zoom, $timeZone)
    {
        $token = $this->tokenService->generateToken($personId)->getToken();
        $person = $this->personRepository->find($personId);
        $this->isObjectExist($person, 'Person Not Found', 'person_not_found');
        $organizationId = $person->getOrganization()->getId();

        //No organization should be handed to getSystemUrl as this is NOT used with LDAP/SAML and does not require special routing
        //The default parameters should get the unaltered skyfactor base system URL
        $systemUrl = $this->ebiConfigService->getSystemUrl();

        // Handling additional query parameters to be sent for pdf generation if they happen to be present
        $knownQueryParameters = ['person_id', 'timezone', 'access_token', 'print'];
        $httpRequestParameters = $_REQUEST;

        // TODO: Figure out if this is supposed to be appending instead of replacing this really awful query parameter string thing.
        $additionalQueryParameterString = '';
        foreach ($httpRequestParameters as $queryParameter => $parameterValue) {
            if (!in_array($queryParameter, $knownQueryParameters)) {
                $additionalQueryParameterString = "&$queryParameter=$parameterValue";
            }
        }

        $pageUrl = '#/reports/' . $reportShortCode . '/' . $reportInstanceId . '?person_id=' . $personId . '&timezone=' . $timeZone . '&access_token=' . $token . '&print=PDF' . $additionalQueryParameterString;

        $pdfUrl = $systemUrl . $pageUrl;

        $reportObject = $this->reportsRepository->findOneBy(['shortCode' => $reportShortCode]);

        if ($reportObject) {
            $reportName = $reportObject->getName();
            $reportName = str_replace(' ', '', $reportName);
        } else {
            $reportName = $reportShortCode;
        }

        $currentDate = new \DateTime('now');
        $currentDateAtMidnight = $currentDate->setTime(0, 0, 0);

        $yearId = $this->orgAcademicYearRepository->getCurrentAcademicDetails($currentDateAtMidnight, $organizationId);
        if ($yearId) {
            $currentAcademicYear = $yearId[0]['yearId'];
        } else {
            $currentAcademicYear = '';
        }

        $fileName = "$organizationId-$personId-$currentAcademicYear-$reportName-" . time();
        $fileName = md5(Helper::encrypt($fileName)) . '.pdf';
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=" . $fileName);
        $tmpFile = '/tmp/' . $fileName;

        // "A4" is the standard paper size
        // 72 is the defacto standard for printer resolution. It's 72dpi so that the PDF paper size is correct based on what's rendered
        $process = new Process('/usr/local/bin/phantomjs --web-security=false --ssl-protocol=tlsv12 ' .
            $this->container->getParameter('kernel.root_dir') . '/../pdfify.js ' .
            "'$pdfUrl' $tmpFile A4 72 $zoom"
        );
        $process->run();

        readfile($tmpFile);
        unlink($tmpFile);
    }
}