<?php
namespace Synapse\HelpBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\CoreBundle\Util\Helper;
use Synapse\HelpBundle\Entity\OrgDocuments;
use Synapse\HelpBundle\EntityDto\HelpDto;
use Synapse\HelpBundle\Repository\OrgDocumentsRepository;
use Synapse\HelpBundle\Util\Constants\HelpConstants;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("help_service")
 */
class HelpService extends AbstractService
{

    const SERVICE_KEY = 'help_service';

    // Scaffolding

    /**
     *
     * @var Container
     */
    private $container;

    //Services

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    // Repositories

    /**
     * @var OrgDocumentsRepository
     */
    private $orgDocumentsRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var OrganizationlangRepository
     */
    private $organizationLangRepository;

    /**
     *
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * HelpService constructor.
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
        parent::__construct($repositoryResolver, $logger);

        // Scaffolding
        $this->container = $container;

        // Services
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);

        // Repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->organizationLangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->orgDocumentsRepository = $this->repositoryResolver->getRepository(OrgDocumentsRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);

    }

    /**
     * Create Help Link
     *
     * @param HelpDto $helpDto
     * @param int $orgId
     * @param int $loggedInUserId
     * @return HelpDto $helpDto
     */
    public function createHelp(HelpDto $helpDto, $orgId, $loggedInUserId)
    {
            $logContent = $this->loggerHelperService->getLog($helpDto);
            $this->logger->debug(" Creating Help  -  " . $logContent . "Organization Id " . $orgId );
            $isCoordinator = $this->organizationRoleRepository->getUserCoordinatorRole($orgId, $loggedInUserId);
            $this->isCoordinator($isCoordinator, HelpConstants::COORDINATOR_ACCESS_DENIED, HelpConstants::COORDINATOR_ACCESS_DENIED);

            $organizationId = $this->validateOrganization($orgId);
            $orgDocuments = new OrgDocuments();
            $orgDocuments->setOrganization($organizationId);
            $orgDocuments->setTitle($helpDto->getTitle());
            $orgDocuments->setDescription($helpDto->getDescription());
            $orgDocuments->setType('link');
            $orgDocuments->setLink($this->addURLScheme($helpDto->getLink()));
            $this->validateEntity($orgDocuments);
            $this->orgDocumentsRepository->persist($orgDocuments);
            $helpDto->setLink($orgDocuments->getLink());
            $helpDto->setId($orgDocuments->getId());
            $this->logger->info(" Create Help ");
           return $helpDto;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Synapse\HelpBundle\Service\ProfileServiceInterface::updateProfile()
     */
    public function updateHelp(HelpDto $helpDto, $orgId, $loggedInUserId)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($helpDto);
        $this->logger->debug(" Editing Help  -  " . $logContent . "Organization Id " . $orgId);
        
        $this->orgDocumentsRepo = $this->repositoryResolver->getRepository(HelpConstants::ORGDOCUMENT_ENT);
        $this->orgRoleRepo = $this->repositoryResolver->getRepository(HelpConstants::ORG_ROLE_REPO);
        $isCoordinator = $this->orgRoleRepo->getUserCoordinatorRole($orgId, $loggedInUserId);
        $this->isCoordinator($isCoordinator, HelpConstants::COORDINATOR_ACCESS_DENIED, HelpConstants::COORDINATOR_ACCESS_DENIED);
        
        $organizationId = $this->validateOrganization($orgId);
        $updateHelpRecord = $this->orgDocumentsRepo->find($helpDto->getId());
        
        if (! $updateHelpRecord) {
            $this->logger->error( "Help Bundle - Help Service - updateHelp " . HelpConstants::HELP_NOT_FOUND . $updateHelpRecord);
            throw new ValidationException([
                HelpConstants::HELP_NOT_FOUND
            ], HelpConstants::HELP_NOT_FOUND, HelpConstants::HELP_NOT_FOUND_CODE);
        }
        try {
            $updateHelpRecord->setTitle($helpDto->getTitle());
            $updateHelpRecord->setDescription($helpDto->getDescription());
            $updateHelpRecord->setLink($this->addURLScheme($helpDto->getLink()));
            $this->validateEntity($updateHelpRecord);
            $this->orgDocumentsRepo->flush();
            $helpDto->setLink($updateHelpRecord->getLink());
            return $helpDto;
        } catch (ValidationException $valExp) {
            throw $valExp;
        } catch (\Exception $e) {
            $this->logger->error( "Help Bundle - Help Service - updateHelp " . HelpConstants::HELP_DB_EXCEPTION . $e->getMessage());
            throw new ValidationException([
                HelpConstants::HELP_DB_EXCEPTION . $e->getMessage()
            ], HelpConstants::HELP_DB_EXCEPTION, HelpConstants::HELP_DB_EXCEPTION_CODE);
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Synapse\HelpBundle\Service\ProfileServiceInterface::deleteHelp()
     */
    public function deleteHelp($orgId, $helpId, $loggedInUserId)
    {
        $this->logger->debug("Delete Help by Organization Id" . $orgId . "Help Id " . $helpId);
        $this->orgDocumentsRepo = $this->repositoryResolver->getRepository(HelpConstants::ORGDOCUMENT_ENT);
        $this->orgRoleRepo = $this->repositoryResolver->getRepository(HelpConstants::ORG_ROLE_REPO);
        $isCoordinator = $this->orgRoleRepo->getUserCoordinatorRole($orgId, $loggedInUserId);
        $this->isCoordinator($isCoordinator, HelpConstants::COORDINATOR_ACCESS_DENIED, HelpConstants::COORDINATOR_ACCESS_DENIED);
        
        $organizationId = $this->validateOrganization($orgId);
        $delOrgDocs = $this->orgDocumentsRepo->findOneBy([
            HelpConstants::HELP_ID => $helpId,
            HelpConstants::HELP_ORGID => $organizationId
        ]);
        if (! $delOrgDocs) {
            $this->logger->error( "Help Bundle - Help Service - deleteHelp " . HelpConstants::HELP_NOT_FOUND . $delOrgDocs);
            throw new ValidationException([
                HelpConstants::HELP_NOT_FOUND
            ], HelpConstants::HELP_NOT_FOUND, HelpConstants::HELP_NOT_FOUND_CODE);
        }
        $this->orgDocumentsRepo->remove($delOrgDocs);
        $this->orgDocumentsRepo->flush();
        $this->logger->info("Deleted Help");
        return;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Synapse\HelpBundle\Service\ProfileServiceInterface::getHelps()
     */
    public function getHelps($orgId)
    {
        $this->logger->debug(" Get Helps by Organization Id " . $orgId);
        $this->orgDocumentsRepo = $this->repositoryResolver->getRepository(HelpConstants::ORGDOCUMENT_ENT);
        $this->validateOrganization($orgId);
        $returnSet = array();
        $getOrgHelpDocs = $this->orgDocumentsRepo->getOrgDoc($orgId);
        foreach ($getOrgHelpDocs as $getOrgHelpDoc) {
            $helpJson = $this->getHelpJSON($getOrgHelpDoc);
            $returnSet[] = $helpJson;
        }
        $this->logger->info(" Get Helps by Organization Id");
        return $returnSet;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Synapse\HelpBundle\Service\ProfileServiceInterface::getHelpDetails()
     */
    public function getHelpDetails($orgId, $helpId)
    {
        $this->logger->debug(" Get Help details by Organization Id " . $orgId . "Help Id " . $helpId);
        $this->orgDocumentsRepo = $this->repositoryResolver->getRepository(HelpConstants::ORGDOCUMENT_ENT);
        $organizationId = $this->validateOrganization($orgId);
        $helpDetails = $this->orgDocumentsRepo->getSingleHelpDetails($helpId);
        if (! $helpDetails) {
            $this->logger->error( "Help Bundle - Help Service - getHelpDetails " . HelpConstants::HELP_NOT_FOUND);
            throw new ValidationException([
                HelpConstants::HELP_NOT_FOUND
            ], HelpConstants::HELP_NOT_FOUND, HelpConstants::HELP_NOT_FOUND_CODE);
        } else {
            $helpDetail = $this->getHelpJSON($helpDetails[0]);
        }
        $this->logger->info(" Get Help Details ");
        return $helpDetails;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Synapse\HelpBundle\Service\ProfileServiceInterface::getMapWorksSupportContact()
     */
    public function getMapWorksSupportContact($orgId)
    {
        $this->logger->debug(" Get Map Works Support Contact by Organiation Id " . $orgId);
        $this->organizationlangRepository = $this->repositoryResolver->getRepository(HelpConstants::ORG_LANG_REPO);
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(HelpConstants::EBICONFIG_REPO);
		$organization = $this->validateOrganization($orgId);
        $supportEmail = $this->ebiConfigRepository->findOneByKey(HelpConstants::EBI_CONGIF_EMAIL_KEY);
        $supportPhone = $this->ebiConfigRepository->findOneByKey(HelpConstants::EBI_CONGIF_PHONE_KEY);
        $traningSiteUrl = $this->ebiConfigRepository->findOneByKey(HelpConstants::EBI_CONGIF_TRAINING_SITE_KEY);
        $sandboxSiteUrl = $this->ebiConfigRepository->findOneByKey(HelpConstants::EBI_CONGIF_SANDBOX_SITE_KEY);
        if ($supportEmail) {
            $supportEmail = $supportEmail->getValue();
        }
        if ($supportPhone) {
            $supportPhone = $supportPhone->getValue();
        }
        if ($traningSiteUrl) {
            $traningSiteUrl = $traningSiteUrl->getValue();
        }
        if ($sandboxSiteUrl) {
            $sandboxSiteUrl = $sandboxSiteUrl->getValue();
        }
        $response = array();
		$orgName = $this->organizationlangRepository->findOneBy(array('organization' => $organization->getId()));
        if (!empty($organization) && !empty($orgName)) {
			$tokenValues = array();
			$tokenValues[HelpConstants::SUB_DOMAIN_KEY] = strtolower($organization->getSubdomain());
			$mapTraningSiteUrl = Helper::generateQuery($traningSiteUrl, $tokenValues);
			unset($tokenValues);
			$tokenValues[HelpConstants::SUB_DOMAIN_KEY] = strtolower($organization->getSubdomain());
			$mapSandBoxSiteUrl = Helper::generateQuery($sandboxSiteUrl, $tokenValues);
            $response = [
                'mapworks_contact' => array(
                    'email' => $supportEmail,
                    'phone' => $supportPhone
                ),
                'demo_site_url' => $mapSandBoxSiteUrl,
                'training_site_url' => $mapTraningSiteUrl,
                'campus_name' => $orgName->getOrganizationName()
            ];
        } else {
            $this->logger->error( "Help Bundle - Help Service - getMapWorksSupportContact " . HelpConstants::ORGANIZATION_NOT_FOUND . $orgId);
            throw new ValidationException([
                HelpConstants::ORGANIZATION_NOT_FOUND
            ], HelpConstants::ORGANIZATION_NOT_FOUND, HelpConstants::ORGANIZATION_NOT_FOUND_CODE);
        }
        
        return $response;
    }

    /**
     * Check $orgId is available in Organazation Table
     */
    private function validateOrganization($orgId)
    {
        $this->organizationRepo = $this->repositoryResolver->getRepository(HelpConstants::ORGANIZATION_ENT);
        $organization = $this->organizationRepo->find($orgId);
        if (! $organization) {
            throw new ValidationException([
                HelpConstants::ORGANIZATION_NOT_FOUND
            ], HelpConstants::ORGANIZATION_NOT_FOUND, HelpConstants::ORGANIZATION_NOT_FOUND_CODE);
        }
        return $organization;
    }

    /**
     * Validate entity
     */
    private function validateEntity($entity)
    {
        $validator = $this->container->get('validator');
        $errors = $validator->validate($entity);
        if (count($errors) > 0) {
            $errorsString = "";
            $errorsString = $errors[0]->getMessage();
            
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'entity_validation');
            return;
        }
    }

    private function addURLScheme($url, $scheme = 'http://')
    {
        return parse_url($url, PHP_URL_SCHEME) === null ? $scheme . $url : $url;
    }

    /**
     * Save Help Document Information
     *
     * @param string $title
     * @param string $description
     * @param string $displayFileName
     * @param string $filePath
     * @param int $orgId
     * @param int $loggedInUserId
     * @return HelpDto $helpDto
     */
    public function createHelpDoc($title, $description, $displayFileName, $filePath, $orgId, $loggedInUserId)
    {
            $this->logger->debug(" Create Help Documentation " . "Title " . $title . "Description " . $description . "Display Filename " . $displayFileName . "File Path " . $filePath . "Organization id " . $orgId);
            $isCoordinator = $this->organizationRoleRepository->getUserCoordinatorRole($orgId, $loggedInUserId);
            $this->isCoordinator($isCoordinator, HelpConstants::COORDINATOR_ACCESS_DENIED, HelpConstants::COORDINATOR_ACCESS_DENIED);
        
            $organizationId = $this->validateOrganization($orgId);
            $orgDocuments = new OrgDocuments();
            $orgDocuments->setOrganization($organizationId);
            $orgDocuments->setTitle($title);
            $orgDocuments->setDescription($description);
            $orgDocuments->setType(HelpConstants::HELP_TYPE_FILE);
            $orgDocuments->setFilePath($filePath);
            $orgDocuments->setDisplayFilename($displayFileName);
            $this->orgDocumentsRepository->persist($orgDocuments);
            $helpDto = new HelpDto();
            $helpDto->setTitle($title);
            $helpDto->setDescription($description);
            $helpDto->setFileName($displayFileName);
            $helpDto->setId($orgDocuments->getId());
            return $helpDto;
    }

    /**
     * Save Help Document Information
     */
    public function updateHelpDoc($id, $title, $description, $displayFileName, $filePath, $orgId, $loggedInUserId)
    {
        $this->logger->debug(" Update Help Documentation by Id " . $id . "Title " . $title . "Description " . $description . "Display Filename " . $displayFileName . "File Path " . $filePath . "Organization id " . $orgId . "Logged In User Id" . $loggedInUserId);
        $this->orgDocumentsRepo = $this->repositoryResolver->getRepository(HelpConstants::ORGDOCUMENT_ENT);
        $this->orgRoleRepo = $this->repositoryResolver->getRepository(HelpConstants::ORG_ROLE_REPO);
        
        $isCoordinator = $this->orgRoleRepo->getUserCoordinatorRole($orgId, $loggedInUserId);
        $this->isCoordinator($isCoordinator, HelpConstants::COORDINATOR_ACCESS_DENIED, HelpConstants::COORDINATOR_ACCESS_DENIED);
        
        $organizationId = $this->validateOrganization($orgId);
        $updateHelpRecord = $this->orgDocumentsRepo->find($id);
        
        if (! $updateHelpRecord) {
            throw new ValidationException([
                HelpConstants::HELP_NOT_FOUND
            ], HelpConstants::HELP_NOT_FOUND, HelpConstants::HELP_NOT_FOUND_CODE);
        }
        try {
            $updateHelpRecord->setTitle($title);
            $updateHelpRecord->setDescription($description);
            $updateHelpRecord->setFilePath($filePath);
            $updateHelpRecord->setDisplayFilename($displayFileName);
            $this->orgDocumentsRepo->flush();
            $helpDto = new HelpDto();
            $helpDto->setTitle($title);
            $helpDto->setDescription($description);
            $helpDto->setFileName($displayFileName);
            $helpDto->setId($id);
            
            return $helpDto;
        } catch (ValidationException $valExp) {
            throw $valExp;
        } catch (\Exception $e) {
            $this->logger->error( "Help Bundle - Help Service - updateHelpDoc " . HelpConstants::HELP_DB_EXCEPTION . $e->getMessage());
            throw new ValidationException([
                HelpConstants::HELP_DB_EXCEPTION . $e->getMessage()
            ], HelpConstants::HELP_DB_EXCEPTION, HelpConstants::HELP_DB_EXCEPTION_CODE);
        }
        return;
    }

    /**
     * Upload Help Document Information
     */
    public function uploadDoc($orgId, $key, $newKey, $jobNumber, $uploadFileObj)
    {
        $this->logger->debug(" UploadDoc " . "Organization Id" . $orgId . "Key " . $key . "New Key" . $newKey . "Job Number " . $jobNumber );
        $cols = [];
        $row = 1;
        
        file_put_contents("data://help_uploads/" . $newKey, fopen("data://help_uploads/" . $key, 'r'));
        $uploadFile = $uploadFileObj->createHelpUploadLog($orgId, $newKey, $cols, $row, $jobNumber);
        $this->logger->info("Upload Help Document Information");
        return $uploadFile;
    }

    private function getHelpJSON($getOrgHelpDoc)
    {
        if ($getOrgHelpDoc[HelpConstants::HELP_TYPE] == HelpConstants::HELP_TYPE_LINK) {
            $rs = [
                HelpConstants::HELP_ID => $getOrgHelpDoc[HelpConstants::HELP_ID],
                HelpConstants::HELP_TITLE => $getOrgHelpDoc[HelpConstants::HELP_TITLE],
                HelpConstants::HELP_DESCRIPTION => $getOrgHelpDoc[HelpConstants::HELP_DESCRIPTION] ? $getOrgHelpDoc[HelpConstants::HELP_DESCRIPTION] : '',
                HelpConstants::HELP_TYPE => $getOrgHelpDoc[HelpConstants::HELP_TYPE],
                HelpConstants::HELP_LINK => $getOrgHelpDoc[HelpConstants::HELP_LINK]
            ];
        } else {
            $rs = [
                HelpConstants::HELP_ID => $getOrgHelpDoc[HelpConstants::HELP_ID],
                HelpConstants::HELP_TITLE => $getOrgHelpDoc[HelpConstants::HELP_TITLE],
                HelpConstants::HELP_DESCRIPTION => $getOrgHelpDoc[HelpConstants::HELP_DESCRIPTION] ? $getOrgHelpDoc[HelpConstants::HELP_DESCRIPTION] : '',
                HelpConstants::HELP_TYPE => $getOrgHelpDoc[HelpConstants::HELP_TYPE],
                HelpConstants::HELP_FILEPATH => $getOrgHelpDoc[HelpConstants::HELP_FILEPATH],
                HelpConstants::HELP_DISPLAY_NAME => $getOrgHelpDoc[HelpConstants::HELP_DISPLAY_NAME] ? $getOrgHelpDoc[HelpConstants::HELP_DISPLAY_NAME] : ''
            ];
        }
        return $rs;
    }

    private function isCoordinator($isCoordinator, $message, $key)
    {
        if (! $isCoordinator) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }
} 