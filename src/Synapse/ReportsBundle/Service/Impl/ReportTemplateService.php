<?php
namespace Synapse\ReportsBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\ReportsBundle\Entity\ReportsTemplate;
use Synapse\ReportsBundle\EntityDto\ReportsTemplatesDto;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportsTemplateRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("report_template_service")
 */
class ReportTemplateService extends AbstractService
{

    const SERVICE_KEY = 'report_template_service';

    /**
     * @var ReportsTemplateRepository
     */
    private $reportTemplateRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var ReportsRepository
     */
    private $reportRepository;


    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
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

        // Repository
        $this->reportTemplateRepository = $this->repositoryResolver->getRepository(ReportsTemplateRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->reportRepository = $this->repositoryResolver->getRepository(ReportsRepository::REPOSITORY_KEY);
    }

    /**
     * Creates Reports Template.
     *
     * @param ReportsTemplatesDto $reportTemplatesDto
     * @return ReportsTemplatesDto
     */
    public function createReportTemplate(ReportsTemplatesDto $reportTemplatesDto)
    {
        $this->logger->info("Create Report Template");
        
        $personId = $reportTemplatesDto->getPersonId();
        $personInstance = $this->personRepository->find($personId);
        
        $organizationId = $reportTemplatesDto->getOrganizationId();
        $organizationInstance = $this->organizationRepository->find($organizationId);
        
        $requestJsonArray = $reportTemplatesDto->getRequestJson();
        $templateName = $reportTemplatesDto->getTemplateName();

        $reportId = $requestJsonArray['report_id'];
        $reportsInstance = $this->reportRepository->find($reportId);
        $this->isObjectExist($reportsInstance, 'Report Not Found', 'reports_not_found');
        $this->isObjectExist($personInstance, 'Person Not Found', 'person_not_found');
        
        $reportTemplate = new ReportsTemplate();
        $reportTemplate->setPerson($personInstance);
        $reportTemplate->setOrganization($organizationInstance);
        $reportTemplate->setReports($reportsInstance);
        $reportTemplate->setTemplateName($templateName);
        $requestJson = json_encode($requestJsonArray);
        $reportTemplate->setFilterCriteria($requestJson);
        $validator = $this->container->get('validator');
        $errors = $validator->validate($reportTemplate);
        if (count($errors) > 0) {
            $errorsString = $errors[0]->getMessage();
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'duplicate_report_template');
        }
        $reportTemplateObj = $this->reportTemplateRepository->create($reportTemplate);
        $this->reportTemplateRepository->flush();
        $reportTemplatesDto->setId($reportTemplateObj->getId());
        
        return $reportTemplatesDto;
    }

    /**
     * Edit Reports Template.
     *
     * @param ReportsTemplatesDto $reportTemplatesDto
     * @return mixed
     */
    public function editReportTemplate(ReportsTemplatesDto $reportTemplatesDto)
    {
        $this->logger->info("updating  Report Template");

        $personId = $reportTemplatesDto->getPersonId();
        $personInstance = $this->personRepository->find($personId);
        
        $organizationId = $reportTemplatesDto->getOrganizationId();
        $organizationInstance = $this->organizationRepository->find($organizationId);
        
        $requestJsonArray = $reportTemplatesDto->getRequestJson();
        
        $templateName = $reportTemplatesDto->getTemplateName();
        
        if(isset($requestJsonArray['report_id'])){
            $requestJsonArray['reportId'] = $requestJsonArray['report_id'];
        }
        $reportId = $requestJsonArray['reportId'];
        
        $reportsInstance = $this->reportRepository->find($reportId);
        $this->isObjectExist($reportsInstance, 'Report Not Found', 'reports_not_found');
        $this->isObjectExist($personInstance, 'Person Not Found', 'person_not_found');
        
        $reportTemplate = $this->reportTemplateRepository->findOneById($reportTemplatesDto->getId());
        
        $reportTemplate->setPerson($personInstance);
        $reportTemplate->setOrganization($organizationInstance);
        $reportTemplate->setReports($reportsInstance);
        $reportTemplate->setTemplateName($templateName);
        $requestJson = json_encode($requestJsonArray);
        $reportTemplate->setFilterCriteria($requestJson);
        $this->reportTemplateRepository->flush();
        
        return $reportTemplate;
    }

    private function isObjectExist($object, $message, $key)
    {
        if (! isset($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }
	
	public function checkPermission($reportId, $personId)
	{	
		$this->orgRoleRepo = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrganizationRole');
		$this->reportRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORTS_REPO);
        $reportObj = $this->reportRepository->findOneById($reportId);		
        
		$personObj = $this->container->get('person_service')->find($personId);
		$reportService = $this->container->get('reports_service');
		$orgId = $personObj->getOrganization()->getId();
		$isCoordinator = $this->orgRoleRepo->getUserCoordinatorRole($orgId, $personId);
		    if($reportObj->getShortCode() == "FUR"){
		        $teamMembersRepo = $this->repositoryResolver->getRepository('SynapseCoreBundle:TeamMembers');
		        
		        $isTeamMember = $teamMembersRepo->findOneBy(array(
		            'isTeamLeader' => 1,
		            'person' => $personId,
		            'organization' => $orgId
		        ));
                if ($isTeamMember || $isCoordinator) {
		            $reports['status'] = true;
		        }else{
		            $reportService->checkAccessPermission($personId, $reportObj);
		        }
		    }else{
    		    if ($isCoordinator && $reportObj->getIsCoordinatorReport() == 'y') {
    	            // dont check anything.. as coordinator should be able to access the coordinators report ,no permission check needed
    	        } else {
    	            $reportService->checkAccessPermission($personId, $reportObj);
    	        }
		    }
		$reports['status'] = true;
		return $reports;
	}
}